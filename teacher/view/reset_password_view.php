<?php
session_start();

// Vérifier si un token est présent dans l'URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    // Rediriger vers la page de demande de réinitialisation
    header("Location: forgot_password_view.php");
    exit();
}

// Récupérer le token
$token = $_GET['token'];

// Inclure le modèle pour vérifier le token
require_once '../model/password_reset_model.php';

// Vérifier si le token est valide
$tokenData = verifyPasswordResetToken($token);

if (!$tokenData) {
    // Token invalide ou expiré
    $_SESSION['forgot_password_errors'] = ["Le lien de réinitialisation est invalide ou a expiré."];
    header("Location: forgot_password_view.php");
    exit();
}

// Récupération des erreurs
$errors = isset($_SESSION['reset_password_errors']) ? $_SESSION['reset_password_errors'] : [];

// Nettoyage des variables de session
unset($_SESSION['reset_password_errors']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
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
            <div class="form-container reset-password-container">
                <h1>Réinitialisation du mot de passe</h1>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-container">
                        <?php foreach ($errors as $error): ?>
                            <p class="error"><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p class="form-description">
                    Veuillez entrer votre nouveau mot de passe ci-dessous.
                </p>
                
                <form id="resetPasswordForm" action="../controller/reset_password_controller.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Votre nouveau mot de passe" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmation du mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmez votre mot de passe" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Réinitialiser le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/animations.js"></script>
    <script>
        // Validation côté client pour vérifier que les mots de passe correspondent
        document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
            }
        });
    </script>
</body>
</html>