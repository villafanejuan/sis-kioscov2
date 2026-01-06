<?php
/**
 * Componente de navegación con control de acceso basado en roles
 * Uso: include __DIR__ . '/../includes/nav.php';
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userName = $_SESSION['nombre'] ?? $_SESSION['username'];
?>
<style>
    /* Global layout adjustment for fixed sidebar on desktop */
    @media (min-width: 768px) {
        body {
            padding-left: 16rem;
            /* w-64 */
        }
    }
</style>

<!-- Mobile Header (Visible only on mobile) -->
<div
    class="md:hidden fixed top-0 left-0 w-full h-16 bg-white border-b border-gray-200 z-40 flex items-center px-4 justify-between shadow-sm">
    <div class="flex items-center">
        <button id="mobile-menu-btn" class="text-gray-800 focus:outline-none p-2 rounded-md hover:bg-gray-100">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div class="ml-3 flex items-center font-bold text-gray-800 text-lg">
            <i class="fas fa-store mr-2"></i> Kiosco Nico
        </div>
    </div>
</div>

<!-- Overlay for mobile (hidden by default) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden glass-effect"
    onclick="toggleSidebar()"></div>

<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 bg-white border-r border-gray-200 text-gray-800 shadow-xl md:shadow-none z-50 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- BRAND -->
    <div class="h-16 flex items-center px-6 border-b border-gray-200">
        <i class="fas fa-store text-2xl mr-3 text-gray-800"></i>
        <span class="text-lg font-bold tracking-wide whitespace-nowrap">
            <?php echo "Kiosco Nico" ?>
        </span>
    </div>

    <!-- NAV -->
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium overflow-y-auto">

        <?php if (canAccess('dashboard')): ?>
            <a href="dashboard.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'dashboard'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-home w-5 text-lg mr-3"></i>
                Dashboard
            </a>
        <?php endif; ?>

        <?php if (canAccess('products')): ?>
            <a href="products.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'products'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-box w-5 text-lg mr-3"></i>
                Productos
            </a>
        <?php endif; ?>

        <?php if (canAccess('categories')): ?>
            <a href="categories.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'categories'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-tags w-5 text-lg mr-3"></i>
                Categorías
            </a>
        <?php endif; ?>

        <?php if (canAccess('products')): ?>
            <a href="customers.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'customers'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-address-book w-5 text-lg mr-3"></i>
                Clientes
            </a>
        <?php endif; ?>

        <?php if (canAccess('Proveedores')): ?>
            <a href="proveedores.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'proveedores'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-truck w-5 text-lg mr-3"></i>
                Proveedores
            </a>
        <?php endif; ?>

        <?php if (canAccess('Tickets')): ?>
            <a href="tickets.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'tickets'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-users w-5 text-lg mr-3"></i>
                Tickets
            </a>
        <?php endif; ?>

        <?php if (canAccess('sales')): ?>
            <a href="sales.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'sales'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-shopping-cart w-5 text-lg mr-3"></i>
                Ventas
            </a>
        <?php endif; ?>

        <?php if (canAccess('cash')): ?>
            <a href="cash.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'cash'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-cash-register w-5 text-lg mr-3"></i>
                Caja
            </a>
        <?php endif; ?>

        <?php if (checkAdmin() || $_SESSION['role'] === 'kiosquero'): ?>
            <a href="promotions.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'promotions'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-percent w-5 text-lg mr-3"></i>
                Promos
            </a>
        <?php endif; ?>

        <?php if (canAccess('reports')): ?>
            <a href="reports.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'reports'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-chart-bar w-5 text-lg mr-3"></i>
                Reportes
            </a>
        <?php endif; ?>

        <?php if (canAccess('users')): ?>
            <a href="users.php" class="flex items-center px-4 py-2 rounded-lg transition
                <?php echo $currentPage === 'users'
                    ? 'bg-gray-100 text-gray-900 border-r-2 border-gray-800'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>">
                <i class="fas fa-users w-5 text-lg mr-3"></i>
                Usuarios
            </a>
        <?php endif; ?>

    </nav>

    <!-- USER / LOGOUT -->
    <div class="mt-auto p-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold mr-2">
                <?php echo strtoupper(substr($userName, 0, 1)); ?>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800 truncate"
                    title="<?php echo htmlspecialchars($userName); ?>">
                    <?php echo htmlspecialchars($userName); ?>
                </p>
                <p class="text-xs text-gray-500">
                    <?php echo ucfirst($_SESSION['role'] ?? 'usuario'); ?>
                </p>
            </div>
        </div>
        <a href="logout.php"
            class="block w-full text-center border border-gray-300 bg-white text-gray-700 py-1.5 rounded-none text-xs hover:bg-gray-100 transition shadow-none">
            <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
        </a>
    </div>

</aside>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const mobileBtn = document.getElementById('mobile-menu-btn');

    function toggleSidebar() {
        if (sidebar.classList.contains('-translate-x-full')) {
            // Open
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            // Close
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    if (mobileBtn) {
        mobileBtn.addEventListener('click', toggleSidebar);
    }
</script>