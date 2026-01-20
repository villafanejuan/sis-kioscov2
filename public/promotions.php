<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

// Permitir acceso a Admin y Kiosquero
if (!checkAdmin() && $_SESSION['role'] !== 'kiosquero') {
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
            if (isset($_POST['add_promo'])) {
                $nombre = sanitize($_POST['nombre']);
                $tipo = $_POST['tipo'];
                $valor = floatval($_POST['valor']);
                $valor_extra = sanitize($_POST['valor_extra'] ?? '');
                $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
                $fecha_inicio = $_POST['fecha_inicio'] ?: null;
                $fecha_fin = $_POST['fecha_fin'] ?: null;

                $stmt = $pdo->prepare("INSERT INTO promociones (nombre, tipo, valor, valor_extra, categoria_id, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $tipo, $valor, $valor_extra, $categoria_id, $fecha_inicio, $fecha_fin]);

                $promo_id = $pdo->lastInsertId();

                // Asociar productos
                if (!empty($_POST['productos'])) {
                    $stmt = $pdo->prepare("INSERT INTO promocion_productos (promocion_id, producto_id) VALUES (?, ?)");
                    foreach ($_POST['productos'] as $prod_id) {
                        $stmt->execute([$promo_id, $prod_id]);
                    }
                }

                $message = 'Promoción creada exitosamente';
                $messageType = 'success';
            } elseif (isset($_POST['toggle_promo'])) {
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("UPDATE promociones SET activo = NOT activo WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Estado actualizado';
                $messageType = 'success';
            } elseif (isset($_POST['delete_promo'])) {
                $id = intval($_POST['id']);
                $pdo->prepare("DELETE FROM promocion_productos WHERE promocion_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM promociones WHERE id = ?")->execute([$id]);
                $message = 'Promoción eliminada';
                $messageType = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Obtener promociones
$stmt = $pdo->query("SELECT p.*, c.nombre as categoria_nombre FROM promociones p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.activo DESC, p.created_at DESC");
$promociones = $stmt->fetchAll();

// Obtener categorías y productos
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
$productos = $pdo->query("SELECT * FROM productos WHERE deleted_at IS NULL ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-tags text-gray-700 mr-2"></i>Promociones
            </h1>
            <button onclick="toggleAddForm()"
                class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-sm transition shadow-sm">
                <i class="fas fa-plus mr-2"></i>Nueva Promoción
            </button>
        </div>

        <?php if ($message): ?>
            <div
                class="mb-4 p-4 rounded-sm border-l-4 <?php echo $messageType == 'success' ? 'bg-gray-100 border-gray-800 text-gray-800' : 'bg-red-50 border-red-800 text-red-900'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div id="addForm" class="hidden bg-white border border-gray-200 rounded-sm shadow-sm mb-6 p-6">
            <h3 class="text-xl font-bold mb-4">Nueva Promoción</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nombre *</label>
                        <input type="text" name="nombre" required
                            class="w-full px-3 py-2 border rounded-sm focus:ring-1 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipo *</label>
                        <select name="tipo" id="tipo" required onchange="updateFields()"
                            class="w-full px-3 py-2 border rounded-sm focus:ring-1 focus:ring-gray-500">
                            <option value="descuento_porcentaje">Descuento % (ej: 10% off)</option>
                            <option value="descuento_fijo">Descuento Fijo (ej: $100 off)</option>
                            <option value="nxm">NxM (ej: 2x1, 3x2)</option>
                            <option value="precio_especial">Precio Especial</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="valor_container">
                        <label class="block text-sm font-medium mb-1" id="valor_label">Valor *</label>
                        <input type="number" step="0.01" name="valor" id="valor" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1" id="valor_help">Ingresa el porcentaje de descuento</p>
                    </div>
                    <div id="nxm_container" class="hidden">
                        <label class="block text-sm font-medium mb-1">Formato NxM</label>
                        <input type="text" name="valor_extra" placeholder="ej: 2x1, 3x2"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">Llevas N, pagas M</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha Fin</label>
                        <input type="date" name="fecha_fin"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Aplicar a Categoría (opcional)</label>
                    <select name="categoria_id"
                        class="w-full px-3 py-2 border rounded-sm focus:ring-1 focus:ring-gray-500">
                        <option value="">Todos los productos seleccionados</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Productos (opcional - si no seleccionas, aplica a
                        categoría)</label>
                    <div class="border rounded-sm p-3 max-h-48 overflow-y-auto">
                        <?php foreach ($productos as $prod): ?>
                            <label class="flex items-center py-1 hover:bg-gray-50 px-2 rounded">
                                <input type="checkbox" name="productos[]" value="<?php echo $prod['id']; ?>" class="mr-2">
                                <span class="text-sm"><?php echo htmlspecialchars($prod['nombre']); ?> -
                                    $<?php echo number_format($prod['precio'], 2); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="toggleAddForm()"
                        class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-sm transition">Cancelar</button>
                    <button type="submit" name="add_promo"
                        class="bg-gray-900 hover:bg-black text-white px-4 py-2 rounded-sm transition">Crear
                        Promoción</button>
                </div>
            </form>
        </div>

        <!-- Lista de Promociones -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($promociones as $promo): ?>
                <div
                    class="bg-white border border-gray-200 rounded-sm shadow-sm p-5 <?php echo $promo['activo'] ? 'border-l-4 border-l-gray-800' : 'opacity-60 bg-gray-50'; ?>">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-lg text-gray-900"><?php echo htmlspecialchars($promo['nombre']); ?></h3>
                        <span
                            class="px-2 py-1 rounded-sm text-xs font-bold border <?php echo $promo['activo'] ? 'bg-gray-100 border-gray-300 text-gray-800' : 'bg-white border-gray-300 text-gray-500'; ?>">
                            <?php echo $promo['activo'] ? 'ACTIVA' : 'INACTIVA'; ?>
                        </span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <p><strong>Tipo:</strong>
                            <?php
                            $tipos = [
                                'descuento_porcentaje' => 'Descuento %',
                                'descuento_fijo' => 'Descuento Fijo',
                                'nxm' => 'NxM',
                                'precio_especial' => 'Precio Especial'
                            ];
                            echo $tipos[$promo['tipo']];
                            ?>
                        </p>
                        <p><strong>Valor:</strong>
                            <?php
                            if ($promo['tipo'] == 'descuento_porcentaje') {
                                echo $promo['valor'] . '%';
                            } elseif ($promo['tipo'] == 'nxm') {
                                echo $promo['valor_extra'];
                            } else {
                                echo '$' . number_format($promo['valor'], 2);
                            }
                            ?>
                        </p>
                        <?php if ($promo['categoria_nombre']): ?>
                            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($promo['categoria_nombre']); ?></p>
                        <?php endif; ?>
                        <?php if ($promo['fecha_inicio'] || $promo['fecha_fin']): ?>
                            <p><strong>Vigencia:</strong>
                                <?php echo $promo['fecha_inicio'] ? date('d/m/Y', strtotime($promo['fecha_inicio'])) : '∞'; ?>
                                -
                                <?php echo $promo['fecha_fin'] ? date('d/m/Y', strtotime($promo['fecha_fin'])) : '∞'; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <form method="POST" class="flex-1">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                            <button type="submit" name="toggle_promo"
                                class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1 rounded-sm text-sm transition">
                                <?php echo $promo['activo'] ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                        <form method="POST" onsubmit="return confirm('¿Eliminar promoción?')">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="id" value="<?php echo $promo['id']; ?>">
                            <button type="submit" name="delete_promo"
                                class="bg-white border border-gray-300 hover:bg-red-50 text-red-600 px-3 py-1 rounded-sm text-sm transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($promociones)): ?>
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No hay promociones creadas</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleAddForm() {
            document.getElementById('addForm').classList.toggle('hidden');
        }

        function updateFields() {
            const tipo = document.getElementById('tipo').value;
            const valorLabel = document.getElementById('valor_label');
            const valorHelp = document.getElementById('valor_help');
            const nxmContainer = document.getElementById('nxm_container');

            if (tipo === 'descuento_porcentaje') {
                valorLabel.textContent = 'Porcentaje *';
                valorHelp.textContent = 'Ingresa el porcentaje de descuento (ej: 10 para 10%)';
                nxmContainer.classList.add('hidden');
            } else if (tipo === 'descuento_fijo') {
                valorLabel.textContent = 'Monto Descuento *';
                valorHelp.textContent = 'Ingresa el monto fijo de descuento';
                nxmContainer.classList.add('hidden');
            } else if (tipo === 'nxm') {
                valorLabel.textContent = 'Cantidad N *';
                valorHelp.textContent = 'Cantidad que lleva el cliente';
                nxmContainer.classList.remove('hidden');
            } else if (tipo === 'precio_especial') {
                valorLabel.textContent = 'Precio Especial *';
                valorHelp.textContent = 'Nuevo precio del producto';
                nxmContainer.classList.add('hidden');
            }
        }
    </script>
</body>

</html>