<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "===========================================\n";
echo "  CREANDO ROLES ESPECÍFICOS\n";
echo "===========================================\n\n";

try {
    // Verificar roles existentes
    $roles = $pdo->query("SELECT * FROM roles")->fetchAll();
    echo "Roles actuales:\n";
    foreach ($roles as $role) {
        echo "  - ID {$role['id']}: {$role['nombre']}\n";
    }
    echo "\n";

    // Actualizar/crear roles
    echo "Configurando roles...\n";

    // Admin (ID 1)
    $pdo->exec("UPDATE roles SET nombre = 'Admin', descripcion = 'Acceso total al sistema' WHERE id = 1");
    echo "✓ Admin configurado\n";

    // Kiosquero (ID 2) - Solo ventas
    $stmt = $pdo->query("SELECT id FROM roles WHERE id = 2");
    if ($stmt->fetch()) {
        $pdo->exec("UPDATE roles SET nombre = 'Kiosquero', descripcion = 'Solo puede realizar ventas' WHERE id = 2");
        echo "✓ Kiosquero actualizado\n";
    } else {
        $pdo->exec("INSERT INTO roles (id, nombre, descripcion) VALUES (2, 'Kiosquero', 'Solo puede realizar ventas')");
        echo "✓ Kiosquero creado\n";
    }

    // Cajero (ID 3) - Solo caja
    $stmt = $pdo->query("SELECT id FROM roles WHERE id = 3");
    if ($stmt->fetch()) {
        $pdo->exec("UPDATE roles SET nombre = 'Cajero', descripcion = 'Solo puede gestionar caja' WHERE id = 3");
        echo "✓ Cajero actualizado\n";
    } else {
        $pdo->exec("INSERT INTO roles (id, nombre, descripcion) VALUES (3, 'Cajero', 'Solo puede gestionar caja')");
        echo "✓ Cajero creado\n";
    }

    echo "\n===========================================\n";
    echo "  ✓ ROLES CONFIGURADOS\n";
    echo "===========================================\n";
    echo "\nRoles finales:\n";
    echo "  1. Admin - Acceso total\n";
    echo "  2. Kiosquero - Solo ventas\n";
    echo "  3. Cajero - Solo caja\n";

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
