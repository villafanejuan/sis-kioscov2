<?php
require_once __DIR__ . '/public/debug.php'; // Reuse existing debug setup if possible, or just raw PDO

// Manual connection since I want to be sure
$host = 'localhost';
$db = 'kiosco_db';
$user = 'root';
$pass = ''; // Default XAMPP password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected.\n";

    $stmt = $pdo->query("SELECT id, nombre, codigo_barra FROM productos WHERE codigo_barra IS NOT NULL");
    $products = $stmt->fetchAll();

    echo "Products with barcodes:\n";
    foreach ($products as $p) {
        echo "ID: " . $p['id'] . " - Name: " . $p['nombre'] . " - Barcode: '" . $p['codigo_barra'] . "'\n";
    }

    // Specific check for 123123
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE codigo_barra = ?");
    $stmt->execute(['123123']);
    $specific = $stmt->fetch();

    if ($specific) {
        echo "\nFound 123123: Yes (ID: " . $specific['id'] . ")\n";
    } else {
        echo "\nFound 123123: No\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
