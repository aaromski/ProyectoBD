<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_pago = $data['id'] ?? null;
$nro_ref = $data['nro_ref'] ?? null;
$detalles = $data['detalles'] ?? null;

if (!$id_pago) {
  echo json_encode(['success' => false, 'message' => 'ID inválido']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  $stmt = $conn->prepare("SELECT id_pago, id_chofer, monto, estado FROM pago_chofer WHERE id_pago = ?");
  $stmt->execute([$id_pago]);
  $pago = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$pago) throw new Exception("Pago no encontrado.");
  if ($pago['estado'] !== 'pendiente') throw new Exception("Este pago ya fue procesado.");

  $id_personal = $_SESSION['id_usuario'];

  $conn->prepare("UPDATE pago_chofer SET id_personal = ?, nro_ref = ?, estado = 'finalizado', fecha = NOW() WHERE id_pago = ?")
    ->execute([$id_personal, $nro_ref, $id_pago]);

  if (!empty($detalles)) {
    $conn->prepare("UPDATE pago_chofer SET detalles = ? WHERE id_pago = ?")
      ->execute([$detalles, $id_pago]);
  }

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Pago aprobado. Se descontó Bs. ' . number_format($pago['monto'], 2) . ' del saldo del chofer.']);

} catch (Exception $e) {
  if ($conn->inTransaction()) $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
