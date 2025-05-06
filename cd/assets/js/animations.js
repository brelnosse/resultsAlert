// Gestion des animations et transitions entre les pages
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les animations de page
    initPageTransitions();
    
    // Ajouter des effets d'ondulation aux boutons
    initRippleEffect();
    
    // Ajouter des animations aux champs de formulaire
    initFormFieldAnimations();
});

// Initialiser les transitions de page
function initPageTransitions() {
    // Récupérer tous les liens qui pointent vers d'autres pages de l'application
    const internalLinks = document.querySelectorAll('a[href^="login_view.php"], a[href^="register_view.php"], a[href^="forgot_password_view.php"]');
    
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetUrl = this.getAttribute('href');
            
            // Animer la sortie de la page actuelle
            const container = document.querySelector('.container');
            container.classList.add('page-exit');
            
            // Attendre la fin de l'animation avant de naviguer
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 500); // Correspond à la durée de l'animation CSS
        });
    });
    
    // Animer l'entrée de la page au chargement
    window.addEventListener('pageshow', function(e) {
        // Vérifier si la page est chargée depuis le cache (navigation retour)
        if (e.persisted) {
            const container = document.querySelector('.container');
            container.classList.remove('page-exit');
            container.classList.add('page-enter');
            
            setTimeout(() => {
                container.classList.add('page-enter-active');
            }, 10);
            
            setTimeout(() => {
                container.classList.remove('page-enter', 'page-enter-active');
            }, 500);
        }
    });
}

// Ajouter des effets d'ondulation aux boutons
function initRippleEffect() {
    const buttons = document.querySelectorAll('.btn-submit');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600); // Durée de l'animation
        });
    });
}

// Ajouter des animations aux champs de formulaire
function initFormFieldAnimations() {
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="tel"]');
    
    inputs.forEach(input => {
        // Animation au focus
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        // Animation à la perte de focus
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
        });
        
        // Animation à la saisie
        input.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.classList.add('has-content');
            } else {
                this.classList.remove('has-content');
            }
        });
    });
}

// Fonction pour afficher une animation d'erreur sur un élément
function showErrorAnimation(element) {
    element.classList.add('error-shake');
    
    // Supprimer la classe après l'animation pour permettre de la rejouer
    setTimeout(() => {
        element.classList.remove('error-shake');
    }, 500);
}

// Fonction pour afficher une animation de succès
function showSuccessAnimation(element) {
    element.classList.add('success-pulse');
    
    setTimeout(() => {
        element.classList.remove('success-pulse');
    }, 1000);
}

// Exporter les fonctions pour les utiliser dans script.js
window.animationUtils = {
    showErrorAnimation,
    showSuccessAnimation
};