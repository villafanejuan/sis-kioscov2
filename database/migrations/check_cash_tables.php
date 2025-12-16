<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$db = Database::getInstance();

echo "===========================================\n";
echo "  VERIFICANDO TABLAS DE CAJA\n";
echo "===========================================\n\n";

// Obtener todas las tablas
$tables = $db->fetchAll("SHOW TABLES");
$tableNames = array_map(function ($t) {
    return array_values($t)[0];
}, $tables);

echo "Tablas existentes relacionadas con caja:\n";
$cashTables = ['turnos_caja', 'movimientos_caja', 'cash_registers', 'cash_shifts', 'cash_movements'];
$found = [];

foreach ($cashTables as $table) {
    if (in_array($table, $tableNames)) {
        echo "  ✓ $table\n";
        $found[] = $table;
    }
}

if (empty($found)) {
    echo "  ✗ No se encontraron tablas de caja\n";
}

echo "\n===========================================\n";
echo "  Total tablas de caja: " . count($found) . "\n";
echo "===========================================\n";
