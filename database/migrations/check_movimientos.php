<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$pdo = Database::getInstance()->getConnection();

echo "--- Columnas movimientos_caja ---\n";
$stmt = $pdo->query("DESCRIBE movimientos_caja");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo implode(', ', $columns) . "\n";
?>