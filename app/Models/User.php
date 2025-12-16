<?php
/**
 * Modelo User - Gestión de usuarios
 */

require_once __DIR__ . '/../Core/Model.php';

class User extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'password',
        'role_id',
        'nombre',
        'email',
        'telefono',
        'is_active'
    ];
    protected $hidden = ['password'];

    /**
     * Buscar usuario por username
     */
    public function findByUsername($username)
    {
        return $this->findWhere('username', $username);
    }

    /**
     * Buscar usuario por email
     */
    public function findByEmail($email)
    {
        return $this->findWhere('email', $email);
    }

    /**
     * Crear usuario con password hasheado
     */
    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

        return $this->create($data);
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = Security::hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Verificar credenciales
     */
    public function verifyCredentials($username, $password)
    {
        $user = $this->findByUsername($username);

        if (!$user) {
            return false;
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }

        // Verificar si necesita rehash
        if (Security::needsRehash($user['password'])) {
            $this->updatePassword($user['id'], $password);
        }

        return $user;
    }

    /**
     * Obtener usuarios activos
     */
    public function getActive()
    {
        return $this->where('is_active', 1);
    }

    /**
     * Obtener usuarios por rol
     */
    public function getByRole($roleId)
    {
        return $this->where('role_id', $roleId);
    }

    /**
     * Bloquear usuario
     */
    public function lockUser($userId, $minutes = 15)
    {
        $lockedUntil = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        return $this->update($userId, ['locked_until' => $lockedUntil]);
    }

    /**
     * Desbloquear usuario
     */
    public function unlockUser($userId)
    {
        return $this->update($userId, [
            'locked_until' => null,
            'failed_attempts' => 0
        ]);
    }

    /**
     * Verificar si usuario está bloqueado
     */
    public function isLocked($userId)
    {
        $user = $this->find($userId);

        if (!$user || !$user['locked_until']) {
            return false;
        }

        return strtotime($user['locked_until']) > time();
    }

    /**
     * Obtener permisos del usuario
     */
    public function getPermissions($userId)
    {
        $sql = "SELECT DISTINCT p.nombre, p.descripcion, p.modulo
                FROM permissions p
                INNER JOIN role_permissions rp ON p.id = rp.permission_id
                INNER JOIN usuarios u ON u.role_id = rp.role_id
                WHERE u.id = ?
                UNION
                SELECT p.nombre, p.descripcion, p.modulo
                FROM permissions p
                INNER JOIN user_permissions up ON p.id = up.permission_id
                WHERE up.user_id = ? AND up.granted = TRUE
                ORDER BY modulo, nombre";

        return $this->fetchAll($sql, [$userId, $userId]);
    }

    /**
     * Verificar si tiene permiso
     */
    public function hasPermission($userId, $permissionName)
    {
        $sql = "SELECT COUNT(*) as has_permission
                FROM (
                    SELECT p.id
                    FROM permissions p
                    INNER JOIN role_permissions rp ON p.id = rp.permission_id
                    INNER JOIN usuarios u ON u.role_id = rp.role_id
                    WHERE u.id = ? AND p.nombre = ?
                    UNION
                    SELECT p.id
                    FROM permissions p
                    INNER JOIN user_permissions up ON p.id = up.permission_id
                    WHERE up.user_id = ? AND up.granted = TRUE AND p.nombre = ?
                ) as perms";

        $result = $this->fetchOne($sql, [$userId, $permissionName, $userId, $permissionName]);
        return $result['has_permission'] > 0;
    }

    /**
     * Obtener con información de rol
     */
    public function getAllWithRole()
    {
        $sql = "SELECT u.*, r.nombre as role_nombre, r.nivel as role_nivel
                FROM usuarios u
                LEFT JOIN roles r ON u.role_id = r.id
                ORDER BY u.nombre";

        return $this->fetchAll($sql);
    }
}
