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
// Ancho de papel para impresión (mm). Puede configurarse en tabla `configuracion` con clave 'ticket_paper_width' (ej. '80' o '58')
$paper_width = !empty($config['ticket_paper_width']) ? $config['ticket_paper_width'] : '80';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $venta_id; ?></title>
    <style>
        /* Forzar tamaño de página para impresión térmica */
        @page { size: <?php echo intval($paper_width); ?>mm auto; margin: 0; }
        @media print {
            html, body { width: <?php echo intval($paper_width); ?>mm; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
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
            width: <?php echo intval($paper_width); ?>mm;
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
        <?php if (intval($paper_width) <= 58): ?>
        /* Ajustes específicos para rollos de 58mm */
        body { font-size: 10px; padding: 6px; }
        .ticket { padding: 6px; }
        img { max-width: 48mm; }
        .products td { padding: 1px 0; }
        <?php endif; ?>
    </style>
</head>

<body>
    <div class="ticket">
        <!-- Encabezado fiscal (formato para impresoras térmicas) -->
        <?php if (!empty($config['negocio_logo_fiscal']) && file_exists(PUBLIC_PATH . '/uploads/' . $config['negocio_logo_fiscal'])): ?>
            <div class="center" style="margin-bottom:6px;">
                <img src="/uploads/<?php echo htmlspecialchars($config['negocio_logo_fiscal']); ?>" alt="Logo" style="max-width:60mm; height:auto;">
            </div>
        <?php endif; ?>

        <div class="center bold" style="font-size: 16px;">
            <?php echo strtoupper($config['negocio_nombre'] ?? 'MI KIOSCO'); ?>
        </div>

        <div style="font-size:10px;">
            <div><?php echo $config['negocio_direccion'] ?? ''; ?></div>
            <div>Tel: <?php echo $config['negocio_telefono'] ?? ''; ?></div>
            <div><?php echo $config['negocio_email'] ?? ''; ?></div>
            <div>CUIT: <?php echo $config['negocio_cuit'] ?? '--'; ?></div>
        </div>

        <div class="line"></div>

        <!-- Datos de la venta / comprobante -->
        <table style="font-size: 10px;">
            <tr>
                <td>Comprobante:</td>
                <td class="right bold"><?php echo ($config['comprobante_letra'] ?? 'X') . ' - ' . ($config['comprobante_tipo'] ?? 'TICKET'); ?></td>
            </tr>
            <tr>
                <td>Punto de Venta:</td>
                <td class="right"><?php echo str_pad($config['punto_venta'] ?? '0001', 4, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td>Número:</td>
                <td class="right bold"><?php echo str_pad($venta_id, 6, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td>Fecha y hora:</td>
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

        <!-- Productos (formato inspirado en ticket de ejemplo) -->
        <div style="font-size:10px;">
            <div style="border-bottom:1px dashed #000; margin-bottom:6px; padding-bottom:4px;">
                <strong>CLIENTE:</strong>
                <?php echo $venta['cliente_nombre'] ? ' ' . htmlspecialchars($venta['cliente_nombre']) : ' A CONSUMIDOR FINAL'; ?>
            </div>

            <div style="margin-top:6px;">
                <table class="products" style="width:100%; font-family: 'Courier New', monospace; font-size:10px; table-layout:fixed; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="width:70%; text-align:center; padding-bottom:6px;">Cant./Precio Unit.</th>
                            <th style="width:30%; text-align:right; padding-bottom:6px;">Descripción (%IVA)[%BI]</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $det):
                            $qty = number_format($det['cantidad'], 3, ',', '.');
                            $unit = number_format($det['precio'], 2, ',', '.');
                            $subtotal = number_format($det['subtotal'], 2, ',', '.');
                            $iva_pct = isset($det['iva']) ? $det['iva'] : (isset($det['iva_porcentaje']) ? $det['iva_porcentaje'] : (isset($det['porcentaje_iva']) ? $det['porcentaje_iva'] : null));
                            $iva_disp = $iva_pct !== null ? '(' . number_format($iva_pct, 2, ',', '.') . ')' : '';
                        ?>
                            <tr>
                                <td style="width:70%; vertical-align:top; padding:2px 0;">
                                    <div><?php echo $qty; ?> X <?php echo $unit; ?></div>
                                    <div style="white-space:normal;"><?php echo htmlspecialchars($det['producto_nombre']); ?></div>
                                </td>
                                <td style="width:30%; text-align:right; vertical-align:top; padding:2px 0;"><?php echo $iva_disp; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="border-top:1px dashed #000; margin-top:6px;"></div>
            </div>
        </div>

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
                <?php
                    $pagos_total = 0;
                    foreach ($pagos as $pago) {
                        $pagos_total += floatval($pago['monto']);
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pago['metodo_nombre']); ?></td>
                        <td class="right">$<?php echo number_format($pago['monto'], 2); ?></td>
                    </tr>
                    <?php if (!empty($pago['referencia'])): ?>
                        <tr>
                            <td colspan="2" style="font-size: 9px; padding-left: 10px;">Ref: <?php echo htmlspecialchars($pago['referencia']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php } ?>
                <tr>
                    <td style="font-weight:bold;">Pagó:</td>
                    <td class="right">$<?php echo number_format($pagos_total, 2); ?></td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">Vuelto:</td>
                    <td class="right">$<?php echo number_format(max(0, $pagos_total - floatval($venta['total'])), 2); ?></td>
                </tr>
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