<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

echo "===========================================\n";
echo "  CREANDO TABLAS DE CAJA\n";
echo "===========================================\n\n";

try {
    // Tabla de turnos de caja
    echo "1. Creando tabla turnos_caja...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS turnos_caja (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            fecha_apertura DATETIME NOT NULL,
            fecha_cierre DATETIME NULL,
            monto_inicial DECIMAL(10,2) NOT NULL,
            monto_final DECIMAL(10,2) NULL,
            monto_esperado DECIMAL(10,2) NULL,
            diferencia DECIMAL(10,2) NULL,
            estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
            notas_apertura TEXT NULL,
            notas_cierre TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
            INDEX idx_estado (estado),
            INDEX idx_fecha_apertura (fecha_apertura)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla turnos_caja creada\n\n";

    // Tabla de movimientos de caja
    echo "2. Creando tabla movimientos_caja...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS movimientos_caja (
            id INT AUTO_INCREMENT PRIMARY KEY,
            turno_id INT NOT NULL,
            tipo ENUM('ingreso', 'egreso', 'venta', 'inicial') NOT NULL,
            monto DECIMAL(10,2) NOT NULL,
            descripcion VARCHAR(255) NOT NULL,
            venta_id INT NULL,
            fecha DATETIME NOT NULL,
            usuario_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (turno_id) REFERENCES turnos_caja(id) ON DELETE CASCADE,
            FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE SET NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
            INDEX idx_turno (turno_id),
            INDEX idx_tipo (tipo),
            INDEX idx_fecha (fecha)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla movimientos_caja creada\n\n";

    echo "===========================================\n";
    echo "  ✓ TABLAS CREADAS EXITOSAMENTE\n";
    echo "===========================================\n";

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
