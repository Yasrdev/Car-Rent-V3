<?php
class ReservationController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Créer une nouvelle réservation (front-end)
     */
    public function createReservation($reservationData) {
        try {
            // Valider les données
            $this->validateReservationData($reservationData);
            
            $this->pdo->beginTransaction();
            
            // 1. Vérifier si le client existe déjà par téléphone
            $clientId = $this->findOrCreateClient(
                $reservationData['first_name'],
                $reservationData['last_name'],
                $reservationData['phone'],
                $reservationData['email'] ?? null
            );
            
            // 2. Vérifier que la voiture existe et est disponible
            $car = $this->getCarDetails($reservationData['car_id']);
            if (!$car) {
                throw new Exception("La voiture sélectionnée n'existe pas.");
            }
            
            if ($car['status'] !== 'disponible') {
                throw new Exception("Désolé, cette voiture n'est actuellement pas disponible à la location.");
            }
            
            // 3. Vérifier la disponibilité de la voiture pour les dates sélectionnées
            if (!$this->isCarAvailable($reservationData['car_id'], $reservationData['start_date'], $reservationData['end_date'])) {
                throw new Exception("Désolé, cette voiture n'est pas disponible pour les dates sélectionnées. Veuillez choisir d'autres dates.");
            }
            
            // 4. Calculer le nombre de jours et le montant total
            $totalDays = $this->calculateTotalDays($reservationData['start_date'], $reservationData['end_date']);
            $dailyPrice = $car['daily_price'];
            $totalAmount = $totalDays * $dailyPrice;
            
            // 5. Créer la réservation
            $reservationId = $this->insertReservation([
                'client_id' => $clientId,
                'car_id' => $reservationData['car_id'],
                'fait_par' => 'Client',
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'start_time' => $reservationData['start_time'],
                'end_time' => $reservationData['end_time'],
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'special_requests' => $reservationData['special_requests'] ?? null,
                'status' => 'pending'
            ]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'reservation_id' => $reservationId,
                'message' => 'Votre réservation a été enregistrée avec succès. Nous vous contacterons dans les plus brefs délais pour confirmation.'
            ];
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer une réservation depuis le dashboard
     */
    public function createReservationDashboard($reservationData) {
        try {
            // Valider les données
            $this->validateReservationDataDashboard($reservationData);
            
            $this->pdo->beginTransaction();
            
            // 1. Vérifier ou créer le client
            $clientId = $this->findOrCreateClientDashboard($reservationData);
            
            // 2. Vérifier que la voiture existe
            $car = $this->getCarDetails($reservationData['car_id']);
            if (!$car) {
                throw new Exception("La voiture sélectionnée n'existe pas.");
            }
            
            // 3. Vérifier la disponibilité (exclure les réservations en cours de modification)
            if (!$this->isCarAvailable($reservationData['car_id'], $reservationData['start_date'], $reservationData['end_date'])) {
                throw new Exception("Cette voiture n'est pas disponible pour les dates sélectionnées.");
            }
            
            // 4. Calculer le total
            $totalDays = $this->calculateTotalDays($reservationData['start_date'], $reservationData['end_date']);
            $totalAmount = $totalDays * $car['daily_price'];
            
            // 5. Créer la réservation
            $reservationId = $this->insertReservationDashboard([
                'client_id' => $clientId,
                'car_id' => $reservationData['car_id'],
                'fait_par' => $reservationData['fait_par'],
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'start_time' => $reservationData['start_time'],
                'end_time' => $reservationData['end_time'],
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'special_requests' => $reservationData['special_requests'] ?? null,
                'status' => $reservationData['status'] ?? 'confirmed'
            ]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'reservation_id' => $reservationId,
                'message' => 'Réservation créée avec succès.'
            ];
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour une réservation
     */
    public function updateReservation($reservationData) {
        try {
            // Valider les données
            $this->validateReservationDataDashboard($reservationData);
            
            $this->pdo->beginTransaction();
            
            // 1. Vérifier que la réservation existe
            $existingReservation = $this->getReservationById($reservationData['reservation_id']);
            if (!$existingReservation) {
                throw new Exception("La réservation n'existe pas.");
            }
            
            // 2. Vérifier ou créer le client
            $clientId = $this->findOrCreateClientDashboard($reservationData);
            
            // 3. Vérifier la disponibilité (exclure la réservation actuelle)
            if (!$this->isCarAvailable($reservationData['car_id'], $reservationData['start_date'], $reservationData['end_date'], $reservationData['reservation_id'])) {
                throw new Exception("Cette voiture n'est pas disponible pour les dates sélectionnées.");
            }
            
            // 4. Calculer le nouveau total
            $totalDays = $this->calculateTotalDays($reservationData['start_date'], $reservationData['end_date']);
            $car = $this->getCarDetails($reservationData['car_id']);
            $totalAmount = $totalDays * $car['daily_price'];
            
            // 5. Mettre à jour la réservation
            $this->updateReservationData([
                'reservation_id' => $reservationData['reservation_id'],
                'client_id' => $clientId,
                'car_id' => $reservationData['car_id'],
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'start_time' => $reservationData['start_time'],
                'end_time' => $reservationData['end_time'],
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'special_requests' => $reservationData['special_requests'] ?? null,
                'status' => $reservationData['status']
            ]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Réservation mise à jour avec succès.'
            ];
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Valider les données de réservation (front-end)
     */
    private function validateReservationData($data) {
        $errors = [];
        
        // Validation des champs requis
        $required = [
            'first_name' => 'Le prénom',
            'last_name' => 'Le nom',
            'phone' => 'Le téléphone',
            'car_id' => 'La voiture',
            'start_date' => 'La date de début',
            'end_date' => 'La date de fin',
            'start_time' => 'L\'heure de début',
            'end_time' => 'L\'heure de fin'
        ];
        
        foreach ($required as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = "$label est requis.";
            }
        }
        
        // Validation du téléphone
        if (!empty($data['phone']) && !preg_match('/^[0-9+\-\s()]{10,20}$/', $data['phone'])) {
            $errors[] = "Le numéro de téléphone n'est pas valide.";
        }
        
        // Validation de l'email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }
        
        // Validation des dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $startDate = new DateTime($data['start_date']);
            $endDate = new DateTime($data['end_date']);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            if ($startDate < $today) {
                $errors[] = "La date de début ne peut pas être dans le passé.";
            }
            
            if ($endDate <= $startDate) {
                $errors[] = "La date de fin doit être après la date de début.";
            }
            
            // Vérifier que la réservation ne dépasse pas 30 jours
            $interval = $startDate->diff($endDate);
            if ($interval->days > 30) {
                $errors[] = "La durée de location ne peut pas dépasser 30 jours.";
            }
        }
        
        if (count($errors) > 0) {
            throw new Exception(implode(' ', $errors));
        }
    }
    
    /**
     * Valider les données pour le dashboard
     */
    private function validateReservationDataDashboard($data) {
        $errors = [];
        
        $required = [
            'client_first_name' => 'Le prénom du client',
            'client_last_name' => 'Le nom du client',
            'client_phone' => 'Le téléphone du client',
            'car_id' => 'La voiture',
            'start_date' => 'La date de début',
            'end_date' => 'La date de fin',
            'start_time' => 'L\'heure de début',
            'end_time' => 'L\'heure de fin'
        ];
        
        foreach ($required as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = "$label est requis.";
            }
        }
        
        // Validation du téléphone
        if (!empty($data['client_phone']) && !preg_match('/^[0-9+\-\s()]{10,20}$/', $data['client_phone'])) {
            $errors[] = "Le numéro de téléphone n'est pas valide.";
        }
        
        // Validation de l'email
        if (!empty($data['client_email']) && !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }
        
        // Validation des dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $startDate = new DateTime($data['start_date']);
            $endDate = new DateTime($data['end_date']);
            
            if ($endDate <= $startDate) {
                $errors[] = "La date de fin doit être après la date de début.";
            }
            
            // Vérifier que la réservation ne dépasse pas 30 jours
            $interval = $startDate->diff($endDate);
            if ($interval->days > 30) {
                $errors[] = "La durée de location ne peut pas dépasser 30 jours.";
            }
        }
        
        if (count($errors) > 0) {
            throw new Exception(implode(' ', $errors));
        }
    }
    
    /**
     * Trouver ou créer un client (front-end)
     */
    private function findOrCreateClient($firstName, $lastName, $phone, $email = null) {
        // Nettoyer le numéro de téléphone
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Vérifier si le client existe déjà par téléphone
        $stmt = $this->pdo->prepare("SELECT id FROM clients WHERE phone = ?");
        $stmt->execute([$cleanPhone]);
        $existingClient = $stmt->fetch();
        
        if ($existingClient) {
            return $existingClient['id'];
        }
        
        // Créer un nouveau client
        $stmt = $this->pdo->prepare("
            INSERT INTO clients (first_name, last_name, phone, email) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$firstName, $lastName, $cleanPhone, $email]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Trouver ou créer un client (version dashboard)
     */
    private function findOrCreateClientDashboard($data) {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $data['client_phone']);
        
        // Vérifier si le client existe
        $stmt = $this->pdo->prepare("SELECT id FROM clients WHERE phone = ?");
        $stmt->execute([$cleanPhone]);
        $existingClient = $stmt->fetch();
        
        if ($existingClient) {
            // Mettre à jour les informations si nécessaire
            $stmt = $this->pdo->prepare("UPDATE clients SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
            $stmt->execute([
                $data['client_first_name'],
                $data['client_last_name'],
                $data['client_email'] ?? null,
                $existingClient['id']
            ]);
            
            return $existingClient['id'];
        }
        
        // Créer un nouveau client
        $stmt = $this->pdo->prepare("
            INSERT INTO clients (first_name, last_name, phone, email) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['client_first_name'],
            $data['client_last_name'],
            $cleanPhone,
            $data['client_email'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Vérifier la disponibilité de la voiture (méthode publique pour usage externe)
     */
    public function checkCarAvailability($carId, $startDate, $endDate) {
        return $this->isCarAvailable($carId, $startDate, $endDate);
    }
    
    /**
     * Vérifier la disponibilité de la voiture (méthode privée)
     */
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
    
    /**
     * Obtenir les détails d'une voiture
     */
    private function getCarDetails($carId) {
        $stmt = $this->pdo->prepare("
            SELECT id, model, daily_price, status 
            FROM cars 
            WHERE id = ? AND status != 'indisponible'
        ");
        $stmt->execute([$carId]);
        return $stmt->fetch();
    }
    
    /**
     * Calculer le nombre total de jours
     */
    private function calculateTotalDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        return $interval->days;
    }
    
    /**
     * Insérer la réservation dans la base de données (front-end)
     */
    private function insertReservation($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reservations (
                client_id, car_id, fait_par, start_date, end_date, 
                start_time, end_time, total_days, total_amount, special_requests, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['client_id'],
            $data['car_id'],
            $data['fait_par'],
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $data['total_days'],
            $data['total_amount'],
            $data['special_requests'],
            $data['status']
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Insérer une réservation (version dashboard)
     */
    private function insertReservationDashboard($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reservations (
                client_id, car_id, fait_par, start_date, end_date, 
                start_time, end_time, total_days, total_amount, 
                special_requests, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['client_id'],
            $data['car_id'],
            $data['fait_par'],
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $data['total_days'],
            $data['total_amount'],
            $data['special_requests'],
            $data['status']
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Mettre à jour les données d'une réservation
     */
    private function updateReservationData($data) {
        $stmt = $this->pdo->prepare("
            UPDATE reservations SET
                client_id = ?, car_id = ?, start_date = ?, end_date = ?,
                start_time = ?, end_time = ?, total_days = ?, total_amount = ?,
                special_requests = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['client_id'],
            $data['car_id'],
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $data['total_days'],
            $data['total_amount'],
            $data['special_requests'],
            $data['status'],
            $data['reservation_id']
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Obtenir les réservations d'un client
     */
    public function getClientReservations($clientPhone) {
        try {
            $cleanPhone = preg_replace('/[^0-9+]/', '', $clientPhone);
            
            $stmt = $this->pdo->prepare("
                SELECT r.*, c.model, cb.name as brand_name, cl.first_name, cl.last_name
                FROM reservations r
                JOIN cars c ON r.car_id = c.id
                JOIN car_brands cb ON c.brand_id = cb.id
                JOIN clients cl ON r.client_id = cl.id
                WHERE cl.phone = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$cleanPhone]);
            
            return [
                'success' => true,
                'reservations' => $stmt->fetchAll()
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtenir toutes les réservations (pour le dashboard)
     */
    public function getAllReservations() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
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
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            
            return [
                'success' => true,
                'reservations' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtenir les statistiques des réservations
     */
    public function getReservationStats() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM reservations
            ");
            $stmt->execute();
            
            return [
                'success' => true,
                'stats' => $stmt->fetch(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtenir une réservation par ID
     */
    public function getReservationById($reservationId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.*,
                    c.first_name as client_first_name,
                    c.last_name as client_last_name,
                    c.phone as client_phone,
                    c.email as client_email,
                    CONCAT(c.first_name, ' ', c.last_name) as client_name,
                    car.model as car_model,
                    car.year as car_year,
                    car.main_image_url,
                    car.daily_price,
                    brand.name as car_brand
                FROM reservations r
                JOIN clients c ON r.client_id = c.id
                JOIN cars car ON r.car_id = car.id
                JOIN car_brands brand ON car.brand_id = brand.id
                WHERE r.id = ?
            ");
            $stmt->execute([$reservationId]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($reservation) {
                return [
                    'success' => true,
                    'reservation' => $reservation
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtenir les voitures disponibles
     */
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

            $params = [];
            
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
                
                $params = array_merge($params, [$startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
            }

            $sql .= " ORDER BY b.name, c.model";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'cars' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Annuler une réservation
     */
    public function cancelReservation($reservationId, $clientPhone) {
        try {
            $this->pdo->beginTransaction();
            
            // Vérifier que la réservation appartient au client
            $stmt = $this->pdo->prepare("
                SELECT r.id 
                FROM reservations r
                JOIN clients c ON r.client_id = c.id
                WHERE r.id = ? AND c.phone = ? AND r.status = 'pending'
            ");
            $stmt->execute([$reservationId, $clientPhone]);
            $reservation = $stmt->fetch();
            
            if (!$reservation) {
                throw new Exception("Réservation non trouvée ou ne peut pas être annulée.");
            }
            
            // Mettre à jour le statut
            $stmt = $this->pdo->prepare("
                UPDATE reservations 
                SET status = 'cancelled', updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$reservationId]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Réservation annulée avec succès.'
            ];
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprimer une réservation
     */
    public function deleteReservation($reservationId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE id = ?");
            $stmt->execute([$reservationId]);
            
            return [
                'success' => true,
                'message' => 'Réservation supprimée avec succès.'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Mettre à jour le statut d'une réservation
     */
    public function updateReservationStatus($reservationId, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE reservations 
                SET status = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$status, $reservationId]);
            
            return [
                'success' => true,
                'message' => 'Statut de la réservation mis à jour avec succès.'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// Traitement des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        // Vérifier si c'est une requête JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            // Si ce n'est pas du JSON, essayer avec form-data
            $input = $_POST;
        }
        
        // Inclure la configuration de la base de données
        require_once '../config/db-config.php';
        $reservationController = new ReservationController($pdo);
        
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'create_reservation':
                    if (!isset($input['data'])) {
                        throw new Exception("Données de réservation manquantes.");
                    }
                    $result = $reservationController->createReservation($input['data']);
                    echo json_encode($result);
                    break;
                    
                case 'create_reservation_dashboard':
                    if (!isset($input['client_first_name']) || !isset($input['car_id'])) {
                        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                        break;
                    }
                    $result = $reservationController->createReservationDashboard($input);
                    echo json_encode($result);
                    break;
                    
                case 'update_reservation':
                    if (!isset($input['reservation_id'])) {
                        echo json_encode(['success' => false, 'message' => 'ID de réservation manquant']);
                        break;
                    }
                    $result = $reservationController->updateReservation($input);
                    echo json_encode($result);
                    break;
                    
                case 'update_reservation_status':
                    if (!isset($input['reservation_id']) || !isset($input['status'])) {
                        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                        break;
                    }
                    $result = $reservationController->updateReservationStatus($input['reservation_id'], $input['status']);
                    echo json_encode($result);
                    break;
                    
                case 'delete_reservation':
                    if (!isset($input['reservation_id'])) {
                        echo json_encode(['success' => false, 'message' => 'ID de réservation manquant']);
                        break;
                    }
                    $result = $reservationController->deleteReservation($input['reservation_id']);
                    echo json_encode($result);
                    break;
                    
                case 'get_client_reservations':
                    if (!isset($input['phone'])) {
                        throw new Exception("Numéro de téléphone manquant.");
                    }
                    $result = $reservationController->getClientReservations($input['phone']);
                    echo json_encode($result);
                    break;
                    
                case 'cancel_reservation':
                    if (!isset($input['reservation_id']) || !isset($input['phone'])) {
                        throw new Exception("Données manquantes pour l'annulation.");
                    }
                    $result = $reservationController->cancelReservation($input['reservation_id'], $input['phone']);
                    echo json_encode($result);
                    break;
                    
                default:
                    echo json_encode([
                        'success' => false,
                        'message' => 'Action non reconnue'
                    ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Aucune action spécifiée'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Traitement des requêtes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        // Inclure la configuration de la base de données
        require_once '../config/db-config.php';
        $reservationController = new ReservationController($pdo);
        
        switch ($_GET['action']) {
            case 'check_availability':
                $carId = $_GET['car_id'] ?? null;
                $startDate = $_GET['start_date'] ?? null;
                $endDate = $_GET['end_date'] ?? null;
                
                if (!$carId || !$startDate || !$endDate) {
                    throw new Exception("Paramètres manquants");
                }
                
                $isAvailable = $reservationController->checkCarAvailability($carId, $startDate, $endDate);
                
                echo json_encode([
                    'success' => true,
                    'available' => $isAvailable
                ]);
                break;
                
            case 'get_reservations':
                $result = $reservationController->getAllReservations();
                echo json_encode($result);
                break;
                
            case 'get_reservation':
                if (!isset($_GET['reservation_id'])) {
                    echo json_encode(['success' => false, 'message' => 'ID de réservation manquant']);
                    break;
                }
                $result = $reservationController->getReservationById($_GET['reservation_id']);
                echo json_encode($result);
                break;
                
            case 'get_stats':
                $result = $reservationController->getReservationStats();
                echo json_encode($result);
                break;
                
            case 'get_available_cars':
                $startDate = $_GET['start_date'] ?? null;
                $endDate = $_GET['end_date'] ?? null;
                $result = $reservationController->getAvailableCars($startDate, $endDate);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Action non reconnue'
                ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>