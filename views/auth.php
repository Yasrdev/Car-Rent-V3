<?php

require_once '../controllers/AuthController.php';
require_once '../config/db-config.php';
$auth = new AuthController($pdo);

// Rediriger si déjà connecté
$auth->redirectIfLoggedIn();

$error = '';
$success = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    try {
        $user_id = $auth->register($_POST);
        
        // Connecter automatiquement l'utilisateur après inscription
        $user = $auth->login($_POST['email'], $_POST['password']);
        
        // Stocker en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    try {
        $user = $auth->login($_POST['email'], $_POST['password']);
        
        // Stocker en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_role'] = $user['role'];
        
        header("Location: dashboard.php");
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/auth.css">
    <title>Connexion - BARIZ CARS</title>
</head>
<body>
     <!-- Image de fond avec effet flou -->
<div class="background"></div>
    <!-- Effets de fumée -->
    <div class="smoke smoke-top"></div>
    <div class="smoke smoke-bottom"></div>
    
    <!-- Formulaire de CONNEXION -->
    <div id="formLogin" class="formulaire actif">
        <div class="login-container">
            <div class="login-header">
                <h1>Connexion</h1>
                <p>Bienvenue ! Veuillez vous connecter à votre compte.</p>
                            <?php if ($error && isset($_POST['login'])): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            </div>

            <form id="loginForm" method="POST" novalidate>
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input 
                        type="email" 
                        id="loginEmail"
                        name="email"
                        class="form-control" 
                        placeholder="votre@email.com"
                        required
                    >
                    <div class="error-message" id="loginEmailError">Veuillez entrer un email valide</div>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Mot de passe</label>
                    <div class="password-container">
                        <input 
                            type="password" 
                            id="loginPassword"
                            name="password"
                            class="form-control" 
                            placeholder="Votre mot de passe"
                            required
                        >
                        <button type="button" class="toggle-password" data-target="loginPassword">
                            👁️
                        </button>
                    </div>
                    <div class="error-message" id="loginPasswordError">Le mot de passe est requis</div>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" id="rememberMe">
                        Se souvenir de moi
                    </label>
                    <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-login" name="login">Se connecter</button>

                <div class="signup-link">
                    Pas de compte ? <a href="#" id="register-btn">S'inscrire</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire d'INSCRIPTION -->
    <div id="formSignup" class="formulaire">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Inscription</h1>
                <p>Rejoignez BARIZ CARS et accédez à notre collection exclusive.</p>
                            <?php if ($error && isset($_POST['register'])): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            </div>

            <form id="registerForm" method="POST" novalidate>
                <!-- Champs nom et prénom côte à côte -->
                <div class="name-fields">
                    <div class="form-group">
                        <label for="firstName">Prénom</label>
                        <input 
                            type="text" 
                            id="firstName"
                            name="first_name"
                            class="form-control" 
                            placeholder="Votre prénom"
                            required
                        >
                        <div class="error-message" id="firstNameError">Le prénom est requis</div>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Nom</label>
                        <input 
                            type="text" 
                            id="lastName"
                            name="last_name"
                            class="form-control" 
                            placeholder="Votre nom"
                            required
                        >
                        <div class="error-message" id="lastNameError">Le nom est requis</div>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input 
                        type="email" 
                        id="registerEmail"
                        name="email"
                        class="form-control" 
                        placeholder="votre@email.com"
                        required
                    >
                    <div class="error-message" id="registerEmailError">Veuillez entrer un email valide</div>
                </div>

                <!-- Téléphone -->
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <div class="phone-field">
                        <span class="phone-prefix">+212</span>
                        <input 
                            type="tel" 
                            id="phone"
                            name="phone"
                            class="form-control" 
                            placeholder="6 00 00 00 00"
                            pattern="[0-9]{9}"
                            required
                        >
                    </div>
                    <div class="error-message" id="phoneError">Numéro de téléphone invalide</div>
                </div>

                <!-- Mot de passe avec indicateur de force -->
                <div class="form-group">
                    <label for="registerPassword">Mot de passe</label>
                    <div class="password-container">
                        <input 
                            type="password" 
                            id="registerPassword"
                            name="password"
                            class="form-control" 
                            placeholder="Créez un mot de passe"
                            required
                            minlength="8"
                        >
                        <button type="button" class="toggle-password" data-target="registerPassword">
                            👁️
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="passwordStrength"></div>
                    </div>
                    <div class="strength-text" id="passwordStrengthText"></div>
                    <div class="error-message" id="registerPasswordError">Le mot de passe doit contenir au moins 8 caractères</div>
                </div>

                <!-- Confirmation mot de passe -->
                <div class="form-group">
                    <label for="confirmPassword">Confirmer le mot de passe</label>
                    <div class="password-container">
                        <input 
                            type="password" 
                            id="confirmPassword"
                            name="confirmPassword"
                            class="form-control" 
                            placeholder="Confirmez votre mot de passe"
                            required
                        >
                        <button type="button" class="toggle-password" data-target="confirmPassword">
                            👁️
                        </button>
                    </div>
                    <div class="error-message" id="confirmPasswordError">Les mots de passe ne correspondent pas</div>
                </div>

                <button type="submit" class="btn-auth" name="register">Créer mon compte</button>

                <div class="auth-link">
                    Déjà inscrit ? <a href="#" id="login-btn">Se connecter</a>
                </div>
            </form>
        </div>
    </div>

<script src="../public/js/auth.js"></script>
</body>
</html>
