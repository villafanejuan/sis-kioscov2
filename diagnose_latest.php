<?php
require_once __DIR__ . '/app/bootstrap.php';

echo "<h2>Diagnostic Report: Latest Data</h2>";

// 1. Session Info
echo "<h3>Session</h3>";
if (session_status() == PHP_SESSION_NONE)
    session_start(); // Ensure session
echo "User ID: " . ($_SESSION['user_id'] ?? 'None') . "<br>";

// 2. Open Shift
echo "<h3>Open Shift</h3>";
$uid = $_SESSION['user_id'] ?? 6; // Default to Admin
$stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
$stmt->execute([$uid]);
$turno = $stmt->fetch(PDO::FETCH_ASSOC);
if ($turno) {
    echo "Turno ID: {$turno['id']} (User: $uid)<br>";
} else {
    echo "NO OPEN SHIFT for User $uid<br>";
}

// 3. Last Payments
echo "<h3>Last 5 Payments (cliente_pagos)</h3>";
$stmt = $pdo->query("SELECT * FROM cliente_pagos ORDER BY id DESC LIMIT 5");
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($pagos, true) . "</pre>";

// 4. Last Movements
echo "<h3>Last 10 Movements (movimientos_caja)</h3>";
$stmt = $pdo->query("SELECT * FROM movimientos_caja ORDER BY id DESC LIMIT 10");
$movs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($movs, true) . "</pre>";

// 5. Log Check
$logFile = __DIR__ . '/storage/logs/pagos_debug.log';
if (file_exists($logFile)) {
    echo "<h3>Log Content (Last 5 lines)</h3>";
    $lines = file($logFile);
    $last = array_slice($lines, -5);
    echo "<pre>" . implode("", $last) . "</pre>";
} else {
    echo "<h3>Log File NOT Found</h3>";
}
?>