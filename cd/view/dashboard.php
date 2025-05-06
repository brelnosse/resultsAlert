<?php
// dashboard.php - Page d'accueil après connexion
require_once '../init.php';
// Inclure le modèle de connexion
require_once 'c:/laragon/www/resultsAlert/cd/model/user_model.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

// Récupérer les informations de l'utilisateur depuis la session
$userId = $_SESSION['user_id'];
$userFirstname = $_SESSION['user_firstname'];
$userLastname = $_SESSION['user_lastname'];
$userEmail = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>Tableau de bord</h1>
            <div class="user-info">
                <span>Bienvenue, <?php echo htmlspecialchars($userFirstname . ' ' . $userLastname); ?></span>
                <a href="logout.php" class="logout-btn">Déconnexion</a>
            </div>
        </div>
    </header>
    
    <main>
        <div class="dashboard-container">
            <div class="welcome-card">
                <h2>Bienvenue sur votre tableau de bord</h2>
                <p>Vous êtes connecté avec l'adresse email: <?php echo htmlspecialchars($userEmail); ?></p>
            </div>
            
            <div class="dashboard-content">
                <!-- Contenu du tableau de bord à personnaliser selon vos besoins -->
                <div class="dashboard-card">
                    <h3>Profil</h3>
                    <p>Gérez vos informations personnelles</p>
                    <a href="#" class="card-link">Modifier mon profil</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>Paramètres</h3>
                    <p>Configurez vos préférences</p>
                    <a href="#" class="card-link">Accéder aux paramètres</a>
                </div>
                
                <!-- Ajoutez d'autres cartes selon vos besoins -->
            </div>
        </div>
    </main>
    
    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> Votre Site. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>