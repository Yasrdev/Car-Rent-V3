<?php
header('Content-Type: application/json; charset=utf-8');

// Mode debug local (mettre false en production)
$debug = true;

// Convertit les erreurs PHP en exceptions afin de toujours renvoyer du JSON
set_exception_handler(function($e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once __DIR__ . '/../config/db-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer et assainir les données
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = trim($_POST['role'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? $_POST['confirmPassword'] ?? '';

$errors = [];

// Validation basique
if ($first_name === '') {
    $errors['first_name'] = 'Le prénom est requis';
}
if ($last_name === '') {
    $errors['last_name'] = 'Le nom est requis';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email invalide';
}

// Normaliser le téléphone: garder uniquement les chiffres
$phone_digits = preg_replace('/\D+/', '', $phone);
// Si l'utilisateur a saisi le préfixe international (ex: +212), on peut l'enlever
if (strpos($phone_digits, '212') === 0 && strlen($phone_digits) > 9) {
    // retire le préfixe pays si présent en début
    $phone_digits = substr($phone_digits, -9);
}
if ($phone_digits === '' || !preg_match('/^[0-9]{9}$/', $phone_digits)) {
    $errors['phone'] = 'Téléphone invalide (9 chiffres requis)';
}

// Certaines vues peuvent envoyer 'manager' mais la table users n'a que 'user' et 'admin'
if ($role === 'manager') {
    $role = 'manager';
}
$allowedRoles = ['admin', 'manager'];
if ($role === '' || !in_array($role, $allowedRoles)) {
    $errors['role'] = 'Rôle invalide';
}

if ($password === '' || strlen($password) < 6) {
    $errors['password'] = 'Mot de passe minimum 6 caractères';
}

if ($password !== $confirm_password) {
    $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
}

// Vérifier unicité email
if (!isset($errors['email'])) {
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors['email'] = 'Cet email existe déjà';
        }
    } catch (PDOException $e) {
        // journaliser et renvoyer un message utile en mode debug
        error_log('Add_User uniqueness check error: ' . $e->getMessage());
        http_response_code(500);
        if (!empty($debug)) {
            echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
        }
        exit;
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Insérer l'utilisateur
try {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Adapter l'insertion au schéma actuel (certaines installations n'ont pas la colonne updated_at)
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, role, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $result = $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $phone_digits,
        $role,
        $hashedPassword
    ]);

    if ($result) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Employé ajouté avec succès',
            'user_id' => $pdo->lastInsertId()
        ]);
        exit;
    } else {
        throw new Exception('Erreur lors de l\'insertion');
    }
} catch (PDOException $e) {
    // journaliser l'erreur et renvoyer un message générique
    error_log('Add_User DB error: ' . $e->getMessage());
    http_response_code(500);
    if (!empty($debug)) {
        echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
    }
    exit;
}

?>
