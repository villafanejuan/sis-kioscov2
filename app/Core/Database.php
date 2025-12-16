<?php
/**
 * Clase Database - Singleton para conexión PDO
 */

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            Logger::error('Database connection failed', [
                'error' => $e->getMessage()
            ]);
            die('Error de conexión a la base de datos. Por favor contacte al administrador.');
        }
    }

    /**
     * Obtener instancia única de la base de datos
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener conexión PDO
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * Ejecutar query
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Logger::error('Database query failed', [
                'sql' => $sql,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obtener un registro
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los registros
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insertar registro y retornar ID
     */
    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $this->query($sql, $values);
        return $this->pdo->lastInsertId();
    }

    /**
     * Actualizar registro
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $fields = [];
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = "$field = ?";
            $values[] = $value;
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $fields),
            $where
        );

        $values = array_merge($values, $whereParams);
        $stmt = $this->query($sql, $values);
        return $stmt->rowCount();
    }

    /**
     * Eliminar registro
     */
    public function delete($table, $where, $whereParams = [])
    {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollback()
    {
        return $this->pdo->rollback();
    }

    /**
     * Prevenir clonación
     */
    private function __clone()
    {
    }

    /**
     * Prevenir deserialización
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
