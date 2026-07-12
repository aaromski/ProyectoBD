<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_traslado = $data['id'] ?? null;

if (!$id_traslado) {
  echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Obtenemos el id_cliente y el costo del traslado
  $stmt = $conn->prepare("SELECT id_chofer, costo FROM traslados WHERE id_traslado = ?");
  $stmt->execute([$id_traslado]);
  $viaje = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$viaje) throw new Exception("Traslado no encontrado.");

  // 2. BUSCAMOS EL ID_USUARIO RELACIONADO AL CLIENTE
  // Asumiendo que en tu tabla 'clientes' tienes el campo 'id_usuario' que los vincula
  $stmtUser = $conn->prepare("SELECT id_usuario FROM clientes WHERE id_cliente = ?");
  $stmtUser->execute([$viaje['id_cliente']]);
  $clienteData = $stmtUser->fetch(PDO::FETCH_ASSOC);

  if (!$clienteData) throw new Exception("Usuario no vinculado al cliente.");
  $id_usuario = $clienteData['id_usuario'];

  // 3. Actualizar estado a finalizado
  $conn->prepare("UPDATE traslados SET estado = 'finalizado' WHERE id_traslado = ?")->execute([$id_traslado]);

  // 4. Registrar en transacciones (usando id_usuario como pide la tabla)
  $nro_ref = 'VIAJE-' . strtoupper(bin2hex(random_bytes(3)));

  $stmtTrans = $conn->prepare("INSERT INTO transacciones
        (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado)
        VALUES (?, 'pago_viaje', 1, ?, ?, NOW(), 'pendiente')");

  $stmtTrans->execute([$id_usuario, $viaje['costo'], $nro_ref]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Viaje finalizado y pago registrado.']);

} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
