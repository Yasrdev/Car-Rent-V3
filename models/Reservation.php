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
    
    public function createReservation($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reservations 
                (client_id, car_id, employee_id, fait_par, start_date, end_date, start_time, end_time, total_days, total_amount, special_requests, status) 
                VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
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
                $data['special_requests']
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur createReservation: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de la réservation: " . $e->getMessage());
        }
    }
}
?>