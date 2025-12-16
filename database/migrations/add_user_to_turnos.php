<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "===========================================\n";
echo "  MIGRACIÓN: Control de Caja por Usuario\n";
echo "===========================================\n\n";

try {
    $pdo->beginTransaction();

    // 1. Obtener el ID del admin
    echo "1. Obteniendo usuario administrador...\n";
    $stmt = $pdo->query("SELECT id, nombre, username FROM usuarios WHERE role_id = 1 LIMIT 1");
    $admin = $stmt->fetch();

    if (!$admin) {
        echo "   ✗ No se encontró usuario admin\n";
        exit(1);
    }

    $adminId = $admin['id'];
    $adminName = $admin['nombre'] ?: $admin['username'];
    echo "   ✓ Admin encontrado: $adminName (ID: $adminId)\n";

    // 2. Verificar si las columnas ya existen
    echo "\n2. Verificando estructura de tabla...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM turnos_caja LIKE 'user_id'");
    $columnExists = $stmt->rowCount() > 0;

    if (!$columnExists) {
        // 3. Agregar columnas
        echo "\n3. Agregando columnas...\n";
        $pdo->exec("ALTER TABLE turnos_caja 
            ADD COLUMN user_id INT NOT NULL DEFAULT $adminId AFTER id,
            ADD COLUMN usuario_nombre VARCHAR(100) NOT NULL DEFAULT '$adminName' AFTER user_id");
        echo "   ✓ Columnas agregadas\n";

        // 4. Agregar foreign key
        echo "\n4. Agregando foreign key...\n";
        $pdo->exec("ALTER TABLE turnos_caja 
            ADD CONSTRAINT fk_turno_usuario 
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE RESTRICT");
        echo "   ✓ Foreign key agregada\n";

        // 5. Agregar índices
        echo "\n5. Agregando índices...\n";
        $pdo->exec("CREATE INDEX idx_turno_usuario ON turnos_caja(user_id)");
        $pdo->exec("CREATE INDEX idx_turno_estado ON turnos_caja(estado)");
        echo "   ✓ Índices creados\n";

        $pdo->commit();

        echo "\n===========================================\n";
        echo "  ✓ MIGRACIÓN COMPLETADA EXITOSAMENTE\n";
        echo "===========================================\n";
        echo "\nCambios realizados:\n";
        echo "  • Columna user_id agregada (default: $adminId)\n";
        echo "  • Columna usuario_nombre agregada (default: $adminName)\n";
        echo "  • Foreign key a usuarios configurada\n";
        echo "  • Índices de rendimiento creados\n";
        echo "  • Todos los turnos existentes asignados a: $adminName\n";
    } else {
        $pdo->rollBack();
        echo "   ℹ Las columnas ya existen, no se requiere migración\n";
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
