<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_zona_origen = (int)$_POST['origen'];
$id_zona_destino = (int)$_POST['destino'];
$costo = (float)$_POST['costo'];

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Obtener el ID real del cliente y bloquear la fila para evitar gastos dobles (FOR UPDATE)
  $stmt_saldo = $conn->prepare("SELECT id_cliente, saldo FROM clientes WHERE id_usuario = ? FOR UPDATE");
  $stmt_saldo->execute([$id_usuario]);
  $cliente = $stmt_saldo->fetch(PDO::FETCH_ASSOC);

  if (!$cliente || $cliente['saldo'] < $costo) {
    throw new Exception("Saldo insuficiente en su billetera virtual.");
  }
  $id_cliente = $cliente['id_cliente'];

  // 2. MAGIA PURA: Seleccionar un chofer y un vehículo 100% aptos al azar en una sola consulta
  $stmt_asignacion = $conn->prepare("
    SELECT 
        c.id_chofer, 
        v.id_vehiculo,
        u.nombres AS chofer_nombres,
        u.apellidos AS chofer_apellidos,
        u.telefono AS chofer_telefono,
        v.marca,
        v.modelo,
        v.placa
    FROM choferes c
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    INNER JOIN evaluaciones_choferes ec ON c.id_chofer = ec.id_chofer AND ec.estado = 'aprobado'
    INNER JOIN vehiculos v ON c.id_chofer = v.id_chofer
    INNER JOIN evaluaciones_vehiculos ev ON v.id_vehiculo = ev.id_vehiculo AND ev.estado = 'apto'
    ORDER BY RAND()
    LIMIT 1
  ");
  $stmt_asignacion->execute();
  $asignacion = $stmt_asignacion->fetch(PDO::FETCH_ASSOC);

  if (!$asignacion) {
    throw new Exception("Lo sentimos, no hay choferes o vehículos disponibles en este momento.");
  }

  // 3. Descontar el 100% del costo al cliente
  $conn->prepare("UPDATE clientes SET saldo = saldo - ? WHERE id_cliente = ?")->execute([$costo, $id_cliente]);

  // 4. Repartir el dinero: El sistema se queda con 30%, se le suma el 70% al chofer
  $pago_chofer = $costo * 0.70;
  $conn->prepare("UPDATE choferes SET saldo = saldo + ? WHERE id_chofer = ?")->execute([$pago_chofer, $asignacion['id_chofer']]);

  // 5. Registrar el traslado como completado de una vez con su vehículo asignado
  $sql_traslado = "INSERT INTO traslados (id_cliente, id_chofer, id_zona_origen, id_zona_destino, costo, estado, id_vehiculo, fecha)
                   VALUES (?, ?, ?, ?, ?, 'realizado', ?, NOW())";
                   
  $stmt_insert = $conn->prepare($sql_traslado);
  $stmt_insert->execute([
    $id_cliente,
    $asignacion['id_chofer'],
    $id_zona_origen,
    $id_zona_destino,
    $costo,
    $asignacion['id_vehiculo']
  ]);

  $conn->commit();
  
  // 6. Devolverle al Frontend los datos exactos del conductor y el carro para mostrarlos
  echo json_encode([
    'success' => true, 
    'message' => '¡Tu conductor está en camino!',
    'datos_viaje' => [
        'chofer' => $asignacion['chofer_nombres'] . ' ' . $asignacion['chofer_apellidos'],
        'telefono' => $asignacion['chofer_telefono'],
        'vehiculo' => $asignacion['marca'] . ' ' . $asignacion['modelo'],
        'placa' => $asignacion['placa']
    ]
  ]);

} catch (Exception $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>