<?php
// Standalone debug script
$host = 'localhost';
$db = 'kiosco_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. Check if 123123 exists
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE codigo_barra = ?");
    $stmt->execute(['123123']);
    $p = $stmt->fetch();

    if ($p) {
        echo "DATABASE CHECK: Found product '{$p['nombre']}' with barcode '123123'.\n";
    } else {
        echo "DATABASE CHECK: Barcode '123123' NOT found in database.\n";
        // Check Coca Cola specifically
        $stmt = $pdo->query("SELECT * FROM productos WHERE nombre LIKE '%Cola%'");
        $coke = $stmt->fetch();
        if ($coke) {
            echo "DEBUG: '{$coke['nombre']}' has barcode: [" . ($coke['codigo_barra'] ?? 'NULL') . "]\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
