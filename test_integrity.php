<?php
require_once __DIR__ . '/app/bootstrap.php';

echo "<h2>Integrity Test</h2>\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_HOST: " . DB_HOST . "\n";

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create Dummy Customer if needed
    $stmt = $pdo->query("SELECT id FROM clientes LIMIT 1");
    $cid = $stmt->fetchColumn();
    if (!$cid) {
        $pdo->exec("INSERT INTO clientes (nombre, telefono) VALUES ('Test User', '123')");
        $cid = $pdo->lastInsertId();
        echo "Created Test Customer ID: $cid\n";
    } else {
        echo "Using Customer ID: $cid\n";
    }

    // 2. Transact
    echo "Starting Transaction...\n";
    $pdo->beginTransaction();

    $amount = 50.00;
    $uid = 6; // Admin usually
    $shift_id = 34; // From previous check
    $desc = "Test Payment CLI " . date('H:i:s');

    echo "Inserting into cliente_pagos...\n";
    $stmt = $pdo->prepare("INSERT INTO cliente_pagos (cliente_id, monto, usuario_id, turno_id, descripcion, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$cid, $amount, $uid, $shift_id, $desc]);
    $pid = $pdo->lastInsertId();
    echo "Inserted Payment ID: $pid\n";

    echo "Committing...\n";
    $pdo->commit();

    // 3. Verify
    echo "Verifying...\n";
    $stmt = $pdo->query("SELECT * FROM cliente_pagos WHERE id = $pid");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "SUCCESS: Found Payment in DB.\n";
        print_r($row);
    } else {
        echo "FAILURE: Payment NOT found in DB after commit.\n";
    }

    // 4. Cleanup
    // $pdo->exec("DELETE FROM cliente_pagos WHERE id = $pid");
    // echo "Cleaned up.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>