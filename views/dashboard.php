<?php

// Inclure le AuthController
require_once '../controllers/AuthController.php';
require_once '../config/db-config.php';
require_once '../models/User.php';

// Charger le modèle Car pour afficher les voitures
require_once '../models/Car.php';

$userModel = new User($pdo);
$auth = new AuthController($pdo);

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers auth.php
$auth->redirectIfNotLoggedIn();
$totalUsers = $userModel->getTotalUsers();
$allUsers = $userModel->getAllUsers();
// Récupérer les voitures pour l'affichage
$carModel = new Car($pdo);
$allCars = $carModel->getAllCars();
$totalCars = count($allCars);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/dashboard.css">
    <title>Dashboard - BARIZ CARS</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="../public/images/bariz-logo.png" alt="BARIZ CARS" class="sidebar-logo">
                <h2>BARIZ CARS</h2>
                <button class="sidebar-close" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Profil utilisateur -->
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></h3>
                    <p><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Utilisateur'); ?></p>
                </div>
            </div>

            <!-- Menu de navigation -->
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="#dashboard" class="nav-item active" data-content="dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#employees" class="nav-item" data-content="employees">
                            <i class="fas fa-users"></i>
                            <span>Employés</span>
                        </a>
                    </li>
                    <li>
                        <a href="#cars" class="nav-item" data-content="cars">
                            <i class="fas fa-car"></i>
                            <span>Voitures</span>
                        </a>
                    </li>
                    <li>
                        <a href="#reservations" class="nav-item" data-content="reservations">
                            <i class="fas fa-calendar-check"></i>
                            <span>Réservations</span>
                        </a>
                    </li>
                    <li>
                        <a href="#settings" class="nav-item" data-content="settings">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Déconnexion -->
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-title">
                    <h1>Dashboard</h1>
                </div>
                <div class="header-actions">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="user-menu">
                        <img src="../public/images/bariz-logo.png" alt="User" class="user-avatar-small">
                    </div>
                </div>
            </header>

            <!-- Contenu dynamique -->
            <div class="content-area">
                <!-- Dashboard Content -->
                <div id="dashboard-content" class="content-section active">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalCars; ?></h3>
                                <p>Voitures totales</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalUsers?></h3>
                                <p>Employés</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-info">
                                <h3>28</h3>
                                <p>Réservations actives</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-info">
                                <h3>85%</h3>
                                <p>Taux d'occupation</p>
                            </div>
                        </div>
                    </div>

                    <div class="recent-activity">
                        <h2>Activité récente</h2>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-car"></i>
                                </div>
                                <div class="activity-content">
                                    <p>Nouvelle voiture ajoutée - Porsche 911</p>
                                    <span class="activity-time">Il y a 2 heures</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <p>Nouvelle réservation - Mercedes Classe S</p>
                                    <span class="activity-time">Il y a 5 heures</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <p>Nouvel employé ajouté</p>
                                    <span class="activity-time">Il y a 1 jour</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees Content -->
                <div id="employees-content" class="content-section">
                    <div class="section-header">
                        <h2>Gestion des Employés</h2>
                        <button class="btn-primary" id="openEmployeeModal">
                            <i class="fas fa-plus"></i>
                            Ajouter un employé
                        </button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($allUsers)): ?>
                            <?php foreach ($allUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo strtolower($user['first_name'] .' '. $user['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>+212 <?php echo htmlspecialchars($user['phone']); ?></td>
                                    <?php if ($user['role'] === 'manager'): ?>
                                    <td><span class="role-badge manager"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                    <?php else: ?>
                                        <td><span class="role-badge admin"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                    <?php endif ?> 
                                    <td><?php $date = new DateTime($user['created_at']);echo $date->format('d/m/Y');?></td>
                                    <td>
                                    <button class="btn-action edit-employee-btn" 
                                            data-id="<?php echo htmlspecialchars($user['id']); ?>"
                                            data-firstname="<?php echo htmlspecialchars($user['first_name']); ?>"
                                            data-lastname="<?php echo htmlspecialchars($user['last_name']); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                            data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-employee-btn" data-id="<?php echo htmlspecialchars($user['id']); ?>" 
                                            data-name="<?php echo htmlspecialchars(strtolower($user['first_name'] . ' ' . $user['last_name'])); ?>"
                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                            data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <i class="fas fa-users"></i>
                                        <p>Aucun employé trouvé</p>
                                    </td>
                                </tr>
                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal d'ajout d'employé -->
                <div id="employeeModal" class="employee-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><i class="fas fa-user-plus"></i> Ajouter un employé</h2>
                            <button class="modal-close" id="closeEmployeeModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- zone de message serveur (erreurs / succès) affichée sous le titre -->
                        <div id="employeeFormServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
                        <div class="modal-body">
                            <form id="employeeForm" novalidate>
                                <div class="name-fields">
                                    <div class="form-group">
                                        <label for="firstName">Prénom <span style="color: #e74c3c;">*</span></label>
                                        <input type="text" id="firstName" name="first_name" class="form-control" placeholder="Jean" required>
                                        <div class="error-message" id="firstNameError"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName">Nom <span style="color: #e74c3c;">*</span></label>
                                        <input type="text" id="lastName" name="last_name" class="form-control" placeholder="Dupont" required>
                                        <div class="error-message" id="lastNameError"></div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email <span style="color: #e74c3c;">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="jean@exemple.com" required>
                                    <div class="error-message" id="emailError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Téléphone <span style="color: #e74c3c;">*</span></label>
                                    <div class="phone-field">
                                        <span class="phone-prefix">+212</span>
                                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="612345678" pattern="[0-9]{9}" required>
                                    </div>
                                    <div class="error-message" id="phoneError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="role">Rôle <span style="color: #e74c3c;">*</span></label>
                                    <select id="role" name="role" class="form-control" required>
                                        <option value="">-- Sélectionnez un rôle --</option>
                                        <option value="admin">Administrateur</option>
                                        <option value="manager">Manager</option>
                                    </select>
                                    <div class="error-message" id="roleError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Mot de passe <span style="color: #e74c3c;">*</span></label>
                                    <div class="password-container">
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 6 caractères" required>
                                        <button type="button" class="toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="error-message" id="passwordError"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirmPassword">Confirmer <span style="color: #e74c3c;">*</span></label>
                                    <div class="password-container">
                                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Confirmez le mot de passe" required>
                                        <button type="button" class="toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="error-message" id="confirmPasswordError"></div>
                                </div>
                                
                                <div class="modal-actions">
                                    <button type="button" class="btn-secondary" id="cancelEmployeeModal">Annuler</button>
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Ajouter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal de modification d'employé -->
<div id="editEmployeeModal" class="employee-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier l'employé</h2>
            <button class="modal-close" id="closeEditModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- zone de message serveur -->
        <div id="editFormServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
        <div class="modal-body">
            <form id="editEmployeeForm" novalidate>
                <input type="hidden" id="editEmployeeId" name="employee_id">
                
                <div class="name-fields">
                    <div class="form-group">
                        <label for="editFirstName">Prénom <span style="color: #e74c3c;">*</span></label>
                        <input type="text" id="editFirstName" name="first_name" class="form-control" placeholder="Jean" required>
                        <div class="error-message" id="editFirstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="editLastName">Nom <span style="color: #e74c3c;">*</span></label>
                        <input type="text" id="editLastName" name="last_name" class="form-control" placeholder="Dupont" required>
                        <div class="error-message" id="editLastNameError"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editEmail">Email <span style="color: #e74c3c;">*</span></label>
                    <input type="email" id="editEmail" name="email" class="form-control" placeholder="jean@exemple.com" required>
                    <div class="error-message" id="editEmailError"></div>
                </div>
                
                <div class="form-group">
                    <label for="editPhone">Téléphone <span style="color: #e74c3c;">*</span></label>
                    <div class="phone-field">
                        <span class="phone-prefix">+212</span>
                        <input type="tel" id="editPhone" name="phone" class="form-control" placeholder="612345678" pattern="[0-9]{9}" required>
                    </div>
                    <div class="error-message" id="editPhoneError"></div>
                </div>
                
                <div class="form-group">
                    <label for="editRole">Rôle <span style="color: #e74c3c;">*</span></label>
                    <select id="editRole" name="role" class="form-control" required>
                        <option value="">-- Sélectionnez un rôle --</option>
                        <option value="admin">Administrateur</option>
                        <option value="manager">Manager</option>
                    </select>
                    <div class="error-message" id="editRoleError"></div>
                </div>
                
                <div class="form-group">
                    <label for="editPassword">Nouveau mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="editPassword" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer">
                        <button type="button" class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="editPasswordError"></div>
                    <small style="color: #888; font-size: 12px;">Laisser vide pour conserver le mot de passe actuel</small>
                </div>
                
                <div class="form-group">
                    <label for="editConfirmPassword">Confirmer le nouveau mot de passe</label>
                    <div class="password-container">
                        <input type="password" id="editConfirmPassword" name="confirm_password" class="form-control" placeholder="Confirmez le nouveau mot de passe">
                        <button type="button" class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="editConfirmPasswordError"></div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelEditModal">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
                <!-- Modal de confirmation de suppression -->
                <div id="deleteEmployeeModal" class="employee-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><i class="fas fa-exclamation-triangle"></i> Confirmer la suppression</h2>
                            <button class="modal-close" id="closeDeleteModal" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- zone de message serveur pour la suppression (erreurs / succès) -->
                        <div id="deleteEmployeeServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
                        <div class="modal-body">
                            <div class="delete-confirmation">
                                <div class="warning-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <h3>Êtes-vous sûr de vouloir supprimer cet employé ?</h3>
                                <p>Cette action est irréversible. Toutes les données associées à cet employé seront perdues.</p>
                                
                                <div class="employee-info" id="employeeToDeleteInfo" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin: 15px 0;">
                                    <p><strong>Nom:</strong> <span id="deleteEmployeeName"></span></p>
                                    <p><strong>Email:</strong> <span id="deleteEmployeeEmail"></span></p>
                                    <p><strong>Rôle:</strong> <span id="deleteEmployeeRole"></span></p>
                                </div>
                            </div>
                            
                            <div class="modal-actions">
                                <button type="button" class="btn-secondary" id="cancelDeleteModal">Annuler</button>
                                <button type="button" class="btn-danger" id="confirmDeleteEmployee">
                                    <i class="fas fa-trash"></i>
                                    Supprimer définitivement
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cars Content -->
                <div id="cars-content" class="content-section"> 
                    <style>
                        .section-buttons-cars{
                            display: flex;
                            gap: 20px;
                            align-items: center;
                            justify-content: center;
                            margin-top: 10px;
                            margin-bottom: 10px;
                        }
                        /* Responsive */
                        @media (max-width: 1024px) {
                            .section-buttons-cars {
                                gap: 10px;
                                padding: 15px;
                            }
                            
                            .section-buttons-cars .btn-primary {
                                padding: 10px 16px;
                                font-size: 14px;
                            }
                        }

                        @media (max-width: 768px) {
                            .section-header-cars {
                                gap: 15px;
                            }
                            
                            .section-buttons-cars {
                                flex-direction: column;
                                align-items: center;
                                padding: 15px;
                                gap: 8px;
                            }
                            
                            .section-buttons-cars .btn-primary {
                                width: 100%;
                                max-width: 280px;
                                justify-content: center;
                            }
                        }

                        @media (max-width: 480px) {
                            .section-header-cars {
                                gap: 12px;
                            }
                            
                            .section-buttons-cars {
                                padding: 12px;
                                gap: 6px;
                            }
                            
                            .section-buttons-cars .btn-primary {
                                width: 100%;
                                max-width: none;
                            }
                        }
                    </style>  
                    <div class="section-header-cars">
                        <h2>Gestion des Voitures</h2>
                        <div class="section-buttons-cars">
                            <button class="btn-primary" id="openCarModal">
                                <i class="fas fa-plus"></i>
                                Ajouter une voiture
                            </button>
                            <button class="btn-primary" id="openBrandModal">
                                <i class="fas fa-plus"></i>
                                Ajouter une marque
                            </button>
                            <button class="btn-primary" id="openCategoryModal">
                                <i class="fas fa-plus"></i>
                                Ajouter une catégorie
                            </button>
                            <button class="btn-primary" id="openViewBrandsModal">
                                <i class="fas fa-eye"></i>
                                Voir les marques
                            </button>
                            <button class="btn-primary" id="openViewCategoriesModal">
                                <i class="fas fa-eye"></i>
                                Voir les catégories
                            </button>
                        </div>
                    </div>
                    <div class="cars-grid">
                    <?php if (!empty($allCars)): ?>
                        <?php foreach ($allCars as $car): ?>
                            <?php
                                $imagePath = !empty($car['main_image_url']) ? '../public/' . $car['main_image_url'] : '../public/images/car-placeholder.png';
                                $brand = htmlspecialchars($car['brand_name'] ?? $car['brand_id'] ?? '');
                                $model = htmlspecialchars($car['model'] ?? '');
                                $title = trim($brand . ' ' . $model);
                                $year = htmlspecialchars($car['year'] ?? '');
                                $category = htmlspecialchars($car['category_name'] ?? '');
                                $price = isset($car['daily_price']) ? number_format((float)$car['daily_price'], 2) : '';
                                $status = htmlspecialchars($car['status'] ?? 'Disponible');
                            ?>
                            <div class="car-card">
                                <div class="car-image">
                                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $title; ?>">
                                    <?php if ($status==='réservé'):?>
                                    <span class="car-status reserved"><?php echo $status; ?></span>
                                     <?php elseif ($status==='disponible'):?>   
                                    <span class="car-status available"><?php echo $status; ?></span>
                                        <?php elseif ($status==='en maintenance'):?>   
                                    <span class="car-status maintenance"><?php echo $status; ?></span>
                                            <?php elseif ($status==='indisponible'):?>   
                                    <span class="car-status indisponible"><?php echo $status; ?></span>
                                    <?php endif?>   
                                </div>
                                <div class="car-info">
                                    <h3><?php echo $title ?: 'Voiture'; ?></h3>
                                    <p><?php echo $year; ?> • <?php echo $category; ?></p>
                                    <div class="car-price">€<?php echo $price; ?>/jour</div>
                                </div>
                                <div class="car-actions">
                                    <button class="btn-action edit-car-btn" data-id="<?php echo htmlspecialchars($car['id']); ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn-action delete-car-btn" data-id="<?php echo htmlspecialchars($car['id']); ?>"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data" style="grid-column: 1 / -1; text-align:center; padding:20px;">
                            <i class="fas fa-car"></i>
                            <p>Aucune voiture trouvée</p>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                

                <!-- Reservations Content -->
                <div id="reservations-content" class="content-section">
                    <div class="section-header">
                        <h2>Gestion des Réservations</h2>
                        <button class="btn-primary">
                            <i class="fas fa-plus"></i>
                            Nouvelle réservation
                        </button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Voiture</th>
                                    <th>Date de début</th>
                                    <th>Date de fin</th>
                                    <th>Prix total</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mohamed Ali</td>
                                    <td>Porsche 911</td>
                                    <td>15/12/2023</td>
                                    <td>20/12/2023</td>
                                    <td>€1,250</td>
                                    <td><span class="status-badge confirmed">Confirmée</span></td>
                                    <td>
                                        <button class="btn-action"><i class="fas fa-eye"></i></button>
                                        <button class="btn-action"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sarah Johnson</td>
                                    <td>Mercedes Classe S</td>
                                    <td>18/12/2023</td>
                                    <td>22/12/2023</td>
                                    <td>€720</td>
                                    <td><span class="status-badge pending">En attente</span></td>
                                    <td>
                                        <button class="btn-action"><i class="fas fa-eye"></i></button>
                                        <button class="btn-action"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Settings Content -->
                <div id="settings-content" class="content-section">
                    <div class="section-header">
                        <h2>Paramètres</h2>
                    </div>
                    <div class="settings-grid">
                        <div class="setting-card">
                            <div class="setting-icon">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <h3>Profil</h3>
                            <p>Gérer vos informations personnelles</p>
                            <button class="btn-secondary">Modifier</button>
                        </div>
                        <div class="setting-card">
                            <div class="setting-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3>Sécurité</h3>
                            <p>Changer votre mot de passe</p>
                            <button class="btn-secondary">Modifier</button>
                        </div>
                        <div class="setting-card">
                            <div class="setting-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h3>Notifications</h3>
                            <p>Configurer les alertes</p>
                            <button class="btn-secondary">Configurer</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Ajouter une voiture -->
    <div id="addCarModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-car"></i> Ajouter une voiture</h2>
                <button class="modal-close" id="closeCarModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div class="server-message" id="carFormServerMessage" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="server-message" id="viewCategoriesServerMessage" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="modal-body">
                <form id="addCarForm" enctype="multipart/form-data" novalidate>
                    <div class="form-group">
                        <label for="carBrand">Marque</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <select id="carBrand" name="brand_id" class="form-control" style="flex:1;">
                                <option value="">-- Sélectionnez une marque --</option>
                            </select>
                            <button type="button" class="btn-secondary" id="quickAddBrandBtn" style="white-space:nowrap;">+ Marque</button>
                        </div>
                        <div class="error-message" id="carBrandError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carModel">Modèle</label>
                        <input type="text" id="carModel" name="model" class="form-control" placeholder="911">
                        <div class="error-message" id="carModelError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carCategory">Catégorie</label>
                        <select id="carCategory" name="category_id" class="form-control">
                            <option value="">-- Sélectionnez une catégorie --</option>
                        </select>
                        <div class="error-message" id="carCategoryError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carYear">Année</label>
                        <input type="number" id="carYear" name="year" class="form-control" placeholder="2023">
                        <div class="error-message" id="carYearError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carColor">Couleur</label>
                        <input type="text" id="carColor" name="color" class="form-control" placeholder="Noir">
                        <div class="error-message" id="carColorError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carLicensePlate">Plaque</label>
                        <input type="text" id="carLicensePlate" name="license_plate" class="form-control" placeholder="61234A56">
                        <div class="error-message" id="carLicensePlateError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carDailyPrice">Prix journalier</label>
                        <input type="number" step="0.01" id="carDailyPrice" name="daily_price" class="form-control" placeholder="250">
                        <div class="error-message" id="carDailyPriceError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carFuelType">Carburant</label>
                        <select id="carFuelType" name="fuel_type" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="gasoline">Essence</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Électrique</option>
                        </select>
                        <div class="error-message" id="carFuelTypeError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carTransmission">Transmission</label>
                        <select id="carTransmission" name="transmission" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="manual">Manuelle</option>
                            <option value="automatic">Automatique</option>
                        </select>
                        <div class="error-message" id="carTransmissionError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carStatus">Statut</label>
                        <select id="carStatus" name="status" class="form-control">
                            <option value="disponible" selected>Disponible</option>
                            <option value="réservé">Réservé</option>
                            <option value="en maintenance">En maintenance</option>
                            <option value="indisponible">Indisponible</option>
                        </select>
                        <div class="error-message" id="carStatusError"></div>
                    </div>

                    <div class="form-group">
                        <label for="carDescription">Description</label>
                        <textarea id="carDescription" name="description" class="form-control" rows="3"></textarea>
                        <div class="error-message" id="carDescriptionError"></div>
                    </div>

                    <div class="form-group">
                        <label for="mainImage">Image principale</label>
                        <input type="file" id="mainImage" name="main_image" accept="image/*" class="form-control">
                        <div class="error-message" id="mainImageError"></div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancelCarModal">Annuler</button>
                        <button type="submit" class="btn-primary">Ajouter la voiture</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter catégorie -->
    <div id="addCategoryModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-tags"></i> Ajouter une catégorie</h2>
                <button class="modal-close" id="closeCategoryModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div class="server-message" id="categoryFormServerMessage" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="modal-body">
                <form id="addCategoryForm" novalidate>
                    <div class="form-group">
                        <label for="categoryName">Nom de la catégorie</label>
                        <input type="text" id="categoryName" name="name" class="form-control" placeholder="SUV">
                        <div class="error-message" id="categoryNameError"></div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancelCategoryModal">Annuler</button>
                        <button type="submit" class="btn-primary">Ajouter la catégorie</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Voir catégories -->
    <div id="viewCategoriesModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-eye"></i> Catégories</h2>
                <button class="modal-close" id="closeViewCategoriesModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <!-- rempli par JS -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="closeViewCategoriesBtn">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Suppression marque -->
    <div id="deleteBrandModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Supprimer la marque</h2>
                <button class="modal-close" id="closeDeleteBrandModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div id="deleteBrandServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3>Êtes-vous sûr de vouloir supprimer la marque&nbsp;: <strong id="deleteBrandName"></strong> ?</h3>
                    <p>Cette action est irréversible et peut affecter des voitures associées.</p>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelDeleteBrand">Annuler</button>
                    <button type="button" class="btn-danger" id="confirmDeleteBrand"><i class="fas fa-trash"></i> Supprimer</button>
                </div>
            </div>
        </div>
    </div>

                <!-- Modal Ajouter marque -->
                <div id="addBrandModal" class="employee-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><i class="fas fa-tag"></i> Ajouter une marque</h2>
                            <button class="modal-close" id="closeBrandModal" type="button"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="server-message" id="brandFormServerMessage" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
                        <div class="modal-body">
                            <form id="addBrandForm" novalidate>
                                <div class="form-group">
                                    <label for="brandName">Nom de la marque</label>
                                    <input type="text" id="brandName" name="name" class="form-control" placeholder="Porsche">
                                    <div class="error-message" id="brandNameError"></div>
                                </div>
                                <div class="modal-actions">
                                    <button type="button" class="btn-secondary" id="cancelBrandModal">Annuler</button>
                                    <button type="submit" class="btn-primary">Ajouter la marque</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Voir marques -->
                <div id="viewBrandsModal" class="employee-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><i class="fas fa-eye"></i> Marques</h2>
                            <button class="modal-close" id="closeViewBrandsModal" type="button"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Créé le</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="brandsTableBody">
                                        <!-- rempli par JS -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn-secondary" id="closeViewBrandsBtn">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

    <!-- Modal Confirmation suppression marque -->
<div id="deleteBrandModal" class="employee-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Supprimer la marque</h2>
            <button class="modal-close" id="closeDeleteBrandModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="deleteBrandServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
        <div class="modal-body">
            <div class="delete-confirmation">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3>Êtes-vous sûr de vouloir supprimer la marque&nbsp;: <strong id="deleteBrandName"></strong> ?</h3>
                <p>Cette action est irréversible et peut affecter des voitures associées.</p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="cancelDeleteBrand">Annuler</button>
                <button type="button" class="btn-danger" id="confirmDeleteBrand">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- Modal Confirmation suppression catégorie -->
    <div id="deleteCategoryModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Supprimer la catégorie</h2>
                <button class="modal-close" id="closeDeleteCategoryModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div id="deleteCategoryServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3>Êtes-vous sûr de vouloir supprimer la catégorie&nbsp;: <strong id="deleteCategoryName"></strong> ?</h3>
                    <p>Cette action est irréversible et peut affecter des voitures associées.</p>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelDeleteCategory">Annuler</button>
                    <button type="button" class="btn-danger" id="confirmDeleteCategory"><i class="fas fa-trash"></i> Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modification de voiture -->
    <div id="editCarModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Modifier la voiture</h2>
                <button class="modal-close" id="closeEditCarModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div id="editCarServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="modal-body">
                <div style="background-color: #e8f4f8; border-left: 4px solid #17a2b8; padding: 10px 15px; margin-bottom: 15px; border-radius: 4px; font-size: 13px; color: #004085;">
                    <strong>💡 Conseil:</strong> Laissez les champs vides pour conserver les valeurs actuelles. Modifiez uniquement les champs que vous souhaitez changer.
                </div>
                <form id="editCarForm" enctype="multipart/form-data" novalidate>
                    <input type="hidden" id="editCarId" name="car_id">

                    <div class="form-group">
                        <label for="editCarBrand">Marque <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <select id="editCarBrand" name="brand_id" class="form-control">
                            <option value="">-- Sélectionnez une marque --</option>
                        </select>
                        <div class="error-message" id="editCarBrandError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarModel">Modèle <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <input type="text" id="editCarModel" name="model" class="form-control" placeholder="911">
                        <div class="error-message" id="editCarModelError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarCategory">Catégorie <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <select id="editCarCategory" name="category_id" class="form-control">
                            <option value="">-- Sélectionnez une catégorie --</option>
                        </select>
                        <div class="error-message" id="editCarCategoryError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarYear">Année <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <input type="number" id="editCarYear" name="year" class="form-control" placeholder="2023">
                        <div class="error-message" id="editCarYearError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarColor">Couleur <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <input type="text" id="editCarColor" name="color" class="form-control" placeholder="Noir">
                        <div class="error-message" id="editCarColorError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarLicensePlate">Plaque <span style="color: #999; font-size: 12px;">(optionnel, doit être unique)</span></label>
                        <input type="text" id="editCarLicensePlate" name="license_plate" class="form-control" placeholder="61234A56">
                        <div class="error-message" id="editCarLicensePlateError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarDailyPrice">Prix journalier <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <input type="number" step="0.01" id="editCarDailyPrice" name="daily_price" class="form-control" placeholder="250">
                        <div class="error-message" id="editCarDailyPriceError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarStatus">Statut <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <select id="editCarStatus" name="status" class="form-control">
                            <option value="disponible">Disponible</option>
                            <option value="réservé">Réservé</option>
                            <option value="en maintenance">En maintenance</option>
                            <option value="indisponible">Indisponible</option>
                        </select>
                        <div class="error-message" id="editCarStatusError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarFuelType">Carburant <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <select id="editCarFuelType" name="fuel_type" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="gasoline">Essence</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Électrique</option>
                        </select>
                        <div class="error-message" id="editCarFuelTypeError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarTransmission">Transmission <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <select id="editCarTransmission" name="transmission" class="form-control">
                            <option value="">-- Sélectionnez --</option>
                            <option value="manual">Manuelle</option>
                            <option value="automatic">Automatique</option>
                        </select>
                        <div class="error-message" id="editCarTransmissionError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editCarDescription">Description <span style="color: #999; font-size: 12px;">(optionnel)</span></label>
                        <textarea id="editCarDescription" name="description" class="form-control" rows="3"></textarea>
                        <div class="error-message" id="editCarDescriptionError"></div>
                    </div>

                    <div class="form-group">
                        <label for="editMainImage">Image principale</label>
                        <div id="editImagePreview" style="margin-bottom: 10px;">
                            <img id="previewImg" src="" alt="Aperçu" style="max-width: 100%; height: auto; border-radius: 8px; display: none;">
                        </div>
                        <input type="file" id="editMainImage" name="main_image" accept="image/*" class="form-control">
                        <div class="error-message" id="editMainImageError"></div>
                        <small style="color: #888; font-size: 12px; margin-top: 5px; display: block;">Laisser vide pour conserver l'image actuelle. Si vous modifiez l'image, l'ancienne sera supprimée.</small>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancelEditCarModal">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Suppression voiture -->
    <div id="deleteCarModal" class="employee-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Supprimer la voiture</h2>
                <button class="modal-close" id="closeDeleteCarModal" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div id="deleteCarServerMessage" class="server-message" style="display:none;margin:10px 20px;padding:10px;border-radius:4px;"></div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="warning-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3>Êtes-vous sûr de vouloir supprimer la voiture&nbsp;: <strong id="deleteCarName"></strong> ?</h3>
                    <p>Cette action est irréversible.</p>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelDeleteCar">Annuler</button>
                    <button type="button" class="btn-danger" id="confirmDeleteCar"><i class="fas fa-trash"></i> Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/js/dashboard.js"></script>
</body>
</html>