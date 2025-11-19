<?php
// models/Client.php

class Client {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getClientByIdentity($firstName, $lastName, $phone) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM clients 
                WHERE phone = ? AND first_name = ? AND last_name = ?
                LIMIT 1
            ");
            $stmt->execute([$phone, $firstName, $lastName]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getClientByIdentity: " . $e->getMessage());
            return false;
        }
    }
    
    public function createClient($firstName, $lastName, $phone) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO clients (first_name, last_name, phone) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$firstName, $lastName, $phone]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur createClient: " . $e->getMessage());
            throw new Exception("Erreur lors de la création du client: " . $e->getMessage());
        }
    }
}
?>