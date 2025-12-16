<?php
/**
 * Modelo Product - Gestión de productos
 */

require_once __DIR__ . '/../Core/Model.php';

class Product extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_barras',
        'precio',
        'costo',
        'stock',
        'stock_minimo',
        'categoria_id',
        'provider_id',
        'imagen',
        'is_active'
    ];

    /**
     * Obtener productos activos
     */
    public function getActive()
    {
        return $this->where('is_active', 1);
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStock()
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, pr.nombre as proveedor_nombre
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN providers pr ON p.provider_id = pr.id
                WHERE p.stock < p.stock_minimo AND p.is_active = 1
                ORDER BY p.stock ASC";

        return $this->fetchAll($sql);
    }

    /**
     * Buscar por código de barras
     */
    public function findByBarcode($barcode)
    {
        return $this->findWhere('codigo_barras', $barcode);
    }

    /**
     * Buscar productos (búsqueda)
     */
    public function search($query)
    {
        $sql = "SELECT p.*, c.nombre as categoria_nombre
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.is_active = 1 
                AND (p.nombre LIKE ? OR p.codigo_barras LIKE ? OR p.descripcion LIKE ?)
                ORDER BY p.nombre
                LIMIT 20";

        $searchTerm = "%{$query}%";
        return $this->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }

    /**
     * Obtener productos con información completa
     */
    public function getAllWithDetails()
    {
        $sql = "SELECT p.*, 
                c.nombre as categoria_nombre,
                pr.nombre as proveedor_nombre,
                CASE WHEN p.stock < p.stock_minimo THEN 1 ELSE 0 END as stock_bajo
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN providers pr ON p.provider_id = pr.id
                ORDER BY p.nombre";

        return $this->fetchAll($sql);
    }

    /**
     * Obtener por categoría
     */
    public function getByCategory($categoryId)
    {
        return $this->where('categoria_id', $categoryId);
    }

    /**
     * Actualizar stock
     */
    public function updateStock($productId, $quantity, $type, $motivo, $referencia = null)
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Obtener stock actual
            $product = $this->find($productId);
            if (!$product) {
                throw new Exception('Producto no encontrado');
            }

            $stockAnterior = $product['stock'];
            $stockNuevo = $stockAnterior + $quantity;

            // Validar que no quede negativo
            if ($stockNuevo < 0) {
                throw new Exception('Stock insuficiente');
            }

            // Actualizar stock
            $this->update($productId, ['stock' => $stockNuevo]);

            // Registrar movimiento
            $db->insert('stock_movements', [
                'producto_id' => $productId,
                'tipo' => $type,
                'cantidad' => $quantity,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'referencia' => $referencia,
                'user_id' => $_SESSION['user_id'] ?? 1
            ]);

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollback();
            Logger::error('Error al actualizar stock', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener historial de movimientos de stock
     */
    public function getStockHistory($productId, $limit = 50)
    {
        $sql = "SELECT sm.*, u.nombre as usuario_nombre, p.nombre as producto_nombre
                FROM stock_movements sm
                INNER JOIN usuarios u ON sm.user_id = u.id
                INNER JOIN productos p ON sm.producto_id = p.id
                WHERE sm.producto_id = ?
                ORDER BY sm.created_at DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$productId, $limit]);
    }

    /**
     * Obtener productos más vendidos
     */
    public function getTopSelling($days = 30, $limit = 10)
    {
        $sql = "SELECT p.*, 
                SUM(vd.cantidad) as total_vendido,
                COUNT(DISTINCT v.id) as num_ventas
                FROM productos p
                INNER JOIN venta_detalles vd ON p.id = vd.producto_id
                INNER JOIN ventas v ON vd.venta_id = v.id
                WHERE v.fecha >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND v.estado = 'completada'
                GROUP BY p.id
                ORDER BY total_vendido DESC
                LIMIT ?";

        return $this->fetchAll($sql, [$days, $limit]);
    }

    /**
     * Calcular margen de ganancia
     */
    public function calculateMargin($precio, $costo)
    {
        if ($costo <= 0) {
            return 0;
        }
        return (($precio - $costo) / $costo) * 100;
    }

    /**
     * Validar stock disponible
     */
    public function hasStock($productId, $quantity)
    {
        $product = $this->find($productId);
        return $product && $product['stock'] >= $quantity;
    }
}
