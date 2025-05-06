<?php
// config/mail_config.php - Configuration de l'envoi d'emails

return [
    // Configuration SMTP
    'smtp' => [
        'host' => 'smtp.gmail.com',         // Serveur SMTP
        'port' => 587,                         // Port SMTP
        'encryption' => 'tls',                 // Encryption: 'tls' ou 'ssl'
        'auth' => true,                        // Authentification requise
        'username' => 'brelnosse2@example.com', // Nom d'utilisateur SMTP
        'password' => 'uomo yvbi igkh umte',    // Mot de passe SMTP
    ],
    
    // Configuration de l'expéditeur
    'from' => [
        'email' => 'brelnosse2@gmail.com',    // Email de l'expéditeur
        'name' => 'resultsAlert.com',                // Nom de l'expéditeur
    ],
    
    // Autres paramètres
    'debug' => 0,                              // Niveau de débogage (0-4)
    'charset' => 'UTF-8',                      // Encodage des caractères
];
?>