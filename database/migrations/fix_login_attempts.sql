-- Script de corrección rápida para crear tabla login_attempts
-- Ejecutar este script en phpMyAdmin o MySQL

USE kiosco_db;

-- Crear tabla login_attempts si no existe
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    success BOOLEAN DEFAULT FALSE,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username_time (username, attempted_at),
    INDEX idx_ip_time (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar que se creó
SELECT 'Tabla login_attempts creada exitosamente' as status;
SHOW CREATE TABLE login_attempts;
