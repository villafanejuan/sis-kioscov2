<?php
/**
 * Logout - Cerrar sesión
 */

require_once __DIR__ . '/../app/bootstrap.php';

$authController = new AuthController();
$authController->logout();
