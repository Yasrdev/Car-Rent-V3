<?php
// UserController.php
header('Content-Type: application/json; charset=utf-8');

// Mode debug - mettre à false en production
$debug = true;

// Gestion des erreurs
set_exception_handler(function($e) use ($debug) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $debug ? $e->getMessage() : 'Erreur serveur'
    ]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once __DIR__ . '/../config/db-config.php';
require_once __DIR__ . '/../models/User.php';

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérifier l'action
$action = $_POST['action'] ?? '';
if (empty($action)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action non spécifiée']);
    exit;
}

// Vérifier l'authentification et les permissions
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

$currentUserId = $_SESSION['user_id'];
$currentUserRole = $_SESSION['user_role'];
$userModel = new User($pdo);

// Router les actions
switch ($action) {
    case 'add_user':
        addUser($userModel, $currentUserRole);
        break;
    case 'update_user':
        updateUser($userModel, $currentUserRole);
        break;
    case 'delete_user':
        deleteUser($userModel, $currentUserRole, $currentUserId);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        exit;
}

// ========== FONCTIONS ==========

/**
 * Ajouter un utilisateur
 */
function addUser($userModel, $currentUserRole) {
    // Vérifier les permissions - seul le manager peut ajouter des utilisateurs
    if ($currentUserRole !== 'manager') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission refusée - Rôle insuffisant']);
        exit;
    }

    // Récupérer et valider les données
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validation
    if (empty($first_name)) {
        $errors['first_name'] = 'Le prénom est requis';
    }

    if (empty($last_name)) {
        $errors['last_name'] = 'Le nom est requis';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email invalide';
    }

    // Validation téléphone
    $phone_digits = preg_replace('/\D+/', '', $phone);
    if (strpos($phone_digits, '212') === 0 && strlen($phone_digits) > 9) {
        $phone_digits = substr($phone_digits, -9);
    }
    if (empty($phone_digits) || !preg_match('/^[0-9]{9}$/', $phone_digits)) {
        $errors['phone'] = 'Téléphone invalide (9 chiffres requis)';
    }

    // Validation rôle
    $allowedRoles = ['admin', 'manager'];
    if (empty($role) || !in_array($role, $allowedRoles)) {
        $errors['role'] = 'Rôle invalide';
    }

    // Validation mot de passe
    if (empty($password) || strlen($password) < 6) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
    }

    // Vérifier l'unicité de l'email
    if (!isset($errors['email'])) {
        $existingUser = $userModel->getUserByEmail($email);
        if ($existingUser) {
            $errors['email'] = 'Cet email existe déjà';
        }
    }

    // Si erreurs de validation
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // Créer l'utilisateur
    try {
        $userId = $userModel->createUser([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone_digits,
            'role' => $role,
            'password' => $password
        ]);

        if ($userId) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Employé ajouté avec succès',
                'user_id' => $userId
            ]);
        } else {
            throw new Exception('Erreur lors de la création de l\'utilisateur');
        }
    } catch (Exception $e) {
        error_log('Erreur création utilisateur: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de l\'ajout de l\'employé'
        ]);
    }
}

/**
 * Modifier un utilisateur
 */
function updateUser($userModel, $currentUserRole) {
    // Vérifier les permissions - seul le manager peut modifier des utilisateurs
    if ($currentUserRole !== 'manager') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission refusée - Rôle insuffisant']);
        exit;
    }

    // Vérifier les données requises
    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID employé manquant']);
        exit;
    }

    $employeeId = (int)$_POST['employee_id'];

    // Vérifier si l'employé existe
    $existingEmployee = $userModel->getUserById($employeeId);
    if (!$existingEmployee) {
        http_response_code(404);
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

    // Validation rôle
    $allowedRoles = ['admin', 'manager'];
    if (!empty($_POST['role']) && !in_array($_POST['role'], $allowedRoles)) {
        $errors['role'] = 'Rôle invalide';
    }

    // Validation mot de passe
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
        } elseif ($_POST['password'] !== ($_POST['confirm_password'] ?? '')) {
            $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
        }
    }

    // Si erreurs de validation
    if (!empty($errors)) {
        http_response_code(400);
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
        $updateData['password'] = $_POST['password'];
    }

    // Mettre à jour l'employé
    try {
        $result = $userModel->updateUser($employeeId, $updateData);
        
        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Employé modifié avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Aucune modification effectuée'
            ]);
        }
    } catch (Exception $e) {
        error_log('Erreur modification employé: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de la modification de l\'employé'
        ]);
    }
}

/**
 * Supprimer un utilisateur
 */
function deleteUser($userModel, $currentUserRole, $currentUserId) {
    // Vérifier les permissions - seul le manager peut supprimer des utilisateurs
    if ($currentUserRole !== 'manager') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permission refusée - Rôle insuffisant']);
        exit;
    }

    // Vérifier les données requises
    if (!isset($_POST['employee_id']) || empty($_POST['employee_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID employé manquant']);
        exit;
    }

    $employeeId = (int)$_POST['employee_id'];
    if ($employeeId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID employé invalide']);
        exit;
    }

    try {
        // Récupérer l'utilisateur cible
        $target = $userModel->getUserById($employeeId);
        if (!$target) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Employé introuvable']);
            exit;
        }

        // Ne pas permettre à un utilisateur de se supprimer lui-même
        if ($target['id'] == $currentUserId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte']);
            exit;
        }

        // Vérifier les permissions spécifiques
        // Un manager ne peut pas supprimer un autre manager
        if ($target['role'] === 'manager') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Impossible de supprimer un manager']);
            exit;
        }

        // Effectuer la suppression
        $result = $userModel->deleteUser($employeeId);

        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Employé supprimé avec succès'
            ]);
        } else {
            throw new Exception('Échec de la suppression');
        }

    } catch (Exception $e) {
        error_log('Erreur suppression employé: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de la suppression de l\'employé'
        ]);
    }
}
?>