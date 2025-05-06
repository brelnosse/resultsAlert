<?php
// controller/user_controller.php - Gestion des utilisateurs et de l'authentification

require_once '../model/user_model.php';
require_once '../model/password_reset_model.php'; // Si nécessaire pour la réinitialisation de mot de passe

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
 * Valide un email
 * @param string $email Email à valider
 * @return bool True si l'email est valide, false sinon
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numéro de téléphone (format français)
 * @param string $phone Numéro à valider
 * @return bool True si le numéro est valide, false sinon
 */
function validatePhone($phone) {
    // Format français: 10 chiffres, peut commencer par 0 ou +33
    return preg_match('/^6\d{8}$/', $phone);
}

/**
 * Traite la soumission du formulaire d'inscription
 */
function processSignup() {
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer et nettoyer les données du formulaire
        $firstname = sanitizeInput($_POST['firstname']);
        $lastname = sanitizeInput($_POST['lastname']);
        $phone = sanitizeInput($_POST['phone']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm-password'];
        
        $errors = [];
        
        // Validation des champs
        if (empty($firstname)) {
            $errors[] = "Le prénom est requis";
        }
        
        if (empty($lastname)) {
            $errors[] = "Le nom est requis";
        }
        
        if (empty($phone)) {
            $errors[] = "Le numéro de téléphone est requis";
        } elseif (!validatePhone($phone)) {
            $errors[] = "Le format du numéro de téléphone est invalide";
        } elseif (phoneExists($phone)) {
            $errors[] = "Ce numéro de téléphone est déjà utilisé";
        }
        
        if (empty($email)) {
            $errors[] = "L'adresse email est requise";
        } elseif (!validateEmail($email)) {
            $errors[] = "Le format de l'email est invalide";
        } elseif (emailExists($email)) {
            $errors[] = "Cette adresse email est déjà utilisée";
        }
        
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis";
        } elseif (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        
        // Si aucune erreur, procéder à l'inscription
        if (empty($errors)) {
            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Préparation des données pour l'insertion
            $userData = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email,
                'password' => $hashedPassword
            ];
            
            // Création de l'utilisateur
            if (createUser($userData)) {
                // Envoyer un email de bienvenue
                sendWelcomeEmail($userData);
                
                // Redirection vers la page de connexion avec message de succès
                session_start();
                $_SESSION['success_message'] = "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.";
                header("Location: ../view/login_view.php");
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors de la création du compte. Veuillez réessayer.";
            }
        }
        
        // S'il y a des erreurs, les stocker en session pour les afficher
        if (!empty($errors)) {
            session_start();
            $_SESSION['signup_errors'] = $errors;
            $_SESSION['form_data'] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email
            ];
            header("Location: ../view/register_view.php");
            exit();
        }
    } else {
        // Si accès direct au contrôleur sans soumission de formulaire
        header("Location: ../view/register_view.php");
        exit();
    }
}

/**
 * Traite la soumission du formulaire de connexion
 */
function processLogin() {
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer et nettoyer les données du formulaire
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $rememberMe = isset($_POST['remember']) ? true : false;
        
        $errors = [];
        
        // Validation des champs
        if (empty($email)) {
            $errors[] = "L'adresse email est requise";
        }
        
        if (empty($password)) {
            $errors[] = "Le mot de passe est requis";
        }
        
        // Si aucune erreur de validation, tenter l'authentification
        if (empty($errors)) {
            $user = authenticateUser($email, $password);
            
            if ($user) {
                // Authentification réussie
                createUserSession($user);
                
                // Gérer "Se souvenir de moi"
                if ($rememberMe) {
                    // Créer un cookie qui expire dans 30 jours
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/', '', true, true);
                    
                    // Stocker le token en base de données
                    storeRememberToken($user['id'], $token, 30);
                }
                
                // Rediriger vers le tableau de bord ou la page d'accueil
                header("Location: ../view/dashboard.php");
                exit();
            } else {
                // Authentification échouée
                $errors[] = "Email ou mot de passe incorrect";
            }
        }
        
        // S'il y a des erreurs, les stocker en session pour les afficher
        if (!empty($errors)) {
            session_start();
            $_SESSION['login_errors'] = $errors;
            $_SESSION['form_data'] = ['email' => $email];
            header("Location: ../view/login_view.php");
            exit();
        }
    } else {
        // Si accès direct au contrôleur sans soumission de formulaire
        header("Location: ../view/login_view.php");
        exit();
    }
}

/**
 * Traite la déconnexion de l'utilisateur
 */
function processLogout() {
    // Récupérer l'ID de l'utilisateur avant de détruire la session
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Déconnecter l'utilisateur
    logoutUser();
    
    // Supprimer le cookie "Se souvenir de moi" s'il existe
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Supprimer le token de la base de données
        deleteRememberToken($token);
        
        // Supprimer le cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Si nous avons l'ID de l'utilisateur, supprimer tous ses tokens (optionnel)
    if ($userId) {
        deleteAllRememberTokens($userId);
    }
    
    // Rediriger vers la page de connexion avec un message
    session_start();
    $_SESSION['success_message'] = "Vous avez été déconnecté avec succès.";
    header("Location: ../view/login_view.php");
    exit();
}

/**
 * Traite la mise à jour du profil utilisateur
 */
function processUpdateProfile() {
    // Vérifier si l'utilisateur est connecté
    if (!isUserLoggedIn()) {
        header("Location: ../view/login_view.php");
        exit();
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer l'ID de l'utilisateur depuis la session
        $userId = $_SESSION['user_id'];
        
        // Récupérer et nettoyer les données du formulaire
        $firstname = sanitizeInput($_POST['firstname']);
        $lastname = sanitizeInput($_POST['lastname']);
        $phone = sanitizeInput($_POST['phone']);
        $email = sanitizeInput($_POST['email']);
        
        $errors = [];
        
        // Validation des champs
        if (empty($firstname)) {
            $errors[] = "Le prénom est requis";
        }
        
        if (empty($lastname)) {
            $errors[] = "Le nom est requis";
        }
        
        if (empty($phone)) {
            $errors[] = "Le numéro de téléphone est requis";
        } elseif (!validatePhone($phone)) {
            $errors[] = "Le format du numéro de téléphone est invalide";
        }
        
        if (empty($email)) {
            $errors[] = "L'adresse email est requise";
        } elseif (!validateEmail($email)) {
            $errors[] = "Le format de l'email est invalide";
        }
        
        // Vérifier si l'email ou le téléphone est déjà utilisé par un autre utilisateur
        $user = getUserById($userId);
        if ($email !== $user['email'] && emailExists($email)) {
            $errors[] = "Cette adresse email est déjà utilisée";
        }
        
        if ($phone !== $user['phone'] && phoneExists($phone)) {
            $errors[] = "Ce numéro de téléphone est déjà utilisé";
        }
        
        // Si aucune erreur, procéder à la mise à jour
        if (empty($errors)) {
            // Préparation des données pour la mise à jour
            $userData = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email
            ];
            
            // Mise à jour de l'utilisateur
            if (updateUser($userId, $userData)) {
                // Mettre à jour les informations de session
                $_SESSION['user_firstname'] = $firstname;
                $_SESSION['user_lastname'] = $lastname;
                $_SESSION['user_email'] = $email;
                
                // Redirection avec message de succès
                $_SESSION['success_message'] = "Votre profil a été mis à jour avec succès.";
                header("Location: ../profile.php");
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors de la mise à jour du profil. Veuillez réessayer.";
            }
        }
        
        // S'il y a des erreurs, les stocker en session pour les afficher
        if (!empty($errors)) {
            $_SESSION['profile_errors'] = $errors;
            $_SESSION['form_data'] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phone' => $phone,
                'email' => $email
            ];
            header("Location: ../profile.php");
            exit();
        }
    } else {
        // Si accès direct au contrôleur sans soumission de formulaire
        header("Location: ../profile.php");
        exit();
    }
}

/**
 * Traite le changement de mot de passe
 */
function processChangePassword() {
    // Vérifier si l'utilisateur est connecté
    if (!isUserLoggedIn()) {
        header("Location: ../view/login_view.php");
        exit();
    }
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer l'ID et l'email de l'utilisateur depuis la session
        $userId = $_SESSION['user_id'];
        $email = $_SESSION['user_email'];
        
        // Récupérer les données du formulaire
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        $errors = [];
        
        // Vérifier le mot de passe actuel
        $user = getUserById($userId);
        if (!password_verify($currentPassword, $user['password'])) {
            $errors[] = "Le mot de passe actuel est incorrect";
        }
        
        // Validation du nouveau mot de passe
        if (empty($newPassword)) {
            $errors[] = "Le nouveau mot de passe est requis";
        } elseif (strlen($newPassword) < 8) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères";
        }
        
        // Vérification de la correspondance des mots de passe
        if ($newPassword !== $confirmPassword) {
            $errors[] = "Les nouveaux mots de passe ne correspondent pas";
        }
        
        // Si aucune erreur, procéder au changement de mot de passe
        if (empty($errors)) {
            if (updateUserPassword($email, $newPassword)) {
                // Redirection avec message de succès
                $_SESSION['success_message'] = "Votre mot de passe a été modifié avec succès.";
                header("Location: ../profile.php");
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors du changement de mot de passe. Veuillez réessayer.";
            }
        }
        
        // S'il y a des erreurs, les stocker en session pour les afficher
        if (!empty($errors)) {
            $_SESSION['password_errors'] = $errors;
            header("Location: ../change_password.php");
            exit();
        }
    } else {
        // Si accès direct au contrôleur sans soumission de formulaire
        header("Location: ../change_password.php");
        exit();
    }
}

// Déterminer l'action à effectuer en fonction du paramètre 'action'
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'signup':
        processSignup();
        break;
    case 'login':
        processLogin();
        break;
    case 'logout':
        processLogout();
        break;
    case 'update_profile':
        processUpdateProfile();
        break;
    case 'change_password':
        processChangePassword();
        break;
    default:
        // Action par défaut (si aucune action spécifiée)
        // Déterminer l'action en fonction de la méthode HTTP et du contexte
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Si c'est une soumission de formulaire, traiter comme une inscription par défaut
            processSignup();
        } else {
            // Sinon, rediriger vers la page d'accueil
            header("Location: ../index.php");
            exit();
        }
        break;
}
?>