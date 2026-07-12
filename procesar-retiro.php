<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';
$monto_bs = filter_input(INPUT_POST, 'monto_bs', FILTER_VALIDATE_FLOAT);

if (!$monto_bs || $monto_bs <= 0) {
  echo json_encode(['success' => false, 'message' => 'Monto inválido']);
  exit();
}

try {
  $id_usuario = $_SESSION['id_usuario'];
  /** @var PDO $conn */
  $conn->beginTransaction();

  // Buscamos el registro del chofer por su id_usuario bloqueando la fila
  $stmtChofer = $conn->prepare("SELECT id_chofer, saldo FROM choferes WHERE id_usuario = :id_usuario FOR UPDATE");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Chofer no encontrado']);
    exit();
  }

  $id_chofer = $chofer['id_chofer'];
  $saldo_actual = (float)$chofer['saldo'];

  if ($monto_bs > $saldo_actual) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fondos insuficientes']);
    exit();
  }

  $nuevo_saldo = $saldo_actual - $monto_bs;
  $conn->prepare("UPDATE choferes SET saldo = :nuevo_saldo WHERE id_chofer = :id_chofer")
    ->execute([':nuevo_saldo' => $nuevo_saldo, ':id_chofer' => $id_chofer]);

  $conn->prepare("INSERT INTO historial_retiros (id_chofer, monto_bs, fecha) VALUES (:id_chofer, :monto_bs, NOW())")
    ->execute([':id_chofer' => $id_chofer, ':monto_bs' => $monto_bs]);

  $conn->commit();
  echo json_encode(['success' => true]);

} catch (Exception $e) {
  if ($conn->inTransaction()) $conn->rollBack();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
