<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

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
  $stmt = $conn->prepare("SELECT id_cliente, costo, estado
                            FROM traslados
                            WHERE id_traslado = ? FOR UPDATE");
  $stmt->execute([$id_traslado]);
  $viaje = $stmt->fetch(PDO::FETCH_ASSOC);
  // Debug: Ver qué valor tiene el costo antes de actualizar
  error_log("Costo recuperado: " . $viaje['costo']);
  error_log("ID Cliente recuperado: " . $viaje['id_cliente']);
  if (!$viaje) {
    throw new Exception("Traslado no encontrado.");
  }

  if ($viaje['estado'] === 'cancelado') {
    throw new Exception("El viaje ya fue cancelado previamente.");
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
