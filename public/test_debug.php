<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug Paths
echo "Current Dir: " . __DIR__ . "<br>";
$db_path = __DIR__ . '/../includes/db.php';
echo "DB Path: " . $db_path . "<br>";

if (!file_exists($db_path)) {
    die("DB File not found at calculated path!");
}

require_once $db_path;

echo "<h2>Diagnóstico de Clientes</h2>";

try {
    // 1. Ver estructura de tabla
    $stmt = $pdo->query("DESCRIBE clientes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Columnas en tabla 'clientes':</h3><ul>";
    $has_saldo = false;
    foreach ($columns as $col) {
        echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
        if ($col['Field'] == 'saldo_cuenta')
            $has_saldo = true;
    }
    echo "</ul>";

    if (!$has_saldo) {
        echo "<h3 style='color:red'>CRITICO: La columna 'saldo_cuenta' NO EXISTE.</h3>";
        // Intentar agregarla
        echo "Intentando agregar columna saldo_cuenta...<br>";
        $pdo->exec("ALTER TABLE clientes ADD COLUMN saldo_cuenta DECIMAL(10,2) DEFAULT 0.00 AFTER telefono");
        $pdo->exec("ALTER TABLE clientes ADD COLUMN limite_credito DECIMAL(10,2) DEFAULT 0.00 AFTER saldo_cuenta");
        echo "Columnas agregadas (saldo_cuenta, limite_credito).<br>";

        // Fix existing NULLs
        echo "Corrigiendo valores NULL...<br>";
        $pdo->exec("UPDATE clientes SET saldo_cuenta = 0 WHERE saldo_cuenta IS NULL");
        $pdo->exec("UPDATE clientes SET limite_credito = 0 WHERE limite_credito IS NULL");
        echo "Valores corregidos.<br>";

    } else {
        echo "<h3 style='color:green'>La columna 'saldo_cuenta' existe.</h3>";
    }

} catch (Exception $e) {
    echo "Excepción: " . $e->getMessage();
}
?>