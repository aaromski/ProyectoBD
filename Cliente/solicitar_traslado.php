<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

$id_cliente = $_SESSION['id_usuario'];
// Corregido: Aseguramos que recibimos los IDs de las zonas
$id_zona_origen = (int)$_POST['origen'];
$id_zona_destino = (int)$_POST['destino'];
$costo = (float)$_POST['costo'];

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Verificar y descontar saldo del cliente
  $stmt_saldo = $conn->prepare("SELECT saldo FROM clientes WHERE id_usuario = ? FOR UPDATE");
  $stmt_saldo->execute([$id_cliente]);
  $cliente = $stmt_saldo->fetch(PDO::FETCH_ASSOC);

  if (!$cliente || $cliente['saldo'] < $costo) {
    throw new Exception("Saldo insuficiente o usuario no encontrado.");
  }

  $conn->prepare("UPDATE clientes SET saldo = saldo - ? WHERE id_usuario = ?")->execute([$costo, $id_cliente]);

  // 2. Seleccionar un chofer que tenga evaluaciones aprobadas (psicológica y técnica)
  $stmt_chofer = $conn->prepare("SELECT c.id_chofer
    FROM choferes c
      WHERE EXISTS (
      SELECT 1 FROM evaluaciones_choferes ec
      WHERE ec.id_chofer = c.id_chofer AND ec.estado = 'aprobado'
  )
      AND EXISTS (
      SELECT 1 FROM vehiculos v
      INNER JOIN evaluaciones_vehiculos ev ON v.id_vehiculo = ev.id_vehiculo
      WHERE v.id_chofer = c.id_chofer AND ev.estado = 'apto'
  )
    ORDER BY RAND()
    LIMIT 1");
  $stmt_chofer->execute();
  $chofer = $stmt_chofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    throw new Exception("No hay choferes disponibles actualmente.");
  }

  // 3. Registrar el traslado con las columnas correctas
  // id_vehiculo se deja NULL para que el chofer lo elija al aceptar
  $sql_traslado = "INSERT INTO traslados (id_cliente, id_chofer, id_zona_origen, id_zona_destino, costo, estado, id_vehiculo, fecha)
                     VALUES (?, ?, ?, ?, ?, 'pendiente', NULL, NOW())";

  $stmt_insert = $conn->prepare($sql_traslado);
  $stmt_insert->execute([
    $id_cliente,
    $chofer['id_chofer'],
    $id_zona_origen,
    $id_zona_destino,
    $costo
  ]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Traslado solicitado con éxito']);

} catch (Exception $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
