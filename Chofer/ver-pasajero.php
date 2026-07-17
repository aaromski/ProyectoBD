<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado.']);
  exit();
}

/** @var PDO $conn */
$id_usuario_chofer = $_SESSION['id_usuario'];
$id_viaje = intval($_GET['id_viaje'] ?? 0);

if ($id_viaje <= 0) {
  echo json_encode(['success' => false, 'message' => 'ID de viaje inválido.']);
  exit();
}

try {
  $stmt = $conn->prepare("SELECT u.nombres, u.apellidos, u.telefono
    FROM traslados t
    INNER JOIN clientes c ON t.id_cliente = c.id_cliente
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    INNER JOIN choferes ch ON t.id_chofer = ch.id_chofer
    WHERE t.id_traslado = :id_viaje AND ch.id_usuario = :id_chofer");
  $stmt->execute([':id_viaje' => $id_viaje, ':id_chofer' => $id_usuario_chofer]);
  $pasajero = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$pasajero) {
    echo json_encode(['success' => false, 'message' => 'Viaje no encontrado o no pertenece a este chofer.']);
    exit();
  }

  echo json_encode([
    'success' => true,
    'nombres' => $pasajero['nombres'],
    'apellidos' => $pasajero['apellidos'],
    'telefono' => $pasajero['telefono']
  ]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error del servidor.']);
}
