<?php
require_once __DIR__ . '/../app/bootstrap.php';

// NO verificar sesión para poder ver el estado
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Sesión</title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6 text-blue-600">🔍 Diagnóstico de Sesión</h1>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 p-4 mb-4">
                <p class="font-bold text-red-700">❌ NO HAY SESIÓN ACTIVA</p>
                <p class="text-red-600">Necesitas hacer login primero</p>
                <a href="index.php" class="inline-block mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Ir al Login
                </a>
            </div>
        <?php else: ?>
            <div class="bg-green-100 border-l-4 border-green-500 p-4 mb-6">
                <p class="font-bold text-green-700">✅ Sesión Activa</p>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded">
                    <h2 class="font-bold text-lg mb-2">Datos de Sesión:</h2>
                    <table class="w-full">
                        <tr class="border-b">
                            <td class="py-2 font-semibold">User ID:</td>
                            <td><?php echo $_SESSION['user_id'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Username:</td>
                            <td><?php echo $_SESSION['username'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Nombre:</td>
                            <td><?php echo $_SESSION['nombre'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-semibold">Role ID:</td>
                            <td><?php echo $_SESSION['role_id'] ?? 'N/A'; ?></td>
                        </tr>
                        <tr class="border-b bg-yellow-50">
                            <td class="py-2 font-semibold">Role (nombre):</td>
                            <td
                                class="font-bold <?php echo isset($_SESSION['role']) ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo isset($_SESSION['role']) ? $_SESSION['role'] : '❌ NO ESTABLECIDO'; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <h2 class="font-bold text-lg mb-2">Verificación de Permisos:</h2>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 bg-white rounded">
                            <span>checkAdmin():</span>
                            <span class="font-bold <?php echo checkAdmin() ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo checkAdmin() ? '✅ TRUE (Es Admin)' : '❌ FALSE (No es Admin)'; ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded">
                            <span>Puede acceder a Caja:</span>
                            <span class="font-bold <?php echo checkAdmin() ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo checkAdmin() ? '✅ SÍ' : '❌ NO'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <?php if (!isset($_SESSION['role'])): ?>
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4">
                        <p class="font-bold text-yellow-700">⚠️ PROBLEMA DETECTADO</p>
                        <p class="text-yellow-600 mb-4">La sesión no tiene el campo 'role' establecido.</p>
                        <p class="font-bold mb-2">SOLUCIÓN:</p>
                        <ol class="list-decimal list-inside space-y-2 text-sm">
                            <li>Haz clic en el botón "Cerrar Sesión" abajo</li>
                            <li>Vuelve a hacer login con admin / password</li>
                            <li>El rol se cargará correctamente</li>
                            <li>Podrás acceder a Caja</li>
                        </ol>
                        <a href="logout.php"
                            class="inline-block mt-4 bg-red-500 text-white px-6 py-3 rounded hover:bg-red-600 font-bold">
                            🚪 Cerrar Sesión y Volver a Iniciar
                        </a>
                    </div>
                <?php else: ?>
                    <div class="bg-green-100 border-l-4 border-green-500 p-4">
                        <p class="font-bold text-green-700">✅ TODO CORRECTO</p>
                        <p class="text-green-600">Tu sesión está configurada correctamente.</p>
                        <a href="cash.php"
                            class="inline-block mt-4 bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-600 font-bold">
                            💰 Ir a Gestión de Caja
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mt-6 text-center">
                    <a href="dashboard.php" class="text-blue-600 hover:underline">← Volver al Dashboard</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>