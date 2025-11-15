<?php
// models/CarImage.php
class CarImage {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter une image pour une voiture
    public function addCarImage($carId, $imageUrl, $imageOrder = 0) {
        $sql = "INSERT INTO car_images (car_id, image_url, image_order) VALUES (?, ?, ?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([$carId, $imageUrl, $imageOrder]);
            
            if ($success) {
                error_log("Car image added successfully for car ID: " . $carId);
                return true;
            }
            
            error_log("Failed to add car image for car ID: " . $carId);
            return false;
        } catch (PDOException $e) {
            error_log("PDO Error in addCarImage: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer toutes les images d'une voiture
    public function getImagesByCarId($carId) {
        $sql = "SELECT * FROM car_images WHERE car_id = ? ORDER BY image_order ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$carId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une image spécifique
    public function getImageById($imageId) {
        $sql = "SELECT * FROM car_images WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$imageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Supprimer une image spécifique
    public function deleteImage($imageId) {
        $sql = "DELETE FROM car_images WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$imageId]);
    }

    // Supprimer toutes les images d'une voiture
    public function deleteImagesByCarId($carId) {
        $sql = "DELETE FROM car_images WHERE car_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$carId]);
    }

    // Compter le nombre d'images pour une voiture
    public function countImagesByCarId($carId) {
        $sql = "SELECT COUNT(*) FROM car_images WHERE car_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$carId]);
        return $stmt->fetchColumn();
    }

    // Mettre à jour l'ordre des images
    public function updateImageOrder($imageId, $imageOrder) {
        $sql = "UPDATE car_images SET image_order = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$imageOrder, $imageId]);
    }

        public function getMainImageByCarId($car_id) {
        $sql = "SELECT * FROM car_images WHERE car_id = ? ORDER BY image_order ASC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$car_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>