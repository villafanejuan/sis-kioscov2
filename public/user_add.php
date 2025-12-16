<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();
checkAdmin(); // Solo administradores pueden acceder

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error de seguridad. Inténtalo de nuevo.</div>';
    } else {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (empty($username) || strlen($username) < 3 || empty($password) || strlen($password) < 6 || !in_array($role, ['user', 'admin'])) {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">El nombre de usuario debe tener al menos 3 caracteres y la contraseña al menos 6.</div>';
        } else {
            // Verificar si el usuario ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">El nombre de usuario ya existe.</div>';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $hashed_password, $role])) {
                    $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Usuario creado exitosamente.</div>';
                } else {
                    $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">Error al crear usuario.</div>';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario - Sistema Kiosco</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a class="text-xl font-bold" href="dashboard.php">Kiosco Manager</a>
                <div class="flex space-x-4">
                    <a class="hover:underline" href="dashboard.php">Dashboard</a>
                    <a class="hover:underline" href="products.php">Productos</a>
                    <a class="hover:underline" href="sales.php">Ventas</a>
                    <a class="hover:underline" href="reports.php">Reportes</a>
                    <a class="hover:underline font-bold" href="user_add.php">Agregar Usuario</a>
                    <a class="hover:underline" href="profile.php">Perfil</a>
                </div>
                <a class="hover:underline" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Agregar Nuevo Usuario</h1>

        <?php echo $message; ?>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <form method="POST" class="max-w-md">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" id="password" name="password" required value="admin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="user">Empleado</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Crear Usuario</button>
            </form>
        </div>
    </div>
</body>
</html>
