<?php
require_once __DIR__ . '/../app/bootstrap.php';

try {
    $stmt = $pdo->query("DESCRIBE productos");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in productos table:\n";
    print_r($columns);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
