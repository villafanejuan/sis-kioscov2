<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

// Determinar permisos y filtro
$isAdmin = checkAdmin();
$userId = $_SESSION['user_id'];
$userRole = strtolower($_SESSION['role'] ?? '');
$isKiosquero = ($userRole === 'kiosquero');
$userFilter = null;

$whereClauseVentas = "";
$whereClausePagos = "";
$params = [];

// Admin y Cajero (Auditor) pueden ver todo y filtrar
if ($isAdmin || $userRole === 'cajero') {
    // Verificar si hay filtro en URL
    if (isset($_GET['user_filter']) && $_GET['user_filter'] !== 'all') {
        $filterId = intval($_GET['user_filter']);
        $whereClauseVentas = "WHERE v.usuario_id = ?";
        $whereClausePagos = "WHERE cp.usuario_id = ?";
        $params = [$filterId];
    }
    $stmt = $pdo->query("SELECT id, nombre, username, role FROM usuarios ORDER BY nombre");
    $users = $stmt->fetchAll();
} else {
    // Kiosquero: Solo puede ver SUS ventas
    $whereClauseVentas = "WHERE v.usuario_id = ?";
    $whereClausePagos = "WHERE cp.usuario_id = ?";
    $params = [$userId];
    $users = [];
}

// 1. Estadísticas generales
$statsParams = array_merge($params, $params);

$sqlStatsVentas = "SELECT 
                    COUNT(*) as cnt, 
                    COALESCE(SUM(total), 0) as val, 
                    COALESCE(SUM(LEAST(total, monto_pagado)), 0) as cash,
                    COALESCE(SUM(CASE WHEN total > monto_pagado THEN total - monto_pagado ELSE 0 END), 0) as credit
                   FROM ventas v $whereClauseVentas";

$sqlStatsPagos = "SELECT 
                    COALESCE(SUM(monto), 0) as cash_debt
                  FROM cliente_pagos cp $whereClausePagos";

// Execute separately
$stmtV = $pdo->prepare($sqlStatsVentas);
$stmtV->execute($params);
$resV = $stmtV->fetch();

$stmtP = $pdo->prepare($sqlStatsPagos);
$stmtP->execute($params);
$resP = $stmtP->fetch();

// Calculate Transfers Total (To subtract from Cash and show separately)
// Helper to prepend where
function prependWhere($clause, $extra)
{
    if (empty($clause))
        return "WHERE " . $extra;
    return $clause . " AND " . $extra;
}

$paramTransfer = $params;
$sqlStatsTransfer = "SELECT 
                        COALESCE(SUM(vp.monto), 0) as total_trans
                     FROM venta_pagos vp
                     JOIN metodos_pago mp ON vp.metodo_pago_id = mp.id
                     JOIN ventas v ON vp.venta_id = v.id 
                     $whereClauseVentas 
                     AND mp.nombre = 'Transferencia'";

$stmtT = $pdo->prepare($sqlStatsTransfer);
$stmtT->execute($paramTransfer);
$resT = $stmtT->fetch();
$total_transfer = $resT['total_trans'];

$stats = [
    'total_ventas' => $resV['cnt'],
    'total_valor' => $resV['val'], // Facturado
    'dinero_caja' => ($resV['cash'] + $resP['cash_debt']) - $total_transfer, // Adjust Cash to be REAL PHYSICAL CASH
    'total_transfer' => $total_transfer,
    'credito_otorgado' => $resV['credit'],
    'debt_collected' => $resP['cash_debt']
];

// 2. Ventas Semanales (Solo Ventas volumen)
$dateCondition = "fecha >= DATE_SUB(CURDATE(), INTERVAL 5 DAY)";

$weeklyWhere = prependWhere($whereClauseVentas, $dateCondition);
$sql = "SELECT DATE(fecha) as fecha, COUNT(*) as cantidad, SUM(total) as total 
        FROM ventas v
        $weeklyWhere
        GROUP BY DATE(fecha) 
        ORDER BY fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ventas_semanales = $stmt->fetchAll();

// 3. Productos Top (Solo Ventas)
$sql = "SELECT p.nombre, SUM(vd.cantidad) as total_vendido 
        FROM venta_detalles vd 
        JOIN productos p ON vd.producto_id = p.id 
        JOIN ventas v ON vd.venta_id = v.id
        $whereClauseVentas
        GROUP BY p.id, p.nombre 
        ORDER BY total_vendido DESC 
        LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos_mas_vendidos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema Kiosco</title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
    <style>
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }

        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-chart-pie text-purple-600 mr-2"></i>Reportes Financieros
            </h1>

            <?php if ($isAdmin || $userRole === 'cajero'): ?>
                <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-200">
                    <form method="GET" class="flex items-center gap-2">
                        <label for="user_filter" class="text-sm font-medium text-gray-700">Filtrar por:</label>
                        <select name="user_filter" id="user_filter" onchange="this.form.submit()"
                            class="border-gray-300 rounded-md shadow-sm text-sm py-1">
                            <option value="all" <?php echo (!isset($_GET['user_filter']) || $_GET['user_filter'] === 'all') ? 'selected' : ''; ?>>
                                📊 General (Todos)
                            </option>
                            <option value="<?php echo $_SESSION['user_id']; ?>" <?php echo (isset($_GET['user_filter']) && $_GET['user_filter'] == $_SESSION['user_id']) ? 'selected' : ''; ?>>
                                👤 Mi Usuario
                            </option>
                            <optgroup label="Usuarios">
                                <?php foreach ($users as $u): ?>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <option value="<?php echo $u['id']; ?>" <?php echo (isset($_GET['user_filter']) && $_GET['user_filter'] == $u['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['nombre'] ?? $u['username']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </form>
                </div>
            <?php endif; ?>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Ventas -->
            <div class="bg-white p-4 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase">Ventas (Cant)</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">
                            <?php echo number_format($stats['total_ventas']); ?>
                        </h3>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-full text-blue-600"><i class="fas fa-shopping-cart text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Total Facturado -->
            <div class="bg-white p-4 rounded-xl shadow-lg border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase">Total Facturado</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">
                            $<?php echo number_format($stats['total_valor'], 2); ?></h3>
                        <p class="text-xs text-gray-400 mt-1">Valor mercancía vendida</p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-full text-green-600"><i
                            class="fas fa-file-invoice-dollar text-lg"></i></div>
                </div>
            </div>

            <!-- Efectivo (Caja) -->
            <div class="bg-white p-4 rounded-xl shadow-lg border-l-4 border-teal-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase">Efectivo Físico</p>
                        <h3 class="text-2xl font-bold text-teal-700 mt-1">
                            $<?php echo number_format($stats['dinero_caja'], 2); ?></h3>
                        <p class="text-xs text-gray-400 mt-1">En mano</p>
                    </div>
                    <div class="p-2 bg-teal-100 rounded-full text-teal-600"><i class="fas fa-cash-register text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Transferencias -->
            <div class="bg-white p-4 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase">Transferencias</p>
                        <h3 class="text-2xl font-bold text-purple-700 mt-1">
                            $<?php echo number_format($stats['total_transfer'], 2); ?></h3>
                        <p class="text-xs text-gray-400 mt-1">Digital</p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-full text-purple-600"><i
                            class="fas fa-mobile-alt text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Crédito Otorgado (Fiado) -->
            <div class="bg-white p-4 rounded-xl shadow-lg border-l-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase">Fiado Pendiente</p>
                        <h3 class="text-2xl font-bold text-red-600 mt-1">
                            $<?php echo number_format($stats['credito_otorgado'], 2); ?></h3>
                        <p class="text-xs text-gray-400 mt-1">Crédito otorgado</p>
                    </div>
                    <div class="p-2 bg-red-100 rounded-full text-red-600"><i
                            class="fas fa-hand-holding-usd text-lg"></i></div>
                </div>
            </div>
        </div>

        <?php
        // Filtros Fecha
        $period = $_GET['period'] ?? 'today'; // Default to today for speed
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        $dateWhereV = "";
        $dateWhereP = "";
        $dateParams = [];

        if ($period === 'custom' && !empty($dateFrom) && !empty($dateTo)) {
            $dateWhereV = "AND DATE(v.fecha) BETWEEN ? AND ?";
            $dateWhereP = "AND DATE(cp.fecha) BETWEEN ? AND ?";
            $dateParams = [$dateFrom, $dateTo];
        } else {
            $colV = "DATE(v.fecha)";
            $colP = "DATE(cp.fecha)";
            switch ($period) {
                case 'today':
                    $dateWhereV = "AND $colV = CURDATE()";
                    $dateWhereP = "AND $colP = CURDATE()";
                    break;
                case 'week':
                    $dateWhereV = "AND YEARWEEK(v.fecha, 1) = YEARWEEK(CURDATE(), 1)";
                    $dateWhereP = "AND YEARWEEK(cp.fecha, 1) = YEARWEEK(CURDATE(), 1)";
                    break;
                case 'month':
                    $dateWhereV = "AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())";
                    $dateWhereP = "AND MONTH(cp.fecha) = MONTH(CURDATE()) AND YEAR(cp.fecha) = YEAR(CURDATE())";
                    break;
                case 'year':
                    $dateWhereV = "AND YEAR(v.fecha) = YEAR(CURDATE())";
                    $dateWhereP = "AND YEAR(cp.fecha) = YEAR(CURDATE())";
                    break;
                case 'all':
                default:
                    break;
            }
        }

        $finalParams = array_merge($params, $dateParams, $params, $dateParams);

        $sql_union = "
        (SELECT 
            v.id as id,
            v.fecha as fecha,
            COALESCE(u.nombre, u.username) as usuario,
            GROUP_CONCAT(CONCAT(p.nombre, ' (', vd.cantidad, ')') SEPARATOR ', ') as detalle,
            'venta' as tipo,
            v.total as total_ft,
            LEAST(v.total, v.monto_pagado) as pagado,
            (CASE WHEN v.total > v.monto_pagado THEN v.total - v.monto_pagado ELSE 0 END) as deuda,
            (SELECT GROUP_CONCAT(mp.nombre SEPARATOR ', ') FROM venta_pagos vp JOIN metodos_pago mp ON vp.metodo_pago_id = mp.id WHERE vp.venta_id = v.id) as metodo,
            (SELECT referencia FROM venta_pagos vp JOIN metodos_pago mp ON vp.metodo_pago_id = mp.id WHERE vp.venta_id = v.id AND mp.requiere_referencia = 1 LIMIT 1) as referencia,
            (SELECT telefono FROM venta_pagos vp JOIN metodos_pago mp ON vp.metodo_pago_id = mp.id WHERE vp.venta_id = v.id AND mp.requiere_referencia = 1 LIMIT 1) as telefono
        FROM ventas v
        LEFT JOIN usuarios u ON v.usuario_id = u.id
        JOIN venta_detalles vd ON v.id = vd.venta_id
        JOIN productos p ON vd.producto_id = p.id
        $whereClauseVentas $dateWhereV
        GROUP BY v.id)

        UNION ALL

        (SELECT
            cp.id as id,
            cp.fecha as fecha,
            COALESCE(u.nombre, u.username) as usuario,
            CONCAT('Abono Saldo - ', c.nombre) as detalle,
            'pago' as tipo,
            0 as total_ft,
            cp.monto as pagado,
            0 as deuda,
            'Efectivo' as metodo,
            NULL as referencia,
            NULL as telefono
        FROM cliente_pagos cp
        LEFT JOIN usuarios u ON cp.usuario_id = u.id
        JOIN clientes c ON cp.cliente_id = c.id
        $whereClausePagos $dateWhereP)

        ORDER BY fecha DESC
        LIMIT 2000
        ";

        $stmt = $pdo->prepare($sql_union);
        $stmt->execute($finalParams);
        $transactions = $stmt->fetchAll();

        // Calculate Totals for Footer
        $ft_total_facturado = 0;
        $ft_total_cash = 0;
        $ft_total_deuda = 0;
        foreach ($transactions as $t) {
            $ft_total_facturado += $t['total_ft'];
            $ft_total_cash += $t['pagado'];
            $ft_total_deuda += $t['deuda'];
        }
        ?>

        <!-- Transactions Table -->
        <div class="bg-white p-6 rounded-xl shadow-lg mt-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-list-ul text-blue-600 mr-2"></i>Historial de Transacciones (Ventas y Pagos)
                </h3>

                <!-- Filter Form -->
                <form method="GET" class="flex flex-wrap items-center gap-2" id="periodForm">
                    <?php if (isset($_GET['user_filter'])): ?>
                        <input type="hidden" name="user_filter"
                            value="<?php echo htmlspecialchars($_GET['user_filter']); ?>">
                    <?php endif; ?>
                    <select name="period" id="period" onchange="toggleDateInputs()"
                        class="border-gray-300 rounded text-sm py-1">
                        <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>📅 Hoy</option>
                        <option value="week" <?php echo $period === 'week' ? 'selected' : ''; ?>>📅 Semana</option>
                        <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>📅 Mes</option>
                        <option value="all" <?php echo $period === 'all' ? 'selected' : ''; ?>>∞ Todo</option>
                        <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>📆 Rango</option>
                    </select>

                    <div id="customDateInputs" class="flex items-center gap-2"
                        style="display: <?php echo $period === 'custom' ? 'flex' : 'none'; ?>;">
                        <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>"
                            class="hidden">
                        <span class="text-xs text-gray-500">(Usar filtros arriba)</span>
                    </div>
                </form>
                <script>
                    function toggleDateInputs() {
                        const period = document.getElementById('period').value;
                        if (period !== 'custom') document.getElementById('periodForm').submit();
                    }
                </script>

                <div class="flex gap-2">
                    <button onclick="exportTableToExcel('trans_table', 'Transacciones_<?php echo $period; ?>')"
                        class="bg-green-600 text-white px-3 py-1 rounded text-sm"><i
                            class="fas fa-file-excel mr-1"></i>Excel</button>
                    <button onclick="exportTableToPDF('Transacciones_<?php echo $period; ?>')"
                        class="bg-red-600 text-white px-3 py-1 rounded text-sm"><i
                            class="fas fa-file-pdf mr-1"></i>PDF</button>
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                <table id="trans_table" class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Tipo</th>
                            <th class="px-3 py-2 text-left">Fecha</th>
                            <th class="px-3 py-2 text-left">Detalle / Productos</th>
                            <th class="px-3 py-2 text-left">Método</th>
                            <th class="px-3 py-2 text-right">Facturado</th>
                            <th class="px-3 py-2 text-right text-teal-700">Ingreso</th>
                            <th class="px-3 py-2 text-right text-red-600">Deuda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($transactions as $t):
                            $isPago = ($t['tipo'] === 'pago');
                            $badge = $isPago
                                ? '<span class="badge badge-primary">ABONO</span>'
                                : '<span class="badge badge-success">VENTA</span>';

                            if (!$isPago && $t['deuda'] > 0) {
                                if ($t['pagado'] == 0)
                                    $badge = '<span class="badge badge-danger">FIADO</span>';
                                else
                                    $badge = '<span class="badge badge-warning">PARCIAL</span>';
                            }

                            $rowClass = $isPago ? 'bg-blue-50 hover:bg-blue-100' : 'hover:bg-gray-50';

                            $metodoDisplay = htmlspecialchars($t['metodo'] ?? '-');
                            if (!empty($t['referencia'])) {
                                $metodoDisplay .= ' <span class="text-xs text-gray-500">Ref: ' . htmlspecialchars($t['referencia']) . '</span>';
                            }
                            if (!empty($t['telefono'])) {
                                $metodoDisplay .= ' <span class="text-xs text-blue-500"><i class="fas fa-phone-alt ml-1 mr-1"></i>' . htmlspecialchars($t['telefono']) . '</span>';
                            }
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td class="px-3 py-2 text-center"><?php echo $badge; ?></td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <?php echo date('d/m/y H:i', strtotime($t['fecha'])); ?>
                                </td>
                                <td class="px-3 py-2 text-xs truncate max-w-xs"
                                    title="<?php echo htmlspecialchars($t['detalle']); ?>">
                                    <?php echo htmlspecialchars($t['detalle']); ?>
                                </td>
                                <td class="px-3 py-2 text-xs">
                                    <?php echo $metodoDisplay; ?>
                                </td>
                                <td class="px-3 py-2 text-right font-bold">
                                    <?php echo ($t['total_ft'] > 0) ? '$' . number_format($t['total_ft'], 2) : '-'; ?>
                                </td>
                                <td class="px-3 py-2 text-right text-teal-700 font-bold">
                                    <?php echo ($t['pagado'] > 0) ? '$' . number_format($t['pagado'], 2) : '-'; ?>
                                </td>
                                <td class="px-3 py-2 text-right text-red-600 font-bold">
                                    <?php echo ($t['deuda'] > 0) ? '$' . number_format($t['deuda'], 2) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="4" class="px-3 py-2 text-right">TOTALES:</td>
                            <td class="px-3 py-2 text-right">$<?php echo number_format($ft_total_facturado, 2); ?></td>
                            <td class="px-3 py-2 text-right text-teal-700">
                                $<?php echo number_format($ft_total_cash, 2); ?></td>
                            <td class="px-3 py-2 text-right text-red-600">
                                $<?php echo number_format($ft_total_deuda, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <!-- Export Scripts -->
    <script src="assets/js/xlsx.full.min.js"></script>
    <script src="assets/js/jspdf.umd.min.js"></script>
    <script src="assets/js/jspdf.plugin.autotable.min.js"></script>

    <script>
        function exportTableToExcel(tableId, filename) {
            const table = document.getElementById(tableId);
            const wb = XLSX.utils.table_to_book(table, { sheet: "Transacciones" });
            XLSX.writeFile(wb, filename + ".xlsx");
        }

        function exportTableToPDF(filename) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            doc.text("Reporte Transacciones", 14, 15);
            doc.autoTable({ html: '#trans_table', startY: 25, theme: 'grid' });
            doc.save(filename + ".pdf");
        }
    </script>
</body>

</html>