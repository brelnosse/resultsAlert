<?php
// controller/forgot_password_controller.php - Traitement de la demande de réinitialisation

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
 * Traite la demande de réinitialisation de mot de passe
 */
function processForgotPassword() {
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer et nettoyer l'email
        $email = sanitizeInput($_POST['email']);
        
        $errors = [];
        
        // Validation de l'email
        if (empty($email)) {
            $errors[] = "L'adresse email est requise";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Le format de l'email est invalide";
        } elseif (!emailExists($email)) {
            // Pour des raisons de sécurité, ne pas indiquer si l'email existe ou non
            // Mais enregistrer l'erreur pour le débogage
            error_log("Tentative de réinitialisation pour un email inexistant: $email");
        }
        
        // S'il n'y a pas d'erreurs, procéder à la réinitialisation
        if (empty($errors)) {
            // Créer un token de réinitialisation
            $token = createPasswordResetToken($email);
            
            if ($token) {
                // Envoyer l'email de réinitialisation
                if (sendPasswordResetEmail($email, $token)) {
                    // Rediriger avec un message de succès
                    session_start();
                    $_SESSION['success_message'] = "Un email de réinitialisation a été envoyé à votre adresse email.";
                    header("Location: ../view/forgot_password_view.php");
                    exit();
                } else {
                    $errors[] = "Une erreur est survenue lors de l'envoi de l'email. Veuillez réessayer.";
                }
            } else {
                $errors[] = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
        
        // S'il y a des erreurs, les stocker en session pour les afficher
        if (!empty($errors)) {
            session_start();
            $_SESSION['forgot_password_errors'] = $errors;
            $_SESSION['form_data'] = ['email' => $email];
            header("Location: ../view/forgot_password_view.php");
            exit();
        }
    } else {
        // Si accès direct au contrôleur sans soumission de formulaire
        header("Location: ../view/forgot_password_view.php");
        exit();
    }
}

// Exécuter le traitement du formulaire
processForgotPassword();
?>