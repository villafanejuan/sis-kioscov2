<?php
/**
 * Helper Functions para Métodos de Pago
 * Funciones auxiliares para gestionar pagos múltiples
 */

/**
 * Obtiene todos los métodos de pago
 * @param bool $active_only Si es true, solo retorna métodos activos
 * @return array Lista de métodos de pago
 */
function getPaymentMethods($active_only = true)
{
    global $pdo;

    $sql = "SELECT * FROM metodos_pago";
    if ($active_only) {
        $sql .= " WHERE activo = 1";
    }
    $sql .= " ORDER BY nombre";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Valida que los pagos sumen el total requerido
 * @param array $payments Array de pagos [['metodo_id' => 1, 'monto' => 100], ...]
 * @param float $total Total a pagar
 * @return array ['valid' => bool, 'message' => string, 'suma' => float]
 */
function validatePayments($payments, $total)
{
    if (empty($payments)) {
        return [
            'valid' => false,
            'message' => 'Debe agregar al menos un método de pago',
            'suma' => 0
        ];
    }

    $suma = 0;
    foreach ($payments as $payment) {
        if (!isset($payment['monto']) || $payment['monto'] <= 0) {
            return [
                'valid' => false,
                'message' => 'Todos los montos deben ser mayores a 0',
                'suma' => $suma
            ];
        }
        $suma += floatval($payment['monto']);
    }

    if ($suma < $total) {
        return [
            'valid' => false,
            'message' => 'El total de pagos ($' . number_format($suma, 2) . ') es menor al total de la venta ($' . number_format($total, 2) . ')',
            'suma' => $suma
        ];
    }

    return [
        'valid' => true,
        'message' => 'Pagos válidos',
        'suma' => $suma
    ];
}

/**
 * Guarda los pagos de una venta en la base de datos
 * @param int $venta_id ID de la venta
 * @param array $payments Array de pagos
 * @return bool True si se guardaron correctamente
 */
function savePayments($venta_id, $payments)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO venta_pagos (venta_id, metodo_pago_id, monto, referencia) 
            VALUES (?, ?, ?, ?)
        ");

        foreach ($payments as $payment) {
            $stmt->execute([
                $venta_id,
                $payment['metodo_id'],
                $payment['monto'],
                $payment['referencia'] ?? null
            ]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Error al guardar pagos: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualiza el saldo de cuenta corriente de un cliente
 * @param int $cliente_id ID del cliente
 * @param float $monto Monto a sumar/restar (positivo=pago, negativo=compra)
 * @param string $tipo Tipo de movimiento: 'venta', 'pago', 'ajuste'
 * @param int $venta_id ID de la venta relacionada (opcional)
 * @return bool True si se actualizó correctamente
 */
function updateCustomerBalance($cliente_id, $monto, $tipo = 'venta', $venta_id = null)
{
    global $pdo;

    try {
        // Actualizar saldo del cliente
        $stmt = $pdo->prepare("
            UPDATE clientes 
            SET saldo_cuenta = COALESCE(saldo_cuenta, 0) + ? 
            WHERE id = ?
        ");
        $stmt->execute([$monto, $cliente_id]);

        // Registrar movimiento en tabla de movimientos (si existe)
        // Por ahora solo actualizamos el saldo

        return true;
    } catch (PDOException $e) {
        error_log("Error al actualizar saldo del cliente: " . $e->getMessage());
        return false;
    }
}

/**
 * Calcula el monto en efectivo de los pagos (para caja)
 * @param array $payments Array de pagos
 * @return float Monto total en efectivo
 */
function getCashAmount($payments)
{
    global $pdo;

    $cash_amount = 0;

    // Obtener el ID del método "Efectivo"
    $stmt = $pdo->query("SELECT id FROM metodos_pago WHERE nombre = 'Efectivo' LIMIT 1");
    $efectivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$efectivo) {
        return 0;
    }

    $efectivo_id = $efectivo['id'];

    foreach ($payments as $payment) {
        if ($payment['metodo_id'] == $efectivo_id) {
            $cash_amount += floatval($payment['monto']);
        }
    }

    return $cash_amount;
}

/**
 * Obtiene el nombre de un método de pago por su ID
 * @param int $metodo_id ID del método de pago
 * @return string Nombre del método
 */
function getPaymentMethodName($metodo_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT nombre FROM metodos_pago WHERE id = ?");
    $stmt->execute([$metodo_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['nombre'] : 'Desconocido';
}

/**
 * Verifica si un método de pago requiere referencia
 * @param int $metodo_id ID del método de pago
 * @return bool True si requiere referencia
 */
function requiresReference($metodo_id)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT requiere_referencia FROM metodos_pago WHERE id = ?");
    $stmt->execute([$metodo_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? (bool) $result['requiere_referencia'] : false;
}
