<?php
/**
 * Archivo de configuración principal
 * Carga variables de entorno y configuraciones globales
 */

// Definir constantes de entorno
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'development'); // development, production
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CONFIG_PATH', BASE_PATH . '/config');

// Configuración de zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de errores según entorno
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');
}

// Cargar archivo .env si existe
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Configuración de base de datos
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'kiosco_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistema Kiosco Profesional');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('APP_KEY', $_ENV['APP_KEY'] ?? 'base64:' . base64_encode(random_bytes(32)));

// Configuración de seguridad
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 15); // minutos
define('SESSION_LIFETIME', 7200); // 2 horas en segundos
define('CSRF_TOKEN_NAME', 'csrf_token');

// Configuración de archivos
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

return [
    'app' => [
        'name' => APP_NAME,
        'url' => APP_URL,
        'environment' => ENVIRONMENT,
        'key' => APP_KEY,
    ],
    'database' => [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'charset' => DB_CHARSET,
    ],
    'security' => [
        'max_login_attempts' => MAX_LOGIN_ATTEMPTS,
        'lockout_duration' => LOCKOUT_DURATION,
        'session_lifetime' => SESSION_LIFETIME,
    ],
    'paths' => [
        'base' => BASE_PATH,
        'app' => APP_PATH,
        'public' => PUBLIC_PATH,
        'storage' => STORAGE_PATH,
        'logs' => STORAGE_PATH . '/logs',
        'cache' => STORAGE_PATH . '/cache',
        'uploads' => PUBLIC_PATH . '/uploads',
        'backups' => BASE_PATH . '/database/backups',
    ],
];
