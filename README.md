# Sistema de Kiosco Profesional v2.0

Sistema completo de gestión para kioscos con arquitectura MVC, control de caja, gestión de stock, ventas, reportes y auditoría completa. **Optimizado para máxima velocidad y control financiero.**

## 🚀 Características Principales (Actualizado v2.0)

### ✅ Sistema de Roles y Permisos
- **Administrador**: Control total del sistema.
- **Kiosquero/Empleado**: Ventas, caja propia, administración de productos (alta/baja/modificación).
- **Cajero**: Rol de solo lectura para supervisión y auditoría de cajas ajenas.

### 👥 Gestión de Usuarios
- Alta, modificación y baja de usuarios.
- Asignación de roles (Admin, Kiosquero, Cajero, Empleado).
- Control de acceso por permisos.

### 💰 Cuentas Corrientes y Finanzas
- **Ventas Fiadas**: Soporte para Ventas a Crédito (Fiado) asociado a Clientes.
- **Pagos Parciales**: Posibilidad de recibir una seña y dejar el resto como deuda.
- **Cobro de Deudas**: Módulo "Clientes" para gestionar y registrar abonos de deuda.
- **Caja Unificada**: El dinero en caja refleja tanto ventas contado como abonos de deuda.

### 🏪 Punto de Venta Profesional
- **Pagos Mixtos**: Efectivo, Tarjeta, Transferencia y Cuenta Corriente.
- **Promociones**: Sistema automático de descuentos por producto o categoría.
- **Ticketera**: Interfaz de impresión optimizada (Hide UI elements).
- **Offline First**: Todas las librerías (Tailwind, FontAwesome, JS) son locales. Funciona sin internet.

### 📦 Gestión de Proveedores
- Alta, modificación y baja de proveedores.
- Contacto y datos fiscales de proveedores.

### 🎫 Sistema de Tickets
- Gestión de tickets/remitos.
- Seguimiento de entregas.

### 💳 Métodos de Pago
- Configuración de métodos de pago disponibles.
- Activar/desactivar métodos de pago.

### 📊 Gestión de Stock Inteligente
- **Reposición Rápida**: Botón "Quick Restock" directamente en el Dashboard para sumar stock sin navegar.
- **Alertas**: Indicadores visuales y filtros para "Stock Bajo".
- Control de inventario por producto.

### 📈 Reportes Unificados y Exportación
- **Historial Único**: Tabla cronológica que mezcla **VENTAS** y **ABONOS** para auditoría perfecta.
- **Métricas Reales**: Distinción clara entre "Total Facturado" (Ventas) y "Efectivo Ingresado" (Caja Real).
- **Exportación**: Generación nativa de **Excel** y **PDF** de todos los reportes.

### 🔒 Seguridad Avanzada
- Protección CSRF
- Protección contra fuerza bruta
- Encriptación de datos sensibles
- Auditoría completa de acciones
- Sesiones seguras
- Perfil de usuario con cambio de contraseña

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
│   ├── Helpers/          # Funciones helper
│   └── bootstrap.php      # Inicialización
├── config/                # Configuración
├── database/
│   └── migrations/        # Migraciones de BD
├── public/                # Punto de entrada público
│   ├── assets/            # CSS/JS Locales (Offline)
│   ├── index.php          # Login
│   ├── dashboard.php     # Dashboard con Quick Restock
│   ├── sales.php         # POS
│   ├── products.php      # Gestión de productos
│   ├── categories.php    # Categorías
│   ├── customers.php     # Clientes
│   ├── customer_account.php # Gestión Deudas
│   ├── cash.php          # Gestión de caja
│   ├── reports.php       # Reportes Unificados
│   ├── users.php         # Gestión de usuarios
│   ├── proveedores.php   # Gestión de proveedores
│   ├── tickets.php       # Sistema de tickets
│   ├── promotions.php    # Promociones
│   ├── payment_methods.php # Métodos de pago
│   ├── config.php        # Configuración del sistema
│   └── profile.php       # Perfil de usuario
├── storage/
│   ├── logs/              # Logs del sistema
│   └── backups/          # Backups automáticos
├── includes/              # Componentes compartidos
└── README.md
```

## 🎯 Uso Rápido

### Iniciar Sesión
1. Acceder a `http://localhost/sis-kiosco/public/`
2. Ingresar usuario y contraseña

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

### Gestionar Usuarios (Admin)
1. Ir a **Usuarios** (solo visible para Admin)
2. Alta, modificar roles o desactivar usuarios.

### Gestionar Proveedores
1. Ir a **Proveedores**
2. Agregar, modificar o eliminar proveedores.

### Crear Promociones
1. Ir a **Promos**
2. Crear descuentos por producto o categoría.

## 📊 Reportes Disponibles

- **Historial de Transacciones**: Visión unificada de ventas y cobros.
- **Ventas por Empleado**: Performance individual.
- **Productos Más Vendidos**: Top productos.
- **Stock Crítico**: Productos con stock bajo.
- **Reportes por Fecha**: Filtrar por rango de fechas.
- **Exportación**: Botones Excel/PDF en la esquina superior.

## 👤 Perfil de Usuario

- Ver información del perfil
- Cambiar contraseña
- Cerrar sesión

## ⌨️ Atajos de Teclado

El sistema cuenta con atajos de teclado para navegación rápida:

| Tecla | Sección |
|-------|---------|
| F1 | Dashboard |
| F2 | Productos |
| F3 | Categorías |
| F4 | Clientes |
| F5 | Ventas |
| F6 | Caja |

*Nota: Los atajos no funcionan cuando se está dentro de un campo de texto.*

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
**Última actualización**: Marzo 2026
