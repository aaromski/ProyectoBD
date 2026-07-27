<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'msg' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = :id_usuario");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode(['success' => true, 'data' => []]);
    exit();
  }

  $id_chofer = $chofer['id_chofer'];

  $sql = "SELECT t.id_traslado AS id,
                 CONCAT('C-', c.id_cliente) AS id_pasajero,
                 CONCAT(
                    CONCAT(UPPER(SUBSTRING(SUBSTRING_INDEX(u.nombres, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(u.nombres, ' ', 1), 2))),
                    ' ',
                    CONCAT(UPPER(SUBSTRING(SUBSTRING_INDEX(u.apellidos, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(u.apellidos, ' ', 1), 2)))
                 ) AS pasajero,
                 z1.nombre_zona AS origen,
                 z2.nombre_zona AS destino,
                 t.costo AS costo_total,
                 ROUND(t.costo * 0.70, 2) AS ganancia,
                 t.estado AS estado,
                 t.fecha AS fecha,
                 t.id_pago_chofer AS id_pago_chofer
          FROM traslados t
          INNER JOIN clientes c ON t.id_cliente = c.id_cliente
          INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
          INNER JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
          INNER JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
          WHERE t.id_chofer = :id_chofer";

  $params = [':id_chofer' => $id_chofer];

  $estado_pago = $_GET['estado_pago'] ?? null;
  $desde = $_GET['desde'] ?? null;
  $hasta = $_GET['hasta'] ?? null;

  if ($desde && preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
    $sql .= " AND DATE(t.fecha) >= :desde";
    $params[':desde'] = $desde;
  }
  if ($hasta && preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
    $sql .= " AND DATE(t.fecha) <= :hasta";
    $params[':hasta'] = $hasta;
  }

  $sql .= " ORDER BY t.id_traslado DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);
  $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($estado_pago && $estado_pago !== 'todos') {
    $viajes = array_filter($viajes, function ($v) use ($estado_pago) {
      if ($estado_pago === 'pagados') return $v['id_pago_chofer'] !== null;
      if ($estado_pago === 'pendientes') return $v['id_pago_chofer'] === null;
      return true;
    });
    $viajes = array_values($viajes);
  }

  echo json_encode(['success' => true, 'data' => $viajes]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
?>
