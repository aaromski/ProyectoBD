<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['error' => 'No autorizado']));
}

try {
  $sql = "SELECT ev.id_evaluacion, ev.estado, ev.fecha AS fecha_solicitud, ev.nota_tecnica,
                   v.marca, v.modelo, v.placa,
                   u.nombres, u.apellidos
            FROM evaluaciones_vehiculos ev
            INNER JOIN vehiculos v ON ev.id_vehiculo = v.id_vehiculo
            INNER JOIN choferes c ON v.id_chofer = c.id_chofer
            INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE ev.estado = 'pendiente'
            ORDER BY ev.fecha ASC";

  /** @var PDO $conn */
  $stmt = $conn->query($sql);
  $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $vehiculos]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
