<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$desde = isset($_GET['desde']) ? trim($_GET['desde']) : '';
$hasta = isset($_GET['hasta']) ? trim($_GET['hasta']) : '';

if (empty($desde) || empty($hasta)) {
  echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
  exit();
}

try {
  /** @var PDO $conn */
  $sql = "SELECT
            t.id_traslado,
            CONCAT(z1.nombre_zona, ' → ', z2.nombre_zona) AS ruta,
            t.costo,
            t.fecha,
            ROUND(t.costo * 0.30, 2) AS comision
          FROM traslados t
          JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
          JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
          WHERE t.estado = 'finalizado'
            AND DATE_FORMAT(t.fecha, '%Y-%m') BETWEEN :desde AND :hasta
          ORDER BY t.fecha DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>
