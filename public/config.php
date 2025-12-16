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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Error de seguridad';
        $messageType = 'error';
    } else {
        try {
            $configs = [
                'negocio_nombre',
                'negocio_direccion',
                'negocio_telefono',
                'negocio_email',
                'ticket_mensaje',
                'ticket_auto_print'
            ];

            $stmt = $pdo->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
            
            foreach ($configs as $clave) {
                if (isset($_POST[$clave])) {
                    $valor = sanitize($_POST[$clave]);
                    $stmt->execute([$valor, $clave]);
                }
            }

            $message = 'Configuración actualizada exitosamente';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Obtener configuración actual
$stmt = $pdo->query("SELECT clave, valor FROM configuracion");
$config_rows = $stmt->fetchAll();
$config = [];
foreach ($config_rows as $row) {
    $config[$row['clave']] = $row['valor'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <i class="fas fa-cog text-4xl text-blue-600 mr-4"></i>
            <h1 class="text-3xl font-bold text-gray-800">Configuración del Sistema</h1>
        </div>

        <?php if ($message): ?>
            <div class="mb-4 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <!-- Datos del Negocio -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-store text-blue-600 mr-2"></i>
                        Datos del Negocio
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Negocio *
                            </label>
                            <input type="text" name="negocio_nombre" required
                                value="<?php echo htmlspecialchars($config['negocio_nombre'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono
                            </label>
                            <input type="text" name="negocio_telefono"
                                value="<?php echo htmlspecialchars($config['negocio_telefono'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email
                            </label>
                            <input type="email" name="negocio_email"
                                value="<?php echo htmlspecialchars($config['negocio_email'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Dirección
                            </label>
                            <input type="text" name="negocio_direccion"
                                value="<?php echo htmlspecialchars($config['negocio_direccion'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Configuración de Tickets -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-receipt text-green-600 mr-2"></i>
                        Configuración de Tickets
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Mensaje del Ticket
                            </label>
                            <input type="text" name="ticket_mensaje"
                                value="<?php echo htmlspecialchars($config['ticket_mensaje'] ?? '¡Gracias por su compra!'); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="¡Gracias por su compra!">
                            <p class="text-xs text-gray-500 mt-1">Este mensaje aparecerá al final de cada ticket</p>
                        </div>

                        <div>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="ticket_auto_print" value="1"
                                    <?php echo (isset($config['ticket_auto_print']) && $config['ticket_auto_print'] == '1') ? 'checked' : ''; ?>
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">
                                    Imprimir ticket automáticamente al completar venta
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-6">
                                Si está activado, se abrirá automáticamente la ventana de impresión
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Vista Previa del Ticket -->
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-eye text-purple-600 mr-2"></i>
                        Vista Previa del Ticket
                    </h2>

                    <div class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                        <div class="max-w-sm mx-auto bg-white p-4 shadow-lg font-mono text-xs">
                            <div class="text-center border-b border-gray-300 pb-2 mb-2">
                                <div class="font-bold text-sm"><?php echo strtoupper($config['negocio_nombre'] ?? 'MI KIOSCO'); ?></div>
                                <div class="text-xs"><?php echo $config['negocio_direccion'] ?? 'Dirección del negocio'; ?></div>
                                <div class="text-xs">Tel: <?php echo $config['negocio_telefono'] ?? '123-456-7890'; ?></div>
                                <div class="text-xs"><?php echo $config['negocio_email'] ?? 'email@ejemplo.com'; ?></div>
                            </div>
                            
                            <div class="border-b border-gray-300 pb-2 mb-2">
                                <div class="flex justify-between">
                                    <span>Ticket #:</span>
                                    <span class="font-bold">000123</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Fecha:</span>
                                    <span><?php echo date('d/m/Y H:i'); ?></span>
                                </div>
                            </div>

                            <div class="border-b border-gray-300 pb-2 mb-2">
                                <table class="w-full">
                                    <tr>
                                        <td>Coca Cola 500ml</td>
                                        <td class="text-right">2</td>
                                        <td class="text-right">$300.00</td>
                                    </tr>
                                    <tr>
                                        <td>Papas Lays</td>
                                        <td class="text-right">1</td>
                                        <td class="text-right">$100.00</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="border-b border-gray-300 pb-2 mb-2">
                                <div class="flex justify-between font-bold text-sm">
                                    <span>TOTAL:</span>
                                    <span>$400.00</span>
                                </div>
                            </div>

                            <div class="text-center text-xs mt-3">
                                <?php echo $config['ticket_mensaje'] ?? '¡Gracias por su compra!'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow transition">
                        <i class="fas fa-save mr-2"></i>Guardar Configuración
                    </button>
                    <a href="dashboard.php"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg shadow transition text-center">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
