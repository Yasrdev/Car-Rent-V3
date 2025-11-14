<?php
// logout.php - Endpoint de déconnexion

require_once '../config/db-config.php';
require_once '../controllers/AuthController.php';

$auth = new AuthController($pdo);
$auth->logout();
?>
