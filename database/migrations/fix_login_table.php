<?php
/**
 * Script para crear tabla login_attempts faltante
 * Ejecutar: php fix_login_table.php
 */

echo "===========================================\n";
echo "  CREANDO TABLA login_attempts\n";
echo "===========================================\n\n";

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=localhost;dbname=kiosco_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "[1/2] Conectado a la base de datos...\n";

    // Crear tabla login_attempts
    $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        success BOOLEAN DEFAULT FALSE,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_username_time (username, attempted_at),
        INDEX idx_ip_time (ip_address, attempted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);

    echo "[2/2] Tabla login_attempts creada exitosamente\n\n";

    // Verificar que existe
    $result = $pdo->query("SHOW TABLES LIKE 'login_attempts'")->fetch();

    if ($result) {
        echo "✓ Verificación exitosa: Tabla login_attempts existe\n\n";
        echo "===========================================\n";
        echo "  ✓ TABLA CREADA CORRECTAMENTE\n";
        echo "===========================================\n\n";
        echo "Ahora puedes intentar hacer login nuevamente en:\n";
        echo "  http://localhost/sis-kiosco/public/\n\n";
    } else {
        echo "✗ Error: No se pudo verificar la tabla\n";
    }

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
