<?php
/**
 * Punto de entrada principal - Index con login
 */

require_once __DIR__ . '/../app/bootstrap.php';

$authController = new AuthController();

// Si ya está autenticado, redirigir
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Procesar login si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
    exit;
}

// Mostrar formulario de login
$csrf_token = Security::generateCsrf();
$flash = null;

if (isset($_SESSION['flash_message'])) {
    $flash = [
        'message' => $_SESSION['flash_message'],
        'type' => $_SESSION['flash_type'] ?? 'info'
    ];
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <script src="assets/js/theme-config.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-gray-200 rounded-full mb-4">
                <i class="fas fa-store text-4xl text-gray-800"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo APP_NAME; ?></h1>
            <p class="text-gray-600 mt-2">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Mensajes Flash -->
        <?php if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg <?php
            echo $flash['type'] === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
                ($flash['type'] === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
                    'bg-blue-100 border border-blue-400 text-blue-700');
            ?>">
                <i class="fas fa-<?php echo $flash['type'] === 'error' ? 'exclamation-circle' :
                    ($flash['type'] === 'success' ? 'check-circle' : 'info-circle'); ?> mr-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de Login -->
        <form method="POST" action="index.php" class="space-y-6">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo $csrf_token; ?>">

            <!-- Usuario -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Usuario
                </label>
                <input type="text" id="username" name="username" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Ingresa tu usuario">
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Contraseña
                </label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Ingresa tu contraseña">
            </div>

            <!-- Botón Submit -->
            <button type="submit"
                class="w-full bg-gray-900 text-white font-bold py-3 px-4 hover:bg-black transition duration-200 shadow-sm border border-transparent">
                <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>
                <i class="fas fa-shield-alt mr-1"></i>
                Sesión segura con encriptación
            </p>
            <p class="mt-2 text-xs">
                Usuario por defecto: <strong>admin</strong> / Contraseña: <strong>password</strong>
            </p>
        </div>
    </div>

    <!-- Información de versión -->
    <div class="fixed bottom-4 right-4 text-white text-sm bg-black bg-opacity-30 px-4 py-2 rounded-lg">
        <i class="fas fa-code mr-2"></i>v2.0 Professional
    </div>
</body>

</html>