<?php
// Standalone debug script
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

    // Check total count
    $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
    echo "Total products: " . $stmt->fetchColumn() . "\n";

    $stmt = $pdo->query("SELECT id, nombre, codigo_barra FROM productos WHERE codigo_barra IS NOT NULL AND codigo_barra != ''");
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
        echo "\nFound 123123: YES (ID: " . $specific['id'] . ", Name: " . $specific['nombre'] . ")\n";
    } else {
        echo "\nFound 123123: NO\n";

        // Check if there are ANY products
        $stmt = $pdo->query("SELECT * FROM productos LIMIT 5");
        echo "\nFirst 5 products:\n";
        foreach ($stmt->fetchAll() as $p) {
            echo "ID: " . $p['id'] . " - Name: " . $p['nombre'] . " - Barcode: '" . ($p['codigo_barra'] ?? 'NULL') . "'\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
