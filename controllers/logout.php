<?php

// Inclure le AuthController
require_once './controllers/AuthController.php';
require_once './config/db-config.php';
$auth = new AuthController($pdo);

// Déconnexion
$auth->logout();
?>