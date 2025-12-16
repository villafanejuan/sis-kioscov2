<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->query("DESCRIBE turnos_caja");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Columns in turnos_caja: " . implode(', ', $columns) . "\n";
?>