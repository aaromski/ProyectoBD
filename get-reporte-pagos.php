<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
$mes = isset($_GET['mes']) ? trim($_GET['mes']) : '';

if ($id_usuario <= 0 || empty($mes)) {
  echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
  exit();
}

try {
  /** @var PDO $conn */
  $sql = "SELECT t.id_transaccion, t.tipo, t.nro_ref, t.monto, t.fecha, t.estado
          FROM transacciones t
          WHERE t.id_usuario = :id_usuario
          AND t.tipo = 'pago_chofer'
          AND DATE_FORMAT(t.fecha, '%Y-%m') = :mes
          ORDER BY t.fecha DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':id_usuario' => $id_usuario, ':mes' => $mes]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true,
    'data' => $data,
    'id_buscado' => $id_usuario
  ]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>
