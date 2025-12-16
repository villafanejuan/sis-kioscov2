<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();
$userName = $_SESSION['nombre'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Header</title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navegación EXACTA del dashboard -->
    <nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-store text-2xl"></i>
                    <span class="text-xl font-bold"><?php echo APP_NAME; ?></span>
                </div>

                <div class="flex space-x-6">
                    <a href="dashboard.php" class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="products.php"
                        class="hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded transition font-semibold">
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
                        <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($userName); ?>
                    </span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                        <i class="fas fa-sign-out-alt mr-2"></i>Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold">Test Header - Productos</h1>
        <p class="mt-4">Si este header se ve del mismo tamaño que el dashboard, entonces el problema es el caché del
            navegador.</p>
        <p class="mt-2">Presiona <strong>Ctrl + Shift + R</strong> para limpiar el caché y recargar.</p>
    </div>
</body>

</html>