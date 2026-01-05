<?php
/**
 * Componente de navegación con control de acceso basado en roles
 * Uso: include __DIR__ . '/../includes/nav.php';
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userName = $_SESSION['nombre'] ?? $_SESSION['username'];
?>
<aside class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-blue-600 to-purple-600 text-white shadow-lg z-50 flex flex-col">

    <!-- BRAND -->
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <i class="fas fa-store text-2xl mr-3 text-yellow-300"></i>
        <span class="text-lg font-bold tracking-wide whitespace-nowrap">
            <?php echo "Kiosco Nico" ?>
        </span>
    </div>

    <!-- NAV -->
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium overflow-y-auto">

        <?php if (canAccess('dashboard')): ?>
            <a href="dashboard.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'dashboard'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-home w-5 text-lg mr-3"></i>
                Dashboard
            </a>
        <?php endif; ?>

        <?php if (canAccess('products')): ?>
            <a href="products.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'products'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-box w-5 text-lg mr-3"></i>
                Productos
            </a>
        <?php endif; ?>

        <?php if (canAccess('categories')): ?>
            <a href="categories.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'categories'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-tags w-5 text-lg mr-3"></i>
                Categorías
            </a>
        <?php endif; ?>

        <?php if (canAccess('products')): ?>
            <a href="customers.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'customers'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-address-book w-5 text-lg mr-3"></i>
                Clientes
            </a>
        <?php endif; ?>

        <?php if (canAccess('Proveedores')): ?>
            <a href="proveedores.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'proveedores'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-truck w-5 text-lg mr-3"></i>
                Proveedores
            </a>
        <?php endif; ?>

        <?php if (canAccess('sales')): ?>
            <a href="sales.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'sales'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-shopping-cart w-5 text-lg mr-3"></i>
                Ventas
            </a>
        <?php endif; ?>

        <?php if (canAccess('cash')): ?>
            <a href="cash.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'cash'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-cash-register w-5 text-lg mr-3"></i>
                Caja
            </a>
        <?php endif; ?>

        <?php if (checkAdmin() || $_SESSION['role'] === 'kiosquero'): ?>
            <a href="promotions.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'promotions'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-percent w-5 text-lg mr-3"></i>
                Promos
            </a>
        <?php endif; ?>

        <?php if (canAccess('reports')): ?>
            <a href="reports.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'reports'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-chart-bar w-5 text-lg mr-3"></i>
                Reportes
            </a>
        <?php endif; ?>

        <?php if (canAccess('users')): ?>
            <a href="users.php"
               class="flex items-center px-4 py-2 rounded-lg transition
               <?php echo $currentPage === 'users'
                   ? 'bg-white bg-opacity-20 text-white shadow'
                   : 'text-blue-100 hover:bg-white hover:bg-opacity-10 hover:text-white'; ?>">
                <i class="fas fa-users w-5 text-lg mr-3"></i>
                Usuarios
            </a>
        <?php endif; ?>

    </nav>

    <!-- USER / LOGOUT -->
    <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center mb-4">
            <i class="fas fa-user-circle text-2xl text-yellow-300 mr-3"></i>
            <span class="text-sm font-semibold">
                <?php echo htmlspecialchars($userName); ?>
            </span>
        </div>

        <a href="logout.php"
           class="flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg transition shadow">
            <i class="fas fa-sign-out-alt"></i>
            <span>Salir</span>
        </a>
    </div>


</aside>