<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();
// Verificar acceso (Admin y Kiosquero pueden gestionar categorías)
if (!canAccess('categories')) {
    header('Location: dashboard.php');
    exit;
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar CSRF para todas las acciones
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error de seguridad. Inténtalo de nuevo.</div>';
    } else {
        if (isset($_POST['add_category'])) {
            $nombre = sanitize($_POST['nombre']);
            $descripcion = sanitize($_POST['descripcion']);

            if (empty($nombre)) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Nombre de categoría requerido.</div>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
                if ($stmt->execute([$nombre, $descripcion])) {
                    $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Categoría agregada exitosamente.</div>';
                } else {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al agregar categoría.</div>';
                }
            }
        } elseif (isset($_POST['update_category'])) {
            $id = intval($_POST['id']);
            $nombre = sanitize($_POST['nombre']);
            $descripcion = sanitize($_POST['descripcion']);

            if (empty($nombre) || $id <= 0) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Datos inválidos.</div>';
            } else {
                $stmt = $pdo->prepare("UPDATE categorias SET nombre=?, descripcion=? WHERE id=?");
                if ($stmt->execute([$nombre, $descripcion, $id])) {
                    $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Categoría actualizada exitosamente.</div>';
                } else {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al actualizar categoría.</div>';
                }
            }
        } elseif (isset($_POST['delete_category'])) {
            $id = intval($_POST['id']);
            if ($id > 0) {
                // Check if category has associated products
                $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = ?");
                $check_stmt->execute([$id]);
                $product_count = $check_stmt->fetchColumn();

                if ($product_count > 0) {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">No se puede eliminar la categoría porque tiene productos asociados.</div>';
                } else {
                    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id=?");
                    if ($stmt->execute([$id])) {
                        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Categoría eliminada exitosamente.</div>';
                    } else {
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al eliminar categoría.</div>';
                    }
                }
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">ID inválido.</div>';
            }
        }
    }
}

// Obtener categorías
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Categorías</h1>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                onclick="toggleAddCategoryForm()">
                <i class="fas fa-plus mr-2"></i>Agregar Categoría
            </button>
        </div>

        <?php echo $message; ?>

        <!-- Formulario Agregar Categoría -->
        <div id="addCategoryForm" class="bg-white p-6 rounded-lg shadow-md mb-6 hidden">
            <h3 class="text-xl font-semibold mb-4">Agregar Nueva Categoría</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la
                        Categoría</label>
                    <input type="text" id="nombre" name="nombre" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end space-x-2">
                    <button type="button" onclick="toggleAddCategoryForm()"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancelar</button>
                    <button type="submit" name="add_category"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Agregar
                        Categoría</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripción</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($categorias as $cat): ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($cat['id']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo htmlspecialchars($cat['nombre']); ?></td>
                            <td class="px-4 py-2">
                                <?php echo htmlspecialchars(substr($cat['descripcion'], 0, 50) . (strlen($cat['descripcion']) > 50 ? '...' : '')); ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <button
                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded mr-1"
                                    onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['nombre']); ?>', '<?php echo addslashes($cat['descripcion']); ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
                                    onclick="deleteCategory(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['nombre']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Editar Categoría -->
    <div id="editCategoryModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Editar Categoría</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" id="edit_id" name="id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" id="edit_nombre" name="nombre" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="edit_descripcion" name="descripcion" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button type="submit" name="update_category"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar Categoría -->
    </div>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" id="delete_id" name="id">
        <div class="mt-2 mb-4">
            <p>¿Estás seguro de que quieres eliminar la categoría <strong id="delete_name"></strong>?</p>
        </div>
        <div class="flex justify-end space-x-2">
            <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                onclick="closeDeleteModal()">Cancelar</button>
            <button type="submit" name="delete_category"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Eliminar</button>
        </div>
    </form>
    </div>
    </div>

    <script>
        function toggleAddCategoryForm() {
            document.getElementById('addCategoryForm').classList.toggle('hidden');
        }

        function editCategory(id, nombre, descripcion) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }

        function deleteCategory(id, nombre) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = nombre;
            document.getElementById('deleteCategoryModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteCategoryModal').classList.add('hidden');
        }
    </script>
</body>

</html>
```