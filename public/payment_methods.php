<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

if (!checkAdmin()) {
    header('Location: dashboard.php');
    exit;
}

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
            if (isset($_POST['add_method'])) {
                $nombre = sanitize($_POST['nombre']);
                $requiere_ref = isset($_POST['requiere_referencia']) ? 1 : 0;

                $stmt = $pdo->prepare("INSERT INTO metodos_pago (nombre, requiere_referencia) VALUES (?, ?)");
                $stmt->execute([$nombre, $requiere_ref]);

                $message = 'Método de pago agregado exitosamente';
                $messageType = 'success';
            } elseif (isset($_POST['update_method'])) {
                $id = intval($_POST['id']);
                $nombre = sanitize($_POST['nombre']);
                $requiere_ref = isset($_POST['requiere_referencia']) ? 1 : 0;

                $stmt = $pdo->prepare("UPDATE metodos_pago SET nombre = ?, requiere_referencia = ? WHERE id = ?");
                $stmt->execute([$nombre, $requiere_ref, $id]);

                $message = 'Método de pago actualizado';
                $messageType = 'success';
            } elseif (isset($_POST['toggle_method'])) {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE metodos_pago SET activo = NOT activo WHERE id = ?");
                $stmt->execute([$id]);

                $message = 'Estado actualizado';
                $messageType = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Obtener métodos de pago
$stmt = $pdo->query("SELECT * FROM metodos_pago ORDER BY activo DESC, nombre");
$metodos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métodos de Pago - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-credit-card text-green-600 mr-2"></i>Métodos de Pago
            </h1>
            <button onclick="toggleAddForm()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Nuevo Método
            </button>
        </div>

        <?php if ($message): ?>
            <div
                class="mb-4 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario Agregar -->
        <div id="addForm" class="hidden bg-white rounded-lg shadow-lg mb-6 p-6">
            <h3 class="text-xl font-bold mb-4">Nuevo Método de Pago</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="nombre" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
                        placeholder="Ej: MercadoPago, Efectivo, etc.">
                </div>

                <div>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="requiere_referencia" value="1"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-700">
                            Requiere número de referencia/transacción
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-6">
                        Activa esto para tarjetas, transferencias, etc.
                    </p>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="toggleAddForm()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</button>
                    <button type="submit" name="add_method"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Guardar</button>
                </div>
            </form>
        </div>

        <!-- Tabla de Métodos -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Método</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Requiere Ref.</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($metodos as $metodo): ?>
                        <tr class="<?php echo $metodo['activo'] ? 'hover:bg-gray-50' : 'bg-red-50'; ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i
                                        class="fas fa-<?php echo getPaymentIcon($metodo['nombre']); ?> text-2xl text-gray-400 mr-3"></i>
                                    <span class="font-semibold"><?php echo htmlspecialchars($metodo['nombre']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($metodo['requiere_referencia']): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check"></i> Sí
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                        <i class="fas fa-times"></i> No
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($metodo['activo']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        <i class="fas fa-times-circle"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="editMethod(<?php echo htmlspecialchars(json_encode($metodo)); ?>)"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $metodo['id']; ?>">
                                    <button type="submit" name="toggle_method"
                                        class="<?php echo $metodo['activo'] ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'; ?>">
                                        <i class="fas fa-<?php echo $metodo['activo'] ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Información -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                <div>
                    <h3 class="font-bold text-blue-800">Información sobre Métodos de Pago</h3>
                    <ul class="text-sm text-blue-700 mt-2 space-y-1">
                        <li>• Los métodos inactivos no aparecerán en el punto de venta</li>
                        <li>• Si un método requiere referencia, se pedirá el número de transacción al usarlo</li>
                        <li>• El método "Efectivo" se usa para calcular el arqueo de caja</li>
                        <li>• Puedes tener múltiples métodos activos para pagos mixtos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Editar Método de Pago</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="edit_id">

                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input type="text" name="nombre" id="edit_nombre" required
                        class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="requiere_referencia" id="edit_requiere" value="1"
                            class="w-4 h-4 text-green-600 border-gray-300 rounded">
                        <span class="text-sm font-medium text-gray-700">
                            Requiere número de referencia
                        </span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancelar</button>
                    <button type="submit" name="update_method"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAddForm() {
            document.getElementById('addForm').classList.toggle('hidden');
        }

        function editMethod(metodo) {
            document.getElementById('edit_id').value = metodo.id;
            document.getElementById('edit_nombre').value = metodo.nombre;
            document.getElementById('edit_requiere').checked = metodo.requiere_referencia == 1;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>
</body>

</html>

<?php
function getPaymentIcon($nombre)
{
    $nombre_lower = strtolower($nombre);
    if (strpos($nombre_lower, 'efectivo') !== false)
        return 'money-bill-wave';
    if (strpos($nombre_lower, 'tarjeta') !== false || strpos($nombre_lower, 'débito') !== false || strpos($nombre_lower, 'crédito') !== false)
        return 'credit-card';
    if (strpos($nombre_lower, 'transfer') !== false)
        return 'exchange-alt';
    if (strpos($nombre_lower, 'cuenta') !== false)
        return 'file-invoice-dollar';
    return 'dollar-sign';
}
?>