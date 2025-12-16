<?php
require_once __DIR__ . '/../app/bootstrap.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

try {
    // Verificar producto existente
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if (!$prod) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit;
    }

    // Actualizar Stock
    // Sumamos la cantidad ingresada al stock actual
    $newStock = $prod['stock'] + $quantity;

    $update = $pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
    if ($update->execute([$newStock, $id])) {
        echo json_encode(['success' => true, 'new_stock' => $newStock, 'message' => 'Stock actualizado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar DB']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
