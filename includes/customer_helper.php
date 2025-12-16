<?php
/**
 * Helper Functions para Gestión de Clientes
 * Funciones auxiliares para cuenta corriente y clientes
 */

/**
 * Obtiene el saldo actual de un cliente
 * @param int $cliente_id ID del cliente
 * @return float Saldo actual (positivo=a favor, negativo=deuda)
 */
function getCustomerBalance($cliente_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT saldo_cuenta FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? floatval($result['saldo_cuenta']) : 0;
}

/**
 * Verifica si un cliente puede comprar a crédito
 * @param int $cliente_id ID del cliente
 * @param float $monto Monto de la compra
 * @return array ['can_buy' => bool, 'message' => string, 'saldo_actual' => float, 'limite' => float]
 */
function canBuyOnCredit($cliente_id, $monto)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT saldo_cuenta, limite_credito, activo 
        FROM clientes 
        WHERE id = ?
    ");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        return [
            'can_buy' => false,
            'message' => 'Cliente no encontrado',
            'saldo_actual' => 0,
            'limite' => 0
        ];
    }

    if (!$cliente['activo']) {
        return [
            'can_buy' => false,
            'message' => 'Cliente inactivo',
            'saldo_actual' => floatval($cliente['saldo_cuenta']),
            'limite' => floatval($cliente['limite_credito'])
        ];
    }

    $saldo_actual = floatval($cliente['saldo_cuenta']);
    $limite = floatval($cliente['limite_credito']);

    // El saldo es negativo cuando debe
    $deuda_actual = abs(min(0, $saldo_actual));
    $nueva_deuda = $deuda_actual + $monto;

    /* 
    if ($nueva_deuda > $limite) {
        return [
            'can_buy' => false,
            'message' => 'Excede el límite de crédito. Deuda actual: $' . number_format($deuda_actual, 2) . ', Límite: $' . number_format($limite, 2),
            'saldo_actual' => $saldo_actual,
            'limite' => $limite
        ];
    }
    */

    return [
        'can_buy' => true,
        'message' => 'Puede comprar a crédito',
        'saldo_actual' => $saldo_actual,
        'limite' => $limite
    ];
}

/**
 * Obtiene el historial de compras de un cliente
 * @param int $cliente_id ID del cliente
 * @param int $limit Número máximo de registros
 * @return array Lista de ventas
 */
function getCustomerPurchases($cliente_id, $limit = 10)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT v.*, u.nombre as vendedor_nombre
        FROM ventas v
        LEFT JOIN usuarios u ON v.usuario_id = u.id
        WHERE v.cliente_id = ?
        ORDER BY v.fecha DESC
        LIMIT ?
    ");
    $stmt->execute([$cliente_id, $limit]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Registra un pago de un cliente
 * @param int $cliente_id ID del cliente
 * @param float $monto Monto del pago (positivo)
 * @param string $descripcion Descripción del pago
 * @param int $usuario_id ID del usuario que registra el pago
 * @return bool True si se registró correctamente
 */
function registerPayment($cliente_id, $monto, $descripcion = 'Pago a cuenta', $usuario_id = null)
{
    global $pdo;

    try {
        // Actualizar saldo (sumar porque es un pago)
        $stmt = $pdo->prepare("
            UPDATE clientes 
            SET saldo_cuenta = saldo_cuenta + ? 
            WHERE id = ?
        ");
        $stmt->execute([$monto, $cliente_id]);

        // TODO: Registrar en tabla de movimientos de cuenta corriente
        // Por ahora solo actualizamos el saldo

        return true;
    } catch (PDOException $e) {
        error_log("Error al registrar pago: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene información completa de un cliente
 * @param int $cliente_id ID del cliente
 * @return array|null Datos del cliente o null si no existe
 */
function getCustomerInfo($cliente_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$cliente_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obtiene todos los clientes activos
 * @return array Lista de clientes activos
 */
function getActiveCustomers()
{
    global $pdo;

    $stmt = $pdo->query("
        SELECT id, nombre, telefono, saldo_cuenta, limite_credito 
        FROM clientes 
        WHERE activo = 1 
        ORDER BY nombre
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Calcula el crédito disponible de un cliente
 * @param int $cliente_id ID del cliente
 * @return float Crédito disponible
 */
function getAvailableCredit($cliente_id)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT saldo_cuenta, limite_credito 
        FROM clientes 
        WHERE id = ?
    ");
    $stmt->execute([$cliente_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        return 0;
    }

    $saldo = floatval($cliente['saldo_cuenta']);
    $limite = floatval($cliente['limite_credito']);

    // Si el saldo es negativo, es deuda
    $deuda = abs(min(0, $saldo));

    return max(0, $limite - $deuda);
}

/**
 * Obtiene estadísticas de un cliente
 * @param int $cliente_id ID del cliente
 * @return array Estadísticas del cliente
 */
function getCustomerStats($cliente_id)
{
    global $pdo;

    // Total de compras
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_compras, SUM(total) as total_gastado
        FROM ventas
        WHERE cliente_id = ?
    ");
    $stmt->execute([$cliente_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Última compra
    $stmt = $pdo->prepare("
        SELECT fecha, total
        FROM ventas
        WHERE cliente_id = ?
        ORDER BY fecha DESC
        LIMIT 1
    ");
    $stmt->execute([$cliente_id]);
    $ultima_compra = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'total_compras' => intval($stats['total_compras'] ?? 0),
        'total_gastado' => floatval($stats['total_gastado'] ?? 0),
        'ultima_compra' => $ultima_compra,
        'promedio_compra' => $stats['total_compras'] > 0 ?
            floatval($stats['total_gastado']) / intval($stats['total_compras']) : 0
    ];
}
