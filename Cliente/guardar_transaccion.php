<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

/** @var PDO $conn */
$id_usuario = $_SESSION['id_usuario'];
$monto = $_POST['monto'];
$id_cuenta_empresa = isset($_POST['id_cuenta_empresa']) ? (int)$_POST['id_cuenta_empresa'] : 1;
$fecha_pago = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');

$nro_ref = $_POST['nro_ref'];
if (!preg_match('/^\d{6}$/', $nro_ref)) {
  die(json_encode(['success' => false, 'message' => 'La referencia de recarga debe ser de 6 dígitos']));
}
$nro_ref_completo = 'REC-' . $nro_ref;

try {
  $stmt_banco = $conn->prepare("SELECT id_banco FROM cuentas_empresa WHERE id_cuenta = ?");
  $stmt_banco->execute([$id_cuenta_empresa]);
  $id_banco = $stmt_banco->fetchColumn();

  if (!$id_banco) {
      die(json_encode(['success' => false, 'message' => 'El banco asociado a la cuenta no existe']));
  }

  $stmt_cliente = $conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
  $stmt_cliente->execute([$id_usuario]);
  $id_cliente = $stmt_cliente->fetchColumn();

  if (!$id_cliente) {
    die(json_encode(['success' => false, 'message' => 'Cliente no encontrado']));
  }

  $conn->beginTransaction();

  $stmt = $conn->prepare("INSERT INTO recargas (id_cliente, id_banco, monto, nro_ref, fecha_pago) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$id_cliente, $id_banco, $monto, $nro_ref_completo, $fecha_pago]);

  $conn->prepare("UPDATE clientes SET saldo = saldo + ? WHERE id_cliente = ?")->execute([$monto, $id_cliente]);

  $conn->commit();

  echo json_encode(['success' => true, 'message' => 'Recarga registrada. Ref: ' . $nro_ref_completo]);
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: La referencia ya existe o datos inválidos']);
}
?>
