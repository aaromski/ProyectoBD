<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  $desde = $_GET['desde'] ?? null;
  $hasta = $_GET['hasta'] ?? null;

  $sql = "SELECT r.id_recarga AS id, r.monto, r.nro_ref, r.fecha_registro AS fecha, b.nombre_banco
          FROM recargas r
          LEFT JOIN bancos b ON r.id_banco = b.id_banco
          INNER JOIN clientes c ON r.id_cliente = c.id_cliente
          WHERE c.id_usuario = :id";

  $params = [':id' => $id_usuario];

  if ($desde && preg_match('/^\d{4}-\d{2}$/', $desde)) {
    $sql .= " AND DATE_FORMAT(r.fecha_registro, '%Y-%m') >= :desde";
    $params[':desde'] = $desde;
  }
  if ($hasta && preg_match('/^\d{4}-\d{2}$/', $hasta)) {
    $sql .= " AND DATE_FORMAT(r.fecha_registro, '%Y-%m') <= :hasta";
    $params[':hasta'] = $hasta;
  }

  $sql .= " ORDER BY r.fecha_registro DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);

  echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
