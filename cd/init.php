<?php
// init.php - Fichier d'initialisation à inclure au début de chaque page

// Démarrer la session
session_start();

// Inclure le modèle utilisateur
require_once __DIR__ . '/model/user_model.php';

// Variable pour suivre si l'utilisateur vient d'être connecté automatiquement
$autoLoggedIn = false;

// Vérifier si l'utilisateur n'est pas déjà connecté et s'il a un cookie "Se souvenir de moi"
if (!isUserLoggedIn() && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // Valider le token et récupérer les informations de l'utilisateur
    $user = validateRememberToken($token);
    
    if ($user) {
        // Créer une session pour l'utilisateur
        createUserSession($user);
        
        // Régénérer le token pour plus de sécurité (rotation des tokens)
        $newToken = bin2hex(random_bytes(32));
        setcookie('remember_token', $newToken, time() + 30 * 24 * 60 * 60, '/', '', true, true);
        
        // Mettre à jour le token en base de données
        storeRememberToken($user['id'], $newToken, 30);
        
        // Supprimer l'ancien token
        deleteRememberToken($token);
        
        // Marquer que l'utilisateur vient d'être connecté automatiquement
        $autoLoggedIn = true;
    } else {
        // Token invalide, supprimer le cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
}

// Rediriger automatiquement vers le tableau de bord si l'utilisateur vient d'être connecté
// et que nous sommes sur la page d'accueil ou la page de connexion
if ($autoLoggedIn) {
    // Obtenir le nom du script actuel
    $currentScript = basename($_SERVER['PHP_SELF']);
    
    // Liste des pages où la redirection automatique doit se produire
    $redirectPages = ['login_view.php'];
    
    if (in_array($currentScript, $redirectPages)) {
        // Rediriger vers le tableau de bord
        header("Location: " . getDashboardUrl());
        exit();
    }
}

/**
 * Détermine l'URL du tableau de bord en fonction de la structure du projet
 * @return string URL du tableau de bord
 */
function getDashboardUrl() {
    // Déterminer si nous sommes dans un sous-dossier
    $scriptDir = dirname($_SERVER['PHP_SELF']);
    
    // Si nous sommes dans le dossier racine
    if ($scriptDir == '/' || $scriptDir == '\\') {
        return '/view/dashboard.php';
    }
    
    // Si nous sommes dans un sous-dossier (comme /view/)
    if (strpos($scriptDir, '/view') !== false) {
        return 'dashboard.php';
    }
    
    // Par défaut, supposer que le tableau de bord est dans le dossier view
    return 'view/dashboard.php';
}
?>