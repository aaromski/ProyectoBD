<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

$id_chofer = intval($_GET['id_chofer'] ?? 0);
if ($id_chofer <= 0) {
  echo json_encode(['success' => false, 'message' => 'ID de chofer inválido.']);
  exit;
}

try {
  /** @var PDO $conn */

  $stmt = $conn->prepare("
    SELECT
      c.id_chofer,
      u.nombres,
      u.apellidos,
      u.cedula,
      b.nombre_banco,
      c.nro_cuenta
    FROM choferes c
    JOIN usuarios u ON c.id_usuario = u.id_usuario
    JOIN bancos b ON c.id_banco = b.id_banco
    WHERE c.id_chofer = ?
  ");
  $stmt->execute([$id_chofer]);
  $chofer = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode(['success' => false, 'message' => 'Chofer no encontrado.']);
    exit;
  }

  $stmt = $conn->prepare("
    SELECT
      t.id_traslado,
      DATE(t.fecha) AS fecha,
      zo.nombre_zona AS origen,
      zd.nombre_zona AS destino,
      t.costo,
      ROUND(t.costo * 0.70, 2) AS monto_chofer
    FROM traslados t
    INNER JOIN zonas zo ON t.id_zona_origen = zo.id_zona
    INNER JOIN zonas zd ON t.id_zona_destino = zd.id_zona
    WHERE t.id_chofer = ?
      AND t.estado = 'finalizado'
      AND t.id_pago_chofer IS NULL
    ORDER BY t.fecha ASC
  ");
  $stmt->execute([$id_chofer]);
  $traslados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $total = 0;
  foreach ($traslados as &$t) {
    $total += floatval($t['monto_chofer']);
  }
  unset($t);

  echo json_encode([
    'success' => true,
    'chofer' => $chofer,
    'traslados' => $traslados,
    'total' => round($total, 2)
  ]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
