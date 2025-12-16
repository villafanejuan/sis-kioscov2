<?php
/**
 * Clase Security - Funciones de seguridad centralizadas
 */

class Security
{

    /**
     * Sanitizar input para prevenir XSS
     */
    public static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar CSRF token
     */
    public static function validateCsrf($token)
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $token !== $_SESSION[CSRF_TOKEN_NAME]) {
            Logger::warning('CSRF token inválido', [
                'expected' => $_SESSION[CSRF_TOKEN_NAME] ?? 'none',
                'received' => $token
            ]);
            throw new SecurityException('Token de seguridad inválido');
        }
        return true;
    }

    /**
     * Generar CSRF token
     */
    public static function generateCsrf()
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Regenerar CSRF token
     */
    public static function regenerateCsrf()
    {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Hash de contraseña seguro
     */
    public static function hashPassword($password)
    {
        // Usar Argon2id si está disponible, sino bcrypt
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 3
            ]);
        }
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verificar contraseña
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Verificar si la contraseña necesita rehash
     */
    public static function needsRehash($hash)
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_needs_rehash($hash, PASSWORD_ARGON2ID);
        }
        return password_needs_rehash($hash, PASSWORD_BCRYPT);
    }

    /**
     * Encriptar datos sensibles
     */
    public static function encrypt($data)
    {
        $key = base64_decode(str_replace('base64:', '', APP_KEY));
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Desencriptar datos
     */
    public static function decrypt($data)
    {
        $key = base64_decode(str_replace('base64:', '', APP_KEY));
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }

    /**
     * Validar y sanitizar email
     */
    public static function validateEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }

    /**
     * Validar entero
     */
    public static function validateInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * Validar decimal
     */
    public static function validateFloat($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Verificar intentos de login
     */
    public static function checkLoginAttempts($username)
    {
        $db = Database::getInstance();

        // Contar intentos fallidos en los últimos 15 minutos
        $sql = "SELECT COUNT(*) as attempts 
                FROM login_attempts 
                WHERE username = ? 
                AND success = FALSE 
                AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)";

        $result = $db->fetchOne($sql, [$username, LOCKOUT_DURATION]);

        if ($result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            Logger::warning('Usuario bloqueado por intentos fallidos', [
                'username' => $username,
                'attempts' => $result['attempts']
            ]);
            throw new SecurityException(
                'Demasiados intentos fallidos. Intenta nuevamente en ' . LOCKOUT_DURATION . ' minutos.'
            );
        }

        return true;
    }

    /**
     * Registrar intento de login
     */
    public static function logLoginAttempt($username, $success)
    {
        $db = Database::getInstance();

        $data = [
            'username' => $username,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'success' => $success ? 1 : 0
        ];

        $db->insert('login_attempts', $data);

        // Si fue exitoso, actualizar usuario
        if ($success) {
            $db->update(
                'usuarios',
                ['failed_attempts' => 0, 'last_login' => date('Y-m-d H:i:s')],
                'username = ?',
                [$username]
            );
        } else {
            // Incrementar intentos fallidos
            $db->query(
                "UPDATE usuarios SET failed_attempts = failed_attempts + 1 WHERE username = ?",
                [$username]
            );
        }
    }

    /**
     * Generar token aleatorio
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Sanitizar nombre de archivo
     */
    public static function sanitizeFilename($filename)
    {
        // Remover caracteres peligrosos
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        // Prevenir directory traversal
        $filename = str_replace(['..', '/', '\\'], '', $filename);
        return $filename;
    }

    /**
     * Validar tipo de archivo de imagen
     */
    public static function validateImageType($file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return in_array($mimeType, ALLOWED_IMAGE_TYPES);
    }
}

/**
 * Excepción de seguridad personalizada
 */
class SecurityException extends Exception
{
}
