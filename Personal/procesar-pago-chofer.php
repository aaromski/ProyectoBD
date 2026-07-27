<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_chofer = intval($data['id_chofer'] ?? 0);
$nro_ref   = trim($data['nro_ref'] ?? '');
$detalles  = trim($data['detalles'] ?? '');

if ($id_chofer <= 0 || empty($nro_ref)) {
  echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios (id_chofer, nro_ref).']);
  exit;
}

if (!preg_match('/^\d{6}$/', $nro_ref)) {
  echo json_encode(['success' => false, 'message' => 'La referencia debe ser exactamente 6 dígitos numéricos.']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Obtener datos del chofer
  $stmt = $conn->prepare("
    SELECT c.id_chofer, c.id_banco, c.nro_cuenta
    FROM choferes c
    WHERE c.id_chofer = ?
    FOR UPDATE
  ");
  $stmt->execute([$id_chofer]);
  $chofer = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    throw new Exception("Chofer no encontrado.");
  }

  // 2. Calcular la suma total de traslados finalizados sin pago
  $stmt = $conn->prepare("
    SELECT ROUND(SUM(costo * 0.70), 2) AS total
    FROM traslados
    WHERE id_chofer = ?
      AND estado = 'finalizado'
      AND id_pago_chofer IS NULL
  ");
  $stmt->execute([$id_chofer]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $monto_total = floatval($row['total'] ?? 0);

  if ($monto_total <= 0) {
    throw new Exception("No hay traslados pendientes de pago para este chofer.");
  }

  // 3. Insertar registro en pago_chofer
  $stmt = $conn->prepare("
    INSERT INTO pago_chofer (id_chofer, id_personal, id_banco, numero_cuenta, monto, nro_ref, detalles, fecha)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
  ");
  $stmt->execute([
    $id_chofer,
    $_SESSION['id_usuario'],
    $chofer['id_banco'],
    $chofer['nro_cuenta'],
    $monto_total,
    $nro_ref,
    $detalles ?: null
  ]);

  $id_pago = $conn->lastInsertId();

  // 4. Actualizar los traslados para vincularlos al pago
  $stmt = $conn->prepare("
    UPDATE traslados
    SET id_pago_chofer = ?
    WHERE id_chofer = ?
      AND estado = 'finalizado'
      AND id_pago_chofer IS NULL
  ");
  $stmt->execute([$id_pago, $id_chofer]);

  // 5. Descontar saldo del chofer
  $stmt = $conn->prepare("
    UPDATE choferes
    SET saldo = GREATEST(saldo - ?, 0)
    WHERE id_chofer = ?
  ");
  $stmt->execute([$monto_total, $id_chofer]);

  $conn->commit();

  echo json_encode([
    'success' => true,
    'message' => "Pago procesado exitosamente. Bs. " . number_format($monto_total, 2, '.', '') . " transferidos al chofer."
  ]);

} catch (Exception $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
