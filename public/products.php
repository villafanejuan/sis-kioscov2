<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$is_admin = checkAdmin();
$can_manage_products = $is_admin || hasRole('kiosquero');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $can_manage_products) {
    // Verificar CSRF para todas las acciones
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error de seguridad. Inténtalo de nuevo.</div>';
    } else {
        if (isset($_POST['add_product'])) {
            $nombre = sanitize($_POST['nombre']);
            $descripcion = sanitize($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $stock = intval($_POST['stock']);
            $codigo_barra = sanitize($_POST['codigo_barra'] ?? '');
            $categoria_id = intval($_POST['categoria_id']);

            if (empty($nombre) || $precio <= 0 || $stock < 0) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Datos inválidos.</div>';
            } else {
                // Verificar código de barra único si se proporcionó
                $duplicate = false;
                if (!empty($codigo_barra)) {
                    $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo_barra = ?");
                    $stmt->execute([$codigo_barra]);
                    if ($stmt->fetch()) {
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">El código de barra ya existe.</div>';
                        $duplicate = true;
                    }
                }

                if (!$duplicate) {
                    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, codigo_barra) VALUES (?, ?, ?, ?, ?, ?)");
                    // Si codigo_barra es vacío, guardar como NULL
                    $barcodeToSave = empty($codigo_barra) ? null : $codigo_barra;
                    if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria_id, $barcodeToSave])) {
                        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Producto agregado exitosamente.</div>';
                    } else {
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al agregar producto.</div>';
                    }
                }
            }
        } elseif (isset($_POST['update_product'])) {
            $id = intval($_POST['id']);
            $nombre = sanitize($_POST['nombre']);
            $descripcion = sanitize($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $stock = intval($_POST['stock']);
            $codigo_barra = sanitize($_POST['codigo_barra'] ?? '');
            $categoria_id = intval($_POST['categoria_id']);

            if (empty($nombre) || $precio <= 0 || $stock < 0 || $id <= 0) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Datos inválidos.</div>';
            } else {
                // Verificar código de barra único (excluyendo el actual)
                $duplicate = false;
                if (!empty($codigo_barra)) {
                    $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo_barra = ? AND id != ?");
                    $stmt->execute([$codigo_barra, $id]);
                    if ($stmt->fetch()) {
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">El código de barra ya existe en otro producto.</div>';
                        $duplicate = true;
                    }
                }

                if (!$duplicate) {
                    $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=?, codigo_barra=? WHERE id=?");
                    $barcodeToSave = empty($codigo_barra) ? null : $codigo_barra;
                    if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria_id, $barcodeToSave, $id])) {
                        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Producto actualizado exitosamente.</div>';
                    } else {
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al actualizar producto.</div>';
                    }
                }
            }
        } elseif (isset($_POST['delete_product'])) {
            $id = intval($_POST['id']);
            if ($id > 0) {
                // Soft delete: marcar como eliminado en lugar de borrar físicamente
                $stmt = $pdo->prepare("UPDATE productos SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL");
                if ($stmt->execute([$id])) {
                    if ($stmt->rowCount() > 0) {
                        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Producto eliminado exitosamente.</div>';
                    } else {
                        $message = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">El producto ya ha sido eliminado.</div>';
                    }
                } else {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al eliminar producto.</div>';
                }
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">ID inválido.</div>';
            }
        } elseif (isset($_POST['restore_product'])) {
            // Solo administradores pueden restaurar productos
            if (!$is_admin) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">No tienes permisos para restaurar productos.</div>';
            } else {
                $id = intval($_POST['id']);
                if ($id > 0) {
                    $stmt = $pdo->prepare("UPDATE productos SET deleted_at = NULL WHERE id = ? AND deleted_at IS NOT NULL");
                    if ($stmt->execute([$id])) {
                        if ($stmt->rowCount() > 0) {
                            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Producto restaurado exitosamente.</div>';
                        } else {
                            $message = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">El producto no estaba eliminado.</div>';
                        }
                    } else {
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al restaurar producto.</div>';
                    }
                } else {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">ID inválido.</div>';
                }
            }
        }
    }
}

// Determinar si mostrar productos eliminados (solo para admins)
$show_deleted = isset($_GET['show_deleted']) && $_GET['show_deleted'] == '1' && $is_admin;

// Obtener productos
if ($show_deleted) {
    // Mostrar TODOS los productos (activos y eliminados) para admin
    $stmt = $pdo->query("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.deleted_at IS NULL DESC, p.nombre");
} else {
    // Solo mostrar productos activos
    $stmt = $pdo->query("SELECT p.*, c.nombre as categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.deleted_at IS NULL ORDER BY p.nombre");
}
$productos = $stmt->fetchAll();

// Obtener categorías
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema Kiosco</title>
    <script src="assets/js/tailwindcss.js"></script>
    <script src="assets/js/theme-config.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                <i class="fas fa-boxes text-gray-600 mr-2"></i>Gestión de Productos
            </h1>
            <div class="flex items-center gap-4">
                <?php if ($is_admin): ?>
                    <!-- Toggle para mostrar productos eliminados (solo admin) -->
                    <a href="?show_deleted=<?php echo $show_deleted ? '0' : '1'; ?>"
                        class="<?php echo $show_deleted ? 'bg-gray-600 hover:bg-gray-700' : 'bg-gray-800 hover:bg-gray-900'; ?> text-white font-semibold py-2 px-6 rounded-none shadow-none transition duration-200 ease-in-out flex items-center">
                        <i class="fas <?php echo $show_deleted ? 'fa-eye-slash' : 'fa-trash-restore'; ?> mr-2"></i>
                        <?php echo $show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados'; ?>
                    </a>
                <?php endif; ?>
                <?php if ($can_manage_products): ?>
                    <button
                        class="bg-gray-900 hover:bg-black text-white font-semibold py-2 px-6 rounded-none shadow-none transition duration-200 ease-in-out flex items-center"
                        onclick="toggleAddProductForm()">
                        <i class="fas fa-plus mr-2"></i>Agregar Producto
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Formulario Agregar Producto -->
        <div id="addProductForm"
            class="bg-white border border-gray-300 shadow-none mb-8 hidden overflow-hidden transition-all duration-300">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">Agregar Nuevo Producto</h3>
            </div>
            <div class="p-6">
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del
                            Producto</label>
                        <input type="text" id="nombre" name="nombre" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                    </div>
                    <div>
                        <label for="codigo_barra" class="block text-sm font-medium text-gray-700 mb-1">Código de
                            Barra
                            (Opcional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </div>
                            <input type="text" id="codigo_barra" name="codigo_barra"
                                class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                                placeholder="Escanear...">
                        </div>
                    </div>
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select id="categoria_id" name="categoria_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-white">
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                            <input type="number" step="0.01" id="precio" name="precio" required
                                class="w-full pl-7 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock
                            Inicial</label>
                        <input type="number" id="stock" name="stock" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="descripcion"
                            class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"></textarea>
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-3 mt-2">
                        <button type="button" onclick="toggleAddProductForm()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">Cancelar</button>
                        <button type="submit" name="add_product"
                            class="bg-gray-900 hover:bg-black text-white font-semibold py-2 px-6 rounded-none shadow-none transition duration-200">Guardar
                            Producto</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="bg-white border border-gray-300 shadow-none overflow-x-auto">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nombre</th>
                            <?php if ($show_deleted): ?>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                            <?php endif; ?>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                Descripción</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Categoría</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Precio</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Stock</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Código</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($productos as $prod): ?>
                            <?php $is_deleted = !empty($prod['deleted_at']); ?>
                            <tr
                                class="<?php echo $is_deleted ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50'; ?> transition duration-150 border-b border-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-bold <?php echo $is_deleted ? 'text-gray-500' : 'text-gray-900'; ?>">
                                        <?php echo htmlspecialchars($prod['nombre']); ?>
                                    </div>
                                </td>
                                <?php if ($is_admin && $show_deleted): ?>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($is_deleted): ?>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-trash mr-1"></i> ELIMINADO
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i> ACTIVO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="text-sm text-gray-500 truncate max-w-xs"
                                        title="<?php echo htmlspecialchars($prod['descripcion']); ?>">
                                        <?php echo htmlspecialchars($prod['descripcion']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold bg-gray-100 text-gray-800 border border-gray-300">
                                        <?php echo htmlspecialchars($prod['categoria'] ?? 'Sin categoría'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        $<?php echo number_format($prod['precio'], 2); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $stockClass = $prod['stock'] > 10 ? 'bg-green-100 text-green-800' : ($prod['stock'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                    ?>
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold <?php echo $stockClass; ?> border border-gray-200">
                                        <?php echo htmlspecialchars($prod['stock']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    <?php echo !empty($prod['codigo_barra']) ? '<span class="font-mono bg-gray-100 px-2 py-1 rounded"><i class="fas fa-barcode mr-1 text-gray-400"></i>' . htmlspecialchars($prod['codigo_barra']) . '</span>' : '<span class="text-gray-300">-</span>'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if ($is_deleted): ?>
                                        <!-- Producto eliminado: solo mostrar botón restaurar (admin) -->
                                        <button
                                            class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 p-2 rounded-lg transition"
                                            onclick="restoreProduct(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['nombre']); ?>')"
                                            title="Restaurar">
                                            <i class="fas fa-trash-restore"></i> Restaurar
                                        </button>
                                    <?php else: ?>
                                        <!-- Producto activo: mostrar botones normales -->
                                        <?php if ($can_manage_products): ?>
                                            <button
                                                class="text-gray-600 hover:text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 p-2 transition mr-2"
                                                onclick="editProduct(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['nombre']); ?>', '<?php echo addslashes($prod['descripcion']); ?>', <?php echo $prod['precio']; ?>, <?php echo $prod['stock']; ?>, <?php echo $prod['categoria_id']; ?>, '<?php echo addslashes($prod['codigo_barra'] ?? ''); ?>')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                class="text-red-600 hover:text-red-900 bg-white border border-gray-300 hover:bg-red-50 p-2 transition"
                                                onclick="deleteProduct(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['nombre']); ?>')"
                                                title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($productos)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-box-open text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg">No hay productos registrados.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Editar Producto (Custom Tailwind) -->
    <div id="editProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('editProductModal')"></div>
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Editar Producto
                            </h3>
                            <div class="mt-4">
                                <form method="POST" id="editForm">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" id="edit_id" name="id">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="edit_nombre"
                                                class="block text-sm font-medium text-gray-700">Nombre</label>
                                            <input type="text" id="edit_nombre" name="nombre" required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="edit_codigo_barra"
                                                class="block text-sm font-medium text-gray-700">Código de
                                                Barra</label>
                                            <input type="text" id="edit_codigo_barra" name="codigo_barra"
                                                placeholder="Opcional"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="edit_descripcion"
                                                class="block text-sm font-medium text-gray-700">Descripción</label>
                                            <textarea id="edit_descripcion" name="descripcion" rows="3"
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="edit_precio"
                                                    class="block text-sm font-medium text-gray-700">Precio</label>
                                                <div class="relative mt-1">
                                                    <span
                                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                                    <input type="number" step="0.01" id="edit_precio" name="precio"
                                                        required
                                                        class="block w-full pl-7 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                </div>
                                            </div>
                                            <div>
                                                <label for="edit_stock"
                                                    class="block text-sm font-medium text-gray-700">Stock</label>
                                                <input type="number" id="edit_stock" name="stock" required
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="edit_categoria_id"
                                                class="block text-sm font-medium text-gray-700">Categoría</label>
                                            <select id="edit_categoria_id" name="categoria_id" required
                                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                <option value="">Seleccionar categoría</option>
                                                <?php foreach ($categorias as $cat): ?>
                                                    <option value="<?php echo $cat['id']; ?>">
                                                        <?php echo $cat['nombre']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                        <button type="submit" name="update_product"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                                            Actualizar
                                        </button>
                                        <button type="button" onclick="closeModal('editProductModal')"
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

    <!-- Modal Eliminar Producto (Custom Tailwind) -->
    <div id="deleteProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('deleteProductModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Eliminar
                                Producto
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que quieres eliminar el producto <strong id="delete_name"
                                        class="text-gray-900"></strong>? Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" id="delete_id" name="id">
                        <button type="submit" name="delete_product"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" onclick="closeModal('deleteProductModal')"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Restaurar Producto (Solo Admin) -->
    <div id="restoreProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal('restoreProductModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-trash-restore text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Restaurar
                                Producto
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que quieres restaurar el producto <strong id="restore_name"
                                        class="text-gray-900"></strong>? El producto volverá a estar disponible.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" id="restore_id" name="id">
                        <button type="submit" name="restore_product"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Restaurar
                        </button>
                    </form>
                    <button type="button" onclick="closeModal('restoreProductModal')"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAddProductForm() {
            const form = document.getElementById('addProductForm');
            form.classList.toggle('hidden');
        }

        function editProduct(id, nombre, descripcion, precio, stock, categoria_id, codigo_barra) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_categoria_id').value = categoria_id;
            document.getElementById('edit_codigo_barra').value = codigo_barra;

            // Show modal
            document.getElementById('editProductModal').classList.remove('hidden');
        }

        function deleteProduct(id, nombre) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = nombre;

            // Show modal
            document.getElementById('deleteProductModal').classList.remove('hidden');
        }

        function restoreProduct(id, nombre) {
            document.getElementById('restore_id').value = id;
            document.getElementById('restore_name').textContent = nombre;

            // Show modal
            document.getElementById('restoreProductModal').classList.remove('hidden');
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