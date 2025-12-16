# GUÍA DE INSTALACIÓN RÁPIDA

## Paso 1: Copiar archivos
Asegúrate de que todos los archivos estén en:
```
C:\xampp\htdocs\sis-kiosco\
```

## Paso 2: Ejecutar migración de base de datos

### Opción A: Desde línea de comandos (Recomendado)
```bash
cd C:\xampp\htdocs\sis-kiosco
php database/migrations/migrate.php
```

### Opción B: Desde navegador
1. Abrir: http://localhost/sis-kiosco/database/migrations/migrate.php
2. Esperar a que termine la migración

## Paso 3: Configurar .env
1. Copiar `.env.example` a `.env`
2. Editar `.env` con tus datos:
```
DB_HOST=localhost
DB_NAME=kiosco_db
DB_USER=root
DB_PASS=
```

## Paso 4: Acceder al sistema
```
URL: http://localhost/sis-kiosco/public/
Usuario: admin
Contraseña: password
```

## Paso 5: Cambiar contraseña (IMPORTANTE)
1. Iniciar sesión
2. Ir a Perfil
3. Cambiar contraseña por defecto

## ✅ Verificación
Si todo está correcto, deberías ver:
- ✓ Página de login moderna
- ✓ Dashboard con estadísticas
- ✓ Menú de navegación completo

## ⚠️ Problemas Comunes

### Error de conexión a BD
- Verificar que XAMPP MySQL esté corriendo
- Verificar credenciales en `.env`

### Página en blanco
- Activar display_errors en php.ini
- Revisar logs en `storage/logs/`

### Error 404
- Verificar que mod_rewrite esté habilitado en Apache
- Verificar archivo `.htaccess` en public/

## 📞 Soporte
Revisar `README.md` para documentación completa.
