<?php
// Usar ruta absoluta directa para evitar problemas
require_once 'd:/Archivos de programas/XAMPPg/htdocs/sis-kiosco/app/bootstrap.php';
checkSession();
$userId = $_SESSION['user_id'];
$pdo = Database::getInstance()->getConnection();

echo "Current User ID in Session: " . $userId . "<br>";

echo "<h3>Turnos Caja</h3>";
$stmt = $pdo->query("SELECT * FROM turnos_caja");
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1'><tr><th>ID</th><th>UserID</th><th>Name</th><th>Status</th><th>Opened At</th></tr>";
foreach ($turnos as $t) {
    echo "<tr>";
    echo "<td>" . $t['id'] . "</td>";
    echo "<td>" . $t['user_id'] . "</td>";
    echo "<td>" . $t['usuario_nombre'] . "</td>";
    echo "<td>" . $t['estado'] . "</td>";
    echo "<td>" . $t['fecha_apertura'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Usuarios</h3>";
$stmt = $pdo->query("SELECT id, username, nombre FROM usuarios");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Nombre</th></tr>";
foreach ($users as $u) {
    echo "<tr>";
    echo "<td>" . $u['id'] . "</td>";
    echo "<td>" . $u['username'] . "</td>";
    echo "<td>" . $u['nombre'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>