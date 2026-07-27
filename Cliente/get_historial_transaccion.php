<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_id'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_cliente = $_SESSION['cliente_id'];

  $desde = $_GET['desde'] ?? null;
  $hasta = $_GET['hasta'] ?? null;

  $sql = "SELECT r.id_recarga AS id, r.monto, r.nro_ref, r.fecha_registro AS fecha, b.nombre_banco, b.prefijo, ce.numero_cuenta
          FROM recargas r
          LEFT JOIN cuentas_empresa ce ON r.id_cuenta = ce.id_cuenta
          LEFT JOIN bancos b ON ce.id_banco = b.id_banco
          WHERE r.id_cliente = :id_cliente";

  $params = [':id_cliente' => $id_cliente];

  if ($desde && preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
    $sql .= " AND DATE(r.fecha_registro) >= :desde";
    $params[':desde'] = $desde;
  }
  if ($hasta && preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
    $sql .= " AND DATE(r.fecha_registro) <= :hasta";
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
