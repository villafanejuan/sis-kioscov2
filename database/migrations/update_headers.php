<?php
/**
 * Script para actualizar headers de todas las páginas al estilo del dashboard
 */

echo "===========================================\n";
echo "  ACTUALIZANDO HEADERS\n";
echo "===========================================\n\n";

// Header del dashboard para copiar
$dashboardHeader = '    <!-- Navegación -->
    <nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-store text-2xl"></i>
                    <span class="text-xl font-bold"><?php echo APP_NAME; ?></span>
                </div>

                <div class="flex space-x-6">
                    <a href="dashboard.php"
                        class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="products.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">
                        <i class="fas fa-box mr-2"></i>Productos
                    </a>
                    <a href="sales.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">
                        <i class="fas fa-shopping-cart mr-2"></i>Ventas
                    </a>
                    <a href="cash.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">
                        <i class="fas fa-cash-register mr-2"></i>Caja
                    </a>
                    <a href="reports.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">
                        <i class="fas fa-chart-bar mr-2"></i>Reportes
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($_SESSION[\'nombre\'] ?? $_SESSION[\'username\']); ?>
                    </span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>';

// Función para actualizar header en un archivo
function updateHeader($file, $newHeader, $pageName)
{
    if (!file_exists($file)) {
        echo "⚠ No encontrado: " . basename($file) . "\n";
        return false;
    }

    $content = file_get_contents($file);

    // Buscar y reemplazar el nav existente
    $pattern = '/<nav[^>]*>.*?<\/nav>/s';

    if (preg_match($pattern, $content)) {
        // Personalizar header para esta página
        $customHeader = str_replace(
            'class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">',
            'class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition font-semibold">',
            $newHeader
        );

        // Marcar la página actual
        $customHeader = str_replace(
            'href="' . $pageName . '.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition',
            'href="' . $pageName . '.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition font-semibold',
            $customHeader
        );

        $newContent = preg_replace($pattern, $customHeader, $content);

        if ($newContent !== $content) {
            if (file_put_contents($file, $newContent)) {
                echo "✓ Header actualizado: " . basename($file) . "\n";
                return true;
            }
        }
    }

    echo "- Sin cambios en: " . basename($file) . "\n";
    return false;
}

// Actualizar archivos
$files = [
    ['path' => 'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/products.php', 'page' => 'products'],
    ['path' => 'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/sales.php', 'page' => 'sales'],
    ['path' => 'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/public/reports.php', 'page' => 'reports'],
];

$updated = 0;
foreach ($files as $file) {
    if (updateHeader($file['path'], $dashboardHeader, $file['page'])) {
        $updated++;
    }
}

echo "\n===========================================\n";
echo "  Headers actualizados: $updated\n";
echo "===========================================\n\n";
