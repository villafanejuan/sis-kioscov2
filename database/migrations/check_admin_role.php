<?php
require_once __DIR__ . '/../../app/bootstrap.php';

echo "===========================================\n";
echo "  VERIFICANDO SESIÓN Y ROL\n";
echo "===========================================\n\n";

if (!isset($_SESSION['user_id'])) {
    echo "✗ No hay sesión activa\n";
    echo "  Necesitas hacer login primero\n";
} else {
    echo "✓ Sesión activa\n";
    echo "  User ID: " . $_SESSION['user_id'] . "\n";
    echo "  Username: " . ($_SESSION['username'] ?? 'N/A') . "\n";
    echo "  Nombre: " . ($_SESSION['nombre'] ?? 'N/A') . "\n";
    echo "  Role: " . ($_SESSION['role'] ?? 'N/A') . "\n\n";

    if (checkAdmin()) {
        echo "✓ Eres ADMINISTRADOR - Puedes acceder a Caja\n";
    } else {
        echo "✗ NO eres administrador - NO puedes acceder a Caja\n";
        echo "  Necesitas ser admin para gestionar la caja\n\n";

        // Verificar en base de datos
        $db = Database::getInstance();
        $stmt = $db->getConnection()->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if ($user) {
            echo "  Datos en BD:\n";
            echo "    - Role ID: " . ($user['role_id'] ?? 'NULL') . "\n";
            echo "    - Rol Nombre: " . ($user['rol_nombre'] ?? 'N/A') . "\n";
        }
    }
}

echo "\n===========================================\n";
