<?php
// models/Reservation.php
class Reservation {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer toutes les réservations avec les informations des clients et voitures
    public function getAllReservations() {
        try {
            $sql = "SELECT 
                    r.id,
                    r.start_date,
                    r.end_date,
                    r.start_time,
                    r.end_time,
                    r.total_days,
                    r.total_amount,
                    r.status,
                    r.special_requests,
                    r.created_at,
                    r.fait_par,
                    CONCAT(c.first_name, ' ', c.last_name) as client_name,
                    c.phone as client_phone,
                    c.email as client_email,
                    car.id as car_id,
                    car.model as car_model,
                    car.year as car_year,
                    car.main_image_url,
                    car.daily_price,
                    brand.name as car_brand,
                    cat.name as car_category
                FROM reservations r
                JOIN clients c ON r.client_id = c.id
                JOIN cars car ON r.car_id = car.id
                JOIN car_brands brand ON car.brand_id = brand.id
                LEFT JOIN car_categories cat ON car.category_id = cat.id
                ORDER BY r.created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des réservations: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer les statistiques des réservations
    public function getReservationStats() {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM reservations";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'active' => 0, 'completed' => 0, 'cancelled' => 0];
        }
    }

    // Récupérer une réservation par ID
    public function getReservationById($id) {
        try {
            $sql = "SELECT 
                    r.*,
                    CONCAT(c.first_name, ' ', c.last_name) as client_name,
                    c.phone as client_phone,
                    c.email as client_email,
                    car.model as car_model,
                    car.year as car_year,
                    car.main_image_url,
                    car.daily_price,
                    brand.name as car_brand,
                    cat.name as car_category
                FROM reservations r
                JOIN clients c ON r.client_id = c.id
                JOIN cars car ON r.car_id = car.id
                JOIN car_brands brand ON car.brand_id = brand.id
                LEFT JOIN car_categories cat ON car.category_id = cat.id
                WHERE r.id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la réservation: " . $e->getMessage());
            return null;
        }
    }

    // Créer une réservation (pour le dashboard)
    public function createReservation($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Vérifier ou créer le client
            $clientId = $this->findOrCreateClient($data);

            // 2. Vérifier la disponibilité de la voiture
            if (!$this->isCarAvailable($data['car_id'], $data['start_date'], $data['end_date'], $data['reservation_id'] ?? null)) {
                throw new Exception("La voiture n'est pas disponible pour les dates sélectionnées.");
            }

            // 3. Calculer le total
            $totalDays = $this->calculateTotalDays($data['start_date'], $data['end_date']);
            $car = $this->getCarById($data['car_id']);
            $totalAmount = $totalDays * $car['daily_price'];

            // 4. Créer la réservation
            $sql = "INSERT INTO reservations (
                client_id, car_id, fait_par, start_date, end_date, 
                start_time, end_time, total_days, total_amount, 
                special_requests, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $clientId,
                $data['car_id'],
                $data['fait_par'],
                $data['start_date'],
                $data['end_date'],
                $data['start_time'],
                $data['end_time'],
                $totalDays,
                $totalAmount,
                $data['special_requests'] ?? null,
                $data['status'] ?? 'confirmed'
            ]);

            $reservationId = $this->pdo->lastInsertId();
            $this->pdo->commit();

            return $reservationId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Mettre à jour une réservation
    public function updateReservation($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Vérifier ou créer le client
            $clientId = $this->findOrCreateClient($data);

            // 2. Vérifier la disponibilité (exclure la réservation actuelle)
            if (!$this->isCarAvailable($data['car_id'], $data['start_date'], $data['end_date'], $id)) {
                throw new Exception("La voiture n'est pas disponible pour les dates sélectionnées.");
            }

            // 3. Calculer le nouveau total
            $totalDays = $this->calculateTotalDays($data['start_date'], $data['end_date']);
            $car = $this->getCarById($data['car_id']);
            $totalAmount = $totalDays * $car['daily_price'];

            // 4. Mettre à jour la réservation
            $sql = "UPDATE reservations SET
                client_id = ?, car_id = ?, start_date = ?, end_date = ?,
                start_time = ?, end_time = ?, total_days = ?, total_amount = ?,
                special_requests = ?, status = ?, updated_at = NOW()
                WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $clientId,
                $data['car_id'],
                $data['start_date'],
                $data['end_date'],
                $data['start_time'],
                $data['end_time'],
                $totalDays,
                $totalAmount,
                $data['special_requests'] ?? null,
                $data['status'],
                $id
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Mettre à jour le statut d'une réservation
    public function updateStatus($reservationId, $status) {
        try {
            $sql = "UPDATE reservations SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$status, $reservationId]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut: " . $e->getMessage());
            return false;
        }
    }

    // Supprimer une réservation
    public function deleteReservation($reservationId) {
        try {
            $sql = "DELETE FROM reservations WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$reservationId]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la réservation: " . $e->getMessage());
            return false;
        }
    }

    // Méthodes utilitaires privées
    private function findOrCreateClient($data) {
        // Vérifier si le client existe par téléphone
        $stmt = $this->pdo->prepare("SELECT id FROM clients WHERE phone = ?");
        $stmt->execute([$data['client_phone']]);
        $client = $stmt->fetch();

        if ($client) {
            return $client['id'];
        }

        // Créer un nouveau client
        $stmt = $this->pdo->prepare("
            INSERT INTO clients (first_name, last_name, phone, email) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['client_first_name'],
            $data['client_last_name'],
            $data['client_phone'],
            $data['client_email'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    private function isCarAvailable($carId, $startDate, $endDate, $excludeReservationId = null) {
        $sql = "SELECT COUNT(*) as count 
                FROM reservations 
                WHERE car_id = ? 
                AND status IN ('confirmed', 'active')
                AND id != ?
                AND (
                    (start_date BETWEEN ? AND ?) 
                    OR (end_date BETWEEN ? AND ?)
                    OR (? BETWEEN start_date AND end_date)
                    OR (? BETWEEN start_date AND end_date)
                )";

        $stmt = $this->pdo->prepare($sql);
        $params = [
            $carId,
            $excludeReservationId ?: 0,
            $startDate, $endDate,
            $startDate, $endDate,
            $startDate, $endDate
        ];
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] == 0;
    }

    private function calculateTotalDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        return $interval->days;
    }

    private function getCarById($carId) {
        $stmt = $this->pdo->prepare("SELECT id, daily_price FROM cars WHERE id = ?");
        $stmt->execute([$carId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les voitures disponibles
    public function getAvailableCars($startDate = null, $endDate = null) {
        try {
            $sql = "SELECT 
                    c.id, c.model, c.year, c.daily_price, c.main_image_url,
                    b.name as brand_name,
                    cat.name as category_name
                FROM cars c
                JOIN car_brands b ON c.brand_id = b.id
                LEFT JOIN car_categories cat ON c.category_id = cat.id
                WHERE c.status = 'disponible'";

            if ($startDate && $endDate) {
                $sql .= " AND c.id NOT IN (
                    SELECT car_id FROM reservations 
                    WHERE status IN ('confirmed', 'active')
                    AND (
                        (start_date BETWEEN ? AND ?) 
                        OR (end_date BETWEEN ? AND ?)
                        OR (? BETWEEN start_date AND end_date)
                        OR (? BETWEEN start_date AND end_date)
                    )
                )";
            }

            $sql .= " ORDER BY b.name, c.model";

            $stmt = $this->pdo->prepare($sql);
            
            if ($startDate && $endDate) {
                $stmt->execute([$startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
            } else {
                $stmt->execute();
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des voitures disponibles: " . $e->getMessage());
            return [];
        }
    }
}
?>