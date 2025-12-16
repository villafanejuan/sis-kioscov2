<?php
/**
 * Dashboard Principal
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Logic for welcome modal
$show_welcome = false;
if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true;
    $show_welcome = true;
}

// Obtener datos del usuario
$userId = $_SESSION['user_id'];
$userName = $_SESSION['nombre'] ?? $_SESSION['username'];
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Obtener conexión directa para queries simples
$db = Database::getInstance();

// Obtener estadísticas básicas
try {
    // Total productos
    $totalProductos = $db->fetchOne("SELECT COUNT(*) as total FROM productos")['total'] ?? 0;

    // Productos con stock bajo (menos de 5)
    $productosStockBajo = $db->fetchOne("SELECT COUNT(*) as total FROM productos WHERE stock < 5")['total'] ?? 0;

    // Ventas hoy
    $statsHoy = $db->fetchOne("
        SELECT COUNT(*) as total_ventas, COALESCE(SUM(total), 0) as total_ingresos 
        FROM ventas 
        WHERE DATE(fecha) = CURDATE()
    ");
    $ventasHoy = $statsHoy['total_ventas'] ?? 0;
    $ingresosHoy = $statsHoy['total_ingresos'] ?? 0;

    // Productos con stock bajo (detalles)
    $productosBajos = $db->fetchAll("
        SELECT p.*, c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.stock < 5
        ORDER BY p.stock ASC
        LIMIT 5
    ");

    // Productos más vendidos (últimos 7 días)
    $topProductos = $db->fetchAll("
        SELECT p.nombre, 
               SUM(vd.cantidad) as total_vendido,
               COUNT(DISTINCT v.id) as num_ventas
        FROM productos p
        INNER JOIN venta_detalles vd ON p.id = vd.producto_id
        INNER JOIN ventas v ON vd.venta_id = v.id
        WHERE v.fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY p.id, p.nombre
        ORDER BY total_vendido DESC
        LIMIT 5
    ");

} catch (Exception $e) {
    // Si hay error, usar valores por defecto
    $totalProductos = 0;
    $productosStockBajo = 0;
    $ventasHoy = 0;
    $ingresosHoy = 0;
    $productosBajos = [];
    $topProductos = [];
}

// Obtener turno abierto del usuario actual (si tiene acceso a caja)
$turnoAbierto = null;
if (canAccess('cash')) {
    $turnoAbierto = $db->fetchOne("SELECT * FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'", [$userId]);
}

// CSRF Token
$csrf_token = Security::generateCsrf();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <!-- Contenido Principal -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Alertas -->
        <!-- Alertas (Solo para Admin y Kiosquero) -->
        <?php if ($_SESSION['role'] !== 'cajero'): ?>
            <?php if (canAccess('cash')): ?>
                <?php if ($turnoAbierto): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-4"></i>
                            <div>
                                <p class="font-bold">Turno de caja abierto</p>
                                <p class="text-sm">Puedes realizar ventas normalmente</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
                            <div>
                                <p class="font-bold">No tienes turno abierto</p>
                                <p class="text-sm">Debes abrir un turno de caja para realizar ventas</p>
                            </div>
                            <a href="cash.php" class="ml-auto bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                                Abrir Turno
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($productosStockBajo > 0): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-2xl mr-4"></i>
                        <div>
                            <p class="font-bold"><?php echo $productosStockBajo; ?> producto(s) con stock bajo</p>
                            <p class="text-sm">Requieren reposición urgente</p>
                        </div>
                        <a href="products.php?filter=low_stock"
                            class="ml-auto bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Ver Productos
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-gray-800 mb-8">
            <i class="fas fa-chart-line mr-3"></i>Dashboard
        </h1>

        <!-- Tarjetas de Estadísticas -->
        <div
            class="grid grid-cols-1 <?php echo $_SESSION['role'] === 'cajero' ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-4'; ?> gap-6 mb-8">
            <!-- Total Productos (Oculto para Cajero) -->
            <?php if ($_SESSION['role'] !== 'cajero'): ?>
                <div
                    class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-xl shadow-lg transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Productos</p>
                            <p class="text-4xl font-bold mt-2"><?php echo $totalProductos; ?></p>
                        </div>
                        <i class="fas fa-box text-5xl opacity-30"></i>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Ventas Hoy -->
            <div
                class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-xl shadow-lg transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Ventas Hoy (Global)</p>
                        <p class="text-4xl font-bold mt-2"><?php echo $ventasHoy; ?></p>
                    </div>
                    <i class="fas fa-shopping-cart text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Ingresos Hoy -->
            <div
                class="bg-gradient-to-br from-yellow-400 to-yellow-600 text-white p-6 rounded-xl shadow-lg transform hover:scale-105 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Ingresos Hoy (Global)</p>
                        <p class="text-4xl font-bold mt-2">$<?php echo number_format($ingresosHoy, 0); ?></p>
                    </div>
                    <i class="fas fa-dollar-sign text-5xl opacity-30"></i>
                </div>
            </div>

            <!-- Stock Bajo (Oculto para Cajero) -->
            <?php if ($_SESSION['role'] !== 'cajero'): ?>
                <div
                    class="bg-gradient-to-br from-red-400 to-red-600 text-white p-6 rounded-xl shadow-lg transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Stock Bajo</p>
                            <p class="text-4xl font-bold mt-2"><?php echo $productosStockBajo; ?></p>
                        </div>
                        <i class="fas fa-exclamation-triangle text-5xl opacity-30"></i>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Grid de Contenido -->
        <div class="grid grid-cols-1 <?php echo $_SESSION['role'] === 'cajero' ? '' : 'lg:grid-cols-2'; ?> gap-6">
            <!-- Productos con Stock Bajo (Oculto para Cajero) -->
            <?php if ($_SESSION['role'] !== 'cajero'): ?>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        Productos con Stock Bajo
                    </h3>
                    <?php if (empty($productosBajos)): ?>
                        <p class="text-gray-500 text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-2"></i><br>
                            ¡Todos los productos tienen stock suficiente!
                        </p>
                    <?php else: ?>
                        <div class="space-y-3" id="lowStockList">
                            <?php foreach (array_slice($productosBajos, 0, 5) as $producto): ?>
                                <div id="prod-row-<?php echo $producto['id']; ?>"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($producto['nombre']); ?>
                                        </p>
                                        <p class="text-sm text-gray-600">Stock actual: <span
                                                id="stock-val-<?php echo $producto['id']; ?>"><?php echo $producto['stock']; ?></span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                            Bajo
                                        </span>
                                        <button
                                            onclick="openRestockModal(<?php echo $producto['id']; ?>, '<?php echo addslashes($producto['nombre']); ?>')"
                                            class="bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded-full flex items-center justify-center transition shadow"
                                            title="Reponer Stock Rápido">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Productos Más Vendidos -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-fire text-orange-500 mr-3"></i>
                    Top 5 Más Vendidos (7 días)
                </h3>
                <?php if (empty($topProductos)): ?>
                    <p class="text-gray-500 text-center py-8">
                        <i class="fas fa-chart-line text-gray-400 text-4xl mb-2"></i><br>
                        No hay datos de ventas aún
                    </p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($topProductos as $index => $producto): ?>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <span
                                    class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">
                                    <?php echo $index + 1; ?>
                                </span>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($producto['nombre']); ?>
                                    </p>
                                    <p class="text-sm text-gray-600"><?php echo $producto['num_ventas']; ?> ventas</p>
                                </div>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                                    <?php echo $producto['total_vendido']; ?> unid.
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-bolt text-yellow-500 mr-3"></i>Acciones Rápidas
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php if (canAccess('sales')): ?>
                    <a href="sales.php"
                        class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-lg text-center hover:from-green-600 hover:to-green-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                        <p class="font-bold">Nueva Venta</p>
                    </a>
                <?php endif; ?>

                <?php if (canAccess('products')): ?>
                    <a href="products.php"
                        class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-lg text-center hover:from-blue-600 hover:to-blue-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-box text-3xl mb-2"></i>
                        <p class="font-bold">Productos</p>
                    </a>
                <?php endif; ?>

                <?php if (canAccess('cash')): ?>
                    <a href="cash.php"
                        class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-lg text-center hover:from-purple-600 hover:to-purple-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-cash-register text-3xl mb-2"></i>
                        <p class="font-bold">Caja</p>
                    </a>
                <?php endif; ?>

                <?php if (canAccess('reports')): ?>
                    <a href="reports.php"
                        class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-lg text-center hover:from-orange-600 hover:to-orange-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-chart-bar text-3xl mb-2"></i>
                        <p class="font-bold">Reportes</p>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white mt-12 py-6 border-t">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600">
            <p><?php echo APP_NAME; ?> v2.0 Professional &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <!-- Restock Modal -->
    <div id="restockModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRestockModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto pointer-events-none">
            <div
                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0 pointer-events-auto">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-plus text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="restockTitle">Reponer
                                    Stock</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">¿Cuántas unidades deseas agregar?</p>
                                    <form id="restockForm" class="mt-4"
                                        onsubmit="event.preventDefault(); submitRestock();">
                                        <input type="hidden" id="restockId">
                                        <div class="relative">
                                            <input type="number" id="restockQty" min="1" value="10"
                                                class="block w-full rounded-md border-0 py-1.5 pl-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 border border-gray-300">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" onclick="submitRestock()"
                            class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Confirmar</button>
                        <button type="button" onclick="closeRestockModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Modal -->
    <?php if ($show_welcome): ?>
        <div id="welcomeModal" class="fixed inset-0 z-50 flex items-center justify-center hidden"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 transition-opacity" aria-hidden="true"></div>

            <div
                class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all scale-100">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" onclick="closeWelcomeModal()"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Cerrar</span>
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-6 animate-bounce">
                        <i class="fas fa-smile-beam text-4xl text-blue-600"></i>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                        ¡Hola, <?php echo htmlspecialchars($userName); ?>!
                    </h3>

                    <div class="mt-4">
                        <p id="welcomeMessage" class="text-lg text-gray-600 italic font-medium"></p>
                    </div>

                    <div class="mt-8">
                        <button type="button" onclick="closeWelcomeModal()"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm transition duration-150 ease-in-out transform hover:-translate-y-0.5">
                            ¡Comenzar!
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>         document.addEventListener('DOMContentLoaded', function () {
                const messages = ["¡Que tengas una excelente jornada!", "¡A darle con todo hoy!", "Tu esfuerzo hace la diferencia.", "¡Éxito en tus ventas de hoy!", "Recuerda sonreír, ¡es contagioso!", "¡Hoy será un gran día!", "La mejor actitud para hoy.", "¡Listo para triunfar!"];
                const randomMessage = messages[Math.floor(Math.random() * messages.length)]; document.getElementById('welcomeMessage').textContent = randomMessage;
                // Show modal with a slight delay for better UX             setTimeout(() => {                 const modal = document.getElementById('welcomeModal');                 modal.classList.remove('hidden');             }, 500);         });
                function closeWelcomeModal() { const modal = document.getElementById('welcomeModal'); modal.classList.add('hidden'); }
        </script>
    <?php endif; ?>

    <script>
                         function openRestockModal(id, nombre) {
                             document.getElementById('restockId').value = id;
                             document.getElementById('restockTitle').textContent = 'Reponer Stock: ' + nombre;
                             document.getElementById('restockQty').value = 10;
                             document.getElementById('restockModal').classList.remove('hidden');
                             setTimeout(() => document.getElementById('restockQty').focus(), 100);
                         }

                         function closeRestockModal() {
                             document.getElementById('restockModal').classList.add('hidden');
                         }

                         async function submitRestock() {
                             const id = document.getElementById('restockId').value;
                             const qty = document.getElementById('restockQty').value;

                             if (!qty || qty <= 0) return;

                             const formData = new FormData();
                             formData.append('id', id);
                             formData.append('quantity', qty);

                             try {
                                 const res = await fetch('ajax_update_stock.php', { method: 'POST', body: formData });
                                 const data = await res.json();

                                 if (data.success) {
                                     const row = document.getElementById('prod-row-' + id);
                                     if (data.new_stock >= 5) {
                                         // Remove from "Low Stock" list if it's healthy now
                                         row.remove();
                                         // Check if list is empty, reload to clear alert
                                         const list = document.getElementById('lowStockList');
                                         if (!list || list.children.length === 0) {
                                             location.reload();
                                         }
                                     } else {
                                         // Just update number
                                         document.getElementById('stock-val-' + id).textContent = data.new_stock;
                                     }
                                     closeRestockModal();
                                 } else {
                                     alert('Error: ' + data.message);
                                 }
                             } catch (e) {
                                 console.error(e);
                                 alert('Error de conexión');
                             }
                         }
    </script>
</body>

</html>