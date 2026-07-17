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

  // 1. Obtener el pago_chofer pendiente
  $stmt = $conn->prepare("SELECT id_usuario, nro_ref FROM transacciones WHERE id_transaccion = ? AND tipo = 'pago_chofer' AND estado = 'pendiente'");
  $stmt->execute([$id_transaccion]);
  $trans = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$trans) throw new Exception("Transacción no encontrada o ya procesada.");

  // 2. Extraer el número base (quitar prefijo CHOFER-)
  $ref_num = str_replace('CHOFER-', '', $trans['nro_ref']);

  // 3. Buscar el pago_viaje pareado para obtener el monto original
  $nro_ref_viaje = 'VIAJE-' . $ref_num;
  $stmt_viaje = $conn->prepare("SELECT monto FROM transacciones WHERE nro_ref = ? AND tipo = 'pago_viaje'");
  $stmt_viaje->execute([$nro_ref_viaje]);
  $viaje = $stmt_viaje->fetch(PDO::FETCH_ASSOC);

  if (!$viaje) throw new Exception("No se encontró el viaje asociado.");

  // 4. Calcular el 70% del monto del viaje
  $monto_chofer = $viaje['monto'] * 0.70;

  // 5. Actualizar el pago_chofer con el monto del 70% y marcar como finalizado
  $conn->prepare("UPDATE transacciones SET monto = ?, estado = 'finalizado' WHERE id_transaccion = ?")
    ->execute([$monto_chofer, $id_transaccion]);

  // 6. Sumar el 70% al saldo del chofer
  $conn->prepare("UPDATE choferes SET saldo = saldo + ? WHERE id_usuario = ?")
    ->execute([$monto_chofer, $trans['id_usuario']]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Pago aprobado. Chofer recibió el 70% (Bs. ' . number_format($monto_chofer, 2) . ').']);
} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
