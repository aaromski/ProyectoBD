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

  $stmt = $conn->query("
    SELECT
      COALESCE(SUM(CASE WHEN tipo = 'pago_viaje' THEN monto * 0.30 ELSE 0 END), 0) AS ganancia_historica,
      COALESCE(SUM(CASE WHEN tipo = 'pago_viaje' AND DATE_FORMAT(fecha, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') THEN monto * 0.30 ELSE 0 END), 0) AS ganancia_mes_actual,
      COALESCE(SUM(CASE WHEN tipo = 'pago_viaje' THEN 1 ELSE 0 END), 0) AS carreras_completadas
    FROM transacciones
    WHERE estado = 'finalizado'
  ");

  $data = $stmt->fetch(PDO::FETCH_ASSOC);

  echo json_encode([
    'ganancia_historica'  => floatval($data['ganancia_historica']),
    'ganancia_mes_actual' => floatval($data['ganancia_mes_actual']),
    'carreras_completadas' => intval($data['carreras_completadas'])
  ]);
} catch (PDOException $e) {
  echo json_encode(['error' => 'Error en el servidor.']);
}
?>
