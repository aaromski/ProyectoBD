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
$mes = isset($_GET['mes']) ? trim($_GET['mes']) : '';

if ($id_usuario <= 0 || empty($mes)) {
  echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
  exit();
}

try {
  /** @var PDO $conn */
  $sql = "SELECT pc.id_pago, pc.nro_ref, pc.monto, pc.fecha, pc.estado, pc.detalles,
                 ch.nro_cuenta,
                 b.nombre_banco
          FROM pago_chofer pc
          JOIN choferes ch ON pc.id_chofer = ch.id_chofer
          LEFT JOIN bancos b ON pc.id_banco = b.id_banco
          WHERE ch.id_usuario = :id_usuario
          AND DATE_FORMAT(pc.fecha, '%Y-%m') = :mes
          ORDER BY pc.fecha DESC";

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
