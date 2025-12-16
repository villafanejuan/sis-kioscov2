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
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                <i class="fas fa-tags text-blue-600 mr-2"></i>Gestión de Categorías
            </h1>
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:-translate-y-0.5 flex items-center"
                onclick="toggleAddCategoryForm()">
                <i class="fas fa-plus mr-2"></i>Agregar Categoría
            </button>
        </div>

        <?php echo $message; ?>

        <!-- Formulario Agregar Categoría -->
        <div id="addCategoryForm"
            class="bg-white rounded-xl shadow-lg mb-8 hidden overflow-hidden transition-all duration-300">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">Agregar Nueva Categoría</h3>
            </div>
            <div class="p-6">
                <form method="POST" class="grid grid-cols-1 gap-6">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la
                            Categoría</label>
                        <input type="text" id="nombre" name="nombre" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                    </div>
                    <div>
                        <label for="descripcion"
                            class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 mt-2">
                        <button type="button" onclick="toggleAddCategoryForm()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">Cancelar</button>
                        <button type="submit" name="add_category"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">Guardar
                            Categoría</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Categorías -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nombre</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Descripción</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($categorias as $cat): ?>
                            <tr class="hover:bg-blue-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($cat['id']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <div class="text-sm text-gray-500 truncate max-w-xs"
                                        title="<?php echo htmlspecialchars($cat['descripcion']); ?>">
                                        <?php echo htmlspecialchars($cat['descripcion']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 p-2 rounded-lg transition mr-2"
                                        onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['nombre']); ?>', '<?php echo addslashes($cat['descripcion']); ?>')"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-2 rounded-lg transition"
                                        onclick="deleteCategory(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['nombre']); ?>')"
                                        title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($categorias)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-tags text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg">No hay categorías registradas.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Editar Categoría -->
    <div id="editCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('editCategoryModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-edit text-yellow-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Editar Categoría
                            </h3>
                            <div class="mt-4">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" id="edit_id" name="id">
                                    <div class="mb-4">
                                        <label for="edit_nombre"
                                            class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                        <input type="text" id="edit_nombre" name="nombre" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                                    </div>
                                    <div class="mb-4">
                                        <label for="edit_descripcion"
                                            class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                        <textarea id="edit_descripcion" name="descripcion" rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"></textarea>
                                    </div>
                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                        <button type="submit" name="update_category"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                                            Actualizar
                                        </button>
                                        <button type="button" onclick="closeModal('editCategoryModal')"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Categoría -->
    <div id="deleteCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('deleteCategoryModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Eliminar Categoría
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que quieres eliminar la categoría <strong id="delete_name"
                                        class="text-gray-900"></strong>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" id="delete_id" name="id">
                        <button type="submit" name="delete_category"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" onclick="closeModal('deleteCategoryModal')"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
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

            // Show modal
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }

        function deleteCategory(id, nombre) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = nombre;

            // Show modal
            document.getElementById('deleteCategoryModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modals on Esc key
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
</body>

</html>