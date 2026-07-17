<?php
include('../conexion.php');
session_start();


if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['success' => false, 'message' => 'No autorizado']));
}
/** @var PDO $conn */
$id_usuario = $_SESSION['id_usuario'];
$tipo = strtolower($_POST['tipo']);
$monto = $_POST['monto'];
$id_banco = isset($_POST['id_cuenta_empresa']) ? (int)$_POST['id_cuenta_empresa'] : 1;
$fecha_usuario = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
$fecha = $fecha_usuario . ' ' . date('H:i:s');
$detalles = null;
// Lógica de blindaje para el número de referencia
if ($tipo === 'recarga') {
  // Para recargas, validamos que el usuario ingrese 6 dígitos reales
  $nro_ref = $_POST['nro_ref'];
  if (!preg_match('/^\d{6}$/', $nro_ref)) {
    die(json_encode(['success' => false, 'message' => 'La referencia de recarga debe ser de 6 dígitos']));
  }
  $nro_ref_completo = 'REC-' . $nro_ref;

  try {
    $stmt_banco = $conn->prepare("SELECT nombre_banco FROM bancos WHERE id_banco = ?");
    $stmt_banco->execute([$id_banco]);
    $banco = $stmt_banco->fetch(PDO::FETCH_ASSOC);

    if ($banco) {
      $detalles = "Recarga a " . $banco['nombre_banco'];
    } else {
      $detalles = "Recarga a Banco no especificado";
    }
  } catch (PDOException $e) {
    // Si falla la búsqueda del banco por alguna razón, ponemos un texto por defecto
    $detalles = "Recarga de saldo";
  }

} else {
  // Para movimientos internos (viajes/pagos), generamos referencia automática
  $ref_num = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
  if ($tipo === 'pago_viaje') {
    $nro_ref_completo = 'VIAJE-' . $ref_num;
  } else {
    $nro_ref_completo = 'PAGO-' . $ref_num;
  }
}

// Lógica de estado
$estado = ($tipo === 'recarga') ? 'finalizado' : 'pendiente';


try {
  $conn->beginTransaction();

  // Insertar la transacción principal
  $stmt = $conn->prepare("INSERT INTO transacciones (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado, detalles) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$id_usuario, $tipo, $id_banco, $monto, $nro_ref_completo, $fecha, $estado, $detalles]);

  if ($tipo === 'recarga') {
    $stmt_c = $conn->prepare("UPDATE clientes SET saldo = saldo + ? WHERE id_usuario = ?");
    $stmt_c->execute([$monto, $id_usuario]);
  }

  $conn->commit();

  echo json_encode(['success' => true, 'message' => 'Transacción registrada. Ref: ' . $nro_ref_completo]);
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: La referencia ya existe o datos inválidos']);
}
?>
