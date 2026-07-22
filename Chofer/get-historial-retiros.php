<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  $stmt = $conn->prepare("
    SELECT pc.id_pago, pc.monto, pc.nro_ref, pc.fecha, pc.estado, pc.detalles,
           b.nombre_banco
    FROM pago_chofer pc
    JOIN choferes ch ON pc.id_chofer = ch.id_chofer
    LEFT JOIN bancos b ON pc.id_banco = b.id_banco
    WHERE ch.id_usuario = ?
    ORDER BY pc.fecha DESC
  ");
  $stmt->execute([$id_usuario]);

  echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error al obtener historial de retiros.']);
}
?>
