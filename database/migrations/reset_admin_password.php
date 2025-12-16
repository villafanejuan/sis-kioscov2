<?php
/**
 * Script para resetear contraseña del usuario admin
 * Ejecutar: php reset_admin_password.php
 */

echo "===========================================\n";
echo "  RESETEANDO CONTRASEÑA DE ADMIN\n";
echo "===========================================\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kiosco_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "[1/3] Conectado a la base de datos...\n";

    // Verificar usuarios existentes
    $users = $pdo->query("SELECT id, username, role FROM usuarios")->fetchAll();
    echo "[2/3] Usuarios encontrados:\n";
    foreach ($users as $user) {
        echo "  - {$user['username']} (ID: {$user['id']}, Role: {$user['role']})\n";
    }
    echo "\n";

    // Hashear la contraseña "password"
    $newPassword = password_hash('password', PASSWORD_BCRYPT);

    // Actualizar contraseña del usuario admin
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
    $stmt->execute([$newPassword]);

    echo "[3/3] ✓ Contraseña actualizada para usuario 'admin'\n\n";

    // Verificar el cambio
    $admin = $pdo->query("SELECT id, username, role, role_id FROM usuarios WHERE username = 'admin'")->fetch();

    if ($admin) {
        echo "Usuario admin actualizado:\n";
        echo "  ID: {$admin['id']}\n";
        echo "  Username: {$admin['username']}\n";
        echo "  Role: {$admin['role']}\n";
        echo "  Role ID: {$admin['role_id']}\n";
    }

    echo "\n===========================================\n";
    echo "  ✓ CONTRASEÑA RESETEADA\n";
    echo "===========================================\n\n";
    echo "Credenciales de login:\n";
    echo "  Usuario: admin\n";
    echo "  Contraseña: password\n\n";
    echo "Intenta hacer login nuevamente.\n\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
