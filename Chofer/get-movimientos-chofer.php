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
  $movimientos = [];

  // 1. Retiros del chofer (desde pago_chofer)
  $stmtRetiros = $conn->prepare("
    SELECT pc.nro_ref AS id_ref, pc.fecha, pc.monto, pc.detalles
    FROM pago_chofer pc
    JOIN choferes ch ON pc.id_chofer = ch.id_chofer
    WHERE ch.id_usuario = ? AND pc.estado = 'finalizado'
    ORDER BY pc.fecha DESC
    LIMIT 10
  ");
  $stmtRetiros->execute([$id_usuario]);
  foreach ($stmtRetiros->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $movimientos[] = [
      'id_ref'   => $row['id_ref'],
      'fecha'    => $row['fecha'],
      'tipo'     => 'RETIRO',
      'monto'    => $row['monto'],
      'detalles' => $row['detalles'] ?: 'Retiro de fondos'
    ];
  }

  // 2. Pagos de viaje del chofer (desde traslados finalizados)
  $stmtViajes = $conn->prepare("
    SELECT
        tr.id_traslado,
        tr.fecha,
        tr.costo,
        zo.nombre_zona AS origen,
        zd.nombre_zona AS destino
    FROM traslados tr
    INNER JOIN choferes c ON tr.id_chofer = c.id_chofer
    LEFT JOIN zonas zo ON tr.id_zona_origen = zo.id_zona
    LEFT JOIN zonas zd ON tr.id_zona_destino = zd.id_zona
    WHERE c.id_usuario = ? AND tr.estado = 'finalizado'
    ORDER BY tr.fecha DESC
    LIMIT 10
  ");
  $stmtViajes->execute([$id_usuario]); // Ahora sí machea perfectamente gracias al JOIN

  foreach ($stmtViajes->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $monto_chofer = $row['costo'] * 0.70;
    $origen = $row['origen'] ?: 'N/A';
    $destino = $row['destino'] ?: 'N/A';

    $movimientos[] = [
      'id_ref'   => '#' . $row['id_traslado'],
      'fecha'    => $row['fecha'],
      'tipo'     => 'PAGO VIAJE',
      'monto'    => $monto_chofer,
      'detalles' => 'Traslado de ' . $origen . ' a ' . $destino
    ];
  }



  // Ordenar por fecha descendente y tomar los últimos 10
  usort($movimientos, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
  });
  $movimientos = array_slice($movimientos, 0, 10);

  echo json_encode(['success' => true, 'data' => $movimientos]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al obtener movimientos.']);
}
?>
