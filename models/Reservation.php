<?php
// models/Reservation.php

class Reservation {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function isCarAvailable($carId, $startDate, $endDate) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM reservations 
                WHERE car_id = ? 
                AND status IN ('pending', 'confirmed', 'active')
                AND (
                    (start_date BETWEEN ? AND ?) 
                    OR (end_date BETWEEN ? AND ?)
                    OR (? BETWEEN start_date AND end_date)
                    OR (? BETWEEN start_date AND end_date)
                )
            ");
            $stmt->execute([$carId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
            $count = $stmt->fetchColumn();
            
            return $count == 0;
        } catch (PDOException $e) {
            error_log("Erreur isCarAvailable: " . $e->getMessage());
            return false;
        }
    }

    public function isCarAvailableForUpdate($carId, $startDate, $endDate, $reservationId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM reservations 
                WHERE car_id = ? 
                AND id != ?
                AND status IN ('pending', 'confirmed', 'active')
                AND (
                    (start_date BETWEEN ? AND ?) 
                    OR (end_date BETWEEN ? AND ?)
                    OR (? BETWEEN start_date AND end_date)
                    OR (? BETWEEN start_date AND end_date)
                )
            ");
            $stmt->execute([$carId, $reservationId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);
            $count = $stmt->fetchColumn();
            
            return $count == 0;
        } catch (PDOException $e) {
            error_log("Erreur isCarAvailableForUpdate: " . $e->getMessage());
            return false;
        }
    }
    
    public function createReservation($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reservations 
                (client_id, car_id, employee_id, fait_par, start_date, end_date, start_time, end_time, total_days, total_amount, special_requests, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['client_id'],
                $data['car_id'],
                $data['employee_id'] ?? null,
                $data['fait_par'],
                $data['start_date'],
                $data['end_date'],
                $data['start_time'],
                $data['end_time'],
                $data['total_days'],
                $data['total_amount'],
                $data['special_requests'],
                $data['status'] ?? 'pending'
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur createReservation: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de la réservation: " . $e->getMessage());
        }
    }

    public function getAllReservationsWithDetails() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    r.*,
                    c.first_name AS client_first_name,
                    c.last_name AS client_last_name,
                    c.phone AS client_phone,
                    car.model AS car_model,
                    car.year AS car_year,
                    car.license_plate,
                    car.daily_price,
                    car.main_image_url,
                    brand.name AS brand_name,
                    category.name AS category_name,
                    u.first_name AS employee_first_name,
                    u.last_name AS employee_last_name
                FROM reservations r
                INNER JOIN clients c ON r.client_id = c.id
                INNER JOIN cars car ON r.car_id = car.id
                LEFT JOIN car_brand brand ON car.brand_id = brand.id
                LEFT JOIN car_categories category ON car.category_id = category.id
                LEFT JOIN users u ON r.employee_id = u.id
                ORDER BY r.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllReservationsWithDetails: " . $e->getMessage());
            return [];
        }
    }

    public function getReservationById($reservationId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.*,
                    c.first_name AS client_first_name,
                    c.last_name AS client_last_name,
                    c.phone AS client_phone,
                    car.model AS car_model,
                    car.year AS car_year,
                    car.license_plate,
                    car.daily_price,
                    car.main_image_url,
                    brand.name AS brand_name,
                    category.name AS category_name,
                    u.first_name AS employee_first_name,
                    u.last_name AS employee_last_name
                FROM reservations r
                INNER JOIN clients c ON r.client_id = c.id
                INNER JOIN cars car ON r.car_id = car.id
                LEFT JOIN car_brand brand ON car.brand_id = brand.id
                LEFT JOIN car_categories category ON car.category_id = category.id
                LEFT JOIN users u ON r.employee_id = u.id
                WHERE r.id = ?
                LIMIT 1
            ");
            $stmt->execute([$reservationId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getReservationById: " . $e->getMessage());
            return false;
        }
    }

    public function updateReservation($reservationId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE reservations
                SET 
                    car_id = :car_id,
                    employee_id = :employee_id,
                    fait_par = :fait_par,
                    start_date = :start_date,
                    end_date = :end_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    total_days = :total_days,
                    total_amount = :total_amount,
                    special_requests = :special_requests,
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :reservation_id
            ");

            return $stmt->execute([
                ':car_id' => $data['car_id'],
                ':employee_id' => $data['employee_id'] ?? null,
                ':fait_par' => $data['fait_par'] ?? 'Employé',
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':total_days' => $data['total_days'],
                ':total_amount' => $data['total_amount'],
                ':special_requests' => $data['special_requests'] ?? '',
                ':status' => $data['status'],
                ':reservation_id' => $reservationId
            ]);
        } catch (PDOException $e) {
            error_log("Erreur updateReservation: " . $e->getMessage());
            throw new Exception("Erreur lors de la modification de la réservation: " . $e->getMessage());
        }
    }

    public function deleteReservation($reservationId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE id = ?");
            return $stmt->execute([$reservationId]);
        } catch (PDOException $e) {
            error_log("Erreur deleteReservation: " . $e->getMessage());
            throw new Exception("Erreur lors de la suppression de la réservation: " . $e->getMessage());
        }
    }

    public function getReservationStats() {
        try {
            $stmt = $this->pdo->query("
                SELECT status, COUNT(*) as total
                FROM reservations
                GROUP BY status
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stats = [];
            foreach ($results as $row) {
                $stats[$row['status']] = (int)$row['total'];
            }
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur getReservationStats: " . $e->getMessage());
            return [];
        }
    }

    public function autoActivateReservations($currentDateTime = null) {
        try {
            $now = $currentDateTime ?: date('Y-m-d H:i:s');
            $stmt = $this->pdo->prepare("
                UPDATE reservations
                SET status = 'active', updated_at = CURRENT_TIMESTAMP
                WHERE status = 'confirmed'
                  AND TIMESTAMP(start_date, start_time) <= ?
                  AND TIMESTAMP(end_date, end_time) >= ?
            ");
            $stmt->execute([$now, $now]);
        } catch (PDOException $e) {
            error_log("Erreur autoActivateReservations: " . $e->getMessage());
        }
    }
}
?>