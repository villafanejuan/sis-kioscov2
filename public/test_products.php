<?php
// Script de prueba para products.php
require_once __DIR__ . '/../app/bootstrap.php';

echo "=== DIAGNÓSTICO DE PRODUCTS.PHP ===\n\n";

echo "1. Sesión iniciada: " . (isset($_SESSION['user_id']) ? "SÍ" : "NO") . "\n";
echo "2. User ID: " . ($_SESSION['user_id'] ?? 'N/A') . "\n";
echo "3. Username: " . ($_SESSION['username'] ?? 'N/A') . "\n";
echo "4. Role: " . ($_SESSION['role'] ?? 'N/A') . "\n";

echo "\n5. Probando checkSession()...\n";
try {
    checkSession();
    echo "   ✓ checkSession() OK\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n6. Probando checkAdmin()...\n";
try {
    $is_admin = checkAdmin();
    echo "   ✓ checkAdmin() retornó: " . ($is_admin ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n7. Probando conexión PDO...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
    $count = $stmt->fetchColumn();
    echo "   ✓ PDO OK - Productos en DB: $count\n";
} catch (Exception $e) {
    echo "   ✗ Error PDO: " . $e->getMessage() . "\n";
}

echo "\n8. Probando consulta de productos...\n";
try {
    $stmt = $pdo->query("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.nombre LIMIT 3");
    $productos = $stmt->fetchAll();
    echo "   ✓ Consulta OK - Primeros productos:\n";
    foreach ($productos as $p) {
        echo "      - " . $p['nombre'] . " (Stock: " . $p['stock'] . ")\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
