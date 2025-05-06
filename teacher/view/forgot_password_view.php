<?php
session_start();
// Récupération des erreurs et messages de succès
$errors = isset($_SESSION['forgot_password_errors']) ? $_SESSION['forgot_password_errors'] : [];
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : ['email' => ''];

// Nettoyage des variables de session
unset($_SESSION['forgot_password_errors']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de Mot de Passe</title>
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
            <div class="form-container">
                <h1>Réinitialisation du mot de passe</h1>
                <?php if (!empty($errors)): ?>
                    <div class="error-container">
                        <?php foreach ($errors as $error): ?>
                            <p class="error"><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="success-container">
                        <p class="success"><?php echo $successMessage; ?></p>
                    </div>
                <?php endif; ?>
                <div class="reset-info">
                    <p>Veuillez entrer votre adresse email. Nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
                </div>
                
                <form id="resetPasswordForm" action="../controller/forgot_password_controller.php?action=reset_password" method="POST">
                    <div class="form-group">
                        <label for="reset-email">Adresse email</label>
                        <input type="email" id="reset-email" name="email" placeholder="Entrez votre adresse email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Envoyer le lien de réinitialisation</button>
                    
                    <div class="form-links">
                        <p>Vous vous souvenez de votre mot de passe? <a href="login_view.php">Se connecter</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/animations.js"></script>
    <script src="../assets/js/authentication.js"></script>
</body>
</html>