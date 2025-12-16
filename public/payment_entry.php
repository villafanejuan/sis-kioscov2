<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../includes/customer_helper.php';
require_once __DIR__ . '/../includes/payment_helper.php';
checkSession();

$currentPage = 'customers';
$message = '';
$messageType = '';

// Pre-fill from GET
$pre_cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $monto = floatval($_POST['monto']);
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($monto <= 0) {
        $message = "El monto debe ser mayor a 0.";
        $messageType = "error";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
            $stmt->execute([$_SESSION['user_id']]);
            $turno = $stmt->fetch();
            if (!$turno)
                throw new Exception("No tienes turno abierto");

            // 1. Cliente Pagos
            $stmt = $pdo->prepare("INSERT INTO cliente_pagos (cliente_id, monto, usuario_id, turno_id, descripcion, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
            if (!$stmt->execute([$cliente_id, $monto, $_SESSION['user_id'], $turno['id'], $descripcion]))
                throw new Exception("Error al guardar pago");
            $pid = $pdo->lastInsertId();

            // 2. Saldo
            if (!updateCustomerBalance($cliente_id, $monto, 'pago'))
                throw new Exception("Error al actualizar saldo");

            // 3. Caja
            $clienteInfo = getCustomerInfo($cliente_id);
            $nombreCliente = $clienteInfo ? $clienteInfo['nombre'] : "Cliente #$cliente_id";
            $cajaDesc = "Pago Cta. Cte. #$cliente_id $nombreCliente";
            if ($descripcion)
                $cajaDesc .= " - $descripcion";

            $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, usuario_id, fecha, created_at) VALUES (?, 'ingreso', ?, ?, ?, NOW(), NOW())");
            if (!$stmt->execute([$turno['id'], $monto, $cajaDesc, $_SESSION['user_id']]))
                throw new Exception("Error al guardar en caja");
            $mid = $pdo->lastInsertId();

            $pdo->commit();

            // Redirect back to customer account with success
            header("Location: customer_account.php?id=$cliente_id&payment_success=1&pid=$pid");
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction())
                $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Get Customers for Select (if needed)
$stmt = $pdo->query("SELECT id, nombre, saldo_cuenta FROM clientes ORDER BY nombre");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pago - Sistema Kiosco</title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-md mx-auto mt-10 px-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-green-600 px-6 py-4 flex items-center justify-between">
                <h1 class="text-white text-xl font-bold"><i class="fas fa-money-bill-wave mr-2"></i>Registrar Pago</h1>
                <a href="<?php echo $pre_cliente_id ? "customer_account.php?id=$pre_cliente_id" : "customers.php"; ?>"
                    class="text-green-100 hover:text-white"><i class="fas fa-times"></i></a>
            </div>

            <form method="POST" class="p-6">
                <?php if ($message): ?>
                    <div
                        class="mb-4 p-3 rounded <?php echo $messageType == 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Cliente</label>
                    <select name="cliente_id"
                        class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-green-500">
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $pre_cliente_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nombre']); ?>
                                ($<?php echo number_format($c['saldo_cuenta'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Monto a Abonar</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" name="monto" step="0.01" min="0.01" required autofocus
                            class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Nota / Descripción</label>
                    <input type="text" name="descripcion" placeholder="Opcional..."
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                </div>

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow transition transform hover:scale-105">
                    CONFIRMAR PAGO
                </button>
            </form>
        </div>
        <p class="text-center text-gray-500 text-sm mt-4">El pago se impactará inmediatamente en la Caja y Cuenta.</p>
    </div>
</body>

</html>