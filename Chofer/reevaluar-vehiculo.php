<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);

if (!$id_vehiculo) {
  echo json_encode(['success' => false, 'message' => 'ID de vehículo inválido.']);
  exit;
}

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  $stmtCh = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
  $stmtCh->execute([$id_usuario]);
  $chofer = $stmtCh->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode(['success' => false, 'message' => 'Chofer no encontrado.']);
    exit;
  }

  $stmtV = $conn->prepare("SELECT id_vehiculo FROM vehiculos WHERE id_vehiculo = ? AND id_chofer = ?");
  $stmtV->execute([$id_vehiculo, $chofer['id_chofer']]);
  if (!$stmtV->fetch()) {
    echo json_encode(['success' => false, 'message' => 'El vehículo no pertenece a este chofer.']);
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO evaluaciones_vehiculos (id_vehiculo, fecha, estado) VALUES (?, NOW(), 'pendiente')");
  $stmt->execute([$id_vehiculo]);

  echo json_encode(['success' => true, 'message' => 'Solicitud de reevaluación registrada. Pendiente de evaluación por personal.']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al registrar la reevaluación.']);
}
?>
