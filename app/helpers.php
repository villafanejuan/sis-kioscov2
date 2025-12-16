<?php
/**
 * Funciones helper para compatibilidad con código antiguo
 */

if (!function_exists('checkSession')) {
    function checkSession()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }
    }
}

if (!function_exists('checkAdmin')) {
    function checkAdmin()
    {
        // Solo verifica si es admin, no redirige
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data)
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Verificar si el usuario tiene un rol específico
 */
if (!function_exists('hasRole')) {
    function hasRole($roleName)
    {
        if (!isset($_SESSION['role'])) {
            return false;
        }
        return strtolower($_SESSION['role']) === strtolower($roleName);
    }
}

/**
 * Verificar si el usuario puede acceder a una sección
 */
if (!function_exists('canAccess')) {
    function canAccess($section)
    {
        if (!isset($_SESSION['role'])) {
            return false;
        }

        $role = strtolower($_SESSION['role']);

        // Admin tiene acceso a todo, incluyendo 'users' y 'reports'
        if ($role === 'admin') {
            return true;
        }

        // Kiosquero: ventas, productos, categorías, caja y dashboard (y ahora reportes para ver sus ventas)
        if ($role === 'kiosquero') {
            return in_array($section, ['dashboard', 'products', 'sales', 'categories', 'cash', 'reports']);
        }

        // Cajero: solo caja, dashboard y reportes (SIN ventas ni productos)
        if ($role === 'cajero') {
            return in_array($section, ['dashboard', 'cash', 'reports']);
        }

        // Por defecto, si no está en las listas anteriores, no tiene acceso
        // Esto restringe 'users' y 'reports' a roles que no sean admin
        return false;
    }
}

// Obtener conexión PDO para código antiguo
if (!isset($pdo)) {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
}
