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

  $sql = "
    SELECT * FROM (
      SELECT
        CASE
          WHEN pc.nro_ref IS NOT NULL AND pc.nro_ref != '' THEN CONCAT('Ref. ', pc.nro_ref)
          ELSE CONCAT('PAG-', pc.id_pago)
        END AS id_ref,
        pc.fecha,
        'PAGO CHOFER' AS tipo,
        CAST(pc.monto AS DECIMAL(10,2)) AS monto,
        COALESCE(pc.detalles, 'Retiro de fondos') AS detalles
      FROM pago_chofer pc
      INNER JOIN choferes ch ON pc.id_chofer = ch.id_chofer
      WHERE ch.id_usuario = :id_usuario_1

      UNION ALL

      SELECT
        CONCAT('TR-', tr.id_traslado) AS id_ref,
        tr.fecha,
        'PAGO VIAJE' AS tipo,
        CAST((tr.costo * 0.70) AS DECIMAL(10,2)) AS monto,
        CONCAT('Traslado de ', COALESCE(zo.nombre_zona, 'N/A'), ' a ', COALESCE(zd.nombre_zona, 'N/A')) AS detalles
      FROM traslados tr
      INNER JOIN choferes c ON tr.id_chofer = c.id_chofer
      LEFT JOIN zonas zo ON tr.id_zona_origen = zo.id_zona
      LEFT JOIN zonas zd ON tr.id_zona_destino = zd.id_zona
      WHERE c.id_usuario = :id_usuario_2 AND LOWER(tr.estado) = 'finalizado'
    ) AS movimientos
    ORDER BY movimientos.fecha DESC
    LIMIT 10
  ";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':id_usuario_1' => $id_usuario, ':id_usuario_2' => $id_usuario]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $movimientos = [];
  foreach ($rows as $row) {
    $movimientos[] = [
      'id_ref'   => $row['id_ref'],
      'fecha'    => $row['fecha'],
      'tipo'     => $row['tipo'],
      'monto'    => floatval($row['monto']),
      'detalles' => $row['detalles']
    ];
  }

  echo json_encode(['success' => true, 'data' => $movimientos]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al obtener movimientos.']);
}
?>