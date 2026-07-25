<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

/** @var PDO $conn */
$id_usuario = $_SESSION['id_usuario'];
$monto = (float)$_POST['monto'];
$id_cuenta_empresa = isset($_POST['id_cuenta_empresa']) ? (int)$_POST['id_cuenta_empresa'] : 0;
$fecha_pago = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
$id_banco = 1; // Valor por defecto

$nro_ref = trim($_POST['nro_ref']);

if (!preg_match('/^\d{6}$/', $nro_ref)) {
  die(json_encode(['success' => false, 'message' => 'La referencia de recarga debe ser de 6 dígitos exactos.']));
}

// Así guardaba tu grupo las referencias en la nueva tabla recargas
$nro_ref_completo = 'REC-' . $nro_ref; 

try {
  $conn->beginTransaction();

  // 1. Obtener el ID REAL del cliente (la tabla recargas exige id_cliente, no id_usuario)
  $stmt_cliente = $conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
  $stmt_cliente->execute([$id_usuario]);
  $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
  
  if (!$cliente) {
    throw new Exception("Cliente no encontrado en la base de datos.");
  }
  $id_cliente = $cliente['id_cliente'];

  // 2. Obtener el banco al que se transfirió
  $stmt_cuenta = $conn->prepare("SELECT id_banco FROM cuentas_empresa WHERE id_cuenta = ?");
  $stmt_cuenta->execute([$id_cuenta_empresa]);
  $cuenta_data = $stmt_cuenta->fetch(PDO::FETCH_ASSOC);
  
  if ($cuenta_data) {
      $id_banco = $cuenta_data['id_banco'];
  }

  // 3. MAGIA: Insertar en la NUEVA tabla 'recargas' en vez de la vieja 'transacciones'
  $stmt = $conn->prepare("INSERT INTO recargas (id_cliente, id_banco, monto, nro_ref, fecha_pago, fecha_registro) VALUES (?, ?, ?, ?, ?, NOW())");
  $stmt->execute([$id_cliente, $id_banco, $monto, $nro_ref_completo, $fecha_pago]);

  // 4. Aumentar el saldo del cliente
  $stmt_c = $conn->prepare("UPDATE clientes SET saldo = saldo + ? WHERE id_cliente = ?");
  $stmt_c->execute([$monto, $id_cliente]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => '¡Recarga registrada exitosamente!']);
  
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()]);
} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>