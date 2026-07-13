<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

$input = json_decode(file_get_contents('php://input'), true);

$id_evaluacion = intval($input['id'] ?? 0);
$nota = intval($input['nota'] ?? -1);
$observacion = trim($input['observacion'] ?? '');

if ($id_evaluacion <= 0 || $nota < 0 || $nota > 100) {
  echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
  exit();
}

$estado = ($nota >= 70) ? 'aprobado' : 'reprobado';

try {
  /** @var PDO $conn */
  $sql = "UPDATE evaluaciones_choferes
          SET nota_psicologica = :nota, estado = :estado, id_personal = :id_personal
          WHERE id_evaluacion = :id";

  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':nota' => $nota,
    ':estado' => $estado,
    ':id_personal' => $_SESSION['id_usuario'],
    ':id' => $id_evaluacion
  ]);

  echo json_encode(['success' => true, 'message' => 'Evaluación guardada correctamente.', 'estado' => $estado]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
}
?>
