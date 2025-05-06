// Fonction pour valider le formulaire de connexion
document.addEventListener('DOMContentLoaded', function() {
    // Charger les animations
    if (!document.querySelector('script[src="../assets/js/animations.js"]')) {
        const script = document.createElement('script');
        script.src = '../assets/js/animations.js';
        document.head.appendChild(script);
    }
    
    // Ajouter des styles CSS pour les messages d'erreur
    addErrorStyles();
    
    // Récupérer le formulaire de connexion s'il existe sur la page
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Supprimer tous les messages d'erreur existants
            removeAllErrors();
            
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const remember = document.getElementById('remember').checked;
            
            let isValid = true;
            
            // Validation de l'email
            if (!validateEmail(email.value)) {
                isValid = false;
                showError(email, 'Veuillez entrer une adresse email valide');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(email.parentElement);
                }
            }
            
            // Validation du mot de passe
            if (password.value.length < 8) {
                isValid = false;
                showError(password, 'Le mot de passe doit contenir au moins 8 caractères');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(password.parentElement);
                }
            }
            
            if (!isValid) {
                // Animer le formulaire pour indiquer une erreur
                loginForm.classList.add('error-shake');
                setTimeout(() => {
                    loginForm.classList.remove('error-shake');
                }, 500);
                return;
            }
            
            // Simuler une connexion réussie avec animation
            const submitButton = loginForm.querySelector('.btn-submit');
            submitButton.innerHTML = '<span class="loading-spinner"></span> Connexion...';
            submitButton.disabled = true;
            
            setTimeout(() => {
                console.log('Tentative de connexion avec:', { email: email.value, password: password.value, remember });
                
                // Simuler une redirection avec animation
                const container = document.querySelector('.container');
                container.classList.add('page-exit');
                
                setTimeout(() => {
                    submitButton.innerHTML = 'Se connecter maintenant';
                    submitButton.disabled = false;
                    container.classList.remove('page-exit');
                    
                    loginForm.submit();
                }, 500);
            }, 1500); // Délai pour simuler une requête réseau
        });
    }
    
    // Récupérer le formulaire d'inscription s'il existe sur la page
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        // Vérification en temps réel de la correspondance des mots de passe
        const password = document.getElementById('signup-password');
        const confirmPassword = document.getElementById('confirm-password');
        
        if (password && confirmPassword) {
            // Fonction pour vérifier la correspondance des mots de passe
            const checkPasswordMatch = function() {
                // Ne vérifier que si les deux champs ont du contenu
                if (password.value && confirmPassword.value) {
                    if (password.value !== confirmPassword.value) {
                        showError(confirmPassword, 'Les mots de passe ne correspondent pas');
                        confirmPassword.classList.add('input-error');
                        confirmPassword.classList.remove('input-success');
                    } else {
                        removeError(confirmPassword);
                        confirmPassword.classList.remove('input-error');
                        confirmPassword.classList.add('input-success');
                    }
                } else {
                    // Si l'un des champs est vide, supprimer les messages et classes
                    removeError(confirmPassword);
                    confirmPassword.classList.remove('input-error', 'input-success');
                }
            };
            
            // Vérifier à chaque saisie dans l'un des deux champs
            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
            
            // Vérifier également au focus/blur pour une meilleure expérience utilisateur
            password.addEventListener('blur', checkPasswordMatch);
            confirmPassword.addEventListener('blur', checkPasswordMatch);
        }
        
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Supprimer tous les messages d'erreur existants
            removeAllErrors();
            
            const firstname = document.getElementById('firstname');
            const lastname = document.getElementById('lastname');
            const phone = document.getElementById('phone');
            const email = document.getElementById('signup-email');
            const password = document.getElementById('signup-password');
            const confirmPassword = document.getElementById('confirm-password');
            
            let isValid = true;
            
            // Validation du prénom
            if (firstname.value.length < 2) {
                isValid = false;
                showError(firstname, 'Le prénom doit contenir au moins 2 caractères');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(firstname.parentElement);
                }
            }
            
            // Validation du nom
            if (lastname.value.length < 2) {
                isValid = false;
                showError(lastname, 'Le nom doit contenir au moins 2 caractères');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(lastname.parentElement);
                }
            }
            
            // Validation du téléphone
            if (!validatePhone(phone.value)) {
                isValid = false;
                showError(phone, 'Veuillez entrer un numéro de téléphone valide (10 chiffres minimum)');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(phone.parentElement);
                }
            }
            
            // Validation de l'email
            if (!validateEmail(email.value)) {
                isValid = false;
                showError(email, 'Veuillez entrer une adresse email valide');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(email.parentElement);
                }
            }
            
            // Validation du mot de passe
            if (password.value.length < 8) {
                isValid = false;
                showError(password, 'Le mot de passe doit contenir au moins 8 caractères');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(password.parentElement);
                }
            }
            
            // Validation de la correspondance des mots de passe
            if (password.value !== confirmPassword.value) {
                isValid = false;
                showError(confirmPassword, 'Les mots de passe ne correspondent pas');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(confirmPassword.parentElement);
                }
            }
            
            if (!isValid) {
                // Animer le formulaire pour indiquer une erreur
                signupForm.classList.add('error-shake');
                setTimeout(() => {
                    signupForm.classList.remove('error-shake');
                }, 500);
                return;
            }
            
            // Simuler une inscription réussie avec animation
            const submitButton = signupForm.querySelector('.btn-submit');
            submitButton.innerHTML = '<span class="loading-spinner"></span> Inscription...';
            submitButton.disabled = true;
            
            setTimeout(() => {
                console.log('Tentative d\'inscription avec:', { 
                    firstname: firstname.value, 
                    lastname: lastname.value, 
                    phone: phone.value, 
                    email: email.value 
                });
                
                // Simuler une redirection avec animation
                const container = document.querySelector('.container');
                container.classList.add('page-exit');
                
                setTimeout(() => {
                    submitButton.innerHTML = 'S\'inscrire';
                    submitButton.disabled = false;
                    container.classList.remove('page-exit');
                    signupForm.submit(); // Soumettre le formulaire pour une inscription réelle
                }, 500);
            }, 1500); // Délai pour simuler une requête réseau
        });
    }
    
    // Récupérer le formulaire de réinitialisation de mot de passe s'il existe sur la page
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Supprimer tous les messages d'erreur existants
            removeAllErrors();
            
            const email = document.getElementById('reset-email');
            
            // Validation de l'email
            if (!validateEmail(email.value)) {
                showError(email, 'Veuillez entrer une adresse email valide');
                if (window.animationUtils) {
                    window.animationUtils.showErrorAnimation(email.parentElement);
                }
                return;
            }
            
            // Simuler une demande de réinitialisation réussie avec animation
            const submitButton = resetPasswordForm.querySelector('.btn-submit');
            submitButton.innerHTML = '<span class="loading-spinner"></span> Envoi...';
            submitButton.disabled = true;
            
            setTimeout(() => {
                // Afficher un message de confirmation avec animation
                showResetConfirmation(email.value);
                resetPasswordForm.submit();
            }, 1500); // Délai pour simuler une requête réseau
        });
    }
    
    // Ajouter des classes CSS pour les styles d'animation
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.add('input-focus');
        });
        
        input.addEventListener('blur', function() {
            this.classList.remove('input-focus');
        });
    });
});

// Fonction pour afficher un message d'erreur sous un champ
function showError(inputElement, message) {
    // Supprimer tout message d'erreur existant
    removeError(inputElement);
    
    // Ajouter la classe d'erreur à l'input
    inputElement.classList.add('input-error');
    
    // Créer le message d'erreur
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    
    // Ajouter le message après l'élément parent du champ (généralement un div.form-group)
    inputElement.parentElement.appendChild(errorElement);
    
    // Animation d'entrée pour le message d'erreur
    setTimeout(() => {
        errorElement.classList.add('show');
    }, 10);
}

// Fonction pour supprimer un message d'erreur
function removeError(inputElement) {
    // Trouver et supprimer le message d'erreur s'il existe
    const errorElement = inputElement.parentElement.querySelector('.error-message');
    if (errorElement) {
        errorElement.classList.remove('show');
        
        // Attendre la fin de l'animation avant de supprimer l'élément
        setTimeout(() => {
            errorElement.remove();
        }, 300);
    }
}

// Fonction pour supprimer tous les messages d'erreur
function removeAllErrors() {
    document.querySelectorAll('.error-message').forEach(error => {
        error.classList.remove('show');
        setTimeout(() => {
            error.remove();
        }, 300);
    });
    
    document.querySelectorAll('.input-error').forEach(input => {
        input.classList.remove('input-error');
    });
    
    document.querySelectorAll('.input-success').forEach(input => {
        input.classList.remove('input-success');
    });
}

// Fonction pour ajouter les styles CSS des messages d'erreur
function addErrorStyles() {
    if (!document.getElementById('error-styles')) {
        const style = document.createElement('style');
        style.id = 'error-styles';
        style.textContent = `
            .error-message {
                color: #e74c3c;
                font-size: 12px;
                margin-top: 5px;
                padding-left: 2px;
                opacity: 0;
                transform: translateY(-10px);
                transition: opacity 0.3s ease, transform 0.3s ease;
            }
            
            .error-message.show {
                opacity: 1;
                transform: translateY(0);
            }
            
            .input-error {
                border-color: #e74c3c !important;
                box-shadow: 0 0 0 1px rgba(231, 76, 60, 0.2) !important;
                background-color: rgba(231, 76, 60, 0.05) !important;
            }
            
            .input-success {
                border-color: #2ecc71 !important;
                box-shadow: 0 0 0 1px rgba(46, 204, 113, 0.2) !important;
                background-color: rgba(46, 204, 113, 0.05) !important;
            }
            
            .loading-spinner {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: #fff;
                animation: spin 0.8s linear infinite;
                margin-right: 8px;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            .input-focus {
                transform: translateY(-2px);
                transition: transform 0.3s ease;
            }
            
            /* Message de succès pour la correspondance des mots de passe */
            .password-match-success {
                color: #2ecc71;
                font-size: 12px;
                margin-top: 5px;
                padding-left: 2px;
            }
        `;
        document.head.appendChild(style);
    }
}

// Fonction pour afficher un message de confirmation après la demande de réinitialisation
function showResetConfirmation(email) {
    // Récupérer le conteneur du formulaire
    // const formContainer = document.querySelector('.form-container');
    
    // if (formContainer) {
    //     // Animer la sortie du formulaire
    //     formContainer.style.opacity = '0';
    //     formContainer.style.transform = 'translateY(20px)';
        
    //     setTimeout(() => {
    //         // Remplacer le contenu du formulaire par un message de confirmation
    //         formContainer.innerHTML = `
    //             <h1>Email envoyé</h1>
    //             <div class="confirmation-message">
    //                 <p>Un email contenant les instructions pour réinitialiser votre mot de passe a été envoyé à <strong>${email}</strong>.</p>
    //                 <p>Veuillez vérifier votre boîte de réception et suivre les instructions.</p>
    //                 <p>Si vous ne recevez pas l'email dans les prochaines minutes, vérifiez votre dossier de spam.</p>
    //             </div>
    //             <div class="form-links">
    //                 <p><a href="login_view.php">Retour à la page de connexion</a></p>
    //             </div>
    //         `;
            
    //         // Animer l'entrée du message de confirmation
    //         formContainer.style.opacity = '1';
    //         formContainer.style.transform = 'translateY(0)';
    //         formContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    //     }, 500);
    // }
}

// Fonction pour valider le format d'email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Fonction pour valider le format de numéro de téléphone
function validatePhone(phone) {
    // Format international simple
    const re = /^6[0-9]{8}$/;
    return re.test(phone.replace(/\s/g, ''));
}