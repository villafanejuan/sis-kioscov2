<?php
/**
 * Script de Migración de Base de Datos
 * Ejecuta las migraciones para actualizar la BD
 */

// Configuración
$host = 'localhost';
$dbname = 'kiosco_db';
$user = 'root';
$pass = '';

echo "===========================================\n";
echo "  MIGRACIÓN DE BASE DE DATOS - KIOSCO v2.0\n";
echo "===========================================\n\n";

try {
    // Conectar a MySQL
    echo "[1/4] Conectando a la base de datos...\n";
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conexión establecida\n\n";

    // Crear base de datos si no existe
    echo "[2/4] Verificando base de datos...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");
    echo "✓ Base de datos lista\n\n";

    // Ejecutar migración inicial si es necesario
    echo "[3/4] Ejecutando migración inicial...\n";
    $initialMigration = file_get_contents(__DIR__ . '/../db.sql');
    if ($initialMigration) {
        // Ejecutar cada statement
        $statements = array_filter(array_map('trim', explode(';', $initialMigration)));
        foreach ($statements as $statement) {
            if (!empty($statement) && strpos($statement, '--') !== 0) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignorar errores de tablas que ya existen
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "⚠ Advertencia: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
    echo "✓ Migración inicial completada\n\n";

    // Ejecutar migración profesional
    echo "[4/4] Ejecutando migración profesional...\n";
    $professionalMigration = file_get_contents(__DIR__ . '/002_upgrade_to_professional.sql');
    if ($professionalMigration) {
        $statements = array_filter(array_map('trim', explode(';', $professionalMigration)));
        $executed = 0;
        $skipped = 0;

        foreach ($statements as $statement) {
            if (!empty($statement) && strpos($statement, '--') !== 0) {
                try {
                    $pdo->exec($statement);
                    $executed++;
                } catch (PDOException $e) {
                    // Ignorar errores de elementos que ya existen
                    if (
                        strpos($e->getMessage(), 'Duplicate') !== false ||
                        strpos($e->getMessage(), 'already exists') !== false
                    ) {
                        $skipped++;
                    } else {
                        echo "⚠ Advertencia: " . $e->getMessage() . "\n";
                    }
                }
            }
        }

        echo "✓ Migración profesional completada\n";
        echo "  - Statements ejecutados: $executed\n";
        echo "  - Statements omitidos (ya existían): $skipped\n\n";
    }

    // Verificar tablas creadas
    echo "Verificando tablas creadas...\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Total de tablas: " . count($tables) . "\n";
    echo "  Tablas: " . implode(', ', $tables) . "\n\n";

    echo "===========================================\n";
    echo "  ✓ MIGRACIÓN COMPLETADA EXITOSAMENTE\n";
    echo "===========================================\n\n";
    echo "Credenciales por defecto:\n";
    echo "  Usuario: admin\n";
    echo "  Contraseña: password\n\n";
    echo "Ahora puedes acceder al sistema en:\n";
    echo "  http://localhost/sis-kiosco/public/\n\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
