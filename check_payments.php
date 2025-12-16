<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/bootstrap.php';
// require_once __DIR__ . '/includes/db.php';

echo "<h2>Diagnostic Report</h2>";

// 1. Check Payments
echo "<h3>1. Table: cliente_pagos</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM cliente_pagos ORDER BY id DESC LIMIT 5");
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($pagos)) {
        echo "No payments found in cliente_pagos.<br>";
    } else {
        echo "<table border='1'><tr>";
        foreach (array_keys($pagos[0]) as $k)
            echo "<th>$k</th>";
        echo "</tr>";
        foreach ($pagos as $p) {
            echo "<tr>";
            foreach ($p as $v)
                echo "<td>$v</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "Error querying cliente_pagos: " . $e->getMessage() . "<br>";
}

// 2. Check Movements
echo "<h3>2. Table: movimientos_caja (Last 5)</h3>";
$stmt = $pdo->query("SELECT * FROM movimientos_caja ORDER BY id DESC LIMIT 5");
$movs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1'><tr>";
foreach (array_keys($movs[0] ?? []) as $k)
    echo "<th>$k</th>";
echo "</tr>";
foreach ($movs as $m) {
    echo "<tr>";
    foreach ($m as $v)
        echo "<td>$v</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Active Shift
echo "<h3>3. Active Shifts</h3>";
$stmt = $pdo->query("SELECT id, user_id, estado, fecha_apertura FROM turnos_caja WHERE estado = 'abierto'");
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($turnos, true) . "</pre>";
// 4. Schema Check
echo "<h3>4. Schema: cliente_pagos</h3>";
$stmt = $pdo->query("DESCRIBE cliente_pagos");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo implode(" | ", $row) . "<br>";
}

echo "<h3>5. Schema: movimientos_caja</h3>";
$stmt = $pdo->query("DESCRIBE movimientos_caja");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo implode(" | ", $row) . "<br>";
}
?>