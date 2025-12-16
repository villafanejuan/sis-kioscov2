<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

if (!canAccess('products')) {
    header('Location: dashboard.php');
    exit;
}

$isAdmin = checkAdmin();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Error de seguridad';
        $messageType = 'error';
    } else {
        try {
            if (isset($_POST['add_customer'])) {
                $nombre = sanitize($_POST['nombre']);
                $telefono = sanitize($_POST['telefono']);
                $email = sanitize($_POST['email'] ?? '');
                $direccion = sanitize($_POST['direccion'] ?? '');
                $limite_credito = floatval($_POST['limite_credito'] ?? 0);

                $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, email, direccion, limite_credito) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $telefono ?: null, $email ?: null, $direccion, $limite_credito]);
                $message = 'Cliente agregado exitosamente';
                $messageType = 'success';
            } elseif (isset($_POST['update_customer'])) {
                $id = intval($_POST['id']);
                $nombre = sanitize($_POST['nombre']);
                $telefono = sanitize($_POST['telefono']);
                $email = sanitize($_POST['email'] ?? '');
                $direccion = sanitize($_POST['direccion'] ?? '');
                $limite_credito = floatval($_POST['limite_credito'] ?? 0);

                $stmt = $pdo->prepare("UPDATE clientes SET nombre=?, telefono=?, email=?, direccion=?, limite_credito=? WHERE id=?");
                $stmt->execute([$nombre, $telefono ?: null, $email ?: null, $direccion, $limite_credito, $id]);
                $message = 'Cliente actualizado exitosamente';
                $messageType = 'success';
            } elseif (isset($_POST['delete_customer'])) {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE clientes SET activo = 0 WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Cliente desactivado';
                $messageType = 'success';
            } elseif (isset($_POST['activate_customer'])) {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE clientes SET activo = 1 WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Cliente activado';
                $messageType = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Obtener clientes
$showInactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1';
if ($showInactive) {
    $stmt = $pdo->query("SELECT * FROM clientes ORDER BY activo DESC, nombre");
} else {
    $stmt = $pdo->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre");
}
$clientes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-blue-600 mr-2"></i>Gestión de Clientes
            </h1>
            <div class="flex gap-3">
                <?php if ($isAdmin): ?>
                    <a href="?show_inactive=<?php echo $showInactive ? '0' : '1'; ?>"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-<?php echo $showInactive ? 'eye-slash' : 'eye'; ?> mr-2"></i>
                        <?php echo $showInactive ? 'Ocultar Inactivos' : 'Ver Inactivos'; ?>
                    </a>
                <?php endif; ?>
                <button onclick="toggleAddForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Nuevo Cliente
                </button>
            </div>
        </div>

        <?php if ($message): ?>
            <div
                class="mb-4 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario Agregar -->
        <div id="addForm" class="hidden bg-white rounded-lg shadow-lg mb-6 p-6">
            <h3 class="text-xl font-bold mb-4">Nuevo Cliente</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="nombre" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="telefono"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Límite de Crédito</label>
                    <input type="number" step="0.01" name="limite_credito" value="0"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Dirección</label>
                    <textarea name="direccion" rows="2"
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="button" onclick="toggleAddForm()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</button>
                    <button type="submit" name="add_customer"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Guardar</button>
                </div>
            </form>
        </div>

        <!-- Buscador -->
        <div class="mb-4 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="table_search"
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-lg shadow-sm"
                placeholder="Buscar por Todo: ID, Nombre, Teléfono, Email o Dirección..." onkeyup="filterTable()">
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Saldo</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Límite</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($clientes as $c): ?>
                        <tr class="customer-row <?php echo $c['activo'] ? 'hover:bg-gray-50' : 'bg-red-50'; ?>"
                            data-search="<?php echo strtolower($c['id'] . ' ' . $c['nombre'] . ' ' . ($c['telefono'] ?? '') . ' ' . ($c['email'] ?? '') . ' ' . ($c['direccion'] ?? '')); ?>">
                            <td class="px-6 py-4 text-gray-500">#<?php echo $c['id']; ?></td>
                            <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($c['nombre']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($c['telefono'] ?? '-'); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($c['email'] ?? '-'); ?></td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="<?php echo $c['saldo_cuenta'] >= 0 ? 'text-green-600' : 'text-red-600'; ?> font-bold">
                                    $<?php echo number_format($c['saldo_cuenta'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">$<?php echo number_format($c['limite_credito'], 2); ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="customer_account.php?id=<?php echo $c['id']; ?>"
                                    class="text-purple-600 hover:text-purple-800 mr-2" title="Ver Cuenta">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </a>
                                <button onclick="editCustomer(<?php echo htmlspecialchars(json_encode($c)); ?>)"
                                    class="text-blue-600 hover:text-blue-800 mr-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($c['activo'] && $c['saldo_cuenta'] < 0): ?>
                                    <a href="payment_entry.php?cliente_id=<?php echo $c['id']; ?>"
                                        class="text-green-600 hover:text-green-800 mr-2" title="Registrar Pago">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($c['activo']): ?>
                                    <button
                                        onclick="deleteCustomer(<?php echo $c['id']; ?>, '<?php echo addslashes($c['nombre']); ?>')"
                                        class="text-red-600 hover:text-red-800" title="Desactivar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php else: ?>
                                    <button
                                        onclick="activateCustomer(<?php echo $c['id']; ?>, '<?php echo addslashes($c['nombre']); ?>')"
                                        class="text-green-600 hover:text-green-800" title="Activar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Editar Cliente</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="nombre" id="edit_nombre" required
                        class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Límite de Crédito</label>
                    <input type="number" step="0.01" name="limite_credito" id="edit_limite"
                        class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Dirección</label>
                    <textarea name="direccion" id="edit_direccion" rows="2"
                        class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</button>
                    <button type="submit" name="update_customer"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Desactivar Cliente</h3>
            <p class="mb-4">¿Desactivar a <strong id="delete_name"></strong>?</p>
            <form method="POST" class="flex gap-2">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="delete_id">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</button>
                <button type="submit" name="delete_customer"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">Desactivar</button>
            </form>
        </div>
    </div>

    <!-- Modal Activar -->
    <div id="activateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Activar Cliente</h3>
            <p class="mb-4">¿Activar a <strong id="activate_name"></strong>?</p>
            <form method="POST" class="flex gap-2">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="activate_id">
                <button type="button" onclick="closeActivateModal()"
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</button>
                <button type="submit" name="activate_customer"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Activar</button>
            </form>
        </div>
    </div>


    <script>
        function toggleAddForm() {
            document.getElementById('addForm').classList.toggle('hidden');
        }
        function editCustomer(c) {
            document.getElementById('edit_id').value = c.id;
            document.getElementById('edit_nombre').value = c.nombre;
            document.getElementById('edit_telefono').value = c.telefono || '';
            document.getElementById('edit_email').value = c.email || '';
            document.getElementById('edit_direccion').value = c.direccion || '';
            document.getElementById('edit_limite').value = c.limite_credito;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
        function deleteCustomer(id, name) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }
        function activateCustomer(id, name) {
            document.getElementById('activate_id').value = id;
            document.getElementById('activate_name').textContent = name;
            document.getElementById('activateModal').classList.remove('hidden');
            document.getElementById('activateModal').classList.add('flex');
        }
        function closeActivateModal() {
            document.getElementById('activateModal').classList.add('hidden');
            document.getElementById('activateModal').classList.remove('flex');
        }


        // Función de filtrado con debounce ligero
        function filterTable() {
            const input = document.getElementById('table_search');
            const filter = input.value.toLowerCase().trim();
            const rows = document.getElementsByClassName('customer-row');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const searchData = row.getAttribute('data-search') || "";

                if (filter === "" || searchData.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        }
    </script>
</body>

</html>