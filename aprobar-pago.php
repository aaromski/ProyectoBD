<?php
// aprobar-pago.php
require_once 'conexion.php';
$data = json_decode(file_get_contents('php://input'), true);
$id_transaccion = $data['id'] ?? null;

if (!$id_transaccion) {
  echo json_encode(['success' => false, 'message' => 'ID inválido']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Obtener la transacción pendiente para saber el monto original y quién es el chofer
  $stmt = $conn->prepare("SELECT id_usuario, monto FROM transacciones WHERE id_transaccion = ? AND estado = 'pendiente'");
  $stmt->execute([$id_transaccion]);
  $trans = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$trans) throw new Exception("Transacción no encontrada o ya procesada.");

  // Cálculo del 70%
  $monto_chofer = $trans['monto'] * 0.70;

  // 2. Marcar la transacción actual como finalizada
  $conn->prepare("UPDATE transacciones SET estado = 'finalizado' WHERE id_transaccion = ?")
    ->execute([$id_transaccion]);

  // 3. Crear la NUEVA transacción que registra el pago al chofer (el egreso/desembolso)
  $nro_ref_pago = 'PAGO-CH-' . strtoupper(bin2hex(random_bytes(3)));
  $stmtInsert = $conn->prepare("INSERT INTO transacciones
        (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado)
        VALUES (?, 'pago_chofer', 1, ?, ?, NOW(), 'finalizado')");
  $stmtInsert->execute([$trans['id_usuario'], $monto_chofer, $nro_ref_pago]);

  // 4. Sumar el 70% al saldo del chofer
  $conn->prepare("UPDATE choferes SET saldo = saldo + ? WHERE id_usuario = ?")
    ->execute([$monto_chofer, $trans['id_usuario']]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Pago aprobado, transacción registrada y saldo actualizado al 70%.']);
} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
