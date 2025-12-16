<?php
/**
 * Migración: Agregar columna deleted_at a productos para soft delete
 * 
 * Esta migración agrega soporte para eliminación lógica (soft delete)
 * en la tabla productos, permitiendo "eliminar" productos sin violar
 * restricciones de clave foránea con ventas asociadas.
 */

require_once __DIR__ . '/../../app/bootstrap.php';

try {
    echo "Iniciando migración: Agregar columna deleted_at a productos...\n";

    // Verificar si la columna ya existe
    $checkColumn = $pdo->query("SHOW COLUMNS FROM productos LIKE 'deleted_at'");

    if ($checkColumn->rowCount() > 0) {
        echo "⚠️  La columna 'deleted_at' ya existe en la tabla productos.\n";
        echo "✅ Migración completada (sin cambios).\n";
        exit(0);
    }

    // Agregar la columna deleted_at
    $sql = "ALTER TABLE productos 
            ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL 
            AFTER created_at";

    $pdo->exec($sql);

    echo "✅ Columna 'deleted_at' agregada exitosamente.\n";
    echo "📝 Los productos ahora soportan eliminación lógica (soft delete).\n";
    echo "   - NULL = producto activo\n";
    echo "   - Fecha/hora = producto eliminado\n";
    echo "\n✅ Migración completada exitosamente.\n";

} catch (PDOException $e) {
    echo "❌ Error en la migración: " . $e->getMessage() . "\n";
    exit(1);
}
