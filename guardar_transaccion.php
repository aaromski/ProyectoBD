<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$id_usuario = $_SESSION['id_usuario'];
$tipo = strtolower($_POST['tipo']);
$monto = $_POST['monto'];
$id_banco = isset($_POST['id_banco']) ? (int)$_POST['id_banco'] : 1;
$fecha_usuario = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
$fecha = $fecha_usuario . ' ' . date('H:i:s');

// Lógica de blindaje para el número de referencia
if ($tipo === 'recarga') {
  // Para recargas, validamos que el usuario ingrese 6 dígitos reales
  $nro_ref = $_POST['nro_ref'];
  if (!preg_match('/^\d{6}$/', $nro_ref)) {
    die(json_encode(['success' => false, 'message' => 'La referencia de recarga debe ser de 6 dígitos']));
  }
} else {
  // Para movimientos internos (viajes/pagos), generamos referencia automática
  // Ejemplo: PAGO-123456 (prefijo + 6 dígitos aleatorios)
  $prefijo = ($tipo === 'pago_viaje') ? 'VIAJE-' : 'PAGO-';
  $nro_ref = $prefijo . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Lógica de estado
$estado = ($tipo === 'recarga' || $tipo === 'pago_chofer') ? 'finalizado' : 'pendiente';

/** @var PDO $conn */
$stmt = $conn->prepare("INSERT INTO transacciones (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");

try {
  $conn->beginTransaction();
  /** @var PDO $conn */
  $stmt = $conn->prepare("INSERT INTO transacciones (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$id_usuario, $tipo, $id_banco, $monto, $nro_ref, $fecha, $estado]);

  if ($tipo === 'recarga') {
    // Buscamos el id_cliente asociado al usuario
    $stmt_c = $conn->prepare("UPDATE clientes SET saldo = saldo + ? WHERE id_usuario = ?");
    $stmt_c->execute([$monto, $id_usuario]);
  }
  elseif ($tipo === 'pago_chofer') {
    // Buscamos el id_chofer asociado al usuario
    $stmt_ch = $conn->prepare("UPDATE choferes SET saldo = saldo + ? WHERE id_usuario = ?");
    $stmt_ch->execute([$monto, $id_usuario]);
  }

  $conn->commit();

  echo json_encode(['success' => true, 'message' => 'Transacción registrada. Ref: ' . $nro_ref]);
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: La referencia ya existe o datos inválidos']);
}
?>
