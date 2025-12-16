<?php
/**
 * Script para eliminar session_start() duplicado de archivos PHP
 */

echo "===========================================\n";
echo "  ELIMINANDO session_start() DUPLICADOS\n";
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

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "⚠ No encontrado: " . basename($file) . "\n";
        continue;
    }

    $content = file_get_contents($file);

    // Eliminar línea session_start(); que está antes de require_once bootstrap
    $newContent = preg_replace(
        "/\<\?php\s*\n\s*session_start\(\);\s*\n\s*require_once/",
        "<?php\nrequire_once",
        $content
    );

    if ($newContent !== $content) {
        if (file_put_contents($file, $newContent)) {
            echo "✓ Eliminado session_start() de: " . basename($file) . "\n";
            $updated++;
        } else {
            echo "✗ Error en: " . basename($file) . "\n";
        }
    } else {
        echo "- Sin cambios en: " . basename($file) . "\n";
    }
}

echo "\n===========================================\n";
echo "  Archivos actualizados: $updated\n";
echo "===========================================\n\n";
