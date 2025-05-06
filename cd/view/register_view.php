<?php
require_once '../init.php';
// Affichage des erreurs
if (isset($_SESSION['signup_errors'])) {
    echo '<div class="error-container">';
    foreach ($_SESSION['signup_errors'] as $error) {
        echo '<p class="error">' . $error . '</p>';
    }
    echo '</div>';
    unset($_SESSION['signup_errors']);
}

// Récupération des données du formulaire en cas d'erreur
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [
    'firstname' => '',
    'lastname' => '',
    'phone' => '',
    'email' => ''
];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../assets/css/authentication.css">
</head>
<body>
    <div class="container">
        <div class="pattern-side">
            <!-- Motifs géométriques -->
            <div class="shape shape1"></div>
            <div class="shape shape2"></div>
            <div class="shape shape3"></div>
            <div class="shape shape4"></div>
            <div class="shape shape5"></div>
            <div class="shape shape6"></div>
            <div class="shape shape7"></div>
            <div class="shape shape8"></div>
            <div class="shape shape9"></div>
        </div>
        <div class="form-side">
            <div class="form-container signup-container">
                <h1>Créer un compte</h1>
                
                <form id="signupForm" action="../controller/user_controller.php?action=signup" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname">Prénom</label>
                            <input type="text" id="firstname" name="firstname" placeholder="Votre prénom" <?php echo htmlspecialchars($formData['firstname'] ?? ''); ?> required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastname">Nom</label>
                            <input type="text" id="lastname" name="lastname" placeholder="Votre nom" <?php echo htmlspecialchars($formData['lastname'] ?? ''); ?> required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Numéro de téléphone</label>
                        <input type="tel" id="phone" name="phone" placeholder="Votre numéro de téléphone" <?php echo htmlspecialchars($formData['phone'] ?? ''); ?> required>
                    </div>
                    
                    <div class="form-group">
                        <label for="signup-email">Adresse email</label>
                        <input type="email" id="signup-email" name="email" placeholder="Votre adresse email" <?php echo htmlspecialchars($formData['email'] ?? ''); ?> required>
                    </div>
                    
                    <div class="form-group">
                        <label for="signup-password">Mot de passe</label>
                        <input type="password" id="signup-password" name="password" placeholder="Créez un mot de passe" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Confirmation du mot de passe</label>
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirmez votre mot de passe" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">S'inscrire</button>
                    
                    <div class="form-links">
                        <p>Vous avez déjà un compte? <a href="login_view.php">Se connecter</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/animations.js"></script>
    <script src="../assets/js/authentication.js"></script>
</body>
</html>