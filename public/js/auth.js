

const formLogin = document.getElementById("formLogin");
const formSignup = document.getElementById("formSignup");
const btnLogin = document.getElementById("login-btn");
const btnSignup = document.getElementById("register-btn");

// Handler pour afficher le formulaire de connexion
btnLogin.addEventListener("click", (e) => {
    e.preventDefault();
    formLogin.classList.add("actif");
    formSignup.classList.remove("actif");
});

// Handler pour afficher le formulaire d'inscription
btnSignup.addEventListener("click", (e) => {
    e.preventDefault();
    formSignup.classList.add("actif");
    formLogin.classList.remove("actif");
});

// Gestion de l'affichage/masquage des mots de passe
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const passwordInput = document.getElementById(targetId);
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🙈';
    });
});

// Indicateur de force du mot de passe
if (document.getElementById('registerPassword')) {
    document.getElementById('registerPassword').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('passwordStrengthText');
        
        let strength = 0;
        let text = '';
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        // Réinitialiser les classes
        strengthBar.className = 'strength-bar';
        
        switch(strength) {
            case 0:
                text = '';
                break;
            case 1:
                strengthBar.classList.add('strength-weak');
                text = 'Faible';
                break;
            case 2:
            case 3:
                strengthBar.classList.add('strength-medium');
                text = 'Moyen';
                break;
            case 4:
                strengthBar.classList.add('strength-strong');
                text = 'Fort';
                break;
        }
        
        strengthText.textContent = text;
    });
}

// Validation des formulaires - PERMETTRE LA SOUMISSION
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        // Permettre la soumission normale du formulaire
        // La validation HTML5 native se charge de vérifier email et required
    });
}

if (document.getElementById('registerForm')) {
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        // Validation de la confirmation du mot de passe
        const password = document.getElementById('registerPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();  // Bloquer uniquement si les mots de passe ne correspondent pas
            document.getElementById('confirmPasswordError').style.display = 'block';
            return false;
        } else {
            document.getElementById('confirmPasswordError').style.display = 'none';
        }
        
        // Permettre la soumission normale du formulaire
    });
}

// Masquer les messages d'erreur lors de la saisie
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', function() {
        const errorElement = this.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.style.display = 'none';
        }
    });
});
