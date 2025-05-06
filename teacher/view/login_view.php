<?php


// Rediriger si l'utilisateur est déjà connecté
require_once 'c:/laragon/www/resultsAlert/teacher/model/user_model.php';
redirectIfLoggedIn();

// Récupération des erreurs et messages de succès
$errors = isset($_SESSION['login_errors']) ? $_SESSION['login_errors'] : [];
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : ['email' => ''];

// Nettoyage des variables de session
unset($_SESSION['login_errors']);
unset($_SESSION['success_message']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
                <h1>Connectez-vous à votre compte</h1>
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
                <form id="loginForm" action="../controller/user_controller.php?action=login" method="POST">
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre e-mail" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Se souvenir de moi</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Se connecter maintenant</button>

                    <div class="form-links">
                        <a href="forgot_password_view.php" class="forgot-password">Mot de passe oublié ?</a>
                        <p>Vous n'avez pas de compte ? <a href="register_view.php">Inscrivez-vous</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/animations.js"></script>
    <script src="../assets/js/authentication.js"></script>
</body>

</html>