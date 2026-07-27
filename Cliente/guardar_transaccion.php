<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['cliente_id'])) {
  die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

/** @var PDO $conn */
$id_cliente = $_SESSION['cliente_id'];
$monto = $_POST['monto'];
$id_cuenta = isset($_POST['id_cuenta']) ? (int)$_POST['id_cuenta'] : 0;
$fecha_pago = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');

$nro_ref = trim($_POST['nro_ref']);
if (!preg_match('/^\d{6}$/', $nro_ref)) {
  die(json_encode(['success' => false, 'message' => 'La referencia de recarga debe ser de 6 dígitos']));
}

if ($id_cuenta <= 0) {
  die(json_encode(['success' => false, 'message' => 'Debe seleccionar una cuenta destino válida']));
}

try {
  $stmt_check = $conn->prepare("SELECT id_cuenta FROM cuentas_empresa WHERE id_cuenta = ? AND estado = 'activo'");
  $stmt_check->execute([$id_cuenta]);
  if (!$stmt_check->fetchColumn()) {
    die(json_encode(['success' => false, 'message' => 'La cuenta seleccionada no existe o está inactiva']));
  }

  $conn->beginTransaction();

  $stmt = $conn->prepare("INSERT INTO recargas (id_cliente, id_cuenta, monto, nro_ref, fecha_pago, fecha_registro) VALUES (?, ?, ?, ?, ?, NOW())");
  $stmt->execute([$id_cliente, $id_cuenta, $monto, $nro_ref, $fecha_pago]);

  $conn->prepare("UPDATE clientes SET saldo = saldo + ? WHERE id_cliente = ?")->execute([$monto, $id_cliente]);

  $conn->commit();

  echo json_encode(['success' => true, 'message' => 'Recarga registrada. Ref: ' . $nro_ref]);
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: La referencia ya existe o datos inválidos']);
}
?>