<?php
// controller/reset_password_controller.php - Traitement de la réinitialisation du mot de passe

require_once '../model/password_reset_model.php';

/**
 * Nettoie et valide les données d'entrée
 * @param string $data Donnée à nettoyer
 * @return string Donnée nettoyée
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Traite la réinitialisation du mot de passe
 */
function processResetPassword() {
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer et nettoyer les données
        $token = sanitizeInput($_POST['token']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        $errors = [];
        
        // Vérifier si le token est valide
        $tokenData = verifyPasswordResetToken($token);
        
        if (!$tokenData) {
            $errors[] = "Le lien de réinitialisation est invalide ou a expiré.";
        }
        
        // Validation du mot de passe
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis";
        } elseif (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        
        // Vérification de la correspondance des mots de passe
        if ($password !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        
        // S'il n'y a pas d'erreurs, procéder à la mise à jour du mot de passe
        if (empty($errors) && $tokenData) {
            // Mettre à jour le mot de passe
            if (updateUserPassword($tokenData['email'], $password)) {
                // Marquer le token comme utilisé
                markTokenAsUsed($token);
                
                // Rediriger vers la page de connexion avec un message de succès
                session_start();
                $_SESSION['success_message'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                header("Location: ../view/login_view.php");
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors de la mise à jour du mot de passe. Veuillez réessayer.";
            }
        }
        
        // S'il y a des erreurs, les stocker en session pour les afficher
        if (!empty($errors)) {
            session_start();
            $_SESSION['reset_password_errors'] = $errors;
            header("Location: ../view/reset_password_view.php?token=" . urlencode($token));
            exit();
        }
    } else {
        // Si accès direct au contrôleur sans soumission de formulaire
        header("Location: ../view/forgot_password_view.php");
        exit();
    }
}

// Exécuter le traitement du formulaire
processResetPassword();
?>