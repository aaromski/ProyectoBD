<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';
$monto_bs = filter_input(INPUT_POST, 'monto_bs', FILTER_VALIDATE_FLOAT);

if (!$monto_bs || $monto_bs <= 0) {
  echo json_encode(['success' => false, 'message' => 'Monto inválido']);
  exit();
}

try {
  $id_usuario = $_SESSION['id_usuario'];
  /** @var PDO $conn */
  $conn->beginTransaction();

  $stmtChofer = $conn->prepare("SELECT id_chofer, saldo, id_banco, nro_cuenta FROM choferes WHERE id_usuario = :id_usuario FOR UPDATE");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Chofer no encontrado']);
    exit();
  }

  $id_chofer = $chofer['id_chofer'];
  $saldo_actual = (float)$chofer['saldo'];
  $id_banco = $chofer['id_banco'];
  $nro_cuenta = $chofer['nro_cuenta'];

  if ($monto_bs > $saldo_actual) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Fondos insuficientes']);
    exit();
  }

  $stmt_banco = $conn->prepare("SELECT nombre_banco FROM bancos WHERE id_banco = ?");
  $stmt_banco->execute([$id_banco]);
  $nombre_banco = $stmt_banco->fetchColumn() ?: 'N/A';

  $detalles = "Transferencia a {$nombre_banco} (Cta: {$nro_cuenta})";

  $conn->prepare("INSERT INTO pago_chofer (id_chofer, id_personal, id_banco, numero_cuenta, monto, nro_ref, fecha, estado, detalles) VALUES (?, NULL, ?, ?, ?, NULL, NOW(), 'pendiente', ?)")
    ->execute([$id_chofer, $id_banco, $nro_cuenta, $monto_bs, $detalles]);

  $conn->prepare("UPDATE choferes SET saldo = saldo - ? WHERE id_chofer = ?")
      ->execute([$monto_bs, $id_chofer]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Solicitud de retiro enviada. Pendiente de aprobación por personal.']);

} catch (Exception $e) {
  if ($conn->inTransaction()) $conn->rollBack();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
