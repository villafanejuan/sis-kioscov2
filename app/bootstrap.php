<?php
/**
 * Bootstrap - Punto de entrada de la aplicación
 * Carga configuraciones, autoloader y maneja requests
 */

// Configuración de sesión segura (ANTES de session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Iniciar sesión
session_start();

// Cargar configuración
require_once __DIR__ . '/../config/app.php';

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Core/',
        APP_PATH . '/Models/',
        APP_PATH . '/Controllers/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Cargar funciones helper para compatibilidad
require_once __DIR__ . '/helpers.php';

// Crear directorios necesarios si no existen
$directories = [
    STORAGE_PATH . '/logs',
    STORAGE_PATH . '/cache',
    PUBLIC_PATH . '/uploads',
    BASE_PATH . '/database/backups'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Inicializar logger
Logger::init();

// Manejo de errores global
set_exception_handler(function ($exception) {
    Logger::error('Excepción no capturada', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);

    if (ENVIRONMENT === 'development') {
        echo "<h1>Error</h1>";
        echo "<p><strong>Mensaje:</strong> " . $exception->getMessage() . "</p>";
        echo "<p><strong>Archivo:</strong> " . $exception->getFile() . ":" . $exception->getLine() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        echo "Ha ocurrido un error. Por favor contacte al administrador.";
    }
});

// Verificar si hay modo mantenimiento
if (file_exists(STORAGE_PATH . '/maintenance.flag') && !isset($_SESSION['is_admin'])) {
    http_response_code(503);
    echo "Sistema en mantenimiento. Intenta más tarde.";
    exit;
}
