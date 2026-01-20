<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

// Verificar que sea admin
if (!checkAdmin()) {
    $_SESSION['flash_message'] = 'Solo administradores pueden gestionar usuarios';
    $_SESSION['flash_type'] = 'error';
    header('Location: dashboard.php');
    exit;
}

$message = '';
$messageType = '';

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Error de seguridad. Inténtalo de nuevo.';
        $messageType = 'error';
    } else {
        if (isset($_POST['create_user'])) {
            $username = sanitize($_POST['username']);
            $nombre = sanitize($_POST['nombre']);
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $roleId = intval($_POST['role_id']);

            if (empty($username) || empty($nombre) || empty($password) || $roleId <= 0) {
                $message = 'Todos los campos son obligatorios';
                $messageType = 'error';
            } elseif (strlen($password) < 6) {
                $message = 'La contraseña debe tener al menos 6 caracteres';
                $messageType = 'error';
            } else {
                // Verificar si el usuario ya existe
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $message = 'El nombre de usuario ya existe';
                    $messageType = 'error';
                } else {
                    // Crear usuario
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO usuarios (username, nombre, email, password, role_id, is_active) VALUES (?, ?, ?, ?, ?, 1)");
                    if ($stmt->execute([$username, $nombre, $email, $hashedPassword, $roleId])) {
                        $message = 'Usuario creado exitosamente';
                        $messageType = 'success';
                    } else {
                        $message = 'Error al crear usuario';
                        $messageType = 'error';
                    }
                }
            }
        } elseif (isset($_POST['toggle_status'])) {
            $userId = intval($_POST['user_id']);
            $stmt = $pdo->prepare("UPDATE usuarios SET is_active = NOT is_active WHERE id = ?");
            if ($stmt->execute([$userId])) {
                $message = 'Estado del usuario actualizado';
                $messageType = 'success';
            }
        } elseif (isset($_POST['delete_user'])) {
            $userId = intval($_POST['user_id']);
            if ($userId == $_SESSION['user_id']) {
                $message = 'No puedes eliminar tu propio usuario';
                $messageType = 'error';
            } else {
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = 'Usuario eliminado exitosamente';
                    $messageType = 'success';
                } else {
                    $message = 'Error al eliminar usuario';
                    $messageType = 'error';
                }
            }
        } elseif (isset($_POST['update_user'])) {
            $userId = intval($_POST['user_id']);
            $username = sanitize($_POST['username']);
            $nombre = sanitize($_POST['nombre']);
            $email = sanitize($_POST['email']);

            // Si el usuario se edita a sí mismo, verificar que no se quite el rol de admin si es el único (aunque por ahora permitimos edición básica)
            // Si intenta cambiar password (opcional)
            $password = $_POST['password'] ?? '';

            // Validación básica
            if (empty($username) || empty($nombre)) {
                $message = 'Nombre y Usuario son obligatorios';
                $messageType = 'error';
            } else {
                // Verificar duplicado de username (excluyendo al propio usuario)
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ? AND id != ?");
                $stmt->execute([$username, $userId]);
                if ($stmt->fetch()) {
                    $message = 'El nombre de usuario ya está en uso';
                    $messageType = 'error';
                } else {
                    // Update
                    $sql = "UPDATE usuarios SET username = ?, nombre = ?, email = ? WHERE id = ?";
                    $params = [$username, $nombre, $email, $userId];

                    // Si mandó password
                    if (!empty($password)) {
                        if (strlen($password) < 6) {
                            $message = 'La nueva contraseña debe tener al menos 6 caracteres';
                            $messageType = 'error';
                            // Abortar si falla password
                            $sql = "";
                        } else {
                            $sql = "UPDATE usuarios SET username = ?, nombre = ?, email = ?, password = ? WHERE id = ?";
                            $hashed = password_hash($password, PASSWORD_BCRYPT);
                            $params = [$username, $nombre, $email, $hashed, $userId];
                        }
                    }

                    if (!empty($sql)) {
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute($params)) {
                            $message = 'Usuario actualizado correctamente';
                            $messageType = 'success';
                            // Si se actualizó a sí mismo, actualizar la sesión
                            if ($userId == $_SESSION['user_id']) {
                                $_SESSION['username'] = $username;
                                $_SESSION['nombre'] = $nombre;
                            }
                        } else {
                            $message = 'Error al actualizar usuario';
                            $messageType = 'error';
                        }
                    }
                }
            }
        } elseif (isset($_POST['reset_password'])) {
            $userId = intval($_POST['user_id']);
            $newPassword = $_POST['new_password'];

            if (strlen($newPassword) < 6) {
                $message = 'La contraseña debe tener al menos 6 caracteres';
                $messageType = 'error';
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                if ($stmt->execute([$hashedPassword, $userId])) {
                    $message = 'Contraseña actualizada exitosamente';
                    $messageType = 'success';
                }
            }
        }
    }
}

// Obtener roles
$roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();

// Obtener usuarios
$usuarios = $pdo->query("
    SELECT u.*, r.nombre as rol_nombre 
    FROM usuarios u 
    LEFT JOIN roles r ON u.role_id = r.id 
    ORDER BY u.id
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Título -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-users text-gray-700 mr-3"></i>Gestión de Usuarios
            </h1>
            <p class="text-gray-600">Administra empleados y sus permisos</p>
        </div>

        <!-- Mensajes -->
        <?php if ($message): ?>
            <div
                class="mb-6 p-4 rounded-sm <?php echo $messageType == 'success' ? 'bg-gray-100 border-gray-800 text-gray-800' : 'bg-red-50 border-red-800 text-red-900'; ?> border-l-4">
                <i class="fas <?php echo $messageType == 'success' ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-2"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario Crear Usuario -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-sm shadow-sm border border-gray-200">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">
                        <i class="fas fa-user-plus mr-2"></i>Nuevo Usuario
                    </h3>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuario *</label>
                            <input type="text" name="username" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-500 focus:border-transparent"
                                placeholder="usuario123">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                            <input type="text" name="nombre" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-500 focus:border-transparent"
                                placeholder="Juan Pérez">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="usuario@email.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
                            <input type="password" name="password" required minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Mínimo 6 caracteres">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                            <select name="role_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-500 focus:border-transparent">
                                <?php foreach ($roles as $role): ?>
                                    <?php if ($role['id'] == 3)
                                        continue; ?>
                                    <option value="<?php echo $role['id']; ?>">
                                        <?php echo htmlspecialchars($role['nombre']); ?>
                                        <?php if ($role['id'] == 1):
                                            echo ' - Acceso Total';
                                        endif; ?>
                                        <?php if ($role['id'] == 2):
                                            echo ' - Solo Ventas';
                                        endif; ?>
                                        <?php if ($role['id'] == 3):
                                            echo ' - Solo Caja';
                                        endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" name="create_user"
                            class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-sm transition shadow-sm">
                            <i class="fas fa-user-plus mr-2"></i>Crear Usuario
                        </button>
                    </form>
                </div>
            </div>

            <!-- Lista de Usuarios -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-sm shadow-sm border border-gray-200">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900">
                        <i class="fas fa-list mr-2 text-gray-700"></i>Usuarios Registrados
                    </h3>

                    <div class="space-y-3">
                        <?php foreach ($usuarios as $user): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 flex-1">
                                        <i
                                            class="fas fa-user-circle text-3xl <?php echo $user['is_active'] ? 'text-blue-500' : 'text-gray-400'; ?> mt-1"></i>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-gray-800">
                                                    <?php echo htmlspecialchars($user['nombre'] ?: $user['username']); ?>
                                                </p>
                                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">TÚ</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                @<?php echo htmlspecialchars($user['username']); ?></p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span
                                                    class="text-xs px-2 py-1 rounded-sm border border-gray-300 bg-gray-50 text-gray-700">
                                                    <i
                                                        class="fas fa-shield-alt mr-1"></i><?php echo htmlspecialchars($user['rol_nombre'] ?? 'Sin rol'); ?>
                                                </span>
                                                <span
                                                    class="text-xs px-2 py-1 rounded-sm border <?php echo $user['is_active'] ? 'bg-gray-100 border-gray-400 text-gray-800' : 'bg-white border-gray-300 text-gray-500'; ?>">
                                                    <?php echo $user['is_active'] ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <!-- Edit User (Available for all) -->
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                            class="px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-sm text-sm transition"
                                            title="Editar Datos">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <!-- Toggle Status -->
                                            <!-- Toggle Status -->
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="toggle_status"
                                                    class="px-3 py-2 rounded-sm border border-gray-300 text-sm <?php echo $user['is_active'] ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?> transition"
                                                    onclick="return confirm('¿Cambiar estado del usuario?');"
                                                    title="<?php echo $user['is_active'] ? 'Desactivar' : 'Activar'; ?>">
                                                    <i
                                                        class="fas <?php echo $user['is_active'] ? 'fa-ban' : 'fa-check'; ?>"></i>
                                                </button>
                                            </form>

                                            <!-- Reset Password -->
                                            <button
                                                onclick="showResetPassword(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['nombre'] ?: $user['username']); ?>')"
                                                class="px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-sm text-sm transition"
                                                title="Cambiar contraseña">
                                                <i class="fas fa-key"></i>
                                            </button>

                                            <!-- Delete User -->
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="delete_user"
                                                    class="px-3 py-2 bg-white border border-gray-300 hover:bg-red-50 text-red-600 rounded-sm text-sm transition"
                                                    onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.');"
                                                    title="Eliminar usuario">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div id="resetPasswordModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Restablecer Contraseña</h3>
            <p class="text-gray-600 mb-4">Usuario: <span id="resetUserName" class="font-semibold"></span></p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="user_id" id="resetUserId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Mínimo 6 caracteres">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeResetPassword()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button type="submit" name="reset_password"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Restablecer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div id="editUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Editar Usuario</h3>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <input type="hidden" name="update_user" value="1">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Usuario</label>
                                <input type="text" name="username" id="edit_username" required
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                <input type="text" name="nombre" id="edit_nombre" required
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="edit_email"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nueva Contraseña
                                    (Opcional)</label>
                                <input type="password" name="password" placeholder="Dejar en blanco para no cambiar"
                                    minlength="6"
                                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border">
                                <p class="text-xs text-gray-500 mt-1">Solo llena esto si quieres cambiar la contraseña.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:col-start-2 sm:text-sm">
                                Guardar Cambios
                            </button>
                            <button type="button" onclick="closeEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_nombre').value = user.nombre;
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('editUserModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        function showResetPassword(userId, userName) {
            document.getElementById('resetUserId').value = userId;
            document.getElementById('resetUserName').textContent = userName;
            document.getElementById('resetPasswordModal').classList.remove('hidden');
        }

        function closeResetPassword() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
        }
    </script>
</body>

</html>