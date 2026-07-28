<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$id_evaluacion = intval($data['id'] ?? 0);
$nota = (int)($data['nota'] ?? -1);
$observacion = trim($data['observacion'] ?? '');
$fecha_evaluacion = trim($data['fecha_evaluacion'] ?? '');

if ($id_evaluacion <= 0 || $nota < 0 || $nota > 100) {
  echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
  exit();
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_evaluacion)) {
  echo json_encode(['success' => false, 'message' => 'Debe indicar la fecha de evaluación.']);
  exit();
}

$estado = ($nota >= 65) ? 'apto' : 'no_apto';

try {
  /** @var PDO $conn */
  $stmtCheck = $conn->prepare("SELECT fecha_creacion FROM evaluaciones_vehiculos WHERE id_evaluacion = ?");
  $stmtCheck->execute([$id_evaluacion]);
  $evaluacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

  if (!$evaluacion) {
    echo json_encode(['success' => false, 'message' => 'Evaluación no encontrada.']);
    exit();
  }

  $fecha_min = date('Y-m-d', strtotime($evaluacion['fecha_creacion']));
  $fecha_max = date('Y-m-d');

  if ($fecha_evaluacion < $fecha_min || $fecha_evaluacion > $fecha_max) {
    echo json_encode([
      'success' => false,
      'message' => "La fecha de evaluación debe estar entre $fecha_min y $fecha_max."
    ]);
    exit();
  }

  $sql = "UPDATE evaluaciones_vehiculos
          SET nota_tecnica = :nota,
              observacion = :observacion,
              estado = :estado,
              id_personal = :id_personal,
              fecha_evaluacion = :fecha_evaluacion
          WHERE id_evaluacion = :id";

  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':nota' => $nota,
    ':observacion' => $observacion,
    ':estado' => $estado,
    ':id_personal' => $_SESSION['id_usuario'],
    ':fecha_evaluacion' => $fecha_evaluacion,
    ':id' => $id_evaluacion
  ]);

  echo json_encode(['success' => true, 'estado' => $estado]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
