<?php
/**
 * Logout - Cerrar sesión
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Destruir sesión
session_unset();
session_destroy();

// Redirigir directamente al index del proyecto
header('Location: ./index.php');
exit;
