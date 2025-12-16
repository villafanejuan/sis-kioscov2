<?php
/**
 * Clase Logger - Sistema de logs estructurado
 */

class Logger
{
    private static $logPath;

    /**
     * Inicializar logger
     */
    public static function init()
    {
        self::$logPath = STORAGE_PATH . '/logs/';

        // Crear directorio si no existe
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
    }

    /**
     * Log de información
     */
    public static function info($message, $context = [])
    {
        self::log('INFO', $message, $context);
    }

    /**
     * Log de error
     */
    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context);
    }

    /**
     * Log de advertencia
     */
    public static function warning($message, $context = [])
    {
        self::log('WARNING', $message, $context);
    }

    /**
     * Log de debug (solo en desarrollo)
     */
    public static function debug($message, $context = [])
    {
        if (ENVIRONMENT === 'development') {
            self::log('DEBUG', $message, $context);
        }
    }

    /**
     * Método principal de logging
     */
    private static function log($level, $message, $context)
    {
        if (self::$logPath === null) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'] ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $logEntry = sprintf(
            "[%s] [%s] [User:%s] [IP:%s] %s %s\n",
            $timestamp,
            $level,
            $userId,
            $ip,
            $message,
            !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );

        $filename = self::$logPath . strtolower($level) . '_' . date('Y-m-d') . '.log';
        file_put_contents($filename, $logEntry, FILE_APPEND);

        // También escribir en log general
        $generalLog = self::$logPath . 'app_' . date('Y-m-d') . '.log';
        file_put_contents($generalLog, $logEntry, FILE_APPEND);
    }

    /**
     * Limpiar logs antiguos
     */
    public static function cleanup($days = 30)
    {
        $files = glob(self::$logPath . '*.log');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                }
            }
        }
    }
}

// Inicializar logger
Logger::init();
