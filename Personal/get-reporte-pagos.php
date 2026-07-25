<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
$desde = isset($_GET['desde']) ? trim($_GET['desde']) : '';
$hasta = isset($_GET['hasta']) ? trim($_GET['hasta']) : '';

if (empty($desde) || empty($hasta)) {
  echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
  exit();
}

try {
  /** @var PDO $conn */
  $sql = "SELECT pc.id_pago, pc.nro_ref, pc.monto, pc.fecha, pc.detalles,
                 ch.nro_cuenta,
                 u.nombres AS chofer_nombre,
                 u.apellidos AS chofer_apellidos,
                 b.nombre_banco
          FROM pago_chofer pc
          JOIN choferes ch ON pc.id_chofer = ch.id_chofer
          JOIN usuarios u ON ch.id_usuario = u.id_usuario
          LEFT JOIN bancos b ON pc.id_banco = b.id_banco
          WHERE DATE_FORMAT(pc.fecha, '%Y-%m') BETWEEN :desde AND :hasta";

  $params = [':desde' => $desde, ':hasta' => $hasta];

  if ($id_usuario > 0) {
    $sql .= " AND ch.id_usuario = :id_usuario";
    $params[':id_usuario'] = $id_usuario;
  }

  $sql .= " ORDER BY pc.fecha DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true,
    'data' => $data,
    'id_buscado' => $id_usuario > 0 ? $id_usuario : null
  ]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>