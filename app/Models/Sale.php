<?php
/**
 * Modelo Sale - Gestión de ventas
 */

require_once __DIR__ . '/../Core/Model.php';

class Sale extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ticket_numero',
        'usuario_id',
        'cliente_id',
        'cash_shift_id',
        'total',
        'tipo_pago',
        'monto_pagado',
        'cambio',
        'estado',
        'observaciones'
    ];

    /**
     * Crear venta completa con detalles
     */
    public function createSale($saleData, $items)
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Generar número de ticket
            $saleData['ticket_numero'] = $this->generateTicketNumber();
            $saleData['usuario_id'] = $_SESSION['user_id'];
            $saleData['estado'] = 'completada';

            // Crear venta
            $saleId = $db->insert($this->table, $saleData);

            // Insertar detalles y actualizar stock
            $productModel = new Product();
            foreach ($items as $item) {
                // Validar stock
                if (!$productModel->hasStock($item['producto_id'], $item['cantidad'])) {
                    throw new Exception("Stock insuficiente para el producto ID: {$item['producto_id']}");
                }

                // Insertar detalle
                $db->insert('venta_detalles', [
                    'venta_id' => $saleId,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['subtotal']
                ]);

                // Actualizar stock
                $productModel->updateStock(
                    $item['producto_id'],
                    -$item['cantidad'],
                    'venta',
                    "Venta #{$saleId}",
                    $saleId
                );
            }

            // Registrar movimiento de caja si hay turno abierto
            if (!empty($saleData['cash_shift_id'])) {
                $db->insert('cash_movements', [
                    'cash_shift_id' => $saleData['cash_shift_id'],
                    'tipo' => 'venta',
                    'monto' => $saleData['total'],
                    'concepto' => "Venta {$saleData['ticket_numero']}",
                    'referencia' => $saleId,
                    'user_id' => $_SESSION['user_id']
                ]);
            }

            $db->commit();

            Logger::info('Venta creada exitosamente', [
                'sale_id' => $saleId,
                'ticket' => $saleData['ticket_numero'],
                'total' => $saleData['total']
            ]);

            return $this->getSaleWithDetails($saleId);

        } catch (Exception $e) {
            $db->rollback();
            Logger::error('Error al crear venta', [
                'error' => $e->getMessage(),
                'data' => $saleData
            ]);
            throw $e;
        }
    }

    /**
     * Generar número de ticket
     */
    private function generateTicketNumber()
    {
        $prefix = 'T-';
        $sql = "SELECT MAX(CAST(SUBSTRING(ticket_numero, 3) AS UNSIGNED)) as last_number 
                FROM ventas 
                WHERE ticket_numero LIKE '{$prefix}%'";

        $result = $this->fetchOne($sql);
        $nextNumber = ($result['last_number'] ?? 0) + 1;

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener venta con detalles
     */
    public function getSaleWithDetails($saleId)
    {
        $sql = "SELECT v.*, 
                u.nombre as vendedor_nombre,
                c.nombre as cliente_nombre
                FROM ventas v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE v.id = ?";

        $sale = $this->fetchOne($sql, [$saleId]);

        if ($sale) {
            $sale['items'] = $this->getSaleItems($saleId);
        }

        return $sale;
    }

    /**
     * Obtener items de la venta
     */
    public function getSaleItems($saleId)
    {
        $sql = "SELECT vd.*, p.nombre as producto_nombre
                FROM venta_detalles vd
                INNER JOIN productos p ON vd.producto_id = p.id
                WHERE vd.venta_id = ?";

        return $this->fetchAll($sql, [$saleId]);
    }

    /**
     * Obtener ventas por fecha
     */
    public function getByDate($startDate, $endDate = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }

        $sql = "SELECT v.*, u.nombre as vendedor_nombre
                FROM ventas v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                WHERE DATE(v.fecha) BETWEEN ? AND ?
                ORDER BY v.fecha DESC";

        return $this->fetchAll($sql, [$startDate, $endDate]);
    }

    /**
     * Obtener ventas de hoy
     */
    public function getToday()
    {
        return $this->getByDate(date('Y-m-d'));
    }

    /**
     * Obtener ventas por usuario
     */
    public function getByUser($userId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT v.*, c.nombre as cliente_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE v.usuario_id = ?";

        $params = [$userId];

        if ($startDate) {
            $sql .= " AND DATE(v.fecha) >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(v.fecha) <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY v.fecha DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Obtener estadísticas de ventas
     */
    public function getStats($startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                COUNT(*) as total_ventas,
                SUM(total) as total_ingresos,
                AVG(total) as promedio_venta,
                MIN(total) as venta_minima,
                MAX(total) as venta_maxima
                FROM ventas
                WHERE estado = 'completada'";

        $params = [];

        if ($startDate) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $endDate;
        }

        return $this->fetchOne($sql, $params);
    }

    /**
     * Obtener ventas por hora (para gráficos)
     */
    public function getSalesByHour($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $sql = "SELECT 
                HOUR(fecha) as hora,
                COUNT(*) as cantidad_ventas,
                SUM(total) as total_ventas
                FROM ventas
                WHERE DATE(fecha) = ? AND estado = 'completada'
                GROUP BY HOUR(fecha)
                ORDER BY hora";

        return $this->fetchAll($sql, [$date]);
    }

    /**
     * Cancelar venta
     */
    public function cancelSale($saleId, $motivo)
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Obtener venta con items
            $sale = $this->getSaleWithDetails($saleId);

            if (!$sale) {
                throw new Exception('Venta no encontrada');
            }

            if ($sale['estado'] !== 'completada') {
                throw new Exception('Solo se pueden cancelar ventas completadas');
            }

            // Devolver stock
            $productModel = new Product();
            foreach ($sale['items'] as $item) {
                $productModel->updateStock(
                    $item['producto_id'],
                    $item['cantidad'],
                    'devolucion',
                    "Cancelación venta #{$saleId}: {$motivo}",
                    $saleId
                );
            }

            // Actualizar estado de venta
            $this->update($saleId, [
                'estado' => 'cancelada',
                'observaciones' => $motivo
            ]);

            // Registrar movimiento de caja negativo
            if ($sale['cash_shift_id']) {
                $db->insert('cash_movements', [
                    'cash_shift_id' => $sale['cash_shift_id'],
                    'tipo' => 'egreso',
                    'monto' => $sale['total'],
                    'concepto' => "Cancelación {$sale['ticket_numero']}: {$motivo}",
                    'referencia' => $saleId,
                    'user_id' => $_SESSION['user_id']
                ]);
            }

            $db->commit();

            Logger::info('Venta cancelada', [
                'sale_id' => $saleId,
                'motivo' => $motivo
            ]);

            return true;

        } catch (Exception $e) {
            $db->rollback();
            Logger::error('Error al cancelar venta', [
                'sale_id' => $saleId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
