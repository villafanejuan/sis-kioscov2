<?php
/**
 * Motor de Promociones
 * Lógica para aplicar promociones automáticamente
 */

/**
 * Obtiene todas las promociones activas según fecha y hora actual
 * @return array Lista de promociones activas
 */
function getActivePromotions()
{
    global $pdo;

    $now = date('Y-m-d H:i:s');
    $today = date('Y-m-d');
    $current_time = date('H:i:s');
    $day_of_week = date('N'); // 1=Lunes, 7=Domingo

    $stmt = $pdo->prepare("
        SELECT p.*, GROUP_CONCAT(pp.producto_id) as productos_ids
        FROM promociones p
        LEFT JOIN promocion_productos pp ON p.id = pp.promocion_id
        WHERE p.activo = 1
        AND (p.fecha_inicio IS NULL OR p.fecha_inicio <= ?)
        AND (p.fecha_fin IS NULL OR p.fecha_fin >= ?)
        AND (p.hora_inicio IS NULL OR p.hora_inicio <= ?)
        AND (p.hora_fin IS NULL OR p.hora_fin >= ?)
        GROUP BY p.id
        ORDER BY p.id
    ");
    $stmt->execute([$today, $today, $current_time, $current_time]);

    $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filtrar por día de la semana si está configurado
    $filtered = [];
    foreach ($promociones as $promo) {
        if ($promo['dias_semana']) {
            $dias = json_decode($promo['dias_semana'], true);
            if (is_array($dias) && !in_array($day_of_week, $dias)) {
                continue;
            }
        }

        // Convertir string de IDs a array
        if ($promo['productos_ids']) {
            $promo['productos_ids'] = array_map('intval', explode(',', $promo['productos_ids']));
        } else {
            $promo['productos_ids'] = [];
        }

        $filtered[] = $promo;
    }

    return $filtered;
}

/**
 * Aplica promociones al carrito y retorna los descuentos
 * @param array $carrito Carrito de compras
 * @return array ['descuentos' => array, 'subtotal' => float, 'total_descuento' => float, 'total_final' => float]
 */
function applyPromotions($carrito)
{
    global $pdo;

    if (empty($carrito)) {
        return [
            'descuentos' => [],
            'subtotal' => 0,
            'total_descuento' => 0,
            'total_final' => 0
        ];
    }

    $promociones = getActivePromotions();
    $descuentos = [];
    $productos_con_descuento = []; // Para evitar aplicar múltiples descuentos al mismo producto

    // Calcular subtotal
    $subtotal = 0;
    foreach ($carrito as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }

    // Aplicar cada promoción
    foreach ($promociones as $promo) {
        $descuento = applyPromotion($promo, $carrito, $productos_con_descuento);

        if ($descuento && $descuento['monto'] > 0) {
            $descuentos[] = $descuento;

            // Marcar productos como con descuento
            if (isset($descuento['productos_afectados'])) {
                foreach ($descuento['productos_afectados'] as $prod_id) {
                    $productos_con_descuento[] = $prod_id;
                }
            }
        }
    }

    // Calcular total de descuentos
    $total_descuento = 0;
    foreach ($descuentos as $desc) {
        $total_descuento += $desc['monto'];
    }

    $total_final = max(0, $subtotal - $total_descuento);

    return [
        'descuentos' => $descuentos,
        'subtotal' => $subtotal,
        'total_descuento' => $total_descuento,
        'total_final' => $total_final
    ];
}

/**
 * Aplica una promoción específica al carrito
 * @param array $promo Datos de la promoción
 * @param array $carrito Carrito de compras
 * @param array $productos_con_descuento IDs de productos que ya tienen descuento
 * @return array|null Descuento aplicado o null
 */
function applyPromotion($promo, $carrito, $productos_con_descuento)
{
    // Determinar productos aplicables
    $productos_aplicables = [];

    foreach ($carrito as $prod_id => $item) {
        // Si ya tiene descuento, saltar
        if (in_array($prod_id, $productos_con_descuento)) {
            continue;
        }

        // Verificar si el producto aplica para esta promoción
        $aplica = false;

        if (!empty($promo['productos_ids'])) {
            // Promoción específica para ciertos productos
            if (in_array($prod_id, $promo['productos_ids'])) {
                $aplica = true;
            }
        } elseif ($promo['categoria_id']) {
            // Promoción por categoría
            global $pdo;
            $stmt = $pdo->prepare("SELECT categoria_id FROM productos WHERE id = ?");
            $stmt->execute([$prod_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto && $producto['categoria_id'] == $promo['categoria_id']) {
                $aplica = true;
            }
        } else {
            // Promoción general (todos los productos)
            $aplica = true;
        }

        if ($aplica) {
            $productos_aplicables[$prod_id] = $item;
        }
    }

    if (empty($productos_aplicables)) {
        return null;
    }

    // Aplicar según el tipo de promoción
    switch ($promo['tipo']) {
        case 'descuento_porcentaje':
            return applyPercentageDiscount($promo, $productos_aplicables);

        case 'descuento_fijo':
            return applyFixedDiscount($promo, $productos_aplicables);

        case 'nxm':
            return applyNxMDiscount($promo, $productos_aplicables);

        case 'precio_especial':
            return applySpecialPrice($promo, $productos_aplicables);

        default:
            return null;
    }
}

/**
 * Aplica descuento porcentual
 */
function applyPercentageDiscount($promo, $productos)
{
    $monto_descuento = 0;
    $productos_afectados = [];

    foreach ($productos as $prod_id => $item) {
        $subtotal_producto = $item['precio'] * $item['cantidad'];
        $descuento_producto = $subtotal_producto * ($promo['valor'] / 100);
        $monto_descuento += $descuento_producto;
        $productos_afectados[] = $prod_id;
    }

    return [
        'promocion_id' => $promo['id'],
        'tipo' => 'descuento_porcentaje',
        'descripcion' => $promo['nombre'] . ' (' . $promo['valor'] . '% OFF)',
        'monto' => $monto_descuento,
        'productos_afectados' => $productos_afectados
    ];
}

/**
 * Aplica descuento fijo
 */
function applyFixedDiscount($promo, $productos)
{
    $productos_afectados = [];

    foreach ($productos as $prod_id => $item) {
        $productos_afectados[] = $prod_id;
    }

    return [
        'promocion_id' => $promo['id'],
        'tipo' => 'descuento_fijo',
        'descripcion' => $promo['nombre'] . ' (-$' . number_format($promo['valor'], 2) . ')',
        'monto' => floatval($promo['valor']),
        'productos_afectados' => $productos_afectados
    ];
}

/**
 * Aplica promoción NxM (ej: 2x1, 3x2)
 */
function applyNxMDiscount($promo, $productos)
{
    $monto_descuento = 0;
    $productos_afectados = [];

    // Parsear valor_extra (ej: "2x1" significa llevas 2, pagas 1)
    $nxm = explode('x', $promo['valor_extra']);
    if (count($nxm) != 2) {
        return null;
    }

    $llevas = intval($nxm[0]);
    $pagas = intval($nxm[1]);

    if ($llevas <= 0 || $pagas <= 0 || $pagas >= $llevas) {
        return null;
    }

    foreach ($productos as $prod_id => $item) {
        $cantidad = $item['cantidad'];
        $precio_unitario = $item['precio'];

        // Calcular cuántos sets completos hay
        $sets_completos = floor($cantidad / $llevas);
        $unidades_gratis = $sets_completos * ($llevas - $pagas);

        $descuento_producto = $unidades_gratis * $precio_unitario;
        $monto_descuento += $descuento_producto;
        $productos_afectados[] = $prod_id;
    }

    return [
        'promocion_id' => $promo['id'],
        'tipo' => 'nxm',
        'descripcion' => $promo['nombre'] . ' (' . $promo['valor_extra'] . ')',
        'monto' => $monto_descuento,
        'productos_afectados' => $productos_afectados
    ];
}

/**
 * Aplica precio especial
 */
function applySpecialPrice($promo, $productos)
{
    $monto_descuento = 0;
    $productos_afectados = [];

    $precio_especial = floatval($promo['valor']);

    foreach ($productos as $prod_id => $item) {
        $precio_original = $item['precio'];

        if ($precio_especial < $precio_original) {
            $descuento_unitario = $precio_original - $precio_especial;
            $descuento_producto = $descuento_unitario * $item['cantidad'];
            $monto_descuento += $descuento_producto;
            $productos_afectados[] = $prod_id;
        }
    }

    return [
        'promocion_id' => $promo['id'],
        'tipo' => 'precio_especial',
        'descripcion' => $promo['nombre'] . ' (Precio: $' . number_format($precio_especial, 2) . ')',
        'monto' => $monto_descuento,
        'productos_afectados' => $productos_afectados
    ];
}

/**
 * Guarda los descuentos aplicados en la base de datos
 * @param int $venta_id ID de la venta
 * @param array $descuentos Array de descuentos
 * @return bool True si se guardaron correctamente
 */
function saveDiscounts($venta_id, $descuentos)
{
    global $pdo;

    if (empty($descuentos)) {
        return true;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO venta_descuentos (venta_id, promocion_id, tipo, descripcion, monto_descuento) 
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($descuentos as $descuento) {
            $stmt->execute([
                $venta_id,
                $descuento['promocion_id'] ?? null,
                $descuento['tipo'],
                $descuento['descripcion'],
                $descuento['monto']
            ]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Error al guardar descuentos: " . $e->getMessage());
        return false;
    }
}
