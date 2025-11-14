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

// Vérifier session et permissions
$currentUserId = $_SESSION['user_id'] ?? null;
$currentUserRole = $_SESSION['user_role'] ?? null;

if (!$currentUserId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Autoriser uniquement les managers et admins
$allowed = ['admin', 'manager'];
if (!in_array($currentUserRole, $allowed, true)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission refusée']);
    exit;
}

$action = $_POST['action'] ?? '';
$employeeId = $_POST['employee_id'] ?? null;

if ($action !== 'delete_employee' || !$employeeId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

$employeeId = intval($employeeId);
if ($employeeId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID employé invalide']);
    exit;
}

try {
    // Récupérer l'utilisateur cible
    $stmt = $pdo->prepare('SELECT id, role, first_name, last_name, email FROM users WHERE id = ?');
    $stmt->execute([$employeeId]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

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

    // Si la cible est un admin, seul un admin peut supprimer
    if ($target['role'] === 'admin' && $currentUserRole !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Impossible de supprimer un administrateur']);
        exit;
    }

    // Effectuer la suppression
    $del = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $ok = $del->execute([$employeeId]);

    if ($ok) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Employé supprimé avec succès']);
        exit;
    } else {
        throw new Exception('Échec de la suppression');
    }

} catch (PDOException $e) {
    error_log('Delete_User DB error: ' . $e->getMessage());
    http_response_code(500);
    if (!empty($debug)) {
        echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
    }
    exit;
}

?>
