<?php
/**
 * Script para actualizar todos los archivos PHP y reemplazar config.php por bootstrap.php
 */

echo "===========================================\n";
echo "  ACTUALIZANDO ARCHIVOS PHP\n";
echo "===========================================\n\n";

$files = [
    'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/products.php',
    'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/sales.php',
    'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/reports.php',
    'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/categories.php',
    'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/profile.php',
    'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/user_add.php'
];

$updated = 0;
$errors = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "⚠ Archivo no encontrado: $file\n";
        $errors++;
        continue;
    }

    $content = file_get_contents($file);

    // Reemplazar require_once 'config.php' por require_once __DIR__ . '/../app/bootstrap.php'
    $newContent = str_replace(
        "require_once 'config.php';",
        "require_once __DIR__ . '/../app/bootstrap.php';",
        $content
    );

    // Reemplazar session_start() y require en las primeras líneas
    $newContent = preg_replace(
        "/session_start\(\);\s*require_once 'config\.php';/",
        "require_once __DIR__ . '/../app/bootstrap.php';",
        $newContent
    );

    if ($newContent !== $content) {
        if (file_put_contents($file, $newContent)) {
            echo "✓ Actualizado: " . basename($file) . "\n";
            $updated++;
        } else {
            echo "✗ Error al escribir: " . basename($file) . "\n";
            $errors++;
        }
    } else {
        echo "- Sin cambios: " . basename($file) . "\n";
    }
}

echo "\n===========================================\n";
echo "  Archivos actualizados: $updated\n";
echo "  Errores: $errors\n";
echo "===========================================\n\n";

if ($updated > 0) {
    echo "Ahora puedes refrescar el navegador y probar las páginas.\n\n";
}
