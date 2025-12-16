<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "Actualizando nombre del rol...\n";

// Cambiar "Administrador" a "Admin"
$pdo->exec("UPDATE roles SET nombre = 'Admin' WHERE id = 1");

echo "✅ Rol actualizado de 'Administrador' a 'Admin'\n";
echo "\nAhora cierra sesión y vuelve a iniciar para que se cargue correctamente.\n";
