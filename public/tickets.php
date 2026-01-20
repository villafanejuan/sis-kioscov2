<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

if (!canAccess('sales')) {
    header('Location: dashboard.php');
    exit;
}

// No necesitamos $isAdmin porque no hay acciones de modificar/desactivar

$message = '';
$messageType = '';

// =====================
// LISTADO DE TICKETS
// =====================
$sql = "
    SELECT v.id, v.fecha, v.total,
           c.nombre AS cliente,
           u.nombre AS vendedor
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    LEFT JOIN usuarios u ON v.usuario_id = u.id
    ORDER BY v.fecha DESC
";
$tickets = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tickets - <?php echo APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-ticket-alt text-gray-700 mr-2"></i>Gestión de Tickets
            </h1>
        </div>

        <?php if ($message): ?>
            <div
                class="mb-4 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- BUSCADOR -->
        <input id="table_search" onkeyup="filterTable()"
            placeholder="Buscar tickets por ID, Cliente, Vendedor o Total..."
            class="w-full mb-4 px-4 py-3 border rounded-sm">

        <!-- TABLA -->
        <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b-2 border-gray-800">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Cliente</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr class="hover:bg-gray-50"
                            data-search="<?php echo strtolower($t['id'] . ' ' . $t['cliente'] . ' ' . $t['vendedor'] . ' ' . $t['total']); ?>">
                            <td class="px-6 py-4">#<?php echo $t['id']; ?></td>
                            <td class="px-6 py-4"><?php echo date('d/m/Y H:i', strtotime($t['fecha'])); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($t['cliente'] ?: 'Consumidor final'); ?></td>
                            <td class="px-6 py-4 text-right font-bold">$<?php echo number_format($t['total'], 2); ?></td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <a href="print_ticket.php?id=<?php echo $t['id']; ?>" target="_blank"
                                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1 rounded-sm text-sm">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </a>
                                <a href="print_ticket.php?id=<?php echo $t['id']; ?>" target="_blank"
                                    class="bg-gray-900 hover:bg-black text-white px-3 py-1 rounded-sm text-sm">
                                    <i class="fas fa-print mr-1"></i>Imprimir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('table_search').value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = row.dataset.search.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>

</html>