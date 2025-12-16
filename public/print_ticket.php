<?php
require_once __DIR__ . '/../app/bootstrap.php';

// Verificar que se reciba el ID de venta
if (!isset($_GET['id'])) {
    die('ID de venta no especificado');
}

$venta_id = intval($_GET['id']);

// Obtener datos de la venta
$stmt = $pdo->prepare("
    SELECT v.*, 
           c.nombre as cliente_nombre, c.telefono as cliente_telefono,
           u.nombre as vendedor
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    LEFT JOIN usuarios u ON v.usuario_id = u.id
    WHERE v.id = ?
");
$stmt->execute([$venta_id]);
$venta = $stmt->fetch();

if (!$venta) {
    die('Venta no encontrada');
}

// Obtener detalles de productos
$stmt = $pdo->prepare("
    SELECT vd.*, p.nombre as producto_nombre
    FROM venta_detalles vd
    JOIN productos p ON vd.producto_id = p.id
    WHERE vd.venta_id = ?
");
$stmt->execute([$venta_id]);
$detalles = $stmt->fetchAll();

// Obtener métodos de pago
$stmt = $pdo->prepare("
    SELECT vp.*, mp.nombre as metodo_nombre
    FROM venta_pagos vp
    JOIN metodos_pago mp ON vp.metodo_pago_id = mp.id
    WHERE vp.venta_id = ?
");
$stmt->execute([$venta_id]);
$pagos = $stmt->fetchAll();

// Obtener descuentos
$stmt = $pdo->prepare("SELECT * FROM venta_descuentos WHERE venta_id = ?");
$stmt->execute([$venta_id]);
$descuentos = $stmt->fetchAll();

// Obtener configuración del negocio
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
    <title>Ticket #<?php echo $venta_id; ?></title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
        }

        .ticket {
            border: 1px dashed #000;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        .total-box {
            background: #f0f0f0;
            padding: 5px;
            margin-top: 10px;
            border: 1px solid #000;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <!-- Encabezado -->
        <div class="center bold" style="font-size: 16px;">
            <?php echo strtoupper($config['negocio_nombre'] ?? 'MI KIOSCO'); ?>
        </div>
        <div class="center" style="font-size: 10px;">
            <?php echo $config['negocio_direccion'] ?? ''; ?><br>
            Tel: <?php echo $config['negocio_telefono'] ?? ''; ?><br>
            <?php echo $config['negocio_email'] ?? ''; ?>
        </div>

        <div class="line"></div>

        <!-- Datos de la venta -->
        <table style="font-size: 10px;">
            <tr>
                <td>Ticket #:</td>
                <td class="right bold"><?php echo str_pad($venta_id, 6, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td class="right"><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
            </tr>
            <tr>
                <td>Vendedor:</td>
                <td class="right"><?php echo htmlspecialchars($venta['vendedor'] ?? 'N/A'); ?></td>
            </tr>
            <?php if ($venta['cliente_nombre']): ?>
                <tr>
                    <td>Cliente:</td>
                    <td class="right"><?php echo htmlspecialchars($venta['cliente_nombre']); ?></td>
                </tr>
                <?php if ($venta['cliente_telefono']): ?>
                    <tr>
                        <td>Tel:</td>
                        <td class="right"><?php echo htmlspecialchars($venta['cliente_telefono']); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        </table>

        <div class="line"></div>

        <!-- Productos -->
        <table>
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="text-align: left;">Producto</th>
                    <th style="text-align: center;">Cant</th>
                    <th style="text-align: right;">Precio</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $det): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($det['producto_nombre']); ?></td>
                        <td class="center"><?php echo $det['cantidad']; ?></td>
                        <td class="right">$<?php echo number_format($det['precio'], 2); ?></td>
                        <td class="right bold">$<?php echo number_format($det['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="line"></div>

        <!-- Totales -->
        <table>
            <?php if (!empty($descuentos)): ?>
                <tr>
                    <td>Subtotal:</td>
                    <td class="right">$<?php echo number_format($venta['subtotal'] ?? $venta['total'], 2); ?></td>
                </tr>
                <?php foreach ($descuentos as $desc): ?>
                    <tr>
                        <td style="font-size: 10px;"><?php echo htmlspecialchars($desc['descripcion']); ?></td>
                        <td class="right" style="color: red;">-$<?php echo number_format($desc['monto_descuento'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr style="font-size: 16px;">
                <td class="bold">TOTAL:</td>
                <td class="right bold">$<?php echo number_format($venta['total'], 2); ?></td>
            </tr>
        </table>

        <div class="line"></div>

        <!-- Métodos de pago -->
        <?php if (!empty($pagos)): ?>
            <div class="bold">Forma de Pago:</div>
            <table style="font-size: 10px;">
                <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pago['metodo_nombre']); ?></td>
                        <td class="right">$<?php echo number_format($pago['monto'], 2); ?></td>
                    </tr>
                    <?php if ($pago['referencia']): ?>
                        <tr>
                            <td colspan="2" style="font-size: 9px; padding-left: 10px;">Ref:
                                <?php echo htmlspecialchars($pago['referencia']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <table>
                <tr>
                    <td>Pagó:</td>
                    <td class="right">$<?php echo number_format($venta['monto_pagado'], 2); ?></td>
                </tr>
                <tr>
                    <td>Cambio:</td>
                    <td class="right">$<?php echo number_format($venta['cambio'], 2); ?></td>
                </tr>
            </table>
        <?php endif; ?>

        <div class="line"></div>

        <!-- Mensaje final -->
        <div class="center" style="font-size: 11px; margin-top: 10px;">
            <?php echo $config['ticket_mensaje'] ?? '¡Gracias por su compra!'; ?>
        </div>

        <div class="center" style="font-size: 9px; margin-top: 10px;">
            Sistema: <?php echo APP_NAME; ?>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()"
            style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="window.close()"
            style="background: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>

    <script>
        // Auto-imprimir si está configurado
        <?php if (($config['ticket_auto_print'] ?? '0') == '1'): ?>
            window.onload = function () {
                setTimeout(function () {
                    window.print();
                }, 500);
            };
        <?php endif; ?>
    </script>
</body>

</html>