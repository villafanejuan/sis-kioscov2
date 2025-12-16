# Sistema de Kiosco Profesional v2.0

Sistema completo de gestión para kioscos con arquitectura MVC, control de caja, gestión de stock, ventas, reportes y auditoría completa. **Optimizado para máxima velocidad y control financiero.**

## 🚀 Características Principales (Actualizado v2.0)

### ✅ Sistema de Roles y Permisos
- **Administrador**: Control total del sistema.
- **Kiosquero**: Ventas, caja propia, administración de productos (alta/baja/modificación).
- **Cajero (Auditor)**: Rol de solo lectura para supervisión y auditoría de cajas ajenas.

### 💰 Cuentas Corrientes y Finanzas
- **Ventas Fiadas**: Soporte para Ventas a Crédito (Fiado) asociado a Clientes.
- **Pagos Parciales**: Posibilidad de recibir una seña y dejar el resto como deuda.
- **Cobro de Deudas**: Módulo "Clientes" para gestionar y registrar abonos de deuda.
- **Caja Unificada**: El dinero en caja refleja tanto ventas contado como abonos de deuda.

### � Punto de Venta Profesional
- **Pagos Mixtos**: Efectivo, Tarjeta, Transferencia y Cuenta Corriente.
- **Promociones**: Sistema automático de descuentos.
- **Ticketera**: Interfaz de impresión optimizada (Hide UI elements).
- **Offline First**: Todas las librerías (Tailwind, FontAwesome, JS) son locales. Funciona sin internet.

### � Gestión de Stock Inteligente
- **Reposición Rápida**: Botón "Quick Restock" directamente en el Dashboard para sumar stock sin navegar.
- **Alertas**: Indicadores visuales y filtros para "Stock Bajo".

### 📊 Reportes Unificados y Exportación
- **Historial Único**: Tabla cronológica que mezcla **VENTAS** y **ABONOS** para auditoría perfecta.
- **Métricas Reales**: Distinción clara entre "Total Facturado" (Ventas) y "Efectivo Ingresado" (Caja Real).
- **Exportación**: Generación nativa de **Excel** y **PDF** de todos los reportes.

### 🔒 Seguridad Avanzada
- Protección CSRF
- Protección contra fuerza bruta
- Encriptación de datos sensibles
- Auditoría completa de acciones
- Sesiones seguras

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite
- Extensiones PHP: PDO, OpenSSL, JSON

## 🔧 Instalación

### 1. Clonar/Copiar archivos
```bash
# Copiar todos los archivos a tu directorio de XAMPP
# Ejemplo: C:\xampp\htdocs\sis-kiosco\
```

### 2. Configurar base de datos
Importar el archivo `kiosco_db.sql` incluido en la raíz.

### 3. Configurar variables de entorno
Copiar `.env.example` a `.env` y configurar:

```env
ENVIRONMENT=development
APP_URL=http://localhost/sis-kiosco/public
```

### 4. Acceder al sistema
```
URL: http://localhost/sis-kiosco/public/
Usuario: admin
Contraseña: password
```

## 📁 Estructura del Proyecto

```
sis-kiosco/
├── app/
│   ├── Core/              # Clases core
│   ├── Models/            # Modelos de datos
│   ├── Controllers/       # Controladores
│   └── bootstrap.php      # Inicialización
├── public/                # Punto de entrada público
│   ├── assets/            # CSS/JS Locales (Offline)
│   ├── index.php          # Login
│   ├── dashboard.php      # Dashboard con Quick Restock
│   ├── sales.php          # POS
│   ├── customer_account.php # Gestión Deudas
│   └── reports.php        # Reportes Unificados
├── storage/
│   ├── logs/              # Logs del sistema
└── README.md
```

## 🎯 Uso Rápido

### Abrir Turno de Caja
1. Ir a **Caja** → **Abrir Turno**
2. Ingresar monto inicial.

### Realizar una Venta
1. Ir a **Ventas** → **Nueva Venta**
2. Buscar productos y agregar al carrito.
3. Elegir cliente (si es Fiado) o Consumidor Final.
4. Confirmar pago.

### Reponer Stock (Nuevo)
1. Desde el **Dashboard**, buscar la lista "Stock Bajo".
2. Click en el botón **(+) Azul**.
3. Ingresar cantidad y confirmar. ¡Listo!

## 📊 Reportes Disponibles

- **Historial de Transacciones**: Visión unificada de ventas y cobros.
- **Ventas por Empleado**: Performance individual.
- **Productos Más Vendidos**: Top productos.
- **Stock Crítico**: Productos con stock bajo.
- **Exportación**: Botones Excel/PDF en la esquina superior.

## 🛠️ Mantenimiento

### Backups Automáticos
Los backups se generan automáticamente cada día a las 2 AM (configurar cron).

### Limpieza de Logs
Los logs se limpian automáticamente después de 30 días.

## 🔄 Actualización
Para actualizar el sistema:
1. Hacer backup completo
2. Copiar nuevos archivos
3. Limpiar caché

## 📞 Soporte
- Revisar logs en `storage/logs/`
- Consultar `manual_usuario_v2.md` en la documentación.

## 📜 Licencia
Sistema propietario para uso interno.

## 🎉 Características Futuras
- [ ] Integración con facturación electrónica
- [ ] App móvil para consultas
- [ ] Multi-tienda
- [ ] API REST

---

**Versión**: 2.0 Professional  
**Última actualización**: Diciembre 2025
