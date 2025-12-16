<?php
// Mostrar TODOS los errores
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug</title></head><body>";
echo "<h1>DIAGNÓSTICO COMPLETO</h1>";

echo "<h2>1. Archivos Existen?</h2>";
$files = [
    'cash.php' => __DIR__ . '/cash.php',
    'users.php' => __DIR__ . '/users.php',
    'check_session.php' => __DIR__ . '/check_session.php',
    'fix_session.php' => __DIR__ . '/fix_session.php'
];

foreach ($files as $name => $path) {
    echo $name . ": " . (file_exists($path) ? "✅ SÍ" : "❌ NO") . "<br>";
}

echo "<h2>2. Intentando cargar bootstrap...</h2>";
try {
    require_once __DIR__ . '/../app/bootstrap.php';
    echo "✅ Bootstrap cargado<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    die();
}

echo "<h2>3. Estado de Sesión</h2>";
if (!isset($_SESSION['user_id'])) {
    echo "❌ NO HAY SESIÓN<br>";
    echo "<a href='index.php'>Ir al login</a>";
    die();
}

echo "✅ Sesión activa<br>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "Username: " . ($_SESSION['username'] ?? 'N/A') . "<br>";
echo "Role ID: " . ($_SESSION['role_id'] ?? 'N/A') . "<br>";
echo "Role (nombre): " . ($_SESSION['role'] ?? '❌ NO ESTABLECIDO') . "<br>";

echo "<h2>4. Verificando checkAdmin()</h2>";
$isAdmin = checkAdmin();
echo "checkAdmin() retorna: " . ($isAdmin ? "✅ TRUE" : "❌ FALSE") . "<br>";

echo "<h2>5. Probando acceso a cash.php</h2>";
if ($isAdmin) {
    echo "✅ Deberías poder acceder a cash.php<br>";
    echo "<a href='cash.php' style='background:blue;color:white;padding:10px;display:inline-block;margin:10px 0;'>PROBAR CASH.PHP</a><br>";
} else {
    echo "❌ No eres admin, no puedes acceder<br>";
    echo "Necesitas que \$_SESSION['role'] sea 'admin'<br>";
}

echo "<h2>6. Probando acceso a users.php</h2>";
if ($isAdmin) {
    echo "✅ Deberías poder acceder a users.php<br>";
    echo "<a href='users.php' style='background:green;color:white;padding:10px;display:inline-block;margin:10px 0;'>PROBAR USERS.PHP</a><br>";
} else {
    echo "❌ No eres admin, no puedes acceder<br>";
}

echo "<h2>7. Datos del usuario en BD</h2>";
$db = Database::getInstance();
$stmt = $db->getConnection()->prepare("
    SELECT u.*, r.nombre as rol_nombre 
    FROM usuarios u 
    LEFT JOIN roles r ON u.role_id = r.id 
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

echo "<pre>";
print_r($user);
echo "</pre>";

if ($user && $user['rol_nombre']) {
    $roleLower = strtolower($user['rol_nombre']);
    echo "<h2>8. ARREGLANDO SESIÓN</h2>";
    $_SESSION['role'] = $roleLower;
    echo "✅ Rol establecido en sesión: " . $_SESSION['role'] . "<br>";
    echo "✅ checkAdmin() ahora: " . (checkAdmin() ? "TRUE" : "FALSE") . "<br>";
    echo "<br><strong>AHORA PRUEBA ESTOS ENLACES:</strong><br>";
    echo "<a href='cash.php' style='background:blue;color:white;padding:15px 30px;display:inline-block;margin:10px;text-decoration:none;border-radius:5px;font-size:18px;'>💰 IR A CAJA</a>";
    echo "<a href='users.php' style='background:green;color:white;padding:15px 30px;display:inline-block;margin:10px;text-decoration:none;border-radius:5px;font-size:18px;'>👥 IR A USUARIOS</a>";
    echo "<a href='dashboard.php' style='background:gray;color:white;padding:15px 30px;display:inline-block;margin:10px;text-decoration:none;border-radius:5px;font-size:18px;'>🏠 DASHBOARD</a>";
}

echo "</body></html>";
