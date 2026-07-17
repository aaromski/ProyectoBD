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

  $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = :id_usuario");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode([]);
    exit();
  }

  $id_chofer = $chofer['id_chofer'];


  $sql = "SELECT
                v.id_vehiculo,
                v.marca,
                v.modelo,
                v.placa,
                v.anio,
                e.id_evaluacion,
                COALESCE(e.estado, 'pendiente') AS estado_evaluacion,
                e.fecha AS fecha_evaluacion
            FROM vehiculos v
            LEFT JOIN (
                SELECT ev1.*
                FROM evaluaciones_vehiculos ev1
                INNER JOIN (
                    SELECT id_vehiculo, MAX(fecha) AS max_fecha
                    FROM evaluaciones_vehiculos
                    GROUP BY id_vehiculo
                ) ev2 ON ev1.id_vehiculo = ev2.id_vehiculo AND ev1.fecha = ev2.max_fecha
            ) e ON v.id_vehiculo = e.id_vehiculo
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
