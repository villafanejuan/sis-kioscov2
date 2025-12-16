<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../includes/customer_helper.php';
require_once __DIR__ . '/../includes/payment_helper.php';
checkSession();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $monto = floatval($_POST['monto']);
    $descripcion = $_POST['descripcion'] ?? '';

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
            throw new Exception("ERR_INSERT_PAGO");
        $pid = $pdo->lastInsertId();

        // 2. Saldo
        if (!updateCustomerBalance($cliente_id, $monto, 'pago'))
            throw new Exception("ERR_UPDATE_SALDO");

        // 3. Caja
        $cajaDesc = "Pago Cta. Cte. #$cliente_id - $descripcion";
        $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, usuario_id, fecha, created_at) VALUES (?, 'ingreso', ?, ?, ?, NOW(), NOW())");
        if (!$stmt->execute([$turno['id'], $monto, $cajaDesc, $_SESSION['user_id']]))
            throw new Exception("ERR_INSERT_CAJA");
        $mid = $pdo->lastInsertId();

        $pdo->commit();
        $message = "<div style='color:green; padding:20px; border:2px solid green; margin:20px;'>
            ✅ PAGO EXITOSO <br>
            Pago Ref: #$pid <br>
            Caja Ref: #$mid <br>
            <a href='diag.php'>Ver en Diagnóstico</a>
        </div>";

    } catch (Exception $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        $message = "<div style='color:red; padding:20px; border:2px solid red; margin:20px;'>ERROR: " . $e->getMessage() . "</div>";
    }
}

// Get Customers
$stmt = $pdo->query("SELECT id, nombre, saldo_cuenta FROM clientes ORDER BY nombre");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pago Express</title>
</head>

<body style="font-family:sans-serif; padding:40px;">
    <h1>Pago Express de Emergencia</h1>
    <?php echo $message; ?>

    <form method="POST" action="pay_test.php" style="background:#eee; padding:20px; width:400px;">
        <label>Cliente:</label><br>
        <select name="cliente_id" required style="width:100%; padding:10px; margin-bottom:10px;">
            <?php foreach ($clientes as $c): ?>
                <option value="<?php echo $c['id']; ?>">
                    <?php echo htmlspecialchars($c['nombre']); ?> ($<?php echo $c['saldo_cuenta']; ?>)
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Monto:</label><br>
        <input type="number" name="monto" step="0.01" required
            style="width:100%; padding:10px; margin-bottom:10px;"><br>

        <label>Descripción:</label><br>
        <input type="text" name="descripcion" placeholder="Opcional"
            style="width:100%; padding:10px; margin-bottom:10px;"><br>

        <button type="submit"
            style="background:green; color:white; padding:15px; width:100%; border:none; cursor:pointer; font-size:16px;">
            REGISTRAR PAGO AHORA
        </button>
    </form>
</body>

</html>