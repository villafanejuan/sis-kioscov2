<?php
/**
 * Script para actualizar tabla usuarios con columnas faltantes
 * Ejecutar: php update_usuarios_table.php
 */

echo "===========================================\n";
echo "  ACTUALIZANDO TABLA usuarios\n";
echo "===========================================\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kiosco_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "[1/10] Conectado a la base de datos...\n";

    // Agregar columnas una por una con verificación
    $columns = [
        "role_id INT AFTER role" => "role_id",
        "email VARCHAR(100) AFTER nombre" => "email",
        "telefono VARCHAR(20) AFTER email" => "telefono",
        "is_active BOOLEAN DEFAULT TRUE AFTER telefono" => "is_active",
        "last_login TIMESTAMP NULL AFTER is_active" => "last_login",
        "failed_attempts INT DEFAULT 0 AFTER last_login" => "failed_attempts",
        "locked_until TIMESTAMP NULL AFTER failed_attempts" => "locked_until",
        "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at" => "updated_at"
    ];

    $step = 2;
    foreach ($columns as $columnDef => $columnName) {
        try {
            // Verificar si la columna ya existe
            $check = $pdo->query("SHOW COLUMNS FROM usuarios LIKE '$columnName'")->fetch();

            if (!$check) {
                $pdo->exec("ALTER TABLE usuarios ADD COLUMN $columnDef");
                echo "[$step/10] ✓ Columna '$columnName' agregada\n";
            } else {
                echo "[$step/10] - Columna '$columnName' ya existe\n";
            }
        } catch (PDOException $e) {
            echo "[$step/10] ⚠ Error en '$columnName': " . $e->getMessage() . "\n";
        }
        $step++;
    }

    echo "\n[10/10] Actualizando datos existentes...\n";

    // Actualizar role_id basado en role
    $pdo->exec("UPDATE usuarios SET role_id = 1 WHERE role = 'admin' AND role_id IS NULL");
    $pdo->exec("UPDATE usuarios SET role_id = 3 WHERE role = 'empleado' AND role_id IS NULL");
    $pdo->exec("UPDATE usuarios SET is_active = TRUE WHERE is_active IS NULL");

    echo "✓ Datos actualizados\n\n";

    // Verificar estructura final
    echo "Verificando estructura final de la tabla usuarios:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);
    echo "Columnas: " . implode(", ", $columns) . "\n\n";

    echo "===========================================\n";
    echo "  ✓ TABLA ACTUALIZADA CORRECTAMENTE\n";
    echo "===========================================\n\n";
    echo "Ahora puedes intentar hacer login nuevamente.\n\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
