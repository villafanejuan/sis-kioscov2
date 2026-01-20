<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../includes/payment_helper.php';
require_once __DIR__ . '/../includes/customer_helper.php';
require_once __DIR__ . '/../includes/promotions_engine.php';
checkSession();

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$is_ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

$is_ajax = isset($_POST['ajax']) && $_POST['ajax'] == '1';

// Verificar si hay turno abierto
$stmt = $pdo->prepare("SELECT id FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
$stmt->execute([$_SESSION['user_id']]);
$turnoAbierto = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$turnoAbierto) {
    $error_msg = 'Debes abrir un turno de caja para realizar ventas.';
    if ($is_ajax) {
        echo json_encode(['success' => false, 'message' => $error_msg]);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $turnoAbierto) {
    // Verificar CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_msg = 'Error de seguridad. Inténtalo de nuevo.';
        if ($is_ajax) {
            echo json_encode(['success' => false, 'message' => $error_msg]);
            exit;
        }
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . $error_msg . '</div>';
    } else {
        if (isset($_POST['add_to_cart'])) {
            $producto_id = intval($_POST['producto_id']);
            $cantidad = intval($_POST['cantidad']);

            if ($producto_id <= 0 || $cantidad <= 0) {
                $error_msg = 'Datos inválidos.';
                if ($is_ajax) {
                    echo json_encode(['success' => false, 'message' => $error_msg]);
                    exit;
                }
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . $error_msg . '</div>';
            } else {
                $stmt = $pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch();

                if ($producto) {
                    $proposed_cantidad = $cantidad;
                    if (isset($carrito[$producto_id])) {
                        $proposed_cantidad += $carrito[$producto_id]['cantidad'];
                    }
                    if ($proposed_cantidad > $producto['stock']) {
                        $error_msg = 'No se puede agregar más cantidad de la disponible en stock.';
                        if ($is_ajax) {
                            echo json_encode(['success' => false, 'message' => $error_msg]);
                            exit;
                        }
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . $error_msg . '</div>';
                    } else {
                        if (isset($carrito[$producto_id])) {
                            $carrito[$producto_id]['cantidad'] += $cantidad;
                        } else {
                            $carrito[$producto_id] = [
                                'id' => $producto['id'],
                                'nombre' => $producto['nombre'],
                                'precio' => $producto['precio'],
                                'cantidad' => $cantidad
                            ];
                        }
                        $_SESSION['carrito'] = $carrito;
                        $success_msg = 'Producto agregado al carrito.';
                        if ($is_ajax) {
                            $calc = applyPromotions($carrito);
                            // Get updated product stock
                            $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                            $stmt->execute([$producto_id]);
                            $updated_product = $stmt->fetch();
                            echo json_encode([
                                'success' => true,
                                'message' => $success_msg,
                                'carrito' => $carrito,
                                'total_carrito' => $calc['total_final'],
                                'updated_stock' => $updated_product ? $updated_product['stock'] : $producto['stock']
                            ]);
                            exit;
                        }
                        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . $success_msg . '</div>';
                    }
                } else {
                    $error_msg = 'Producto no encontrado.';
                    if ($is_ajax) {
                        echo json_encode(['success' => false, 'message' => $error_msg]);
                        exit;
                    }
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . $error_msg . '</div>';
                }
            }
        } elseif (isset($_POST['add_manual_to_cart'])) {
            $monto = floatval($_POST['monto']);
            $descripcion = trim($_POST['descripcion']);
            $cantidad = intval($_POST['cantidad']) > 0 ? intval($_POST['cantidad']) : 1;

            if ($monto <= 0) {
                echo json_encode(['success' => false, 'message' => 'El monto debe ser mayor a 0.']);
                exit;
            }

            if (empty($descripcion)) {
                $descripcion = 'Varios';
            }

            // Usamos un ID negativo o con prefijo para diferenciar
            // Pero como la key del array suele ser ID, usaremos un string único
            $manual_id = 'manual_' . time() . '_' . rand(100, 999);

            $carrito[$manual_id] = [
                'id' => $manual_id,
                'nombre' => $descripcion,
                'precio' => $monto,
                'cantidad' => $cantidad,
                'is_manual' => true
            ];

            $_SESSION['carrito'] = $carrito;

            // Recalcular
            $calc = applyPromotions($carrito);

            echo json_encode([
                'success' => true,
                'message' => 'Item manual agregado.',
                'carrito' => $carrito,
                'total_carrito' => $calc['total_final']
            ]);
            exit;
        } elseif (isset($_POST['add_promo_to_cart'])) {
            $promo_id = intval($_POST['promocion_id']);
            $stmt = $pdo->prepare("SELECT * FROM promociones WHERE id = ?");
            $stmt->execute([$promo_id]);
            $promo = $stmt->fetch();

            if ($promo) {
                // Obtener productos de la promo
                $stmt = $pdo->prepare("SELECT producto_id FROM promocion_productos WHERE promocion_id = ?");
                $stmt->execute([$promo_id]);
                $prods = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $added_text = [];

                foreach ($prods as $pid) {
                    // Lógica simplificada de agregar (asumiendo stock)
                    $stmt = $pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
                    $stmt->execute([$pid]);
                    $prod = $stmt->fetch();

                    if ($prod && $prod['stock'] > 0) {
                        if (isset($carrito[$pid])) {
                            $carrito[$pid]['cantidad']++;
                        } else {
                            $carrito[$pid] = [
                                'id' => $prod['id'],
                                'nombre' => $prod['nombre'],
                                'precio' => $prod['precio'],
                                'cantidad' => 1
                            ];
                        }
                        $added_text[] = $prod['nombre'];
                    }
                }

                $_SESSION['carrito'] = $carrito;

                // Recalcular con motor de promociones
                $calc = applyPromotions($carrito);

                if (!empty($added_text)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Promo agregada: ' . implode(', ', $added_text),
                        'carrito' => $carrito,
                        'total_carrito' => $calc['total_final']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudieron agregar los productos (Stock o ID inválido)']);
                }
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Promoción no encontrada']);
                exit;
            }

        } elseif (isset($_POST['scan_barcode'])) {
            $barcode = sanitize($_POST['barcode']);

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Código vacío.']);
                exit;
            }

            // Buscar producto por código de barra
            $stmt = $pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE codigo_barra = ?");
            $stmt->execute([$barcode]);
            $producto = $stmt->fetch();

            if ($producto) {
                // Lógica idéntica a agregar al carrito
                $producto_id = $producto['id'];
                $cantidad = 1; // Escaneo siempre suma 1

                if ($producto['stock'] < 1) {
                    echo json_encode(['success' => false, 'message' => 'Producto sin stock.']);
                    exit;
                }

                if (isset($carrito[$producto_id])) {
                    if ($carrito[$producto_id]['cantidad'] + 1 > $producto['stock']) {
                        echo json_encode(['success' => false, 'message' => 'Stock insuficiente.']);
                        exit;
                    }
                    $carrito[$producto_id]['cantidad']++;
                } else {
                    $carrito[$producto_id] = [
                        'id' => $producto['id'],
                        'nombre' => $producto['nombre'],
                        'precio' => $producto['precio'],
                        'cantidad' => 1
                    ];
                }

                $_SESSION['carrito'] = $carrito;

                // Return updated state
                $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $updated_product = $stmt->fetch();

                $calc = applyPromotions($carrito);

                echo json_encode([
                    'success' => true,
                    'message' => 'Producto agregado: ' . $producto['nombre'],
                    'carrito' => $carrito,
                    'total_carrito' => $calc['total_final'],
                    'updated_stock' => $updated_product ? $updated_product['stock'] : $producto['stock'],
                    'added_product_id' => $producto_id
                ]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado (Código: ' . $barcode . ')']);
                exit;
            }
        } elseif (isset($_POST['remove_from_cart'])) {
            $producto_id = $_POST['producto_id'];
            // Permitir IDs manuales (strings) o numéricos
            if ($producto_id) {
                unset($carrito[$producto_id]);
                $_SESSION['carrito'] = $carrito;
                if ($is_ajax) {
                    $calc = applyPromotions($carrito);
                    echo json_encode([
                        'success' => true,
                        'carrito' => $carrito,
                        'total_carrito' => $calc['total_final']
                    ]);
                    exit;
                }
            }
        } elseif (isset($_POST['update_quantity'])) {
            $producto_id = $_POST['producto_id'];
            $cantidad = intval($_POST['cantidad']);

            if ($producto_id) {
                // Verificar si es manual
                $is_manual = strpos($producto_id, 'manual_') === 0;

                if ($is_manual) {
                    // Item manual: Sin stock check
                    if ($cantidad > 0) {
                        $carrito[$producto_id]['cantidad'] = $cantidad;
                    } else {
                        unset($carrito[$producto_id]);
                    }
                    $_SESSION['carrito'] = $carrito;
                    if ($is_ajax) {
                        $calc = applyPromotions($carrito);
                        echo json_encode([
                            'success' => true,
                            'carrito' => $carrito,
                            'total_carrito' => $calc['total_final']
                        ]);
                        exit;
                    }
                } else {
                    // Item normal: Check Stock
                    $pid_int = intval($producto_id);
                    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                    $stmt->execute([$pid_int]);
                    $producto = $stmt->fetch();

                    if ($producto && $cantidad <= $producto['stock']) {
                        if ($cantidad > 0) {
                            $carrito[$pid_int]['cantidad'] = $cantidad;
                        } else {
                            unset($carrito[$pid_int]);
                        }
                        $_SESSION['carrito'] = $carrito;
                        if ($is_ajax) {
                            $calc = applyPromotions($carrito);
                            echo json_encode([
                                'success' => true,
                                'carrito' => $carrito,
                                'total_carrito' => $calc['total_final']
                            ]);
                            exit;
                        }
                    } else {
                        $error_msg = 'La cantidad solicitada excede el stock disponible.';
                        if ($is_ajax) {
                            echo json_encode(['success' => false, 'message' => $error_msg]);
                            exit;
                        }
                        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . $error_msg . '</div>';
                    }
                }
            }

        } elseif (isset($_POST['clear_cart'])) {
            unset($_SESSION['carrito']);
            $carrito = [];
            $success_msg = 'Carrito limpiado.';
            if ($is_ajax) {
                echo json_encode(['success' => true, 'message' => $success_msg, 'carrito' => $carrito, 'total_carrito' => 0]);
                exit;
            }
            $message = '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">' . $success_msg . '</div>';
        } elseif (isset($_POST['complete_sale'])) {
            if (!empty($carrito)) {
                // Calcular totales con motor de promociones
                $calc = applyPromotions($carrito);
                $subtotal = $calc['subtotal'];
                $descuento_total = $calc['total_descuento'];
                $total = $calc['total_final'];
                $descuentos_aplicados = $calc['descuentos'];

                $stock_insufficient = false;
                foreach ($carrito as $item) {
                    // Si es item manual, no verificamos stock en BD
                    if (isset($item['is_manual']) && $item['is_manual']) {
                        continue;
                    }

                    // Verificar stock actual antes de vender
                    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                    $stmt->execute([$item['id']]);
                    $producto = $stmt->fetch();
                    if (!$producto || $producto['stock'] < $item['cantidad']) {
                        $stock_insufficient = true;
                        break;
                    }
                }

                if ($stock_insufficient) {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Stock insuficiente para completar la venta. Actualice el carrito.</div>';
                } else {
                    // Obtener cliente y método de pago
                    $cliente_id = !empty($_POST['cliente_id']) ? intval($_POST['cliente_id']) : null;
                    $metodo_pago = sanitize($_POST['metodo_pago'] ?? 'efectivo');
                    $amount_paid = floatval($_POST['amount_paid'] ?? 0);

                    // Si es cuenta corriente, validar que haya cliente
                    if ($metodo_pago === 'cuenta_corriente') {
                        if (!$cliente_id) {
                            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Debes seleccionar un cliente para vender a crédito.</div>';
                        } else {
                            // Venta a crédito (Posiblemente parcial)
                            // $amount_paid viene del POST, no lo forzamos a 0
                            $change = 0; // En crédito, si paga de más, sería extraño, asumimos cambio 0 o se ajusta

                            $debt_amount = $total - $amount_paid;
                            if ($debt_amount < 0)
                                $debt_amount = 0;

                            // Insertar venta 
                            $stmt = $pdo->prepare("INSERT INTO ventas (usuario_id, cliente_id, subtotal, descuento_total, total, monto_pagado, cambio) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$_SESSION['user_id'], $cliente_id, $subtotal, $descuento_total, $total, $amount_paid, $change]);
                            $venta_id = $pdo->lastInsertId();

                            // Guardar Descuentos
                            if (!empty($descuentos_aplicados)) {
                                saveDiscounts($venta_id, $descuentos_aplicados);
                            }

                            // Insertar detalles de venta y actualizar stock
                            foreach ($carrito as $item) {
                                if (isset($item['is_manual']) && $item['is_manual']) {
                                    // Item Manual: No hay producto_id, guardamos descripción, NO tocamos stock
                                    $stmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio, subtotal, descripcion) VALUES (?, NULL, ?, ?, ?, ?)");
                                    $stmt->execute([$venta_id, $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad'], $item['nombre']]);
                                } else {
                                    // Item Normal
                                    $stmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio, subtotal, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
                                    $stmt->execute([$venta_id, $item['id'], $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad'], $item['nombre']]);

                                    $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
                                    $stmt->execute([$item['cantidad'], $item['id']]);
                                }
                            }

                            // 1. Actualizar saldo del cliente (SOLO LA DEUDA)
                            if ($debt_amount > 0) {
                                error_log("Actualizando balance del cliente $cliente_id con deuda: -$debt_amount");
                                updateCustomerBalance($cliente_id, -$debt_amount, 'venta', $venta_id);
                            }

                            // 2. Registrar pagos (Split)

                            // A. Parte Credito
                            if ($debt_amount > 0) {
                                $stmt = $pdo->prepare("INSERT INTO venta_pagos (venta_id, metodo_pago_id, monto) VALUES (?, (SELECT id FROM metodos_pago WHERE nombre = 'Cuenta Corriente' LIMIT 1), ?)");
                                $stmt->execute([$venta_id, $debt_amount]);
                            }
                            // B. Parte Efectivo (Entrega)
                            if ($amount_paid > 0) {
                                $stmt = $pdo->prepare("INSERT INTO venta_pagos (venta_id, metodo_pago_id, monto) VALUES (?, (SELECT id FROM metodos_pago WHERE nombre = 'Efectivo' LIMIT 1), ?)");
                                $stmt->execute([$venta_id, $amount_paid]);
                            }

                            // 3. Registrar movimiento en caja
                            // Si hubo entrega de dinero, se registra ese monto. Si fue todo fiado (0 entrega), se registra 0 informativo.
                            if (isset($turnoAbierto) && $turnoAbierto) {
                                $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, venta_id, created_at, usuario_id, fecha) VALUES (?, 'venta', ?, ?, ?, NOW(), ?, NOW())");

                                $desc = 'Venta Cta. Cte. #' . $venta_id . ' (Total: $' . number_format($total, 2) . ')';
                                if ($amount_paid > 0) {
                                    $desc .= " - Entrega: $" . number_format($amount_paid, 2);
                                }

                                $stmt->execute([$turnoAbierto['id'], $amount_paid, $desc, $venta_id, $_SESSION['user_id']]);
                            }

                            // Limpiar carrito y cliente
                            unset($_SESSION['carrito']);
                            unset($_SESSION['selected_customer']);
                            $carrito = [];

                            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Venta a crédito completada. Total: $' . number_format($total, 2) . '. <a href="print_ticket.php?id=' . $venta_id . '" target="_blank" class="underline font-bold">Imprimir Ticket</a> | <a href="customer_account.php?id=' . $cliente_id . '" target="_blank" class="underline font-bold">Ver Cuenta</a></div>';
                        }
                    } elseif ($metodo_pago === 'transferencia') {
                        $referencia = trim($_POST['transfer_reference'] ?? '');
                        if (empty($referencia)) {
                            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Debes ingresar el nombre del remitente para la transferencia.</div>';
                        } else {
                            // Venta por Transferencia / QR
                            $amount_paid = $total; // Asumimos pago completo
                            $change = 0;

                            // Insertar venta 
                            $stmt = $pdo->prepare("INSERT INTO ventas (usuario_id, cliente_id, subtotal, descuento_total, total, monto_pagado, cambio) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$_SESSION['user_id'], $cliente_id, $subtotal, $descuento_total, $total, $amount_paid, $change]);
                            $venta_id = $pdo->lastInsertId();

                            // Guardar Descuentos
                            if (!empty($descuentos_aplicados)) {
                                saveDiscounts($venta_id, $descuentos_aplicados);
                            }

                            // Insertar detalles
                            foreach ($carrito as $item) {
                                if (isset($item['is_manual']) && $item['is_manual']) {
                                    // Item Manual
                                    $stmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio, subtotal, descripcion) VALUES (?, NULL, ?, ?, ?, ?)");
                                    $stmt->execute([$venta_id, $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad'], $item['nombre']]);
                                } else {
                                    // Item Normal
                                    $stmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio, subtotal, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
                                    $stmt->execute([$venta_id, $item['id'], $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad'], $item['nombre']]);

                                    $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
                                    $stmt->execute([$item['cantidad'], $item['id']]);
                                }
                            }

                            // Registrar Pago Transferencia (ID 4 o busca por nombre)
                            $stmt = $pdo->prepare("SELECT id FROM metodos_pago WHERE nombre = 'Transferencia' LIMIT 1");
                            $mp = $stmt->fetch();
                            $mp_id = $mp ? $mp['id'] : 4;

                            $telefono = trim($_POST['transfer_phone'] ?? '');

                            $stmt = $pdo->prepare("INSERT INTO venta_pagos (venta_id, metodo_pago_id, monto, referencia, telefono) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$venta_id, $mp_id, $total, $referencia, $telefono]);

                            // Movimiento en Caja: Monto 0 para NO afectar arqueo físico, pero registrado visualmente
                            // La descripción incluye el monto real para referencia visual
                            if (isset($turnoAbierto) && $turnoAbierto) {
                                $desc = "Transferencia ($" . number_format($total, 2) . ") - Ref: $referencia";
                                if (!empty($telefono)) {
                                    $desc .= " (Tel: $telefono)";
                                }
                                // Tipo 'venta' para que salga en listados, monto 0
                                $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, venta_id, created_at, usuario_id, fecha) VALUES (?, 'venta', 0, ?, ?, NOW(), ?, NOW())");
                                $stmt->execute([$turnoAbierto['id'], $desc, $venta_id, $_SESSION['user_id']]);
                            }

                            // Limpiar
                            unset($_SESSION['carrito']);
                            unset($_SESSION['selected_customer']);
                            $carrito = [];

                            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Transferencia registrada exitosamente. <a href="print_ticket.php?id=' . $venta_id . '" target="_blank" class="underline font-bold">Imprimir Ticket</a></div>';
                        }
                    } else {
                        // Venta en efectivo u otro método
                        if ($amount_paid < $total) {
                            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">El monto pagado debe ser mayor o igual al total.</div>';
                        } else {
                            $change = $amount_paid - $total;

                            // Insertar venta (Usando Subtotal, Descuento, Total)
                            $stmt = $pdo->prepare("INSERT INTO ventas (usuario_id, cliente_id, subtotal, descuento_total, total, monto_pagado, cambio) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$_SESSION['user_id'], $cliente_id, $subtotal, $descuento_total, $total, $amount_paid, $change]);
                            $venta_id = $pdo->lastInsertId();

                            // Guardar Descuentos
                            if (!empty($descuentos_aplicados)) {
                                saveDiscounts($venta_id, $descuentos_aplicados);
                            }

                            // Insertar detalles de venta y actualizar stock
                            foreach ($carrito as $item) {
                                if (isset($item['is_manual']) && $item['is_manual']) {
                                    // Item Manual: No hay producto_id, guardamos descripción, NO tocamos stock
                                    $stmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio, subtotal, descripcion) VALUES (?, NULL, ?, ?, ?, ?)");
                                    $stmt->execute([$venta_id, $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad'], $item['nombre']]);
                                } else {
                                    // Item Normal
                                    $stmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio, subtotal, descripcion) VALUES (?, ?, ?, ?, ?, ?)");
                                    // Descripción = nombre del producto (redundante pero útil si cambia nombre base) o NULL
                                    $stmt->execute([$venta_id, $item['id'], $item['cantidad'], $item['precio'], $item['precio'] * $item['cantidad'], $item['nombre']]);

                                    $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
                                    $stmt->execute([$item['cantidad'], $item['id']]);
                                }
                            }

                            // Registrar pago en efectivo
                            $stmt = $pdo->prepare("INSERT INTO venta_pagos (venta_id, metodo_pago_id, monto) VALUES (?, (SELECT id FROM metodos_pago WHERE nombre = 'Efectivo' LIMIT 1), ?)");
                            $stmt->execute([$venta_id, $amount_paid]);

                            if (isset($turnoAbierto) && $turnoAbierto) {
                                $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, venta_id, created_at, usuario_id, fecha) VALUES (?, 'venta', ?, ?, ?, NOW(), ?, NOW())");
                                $stmt->execute([$turnoAbierto['id'], $total, 'Venta #' . $venta_id, $venta_id, $_SESSION['user_id']]);
                            }

                            // Limpiar carrito y cliente
                            unset($_SESSION['carrito']);
                            unset($_SESSION['selected_customer']);
                            $carrito = [];

                            $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Venta completada exitosamente. Total: $' . number_format($total, 2) . ', Cambio: $' . number_format($change, 2) . '. <a href="print_ticket.php?id=' . $venta_id . '" target="_blank" class="underline font-bold">Imprimir Ticket</a></div>';
                        }
                    }
                }
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">El carrito está vacío.</div>';
            }
        } elseif (isset($_POST['select_customer'])) {
            $cliente_id = intval($_POST['cliente_id']);
            if ($cliente_id > 0) {
                $_SESSION['selected_customer'] = $cliente_id;
            } else {
                unset($_SESSION['selected_customer']);
            }
            exit; // AJAX request
        }
    }
}

// Calcular total del carrito
// Calcular total del carrito con promociones
$initial_calc = applyPromotions($carrito);
$total_carrito = $initial_calc['total_final'];

// Obtener productos disponibles
$stmt = $pdo->query("SELECT * FROM productos WHERE stock > 0 ORDER BY nombre");
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Sistema Kiosco</title>
    <script src="assets/js/tailwindcss.js"></script>
    <script src="assets/js/theme-config.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
    <style>
        /* Chrome, Safari, Edge, Opera */
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Nueva Venta</h1>
        <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-4">
            <?php echo $message; ?>
        </div>



        <?php if (!$turnoAbierto): ?>
            <!-- Bloqueo por turno cerrado -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center max-w-2xl mx-auto">
                <div class="mb-6">
                    <div class="inline-block p-4 rounded-full bg-yellow-100 text-yellow-600 mb-4">
                        <i class="fas fa-lock text-5xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Caja Cerrada</h2>
                    <p class="text-gray-600 text-lg">
                        Para poder realizar ventas, primero debes abrir un turno de caja.
                        Esto nos ayuda a mantener un control preciso del dinero.
                    </p>
                </div>

                <div class="flex justify-center gap-4">
                    <a href="cash.php"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <i class="fas fa-cash-register mr-2"></i>
                        Ir a Abrir Caja
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Interfaz de Ventas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <label for="scanner_input" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-barcode mr-1"></i>Escaner de Código de Barras
                                </label>
                                <div class="relative">
                                    <input type="text" id="scanner_input" placeholder="Escanear producto aquí..." autofocus
                                        class="w-full pl-4 pr-10 py-3 border-2 border-gray-400 rounded-none shadow-none focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-lg font-mono transition duration-150 ease-in-out">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-qrcode text-gray-400 text-xl"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Presiona Enter tras escanear o escribir</p>
                            </div>
                        </div>
                    </div>

                    <!-- Promociones Activas -->
                    <?php
                    $active_promos = getActivePromotions();
                    if (!empty($active_promos)):
                        ?>
                        <div class="bg-white border border-gray-300 p-6 shadow-none mb-6">
                            <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                                <i class="fas fa-tags text-gray-600 mr-2"></i>Promociones Disponibles
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($active_promos as $promo): ?>
                                    <div class="bg-white p-4 border border-gray-300 hover:bg-gray-50 transition duration-200">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-gray-800">
                                                    <?php echo htmlspecialchars($promo['nombre']); ?>
                                                </h4>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <?php echo htmlspecialchars($promo['descripcion'] ?? ''); ?>
                                                </p>

                                                <div
                                                    class="mt-2 inline-block px-2 py-1 bg-gray-100 border border-gray-300 text-gray-800 text-xs font-bold">
                                                    <?php
                                                    if ($promo['tipo'] == 'descuento_porcentaje')
                                                        echo '-' . floatval($promo['valor']) . '%';
                                                    elseif ($promo['tipo'] == 'descuento_fijo')
                                                        echo '-$' . number_format($promo['valor'], 0);
                                                    elseif ($promo['tipo'] == 'nxm')
                                                        echo $promo['valor_extra'];
                                                    else
                                                        echo '$' . number_format($promo['valor'], 2);
                                                    ?>
                                                </div>
                                            </div>

                                            <?php if (!empty($promo['productos_ids']) || ($promo['tipo'] == 'combo')): ?>
                                                <button onclick="addPromoToCart(<?php echo $promo['id']; ?>)"
                                                    class="bg-gray-800 hover:bg-gray-900 text-white p-2 shadow-none transition group"
                                                    title="Agregar Promoción">
                                                    <i class="fas fa-cart-plus group-hover:scale-110 transform transition"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white p-6 shadow-none border border-gray-300">
                        <h3 class="text-xl font-semibold mb-4">Productos Disponibles</h3>
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div class="relative flex-1 w-full">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-search text-lg"></i>
                                </span>
                                <input type="text" id="search_product" placeholder="Buscar producto por nombre..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 shadow-none focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-lg transition duration-150 ease-in-out">
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openManualItemModal()"
                                    class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-4 rounded shadow transition flex items-center gap-2 whitespace-nowrap">
                                    <i class="fas fa-plus-circle"></i> Ingreso Rápido $$
                                </button>
                                <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                                    <button onclick="toggleView('grid')" id="btn_grid"
                                        class="p-2 rounded-md bg-white text-blue-600 shadow-sm transition">
                                        <i class="fas fa-th-large text-xl"></i>
                                    </button>
                                    <button onclick="toggleView('list')" id="btn_list"
                                        class="p-2 rounded-md text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-list text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="products_grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <?php foreach ($productos as $prod): ?>
                                <div class="bg-white p-5 border border-gray-300 shadow-none hover:bg-gray-50 transition duration-200 ease-in-out"
                                    data-product-id="<?php echo $prod['id']; ?>">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="p-2 bg-gray-100 rounded-none text-gray-600">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <span
                                            class="px-2 py-1 rounded-md text-xs font-semibold <?php echo $prod['stock'] > 10 ? 'bg-green-100 text-green-700' : ($prod['stock'] > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'); ?>">
                                            Stock: <span
                                                class="stock-display"><?php echo htmlspecialchars($prod['stock']); ?></span>
                                        </span>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-800 mb-1">
                                        <?php echo htmlspecialchars($prod['nombre']); ?>
                                    </h4>
                                    <p class="text-2xl font-bold text-gray-900 mb-4">
                                        $<?php echo number_format($prod['precio'], 2); ?></p>

                                    <div class="flex items-center space-x-2">
                                        <div class="relative flex items-center w-36">
                                            <button type="button" onclick="decrement(this)"
                                                class="flex-shrink-0 bg-gray-100 hover:bg-gray-200 border border-gray-300 p-2 h-10 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                                                <i class="fas fa-minus text-gray-500 w-2"></i>
                                            </button>
                                            <input type="number" value="1" min="1" max="<?php echo $prod['stock']; ?>"
                                                class="product-quantity flex-1 w-full min-w-0 bg-gray-50 border-x-0 border-gray-300 h-10 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 px-0"
                                                required>
                                            <button type="button" onclick="increment(this, <?php echo $prod['stock']; ?>)"
                                                class="flex-shrink-0 bg-gray-100 hover:bg-gray-200 border border-gray-300 p-2 h-10 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                                                <i class="fas fa-plus text-gray-500 w-2"></i>
                                            </button>
                                        </div>
                                        <button type="button"
                                            class="add-to-cart-btn flex-1 bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2 px-4 shadow-none transition duration-150 ease-in-out flex items-center justify-center gap-2"
                                            data-product-id="<?php echo $prod['id']; ?>">
                                            <i class="fas fa-cart-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- List View (Hidden by default) -->
                        <div id="products_list"
                            class="hidden bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Producto</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Precio</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Stock</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($productos as $prod): ?>
                                            <tr class="hover:bg-gray-50 transition product-row">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg mr-4">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                        <div class="text-sm font-medium text-gray-900 product-name">
                                                            <?php echo htmlspecialchars($prod['nombre']); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                                    $<?php echo number_format($prod['precio'], 2); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $prod['stock'] > 10 ? 'bg-green-100 text-green-800' : ($prod['stock'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                                        <span
                                                            class="stock-display"><?php echo htmlspecialchars($prod['stock']); ?></span>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end space-x-2"
                                                        data-product-id="<?php echo $prod['id']; ?>">
                                                        <!-- data-product-id for stock update scope -->
                                                        <input type="number" value="1" min="1"
                                                            max="<?php echo $prod['stock']; ?>"
                                                            class="product-quantity w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm focus:ring-blue-500 focus:border-blue-500">
                                                        <button type="button"
                                                            class="add-to-cart-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded shadow transition flex items-center gap-1"
                                                            data-product-id="<?php echo $prod['id']; ?>">
                                                            <i class="fas fa-cart-plus"></i> Añadir
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 shadow-none border border-gray-300 sticky top-4">
                        <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                            <i class="fas fa-shopping-cart text-gray-600 mr-2"></i>Carrito
                        </h3>

                        <!-- Selector de Cliente - SIEMPRE VISIBLE -->
                        <div class="mb-4">
                            <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-search mr-1"></i>Filtrar Cliente
                            </label>
                            <input type="text" id="customer_search" placeholder="Escribe para buscar..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onkeyup="filterCustomers(this.value)">

                            <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-user mr-1"></i>Seleccionar Cliente
                            </label>
                            <select id="cliente_id" name="cliente_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="updateCustomerInfo()">
                                <option value="">Venta sin cliente</option>
                                <?php
                                $clientes_activos = getActiveCustomers();
                                foreach ($clientes_activos as $cliente):
                                    $selected = (isset($_SESSION['selected_customer']) && $_SESSION['selected_customer'] == $cliente['id']) ? 'selected' : '';
                                    // Prepare text for search
                                    $displayText = '#' . $cliente['id'] . ' - ' . $cliente['nombre'];
                                    if ($cliente['saldo_cuenta'] < 0) {
                                        $displayText .= ' (Debe: $' . number_format(abs($cliente['saldo_cuenta']), 2) . ')';
                                    }
                                    ?>
                                    <option value="<?php echo $cliente['id']; ?>" <?php echo $selected; ?>
                                        data-saldo="<?php echo $cliente['saldo_cuenta']; ?>">
                                        <?php echo htmlspecialchars($displayText); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="hidden_cliente_id_sync" name="cliente_id_sync">
                            <p class="text-xs text-gray-500 mt-1" id="customer_info"></p>
                        </div>

                        <!-- Método de Pago - SIEMPRE VISIBLE -->
                        <div class="mb-4">
                            <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-credit-card mr-1"></i>Método de Pago
                            </label>
                            <select id="metodo_pago" name="metodo_pago"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="togglePaymentFields()">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="cuenta_corriente" id="cuenta_corriente_option">Cuenta Corriente (Fiado)
                                </option>
                            </select>

                            <div id="transfer_details"
                                class="hidden mt-3 p-3 bg-purple-50 rounded border border-purple-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="transfer_reference"
                                            class="block text-sm font-bold text-purple-800 mb-1">
                                            Nombre del Remitente:
                                        </label>
                                        <input type="text" id="transfer_reference"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                            placeholder="Ej: Juan Pérez">
                                    </div>
                                    <div>
                                        <label for="transfer_phone" class="block text-sm font-bold text-purple-800 mb-1">
                                            Teléfono (Opcional):
                                        </label>
                                        <input type="text" id="transfer_phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                            placeholder="Ej: 11 1234 5678">
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-gray-500 mt-1">Selecciona un cliente para habilitar "Cuenta
                                Corriente"
                            </p>
                        </div>

                        <hr class="my-4">

                        <div id="cart_container">
                            <?php if (empty($carrito)): ?>
                                <p class="text-gray-500">El carrito está vacío</p>
                            <?php else: ?>
                                <div class="space-y-2 mb-4" id="cart_items">
                                    <?php foreach ($carrito as $item): ?>
                                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded"
                                            data-product-id="<?php echo $item['id']; ?>">
                                            <div>
                                                <p class="font-semibold"><?php echo htmlspecialchars($item['nombre']); ?></p>
                                                <input type="number" value="<?php echo $item['cantidad']; ?>" min="1"
                                                    class="cart-quantity w-12 px-1 py-0.5 border rounded text-sm"
                                                    data-product-id="<?php echo $item['id']; ?>">
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="item-total">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                                                <button type="button"
                                                    class="remove-from-cart-btn bg-red-500 hover:bg-red-700 text-white font-bold py-0.5 px-1 rounded text-sm"
                                                    data-product-id="<?php echo $item['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <hr class="mb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <strong>Total:</strong>
                                    <strong id="cart_total">$<?php echo number_format($total_carrito, 2); ?></strong>
                                </div>
                                <form method="POST" id="sale_form">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="cliente_id" id="hidden_cliente_id" value="">
                                    <input type="hidden" name="metodo_pago" id="hidden_metodo_pago" value="efectivo">

                                    <!-- Monto Pagado (solo para efectivo) -->
                                    <div class="mb-4" id="amount_paid_section">
                                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Monto
                                            Pagado</label>
                                        <input type="number" id="amount_paid" name="amount_paid" step="0.01"
                                            min="<?php echo $total_carrito; ?>"
                                            value="<?php echo isset($_POST['amount_paid']) ? $_POST['amount_paid'] : ''; ?>"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            oninput="calculateChange()">
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <button type="button" onclick="addAmount(2000)"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-sm font-medium">$2000</button>
                                            <button type="button" onclick="addAmount(5000)"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-sm font-medium">$5000</button>
                                            <button type="button" onclick="addAmount(10000)"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-sm font-medium">$10000</button>
                                            <button type="button" onclick="addAmount(20000)"
                                                class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded text-sm font-medium">$20000</button>
                                        </div>
                                    </div>
                                    <div class="mb-4" id="change_section">
                                        <strong>Cambio: $<span id="change_amount">0.00</span></strong>
                                    </div>
                                    <button type="submit" name="complete_sale" id="complete_sale_btn"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full"
                                        disabled>
                                        <i class="fas fa-check mr-2"></i>Completar Venta
                                    </button>
                                </form>
                                <div class="mt-2">
                                    <button type="button" id="clear_cart_btn"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded w-full text-sm">
                                        <i class="fas fa-trash mr-1"></i>Limpiar Carrito
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>

    <script>
        let currentTotal = <?php echo $total_carrito; ?>;

        function increment(btn, max) {
            const input = btn.previousElementSibling;
            let val = parseInt(input.value);
            if (val < max) input.value = val + 1;
        }

        function decrement(btn) {
            const input = btn.nextElementSibling;
            let val = parseInt(input.value);
            if (val > 1) input.value = val - 1;
        }

        function calculateChange() {
            const amountPaidEl = document.getElementById('amount_paid');
            if (!amountPaidEl) return;

            const total = currentTotal;
            const amountPaid = parseFloat(amountPaidEl.value) || 0;
            const changeSpan = document.getElementById('change_amount');
            const completeBtn = document.getElementById('complete_sale_btn');
            const methodSelect = document.getElementById('metodo_pago');
            const isCC = methodSelect && methodSelect.value === 'cuenta_corriente';
            const isTransfer = methodSelect && methodSelect.value === 'transferencia';

            if (changeSpan && completeBtn) {
                if (isCC) {
                    // Logic for Credit: Allow ANY amount >= 0
                    const debt = total - amountPaid;
                    if (debt > 0) {
                        changeSpan.textContent = "Deuda Restante: $" + debt.toFixed(2);
                        changeSpan.className = 'text-red-500 font-bold';
                    } else {
                        const realChange = amountPaid - total;
                        changeSpan.textContent = "Cambio: $" + realChange.toFixed(2); // Overpayment?
                        changeSpan.className = 'text-green-600 font-bold';
                    }
                    // Always Enable for CC (unless amount < 0 logic in HTML)
                    completeBtn.disabled = false;
                    completeBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                } else if (isTransfer) {
                    // Logic for Transfer: Always enable, amount paid ignored/hidden
                    completeBtn.disabled = false;
                    completeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    // Logic for Cash
                    const change = amountPaid - total;
                    if (change >= 0) {
                        changeSpan.textContent = change.toFixed(2);
                        changeSpan.className = 'text-green-600 font-bold text-xl';
                        completeBtn.disabled = false;
                        completeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        changeSpan.textContent = '0.00';
                        changeSpan.className = 'text-gray-400 font-bold';
                        completeBtn.disabled = true;
                        completeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            }
        }

        function addAmount(amount) {
            const amountPaidEl = document.getElementById('amount_paid');
            const currentValue = parseFloat(amountPaidEl.value) || 0;
            amountPaidEl.value = currentValue + amount;
            calculateChange();
        }

        // Función para actualizar método de pago cuando se selecciona cliente
        function updatePaymentMethod() {
            const clienteSelect = document.getElementById('cliente_id');
            const cuentaCorrienteOption = document.getElementById('cuenta_corriente_option');
            const customerInfo = document.getElementById('customer_info');
            const metodoPago = document.getElementById('metodo_pago');

            // Actualizar campo hidden
            document.getElementById('hidden_cliente_id').value = clienteSelect.value;

            if (clienteSelect.value) {
                // Cliente seleccionado - habilitar cuenta corriente
                cuentaCorrienteOption.disabled = false;
                cuentaCorrienteOption.removeAttribute('disabled');

                // Mostrar info del cliente
                const selectedOption = clienteSelect.options[clienteSelect.selectedIndex];
                const saldo = parseFloat(selectedOption.dataset.saldo);

                if (saldo < 0) {
                    customerInfo.innerHTML = `<span class="text-red-600 font-semibold">Deuda actual: $${Math.abs(saldo).toFixed(2)}</span>`;
                } else if (saldo > 0) {
                    customerInfo.innerHTML = `<span class="text-green-600 font-semibold">Saldo a favor: $${saldo.toFixed(2)}</span>`;
                } else {
                    customerInfo.innerHTML = `<span class="text-gray-600">Sin deuda</span>`;
                }
            } else {
                // Sin cliente - deshabilitar cuenta corriente
                cuentaCorrienteOption.disabled = true;
                customerInfo.innerHTML = '';
                if (metodoPago.value === 'cuenta_corriente') {
                    metodoPago.value = 'efectivo';
                    togglePaymentFields();
                }
            }
        }

        // Función para mostrar/ocultar campos según método de pago
        function togglePaymentFields() {
            const metodoPago = document.getElementById('metodo_pago').value;
            const clienteVal = document.getElementById('cliente_id').value;
            const amountSection = document.getElementById('amount_paid_section');
            const changeSection = document.getElementById('change_section');
            const completeBtn = document.getElementById('complete_sale_btn');
            const transferDetails = document.getElementById('transfer_details');

            // Actualizar campo hidden
            const hiddenMethod = document.getElementById('hidden_metodo_pago');
            if (hiddenMethod) hiddenMethod.value = metodoPago;

            // Manejo de Cuenta Corriente
            if (metodoPago === 'cuenta_corriente' && !clienteVal) {
                alert('⚠️ ACCIÓN REQUERIDA:\n\nDebes seleccionar un cliente PRIMERO para poder vender al Fiado.');
                document.getElementById('metodo_pago').value = 'efectivo';
                togglePaymentFields(); // Recursively reset interface
                return;
            }

            // Manejo de Detalles de Transferencia
            if (transferDetails) {
                if (metodoPago === 'transferencia') {
                    transferDetails.classList.remove('hidden');
                } else {
                    transferDetails.classList.add('hidden');
                }
            }


            if (metodoPago === 'cuenta_corriente') {
                // Mostrar campos para entrega parcial
                if (amountSection) {
                    amountSection.style.display = 'block';
                    const input = document.getElementById('amount_paid');
                    if (input) {
                        input.placeholder = "Monto entrega (Opcional)";
                        input.min = 0;
                    }
                }
                if (changeSection) changeSection.style.display = 'block';

                // Recalcular para actualizar texto de deuda/cambio
                calculateChange();
            } else if (metodoPago === 'transferencia') {
                // Ocultar montos y cambio
                if (amountSection) amountSection.style.display = 'none';
                if (changeSection) changeSection.style.display = 'none';

                // Habilitar botón de completado
                if (completeBtn) {
                    completeBtn.disabled = false;
                    completeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                // Mostrar campos de efectivo u otros
                if (amountSection) {
                    amountSection.style.display = 'block';
                    const input = document.getElementById('amount_paid');
                    if (input) input.min = currentTotal;
                }
                if (changeSection) changeSection.style.display = 'block';
                // Recalcular cambio
                calculateChange();
            }
        }

        // Función de búsqueda de productos
        document.getElementById('search_product').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            // Filter Grid
            const gridProducts = document.querySelectorAll('#products_grid > div');
            gridProducts.forEach(product => {
                const productName = product.querySelector('h4').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });

            // Filter List
            const listRows = document.querySelectorAll('#products_list .product-row');
            listRows.forEach(row => {
                const productName = row.querySelector('.product-name').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function toggleView(view) {
            const grid = document.getElementById('products_grid');
            const list = document.getElementById('products_list');
            const btnGrid = document.getElementById('btn_grid');
            const btnList = document.getElementById('btn_list');

            if (view === 'list') {
                grid.classList.add('hidden');
                list.classList.remove('hidden');

                // Update buttons
                btnList.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                btnList.classList.remove('text-gray-400', 'hover:text-gray-600');

                btnGrid.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                btnGrid.classList.add('text-gray-400', 'hover:text-gray-600');
            } else {
                grid.classList.remove('hidden');
                list.classList.add('hidden');

                // Update buttons
                btnGrid.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                btnGrid.classList.remove('text-gray-400', 'hover:text-gray-600');

                btnList.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                btnList.classList.add('text-gray-400', 'hover:text-gray-600');
            }
        }

        // AJAX functions
        async function sendAjaxRequest(formData) {
            try {
                const response = await fetch('sales.php', {
                    method: 'POST',
                    body: formData
                });
                return await response.json();
            } catch (error) {
                console.error('Error:', error);
                return { success: false, message: 'Error de conexión' };
            }
        }

        function updateCartUI(data) {
            currentTotal = data.total_carrito;
            const csrfToken = document.getElementById('csrf_token').value;

            if (Object.keys(data.carrito).length === 0) {
                document.getElementById('cart_container').innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-basket text-4xl mb-3 opacity-30"></i>
                        <p>Tu carrito está vacío</p>
                    </div>`;
            } else {
                let cartHtml = '<div class="space-y-3 mb-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar" id="cart_items">';
                for (const [productId, item] of Object.entries(data.carrito)) {
                    cartHtml += `
                        <div class="flex justify-between items-center p-3 bg-white border-b border-gray-200 group hover:bg-gray-50 transition" data-product-id="${productId}">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">${item.nombre}</p>
                                <div class="flex items-center text-sm text-gray-500 mt-1">
                                    <span>$${Number(item.precio).toFixed(2)} x </span>
                                    <input type="number" value="${item.cantidad}" min="1" class="cart-quantity ml-1 w-16 px-2 py-1 border border-gray-300 rounded-none focus:ring-gray-800 focus:border-gray-800 text-center" data-product-id="${productId}">
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-1 ml-4">
                                <span class="font-bold text-gray-800">$${(item.precio * item.cantidad).toFixed(2)}</span>
                                <button type="button" class="remove-from-cart-btn text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition" data-product-id="${productId}" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
                cartHtml += '</div>';
                cartHtml += `
                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between items-end">
                            <span class="text-gray-600 font-medium">Total a Pagar</span>
                            <span class="text-3xl font-bold text-gray-800" id="cart_total">$${currentTotal.toFixed(2)}</span>
                        </div>
                    </div>
                `;
                cartHtml += `
                    <form method="POST" id="sale_form" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="${csrfToken}">
                        <input type="hidden" name="cliente_id" id="hidden_cliente_id" value="">
                        <input type="hidden" name="metodo_pago" id="hidden_metodo_pago" value="efectivo">
                        <input type="hidden" name="transfer_reference" id="hidden_transfer_reference" value="">
                        <input type="hidden" name="transfer_phone" id="hidden_transfer_phone" value="">
                        <div id="amount_paid_section">
                            <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Monto Pagado</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="${currentTotal}" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition" placeholder="0.00" oninput="calculateChange()">
                            </div>
                            <div class="mt-2 grid grid-cols-4 gap-2">
                                <button type="button" onclick="addAmount(2000)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs transition border border-gray-200 shadow-sm">$2000</button>
                                <button type="button" onclick="addAmount(5000)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs transition border border-gray-200 shadow-sm">$5000</button>
                                <button type="button" onclick="addAmount(10000)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs transition border border-gray-200 shadow-sm">$10000</button>
                                <button type="button" onclick="addAmount(20000)" class="bg-white hover:bg-gray-100 text-gray-800 px-2 py-1 rounded-none transition border border-gray-300 shadow-none">$20000</button>
                            </div>
                        </div>
                        <div id="change_section" class="flex justify-between items-center py-2 bg-gray-50 px-3 border border-gray-200">
                            <span class="text-gray-600 font-medium">Cambio:</span>
                            <span id="change_amount" class="text-gray-400 font-bold">0.00</span>
                        </div>
                        <button type="submit" name="complete_sale" id="complete_sale_btn" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 shadow-none transition transform flex items-center justify-center opacity-50 cursor-not-allowed" disabled>
                            <i class="fas fa-check mr-2"></i>Completar Venta
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <button type="button" id="clear_cart_btn" class="text-gray-400 hover:text-red-500 text-sm font-medium transition flex items-center justify-center mx-auto">
                            <i class="fas fa-trash mr-1"></i>Vaciar Carrito
                        </button>
                    </div>
                `;
                document.getElementById('cart_container').innerHTML = cartHtml;
                attachCartEventListeners();
            }

            // Update elements after HTML is set
            const cartTotalEl = document.getElementById('cart_total');
            if (cartTotalEl) {
                cartTotalEl.textContent = '$' + currentTotal.toFixed(2);
            }
            const amountPaidEl = document.getElementById('amount_paid');
            if (amountPaidEl) {
                amountPaidEl.min = currentTotal;
            }

            // Sync Hidden Fields from Persistent Selects (Essential to persistence across cart updates)
            const methodSelect = document.getElementById('metodo_pago');
            const clientSelect = document.getElementById('cliente_id');
            const transferRefInput = document.getElementById('transfer_reference');
            const transferPhoneInput = document.getElementById('transfer_phone');

            const hiddenMethod = document.getElementById('hidden_metodo_pago');
            const hiddenClient = document.getElementById('hidden_cliente_id');
            const hiddenTransferRef = document.getElementById('hidden_transfer_reference');
            const hiddenTransferPhone = document.getElementById('hidden_transfer_phone');

            if (methodSelect && hiddenMethod) hiddenMethod.value = methodSelect.value;
            if (clientSelect && hiddenClient) hiddenClient.value = clientSelect.value;
            if (transferRefInput && hiddenTransferRef) hiddenTransferRef.value = transferRefInput.value;
            if (transferPhoneInput && hiddenTransferPhone) hiddenTransferPhone.value = transferPhoneInput.value;

            if (transferRefInput && hiddenTransferRef) {
                transferRefInput.addEventListener('input', function () {
                    hiddenTransferRef.value = this.value;
                });
            }
            if (transferPhoneInput && hiddenTransferPhone) {
                transferPhoneInput.addEventListener('input', function () {
                    hiddenTransferPhone.value = this.value;
                });
            }

            if (amountPaidEl) {
                // Determine Minimum Payment based on Method
                const isCuentaCorriente = methodSelect && methodSelect.value === 'cuenta_corriente';

                if (isCuentaCorriente) {
                    amountPaidEl.min = 0;
                    amountPaidEl.placeholder = "Entrega parcial (Opcional)";
                } else {
                    amountPaidEl.min = currentTotal;
                    amountPaidEl.placeholder = currentTotal.toFixed(2);
                }

                calculateChange();
            }

            // Re-attach listener to Method Select if needed (Check if exists)
            if (methodSelect) {
                // Remove old listener to avoid dupes? HTML inputs usually static.
                // Ideally this change should be in global scope, but here ensures it works on redraw.
                methodSelect.onchange = function () {
                    togglePaymentFields(); // Ensure UI toggle
                    if (hiddenMethod) hiddenMethod.value = this.value;
                    if (amountPaidEl) {
                        if (this.value === 'cuenta_corriente') {
                            amountPaidEl.min = 0;
                            amountPaidEl.placeholder = "Entrega parcial";
                        } else {
                            amountPaidEl.min = currentTotal;
                            amountPaidEl.placeholder = currentTotal.toFixed(2);
                        }
                        calculateChange();
                    }
                };
            }

            // Ensure UI is consistent with selected method
            togglePaymentFields();
        }

        // Función para mostrar opciones de transferencia


        function attachCartEventListeners() {
            // Cart quantity change
            document.querySelectorAll('.cart-quantity').forEach(input => {
                input.addEventListener('change', async function () {
                    const productId = this.getAttribute('data-product-id');
                    const cantidad = parseInt(this.value);
                    if (cantidad > 0) {
                        const formData = new FormData();
                        formData.append('ajax', '1');
                        formData.append('csrf_token', document.getElementById('csrf_token').value);
                        formData.append('update_quantity', '1');
                        formData.append('producto_id', productId);
                        formData.append('cantidad', cantidad);
                        const data = await sendAjaxRequest(formData);
                        if (data.success) {
                            updateCartUI(data);
                        }
                    }
                });
            });

            // Remove from cart
            document.querySelectorAll('.remove-from-cart-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const productId = this.getAttribute('data-product-id');
                    const formData = new FormData();
                    formData.append('ajax', '1');
                    formData.append('csrf_token', document.getElementById('csrf_token').value);
                    formData.append('remove_from_cart', '1');
                    formData.append('producto_id', productId);
                    const data = await sendAjaxRequest(formData);
                    if (data.success) {
                        updateCartUI(data);
                    }
                });
            });

            // Clear cart
            const clearBtn = document.getElementById('clear_cart_btn');
            if (clearBtn) {
                clearBtn.addEventListener('click', async function () {
                    if (confirm('¿Estás seguro de vaciar el carrito?')) {
                        const formData = new FormData();
                        formData.append('ajax', '1');
                        formData.append('csrf_token', document.getElementById('csrf_token').value);
                        formData.append('clear_cart', '1');
                        const data = await sendAjaxRequest(formData);
                        if (data.success) {
                            updateCartUI(data);
                        }
                    }
                });
            }
        }

        // Add to cart
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const productId = this.getAttribute('data-product-id');
                const quantityInput = this.parentElement.querySelector('.product-quantity');
                const cantidad = parseInt(quantityInput.value);
                if (cantidad > 0) {
                    const formData = new FormData();
                    formData.append('ajax', '1');
                    formData.append('csrf_token', document.getElementById('csrf_token').value);
                    formData.append('add_to_cart', '1');
                    formData.append('producto_id', productId);
                    formData.append('cantidad', cantidad);
                    const data = await sendAjaxRequest(formData);
                    if (data.success) {
                        updateCartUI(data);
                        // Update stock display
                        const productDiv = document.querySelector(`[data-product-id="${productId}"]`);
                        if (productDiv && data.updated_stock !== undefined) {
                            productDiv.querySelector('.stock-display').textContent = data.updated_stock;
                            quantityInput.max = data.updated_stock;
                            // reset to 1
                            quantityInput.value = 1;
                        }
                    } else {
                        alert(data.message);
                    }
                }
            });
        });



        // Barcode Scanner Logic
        const scannerInput = document.getElementById('scanner_input');
        if (scannerInput) {
            // Keep focus on scanner (optional, maybe annoying if user wants to search manually)
            // But good for pure POS usage
            // setInterval(() => { if(document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') scannerInput.focus(); }, 2000);

            scannerInput.addEventListener('keydown', async function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const barcode = this.value.trim();
                    if (barcode) {
                        this.disabled = true;
                        const formData = new FormData();
                        formData.append('ajax', '1');
                        formData.append('csrf_token', document.getElementById('csrf_token').value);
                        formData.append('scan_barcode', '1');
                        formData.append('barcode', barcode);

                        const data = await sendAjaxRequest(formData);

                        this.value = '';
                        this.disabled = false;
                        this.focus();

                        if (data.success) {
                            updateCartUI(data);
                            // Visual feedback
                            const productDiv = document.querySelector(`[data-product-id="${data.added_product_id}"]`);
                            if (productDiv) {
                                productDiv.querySelector('.stock-display').textContent = data.updated_stock;
                                // Highlight effect
                                productDiv.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
                                setTimeout(() => productDiv.classList.remove('ring-2', 'ring-green-500', 'bg-green-50'), 500);
                            }
                            // Play success sound (optional, simple beep)
                            // const audio = new Audio('beep.mp3'); audio.play().catch(e=>{}); 
                        } else {
                            // Error feedback
                            alert(data.message);
                            // Or better, a toaster/flash message
                        }
                    }
                }
            });
        }

        // Initial event listeners
        document.addEventListener('DOMContentLoaded', function () {
            // Load initial cart UI to match JS template
            updateCartUI({
                total_carrito: <?php echo $total_carrito; ?>,
                carrito: <?php echo json_encode($carrito); ?>
            });
        });

        // Calcular cambio inicial si hay monto pagado previo
        <?php if (!empty($carrito) && isset($_POST['amount_paid'])): ?>
            calculateChange();
        <?php endif; ?>
        // Función para filtrar clientes (Buscador Simple - Robust)
        let allCustomerOptions = [];

        function filterCustomers(searchTerm) {
            const select = document.getElementById('cliente_id');
            if (!select) return;

            // Inicializar caché de opciones si es la primera vez
            if (allCustomerOptions.length === 0) {
                allCustomerOptions = Array.from(select.options);
            }

            const search = (searchTerm || "").toLowerCase().trim();
            const currentValue = select.value;

            // Limpiar select
            select.innerHTML = '';

            let hasSelection = false;

            allCustomerOptions.forEach(option => {
                const text = option.text.toLowerCase();
                // Siempre mantener la opción por defecto (valor vacío) o si coincide
                if (option.value === "" || text.includes(search)) {
                    select.appendChild(option);
                    if (option.value === currentValue) hasSelection = true;
                }
            });

            // Intentar mantener la selección si aún es visible
            if (hasSelection) {
                select.value = currentValue;
            } else {
                select.value = ""; // Resetear si la selección desapareció
            }
        }

        // Actualizar info del cliente
        function updateCustomerInfo() {
            const select = document.getElementById('cliente_id');
            const val = select.value;
            const customerInfo = document.getElementById('customer_info');
            const cuentaCorrienteOption = document.getElementById('cuenta_corriente_option');

            // Sync hidden if needed
            if (document.getElementById('hidden_cliente_id')) {
                document.getElementById('hidden_cliente_id').value = val;
            }

            if (val) {
                if (cuentaCorrienteOption) cuentaCorrienteOption.disabled = false;

                const option = select.options[select.selectedIndex];
                const saldo = parseFloat(option.getAttribute('data-saldo')) || 0;

                if (saldo < 0) {
                    customerInfo.innerHTML = `<span class="text-red-600 font-semibold">Deuda actual: $${Math.abs(saldo).toFixed(2)}</span>`;
                } else if (saldo > 0) {
                    customerInfo.innerHTML = `<span class="text-green-600 font-semibold">Saldo a favor: $${saldo.toFixed(2)}</span>`;
                } else {
                    customerInfo.innerHTML = `<span class="text-gray-600">Sin deuda</span>`;
                }
            } else {
                customerInfo.innerHTML = "";
            }
        }

        // currentTotal is already defined at top of script


        // Función para agregar promoción desde el botón
        function addPromoToCart(promoId) {
            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('csrf_token', document.getElementById('csrf_token').value);
            formData.append('add_promo_to_cart', '1');
            formData.append('promocion_id', promoId);

            sendAjaxRequest(formData).then(data => {
                if (data.success) {
                    updateCartUI(data);
                    // Opcional: Mostrar alerta
                    // alert(data.message);
                } else {
                    alert(data.message || 'Error al agregar promoción');
                }
            });
        }
    </script>
    <!-- Modal Ingreso Manual -->
    <div id="manualItemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-2xl transform transition-all scale-100">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                <i class="fas fa-hand-holding-usd mr-2 text-gray-800"></i>Ingreso Rápido
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Monto ($) <span
                            class="text-red-500">*</span></label>
                    <input type="number" id="manual_monto"
                        class="w-full px-4 py-3 text-2xl font-bold text-gray-900 border-2 border-gray-800 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-300"
                        placeholder="0.00" autofocus>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Descripción (Opcional)</label>
                    <input type="text" id="manual_desc"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                        placeholder="Ej: Pan, Fiambre, Varios...">
                    <p class="text-xs text-gray-500 mt-1">Si se deja vacío, se guardará como "Item Manual".</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button onclick="closeManualItemModal()"
                    class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition">Cancelar</button>
                <button onclick="addManualItem()"
                    class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-black font-bold shadow-lg transform hover:-translate-y-0.5 transition">Agregar
                    al Carrito</button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
        function openManualItemModal() {
            document.getElementById('manualItemModal').classList.remove('hidden');
            document.getElementById('manual_monto').value = '';
            document.getElementById('manual_desc').value = '';
            setTimeout(() => document.getElementById('manual_monto').focus(), 100);
        }

        function closeManualItemModal() {
            document.getElementById('manualItemModal').classList.add('hidden');
        }

        function addManualItem() {
            const monto = parseFloat(document.getElementById('manual_monto').value);
            const desc = document.getElementById('manual_desc').value;

            if (!monto || monto <= 0) {
                alert("Por favor ingresa un monto válido.");
                return;
            }

            const formData = new FormData();
            formData.append('add_manual_to_cart', '1');
            formData.append('monto', monto);
            formData.append('descripcion', desc);
            formData.append('cantidad', 1);
            formData.append('csrf_token', document.getElementById('csrf_token').value);
            formData.append('ajax', '1');

            fetch('sales.php', {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        closeManualItemModal();
                        updateCartUI(data); // Fixed: Check signature in other calls
                        showNotification('success', data.message);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => console.error(err));
        }

        // Allow Enter key to submit in modal
        document.getElementById('manual_monto').addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                if (this.value && parseFloat(this.value) > 0) {
                    document.getElementById('manual_desc').focus();
                }
            }
        });
        document.getElementById('manual_desc').addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                addManualItem();
            }
        });
    </script>
</body>

</html>