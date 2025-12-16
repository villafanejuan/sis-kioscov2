<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = Database::getInstance()->getConnection();

echo "--- ventas ---\n";
$stmt = $db->query('DESCRIBE ventas');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo $col['Field'] . "\n";
}

echo "\n--- usuarios ---\n";
$stmt = $db->query('SELECT id, nombre, username, rol FROM usuarios');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
