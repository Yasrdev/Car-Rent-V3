<?php
class User {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Méthode pour récupérer le total des utilisateurs
    public function getTotalUsers() {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des utilisateurs: " . $e->getMessage());
            return 0;
        }
    }

    // Méthode pour récupérer tous les utilisateurs
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
            return [];
        }
    }
    
    // Récupérer un utilisateur par ID
    public function getUserById($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserById: " . $e->getMessage());
            return false;
        }
    }

    // Mettre à jour un utilisateur
    public function updateUser($userId, $data) {
        try {
            $fields = [];
            $params = [];
            
            foreach ($data as $key => $value) {
                // Si c'est le mot de passe, le hasher
                if ($key === 'password') {
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $fields[] = "$key = ?";
                $params[] = $value;
            }
            
            $params[] = $userId;
            
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Erreur updateUser: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer un utilisateur par email
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserByEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function createUser($userData) {
        try {
            // Hasher le mot de passe
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (first_name, last_name, email, phone, role, password, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $userData['phone'],
                $userData['role'],
                $hashedPassword
            ]);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Erreur création utilisateur: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($userId) {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$userId]);
            
        } catch (PDOException $e) {
            error_log("Erreur suppression utilisateur: " . $e->getMessage());
            return false;
        }
    }
}
?>