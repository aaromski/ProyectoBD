<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

// Cambiamos a cliente_id directamente desde la sesión
if (!isset($_SESSION['cliente_id'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

$id_cliente = $_SESSION['cliente_id'];
$id_zona_origen = (int)$_POST['origen'];
$id_zona_destino = (int)$_POST['destino'];
$costo = (float)$_POST['costo'];

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // Consultamos el saldo directamente usando el id_cliente de la sesión
  $stmt_saldo = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ? FOR UPDATE");
  $stmt_saldo->execute([$id_cliente]);
  $cliente = $stmt_saldo->fetch(PDO::FETCH_ASSOC);

  if (!$cliente) {
      throw new Exception("Perfil de cliente no encontrado.");
  }

  if ($cliente['saldo'] < $costo) {
      throw new Exception("Saldo insuficiente.");
  }

  // Descontamos el saldo
  $conn->prepare("UPDATE clientes SET saldo = saldo - ? WHERE id_cliente = ?")->execute([$costo, $id_cliente]);

  // Seleccionar un chofer disponible
  $stmt_chofer = $conn->prepare("SELECT c.id_chofer, v.id_vehiculo,
    u.nombres AS chofer_nombres,
    u.apellidos AS chofer_apellidos,
    u.telefono AS chofer_telefono,
    v.marca,
    v.modelo,
    v.placa
    FROM choferes c
    INNER JOIN usuarios u ON u.id_usuario = c.id_usuario
    INNER JOIN vehiculos v ON v.id_chofer = c.id_chofer
    WHERE v.activo = 1
      AND EXISTS (
        SELECT 1 FROM evaluaciones_choferes ec
        WHERE ec.id_chofer = c.id_chofer AND ec.estado = 'aprobado'
      )
      AND EXISTS (
        SELECT 1 FROM evaluaciones_vehiculos ev
        WHERE ev.id_vehiculo = v.id_vehiculo AND ev.estado = 'apto'
      )
    ORDER BY RAND()
    LIMIT 1");
  $stmt_chofer->execute();
  $chofer = $stmt_chofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    throw new Exception("No hay choferes disponibles actualmente.");
  }

  // Registrar el traslado
  $sql_traslado = "INSERT INTO traslados (id_cliente, id_chofer, id_zona_origen, id_zona_destino, costo, estado, id_vehiculo, fecha)
                     VALUES (?, ?, ?, ?, ?, 'en_curso', ?, NOW())";

  $stmt_insert = $conn->prepare($sql_traslado);
  $stmt_insert->execute([
    $id_cliente,
    $chofer['id_chofer'],
    $id_zona_origen,
    $id_zona_destino,
    $costo,
    $chofer['id_vehiculo']
  ]);

  $conn->commit();
  echo json_encode([
      'success' => true,
      'message' => '¡Tu conductor está en camino!',
      'datos_viaje' => [
          'chofer' => $chofer['chofer_nombres'] . ' ' . $chofer['chofer_apellidos'],
          'telefono' => $chofer['chofer_telefono'],
          'vehiculo' => $chofer['marca'] . ' ' . $chofer['modelo'],
          'placa' => $chofer['placa']
      ]
  ]);

} catch (Exception $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
