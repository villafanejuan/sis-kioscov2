<?php
// Standalone migration script
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
    echo "Connected to database.\n";

    echo "Adding codigo_barra column to productos table...\n";

    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM productos LIKE 'codigo_barra'");
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "Column codigo_barra already exists.\n";
    } else {
        // Add column
        $pdo->exec("ALTER TABLE productos ADD COLUMN codigo_barra VARCHAR(50) DEFAULT NULL AFTER stock");
        echo "Column codigo_barra added successfully.\n";

        // Add unique index
        $pdo->exec("ALTER TABLE productos ADD UNIQUE INDEX idx_codigo_barra (codigo_barra)");
        echo "Unique index added to codigo_barra.\n";
    }

    echo "Migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
