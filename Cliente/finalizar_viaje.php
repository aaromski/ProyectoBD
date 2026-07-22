<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_traslado = $data['id'] ?? null;

if (!$id_traslado) {
  echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  $stmt = $conn->prepare("SELECT id_chofer, costo FROM traslados WHERE id_traslado = ?");
  $stmt->execute([$id_traslado]);
  $viaje = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$viaje) throw new Exception("Traslado no encontrado.");

  $conn->prepare("UPDATE traslados SET estado = 'finalizado' WHERE id_traslado = ?")->execute([$id_traslado]);

  $monto_chofer = $viaje['costo'] * 0.70;
  $conn->prepare("UPDATE choferes SET saldo = saldo + ? WHERE id_chofer = ?")->execute([$monto_chofer, $viaje['id_chofer']]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Viaje finalizado y saldo acreditado al chofer.']);

} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
