<?php
// model/password_reset_model.php - Gestion des tokens de réinitialisation

require_once 'user_model.php';
require_once 'C:/laragon/www/resultsAlert/utils/mailer.php';

/**
 * Crée un token de réinitialisation pour un email donné
 * @param string $email Email de l'utilisateur
 * @return string|false Le token créé ou false en cas d'échec
 */
function createPasswordResetToken($email) {
    // Vérifier si l'email existe dans la base de données
    if (!emailExists($email)) {
        return false;
    }
    
    $pdo = getDbConnection();
    
    try {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        
        // Définir la date d'expiration (24 heures)
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Supprimer les anciens tokens non utilisés pour cet email
        $query = "DELETE FROM password_resets WHERE email = :email AND used = 0";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Insérer le nouveau token
        $query = "INSERT INTO password_resets (email, token, created_at, expires_at, used) 
                  VALUES (:email, :token, :created_at, :expires_at, 0)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $createdAt, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expiresAt, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $token;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la création du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un token est valide
 * @param string $token Token à vérifier
 * @return array|false Données du token ou false si invalide
 */
function verifyPasswordResetToken($token) {
    $pdo = getDbConnection();
    
    try {
        $query = "SELECT * FROM password_resets 
                  WHERE token = :token 
                  AND used = 0 
                  AND expires_at > NOW()";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tokenData) {
            return $tokenData;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Marque un token comme utilisé
 * @param string $token Token à marquer
 * @return bool True si réussi, false sinon
 */
function markTokenAsUsed($token) {
    $pdo = getDbConnection();
    
    try {
        $query = "UPDATE password_resets SET used = 1 WHERE token = :token";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors du marquage du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Envoie un email de réinitialisation de mot de passe avec PHPMailer
 * @param string $email Email du destinataire
 * @param string $token Token de réinitialisation
 * @return bool True si l'email a été envoyé, false sinon
 */
function sendPasswordResetEmail($email, $token) {
    // Récupérer les informations de l'utilisateur
    $user = getUserByEmail($email);
    
    if (!$user) {
        return false;
    }
    
    // Construire l'URL de réinitialisation
    $resetUrl = 'http://resultsAlert.test/teacher/view/reset_password_view.php?token=' . $token;
    
    // Sujet de l'email
    $subject = 'Réinitialisation de votre mot de passe';
    
    // Corps HTML de l'email
    $htmlBody = "
    <html>
    <head>
        <title>Réinitialisation de votre mot de passe</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4a6baf; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
            .button { display: inline-block; background-color: #4a6baf; color: white; padding: 10px 20px; 
                      text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { margin-top: 20px; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Réinitialisation de mot de passe</h2>
            </div>
            <div class='content'>
                <p>Bonjour {$user['firstname']},</p>
                <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
                <p>Veuillez cliquer sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
                <p><a href='{$resetUrl}' class='button' style='color: white'>Réinitialiser mon mot de passe</a></p>
                <p>Si le bouton ne fonctionne pas, vous pouvez copier et coller le lien suivant dans votre navigateur :</p>
                <p>{$resetUrl}</p>
                <p>Ce lien expirera dans 24 heures.</p>
                <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
            </div>
            <div class='footer'>
                <p>Cordialement,<br>L'équipe de votre site</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Corps texte de l'email (version alternative sans HTML)
    $textBody = "
    Bonjour {$user['firstname']},
    
    Vous avez demandé la réinitialisation de votre mot de passe.
    
    Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe :
    {$resetUrl}
    
    Ce lien expirera dans 24 heures.
    
    Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.
    
    Cordialement,
    L'équipe de votre site
    ";
    
    // Envoyer l'email avec notre utilitaire PHPMailer
    return sendEmail($email, $subject, $htmlBody, $textBody);
}
?>