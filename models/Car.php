<?php
class Car {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function addCar($data) {
    $sql = "INSERT INTO cars (brand_id, category_id, model, year, color, license_plate, daily_price, status, fuel_type, transmission, description, main_image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['brand_id'],
            $data['category_id'],
            $data['model'],
            $data['year'],
            $data['color'],
            $data['license_plate'],
            $data['daily_price'],
            $data['status'],
            $data['fuel_type'],
            $data['transmission'],
            $data['description'],
            $data['main_image_url']
        ]);
    }
    
    public function getCarByLicensePlate($licensePlate) {
        $sql = "SELECT * FROM cars WHERE license_plate = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$licensePlate]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCarById($id) {
        $sql = "SELECT * FROM cars WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCars() {
    $sql = "SELECT c.*, cat.name as category_name, b.name as brand_name
        FROM cars c
        LEFT JOIN car_categories cat ON c.category_id = cat.id
        LEFT JOIN car_brand b ON c.brand_id = b.id
        ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCar($id, $data) {
        $sql = "UPDATE cars SET brand_id = ?, category_id = ?, model = ?, year = ?, color = ?, license_plate = ?, daily_price = ?, status = ?, fuel_type = ?, transmission = ?, description = ?, main_image_url = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['brand_id'],
            $data['category_id'],
            $data['model'],
            $data['year'],
            $data['color'],
            $data['license_plate'],
            $data['daily_price'],
            $data['status'],
            $data['fuel_type'],
            $data['transmission'],
            $data['description'],
            $data['main_image_url'],
            $id
        ]);
    }

    public function deleteCar($id) {
        $sql = "DELETE FROM cars WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getFilteredCars($filters = []) {
    $sql = "SELECT c.* FROM cars c WHERE 1=1";
    $params = [];
    
    if (!empty($filters['category_id'])) {
        $sql .= " AND c.category_id = ?";
        $params[] = $filters['category_id'];
    }
    
    if (!empty($filters['brand_id'])) {
        $sql .= " AND c.brand_id = ?";
        $params[] = $filters['brand_id'];
    }
    
    $sql .= " ORDER BY c.created_at DESC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getCarImages($carId) {
    $stmt = $this->pdo->prepare("SELECT image_url FROM car_images WHERE car_id = ? ORDER BY display_order");
    $stmt->execute([$carId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}