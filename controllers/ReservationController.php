<?php
// controllers/ReservationController.php

// Activer l'affichage des erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db-config.php';

// Vérifier si les modèles existent
$modelsPath = __DIR__ . '/../models/';
if (!file_exists($modelsPath . 'Client.php')) {
    die(json_encode(['success' => false, 'message' => 'Fichier Client.php manquant']));
}
if (!file_exists($modelsPath . 'Reservation.php')) {
    die(json_encode(['success' => false, 'message' => 'Fichier Reservation.php manquant']));
}

require_once $modelsPath . 'Client.php';
require_once $modelsPath . 'Reservation.php';

class ReservationController {
    private $clientModel;
    private $reservationModel;
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->clientModel = new Client($pdo);
        $this->reservationModel = new Reservation($pdo);
    }
    
    public function createReservation() {
        header('Content-Type: application/json');
        
        // Log pour debug
        error_log("ReservationController appelé");
        error_log("POST data: " . print_r($_POST, true));
        
        try {
            // Vérifier la connexion PDO
            if (!$this->pdo) {
                throw new Exception("Erreur de connexion à la base de données");
            }
            
            // Validation des données requises
            $requiredFields = ['first-name', 'last-name', 'Telephone', 'pickup-date', 'return-date', 'pickup-time', 'return-time', 'car_id'];
            
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Le champ " . $this->getFieldName($field) . " est requis.");
                }
            }
            
            // Récupération des données
            $firstName = trim($_POST['first-name']);
            $lastName = trim($_POST['last-name']);
            $phone = trim($_POST['Telephone']);
            $carId = intval($_POST['car_id']);
            $pickupDate = $_POST['pickup-date'];
            $returnDate = $_POST['return-date'];
            $pickupTime = $_POST['pickup-time'];
            $returnTime = $_POST['return-time'];
            $specialRequests = isset($_POST['special-requests']) ? trim($_POST['special-requests']) : '';
            $dailyPrice = isset($_POST['daily_price']) ? floatval($_POST['daily_price']) : 0;
            
            // Validation basique
            if (strlen($firstName) < 2) {
                throw new Exception("Le nom doit contenir au moins 2 caractères.");
            }
            
            if (strlen($lastName) < 2) {
                throw new Exception("Le prénom doit contenir au moins 2 caractères.");
            }
            
            // Validation des dates
            $today = new DateTime();
            $pickupDateTime = new DateTime($pickupDate);
            $returnDateTime = new DateTime($returnDate);
            
            if ($pickupDateTime < $today) {
                throw new Exception("La date de prise en charge ne peut pas être dans le passé.");
            }
            
            if ($returnDateTime <= $pickupDateTime) {
                throw new Exception("La date de restitution doit être postérieure à la date de prise en charge.");
            }
            
            // Calcul de la durée
            $interval = $returnDateTime->diff($pickupDateTime);
            $totalDays = $interval->days + 1;
            $totalAmount = $totalDays * $dailyPrice;
            
            // Vérifier la disponibilité
            if (!$this->reservationModel->isCarAvailable($carId, $pickupDate, $returnDate)) {
                throw new Exception("La voiture n'est pas disponible pour les dates sélectionnées.");
            }
            
            // Créer ou récupérer le client sans écraser un autre profil
            $client = $this->clientModel->getClientByIdentity($firstName, $lastName, $phone);
            if (!$client) {
                $clientId = $this->clientModel->createClient($firstName, $lastName, $phone);
            } else {
                $clientId = $client['id'];
            }
            
            // Créer la réservation
            $reservationId = $this->reservationModel->createReservation([
                'client_id' => $clientId,
                'car_id' => $carId,
                'fait_par' => 'Client',
                'start_date' => $pickupDate,
                'end_date' => $returnDate,
                'start_time' => $pickupTime,
                'end_time' => $returnTime,
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'special_requests' => $specialRequests
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Votre réservation a été créée avec succès! Numéro de réservation: #' . $reservationId,
                'reservation_id' => $reservationId
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur dans createReservation: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function getFieldName($field) {
        $fieldNames = [
            'first-name' => 'nom',
            'last-name' => 'prénom',
            'Telephone' => 'téléphone',
            'pickup-date' => 'date de prise en charge',
            'return-date' => 'date de restitution',
            'pickup-time' => 'heure de prise en charge',
            'return-time' => 'heure de restitution'
        ];
        
        return $fieldNames[$field] ?? $field;
    }
}

// Traitement de la requête
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_reservation') {
    error_log("Action create_reservation détectée");
    $reservationController = new ReservationController($pdo);
    $reservationController->createReservation();
    exit;
} else {
    error_log("Action non reconnue ou méthode incorrecte");
    echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
}
?>