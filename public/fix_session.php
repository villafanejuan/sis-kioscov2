<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    die("No hay sesión activa. <a href='index.php'>Ir al login</a>");
}

// Obtener el rol del usuario desde la base de datos
$db = Database::getInstance();
$stmt = $db->getConnection()->prepare("
    SELECT u.role_id, r.nombre as rol_nombre 
    FROM usuarios u 
    LEFT JOIN roles r ON u.role_id = r.id 
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user && $user['rol_nombre']) {
    $_SESSION['role'] = strtolower($user['rol_nombre']);
    echo "✅ Rol actualizado en la sesión: " . $_SESSION['role'] . "<br>";
    echo "✅ checkAdmin() = " . (checkAdmin() ? 'TRUE' : 'FALSE') . "<br><br>";
    echo "<a href='cash.php' style='display:inline-block; background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Ir a Caja</a> ";
    echo "<a href='users.php' style='display:inline-block; background:green; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; margin-left:10px;'>Ir a Usuarios</a> ";
    echo "<a href='dashboard.php' style='display:inline-block; background:gray; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; margin-left:10px;'>Ir al Dashboard</a>";
} else {
    echo "❌ No se pudo obtener el rol del usuario<br>";
    echo "User data: <pre>" . print_r($user, true) . "</pre>";
}
