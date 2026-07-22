<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['error' => 'No autorizado']));
}

try {
  /** @var PDO $conn */
  $stmt1 = $conn->query("SELECT COUNT(*) FROM evaluaciones_choferes WHERE estado = 'pendiente'");
  $choferes_pendientes = $stmt1->fetchColumn();

  $stmt2 = $conn->query("SELECT COUNT(*) FROM pago_chofer WHERE estado = 'pendiente'");
  $pagos_pendientes = $stmt2->fetchColumn();

  $stmt3 = $conn->query("SELECT COUNT(*) FROM evaluaciones_vehiculos WHERE estado = 'pendiente'");
  $vehiculos_pendientes = $stmt3->fetchColumn();

  echo json_encode([
    'choferes_pendientes' => $choferes_pendientes,
    'pagos_pendientes' => $pagos_pendientes,
    'vehiculos_pendientes' => $vehiculos_pendientes
  ]);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>
