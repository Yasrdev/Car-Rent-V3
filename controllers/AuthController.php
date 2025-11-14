<?php
require_once '../config/db-config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Inscription utilisateur
    public function register($data) {
        $first_name = trim($data['first_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';
        // Accepter confirmPassword ou confirm_password
        $confirm_password = $data['confirmPassword'] ?? $data['confirm_password'] ?? '';

        // Validation
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Les mots de passe ne correspondent pas");
        }

        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide");
        }

        if (!preg_match('/^[0-9]{9}$/', $phone)) {
            throw new Exception("Numéro de téléphone invalide (format: 600000000)");
        }

        // Vérifier si l'email existe déjà
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("Cet email est déjà utilisé");
        }

        // Hasher le mot de passe et insérer
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'admin'; // Rôle par défaut admin
        
        $stmt = $this->pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$first_name, $last_name, $email, $phone, $hashed_password, $role])) {
            return $this->pdo->lastInsertId();
        } else {
            throw new Exception("Erreur lors de l'inscription");
        }
    }

    // Connexion utilisateur
    public function login($email, $password) {
        $email = trim($email);
        $password = trim($password);

        if (empty($email) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires");
        }

        // Vérifier les identifiants
        $stmt = $this->pdo->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
        } else {
            throw new Exception("Email ou mot de passe incorrect");
        }
    }

    // Vérifier si l'utilisateur est connecté
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Déconnexion
    public function logout() {
        session_destroy();
        header("Location: auth.php");
        exit();
    }

    // Rediriger si déjà connecté
    public function redirectIfLoggedIn() {
        if ($this->isLoggedIn()) {
            header("Location: dashboard.php");
            exit();
        }
    }

    // Rediriger si non connecté
    public function redirectIfNotLoggedIn() {
        if (!$this->isLoggedIn()) {
            header("Location: auth.php");
            exit();
        }
    }
}
?>