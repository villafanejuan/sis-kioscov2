<?php
/**
 * Script para agregar columnas faltantes a la tabla clientes
 */

require_once __DIR__ . '/../../app/bootstrap.php';

try {
    echo "=== Actualizando tabla clientes ===\n\n";

    // Verificar y agregar columnas faltantes
    $columnas_necesarias = [
        'direccion' => "ALTER TABLE clientes ADD COLUMN direccion TEXT AFTER telefono",
        'saldo_cuenta' => "ALTER TABLE clientes ADD COLUMN saldo_cuenta DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Positivo=a favor, Negativo=deuda' AFTER telefono",
        'limite_credito' => "ALTER TABLE clientes ADD COLUMN limite_credito DECIMAL(10,2) DEFAULT 0.00 AFTER saldo_cuenta",
        'notas' => "ALTER TABLE clientes ADD COLUMN notas TEXT AFTER limite_credito",
        'activo' => "ALTER TABLE clientes ADD COLUMN activo BOOLEAN DEFAULT TRUE AFTER notas",
        'created_at' => "ALTER TABLE clientes ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER activo",
        'updated_at' => "ALTER TABLE clientes ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
    ];

    foreach ($columnas_necesarias as $columna => $sql) {
        // Verificar si la columna existe
        $result = $pdo->query("SHOW COLUMNS FROM clientes LIKE '$columna'");

        if ($result->rowCount() == 0) {
            echo "Agregando columna '$columna'...\n";
            $pdo->exec($sql);
            echo "   ✓ Columna '$columna' agregada\n";
        } else {
            echo "   ⚠ Columna '$columna' ya existe\n";
        }
    }

    // Eliminar columna 'puntos' si existe (no la necesitamos)
    $result = $pdo->query("SHOW COLUMNS FROM clientes LIKE 'puntos'");
    if ($result->rowCount() > 0) {
        echo "\nEliminando columna 'puntos' (no necesaria)...\n";
        $pdo->exec("ALTER TABLE clientes DROP COLUMN puntos");
        echo "   ✓ Columna 'puntos' eliminada\n";
    }

    // Agregar índices
    echo "\nAgregando índices...\n";
    try {
        $pdo->exec("ALTER TABLE clientes ADD INDEX idx_telefono (telefono)");
        echo "   ✓ Índice en 'telefono' agregado\n";
    } catch (PDOException $e) {
        echo "   ⚠ Índice en 'telefono' ya existe\n";
    }

    try {
        $pdo->exec("ALTER TABLE clientes ADD INDEX idx_nombre (nombre)");
        echo "   ✓ Índice en 'nombre' agregado\n";
    } catch (PDOException $e) {
        echo "   ⚠ Índice en 'nombre' ya existe\n";
    }

    echo "\n=== ✓ ACTUALIZACIÓN COMPLETADA ===\n";
    echo "\nLa tabla 'clientes' ahora tiene todas las columnas necesarias.\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
