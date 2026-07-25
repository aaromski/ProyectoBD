<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_chofer = $data['id'] ?? null; // Este ID corresponde al id_chofer
$nro_ref = $data['nro_ref'] ?? null;
$detalles = $data['detalles'] ?? null;

if (!$id_chofer || !$nro_ref) {
  echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios o número de referencia.']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Consultar los datos del chofer, su saldo, su banco y su número de cuenta actual
  $stmt = $conn->prepare("SELECT id_chofer, id_banco, nro_cuenta, saldo FROM choferes WHERE id_chofer = ? FOR UPDATE");
  $stmt->execute([$id_chofer]);
  $chofer = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    throw new Exception("Chofer no encontrado.");
  }

  $monto_a_pagar = $chofer['saldo'];

  if ($monto_a_pagar <= 0) {
    throw new Exception("Este chofer ya no posee deudas pendientes.");
  }

  $id_personal = $_SESSION['id_usuario'];
  $id_banco = $chofer['id_banco'];
  $numero_cuenta = $chofer['nro_cuenta'];

  // 2. Insertar el registro completo en la tabla pago_chofer con sus campos correspondientes
  $sqlInsert = "INSERT INTO pago_chofer (id_chofer, id_personal, id_banco, numero_cuenta, monto, nro_ref, detalles, fecha)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
  $stmtInsert = $conn->prepare($sqlInsert);
  $stmtInsert->execute([
      $id_chofer,
      $id_personal,
      $id_banco,
      $numero_cuenta,
      $monto_a_pagar,
      $nro_ref,
      $detalles
  ]);

  // 3. Actualizar el saldo del chofer a 0 para que desaparezca de las deudas pendientes
  $sqlUpdate = "UPDATE choferes SET saldo = 0 WHERE id_chofer = ?";
  $conn->prepare($sqlUpdate)->execute([$id_chofer]);

  $conn->commit();
  echo json_encode([
    'success' => true,
    'message' => 'Pago aprobado con éxito. Se registró la transacción y se actualizó el saldo a Bs. 0.00'
  ]);

} catch (Exception $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
