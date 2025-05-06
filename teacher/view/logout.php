<?php
// logout.php - Page de déconnexion

// Rediriger vers le contrôleur de connexion avec l'action de déconnexion
header("Location: ../controller/user_controller.php?action=logout");
exit();
?>