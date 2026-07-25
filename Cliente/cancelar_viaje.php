<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

// Recibimos el ID del traslado
$id_traslado = $_POST['id'] ?? null;

if (!$id_traslado) {
  echo json_encode(['success' => false, 'message' => 'ID de traslado no proporcionado']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Obtenemos el ID del cliente y el monto que se le descontó originalmente
  $stmt = $conn->prepare("SELECT id_cliente, costo, estado, fecha
                            FROM traslados
                            WHERE id_traslado = ? FOR UPDATE");
  $stmt->execute([$id_traslado]);
  $viaje = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$viaje) {
    throw new Exception("Traslado no encontrado.");
  }

  if ($viaje['estado'] === 'cancelado') {
    throw new Exception("El viaje ya fue cancelado previamente.");
  }

  // 2. Validar que no hayan pasado más de 15 minutos desde la creación del viaje (usar NOW() de MySQL para evitar desfase de zona horaria)
  $stmtCheck = $conn->prepare("SELECT TIMESTAMPDIFF(MINUTE, fecha, NOW()) AS minutos FROM traslados WHERE id_traslado = ?");
  $stmtCheck->execute([$id_traslado]);
  $diff = $stmtCheck->fetch(PDO::FETCH_ASSOC);

  if ($diff && $diff['minutos'] > 15) {
    throw new Exception("No es posible cancelar el viaje. Han pasado más de 15 minutos desde su creación.");
  }

  // 2. Marcamos el traslado como cancelado
  $updateViaje = $conn->prepare("UPDATE traslados SET estado = 'cancelado' WHERE id_traslado = ?");
  $updateViaje->execute([$id_traslado]);

  // 3. Devolvemos el dinero al saldo del cliente en la tabla 'clientes'
  $devolverSaldo = $conn->prepare("UPDATE clientes SET saldo = saldo + ? WHERE id_cliente = ?");
  $devolverSaldo->execute([$viaje['costo'], $viaje['id_cliente']]);

  $conn->commit();

  echo json_encode(['success' => true, 'message' => 'Viaje cancelado y saldo reintegrado.']);

} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
