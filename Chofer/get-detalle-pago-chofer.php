<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

require_once '../conexion.php';

$id_pago = intval($_GET['id_pago'] ?? 0);
if ($id_pago <= 0) {
  echo json_encode(['success' => false, 'message' => 'ID de pago inválido.']);
  exit;
}

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  $stmt = $conn->prepare("
    SELECT pc.id_pago, pc.monto, pc.nro_ref, pc.fecha, pc.detalles, pc.numero_cuenta,
           b.nombre_banco, b.prefijo
    FROM pago_chofer pc
    JOIN choferes ch ON pc.id_chofer = ch.id_chofer
    LEFT JOIN bancos b ON pc.id_banco = b.id_banco
    WHERE pc.id_pago = ? AND ch.id_usuario = ?
  ");
  $stmt->execute([$id_pago, $id_usuario]);
  $pago = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$pago) {
    echo json_encode(['success' => false, 'message' => 'Pago no encontrado.']);
    exit;
  }

  $stmt = $conn->prepare("
    SELECT t.id_traslado,
           DATE(t.fecha) AS fecha,
           zo.nombre_zona AS origen,
           zd.nombre_zona AS destino,
           t.costo AS precio_total,
           ROUND(t.costo * 0.70, 2) AS monto_chofer
    FROM traslados t
    INNER JOIN zonas zo ON t.id_zona_origen = zo.id_zona
    INNER JOIN zonas zd ON t.id_zona_destino = zd.id_zona
    WHERE t.id_pago_chofer = ?
    ORDER BY t.fecha ASC
  ");
  $stmt->execute([$id_pago]);
  $traslados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true,
    'pago' => $pago,
    'traslados' => $traslados
  ]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error al obtener detalle del pago.']);
}
?>
