<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

// Verificación básica de seguridad para personal
if (!isset($_SESSION['id_usuario'])) {
  die(json_encode(['error' => 'No autorizado']));
}

try {
  /** @var PDO $conn */
  // 1. Choferes pendientes de evaluación
  $stmt1 = $conn->query("SELECT COUNT(*) FROM evaluaciones_choferes WHERE estado = 'pendiente'");
  $choferes_pendientes = $stmt1->fetchColumn();

  // 2. Pagos/Retiros pendientes de verificación
  $stmt2 = $conn->query("SELECT COUNT(*) FROM transacciones WHERE estado = 'pendiente'");
  $pagos_pendientes = $stmt2->fetchColumn();

  // 3. Vehículos pendientes de inspección (usando tu tabla de evaluaciones)
  $stmt3 = $conn->query("SELECT COUNT(*) FROM evaluaciones_vehiculos WHERE estado = 'pendiente'");
  $vehiculos_pendientes = $stmt3->fetchColumn();

  echo json_encode([
    'choferes_pendientes' => $choferes_pendientes,
    'pagos_pendientes' => $pagos_pendientes, // Nuevo indicador
    'vehiculos_pendientes' => $vehiculos_pendientes
  ]);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>
