<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  $stmt = $conn->prepare("SELECT e.nota_psicologica, e.fecha, e.estado, e.observacion
    FROM evaluaciones_choferes e
    INNER JOIN choferes c ON e.id_chofer = c.id_chofer
    WHERE c.id_usuario = ?
    ORDER BY e.fecha DESC, e.id_evaluacion DESC
    LIMIT 1");

  $stmt->execute([$id_usuario]);
  $eval = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($eval) {
    echo json_encode(['success' => true, 'data' => $eval]);
  } else {
    echo json_encode(['success' => true, 'data' => null]);
  }
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al obtener evaluación.']);
}
?>
