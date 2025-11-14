<?php
// Le fichier de configuration initialise la session et la connexion PDO
require_once '../config/db-config.php';
require_once '../models/Car.php';
require_once '../models/CarCategory.php';
require_once '../models/CarBrand.php';

header('Content-Type: application/json; charset=utf-8');

// Convertir erreurs/exceptions en JSON pour que le front reçoive toujours du JSON
set_exception_handler(function($e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

$carModel = new Car($pdo);
$categoryModel = new CarCategory($pdo);
$brandModel = new CarBrand($pdo);

try {
    // Gestion des différentes actions
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_categories':
                $categories = $categoryModel->getAllCategories();
                echo json_encode(['success' => true, 'categories' => $categories]);
                break;

            case 'get_brands':
                $brands = $brandModel->getAllBrands();
                echo json_encode(['success' => true, 'brands' => $brands]);
                break;
            case 'get_filtered_cars':
                getFilteredCars($carModel);
                break;
            case 'get_car':
                if (!isset($_GET['car_id'])) {
                    echo json_encode(['success' => false, 'message' => 'ID voiture manquant']);
                    break;
                }
                $car = $carModel->getCarById((int)$_GET['car_id']);
                if ($car) {
                    echo json_encode(['success' => true, 'car' => $car]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Voiture non trouvée']);
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_car':
                addCar($carModel, $categoryModel);
                break;
                
            case 'add_category':
                addCategory($categoryModel);
                break;

            case 'add_brand':
                addBrand($brandModel);
                break;
                
            case 'delete_category':
                deleteCategory($categoryModel);
                break;

            case 'delete_brand':
                deleteBrand($brandModel);
                break;

            case 'update_car':
                updateCar($carModel, $categoryModel);
                break;

            case 'delete_car':
                deleteCar($carModel);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    
} catch (Exception $e) {
    error_log('Erreur CarController: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}

function addCar($carModel, $categoryModel) {
    $errors = [];
    
    // Validation des champs requis
    $requiredFields = ['brand_id', 'model', 'category_id', 'year', 'license_plate', 'daily_price', 'fuel_type', 'transmission', 'status'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Ce champ est requis';
        }
    }
    
    // Validation de la catégorie
    if (!empty($_POST['category_id']) && !$categoryModel->getCategoryById($_POST['category_id'])) {
        $errors['category_id'] = 'Catégorie invalide';
    }
    
    // Validation plaque d'immatriculation unique
    if (!empty($_POST['license_plate']) && $carModel->getCarByLicensePlate($_POST['license_plate'])) {
        $errors['license_plate'] = 'Cette plaque d\'immatriculation existe déjà';
    }
    
    // Validation prix
    if (!empty($_POST['daily_price']) && $_POST['daily_price'] <= 0) {
        $errors['daily_price'] = 'Le prix doit être positif';
    }
    
    // Validation année
    if (!empty($_POST['year']) && ($_POST['year'] < 2000 || $_POST['year'] > 2030)) {
        $errors['year'] = 'L\'année doit être entre 2000 et 2030';
    }

    // Validation statut (valeurs autorisées)
    $allowedStatuses = ['disponible', 'réservé', 'en maintenance', 'indisponible'];
    if (!empty($_POST['status']) && !in_array($_POST['status'], $allowedStatuses)) {
        $errors['status'] = 'Statut invalide';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }
    
    // Gestion de l'upload d'image
    $mainImageUrl = null;
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../public/images/cars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        // Vérifier le type de fichier
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($fileExtension), $allowedTypes)) {
            echo json_encode(['success' => false, 'errors' => ['main_image' => 'Type de fichier non autorisé']]);
            return;
        }
        
        // Vérifier la taille (5MB max)
        if ($_FILES['main_image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'errors' => ['main_image' => 'Fichier trop volumineux (max 5MB)']]);
            return;
        }
        
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $filePath)) {
            $mainImageUrl = 'images/cars/' . $fileName;
        }
    }
    
    // Préparer les données
    $carData = [
        'brand_id' => trim($_POST['brand_id']),
        'category_id' => (int)$_POST['category_id'],
        'model' => trim($_POST['model']),
        'year' => (int)$_POST['year'],
        'color' => !empty($_POST['color']) ? trim($_POST['color']) : null,
        'license_plate' => trim($_POST['license_plate']),
        'daily_price' => (float)$_POST['daily_price'],
        'status' => !empty($_POST['status']) ? trim($_POST['status']) : 'disponible',
        'fuel_type' => $_POST['fuel_type'],
        'transmission' => $_POST['transmission'],
        'description' => !empty($_POST['description']) ? trim($_POST['description']) : null,
        'main_image_url' => $mainImageUrl
    ];
    
    $result = $carModel->addCar($carData);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Voiture ajoutée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la voiture']);
    }
}

                function getFilteredCars($carModel) {
                    $filters = [];
                    
                    if (!empty($_POST['category_id'])) {
                        $filters['category_id'] = (int)$_POST['category_id'];
                    }
                    
                    if (!empty($_POST['brand_id'])) {
                        $filters['brand_id'] = (int)$_POST['brand_id'];
                    }
                    
                    $cars = $carModel->getFilteredCars($filters);
                    
                    echo json_encode(['success' => true, 'cars' => $cars]);
                };

function addCategory($categoryModel) {
    if (empty($_POST['name'])) {
        echo json_encode(['success' => false, 'message' => 'Le nom de la catégorie est requis']);
        return;
    }
    
    $name = trim($_POST['name']);
    
    // Vérifier si la catégorie existe déjà
    if ($categoryModel->getCategoryByName($name)) {
        echo json_encode(['success' => false, 'message' => 'Cette catégorie existe déjà']);
        return;
    }
    
    $result = $categoryModel->addCategory($name);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Catégorie ajoutée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la catégorie']);
    }
}

function deleteCategory($categoryModel) {
    global $pdo;
    if (empty($_POST['category_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID catégorie manquant']);
        return;
    }
    
    $categoryId = (int)$_POST['category_id'];
    
    // Vérifier si la catégorie existe
    $category = $categoryModel->getCategoryById($categoryId);
    if (!$category) {
        echo json_encode(['success' => false, 'message' => 'Catégorie non trouvée']);
        return;
    }
    
    // Vérifier si des voitures utilisent cette catégorie
    // (Vous devrez implémenter cette vérification dans votre modèle Car)
    // Vérifier si des voitures utilisent cette catégorie (prévenir la contrainte FK)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $count = (int)$stmt->fetchColumn();

        if ($count > 0) {
            $msg = "Impossible de supprimer cette catégorie — elle est utilisée par $count voiture(s).\nSupprimez ou réaffectez d'abord ces voitures, puis réessayez.";
            echo json_encode(['success' => false, 'message' => $msg]);
            return;
        }

        // Tenter la suppression
        $result = $categoryModel->deleteCategory($categoryId);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Catégorie supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la catégorie']);
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de contrainte FK de façon conviviale
        $errorInfo = $e->errorInfo ?? null;
        $sqlState = is_array($errorInfo) && isset($errorInfo[0]) ? $errorInfo[0] : $e->getCode();
        $driverCode = is_array($errorInfo) && isset($errorInfo[1]) ? $errorInfo[1] : null;

        if ($sqlState === '23000' || $driverCode == 1451) {
            echo json_encode(['success' => false, 'message' => 'Impossible de supprimer la catégorie : des voitures y sont associées. Supprimez ou modifiez d\'abord ces voitures.']);
        } else {
            // Log détaillé pour debug et message générique pour l'utilisateur
            error_log('PDOException deleteCategory: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la catégorie']);
        }
    }
}

/**
 * Update an existing car with validation
 */
function updateCar($carModel, $categoryModel) {
    $errors = [];
    
    // Get required fields (matching form input names from dashboard.php)
    $carId = $_POST['car_id'] ?? null;
    $brandId = $_POST['brand_id'] ?? null;
    $categoryId = $_POST['category_id'] ?? null;
    $model = $_POST['model'] ?? null;
    $year = $_POST['year'] ?? null;
    $color = $_POST['color'] ?? null;
    $licensePlate = $_POST['license_plate'] ?? null;
    $dailyPrice = $_POST['daily_price'] ?? null;
    $status = $_POST['status'] ?? null;
    $fuelType = $_POST['fuel_type'] ?? null;
    $transmission = $_POST['transmission'] ?? null;
    $description = $_POST['description'] ?? null;
    
    // Validation
    if (empty($carId)) {
        echo json_encode(['success' => false, 'message' => 'ID de voiture manquant']);
        return;
    }
    
    // Get current car to retrieve existing values
    $currentCar = $carModel->getCarById($carId);
    if (!$currentCar) {
        echo json_encode(['success' => false, 'message' => 'Voiture non trouvée']);
        return;
    }
    
    // Use current value if field is empty
    $brandId = !empty($brandId) ? $brandId : $currentCar['brand_id'];
    $categoryId = !empty($categoryId) ? $categoryId : $currentCar['category_id'];
    $model = !empty($model) ? $model : $currentCar['model'];
    $year = !empty($year) ? $year : $currentCar['year'];
    $color = !empty($color) ? $color : $currentCar['color'];
    $licensePlate = !empty($licensePlate) ? $licensePlate : $currentCar['license_plate'];
    $dailyPrice = !empty($dailyPrice) ? $dailyPrice : $currentCar['daily_price'];
    $status = !empty($status) ? $status : $currentCar['status'];
    $fuelType = !empty($fuelType) ? $fuelType : $currentCar['fuel_type'];
    $transmission = !empty($transmission) ? $transmission : $currentCar['transmission'];
    $description = !empty($description) ? $description : $currentCar['description'];
    
    // Validation only for fields that were actually filled (optional fields)
    // If a field is empty, we already used the current value, so no validation error needed
    
    // Only validate fields that have actual values
    if (!empty($year) && (!is_numeric($year) || $year < 2000 || $year > date('Y') + 1)) {
        $errors['Year'] = 'L\'année doit être entre 2000 et ' . (date('Y') + 1);
    }
    
    if (!empty($licensePlate)) {
        // Check if license plate is unique (excluding current car)
        $existingCar = $carModel->getCarByLicensePlate($licensePlate);
        if ($existingCar && $existingCar['id'] != $carId) {
            $errors['LicensePlate'] = 'Cette plaque d\'immatriculation existe déjà';
        }
    }
    
    if (!empty($dailyPrice) && (!is_numeric($dailyPrice) || $dailyPrice <= 0)) {
        $errors['DailyPrice'] = 'Le prix doit être un nombre positif';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => 'Erreurs de validation', 'errors' => $errors]);
        return;
    }
    
    $mainImageUrl = $currentCar['main_image_url'];
    
    // Handle image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['size'] > 0) {
        $file = $_FILES['main_image'];
        
        // Validate image
        $validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $validTypes)) {
            $errors['MainImage'] = 'Format d\'image invalide. Utilisez JPG, PNG, GIF ou WebP';
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            $errors['MainImage'] = 'L\'image ne doit pas dépasser 5MB';
        }
        
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => 'Erreurs de validation', 'errors' => $errors]);
            return;
        }
        
        // Create upload directory if needed
        $uploadDir = __DIR__ . '/../public/images/cars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = 'car_' . $carId . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Delete old image if it exists and is different
            if ($mainImageUrl) {
                $oldImagePath = __DIR__ . '/../public/' . $mainImageUrl;
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }
            
            // Store relative path for database
            $mainImageUrl = 'images/cars/' . $filename;
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement de l\'image']);
            return;
        }
    }
    
    // Prepare data for update
    $data = [
        'brand_id' => $brandId,
        'category_id' => $categoryId,
        'model' => $model,
        'year' => $year,
        'color' => $color,
        'license_plate' => $licensePlate,
        'daily_price' => $dailyPrice,
        'status' => $status,
        'fuel_type' => $fuelType,
        'transmission' => $transmission,
        'description' => $description,
        'main_image_url' => $mainImageUrl
    ];
    
    // Update car in database
    try {
        if ($carModel->updateCar($carId, $data)) {
            echo json_encode(['success' => true, 'message' => 'Voiture mise à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la voiture']);
        }
    } catch (Exception $e) {
        error_log('Exception updateCar: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la voiture']);
    }
}

/**
 * Delete a car and its image
 */
function deleteCar($carModel) {
    $carId = $_POST['car_id'] ?? null;
    
    if (empty($carId)) {
        echo json_encode(['success' => false, 'message' => 'ID de voiture manquant']);
        return;
    }
    
    // Get car to retrieve image path
    $car = $carModel->getCarById($carId);
    if (!$car) {
        echo json_encode(['success' => false, 'message' => 'Voiture non trouvée']);
        return;
    }
    
    try {
        // Delete image file if exists
        if ($car['main_image_url']) {
            $imagePath = __DIR__ . '/../public/' . $car['main_image_url'];
            if (file_exists($imagePath) && is_file($imagePath)) {
                @unlink($imagePath);
            }
        }
        
        // Delete car from database
        if ($carModel->deleteCar($carId)) {
            echo json_encode(['success' => true, 'message' => 'Voiture supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la voiture']);
        }
    } catch (PDOException $e) {
        // Handle foreign key constraints (reservations linked to this car)
        $errorInfo = $e->errorInfo ?? null;
        $sqlState = is_array($errorInfo) && isset($errorInfo[0]) ? $errorInfo[0] : $e->getCode();
        $driverCode = is_array($errorInfo) && isset($errorInfo[1]) ? $errorInfo[1] : null;
        
        if ($sqlState === '23000' || $driverCode == 1451) {
            echo json_encode(['success' => false, 'message' => 'Impossible de supprimer cette voiture : elle a des réservations associées. Annulez d\'abord ces réservations, puis réessayez.']);
        } else {
            error_log('PDOException deleteCar: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la voiture']);
        }
    } catch (Exception $e) {
        error_log('Exception deleteCar: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la voiture']);
    }
}

function addBrand($brandModel) {
    if (empty($_POST['name'])) {
        echo json_encode(['success' => false, 'message' => 'Le nom de la marque est requis']);
        return;
    }

    $name = trim($_POST['name']);

    // Vérifier si la marque existe déjà
    if ($brandModel->getBrandByName($name)) {
        echo json_encode(['success' => false, 'message' => 'Cette marque existe déjà']);
        return;
    }

    $result = $brandModel->addBrand($name);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Marque ajoutée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la marque']);
    }
}

function deleteBrand($brandModel) {
    global $pdo;
    if (empty($_POST['brand_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID marque manquant']);
        return;
    }

    $brandId = (int)$_POST['brand_id'];

    // Vérifier si la marque existe
    $brand = $brandModel->getBrandById($brandId);
    if (!$brand) {
        echo json_encode(['success' => false, 'message' => 'Marque non trouvée']);
        return;
    }

    // Vérifier si des voitures utilisent cette marque
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE brand_id = ?");
        $stmt->execute([$brandId]);
        $count = (int)$stmt->fetchColumn();

        if ($count > 0) {
            $msg = "Impossible de supprimer cette marque — elle est utilisée par $count voiture(s).\nSupprimez ou réaffectez d'abord ces voitures, puis réessayez.";
            echo json_encode(['success' => false, 'message' => $msg]);
            return;
        }

        // Tenter la suppression
        $result = $brandModel->deleteBrand($brandId);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Marque supprimée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la marque']);
        }
    } catch (PDOException $e) {
        $errorInfo = $e->errorInfo ?? null;
        $sqlState = is_array($errorInfo) && isset($errorInfo[0]) ? $errorInfo[0] : $e->getCode();
        $driverCode = is_array($errorInfo) && isset($errorInfo[1]) ? $errorInfo[1] : null;

        if ($sqlState === '23000' || $driverCode == 1451) {
            echo json_encode(['success' => false, 'message' => 'Impossible de supprimer la marque : des voitures y sont associées. Supprimez ou modifiez d\'abord ces voitures.']);
        } else {
            error_log('PDOException deleteBrand: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la marque']);
        }
    }
}