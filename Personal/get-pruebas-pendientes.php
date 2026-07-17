<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $sql = "SELECT e.id_evaluacion, e.id_chofer, e.fecha,
                 u.nombres, u.apellidos, u.id_usuario
          FROM evaluaciones_choferes e
          INNER JOIN choferes c ON e.id_chofer = c.id_chofer
          INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
          WHERE e.estado = 'pendiente' AND e.nota_psicologica IS NULL
          ORDER BY e.fecha ASC";

  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
  echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
