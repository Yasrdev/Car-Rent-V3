<?php
// Ne pas appeler session_start() ici car config/db-config.php le fait déjà

// Retourner toujours du JSON (y compris en cas d'erreur)
header('Content-Type: application/json; charset=utf-8');

$debug = true;

set_exception_handler(function($e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once '../config/db-config.php';
require_once '../models/User.php';

// Vérifier si l'utilisateur est connecté et a les droits
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérifier les données requises
if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID employé manquant']);
    exit;
}

$employeeId = (int)$_POST['employee_id'];
$userModel = new User($pdo);

try {
    // Vérifier si l'employé existe
    $existingEmployee = $userModel->getUserById($employeeId);
    if (!$existingEmployee) {
        echo json_encode(['success' => false, 'message' => 'Employé non trouvé']);
        exit;
    }

    // Validation des données
    $errors = [];

    // Validation des champs requis
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'role'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Ce champ est requis';
        }
    }

    // Validation email
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email invalide';
    }

    // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
    if (!empty($_POST['email'])) {
        $existingUser = $userModel->getUserByEmail($_POST['email']);
        if ($existingUser && $existingUser['id'] != $employeeId) {
            $errors['email'] = 'Cet email est déjà utilisé';
        }
    }

    // Validation téléphone
    if (!empty($_POST['phone']) && !preg_match('/^[0-9]{9}$/', $_POST['phone'])) {
        $errors['phone'] = 'Le téléphone doit contenir 9 chiffres';
    }

    // Validation mot de passe
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
        } elseif ($_POST['password'] !== $_POST['confirm_password']) {
            $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
        }
    }

    // Si erreurs de validation
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // Préparer les données pour la mise à jour
    $updateData = [
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'role' => $_POST['role']
    ];

    // Ajouter le mot de passe seulement si fourni
    if (!empty($_POST['password'])) {
        $updateData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Mettre à jour l'employé
    $result = $userModel->updateUser($employeeId, $updateData);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Employé modifié avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune modification effectuée']);
    }
    
} catch (Exception $e) {
    error_log('Erreur modification employé: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}