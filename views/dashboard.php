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
                

<!-- Section Réservations -->
<div id="reservations-content" class="content-section">
    <div class="section-header">
        <h2>Gestion des Réservations</h2>
        <button class="btn-primary" id="openReservationModal">
            <i class="fas fa-plus"></i>
            Ajouter une réservation
        </button>
    </div>

    <!-- Statistiques des réservations -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalReservations">0</h3>
                <p>Total Réservations</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 id="pendingReservations">0</h3>
                <p>En Attente</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="confirmedReservations">0</h3>
                <p>Confirmées</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-car"></i>
            </div>
            <div class="stat-info">
                <h3 id="activeReservations">0</h3>
                <p>Actives</p>
            </div>
        </div>
    </div>

    <!-- Tableau des réservations -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Voiture</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th>Prix Total</th>
                    <th>Statut</th>
                    <th>Créé par</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reservationsTableBody">
                <?php
                // Charger le modèle Reservation
                require_once '../models/Reservation.php';
                $reservationModel = new Reservation($pdo);
                $allReservations = $reservationModel->getAllReservations();
                $reservationStats = $reservationModel->getReservationStats();

                if (!empty($allReservations)):
                    foreach ($allReservations as $reservation):
                        $startDate = new DateTime($reservation['start_date']);
                        $endDate = new DateTime($reservation['end_date']);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($reservation['client_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($reservation['client_phone']); ?></small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($reservation['car_brand'] . ' ' . $reservation['car_model']); ?><br>
                        <small>Année: <?php echo htmlspecialchars($reservation['car_year']); ?></small>
                    </td>
                    <td><?php echo $startDate->format('d/m/Y H:i'); ?></td>
                    <td><?php echo $endDate->format('d/m/Y H:i'); ?></td>
                    <td>€<?php echo number_format($reservation['total_amount'], 2); ?></td>
                    <td>
                        <?php 
                        $statusClass = '';
                        switch($reservation['status']) {
                            case 'pending': $statusClass = 'pending'; break;
                            case 'confirmed': $statusClass = 'confirmed'; break;
                            case 'active': $statusClass = 'active'; break;
                            case 'completed': $statusClass = 'completed'; break;
                            case 'cancelled': $statusClass = 'cancelled'; break;
                            default: $statusClass = 'pending';
                        }
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($reservation['status']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($reservation['fait_par']); ?></td>
                    <td>
                        <button class="btn-action view-reservation-btn" 
                                data-id="<?php echo htmlspecialchars($reservation['id']); ?>"
                                title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action edit-reservation-btn" 
                                data-id="<?php echo htmlspecialchars($reservation['id']); ?>"
                                title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete-reservation-btn" 
                                data-id="<?php echo htmlspecialchars($reservation['id']); ?>"
                                data-client="<?php echo htmlspecialchars($reservation['client_name']); ?>"
                                data-car="<?php echo htmlspecialchars($reservation['car_brand'] . ' ' . $reservation['car_model']); ?>"
                                title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="9" class="no-data">
                        <i class="fas fa-calendar"></i>
                        <p>Aucune réservation trouvée</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter Réservation -->
<div id="reservationModal" class="employee-modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2><i class="fas fa-calendar-plus"></i> <span id="reservationModalTitle">Ajouter une réservation</span></h2>
            <button class="modal-close" id="closeReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="reservationFormServerMessage" class="server-message" style="display:none;"></div>
        <div class="modal-body">
            <form id="reservationForm" novalidate>
                <input type="hidden" id="reservationId" name="reservation_id">
                <input type="hidden" id="faitPar" name="fait_par" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Employé'); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="clientFirstName">Prénom client <span style="color: #e74c3c;">*</span></label>
                        <input type="text" id="clientFirstName" name="client_first_name" class="form-control" required>
                        <div class="error-message" id="clientFirstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="clientLastName">Nom client <span style="color: #e74c3c;">*</span></label>
                        <input type="text" id="clientLastName" name="client_last_name" class="form-control" required>
                        <div class="error-message" id="clientLastNameError"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="clientPhone">Téléphone <span style="color: #e74c3c;">*</span></label>
                        <div class="phone-field">
                            <span class="phone-prefix">+212</span>
                            <input type="tel" id="clientPhone" name="client_phone" class="form-control" pattern="[0-9]{9}" required>
                        </div>
                        <div class="error-message" id="clientPhoneError"></div>
                    </div>
                    <div class="form-group">
                        <label for="clientEmail">Email</label>
                        <input type="email" id="clientEmail" name="client_email" class="form-control">
                        <div class="error-message" id="clientEmailError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="carSelect">Voiture <span style="color: #e74c3c;">*</span></label>
                    <select id="carSelect" name="car_id" class="form-control" required>
                        <option value="">-- Sélectionnez une voiture --</option>
                    </select>
                    <div class="error-message" id="carSelectError"></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="startDate">Date de début <span style="color: #e74c3c;">*</span></label>
                        <input type="date" id="startDate" name="start_date" class="form-control" required>
                        <div class="error-message" id="startDateError"></div>
                    </div>
                    <div class="form-group">
                        <label for="startTime">Heure de début <span style="color: #e74c3c;">*</span></label>
                        <input type="time" id="startTime" name="start_time" class="form-control" required>
                        <div class="error-message" id="startTimeError"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="endDate">Date de fin <span style="color: #e74c3c;">*</span></label>
                        <input type="date" id="endDate" name="end_date" class="form-control" required>
                        <div class="error-message" id="endDateError"></div>
                    </div>
                    <div class="form-group">
                        <label for="endTime">Heure de fin <span style="color: #e74c3c;">*</span></label>
                        <input type="time" id="endTime" name="end_time" class="form-control" required>
                        <div class="error-message" id="endTimeError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reservationStatus">Statut <span style="color: #e74c3c;">*</span></label>
                    <select id="reservationStatus" name="status" class="form-control" required>
                        <option value="pending">En attente</option>
                        <option value="confirmed" selected>Confirmée</option>
                        <option value="active">Active</option>
                        <option value="completed">Terminée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                    <div class="error-message" id="reservationStatusError"></div>
                </div>

                <div class="form-group">
                    <label for="specialRequests">Demandes spéciales</label>
                    <textarea id="specialRequests" name="special_requests" class="form-control" rows="3" placeholder="Demandes particulières du client..."></textarea>
                    <div class="error-message" id="specialRequestsError"></div>
                </div>

                <div class="reservation-summary" id="reservationSummary" style="display: none;">
                    <h4>Résumé de la réservation</h4>
                    <div class="summary-details">
                        <p><strong>Durée:</strong> <span id="summaryDays">0</span> jour(s)</p>
                        <p><strong>Prix total:</strong> €<span id="summaryTotal">0.00</span></p>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelReservationModal">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        <span id="reservationSubmitText">Enregistrer la réservation</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Réservation -->
<div id="editReservationModal" class="employee-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier la réservation</h2>
            <button class="modal-close" id="closeEditReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="editReservationServerMessage" class="server-message" style="display:none;"></div>
        <div class="modal-body">
            <form id="editReservationForm" novalidate>
                <input type="hidden" id="editReservationId" name="reservation_id">
                
                <!-- Le formulaire de modification sera similaire à celui d'ajout -->
                <!-- Contenu similaire au formulaire d'ajout mais pré-rempli -->
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails Réservation -->
<div id="viewReservationModal" class="employee-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-eye"></i> Détails de la réservation</h2>
            <button class="modal-close" id="closeViewReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="reservation-details">
                <div class="reservation-image">
                    <img id="reservationCarImage" src="" alt="Voiture">
                </div>
                <div class="reservation-info">
                    <h3 id="reservationCarName"></h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Client:</strong>
                            <span id="reservationClientName"></span>
                        </div>
                        <div class="info-item">
                            <strong>Téléphone:</strong>
                            <span id="reservationClientPhone"></span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span id="reservationClientEmail"></span>
                        </div>
                        <div class="info-item">
                            <strong>Date début:</strong>
                            <span id="reservationStartDate"></span>
                        </div>
                        <div class="info-item">
                            <strong>Date fin:</strong>
                            <span id="reservationEndDate"></span>
                        </div>
                        <div class="info-item">
                            <strong>Prix total:</strong>
                            <span id="reservationTotalAmount"></span>
                        </div>
                        <div class="info-item">
                            <strong>Statut:</strong>
                            <span id="reservationStatus" class="status-badge"></span>
                        </div>
                    </div>
                    <div class="special-requests" id="reservationSpecialRequests">
                        <strong>Demandes spéciales:</strong>
                        <p id="reservationRequestsText"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Suppression -->
<div id="deleteReservationModal" class="employee-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirmer la suppression</h2>
            <button class="modal-close" id="closeDeleteReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="deleteReservationServerMessage" class="server-message" style="display:none;"></div>
        <div class="modal-body">
            <div class="delete-confirmation">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3>Êtes-vous sûr de vouloir supprimer cette réservation ?</h3>
                <p>Cette action est irréversible.</p>
                <div class="reservation-info">
                    <p><strong>Réservation #</strong><span id="deleteReservationId"></span></p>
                    <p><strong>Client:</strong> <span id="deleteReservationClient"></span></p>
                    <p><strong>Voiture:</strong> <span id="deleteReservationCar"></span></p>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="cancelDeleteReservation">Annuler</button>
                <button type="button" class="btn-danger" id="confirmDeleteReservation">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Réservation -->
<div id="reservationModal" class="employee-modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2><i class="fas fa-calendar-plus"></i> <span id="reservationModalTitle">Ajouter une réservation</span></h2>
            <button class="modal-close" id="closeReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="reservationFormServerMessage" class="server-message" style="display:none;"></div>
        <div class="modal-body">
            <form id="reservationForm" novalidate>
                <input type="hidden" id="reservationId" name="reservation_id">
                <input type="hidden" id="faitPar" name="fait_par" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Employé'); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="clientFirstName">Prénom client <span style="color: #e74c3c;">*</span></label>
                        <input type="text" id="clientFirstName" name="client_first_name" class="form-control" required>
                        <div class="error-message" id="clientFirstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="clientLastName">Nom client <span style="color: #e74c3c;">*</span></label>
                        <input type="text" id="clientLastName" name="client_last_name" class="form-control" required>
                        <div class="error-message" id="clientLastNameError"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="clientPhone">Téléphone <span style="color: #e74c3c;">*</span></label>
                        <div class="phone-field">
                            <span class="phone-prefix">+212</span>
                            <input type="tel" id="clientPhone" name="client_phone" class="form-control" pattern="[0-9]{9}" required>
                        </div>
                        <div class="error-message" id="clientPhoneError"></div>
                    </div>
                    <div class="form-group">
                        <label for="clientEmail">Email</label>
                        <input type="email" id="clientEmail" name="client_email" class="form-control">
                        <div class="error-message" id="clientEmailError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="carSelect">Voiture <span style="color: #e74c3c;">*</span></label>
                    <select id="carSelect" name="car_id" class="form-control" required>
                        <option value="">-- Sélectionnez une voiture --</option>
                    </select>
                    <div class="error-message" id="carSelectError"></div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="startDate">Date de début <span style="color: #e74c3c;">*</span></label>
                        <input type="date" id="startDate" name="start_date" class="form-control" required>
                        <div class="error-message" id="startDateError"></div>
                    </div>
                    <div class="form-group">
                        <label for="startTime">Heure de début <span style="color: #e74c3c;">*</span></label>
                        <input type="time" id="startTime" name="start_time" class="form-control" required>
                        <div class="error-message" id="startTimeError"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="endDate">Date de fin <span style="color: #e74c3c;">*</span></label>
                        <input type="date" id="endDate" name="end_date" class="form-control" required>
                        <div class="error-message" id="endDateError"></div>
                    </div>
                    <div class="form-group">
                        <label for="endTime">Heure de fin <span style="color: #e74c3c;">*</span></label>
                        <input type="time" id="endTime" name="end_time" class="form-control" required>
                        <div class="error-message" id="endTimeError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reservationStatus">Statut <span style="color: #e74c3c;">*</span></label>
                    <select id="reservationStatus" name="status" class="form-control" required>
                        <option value="pending">En attente</option>
                        <option value="confirmed" selected>Confirmée</option>
                        <option value="active">Active</option>
                        <option value="completed">Terminée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                    <div class="error-message" id="reservationStatusError"></div>
                </div>

                <div class="form-group">
                    <label for="specialRequests">Demandes spéciales</label>
                    <textarea id="specialRequests" name="special_requests" class="form-control" rows="3" placeholder="Demandes particulières du client..."></textarea>
                    <div class="error-message" id="specialRequestsError"></div>
                </div>

                <div class="reservation-summary" id="reservationSummary" style="display: none;">
                    <h4>Résumé de la réservation</h4>
                    <div class="summary-details">
                        <p><strong>Durée:</strong> <span id="summaryDays">0</span> jour(s)</p>
                        <p><strong>Prix total:</strong> €<span id="summaryTotal">0.00</span></p>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelReservationModal">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        <span id="reservationSubmitText">Enregistrer la réservation</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails Réservation -->
<div id="viewReservationModal" class="employee-modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2><i class="fas fa-eye"></i> Détails de la réservation</h2>
            <button class="modal-close" id="closeViewReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="reservationDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="closeViewReservationBtn">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Suppression Réservation -->
<div id="deleteReservationModal" class="employee-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirmer la suppression</h2>
            <button class="modal-close" id="closeDeleteReservationModal" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="deleteReservationServerMessage" class="server-message" style="display:none;"></div>
        <div class="modal-body">
            <div class="delete-confirmation">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3>Êtes-vous sûr de vouloir supprimer cette réservation ?</h3>
                <p>Cette action est irréversible.</p>
                <div class="reservation-info" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin: 15px 0;">
                    <p><strong>Client:</strong> <span id="deleteReservationClient"></span></p>
                    <p><strong>Voiture:</strong> <span id="deleteReservationCar"></span></p>
                    <p><strong>Date:</strong> <span id="deleteReservationDate"></span></p>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="cancelDeleteReservation">Annuler</button>
                <button type="button" class="btn-danger" id="confirmDeleteReservation">
                    <i class="fas fa-trash"></i>
                    Supprimer définitivement
                </button>
            </div>
        </div>
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
        <div class="modal-body">
            <form id="addCarForm" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="carBrand">Marque <span style="color: #e74c3c;">*</span></label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select id="carBrand" name="brand_id" class="form-control" style="flex:1;" required>
                            <option value="">-- Sélectionnez une marque --</option>
                        </select>
                        <button type="button" class="btn-secondary" id="quickAddBrandBtn" style="white-space:nowrap;">+ Marque</button>
                    </div>
                    <div class="error-message" id="carBrandError"></div>
                </div>

                <div class="form-group">
                    <label for="carModel">Modèle <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="carModel" name="model" class="form-control" placeholder="911" required>
                    <div class="error-message" id="carModelError"></div>
                </div>

                <div class="form-group">
                    <label for="carCategory">Catégorie <span style="color: #e74c3c;">*</span></label>
                    <select id="carCategory" name="category_id" class="form-control" required>
                        <option value="">-- Sélectionnez une catégorie --</option>
                    </select>
                    <div class="error-message" id="carCategoryError"></div>
                </div>

                <div class="form-group">
                    <label for="carYear">Année <span style="color: #e74c3c;">*</span></label>
                    <input type="number" id="carYear" name="year" class="form-control" placeholder="2023" min="2000" max="2030" required>
                    <div class="error-message" id="carYearError"></div>
                </div>

                <div class="form-group">
                    <label for="carColor">Couleur <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="carColor" name="color" class="form-control" placeholder="Noir" required>
                    <div class="error-message" id="carColorError"></div>
                </div>

                <div class="form-group">
                    <label for="carLicensePlate">Plaque d'immatriculation <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="carLicensePlate" name="license_plate" class="form-control" placeholder="61234A56" required>
                    <div class="error-message" id="carLicensePlateError"></div>
                </div>

                <div class="form-group">
                    <label for="carDailyPrice">Prix journalier (€) <span style="color: #e74c3c;">*</span></label>
                    <input type="number" step="0.01" id="carDailyPrice" name="daily_price" class="form-control" placeholder="250" min="0" required>
                    <div class="error-message" id="carDailyPriceError"></div>
                </div>

                <div class="form-group">
                    <label for="carFuelType">Type de carburant <span style="color: #e74c3c;">*</span></label>
                    <select id="carFuelType" name="fuel_type" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="essence">Essence</option>
                        <option value="diesel">Diesel</option>
                        <option value="electrique">Électrique</option>
                        <option value="hybride">Hybride</option>
                    </select>
                    <div class="error-message" id="carFuelTypeError"></div>
                </div>

                <div class="form-group">
                    <label for="carTransmission">Transmission <span style="color: #e74c3c;">*</span></label>
                    <select id="carTransmission" name="transmission" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="manual">Manuelle</option>
                        <option value="automatic">Automatique</option>
                    </select>
                    <div class="error-message" id="carTransmissionError"></div>
                </div>

                <div class="form-group">
                    <label for="carStatus">Statut <span style="color: #e74c3c;">*</span></label>
                    <select id="carStatus" name="status" class="form-control" required>
                        <option value="disponible" selected>Disponible</option>
                        <option value="réservé">Réservé</option>
                        <option value="en maintenance">En maintenance</option>
                        <option value="indisponible">Indisponible</option>
                    </select>
                    <div class="error-message" id="carStatusError"></div>
                </div>

                <div class="form-group">
                    <label for="carDescription">Description</label>
                    <textarea id="carDescription" name="description" class="form-control" rows="3" placeholder="Description de la voiture..."></textarea>
                    <div class="error-message" id="carDescriptionError"></div>
                </div>

                <!-- Section Images multiples -->
                <div class="form-group">
                    <label for="carImages">Images de la voiture <span style="color: #e74c3c;">*</span> <small>(max 5 images)</small></label>
                    <div id="imageUploadArea" style="border: 2px dashed rgb(139, 137, 137); border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 15px; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #888; margin-bottom: 10px;"></i>
                        <p style="color: #888; margin-bottom: 15px; font-size: 14px;">Glissez-déposez jusqu'à 5 images ou cliquez pour sélectionner</p>
                        <button type="button" id="selectImagesBtn" class="btn-secondary" style="margin-bottom: 10px;">
                            <i class="fas fa-folder-open"></i> Sélectionner des images
                        </button>
                        <input type="file" id="carImages" name="car_images[]" multiple accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <small style="color: #888; display: block; font-size: 12px;">Formats acceptés: JPG, PNG, WebP • Max 5MB par image</small>
                    </div>
                    
                    <div id="imagePreview" style="display: none; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin-top: 15px;"></div>
                    
                    <div id="imageCounter" style="text-align: center; margin-top: 10px; font-size: 12px; color: #888;">
                        <span id="currentImageCount">0</span>/5 images sélectionnées
                    </div>
                    
                    <div class="error-message" id="carImagesError"></div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelCarModal">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i>
                        Ajouter la voiture
                    </button>
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
            <form id="editCarForm" enctype="multipart/form-data" novalidate>
                <input type="hidden" id="editCarId" name="car_id">

                <div class="form-group">
                    <label for="editCarBrand">Marque <span style="color: #e74c3c;">*</span></label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select id="editCarBrand" name="brand_id" class="form-control" style="flex:1;" required>
                            <option value="">-- Sélectionnez une marque --</option>
                        </select>
                        <button type="button" class="btn-secondary" id="quickAddBrandBtnEdit" style="white-space:nowrap;">+ Marque</button>
                    </div>
                    <div class="error-message" id="editCarBrandError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarModel">Modèle <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="editCarModel" name="model" class="form-control" placeholder="911" required>
                    <div class="error-message" id="editCarModelError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarCategory">Catégorie <span style="color: #e74c3c;">*</span></label>
                    <select id="editCarCategory" name="category_id" class="form-control" required>
                        <option value="">-- Sélectionnez une catégorie --</option>
                    </select>
                    <div class="error-message" id="editCarCategoryError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarYear">Année <span style="color: #e74c3c;">*</span></label>
                    <input type="number" id="editCarYear" name="year" class="form-control" placeholder="2023" min="2000" max="2030" required>
                    <div class="error-message" id="editCarYearError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarColor">Couleur <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="editCarColor" name="color" class="form-control" placeholder="Noir" required>
                    <div class="error-message" id="editCarColorError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarLicensePlate">Plaque d'immatriculation <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="editCarLicensePlate" name="license_plate" class="form-control" placeholder="61234A56" required>
                    <div class="error-message" id="editCarLicensePlateError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarDailyPrice">Prix journalier (€) <span style="color: #e74c3c;">*</span></label>
                    <input type="number" step="0.01" id="editCarDailyPrice" name="daily_price" class="form-control" placeholder="250" min="0" required>
                    <div class="error-message" id="editCarDailyPriceError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarFuelType">Type de carburant <span style="color: #e74c3c;">*</span></label>
                    <select id="editCarFuelType" name="fuel_type" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="essence">Essence</option>
                        <option value="diesel">Diesel</option>
                        <option value="electrique">Électrique</option>
                        <option value="hybride">Hybride</option>
                    </select>
                    <div class="error-message" id="editCarFuelTypeError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarTransmission">Transmission <span style="color: #e74c3c;">*</span></label>
                    <select id="editCarTransmission" name="transmission" class="form-control" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="manual">Manuelle</option>
                        <option value="automatic">Automatique</option>
                    </select>
                    <div class="error-message" id="editCarTransmissionError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarStatus">Statut <span style="color: #e74c3c;">*</span></label>
                    <select id="editCarStatus" name="status" class="form-control" required>
                        <option value="disponible">Disponible</option>
                        <option value="réservé">Réservé</option>
                        <option value="en maintenance">En maintenance</option>
                        <option value="indisponible">Indisponible</option>
                    </select>
                    <div class="error-message" id="editCarStatusError"></div>
                </div>

                <div class="form-group">
                    <label for="editCarDescription">Description</label>
                    <textarea id="editCarDescription" name="description" class="form-control" rows="3" placeholder="Description de la voiture..."></textarea>
                    <div class="error-message" id="editCarDescriptionError"></div>
                </div>

                <!-- Section Images multiples pour l'édition -->
                <div class="form-group">
                    <label for="editCarImages">Images de la voiture <small>(max 5 images au total)</small></label>
                    
                    <!-- Aperçu des images existantes -->
                    <div id="editExistingImages" style="margin-bottom: 15px;">
                        <p style="color: #888; font-size: 14px; margin-bottom: 10px;">Images actuelles :</p>
                        <div id="editCurrentImagesPreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin-bottom: 15px;"></div>
                    </div>
                    
                    <!-- Zone d'upload pour nouvelles images -->
                    <div id="editImageUploadArea" style="border: 2px dashed rgb(139, 137, 137); border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 15px; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #888; margin-bottom: 10px;"></i>
                        <p style="color: #888; margin-bottom: 15px; font-size: 14px;">Glissez-déposez de nouvelles images ou cliquez pour sélectionner</p>
                        <button type="button" id="editSelectImagesBtn" class="btn-secondary" style="margin-bottom: 10px;">
                            <i class="fas fa-folder-open"></i> Ajouter des images
                        </button>
                        <input type="file" id="editCarImages" name="car_images[]" multiple accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <small style="color: #888; display: block; font-size: 12px;">Formats acceptés: JPG, PNG, WebP • Max 5MB par image</small>
                    </div>
                    
                    <!-- Aperçu des nouvelles images -->
                    <div id="editImagePreview" style="display: none; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin-top: 15px;"></div>
                    
                    <div id="editImageCounter" style="text-align: center; margin-top: 10px; font-size: 12px; color: #888;">
                        <span id="editCurrentImageCount">0</span>/5 images au total
                    </div>
                    
                    <div class="error-message" id="editCarImagesError"></div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelEditCarModal">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
                    </button>
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