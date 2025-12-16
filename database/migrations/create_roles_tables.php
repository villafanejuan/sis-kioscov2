<?php
/**
 * Script para crear tablas de roles y permisos básicas
 * Ejecutar: php create_roles_tables.php
 */

echo "===========================================\n";
echo "  CREANDO TABLAS DE ROLES Y PERMISOS\n";
echo "===========================================\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kiosco_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "[1/4] Conectado a la base de datos...\n";

    // Crear tabla roles
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) UNIQUE NOT NULL,
        descripcion TEXT,
        nivel INT NOT NULL COMMENT '1=Admin, 2=Kiosquero, 3=Cajero',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "[2/4] ✓ Tabla 'roles' creada\n";

    // Insertar roles por defecto
    $pdo->exec("INSERT IGNORE INTO roles (id, nombre, descripcion, nivel) VALUES
        (1, 'Administrador', 'Acceso completo al sistema', 1),
        (2, 'Kiosquero', 'Puede realizar ventas, abrir/cerrar caja, ver reportes básicos', 2),
        (3, 'Cajero', 'Solo puede realizar ventas', 3)");

    echo "[3/4] ✓ Roles por defecto insertados\n";

    // Actualizar usuarios existentes con role_id
    $pdo->exec("UPDATE usuarios SET role_id = 1 WHERE role = 'admin' AND (role_id IS NULL OR role_id = 0)");
    $pdo->exec("UPDATE usuarios SET role_id = 3 WHERE role = 'empleado' AND (role_id IS NULL OR role_id = 0)");

    echo "[4/4] ✓ Usuarios actualizados con role_id\n\n";

    // Verificar
    $roles = $pdo->query("SELECT * FROM roles")->fetchAll();
    echo "Roles creados:\n";
    foreach ($roles as $role) {
        echo "  - {$role['nombre']} (ID: {$role['id']}, Nivel: {$role['nivel']})\n";
    }

    echo "\n===========================================\n";
    echo "  ✓ CONFIGURACIÓN COMPLETADA\n";
    echo "===========================================\n\n";
    echo "Sistema listo para usar.\n\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
