<?php
// model/user_model.php - Gestion des utilisateurs et de l'authentification

/**
 * Établit une connexion à la base de données
 * @return PDO Instance de connexion PDO
 */
function getDbConnection() {
    $host = 'localhost';
    $dbname = 'resultsAlert';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
}

// ===== FONCTIONS DE GESTION DES UTILISATEURS =====

/**
 * Vérifie si un email existe déjà dans la base de données
 * @param string $email Email à vérifier
 * @return bool True si l'email existe, false sinon
 */
function emailExists($email) {
    $pdo = getDbConnection();
    $query = "SELECT COUNT(*) FROM enseignants WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}

/**
 * Vérifie si un numéro de téléphone existe déjà dans la base de données
 * @param string $phone Numéro de téléphone à vérifier
 * @return bool True si le numéro existe, false sinon
 */
function phoneExists($phone) {
    $pdo = getDbConnection();
    $query = "SELECT COUNT(*) FROM enseignants WHERE phone = :phone";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}

/**
 * Crée un nouvel utilisateur dans la base de données
 * @param array $userData Données de l'utilisateur
 * @return bool True si l'insertion a réussi, false sinon
 */
function createUser($userData) {
    $pdo = getDbConnection();
    
    try {
        $query = "INSERT INTO enseignants (firstname, lastname, phone, email, password, created_at) 
                  VALUES (:firstname, :lastname, :phone, :email, :password, NOW())";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':firstname', $userData['firstname'], PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $userData['lastname'], PDO::PARAM_STR);
        $stmt->bindParam(':phone', $userData['phone'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $userData['password'], PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère un utilisateur par son email
 * @param string $email Email de l'utilisateur
 * @return array|false Données de l'utilisateur ou false si non trouvé
 */
function getUserByEmail($email) {
    $pdo = getDbConnection();
    $query = "SELECT * FROM enseignants WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Récupère un utilisateur par son ID
 * @param int $userId ID de l'utilisateur
 * @return array|false Données de l'utilisateur ou false si non trouvé
 */
function getUserById($userId) {
    $pdo = getDbConnection();
    $query = "SELECT * FROM enseignants WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Met à jour les informations d'un utilisateur
 * @param int $userId ID de l'utilisateur
 * @param array $userData Données à mettre à jour
 * @return bool True si la mise à jour a réussi, false sinon
 */
function updateUser($userId, $userData) {
    $pdo = getDbConnection();
    
    try {
        // Construire la requête dynamiquement en fonction des champs à mettre à jour
        $updateFields = [];
        $params = [':user_id' => $userId];
        
        foreach ($userData as $field => $value) {
            if ($field !== 'id') { // Ignorer l'ID
                $updateFields[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }
        
        // Ajouter la date de mise à jour
        $updateFields[] = "updated_at = NOW()";
        
        $query = "UPDATE enseignants SET " . implode(', ', $updateFields) . " WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
        return false;
    }
}

/**
 * Envoie un email de bienvenue à un nouvel utilisateur
 * @param array $userData Données de l'utilisateur
 * @return bool True si l'email a été envoyé, false sinon
 */
function sendWelcomeEmail($userData) {
    require_once 'C:/laragon/www/resultsAlert/utils/mailer.php';
    
    $email = $userData['email'];
    $firstname = $userData['firstname'];
    
    // Sujet de l'email
    $subject = 'Bienvenue sur notre site';
    
    // Corps HTML de l'email
    $htmlBody = "
    <html>
    <head>
        <title>Bienvenue sur resulsAlert</title>
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
                <h2>Bienvenue sur resultsAlert</h2>
            </div>
            <div class='content'>
                <p>Bonjour {$firstname},</p>
                <p>Nous sommes ravis de vous accueillir sur notre site!</p>
                <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et profiter de toutes nos fonctionnalités.</p>
                <p><a href='http://resultsAlert.test/cd/view/login_view.php' class='button' style='color: white'>Se connecter</a></p>
                <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            </div>
            <div class='footer'>
                <p>Cordialement,<br>L'équipe de resultsAlert</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Corps texte de l'email
    $textBody = "
    Bonjour {$firstname},
    
    Nous sommes ravis de vous accueillir sur notre site!
    
    Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et profiter de toutes nos fonctionnalités.
    
    Se connecter: http://resultsAlert.test/cd/view/login_view.php
    
    Si vous avez des questions, n'hésitez pas à nous contacter.
    
    Cordialement,
    L'équipe de votre site
    ";
    
    // Envoyer l'email
    return sendEmail($email, $subject, $htmlBody, $textBody);
}

// ===== FONCTIONS D'AUTHENTIFICATION =====

/**
 * Vérifie les identifiants de connexion d'un utilisateur
 * @param string $email Email de l'utilisateur
 * @param string $password Mot de passe en clair
 * @return array|false Données de l'utilisateur si authentification réussie, false sinon
 */
function authenticateUser($email, $password) {
    $pdo = getDbConnection();
    
    try {
        $query = "SELECT * FROM enseignants WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Authentification réussie
            return $user;
        } else {
            // Authentification échouée
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de l'authentification: " . $e->getMessage());
        return false;
    }
}

/**
 * Crée une session pour l'utilisateur connecté
 * @param array $user Données de l'utilisateur
 * @return void
 */
function createUserSession($user) {
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Stocker les informations de l'utilisateur en session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_firstname'] = $user['firstname'];
    $_SESSION['user_lastname'] = $user['lastname'];
    $_SESSION['user_logged_in'] = true;
    
    // Enregistrer la date de dernière connexion
    updateLastLogin($user['id']);
}

/**
 * Met à jour la date de dernière connexion de l'utilisateur
 * @param int $userId ID de l'utilisateur
 * @return bool True si réussi, false sinon
 */
function updateLastLogin($userId) {
    $pdo = getDbConnection();
    
    try {
        $query = "UPDATE enseignants SET last_login = NOW(), updated_at = NOW() WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de la date de dernière connexion: " . $e->getMessage());
        return false;
    }
}

/**
 * Déconnecte l'utilisateur en détruisant sa session
 * @return void
 */
function logoutUser() {
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Détruire toutes les variables de session
    $_SESSION = array();
    
    // Détruire la session
    session_destroy();
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function isUserLoggedIn() {
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Redirige l'utilisateur s'il n'est pas connecté
 * @param string $redirectUrl URL de redirection
 * @return void
 */
function requireLogin($redirectUrl = 'login_view.php') {
    if (!isUserLoggedIn()) {
        header("Location: ../view/$redirectUrl");
        exit();
    }
}

/**
 * Redirige l'utilisateur s'il est déjà connecté
 * @param string $redirectUrl URL de redirection
 * @return void
 */
function redirectIfLoggedIn($redirectUrl = 'dashboard.php') {
    if (isUserLoggedIn()) {
        header("Location: ../view/$redirectUrl");
        exit();
    }
}

/**
 * Met à jour le mot de passe d'un utilisateur
 * @param string $email Email de l'utilisateur
 * @param string $newPassword Nouveau mot de passe
 * @return bool True si réussi, false sinon
 */
function updateUserPassword($email, $newPassword) {
    $pdo = getDbConnection();
    
    try {
        // Hachage du nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE enseignants SET password = :password, updated_at = NOW() WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour du mot de passe: " . $e->getMessage());
        return false;
    }
}
/**
 * Stocke un token "Se souvenir de moi" en base de données
 * @param int $userId ID de l'utilisateur
 * @param string $token Token généré
 * @param int $days Nombre de jours avant expiration
 * @return bool True si réussi, false sinon
 */
function storeRememberToken($userId, $token, $days = 30) {
    $pdo = getDbConnection();
    
    try {
        // Supprimer les anciens tokens pour cet utilisateur
        $query = "DELETE FROM remember_tokens WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Calculer la date d'expiration
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$days days"));
        
        // Récupérer des informations supplémentaires pour la sécurité
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        
        // Insérer le nouveau token
        $query = "INSERT INTO remember_tokens (user_id, token, created_at, expires_at, user_agent, ip_address) 
                  VALUES (:user_id, :token, :created_at, :expires_at, :user_agent, :ip_address)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $createdAt, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expiresAt, PDO::PARAM_STR);
        $stmt->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors du stockage du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un token "Se souvenir de moi" est valide
 * @param string $token Token à vérifier
 * @return array|false Données de l'utilisateur si valide, false sinon
 */
function validateRememberToken($token) {
    $pdo = getDbConnection();
    
    try {
        // Vérifier si le token existe et n'est pas expiré
        $query = "SELECT u.* FROM users u
                  JOIN remember_tokens rt ON u.id = rt.user_id
                  WHERE rt.token = :token AND rt.expires_at > NOW()";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Vérifier si l'agent utilisateur correspond (sécurité supplémentaire)
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
            
            $query = "SELECT user_agent FROM remember_tokens WHERE token = :token";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si l'agent utilisateur ne correspond pas, rejeter la connexion
            if ($tokenData && $tokenData['user_agent'] !== $userAgent) {
                return false;
            }
            
            return $user;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erreur lors de la validation du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprime un token "Se souvenir de moi"
 * @param string $token Token à supprimer
 * @return bool True si réussi, false sinon
 */
function deleteRememberToken($token) {
    $pdo = getDbConnection();
    
    try {
        $query = "DELETE FROM remember_tokens WHERE token = :token";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression du token: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprime tous les tokens "Se souvenir de moi" d'un utilisateur
 * @param int $userId ID de l'utilisateur
 * @return bool True si réussi, false sinon
 */
function deleteAllRememberTokens($userId) {
    $pdo = getDbConnection();
    
    try {
        $query = "DELETE FROM remember_tokens WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression des tokens: " . $e->getMessage());
        return false;
    }
}
?>