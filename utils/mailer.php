<?php
// utils/mailer.php - Utilitaire pour l'envoi d'emails avec PHPMailer

// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Importer les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Initialise et configure une instance de PHPMailer
 * @return PHPMailer Instance configurée de PHPMailer
 */
function initMailer() {
    // Charger la configuration
    
    // Créer une nouvelle instance de PHPMailer
    $mail = new PHPMailer(true); // true active les exceptions
    
    try {
        // Configuration du serveur
        $mail->isSMTP();                                      // Utiliser SMTP
        $mail->Host       = 'smtp.gmail.com';         // Serveur SMTP Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'brelnosse2@gmail.com';        // Votre adresse Gmail
        $mail->Password   = 'uomo yvbi igkh umte'; // Mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port       = 587;          

        // Paramètres de l'expéditeur
        $mail->setFrom("brelnosse2@gmail.com", "ResulstAlert");
        $mail->CharSet = "UTF-8";                  // Définir l'encodage des caractères
        
        // Niveau de débogage
        $mail->SMTPDebug = 0;
        
        return $mail;
    } catch (Exception $e) {
        error_log("Erreur lors de l'initialisation de PHPMailer: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Envoie un email avec PHPMailer
 * @param string $to Adresse email du destinataire
 * @param string $subject Sujet de l'email
 * @param string $htmlBody Corps HTML de l'email
 * @param string $textBody Corps texte de l'email (optionnel)
 * @return bool True si l'email a été envoyé, false sinon
 */
function sendEmail($to, $subject, $htmlBody, $textBody = '') {
    try {
        $mail = initMailer();
        
        // Destinataire
        $mail->addAddress($to);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        
        // Version texte alternative (optionnelle mais recommandée)
        if (!empty($textBody)) {
            $mail->AltBody = $textBody;
        } else {
            // Générer une version texte à partir du HTML
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));
        }
        
        // Envoyer l'email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur lors de l'envoi de l'email: " . $e->getMessage());
        return false;
    }
}
?>