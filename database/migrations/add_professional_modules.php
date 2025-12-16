<?php
/**
 * Migración: Módulos Profesionales
 * - Gestión de Clientes
 * - Múltiples Métodos de Pago
 * - Sistema de Promociones
 * - Configuración del Negocio
 */

require_once __DIR__ . '/../../app/bootstrap.php';

try {
    echo "=== Migración: Módulos Profesionales ===\n\n";

    // 1. TABLA CLIENTES
    echo "1. Creando tabla 'clientes'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS clientes (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(20) UNIQUE,
            email VARCHAR(100),
            direccion TEXT,
            saldo_cuenta DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Positivo=a favor, Negativo=deuda',
            limite_credito DECIMAL(10,2) DEFAULT 0.00,
            notas TEXT,
            activo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_telefono (telefono),
            INDEX idx_nombre (nombre)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'clientes' creada\n\n";

    // 2. TABLA MÉTODOS DE PAGO
    echo "2. Creando tabla 'metodos_pago'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS metodos_pago (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL,
            requiere_referencia BOOLEAN DEFAULT FALSE,
            activo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'metodos_pago' creada\n\n";

    // 3. TABLA VENTA_PAGOS (para pagos mixtos)
    echo "3. Creando tabla 'venta_pagos'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS venta_pagos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            venta_id INT NOT NULL,
            metodo_pago_id INT NOT NULL,
            monto DECIMAL(10,2) NOT NULL,
            referencia VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
            FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id),
            INDEX idx_venta (venta_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'venta_pagos' creada\n\n";

    // 4. TABLA PROMOCIONES
    echo "4. Creando tabla 'promociones'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS promociones (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            tipo ENUM('descuento_porcentaje', 'descuento_fijo', 'nxm', 'precio_especial', 'combo') NOT NULL,
            valor DECIMAL(10,2) NOT NULL COMMENT 'Porcentaje, monto fijo, o precio especial',
            valor_extra VARCHAR(10) COMMENT 'Para NxM: ej 2x1, 3x2',
            categoria_id INT NULL,
            fecha_inicio DATE,
            fecha_fin DATE,
            dias_semana VARCHAR(20) COMMENT 'JSON array de días: [1,2,3,4,5,6,7]',
            hora_inicio TIME,
            hora_fin TIME,
            activo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
            INDEX idx_activo (activo),
            INDEX idx_fechas (fecha_inicio, fecha_fin)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'promociones' creada\n\n";

    // 5. TABLA PROMOCION_PRODUCTOS (relación muchos a muchos)
    echo "5. Creando tabla 'promocion_productos'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS promocion_productos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            promocion_id INT NOT NULL,
            producto_id INT NOT NULL,
            FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE,
            FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
            UNIQUE KEY unique_promo_producto (promocion_id, producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'promocion_productos' creada\n\n";

    // 6. TABLA VENTA_DESCUENTOS (registro de descuentos aplicados)
    echo "6. Creando tabla 'venta_descuentos'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS venta_descuentos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            venta_id INT NOT NULL,
            promocion_id INT NULL,
            tipo VARCHAR(50),
            descripcion VARCHAR(200),
            monto_descuento DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
            FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'venta_descuentos' creada\n\n";

    // 7. TABLA CONFIGURACION (datos del negocio)
    echo "7. Creando tabla 'configuracion'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS configuracion (
            id INT PRIMARY KEY AUTO_INCREMENT,
            clave VARCHAR(50) UNIQUE NOT NULL,
            valor TEXT,
            descripcion VARCHAR(200),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ Tabla 'configuracion' creada\n\n";

    // 8. MODIFICAR TABLA VENTAS
    echo "8. Modificando tabla 'ventas'...\n";

    // Verificar si la columna ya existe
    $result = $pdo->query("SHOW COLUMNS FROM ventas LIKE 'cliente_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE ventas ADD COLUMN cliente_id INT NULL AFTER usuario_id");
        $pdo->exec("ALTER TABLE ventas ADD FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL");
        echo "   ✓ Columna 'cliente_id' agregada\n";
    } else {
        echo "   ⚠ Columna 'cliente_id' ya existe\n";
    }

    $result = $pdo->query("SHOW COLUMNS FROM ventas LIKE 'descuento_total'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE ventas ADD COLUMN descuento_total DECIMAL(10,2) DEFAULT 0.00 AFTER total");
        echo "   ✓ Columna 'descuento_total' agregada\n";
    } else {
        echo "   ⚠ Columna 'descuento_total' ya existe\n";
    }

    $result = $pdo->query("SHOW COLUMNS FROM ventas LIKE 'subtotal'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE ventas ADD COLUMN subtotal DECIMAL(10,2) DEFAULT 0.00 AFTER descuento_total");
        echo "   ✓ Columna 'subtotal' agregada\n";
    } else {
        echo "   ⚠ Columna 'subtotal' ya existe\n";
    }
    echo "\n";

    // 9. POBLAR MÉTODOS DE PAGO
    echo "9. Poblando métodos de pago por defecto...\n";
    $metodos = [
        ['Efectivo', 0],
        ['Tarjeta Débito', 1],
        ['Tarjeta Crédito', 1],
        ['Transferencia', 1],
        ['Cuenta Corriente', 0]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO metodos_pago (nombre, requiere_referencia) VALUES (?, ?)");
    foreach ($metodos as $metodo) {
        $stmt->execute($metodo);
    }
    echo "   ✓ Métodos de pago insertados\n\n";

    // 10. POBLAR CONFIGURACIÓN
    echo "10. Poblando configuración del negocio...\n";
    $configs = [
        ['negocio_nombre', 'Mi Kiosco', 'Nombre del negocio'],
        ['negocio_direccion', 'Calle Principal 123', 'Dirección del negocio'],
        ['negocio_telefono', '123-456-7890', 'Teléfono del negocio'],
        ['negocio_email', 'contacto@mikiosco.com', 'Email del negocio'],
        ['ticket_mensaje', '¡Gracias por su compra!', 'Mensaje en el ticket'],
        ['ticket_auto_print', '1', 'Imprimir ticket automáticamente (1=sí, 0=no)']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES (?, ?, ?)");
    foreach ($configs as $config) {
        $stmt->execute($config);
    }
    echo "   ✓ Configuración insertada\n\n";

    echo "=== ✓ MIGRACIÓN COMPLETADA EXITOSAMENTE ===\n";
    echo "\nNuevas funcionalidades disponibles:\n";
    echo "  • Gestión de Clientes\n";
    echo "  • Múltiples Métodos de Pago\n";
    echo "  • Sistema de Promociones\n";
    echo "  • Impresión de Tickets\n\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
