<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$input = json_decode(file_get_contents('php://input'), true);

$id_evaluacion = intval($input['id'] ?? 0);
$nota = intval($input['nota'] ?? -1);
$observacion = trim($input['observacion'] ?? '');
$fecha_evaluacion = trim($input['fecha_evaluacion'] ?? '');

if ($id_evaluacion <= 0 || $nota < 0 || $nota > 100) {
  echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
  exit();
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_evaluacion)) {
  echo json_encode(['success' => false, 'message' => 'Debe indicar la fecha de evaluación.']);
  exit();
}

$estado = ($nota >= 73) ? 'aprobado' : 'reprobado';

try {
  /** @var PDO $conn */
  $stmtCheck = $conn->prepare("SELECT fecha_creacion FROM evaluaciones_choferes WHERE id_evaluacion = ?");
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

  $sql = "UPDATE evaluaciones_choferes
          SET nota_psicologica = :nota,
              estado = :estado,
              id_personal = :id_personal,
              observacion = :observacion,
              fecha_evaluacion = :fecha_evaluacion
          WHERE id_evaluacion = :id";

  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':nota' => $nota,
    ':estado' => $estado,
    ':id_personal' => $_SESSION['id_usuario'],
    ':observacion' => $observacion,
    ':fecha_evaluacion' => $fecha_evaluacion,
    ':id' => $id_evaluacion
  ]);

  echo json_encode(['success' => true, 'message' => 'Evaluación guardada correctamente.', 'estado' => $estado]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
}
?>
