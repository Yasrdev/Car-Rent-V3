<?php
// controllers/AdminReservationController.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db-config.php';
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Car.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentification requise']);
    exit;
}

$authorizedRoles = ['admin', 'manager'];
if (!in_array($_SESSION['user_role'], $authorizedRoles, true)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission refusée']);
    exit;
}

$action = $_POST['action'] ?? '';
if ($action === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action manquante']);
    exit;
}

$reservationModel = new Reservation($pdo);
$clientModel = new Client($pdo);
$carModel = new Car($pdo);

// Mettre à jour automatiquement les statuts avant traitement
$reservationModel->autoActivateReservations();

try {
    switch ($action) {
        case 'create_employee_reservation':
            createReservationAsEmployee($reservationModel, $clientModel, $carModel);
            break;
        case 'update_reservation':
            updateReservation($reservationModel, $carModel);
            break;
        case 'delete_reservation':
            deleteReservation($reservationModel);
            break;
        case 'get_reservation_details':
            getReservationDetails($reservationModel);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            exit;
    }
} catch (Exception $e) {
    error_log('Erreur AdminReservationController: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}

function sanitizePhone($phone)
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (strlen($digits) > 9) {
        $digits = substr($digits, -9);
    }
    return $digits;
}

function validateDates($pickupDate, $returnDate)
{
    $start = new DateTime($pickupDate);
    $end = new DateTime($returnDate);

    if ($end <= $start) {
        throw new Exception("La date de fin doit être postérieure à la date de début.");
    }

    return [$start, $end];
}

function getStatusFromRequest()
{
    $allowedStatus = ['pending', 'confirmed', 'active', 'completed', 'cancelled'];
    $status = $_POST['status'] ?? 'pending';
    if (!in_array($status, $allowedStatus, true)) {
        throw new Exception('Statut invalide.');
    }
    return $status;
}

function createReservationAsEmployee($reservationModel, $clientModel, $carModel)
{
    $firstName = trim($_POST['client_first_name'] ?? '');
    $lastName = trim($_POST['client_last_name'] ?? '');
    $phone = sanitizePhone($_POST['client_phone'] ?? '');
    $carId = (int)($_POST['car_id'] ?? 0);
    $pickupDate = $_POST['pickup_date'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';
    $pickupTime = $_POST['pickup_time'] ?? '09:00';
    $returnTime = $_POST['return_time'] ?? '09:00';
    $specialRequests = trim($_POST['special_requests'] ?? '');
    $status = getStatusFromRequest();

    if (!$firstName || !$lastName || !$phone || !$carId || !$pickupDate || !$returnDate) {
        throw new Exception('Tous les champs obligatoires doivent être remplis.');
    }

    [$startDateTime, $endDateTime] = validateDates($pickupDate, $returnDate);

    $car = $carModel->getCarById($carId);
    if (!$car) {
        throw new Exception('Voiture introuvable.');
    }

    $startDateFormatted = $startDateTime->format('Y-m-d');
    $endDateFormatted = $endDateTime->format('Y-m-d');

    if (!$reservationModel->isCarAvailable($carId, $startDateFormatted, $endDateFormatted)) {
        throw new Exception("La voiture n'est pas disponible sur cette période.");
    }

    $interval = $endDateTime->diff($startDateTime);
    $totalDays = $interval->days + 1;
    $dailyPrice = (float)$car['daily_price'];
    $totalAmount = $dailyPrice * $totalDays;

    $client = $clientModel->getClientByIdentity($firstName, $lastName, $phone);
    if (!$client) {
        $clientId = $clientModel->createClient($firstName, $lastName, $phone);
    } else {
        $clientId = $client['id'];
    }

    $reservationId = $reservationModel->createReservation([
        'client_id' => $clientId,
        'car_id' => $carId,
        'employee_id' => $_SESSION['user_id'],
        'fait_par' => 'Employé',
        'start_date' => $startDateFormatted,
        'end_date' => $endDateFormatted,
        'start_time' => $pickupTime,
        'end_time' => $returnTime,
        'total_days' => $totalDays,
        'total_amount' => $totalAmount,
        'special_requests' => $specialRequests,
        'status' => $status
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Réservation créée avec succès.',
        'reservation_id' => $reservationId
    ]);
}

function updateReservation($reservationModel, $carModel)
{
    $reservationId = (int)($_POST['reservation_id'] ?? 0);
    $carId = (int)($_POST['car_id'] ?? 0);
    $pickupDate = $_POST['pickup_date'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';
    $pickupTime = $_POST['pickup_time'] ?? '09:00';
    $returnTime = $_POST['return_time'] ?? '09:00';
    $specialRequests = trim($_POST['special_requests'] ?? '');
    $status = getStatusFromRequest();
    $faitPar = $_POST['fait_par'] ?? 'Employé';

    if (!$reservationId || !$carId || !$pickupDate || !$returnDate) {
        throw new Exception('Les champs requis sont manquants.');
    }

    [$startDateTime, $endDateTime] = validateDates($pickupDate, $returnDate);
    $car = $carModel->getCarById($carId);
    if (!$car) {
        throw new Exception('Voiture introuvable.');
    }

    $startDateFormatted = $startDateTime->format('Y-m-d');
    $endDateFormatted = $endDateTime->format('Y-m-d');

    if (!$reservationModel->isCarAvailableForUpdate($carId, $startDateFormatted, $endDateFormatted, $reservationId)) {
        throw new Exception("La voiture n'est pas disponible sur cette période.");
    }

    $interval = $endDateTime->diff($startDateTime);
    $totalDays = $interval->days + 1;
    $dailyPrice = (float)$car['daily_price'];
    $totalAmount = $dailyPrice * $totalDays;

    $reservationModel->updateReservation($reservationId, [
        'car_id' => $carId,
        'employee_id' => $_SESSION['user_id'],
        'fait_par' => $faitPar,
        'start_date' => $startDateFormatted,
        'end_date' => $endDateFormatted,
        'start_time' => $pickupTime,
        'end_time' => $returnTime,
        'total_days' => $totalDays,
        'total_amount' => $totalAmount,
        'special_requests' => $specialRequests,
        'status' => $status
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Réservation mise à jour.'
    ]);
}

function deleteReservation($reservationModel)
{
    $reservationId = (int)($_POST['reservation_id'] ?? 0);
    if (!$reservationId) {
        throw new Exception('ID réservation manquant.');
    }

    $reservationModel->deleteReservation($reservationId);

    echo json_encode([
        'success' => true,
        'message' => 'Réservation supprimée.'
    ]);
}

function getReservationDetails($reservationModel)
{
    $reservationId = (int)($_POST['reservation_id'] ?? 0);
    if (!$reservationId) {
        throw new Exception('ID réservation manquant.');
    }

    $reservation = $reservationModel->getReservationById($reservationId);
    if (!$reservation) {
        throw new Exception('Réservation introuvable.');
    }

    echo json_encode([
        'success' => true,
        'reservation' => $reservation
    ]);
}
?>

