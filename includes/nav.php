<?php
/**
 * Componente de navegación con control de acceso basado en roles
 * Uso: include __DIR__ . '/../includes/nav.php';
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userName = $_SESSION['nombre'] ?? $_SESSION['username'];
?>
<nav class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg sticky top-0 z-50">
    <div class="w-full px-6">
        <div class="flex justify-between items-center h-16"> <!-- Compact height -->
            <!-- Brand - Pinned to the Left -->
            <div class="flex-shrink-0 flex items-center pr-4">
                <i class="fas fa-store text-xl sm:text-2xl mr-2 text-yellow-300"></i>
                <span
                    class="text-lg sm:text-xl font-bold tracking-wide whitespace-nowrap"><?php echo APP_NAME; ?></span>
            </div>

            <!-- Main Navigation - Compact -->
            <div
                class="hidden md:flex flex-1 justify-center items-center space-x-2 lg:space-x-6 overflow-x-auto no-scrollbar">
                <?php if (canAccess('dashboard')): ?>
                    <a href="dashboard.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'dashboard' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-home mr-3 text-xl"></i>Dashboard
                    </a>
                <?php endif; ?>

                <?php if (canAccess('products')): ?>
                    <a href="products.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'products' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-box mr-3 text-xl"></i>Productos
                    </a>
                <?php endif; ?>

                <?php if (canAccess('categories')): ?>
                    <a href="categories.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'categories' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-tags mr-3 text-xl"></i>Categorías
                    </a>
                <?php endif; ?>

                <?php if (canAccess('products')): ?>
                    <a href="customers.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'customers' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-address-book mr-3 text-xl"></i>Clientes
                    </a>
                <?php endif; ?>

                <?php if (canAccess('sales')): ?>
                    <a href="sales.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'sales' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-shopping-cart mr-3 text-xl"></i>Ventas
                    </a>
                <?php endif; ?>

                <?php if (canAccess('cash')): ?>
                    <a href="cash.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'cash' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-cash-register mr-3 text-xl"></i>Caja
                    </a>
                <?php endif; ?>

                <?php if (checkAdmin() || $_SESSION['role'] === 'kiosquero'): ?>
                    <a href="promotions.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'promotions' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-percent mr-3 text-xl"></i>Promos
                    </a>
                <?php endif; ?>

                <?php if (canAccess('reports')): ?>
                    <a href="reports.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'reports' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-chart-bar mr-3 text-xl"></i>Reportes
                    </a>
                <?php endif; ?>

                <?php if (canAccess('users')): ?>
                    <a href="users.php"
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition whitespace-nowrap <?php echo $currentPage == 'users' ? 'bg-white bg-opacity-20 text-white shadow-sm' : 'hover:bg-white hover:bg-opacity-10 text-blue-100 hover:text-white'; ?>">
                        <i class="fas fa-users mr-3 text-xl"></i>Usuarios
                    </a>
                <?php endif; ?>
            </div>

            <!-- User Profile & Logout - Pinned to the Right -->
            <div class="flex items-center space-x-4 flex-shrink-0">
                <div class="hidden sm:flex items-center px-4 py-1.5 bg-black bg-opacity-10 rounded-full">
                    <i class="fas fa-user-circle mr-2 text-xl text-yellow-300"></i>
                    <span class="text-base font-semibold"><?php echo htmlspecialchars($userName); ?></span>
                </div>
                <a href="logout.php"
                    class="flex items-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition shadow-md"
                    title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt text-lg mr-2"></i>
                    <span class="font-medium">Salir</span>
                </a>
            </div>
        </div>
    </div>
</nav>