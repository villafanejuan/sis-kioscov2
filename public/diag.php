<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

echo "<!DOCTYPE html><html><head><title>Diagnóstico</title><script src='assets/js/tailwindcss.js'></script></head><body class='bg-gray-100 p-8'>";
echo "<h1 class='text-2xl font-bold mb-4'>Diagnóstico de Pagos y Caja</h1>";

// 1. Session & Shift
echo "<div class='bg-white p-4 rounded shadow mb-4'>";
echo "<h2 class='font-bold text-blue-600'>Sesión y Turno</h2>";
echo "<p>Usuario Logueado ID: " . ($_SESSION['user_id'] ?? 'N/A') . "</p>";

$stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
$stmt->execute([$_SESSION['user_id']]);
$turno = $stmt->fetch(PDO::FETCH_ASSOC);

if ($turno) {
    echo "<p class='text-green-600'>Turno Abierto ID: <strong>{$turno['id']}</strong> (Inicio: {$turno['fecha_apertura']})</p>";
} else {
    echo "<p class='text-red-600'>NO HAY TURNO ABIERTO</p>";
}
echo "</div>";

// 2. Payments
echo "<div class='bg-white p-4 rounded shadow mb-4'>";
echo "<h2 class='font-bold text-green-600'>Últimos 5 Pagos (Tabla: cliente_pagos)</h2>";
$stmt = $pdo->query("SELECT * FROM cliente_pagos ORDER BY id DESC LIMIT 5");
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($pagos)) {
    echo "<p class='text-gray-500'>Tabla vacía o sin datos recientes.</p>";
} else {
    echo "<table class='w-full border mt-2 text-sm'>";
    echo "<tr class='bg-gray-50 text-left'><th>ID</th><th>Cliente</th><th>Monto</th><th>Turno</th><th>Usuario</th><th>Fecha</th></tr>";
    foreach ($pagos as $p) {
        $hl = ($turno && $p['turno_id'] == $turno['id']) ? 'bg-green-100' : '';
        echo "<tr class='border-t $hl'>";
        echo "<td class='p-2'>{$p['id']}</td>";
        echo "<td class='p-2'>{$p['cliente_id']}</td>";
        echo "<td class='p-2'>\${$p['monto']}</td>";
        echo "<td class='p-2'>ID: {$p['turno_id']} " . (($turno && $p['turno_id'] == $turno['id']) ? '(ACTUAL)' : '(OTRO)') . "</td>";
        echo "<td class='p-2'>{$p['usuario_id']}</td>";
        echo "<td class='p-2'>{$p['fecha']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "</div>";

// 3. Movements
echo "<div class='bg-white p-4 rounded shadow mb-4'>";
echo "<h2 class='font-bold text-purple-600'>Últimos 10 Movimientos de Caja (Tabla: movimientos_caja)</h2>";
$stmt = $pdo->query("SELECT id, turno_id, tipo, monto, descripcion, fecha, created_at FROM movimientos_caja ORDER BY id DESC LIMIT 10");
$movs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table class='w-full border mt-2 text-sm'>";
echo "<tr class='bg-gray-50 text-left'><th>ID</th><th>Turno</th><th>Tipo</th><th>Monto</th><th>Desc</th><th>Fecha</th></tr>";
foreach ($movs as $m) {
    $hl = ($turno && $m['turno_id'] == $turno['id']) ? 'bg-purple-100' : '';
    echo "<tr class='border-t $hl'>";
    echo "<td class='p-2'>{$m['id']}</td>";
    echo "<td class='p-2'>{$m['turno_id']} " . (($turno && $m['turno_id'] == $turno['id']) ? '(ACTUAL)' : '') . "</td>";
    echo "<td class='p-2 uppercase font-bold " . ($m['tipo'] == 'ingreso' ? 'text-green-600' : '') . "'>{$m['tipo']}</td>";
    echo "<td class='p-2'>\${$m['monto']}</td>";
    echo "<td class='p-2'>{$m['descripcion']}</td>";
    echo "<td class='p-2'>{$m['fecha']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

echo "<div class='mt-4'><a href='customer_account.php?id=2' class='text-blue-600 underline'>&larr; Volver a Cliente</a></div>";
echo "</body></html>";
