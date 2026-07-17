<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_traslado = $data['id'] ?? null;

if (!$id_traslado) {
  echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
  exit;
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Obtenemos el id_chofer, id_cliente y el costo del traslado
  $stmt = $conn->prepare("SELECT id_chofer, id_cliente, costo, id_zona_origen, id_zona_destino FROM traslados WHERE id_traslado = ?");
  $stmt->execute([$id_traslado]);
  $viaje = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$viaje) throw new Exception("Traslado no encontrado.");

  // 1.2 Obtener el nombre de la zona de origen
  $stmtOrig = $conn->prepare("SELECT nombre_zona FROM zonas WHERE id_zona = ?");
  $stmtOrig->execute([$viaje['id_zona_origen']]);
  $origen = $stmtOrig->fetchColumn() ?: 'N/A'; // Si no encuentra, pone N/A por seguridad

// 1.3 Obtener el nombre de la zona de destino
  $stmtDest = $conn->prepare("SELECT nombre_zona FROM zonas WHERE id_zona = ?");
  $stmtDest->execute([$viaje['id_zona_destino']]);
  $destino = $stmtDest->fetchColumn() ?: 'N/A'; // Si no encuentra, pone N/A por seguridad

  // 2. Obtener el id_usuario del cliente
  $stmtUser = $conn->prepare("SELECT id_usuario FROM clientes WHERE id_cliente = ?");
  $stmtUser->execute([$viaje['id_cliente']]);
  $clienteData = $stmtUser->fetch(PDO::FETCH_ASSOC);
  if (!$clienteData) throw new Exception("Usuario no vinculado al cliente.");
  $id_usuario_cliente = $clienteData['id_usuario'];

  // 3. Obtener el id_usuario del chofer
  $stmtChofer = $conn->prepare("SELECT id_usuario FROM choferes WHERE id_chofer = ?");
  $stmtChofer->execute([$viaje['id_chofer']]);
  $choferData = $stmtChofer->fetch(PDO::FETCH_ASSOC);
  if (!$choferData) throw new Exception("Usuario no vinculado al chofer.");
  $id_usuario_chofer = $choferData['id_usuario'];

  // 4. Actualizar estado del traslado a finalizado
  $conn->prepare("UPDATE traslados SET estado = 'finalizado' WHERE id_traslado = ?")->execute([$id_traslado]);

  // 5. Generar número de referencia base (6 dígitos)
  $ref_num = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

  // 6. Registrar pago_viaje → estado finalizado
  $nro_ref_viaje = 'VIAJE-' . $ref_num;
  $stmtTrans = $conn->prepare("INSERT INTO transacciones
        (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado)
        VALUES (?, 'pago_viaje', 1, ?, ?, NOW(), 'finalizado')");
  $stmtTrans->execute([$id_usuario_cliente, $viaje['costo'], $nro_ref_viaje]);

  // 7. Registrar pago_chofer → estado pendiente, monto = 70% del viaje
  $monto_chofer = $viaje['costo'] * 0.70;
  $nro_ref_chofer = 'CHOFER-' . $ref_num;
  $detalle_traslado = "Traslado de {$origen} a {$destino}";
  $stmtCh = $conn->prepare("INSERT INTO transacciones
        (id_usuario, tipo, id_banco, monto, nro_ref, fecha, estado, detalles)
        VALUES (?, 'pago_chofer', 1, ?, ?, NOW(), 'pendiente',?)");
  $stmtCh->execute([$id_usuario_chofer, $monto_chofer, $nro_ref_chofer, $detalle_traslado]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Viaje finalizado y pagos registrados.']);

} catch (Exception $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
