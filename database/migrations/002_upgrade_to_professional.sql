-- ============================================
-- MIGRACIÓN: Sistema Kiosco Profesional
-- Versión: 2.0
-- Fecha: 2025-12-11
-- ============================================
-- Este script actualiza la base de datos existente
-- agregando todas las funcionalidades profesionales
-- ============================================

USE kiosco_db;

-- ============================================
-- 1. SISTEMA DE ROLES Y PERMISOS
-- ============================================

-- Tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    nivel INT NOT NULL COMMENT '1=Admin, 2=Kiosquero, 3=Cajero',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de permisos
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    modulo VARCHAR(50) NOT NULL COMMENT 'productos, ventas, caja, reportes, usuarios, sistema',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relación roles-permisos
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos individuales de usuario (opcional)
CREATE TABLE IF NOT EXISTS user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted BOOLEAN DEFAULT TRUE COMMENT 'TRUE=concedido, FALSE=revocado',
    granted_by INT COMMENT 'ID del admin que otorgó el permiso',
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES usuarios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_permission (user_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. ACTUALIZAR TABLA DE USUARIOS
-- ============================================

-- Agregar nuevos campos a usuarios
ALTER TABLE usuarios 
    ADD COLUMN IF NOT EXISTS role_id INT AFTER role,
    ADD COLUMN IF NOT EXISTS email VARCHAR(100) AFTER nombre,
    ADD COLUMN IF NOT EXISTS telefono VARCHAR(20) AFTER email,
    ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER telefono,
    ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL AFTER is_active,
    ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0 AFTER last_login,
    ADD COLUMN IF NOT EXISTS locked_until TIMESTAMP NULL AFTER failed_attempts,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Agregar índices
ALTER TABLE usuarios ADD INDEX IF NOT EXISTS idx_username (username);
ALTER TABLE usuarios ADD INDEX IF NOT EXISTS idx_role_id (role_id);
ALTER TABLE usuarios ADD INDEX IF NOT EXISTS idx_active (is_active);

-- Tabla de intentos de login
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

-- ============================================
-- 3. MÓDULO DE CAJA
-- ============================================

-- Cajas físicas
CREATE TABLE IF NOT EXISTS cash_registers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Turnos de caja
CREATE TABLE IF NOT EXISTS cash_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cash_register_id INT NOT NULL,
    user_id INT NOT NULL,
    monto_inicial DECIMAL(10,2) NOT NULL,
    monto_final DECIMAL(10,2),
    monto_esperado DECIMAL(10,2),
    diferencia DECIMAL(10,2),
    estado ENUM('abierto', 'cerrado_parcial', 'cerrado') DEFAULT 'abierto',
    observaciones TEXT,
    opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id),
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX idx_user_date (user_id, opened_at),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Movimientos de caja
CREATE TABLE IF NOT EXISTS cash_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cash_shift_id INT NOT NULL,
    tipo ENUM('ingreso', 'egreso', 'venta', 'apertura', 'cierre') NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    referencia VARCHAR(100) COMMENT 'ID de venta si es venta',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cash_shift_id) REFERENCES cash_shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX idx_shift_tipo (cash_shift_id, tipo),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. MÓDULO DE STOCK AVANZADO
-- ============================================

-- Proveedores
CREATE TABLE IF NOT EXISTS providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Actualizar tabla de productos
ALTER TABLE productos 
    ADD COLUMN IF NOT EXISTS codigo_barras VARCHAR(50) UNIQUE AFTER descripcion,
    ADD COLUMN IF NOT EXISTS costo DECIMAL(10,2) DEFAULT 0 AFTER precio,
    ADD COLUMN IF NOT EXISTS margen DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE WHEN costo > 0 THEN ((precio - costo) / costo * 100) ELSE 0 END
    ) STORED COMMENT 'Margen de ganancia en %' AFTER costo,
    ADD COLUMN IF NOT EXISTS stock_minimo INT DEFAULT 5 AFTER stock,
    ADD COLUMN IF NOT EXISTS provider_id INT AFTER categoria_id,
    ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER imagen,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Agregar foreign key para provider
ALTER TABLE productos ADD CONSTRAINT fk_productos_provider 
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE SET NULL;

-- Agregar índices
ALTER TABLE productos ADD INDEX IF NOT EXISTS idx_nombre (nombre);
ALTER TABLE productos ADD INDEX IF NOT EXISTS idx_codigo_barras (codigo_barras);
ALTER TABLE productos ADD INDEX IF NOT EXISTS idx_categoria (categoria_id);
ALTER TABLE productos ADD INDEX IF NOT EXISTS idx_stock_bajo (stock, stock_minimo);

-- Movimientos de stock
CREATE TABLE IF NOT EXISTS stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo ENUM('venta', 'ajuste', 'merma', 'ingreso', 'devolucion') NOT NULL,
    cantidad INT NOT NULL COMMENT 'Positivo=ingreso, Negativo=egreso',
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    motivo VARCHAR(255),
    referencia VARCHAR(100) COMMENT 'ID de venta, merma, etc.',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX idx_producto_fecha (producto_id, created_at),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mermas
CREATE TABLE IF NOT EXISTS wastages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    motivo ENUM('vencido', 'roto', 'perdido', 'otro') NOT NULL,
    descripcion TEXT NOT NULL,
    costo_total DECIMAL(10,2),
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX idx_fecha (created_at),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. MÓDULO DE VENTAS MEJORADO
-- ============================================

-- Actualizar tabla de ventas
ALTER TABLE ventas 
    ADD COLUMN IF NOT EXISTS ticket_numero VARCHAR(20) UNIQUE AFTER id,
    ADD COLUMN IF NOT EXISTS cash_shift_id INT AFTER cliente_id,
    ADD COLUMN IF NOT EXISTS tipo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'mixto') DEFAULT 'efectivo' AFTER total,
    ADD COLUMN IF NOT EXISTS estado ENUM('completada', 'cancelada', 'devuelta') DEFAULT 'completada' AFTER cambio,
    ADD COLUMN IF NOT EXISTS observaciones TEXT AFTER estado;

-- Agregar foreign key para cash_shift
ALTER TABLE ventas ADD CONSTRAINT fk_ventas_cash_shift 
    FOREIGN KEY (cash_shift_id) REFERENCES cash_shifts(id) ON DELETE SET NULL;

-- Agregar índices
ALTER TABLE ventas ADD INDEX IF NOT EXISTS idx_usuario_fecha (usuario_id, fecha);
ALTER TABLE ventas ADD INDEX IF NOT EXISTS idx_fecha (fecha);
ALTER TABLE ventas ADD INDEX IF NOT EXISTS idx_ticket (ticket_numero);

-- Ventas en espera
CREATE TABLE IF NOT EXISTS pending_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_venta VARCHAR(100) NOT NULL,
    usuario_id INT NOT NULL,
    cliente_id INT,
    total_parcial DECIMAL(10,2),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pending_sale_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pending_sale_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pending_sale_id) REFERENCES pending_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. SISTEMA DE AUDITORÍA
-- ============================================

-- Logs de auditoría
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    accion VARCHAR(100) NOT NULL COMMENT 'crear, editar, eliminar, login, etc.',
    modulo VARCHAR(50) NOT NULL COMMENT 'productos, ventas, usuarios, etc.',
    registro_id INT COMMENT 'ID del registro afectado',
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_user_fecha (user_id, created_at),
    INDEX idx_modulo_accion (modulo, accion),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. SISTEMA DE BACKUPS
-- ============================================

-- Registro de backups
CREATE TABLE IF NOT EXISTS system_backups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    size_bytes BIGINT,
    tipo ENUM('manual', 'automatico') DEFAULT 'manual',
    user_id INT COMMENT 'NULL si es automático',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. CONFIGURACIONES DEL SISTEMA
-- ============================================

-- Configuraciones
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    descripcion TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. DATOS INICIALES
-- ============================================

-- Insertar roles
INSERT INTO roles (nombre, descripcion, nivel) VALUES
('Administrador', 'Acceso completo al sistema', 1),
('Kiosquero', 'Puede realizar ventas, abrir/cerrar caja, ver reportes básicos', 2),
('Cajero', 'Solo puede realizar ventas', 3)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- Insertar permisos
INSERT INTO permissions (nombre, descripcion, modulo) VALUES
-- Dashboard
('view_dashboard', 'Ver dashboard', 'dashboard'),
('view_metrics', 'Ver métricas avanzadas', 'dashboard'),

-- Productos
('view_products', 'Ver productos', 'productos'),
('create_product', 'Crear productos', 'productos'),
('edit_product', 'Editar productos', 'productos'),
('delete_product', 'Eliminar productos', 'productos'),
('view_product_cost', 'Ver costo de productos', 'productos'),
('edit_product_price', 'Editar precio de productos', 'productos'),

-- Stock
('view_stock', 'Ver stock', 'stock'),
('edit_stock', 'Editar stock', 'stock'),
('view_stock_history', 'Ver historial de stock', 'stock'),
('import_stock', 'Importar stock masivo', 'stock'),
('register_wastage', 'Registrar mermas', 'stock'),
('manage_providers', 'Gestionar proveedores', 'stock'),

-- Ventas
('view_sales', 'Ver ventas', 'ventas'),
('create_sale', 'Crear ventas', 'ventas'),
('cancel_sale', 'Cancelar ventas', 'ventas'),
('view_all_sales', 'Ver todas las ventas', 'ventas'),
('process_return', 'Procesar devoluciones', 'ventas'),
('save_pending_sale', 'Guardar ventas en espera', 'ventas'),

-- Caja
('open_cash', 'Abrir caja', 'caja'),
('close_cash', 'Cerrar caja', 'caja'),
('close_cash_partial', 'Cierre parcial de caja', 'caja'),
('manual_cash_movement', 'Movimientos manuales de caja', 'caja'),
('view_cash_history', 'Ver historial de caja', 'caja'),
('view_all_cash', 'Ver todas las cajas', 'caja'),

-- Reportes
('view_basic_reports', 'Ver reportes básicos', 'reportes'),
('view_advanced_reports', 'Ver reportes avanzados', 'reportes'),
('view_financial_reports', 'Ver reportes financieros', 'reportes'),
('export_reports', 'Exportar reportes', 'reportes'),

-- Usuarios
('view_users', 'Ver usuarios', 'usuarios'),
('create_user', 'Crear usuarios', 'usuarios'),
('edit_user', 'Editar usuarios', 'usuarios'),
('delete_user', 'Eliminar usuarios', 'usuarios'),
('manage_roles', 'Gestionar roles', 'usuarios'),
('manage_permissions', 'Gestionar permisos', 'usuarios'),

-- Sistema
('view_audit_logs', 'Ver logs de auditoría', 'sistema'),
('manage_backups', 'Gestionar backups', 'sistema'),
('system_settings', 'Configurar sistema', 'sistema'),
('view_system_health', 'Ver estado del sistema', 'sistema')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- Asignar permisos a Administrador (todos)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id);

-- Asignar permisos a Kiosquero
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE nombre IN (
    'view_dashboard', 'view_metrics',
    'view_products', 'view_stock',
    'view_sales', 'create_sale', 'save_pending_sale',
    'open_cash', 'close_cash', 'close_cash_partial', 'manual_cash_movement', 'view_cash_history',
    'view_basic_reports', 'export_reports'
)
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id);

-- Asignar permisos a Cajero
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE nombre IN (
    'view_dashboard',
    'view_products',
    'view_sales', 'create_sale'
)
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id);

-- Migrar usuarios existentes a roles
UPDATE usuarios SET role_id = 1 WHERE role = 'admin' AND role_id IS NULL;
UPDATE usuarios SET role_id = 3 WHERE role = 'empleado' AND role_id IS NULL;

-- Insertar caja por defecto
INSERT INTO cash_registers (nombre, ubicacion, is_active) VALUES
('Caja Principal', 'Mostrador principal', TRUE)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Insertar configuraciones por defecto
INSERT INTO system_settings (setting_key, setting_value, setting_type, descripcion) VALUES
('stock_alert_threshold', '10', 'number', 'Umbral de alerta de stock bajo'),
('backup_retention_days', '30', 'number', 'Días de retención de backups'),
('max_login_attempts', '5', 'number', 'Intentos máximos de login'),
('lockout_duration_minutes', '15', 'number', 'Duración de bloqueo en minutos'),
('ticket_prefix', 'T-', 'string', 'Prefijo para números de ticket'),
('business_name', 'Mi Kiosco', 'string', 'Nombre del negocio'),
('business_address', '', 'string', 'Dirección del negocio'),
('business_phone', '', 'string', 'Teléfono del negocio')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

-- ============================================
-- MIGRACIÓN COMPLETADA
-- ============================================
-- La base de datos ha sido actualizada exitosamente
-- con todas las funcionalidades profesionales
-- ============================================
