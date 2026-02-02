<?php
require_once __DIR__ . '/../app/bootstrap.php';

// Verificar que se reciba el ID de venta
if (!isset($_GET['id'])) {
    die('ID de venta no especificado');
}

$venta_id = intval($_GET['id']);

// Obtener datos de la venta (USANDO SOLO COLUMNAS EXISTENTES)
$stmt = $pdo->prepare("
    SELECT v.*, 
           c.nombre as cliente_nombre, 
           c.telefono as cliente_telefono,
           c.direccion as cliente_direccion,
           u.username as VENDEDOR
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
    SELECT vd.*, 
           COALESCE(p.nombre, 'Varios') as producto_nombre,
           p.codigo_barra as producto_codigo
    FROM venta_detalles vd
    LEFT JOIN productos p ON vd.producto_id = p.id
    WHERE vd.venta_id = ?
    ORDER BY vd.id
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

// Calcular IVA (21% por defecto para Argentina)
$iva_porcentaje = 21.00;
$subtotal_sin_iva = 0;
$total_iva = 0;

foreach ($detalles as $det) {
    $subtotal_sin_iva += $det['precio'] * $det['cantidad'];
}
$total_iva = $subtotal_sin_iva * ($iva_porcentaje / 100);

// Determinar si es cuenta corriente
$es_cuenta_corriente = false;
foreach ($pagos as $pago) {
    if (stripos($pago['metodo_nombre'], 'Cuenta Corriente') !== false) {
        $es_cuenta_corriente = true;
        break;
    }
}

// Ancho de papel
$paper_width = '80'; // Valor por defecto para impresoras térmicas estándar
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante #<?php echo $venta_id; ?></title>
    <style>
        /* Estilos optimizados para impresión térmica 80mm */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        @media print {
            html, body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 2mm 3mm;
            font-size: 10px;
            line-height: 1.1;
        }

        .ticket {
            padding: 1mm 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .condensed {
            font-size: 9px;
            line-height: 1;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 2px 0;
        }

        .double-line {
            border-top: 3px double #000;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th, td {
            padding: 1px 0;
            vertical-align: top;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }

        .productos-table th {
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .product-row td {
            padding: 1px 0;
        }

        .totales-box {
            background: #f8f8f8;
            padding: 4px;
            margin: 4px 0;
            border: 1px solid #000;
        }

        .iva-row {
            font-size: 8px;
            color: #666;
        }

        .legal-footer {
            font-size: 8px;
            text-align: center;
            margin-top: 5px;
            padding-top: 3px;
            border-top: 1px dashed #ccc;
        }

        .cuenta-corriente {
            background: #fff3cd;
            padding: 3px;
            margin: 3px 0;
            border: 1px solid #ffc107;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <!-- ENCABEZADO PROFESIONAL -->
        <div class="header text-center">
            <div style="font-size: 12px; font-weight: bold; letter-spacing: 1px;">
                <?php echo strtoupper($config['negocio_nombre'] ?? 'MI KIOSCO'); ?>
            </div>
            <div class="condensed">
                <?php echo $config['negocio_direccion'] ?? 'Calle Principal 123'; ?><br>
                Tel: <?php echo $config['negocio_telefono'] ?? '123-456-7890'; ?>
            </div>
        </div>

        <!-- DATOS DEL COMPROBANTE -->
        <table class="condensed">
            <tr>
                <td><strong>COMPROBANTE:</strong></td>
                <td class="text-right"><strong>TICKET</strong></td>
            </tr>
            <tr>
                <td>Número:</td>
                <td class="text-right"><?php echo str_pad($venta_id, 8, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td class="text-right"><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
            </tr>
            <tr>
                <td>Vendedor:</td>
                <td class="text-right"><?php echo htmlspecialchars($venta['VENDEDOR'] ?? 'SISTEMA'); ?></td>
            </tr>
        </table>

        <div class="line"></div>

        <!-- DATOS DEL CLIENTE -->
        <?php if (!empty($venta['cliente_nombre'])): ?>
        <div class="condensed">
            <div><strong>CLIENTE:</strong> <?php echo htmlspecialchars($venta['cliente_nombre']); ?></div>
            <?php if (!empty($venta['cliente_telefono'])): ?>
                <div>Tel: <?php echo htmlspecialchars($venta['cliente_telefono']); ?></div>
            <?php endif; ?>
            <div>Consumidor final</div>
        </div>
        <div class="line"></div>
        <?php endif; ?>

        <!-- DETALLE DE PRODUCTOS -->
        <table class="productos-table">
            <thead>
                <tr>
                    <th class="text-left">Cant.</th>
                    <th class="text-left">Descripción</th>
                    <th class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $det): ?>
                <tr class="product-row">
                    <td class="text-left"><?php echo number_format($det['cantidad'], 0, ',', '.'); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($det['producto_nombre']); ?></td>
                    <td class="text-right">$<?php echo number_format($det['subtotal'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="double-line"></div>

        <!-- TOTALES -->
        <div class="totales-box">
            <table>
                <?php if (!empty($descuentos)): ?>
                    <?php foreach ($descuentos as $desc): ?>
                    <tr class="iva-row">
                        <td class="text-left"><?php echo htmlspecialchars($desc['descripcion']); ?></td>
                        <td class="text-right">-$<?php echo number_format($desc['monto_descuento'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- IVA DISCRIMINADO -->
                <tr class="iva-row">
                    <td class="text-left">IVA 21%:</td>
                    <td class="text-right">$<?php echo number_format($total_iva, 2, ',', '.'); ?></td>
                </tr>
                
                <tr style="border-top: 1px solid #000;">
                    <td class="text-left bold" style="font-size: 11px;">TOTAL:</td>
                    <td class="text-right bold" style="font-size: 11px;">$<?php echo number_format($venta['total'], 2, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <!-- FORMAS DE PAGO -->
        <?php if (!empty($pagos)): ?>
            <div class="condensed" style="margin-top: 5px;">
                <strong>FORMA DE PAGO:</strong>
                <?php 
                $total_pagos = 0;
                foreach ($pagos as $pago): 
                    $total_pagos += floatval($pago['monto']);
                ?>
                <div>
                    <?php echo htmlspecialchars($pago['metodo_nombre']); ?>: 
                    <span class="text-right">$<?php echo number_format($pago['monto'], 2, ',', '.'); ?></span>
                </div>
                <?php endforeach; ?>
                
                <table style="margin-top: 3px;">
                    <?php if ($total_pagos > 0): ?>
                    <tr>
                        <td class="text-left"><strong>Pagó:</strong></td>
                        <td class="text-right"><strong>$<?php echo number_format($total_pagos, 2, ',', '.'); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($total_pagos > $venta['total']): ?>
                    <tr>
                        <td class="text-left"><strong>Vuelto:</strong></td>
                        <td class="text-right"><strong>$<?php echo number_format($total_pagos - $venta['total'], 2, ',', '.'); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        <?php endif; ?>

        <!-- INDICADOR CUENTA CORRIENTE -->
        <?php if ($es_cuenta_corriente): ?>
        <div class="cuenta-corriente">
            ⚠️ COMPROBANTE CUENTA CORRIENTE
        </div>
        <?php endif; ?>

        <!-- PIE DE PÁGINA LEGAL -->
        <div class="legal-footer">
            <div style="margin-bottom: 3px;">
                <?php echo $config['ticket_mensaje'] ?? '¡Gracias por su compra!'; ?>
            </div>
            
            <?php if ($es_cuenta_corriente): ?>
            <div style="color: #d32f2f; font-weight: bold;">
                COMPROBANTE CUENTA CORRIENTE - CONSERVAR PARA CONTROL
            </div>
            <?php else: ?>
            <div>
                <strong>TICKET - NO VÁLIDO COMO FACTURA</strong>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 3px; font-size: 7px;">
                Conserve este comprobante para cambios o reclamos.<br>
                <?php if (!empty($config['negocio_telefono'])): ?>
                Tel: <?php echo $config['negocio_telefono']; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- BOTONES DE ACCIÓN (SOLO EN NAVEGADOR) -->
    <div class="no-print" style="text-align: center; margin-top: 20px; padding: 10px;">
        <button onclick="window.print()" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            🖨️ Imprimir Ticket
        </button>
        <button onclick="window.close()" style="background: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
            ❌ Cerrar
        </button>
    </div>

    <script>
        // Auto-imprimir si está configurado
        <?php if (($config['ticket_auto_print'] ?? '0') == '1'): ?>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        <?php endif; ?>
        
        // Mejorar experiencia de impresión
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>

</html>