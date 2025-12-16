<?php
/**
 * Clase Model - Modelo base para todas las entidades
 */

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todos los registros
     */
    public function all($orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->fetchAll($sql);
    }

    /**
     * Buscar por ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$id]);

        if ($result && !empty($this->hidden)) {
            foreach ($this->hidden as $field) {
                unset($result[$field]);
            }
        }

        return $result;
    }

    /**
     * Buscar por condición
     */
    public function where($field, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$field} {$operator} ?";
        return $this->db->fetchAll($sql, [$value]);
    }

    /**
     * Buscar un registro por condición
     */
    public function findWhere($field, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$field} {$operator} ? LIMIT 1";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Crear nuevo registro
     */
    public function create($data)
    {
        // Filtrar solo campos permitidos
        $filtered = $this->filterFillable($data);

        if (empty($filtered)) {
            throw new Exception('No hay datos válidos para insertar');
        }

        try {
            $id = $this->db->insert($this->table, $filtered);

            // Auditoría
            $this->logAudit('crear', $id, null, $filtered);

            return $this->find($id);
        } catch (PDOException $e) {
            Logger::error('Error al crear registro', [
                'table' => $this->table,
                'data' => $filtered,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar registro
     */
    public function update($id, $data)
    {
        // Obtener datos anteriores para auditoría
        $oldData = $this->find($id);

        if (!$oldData) {
            throw new Exception('Registro no encontrado');
        }

        // Filtrar solo campos permitidos
        $filtered = $this->filterFillable($data);

        if (empty($filtered)) {
            throw new Exception('No hay datos válidos para actualizar');
        }

        try {
            $affected = $this->db->update(
                $this->table,
                $filtered,
                "{$this->primaryKey} = ?",
                [$id]
            );

            // Auditoría
            $this->logAudit('editar', $id, $oldData, $filtered);

            return $this->find($id);
        } catch (PDOException $e) {
            Logger::error('Error al actualizar registro', [
                'table' => $this->table,
                'id' => $id,
                'data' => $filtered,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar registro
     */
    public function delete($id)
    {
        // Obtener datos para auditoría
        $oldData = $this->find($id);

        if (!$oldData) {
            throw new Exception('Registro no encontrado');
        }

        try {
            $affected = $this->db->delete(
                $this->table,
                "{$this->primaryKey} = ?",
                [$id]
            );

            // Auditoría
            $this->logAudit('eliminar', $id, $oldData, null);

            return $affected > 0;
        } catch (PDOException $e) {
            Logger::error('Error al eliminar registro', [
                'table' => $this->table,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Contar registros
     */
    public function count($where = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $result = $this->db->fetchOne($sql, $params);
        return (int) $result['total'];
    }

    /**
     * Verificar si existe
     */
    public function exists($field, $value)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$field} = ?";
        $result = $this->db->fetchOne($sql, [$value]);
        return $result['total'] > 0;
    }

    /**
     * Filtrar solo campos permitidos
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        $filtered = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $filtered[$field] = $data[$field];
            }
        }

        return $filtered;
    }

    /**
     * Registrar en auditoría
     */
    protected function logAudit($accion, $registroId, $datosAnteriores, $datosNuevos)
    {
        // Solo auditar si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        try {
            $this->db->insert('audit_logs', [
                'user_id' => $_SESSION['user_id'],
                'accion' => $accion,
                'modulo' => $this->table,
                'registro_id' => $registroId,
                'datos_anteriores' => $datosAnteriores ? json_encode($datosAnteriores) : null,
                'datos_nuevos' => $datosNuevos ? json_encode($datosNuevos) : null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // No fallar si falla la auditoría
            Logger::error('Error al registrar auditoría', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ejecutar query personalizada
     */
    protected function query($sql, $params = [])
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Fetch one personalizado
     */
    protected function fetchOne($sql, $params = [])
    {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Fetch all personalizado
     */
    protected function fetchAll($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
}
