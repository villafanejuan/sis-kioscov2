<?php
require_once __DIR__ . '/../app/bootstrap.php';
checkSession();

// AJAX Handler for Shift Details
if (isset($_GET['action']) && $_GET['action'] == 'get_shift_details' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    try {
        // Permitir acceso a Admin y Cajero (Auditor)
        $isAuditor = ($_SESSION['role'] === 'cajero');

        if (!checkAdmin() && !$isAuditor) {
            // Usuario normal (Kiosquero/Empleado) solo ve sus propios detalles
            $stmt = $pdo->prepare("SELECT user_id FROM turnos_caja WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $turno = $stmt->fetch();
            if (!$turno || $turno['user_id'] != $_SESSION['user_id']) {
                throw new Exception("Acceso denegado");
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM movimientos_caja WHERE turno_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_GET['id']]);
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enriquecer movimientos de venta con detalles de productos
        foreach ($movimientos as &$mov) {
            if ($mov['tipo'] === 'venta' && !empty($mov['venta_id'])) {
                try {
                    $stmtItems = $pdo->prepare("
                        SELECT p.nombre, vd.cantidad 
                        FROM venta_detalles vd 
                        JOIN productos p ON vd.producto_id = p.id 
                        WHERE vd.venta_id = ?
                    ");
                    $stmtItems->execute([$mov['venta_id']]);
                    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                    if ($items) {
                        $desc = [];
                        foreach ($items as $item) {
                            $desc[] = $item['nombre'] . ' (x' . $item['cantidad'] . ')';
                        }
                        $mov['descripcion'] .= ' | ' . implode(', ', $desc);
                    }
                } catch (Exception $e) {
                    // Si falla obtener detalles, mantenemos la descripción original
                    error_log("Error obteniendo detalles de venta: " . $e->getMessage());
                }
            }
        }
        unset($mov); // Romper referencia

        // Fetch shift info for summary
        $stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $turno = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcular Ventas Cta Cte (Credits) - Suma de Pagos con Metodo 'Cuenta Corriente'
        $stmtCC = $pdo->prepare("
            SELECT SUM(vp.monto) 
            FROM venta_pagos vp 
            JOIN ventas v ON vp.venta_id = v.id
            JOIN movimientos_caja m ON v.id = m.venta_id 
            WHERE m.turno_id = ? AND m.tipo = 'venta' 
            AND vp.metodo_pago_id = (SELECT id FROM metodos_pago WHERE nombre = 'Cuenta Corriente' LIMIT 1)
        ");
        $stmtCC->execute([$_GET['id']]);
        $totalCC = $stmtCC->fetchColumn() ?: 0;

        // Calcular Ventas Transferencia
        $stmtTrans = $pdo->prepare("
             SELECT SUM(vp.monto)
             FROM venta_pagos vp
             JOIN ventas v ON vp.venta_id = v.id
             JOIN movimientos_caja m ON v.id = m.venta_id
             WHERE m.turno_id = ? AND m.tipo = 'venta'
             AND vp.metodo_pago_id = (SELECT id FROM metodos_pago WHERE nombre = 'Transferencia' LIMIT 1)
        ");
        $stmtTrans->execute([$_GET['id']]);
        $totalTrans = $stmtTrans->fetchColumn() ?: 0;

        // Calcular Pagos (Cobros) recibidos en el turno
        $stmtPagos = $pdo->prepare("SELECT SUM(monto) FROM cliente_pagos WHERE turno_id = ?");
        $stmtPagos->execute([$_GET['id']]);
        $totalPagos = $stmtPagos->fetchColumn() ?: 0;

        echo json_encode([
            'success' => true,
            'movimientos' => $movimientos,
            'turno' => $turno,
            'credit_sales_total' => $totalCC,
            'transfer_sales_total' => $totalTrans,
            'debt_collections_total' => $totalPagos
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Verificar acceso (Admin y Cajero pueden acceder a caja)
if (!canAccess('cash')) {
    $_SESSION['flash_message'] = 'No tienes permiso para acceder a la gestión de caja';
    $_SESSION['flash_type'] = 'error';
    header('Location: dashboard.php');
    exit;
}

$isAdmin = checkAdmin();
$userName = $_SESSION['nombre'] ?? $_SESSION['username'];
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? '';

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
        try {
            if (isset($_POST['abrir_turno'])) {
                // Verificar que el usuario no tenga ya un turno abierto
                $stmt = $pdo->prepare("SELECT id FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
                $stmt->execute([$userId]);
                $turnoExistente = $stmt->fetch();

                if ($turnoExistente) {
                    $message = 'Ya tienes un turno abierto. Cierra tu turno actual primero.';
                    $messageType = 'error';
                } else {
                    $montoInicial = floatval($_POST['monto_inicial']);
                    $notasApertura = sanitize($_POST['notas_apertura'] ?? '');

                    $pdo->beginTransaction();

                    // Crear turno asociado al usuario
                    // Insertamos tanto en user_id como en usuario_id para mantener compatibilidad
                    $stmt = $pdo->prepare("INSERT INTO turnos_caja (user_id, usuario_id, usuario_nombre, monto_inicial, estado, notas_apertura, fecha_apertura) VALUES (?, ?, ?, ?, 'abierto', ?, NOW())");
                    $stmt->execute([$userId, $userId, $userName, $montoInicial, $notasApertura]);
                    $turnoId = $pdo->lastInsertId();

                    // Crear movimiento inicial
                    // Usamos usuario_id que es la columna existente en movimientos_caja
                    $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, created_at, usuario_id, fecha) VALUES (?, 'inicial', ?, 'Apertura de turno', NOW(), ?, NOW())");
                    $stmt->execute([$turnoId, $montoInicial, $userId]);

                    $pdo->commit();
                    $message = 'Turno abierto exitosamente';
                    $messageType = 'success';
                }
            } elseif (isset($_POST['cerrar_turno'])) {
                $turnoId = intval($_POST['turno_id']);
                $montoFinal = floatval($_POST['monto_final']);
                $notasCierre = sanitize($_POST['notas_cierre'] ?? '');

                // Verificar que el turno pertenezca al usuario (o sea admin)
                $stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE id = ? AND estado = 'abierto'");
                $stmt->execute([$turnoId]);
                $turno = $stmt->fetch();

                if (!$turno) {
                    $message = 'Turno no encontrado o ya cerrado';
                    $messageType = 'error';
                } elseif (!$isAdmin && $turno['user_id'] != $userId) {
                    $message = 'No puedes cerrar el turno de otro usuario';
                    $messageType = 'error';
                } else {
                    // Calcular totales para validación
                    $stmt = $pdo->prepare("
                        SELECT 
                            SUM(CASE WHEN tipo IN ('inicial', 'ingreso', 'venta') THEN monto ELSE 0 END) as total_ingresos,
                            SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as total_egresos
                        FROM movimientos_caja 
                        WHERE turno_id = ?
                    ");
                    $stmt->execute([$turnoId]);
                    $totales = $stmt->fetch();

                    $montoEsperado = ($totales['total_ingresos'] ?? 0) - ($totales['total_egresos'] ?? 0);
                    $diferencia = $montoFinal - $montoEsperado;

                    $pdo->beginTransaction();

                    // Actualizar turno
                    $stmt = $pdo->prepare("UPDATE turnos_caja SET 
                        monto_final = ?,
                        monto_esperado = ?,
                        diferencia = ?,
                        estado = 'cerrado',
                        notas_cierre = ?,
                        fecha_cierre = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$montoFinal, $montoEsperado, $diferencia, $notasCierre, $turnoId]);

                    $pdo->commit();
                    $message = 'Turno cerrado exitosamente';
                    $messageType = 'success';
                }
            } elseif (isset($_POST['registrar_movimiento'])) {
                // Obtener turno activo del usuario
                $stmt = $pdo->prepare("SELECT id FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
                $stmt->execute([$userId]);
                $turnoActivo = $stmt->fetch();

                if (!$turnoActivo) {
                    $message = 'Debes tener un turno abierto para registrar movimientos';
                    $messageType = 'error';
                } else {
                    $tipo = $_POST['tipo'];
                    $monto = floatval($_POST['monto']);
                    $descripcion = sanitize($_POST['descripcion']);

                    $stmt = $pdo->prepare("INSERT INTO movimientos_caja (turno_id, tipo, monto, descripcion, created_at, usuario_id, fecha) VALUES (?, ?, ?, ?, NOW(), ?, NOW())");
                    $stmt->execute([$turnoActivo['id'], $tipo, $monto, $descripcion, $userId]);

                    $message = 'Movimiento registrado exitosamente';
                    $messageType = 'success';
                }
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Obtener turno activo del usuario
$stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE user_id = ? AND estado = 'abierto'");
$stmt->execute([$userId]);
$turnoActivo = $stmt->fetch();

// Obtener movimientos del turno activo
$movimientos = [];
if ($turnoActivo) {
    $stmt = $pdo->prepare("SELECT * FROM movimientos_caja WHERE turno_id = ? ORDER BY created_at DESC");
    $stmt->execute([$turnoActivo['id']]);
    $movimientos = $stmt->fetchAll();
}

// Obtener historial de turnos según el rol
if ($isAdmin || $userRole === 'cajero') {
    // Admin y Cajero pueden ver todos los turnos y filtrar
    $filtroUsuario = isset($_GET['user_filter']) ? intval($_GET['user_filter']) : null;

    if ($filtroUsuario) {
        $stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE user_id = ? ORDER BY fecha_apertura DESC LIMIT 20");
        $stmt->execute([$filtroUsuario]);
    } else {
        $stmt = $pdo->query("SELECT * FROM turnos_caja ORDER BY fecha_apertura DESC LIMIT 20");
    }
    $historial = $stmt->fetchAll();

    // Obtener lista de usuarios para filtro
    $usuarios = $pdo->query("SELECT DISTINCT u.id, u.nombre, u.username FROM usuarios u INNER JOIN turnos_caja t ON u.id = t.user_id ORDER BY u.nombre")->fetchAll();
} else {
    // KIOSQUERO: Solo ve SUS PROPIOS turnos (RESTRICCIÓN DE SEGURIDAD)
    $stmt = $pdo->prepare("SELECT * FROM turnos_caja WHERE user_id = ? ORDER BY fecha_apertura DESC LIMIT 20");
    $stmt->execute([$userId]);
    $historial = $stmt->fetchAll();

    // No hay lista de usuarios porque no pueden filtrar
    $usuarios = [];
}

// Calcular totales del turno activo
$totalActual = 0;
if ($turnoActivo) {
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN tipo IN ('inicial', 'ingreso', 'venta') THEN monto ELSE 0 END) as ingresos,
            SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as egresos
        FROM movimientos_caja 
        WHERE turno_id = ?
    ");
    $stmt->execute([$turnoActivo['id']]);
    $totales = $stmt->fetch();
    $totalActual = ($totales['ingresos'] ?? 0) - ($totales['egresos'] ?? 0);
}

// Lógica de Auditoría (Para rol 'cajero')
$isAuditor = ($userRole === 'cajero');
$globalStats = [];

if ($isAuditor) {
    // 1. Total Inicial en Cajas Abiertas
    $stmt = $pdo->query("SELECT COUNT(*) as count, SUM(monto_inicial) as total FROM turnos_caja WHERE estado = 'abierto'");
    $openShifts = $stmt->fetch();

    // 2. Movimientos en Cajas Abiertas
    $stmt = $pdo->query("
       SELECT 
           SUM(CASE WHEN m.tipo IN ('ingreso', 'venta') THEN m.monto ELSE 0 END) as income,
           SUM(CASE WHEN m.tipo = 'egreso' THEN m.monto ELSE 0 END) as outcome
       FROM movimientos_caja m
       JOIN turnos_caja t ON m.turno_id = t.id
       WHERE t.estado = 'abierto'
   ");
    $movements = $stmt->fetch();

    $globalStats = [
        'active_shifts' => $openShifts['count'],
        'total_initial' => $openShifts['total'] ?? 0,
        'total_income' => $movements['income'] ?? 0,
        'total_outcome' => $movements['outcome'] ?? 0,
        'current_balance' => ($openShifts['total'] ?? 0) + ($movements['income'] ?? 0) - ($movements['outcome'] ?? 0)
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Caja - <?php echo APP_NAME; ?></title>
    <script src="assets/js/tailwindcss.js"></script>
    <script src="assets/js/theme-config.js"></script>
    <link href="assets/css/fontawesome.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navegación -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Título -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-cash-register text-gray-600 mr-3"></i>Gestión de Caja
            </h1>
            <p class="text-gray-600">
                <?php if ($isAdmin): ?>
                    Control de turnos, movimientos y arqueos de caja - Vista Administrativa
                <?php elseif ($isAuditor): ?>
                    Panel de Auditoría y Control Global de Caja
                <?php else: ?>
                    Control de tu turno de caja personal
                <?php endif; ?>
            </p>
        </div>

        <!-- Mensajes -->
        <?php if ($message): ?>
            <div
                class="mb-6 p-4 rounded-none <?php echo $messageType == 'success' ? 'bg-green-50 border-green-500 text-green-800' : 'bg-red-50 border-red-500 text-red-800'; ?> border border-l-4">
                <i class="fas <?php echo $messageType == 'success' ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-2"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- VISTA AUDITOR (CAJERO) -->
        <?php if ($isAuditor): ?>
            <!-- Global Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 border border-gray-300 shadow-none border-b-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Cajas Abiertas</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $globalStats['active_shifts']; ?></p>
                        </div>
                        <i class="fas fa-users text-gray-200 text-4xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 border border-gray-300 shadow-none border-b-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Saldo Global (Vivo)</p>
                            <p class="text-3xl font-bold text-green-700">
                                $<?php echo number_format($globalStats['current_balance'], 2); ?></p>
                        </div>
                        <i class="fas fa-chart-line text-gray-200 text-4xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 border border-gray-300 shadow-none border-b-4 border-emerald-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Ingresos Activos</p>
                            <p class="text-2xl font-bold text-gray-700">
                                +$<?php echo number_format($globalStats['total_income'], 2); ?></p>
                        </div>
                        <i class="fas fa-arrow-up text-gray-200 text-3xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 border border-gray-300 shadow-none border-b-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Egresos Activos</p>
                            <p class="text-2xl font-bold text-gray-700">
                                -$<?php echo number_format($globalStats['total_outcome'], 2); ?></p>
                        </div>
                        <i class="fas fa-arrow-down text-gray-200 text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Historial Full Width -->
            <div class="bg-white p-6 border border-gray-300 shadow-none">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-history text-gray-600 mr-2"></i>Historial Global de Turnos
                    </h3>
                    <form method="GET" class="flex gap-2">
                        <select name="user_filter" onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo (isset($_GET['user_filter']) && $_GET['user_filter'] == $u['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['nombre'] ?: $u['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <?php if (empty($historial)): ?>
                    <div class="bg-gray-50 rounded-lg p-12 text-center border-2 border-dashed border-gray-300">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-lg">No hay registros de turnos.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($historial as $turno): ?>
                            <div
                                class="bg-white border rounded-none p-4 hover:bg-gray-50 transition duration-200 relative overflow-hidden group border-gray-200">
                                <div class="absolute top-0 right-0 p-2">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wide <?php echo $turno['estado'] == 'abierto' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                                        <?php echo $turno['estado']; ?>
                                    </span>
                                </div>

                                <div class="flex items-center mb-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3">
                                        <?php echo strtoupper(substr($turno['usuario_nombre'] ?? 'U', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">
                                            <?php echo htmlspecialchars($turno['usuario_nombre'] ?? 'Usuario'); ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo date('d/m/Y', strtotime($turno['fecha_apertura'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                                    <div class="bg-gray-50 p-2 rounded">
                                        <p class="text-xs text-gray-500">Apertura</p>
                                        <p class="font-mono font-semibold text-gray-700">
                                            <?php echo date('H:i', strtotime($turno['fecha_apertura'])); ?>
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded">
                                        <p class="text-xs text-gray-500">Cierre</p>
                                        <p class="font-mono font-semibold text-gray-700">
                                            <?php echo $turno['fecha_cierre'] ? date('H:i', strtotime($turno['fecha_cierre'])) : '--:--'; ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if ($turno['estado'] === 'cerrado'): ?>
                                    <div class="flex justify-between items-end border-t pt-2">
                                        <div>
                                            <p class="text-xs text-gray-500">Real</p>
                                            <p class="font-bold text-gray-800">
                                                $<?php echo number_format($turno['monto_final'] ?? 0, 2); ?></p>
                                        </div>
                                        <?php $diff = $turno['diferencia'] ?? 0; ?>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Diff</p>
                                            <p class="font-bold <?php echo $diff >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                <?php echo $diff > 0 ? '+' : ''; ?>$<?php echo number_format($diff, 2); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <button onclick="verDetalles(<?php echo $turno['id']; ?>)"
                                    class="mt-3 w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium py-2 rounded-none text-sm transition">
                                    Ver Detalles
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- VISTA ADMIN / ESTANDAR -->
        <?php else: ?>
            <!-- Estado del Turno Actual -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white border border-gray-300 text-gray-800 p-6 shadow-none">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Estado</p>
                            <p class="text-2xl font-bold"><?php echo $turnoActivo ? 'TURNO ABIERTO' : 'SIN TURNO'; ?></p>
                        </div>
                        <i
                            class="fas fa-<?php echo $turnoActivo ? 'door-open' : 'door-closed'; ?> text-4xl text-gray-200"></i>
                    </div>
                </div>

                <div class="bg-white border border-gray-300 text-gray-800 p-6 shadow-none">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Monto Inicial</p>
                            <p class="text-2xl font-bold">
                                $<?php echo number_format($turnoActivo['monto_inicial'] ?? 0, 2); ?></p>
                        </div>
                        <i class="fas fa-coins text-4xl text-gray-200"></i>
                    </div>
                </div>

                <div class="bg-white border border-gray-300 text-gray-800 p-6 shadow-none">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Total Actual</p>
                            <p class="text-2xl font-bold">$<?php echo number_format($totalActual, 2); ?></p>
                        </div>
                        <i class="fas fa-wallet text-4xl text-gray-200"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Panel Izquierdo -->
                <div class="space-y-6">
                    <?php if (!$turnoActivo): ?>
                        <!-- Abrir Turno -->
                        <div class="bg-white p-6 border border-gray-300 shadow-none">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">
                                <i class="fas fa-door-open mr-2 text-gray-500"></i>Abrir Mi Turno
                            </h3>
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto Inicial *</label>
                                    <input type="number" name="monto_inicial" step="0.01" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas de Apertura</label>
                                    <textarea name="notas_apertura" rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                                </div>
                                <button type="submit" name="abrir_turno"
                                    class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-none transition shadow-none">
                                    <i class="fas fa-door-open mr-2"></i>Abrir Turno
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Registrar Movimiento -->
                        <div class="bg-white p-6 border border-gray-300 shadow-none">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">
                                <i class="fas fa-exchange-alt mr-2 text-gray-500"></i>Registrar Movimiento
                            </h3>
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                                    <select name="tipo" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="ingreso">Ingreso</option>
                                        <option value="egreso">Egreso</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto *</label>
                                    <input type="number" name="monto" step="0.01" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                                    <textarea name="descripcion" rows="2" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Ej: Pago a proveedor, Cobro de deuda, etc."></textarea>
                                </div>
                                <button type="submit" name="registrar_movimiento"
                                    class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 font-bold py-3 px-4 rounded-none transition shadow-none">
                                    <i class="fas fa-plus mr-2"></i>Agregar Movimiento
                                </button>
                            </form>
                        </div>

                        <!-- Cerrar Turno -->
                        <div class="bg-white p-6 border border-gray-300 shadow-none">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">
                                <i class="fas fa-door-closed mr-2 text-gray-500"></i>Cerrar Mi Turno
                            </h3>
                            <form method="POST"
                                onsubmit="return confirm('¿Estás seguro de cerrar el turno? Esta acción no se puede deshacer.');">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="turno_id" value="<?php echo $turnoActivo['id']; ?>">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto Real Contado *</label>
                                    <input type="number" name="monto_final" step="0.01" required
                                        class="w-full px-4 py-2 border border-gray-300 bg-gray-50 rounded-none focus:ring-2 focus:ring-gray-500 text-lg font-bold text-gray-800 placeholder-gray-400"
                                        placeholder="0.00">
                                    <p class="text-xs text-gray-500 mt-1">Ingresa el total exacto de dinero físico en caja.</p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas de Cierre</label>
                                    <textarea name="notas_cierre" rows="2"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                                </div>
                                <button type="submit" name="cerrar_turno"
                                    class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-none transition shadow-none">
                                    <i class="fas fa-door-closed mr-2"></i>Cerrar Turno
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Panel Derecho -->
                <div class="space-y-6">
                    <!-- Movimientos del Turno Actual -->
                    <?php if ($turnoActivo): ?>
                        <div class="bg-white p-6 border border-gray-300 shadow-none">
                            <h3 class="text-xl font-semibold mb-4">
                                <i class="fas fa-list text-gray-600 mr-2"></i>Movimientos del Turno
                            </h3>
                            <?php if (empty($movimientos)): ?>
                                <p class="text-gray-500 text-center py-8">No hay movimientos registrados</p>
                            <?php else: ?>
                                <div class="space-y-2 max-h-96 overflow-y-auto">
                                    <?php foreach ($movimientos as $mov): ?>
                                        <div class="p-3 bg-white border-b border-gray-100 <?php
                                        echo $mov['tipo'] == 'ingreso' || $mov['tipo'] == 'venta' || $mov['tipo'] == 'inicial' ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500';
                                        ?>">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-800">
                                                        <?php echo htmlspecialchars($mov['descripcion']); ?>
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        <?php echo date('d/m/Y H:i', strtotime($mov['created_at'])); ?>
                                                    </p>
                                                </div>
                                                <span
                                                    class="font-bold <?php echo $mov['tipo'] == 'egreso' ? 'text-red-600' : 'text-green-600'; ?>">
                                                    <?php echo $mov['tipo'] == 'egreso' ? '-' : '+'; ?>$<?php echo number_format($mov['monto'], 2); ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Historial de Turnos -->
                    <div class="bg-white p-6 border border-gray-300 shadow-none">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold">
                                <i class="fas fa-history text-gray-600 mr-2"></i>
                                <?php
                                if ($isAdmin || $userRole === 'cajero') {
                                    echo 'Historial de Todos los Turnos';
                                } else {
                                    echo 'Mis Turnos Anteriores';
                                }
                                ?>
                            </h3>
                            <?php if (($isAdmin || $userRole === 'cajero') && !empty($usuarios)): ?>
                                <form method="GET" class="flex gap-2">
                                    <select name="user_filter" onchange="this.form.submit()"
                                        class="px-3 py-1 border border-gray-300 rounded text-sm">
                                        <option value="">Todos los usuarios</option>
                                        <?php foreach ($usuarios as $u): ?>
                                            <option value="<?php echo $u['id']; ?>" <?php echo (isset($_GET['user_filter']) && $_GET['user_filter'] == $u['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($u['nombre'] ?: $u['username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($historial)): ?>
                            <p class="text-gray-500 text-center py-8">No hay turnos registrados</p>
                        <?php else: ?>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                <?php foreach ($historial as $turno): ?>
                                    <div class="p-4 bg-white border hover:bg-gray-50 transition border-gray-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold text-gray-800">
                                                    <?php echo htmlspecialchars($turno['usuario_nombre'] ?? 'Usuario'); ?>
                                                </span>
                                                <?php if ($turno['estado'] == 'abierto'): ?>
                                                    <span
                                                        class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 animate-pulse">
                                                        ABIERTO
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-200 text-gray-700">
                                                        CERRADO
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <button onclick="verDetalles(<?php echo $turno['id']; ?>)"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold focus:outline-none">
                                                <i class="fas fa-eye mr-1"></i>Ver Detalles
                                            </button>
                                        </div>

                                        <div class="flex text-xs text-gray-500 mb-2">
                                            <div class="w-1/2">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <?php echo date('d/m/Y', strtotime($turno['fecha_apertura'])); ?>
                                            </div>
                                            <div class="w-1/2 text-right">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?php echo date('H:i', strtotime($turno['fecha_apertura'])); ?>
                                                <?php if ($turno['fecha_cierre']): ?>
                                                    - <?php echo date('H:i', strtotime($turno['fecha_cierre'])); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if ($turno['estado'] == 'cerrado'): ?>
                                            <div class="grid grid-cols-3 gap-2 text-sm border-t border-gray-200 pt-2 mt-2">
                                                <div class="text-center">
                                                    <p class="text-xs text-gray-500 uppercase">Esperado</p>
                                                    <p class="font-semibold text-gray-700">
                                                        $<?php echo number_format($turno['monto_esperado'] ?? 0, 2); ?>
                                                    </p>
                                                </div>
                                                <div class="text-center border-l border-gray-200 border-r">
                                                    <p class="text-xs text-gray-500 uppercase">Real</p>
                                                    <p class="font-bold text-gray-800">
                                                        $<?php echo number_format($turno['monto_final'] ?? 0, 2); ?>
                                                    </p>
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-xs text-gray-500 uppercase">Diferencia</p>
                                                    <?php $diff = $turno['diferencia'] ?? 0; ?>
                                                    <p
                                                        class="font-bold <?php echo $diff >= 0 ? ($diff == 0 ? 'text-green-600' : 'text-green-600') : 'text-red-600'; ?>">
                                                        <?php echo $diff > 0 ? '+' : ''; ?>$<?php echo number_format($diff, 2); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Modal de Detalles del Turno -->
        <div id="modalDetalles" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col m-4">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-file-invoice-dollar text-purple-600 mr-2"></i>Detalle de Arqueo de Caja
                    </h3>
                    <button onclick="document.getElementById('modalDetalles').classList.add('hidden')"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto">
                    <!-- Info General -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <h4 class="font-semibold text-blue-800 mb-2">Información del Turno</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Usuario:</span>
                                    <span class="font-medium" id="modalUsuario"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Apertura:</span>
                                    <span class="font-medium" id="modalApertura"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Cierre:</span>
                                    <span class="font-medium" id="modalCierre"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Estado:</span>
                                    <span class="font-bold uppercase" id="modalEstado"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-2">Resumen Económico</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center border-b border-gray-200 pb-1">
                                    <span class="text-gray-600">Monto Inicial:</span>
                                    <span class="font-medium text-blue-600" id="modalInicial"></span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-200 pb-1">
                                    <span class="text-gray-600">Total Ingresos (+):</span>
                                    <span class="font-medium text-green-600" id="modalIngresos"></span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-200 pb-1">
                                    <span class="text-gray-600">Total Egresos (-):</span>
                                    <span class="font-medium text-red-600" id="modalEgresos"></span>
                                </div>
                                <div class="flex justify-between items-center pt-1">
                                    <span class="font-bold text-gray-700">Saldo Esperado:</span>
                                    <span class="font-bold text-xl" id="modalEsperado"></span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-200 pb-1">
                                    <span class="text-gray-600">Total Ventas Fiado (Crédito):</span>
                                    <span class="font-medium text-purple-600" id="modalVentasCC"></span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-200 pb-1">
                                    <span class="text-gray-600">Total Transferencias:</span>
                                    <span class="font-medium text-blue-600" id="modalVentasTransfer"></span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-200 pb-1">
                                    <span class="text-gray-600">Total Cobros Deuda (Entrada):</span>
                                    <span class="font-medium text-green-600" id="modalCobrosCC"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparativa Final (Solo si cerrado) -->
                    <div id="seccionArqueo" class="mb-8 hidden">
                        <div
                            class="bg-gradient-to-r from-gray-50 to-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">Resultado del Arqueo</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                                <div>
                                    <p class="text-sm text-gray-500 uppercase tracking-wider mb-1">Caja Esperada</p>
                                    <p class="text-2xl font-bold text-gray-700" id="arqueoEsperado"></p>
                                    <p class="text-xs text-gray-400">Calculado por sistema</p>
                                </div>
                                <div class="transform md:scale-110">
                                    <div class="bg-white p-4 rounded-xl shadow-md border-2 border-blue-100">
                                        <p class="text-sm text-blue-600 font-bold uppercase tracking-wider mb-1">Caja
                                            Real
                                        </p>
                                        <p class="text-3xl font-black text-gray-800" id="arqueoReal"></p>
                                        <p class="text-xs text-gray-500">Declarado por cajero</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 uppercase tracking-wider mb-1">Diferencia</p>
                                    <p class="text-2xl font-bold" id="arqueoDiferencia"></p>
                                    <p class="text-xs text-gray-400" id="arqueoEstado"></p>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-sm text-gray-600"><span class="font-semibold">Notas de Cierre:</span>
                                    <span id="modalNotas" class="italic"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Movimientos -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-list-alt text-gray-500 mr-2"></i>Detalle de Transacciones
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Hora</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descripción</th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="tablaMovimientos">
                                    <!-- JS content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button onclick="document.getElementById('modalDetalles').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <script>
            function verDetalles(id) {
                // Mostrar estado de carga
                const modal = document.getElementById('modalDetalles');
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                fetch(`cash.php?action=get_shift_details&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const t = data.turno;

                            // Llenar datos básicos
                            document.getElementById('modalUsuario').textContent = t.usuario_nombre || 'N/A';
                            document.getElementById('modalApertura').textContent = new Date(t.fecha_apertura).toLocaleString();
                            document.getElementById('modalCierre').textContent = t.fecha_cierre ? new Date(t.fecha_cierre).toLocaleString() : 'En curso';
                            document.getElementById('modalEstado').textContent = t.estado;
                            document.getElementById('modalEstado').className = `font-bold uppercase ${t.estado === 'abierto' ? 'text-green-600' : 'text-gray-600'}`;

                            // Calcular acumulados de movimientos
                            let ingresos = 0;
                            let egresos = 0;
                            const movimientos = data.movimientos;

                            // Limpiar tabla
                            const tbody = document.getElementById('tablaMovimientos');
                            tbody.innerHTML = '';

                            movimientos.forEach(m => {
                                if (['inicial', 'ingreso', 'venta'].includes(m.tipo)) {
                                    ingresos += parseFloat(m.monto);
                                } else if (m.tipo === 'egreso') {
                                    egresos += parseFloat(m.monto);
                                }

                                // Fila de tabla
                                const tr = document.createElement('tr');
                                tr.className = 'hover:bg-gray-50';
                                tr.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        ${m.tipo === 'egreso' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                                        ${m.tipo.toUpperCase()}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    ${m.descripcion}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium 
                                    ${m.tipo === 'egreso' ? 'text-red-600' : 'text-green-600'}">
                                    ${m.tipo === 'egreso' ? '-' : '+'}$${parseFloat(m.monto).toFixed(2)}
                                </td>
                            `;
                                tbody.appendChild(tr);
                            });

                            const esperado = ingresos - egresos;

                            // Llenar resumen económico
                            document.getElementById('modalInicial').textContent = `$${parseFloat(t.monto_inicial).toFixed(2)}`;
                            document.getElementById('modalIngresos').textContent = `+$${ingresos.toFixed(2)}`;
                            document.getElementById('modalEgresos').textContent = `-$${egresos.toFixed(2)}`;
                            document.getElementById('modalEgresos').textContent = `-$${egresos.toFixed(2)}`;
                            document.getElementById('modalVentasCC').textContent = `$${parseFloat(data.credit_sales_total || 0).toFixed(2)}`;
                            document.getElementById('modalVentasTransfer').textContent = `$${parseFloat(data.transfer_sales_total || 0).toFixed(2)}`;
                            document.getElementById('modalCobrosCC').textContent = `+$${parseFloat(data.debt_collections_total || 0).toFixed(2)}`;
                            document.getElementById('modalEsperado').textContent = `$${esperado.toFixed(2)}`;

                            // Sección de Arqueo (Solo si cerrado)
                            const seccionArqueo = document.getElementById('seccionArqueo');
                            if (t.estado === 'cerrado') {
                                seccionArqueo.classList.remove('hidden');
                                document.getElementById('arqueoEsperado').textContent = `$${parseFloat(t.monto_esperado).toFixed(2)}`;
                                document.getElementById('arqueoReal').textContent = `$${parseFloat(t.monto_final).toFixed(2)}`;

                                const diff = parseFloat(t.diferencia);
                                const elDiff = document.getElementById('arqueoDiferencia');
                                elDiff.textContent = `${diff > 0 ? '+' : ''}$${diff.toFixed(2)}`;
                                elDiff.className = `text-2xl font-bold ${diff === 0 ? 'text-green-600' : (diff > 0 ? 'text-green-600' : 'text-red-600')}`;

                                const elEstado = document.getElementById('arqueoEstado');
                                if (diff == 0) elEstado.textContent = 'Caje perfecta';
                                else if (diff > 0) elEstado.textContent = 'Sobrante en caja';
                                else elEstado.textContent = 'Faltante en caja';

                                document.getElementById('modalNotas').textContent = t.notas_cierre || 'Sin notas';
                            } else {
                                seccionArqueo.classList.add('hidden');
                            }

                        } else {
                            alert('Error al cargar detalles: ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        alert('Ocurrió un error al cargar los detalles.');
                    });
            }
        </script>
</body>

</html>