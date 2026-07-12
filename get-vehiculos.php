<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  // 1. Conseguimos el id_chofer primero
  $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = :id_usuario");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode([]);
    exit();
  }

  $id_chofer = $chofer['id_chofer'];

  // 2. Consulta corregida: Trae 'anio' y une con la tabla 'evaluaciones_vehiculos' para obtener el 'estado'
  $sql = "SELECT
                v.id_vehiculo,  -- <--- ESTO ES LO QUE FALTA
                v.marca,
                v.modelo,
                v.placa,
                v.anio,
                COALESCE(e.estado, 'pendiente') AS estado_evaluacion
            FROM vehiculos v
            LEFT JOIN evaluaciones_vehiculos e ON v.id_vehiculo = e.id_vehiculo
            WHERE v.id_chofer = :id_chofer
            ORDER BY v.id_vehiculo DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':id_chofer' => $id_chofer]);
  $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($vehiculos);

} catch (PDOException $e) {
  echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
