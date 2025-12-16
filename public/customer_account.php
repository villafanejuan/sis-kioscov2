<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../includes/customer_helper.php';
require_once __DIR__ . '/../includes/payment_helper.php';
checkSession();
$currentPage = 'customers';

// DEBUG REQUEST
// file_put_contents(__DIR__ . '/../storage/logs/pagos_debug.log', date('Y-m-d H:i:s') . " - Request: " . $_SERVER['REQUEST_METHOD'] . " URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

if (!canAccess('products') && !canAccess('sales')) {
    header('Location: dashboard.php');
    exit;
}

$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$cliente_id) {
    header('Location: customers.php');
    exit;
}

$cliente = getCustomerInfo($cliente_id);
if (!$cliente) {
    header('Location: customers.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';

if (isset($_GET['payment_success'])) {
    $pagoId = $_GET['pid'] ?? '';
    $message = "Pago registrado exitosamente (Ref: #$pagoId).";
    $messageType = 'success';
}

// --- OBTENER HISTORIAL UNIFICADO ---
$query = "
    SELECT 
        id, 
        'venta' as tipo, 
        fecha, 
        total as monto, 
        usuario_id, 
        NULL as descripcion,
        monto_pagado as info_extra
    FROM ventas 
    WHERE cliente_id = ?

    UNION ALL

    SELECT 
        id, 
        'pago' as tipo, 
        fecha, 
        monto, 
        usuario_id, 
        descripcion,
        NULL as info_extra
    FROM cliente_pagos 
    WHERE cliente_id = ?

    ORDER BY fecha DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$cliente_id, $cliente_id]);
$movimientos = $stmt->fetchAll();

$userIds = array_unique(array_column($movimientos, 'usuario_id'));
$users = [];
if (!empty($userIds)) {
    $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE id IN ($placeholders)");
    $stmt->execute(array_values($userIds));
    $users = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

$total_compras = 0;
$total_pagado_historico = 0;

foreach ($movimientos as $mov) {
    if ($mov['tipo'] == 'venta') {
        $total_compras += $mov['monto'];
        if ($mov['info_extra'] > 0)
            $total_pagado_historico += $mov['info_extra'];
    } elseif ($mov['tipo'] == 'pago') {
        $total_pagado_historico += $mov['monto'];
    }
}

$saldo_actual = $cliente['saldo_cuenta'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Corriente - <?php echo htmlspecialchars($cliente['nombre']); ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
    <style>
        @media print {

            .no-print,
            nav,
            button,
            .fa,
            .fas,
            .far,
            .bg-purple-500,
            .bg-blue-500,
            .bg-green-500 {
                display: none !important;
            }

            body {
                background: white;
                font-size: 11pt;
            }

            .max-w-6xl {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .shadow-lg,
            .shadow {
                box-shadow: none !important;
            }

            /* Hide URL printing */
            a[href]:after {
                content: none !important;
            }

            /* Table Styling */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 4px !important;
                color: #000 !important;
            }

            /* Adjust Colors to Black/White */
            .text-green-600,
            .text-red-600,
            .text-blue-600,
            .text-purple-600 {
                color: #000 !important;
            }

            /* Always break page before table if necessary, but avoid splitting rows */
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="no-print">
        <?php include __DIR__ . '/../includes/nav.php'; ?>
    </div>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <?php if ($message): ?>
            <div
                class="mb-6 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border-l-4">
                <i class="fas <?php echo $messageType == 'success' ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-2"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-start flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>
                        Cuenta Corriente
                    </h1>
                    <h2 class="text-2xl text-gray-600"><?php echo htmlspecialchars($cliente['nombre']); ?></h2>
                    <?php if ($cliente['telefono']): ?>
                        <p class="text-gray-500"><i
                                class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($cliente['telefono']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-right flex flex-col items-end">
                    <div class="text-sm text-gray-500 mb-2">Saldo Actual</div>
                    <div
                        class="text-4xl font-bold <?php echo $saldo_actual < 0 ? 'text-red-600' : 'text-green-600'; ?>">
                        $<?php echo number_format(abs($saldo_actual), 2); ?>
                    </div>
                    <div class="text-sm font-bold <?php echo $saldo_actual < 0 ? 'text-red-600' : 'text-green-600'; ?>">
                        <?php echo $saldo_actual < 0 ? 'DEBE' : 'A FAVOR'; ?>
                    </div>
                    <div class="mt-4 no-print space-x-2">
                        <a href="payment_entry.php?cliente_id=<?php echo $cliente_id; ?>"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
                            <i class="fas fa-money-bill-wave mr-2"></i>Registrar Pago
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="text-sm text-gray-500 mb-1">Total Compras Históricas</div>
                <div class="text-2xl font-bold text-blue-600">$<?php echo number_format($total_compras, 2); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-500 mb-1">Total Pagado Histórico</div>
                <div class="text-2xl font-bold text-green-600">$<?php echo number_format($total_pagado_historico, 2); ?>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="text-sm text-gray-500 mb-1">Movimientos Registrados</div>
                <div class="text-2xl font-bold text-purple-600"><?php echo count($movimientos); ?></div>
            </div>
        </div>

        <div class="flex gap-4 mb-6 no-print">
            <button onclick="window.print()"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg shadow">
                <i class="fas fa-print mr-2"></i>Imprimir Estado
            </button>
            <a href="customers.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg shadow">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-history text-purple-600 mr-2"></i>Movimientos de la Cuenta
            </h3>
            <?php if (empty($movimientos)): ?>
                <p class="text-gray-500 text-center py-8">No hay movimientos registrados</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($movimientos as $mov): ?>
                        <div
                            class="border rounded-lg p-4 <?php echo $mov['tipo'] == 'venta' ? 'bg-white border-gray-200' : 'bg-green-50 border-green-200'; ?>">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-bold text-lg uppercase <?php echo $mov['tipo'] == 'venta' ? 'text-gray-800' : 'text-green-700'; ?>">
                                            <?php echo $mov['tipo'] == 'venta' ? 'VENTA #' . $mov['id'] : 'PAGO (ABONO)'; ?>
                                        </span>
                                        <span
                                            class="text-xs px-2 py-1 rounded-full <?php echo $mov['tipo'] == 'venta' ? 'bg-gray-100 text-gray-600' : 'bg-green-200 text-green-800'; ?>">
                                            <?php echo date('d/m/Y H:i', strtotime($mov['fecha'])); ?>
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-user mr-1"></i>
                                        Cajero: <?php echo htmlspecialchars($users[$mov['usuario_id']] ?? 'Sistema'); ?>
                                        <?php if ($mov['descripcion']): ?>
                                            <span class="ml-2 text-gray-500">-
                                                <?php echo htmlspecialchars($mov['descripcion']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($mov['tipo'] == 'venta'):
                                        $stmtD = $pdo->prepare("SELECT p.nombre, vd.cantidad, vd.precio FROM venta_detalles vd JOIN productos p ON vd.producto_id = p.id WHERE vd.venta_id = ?");
                                        $stmtD->execute([$mov['id']]);
                                        $detalles = $stmtD->fetchAll();
                                        ?>
                                        <div class="mt-2 text-sm text-gray-500 bg-gray-50 p-2 rounded">
                                            <ul class="list-disc list-inside">
                                                <?php foreach ($detalles as $d): ?>
                                                    <li><?php echo $d['cantidad']; ?>x <?php echo htmlspecialchars($d['nombre']); ?>
                                                        ($<?php echo number_format($d['precio'], 2); ?>)</li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <div
                                        class="text-2xl font-bold <?php echo $mov['tipo'] == 'venta' ? 'text-blue-600' : 'text-green-600'; ?>">
                                        <?php echo $mov['tipo'] == 'venta' ? '-' : '+'; ?>$<?php echo number_format($mov['monto'], 2); ?>
                                    </div>
                                    <?php if ($mov['tipo'] == 'venta' && $mov['info_extra'] > 0): ?>
                                        <div class="text-xs text-green-600">Pagado en caja:
                                            $<?php echo number_format($mov['info_extra'], 2); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>


</body>

</html>