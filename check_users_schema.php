<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->query('DESCRIBE usuarios');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo $col['Field'] . "\n";
}
