<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

if (!canAccess('suppliers')) {
    header('Location: dashboard.php');
    exit;
}

$isAdmin = checkAdmin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';

// =====================
// ACCIONES
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Error de seguridad';
        $messageType = 'error';
    } else {
        try {

            // ALTA
            if (isset($_POST['add_supplier'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO proveedor
                    (razon_social, nombre_fantasia, cuit, condicion_iva, telefono, email, direccion, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo')
                ");
                $stmt->execute([
                    sanitize($_POST['razon_social']),
                    sanitize($_POST['nombre_fantasia'] ?? null),
                    sanitize($_POST['cuit']),
                    sanitize($_POST['condicion_iva']),
                    sanitize($_POST['telefono'] ?? null),
                    sanitize($_POST['email'] ?? null),
                    sanitize($_POST['direccion'] ?? null)
                ]);
                $message = 'Proveedor agregado correctamente';
                $messageType = 'success';
            }

            // EDITAR
            elseif (isset($_POST['update_supplier'])) {
                $stmt = $pdo->prepare("
                    UPDATE proveedor SET
                        razon_social = ?,
                        nombre_fantasia = ?,
                        condicion_iva = ?,
                        telefono = ?,
                        email = ?,
                        direccion = ?
                    WHERE idProveedor = ?
                ");
                $stmt->execute([
                    sanitize($_POST['razon_social']),
                    sanitize($_POST['nombre_fantasia'] ?? null),
                    sanitize($_POST['condicion_iva']),
                    sanitize($_POST['telefono'] ?? null),
                    sanitize($_POST['email'] ?? null),
                    sanitize($_POST['direccion'] ?? null),
                    intval($_POST['idProveedor'])
                ]);
                $message = 'Proveedor actualizado';
                $messageType = 'success';
            }

            // DESACTIVAR
            elseif (isset($_POST['delete_supplier'])) {
                $stmt = $pdo->prepare("UPDATE proveedor SET estado='Inactivo' WHERE idProveedor=?");
                $stmt->execute([intval($_POST['id'])]);
                $message = 'Proveedor desactivado';
                $messageType = 'success';
            }

            // ACTIVAR
            elseif (isset($_POST['activate_supplier'])) {
                $stmt = $pdo->prepare("UPDATE proveedor SET estado='Activo' WHERE idProveedor=?");
                $stmt->execute([intval($_POST['id'])]);
                $message = 'Proveedor activado';
                $messageType = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// =====================
// LISTADO
// =====================
$showInactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == 1;

$sql = $showInactive
    ? "SELECT * FROM proveedor ORDER BY estado DESC, razon_social"
    : "SELECT * FROM proveedor WHERE estado='Activo' ORDER BY razon_social";

$proveedores = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Proveedores - <?php echo APP_NAME; ?></title>
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
                <i class="fas fa-truck text-gray-700 mr-2"></i>Gestión de Proveedores
            </h1>
            <div class="flex gap-3">
                <?php if ($isAdmin): ?>
                    <a href="?show_inactive=<?php echo $showInactive ? 0 : 1; ?>"
                        class="bg-white border border-gray-400 text-gray-800 px-4 py-2 rounded-sm hover:bg-gray-100 transition shadow-sm">
                        <i class="fas fa-eye<?php echo $showInactive ? '-slash' : ''; ?> mr-2"></i>
                        <?php echo $showInactive ? 'Ocultar Inactivos' : 'Ver Inactivos'; ?>
                    </a>
                <?php endif; ?>
                <button onclick="toggleAddForm()"
                    class="bg-gray-900 text-white px-4 py-2 rounded-sm hover:bg-black transition shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Nuevo Proveedor
                </button>
            </div>
        </div>

        <?php if ($message): ?>
            <div
                class="mb-4 p-4 rounded-sm border-l-4 <?php echo $messageType === 'success' ? 'bg-gray-100 border-gray-800 text-gray-800' : 'bg-red-50 border-red-800 text-red-900'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- FORM ALTA -->
        <div id="addForm" class="hidden bg-white border border-gray-200 rounded-sm shadow-sm mb-6 p-6">
            <h3 class="text-xl font-bold mb-4">Nuevo Proveedor</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div>
                    <label>Razón Social *</label>
                    <input name="razon_social" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Nombre Fantasía</label>
                    <input name="nombre_fantasia" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>CUIT *</label>
                    <input name="cuit" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Condición IVA *</label>
                    <select name="condicion_iva" required class="w-full border rounded-lg px-3 py-2">
                        <option>Responsable Inscripto</option>
                        <option>Monotributista</option>
                        <option>Exento</option>
                        <option>No Responsable</option>
                    </select>
                </div>
                <div>
                    <label>Teléfono</label>
                    <input name="telefono" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label>Dirección</label>
                    <textarea name="direccion" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="button" onclick="toggleAddForm()"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-sm hover:bg-gray-50">Cancelar</button>
                    <button name="add_supplier"
                        class="bg-gray-900 text-white px-4 py-2 rounded-sm hover:bg-black">Guardar</button>
                </div>
            </form>
        </div>

        <!-- BUSCADOR -->
        <input id="table_search" onkeyup="filterTable()" placeholder="Buscar proveedor..."
            class="w-full mb-4 px-4 py-3 border rounded-lg">

        <!-- TABLA -->
        <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b-2 border-gray-800">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Razón Social</th>
                        <th class="px-6 py-3">CUIT</th>
                        <th class="px-6 py-3">IVA</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $p): ?>
                        <tr class="border-b border-gray-200 <?php echo $p['estado'] === 'Inactivo' ? 'bg-gray-100 text-gray-500' : 'hover:bg-gray-50'; ?>"
                            data-search="<?php echo strtolower($p['razon_social'] . ' ' . $p['cuit']); ?>">
                            <td class="px-6 py-4">#<?php echo $p['idProveedor']; ?></td>
                            <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($p['razon_social']); ?></td>
                            <td class="px-6 py-4"><?php echo $p['cuit']; ?></td>
                            <td class="px-6 py-4"><?php echo $p['condicion_iva']; ?></td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="viewSupplier(<?php echo htmlspecialchars(json_encode($p)); ?>)"
                                    class="text-blue-600 hover:text-blue-800 mr-2 transition"><i
                                        class="fas fa-eye"></i></button>

                                <button onclick="editSupplier(<?php echo htmlspecialchars(json_encode($p)); ?>)"
                                    class="text-gray-600 hover:text-black mr-2 transition"><i
                                        class="fas fa-edit"></i></button>

                                <?php if ($p['estado'] === 'Activo'): ?>
                                    <button
                                        onclick="deleteSupplier(<?php echo $p['idProveedor']; ?>,'<?php echo addslashes($p['razon_social']); ?>')"
                                        class="text-gray-500 hover:text-red-700 transition"><i class="fas fa-ban"></i></button>
                                <?php else: ?>
                                    <button
                                        onclick="activateSupplier(<?php echo $p['idProveedor']; ?>,'<?php echo addslashes($p['razon_social']); ?>')"
                                        class="text-gray-500 hover:text-green-700 transition"><i
                                            class="fas fa-check"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL VER -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Detalles del Proveedor</h3>
                <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Razón Social</label>
                    <p id="view_razon" class="text-lg font-semibold text-gray-900">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nombre Fantasía</label>
                    <p id="view_fantasia" class="text-base text-gray-800">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">CUIT</label>
                    <p id="view_cuit" class="text-base text-gray-800">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Condición IVA</label>
                    <p id="view_iva" class="text-base text-gray-800">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Teléfono</label>
                    <p id="view_telefono" class="text-base text-gray-800">-</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p id="view_email" class="text-base text-gray-800">-</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Dirección</label>
                    <p id="view_direccion"
                        class="text-base text-gray-800 bg-gray-50 p-3 rounded border border-gray-100">-
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeViewModal()"
                    class="bg-gray-800 text-white px-4 py-2 rounded-sm hover:bg-gray-900">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Editar Proveedor</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="idProveedor" id="edit_id">
                <div>
                    <label>Razón Social *</label>
                    <input name="razon_social" id="edit_razon" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Nombre Fantasía</label>
                    <input name="nombre_fantasia" id="edit_fantasia" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Condición IVA</label>
                    <select name="condicion_iva" id="edit_iva" class="w-full border rounded-lg px-3 py-2">
                        <option>Responsable Inscripto</option>
                        <option>Monotributista</option>
                        <option>Exento</option>
                        <option>No Responsable</option>
                    </select>
                </div>
                <div>
                    <label>Teléfono</label>
                    <input name="telefono" id="edit_telefono" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label>Email</label>
                    <input name="email" id="edit_email" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label>Dirección</label>
                    <textarea name="direccion" id="edit_direccion" rows="2"
                        class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-sm hover:bg-gray-50">Cancelar</button>
                    <button name="update_supplier"
                        class="bg-gray-900 text-white px-4 py-2 rounded-sm hover:bg-black">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DESACTIVAR -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Desactivar Proveedor</h3>
            <p>¿Desactivar a <strong id="delete_name"></strong>?</p>
            <form method="POST" class="flex gap-2 mt-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="delete_id">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-sm">Cancelar</button>
                <button name="delete_supplier"
                    class="flex-1 bg-gray-900 text-white px-4 py-2 rounded-sm hover:bg-red-700 transition">Desactivar</button>
            </form>
        </div>
    </div>

    <!-- MODAL ACTIVAR -->
    <div id="activateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Activar Proveedor</h3>
            <p>¿Activar a <strong id="activate_name"></strong>?</p>
            <form method="POST" class="flex gap-2 mt-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="activate_id">
                <button type="button" onclick="closeActivateModal()"
                    class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
                <button name="activate_supplier"
                    class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg">Activar</button>
            </form>
        </div>
    </div>

    <script>
        function toggleAddForm() {
            document.getElementById('addForm').classList.toggle('hidden');
        }

        function filterTable() {
            const f = document.getElementById('table_search').value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(r => {
                r.style.display = r.dataset.search.includes(f) ? '' : 'none';
            });
        }

        function viewSupplier(p) {
            document.getElementById('view_razon').textContent = p.razon_social;
            document.getElementById('view_fantasia').textContent = p.nombre_fantasia || '-';
            document.getElementById('view_cuit').textContent = p.cuit;
            document.getElementById('view_iva').textContent = p.condicion_iva;
            document.getElementById('view_telefono').textContent = p.telefono || '-';
            document.getElementById('view_email').textContent = p.email || '-';
            document.getElementById('view_direccion').textContent = p.direccion || '-';

            const modal = document.getElementById('viewModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeViewModal() {
            const modal = document.getElementById('viewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editSupplier(p) {
            edit_id.value = p.idProveedor;
            edit_razon.value = p.razon_social;
            edit_fantasia.value = p.nombre_fantasia || '';
            edit_iva.value = p.condicion_iva;
            edit_telefono.value = p.telefono || '';
            edit_email.value = p.email || '';
            edit_direccion.value = p.direccion || '';
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
        }

        function closeEditModal() {
            editModal.classList.add('hidden');
            editModal.classList.remove('flex');
        }

        function deleteSupplier(id, name) {
            delete_id.value = id;
            delete_name.textContent = name;
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }

        function activateSupplier(id, name) {
            activate_id.value = id;
            activate_name.textContent = name;
            activateModal.classList.remove('hidden');
            activateModal.classList.add('flex');
        }

        function closeActivateModal() {
            activateModal.classList.add('hidden');
            activateModal.classList.remove('flex');
        }
    </script>

</body>

</html>