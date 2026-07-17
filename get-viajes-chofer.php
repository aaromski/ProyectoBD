<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'msg' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

try {
  $id_usuario = $_SESSION['id_usuario'];
  /** @var PDO $conn */

  $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = :id_usuario");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode(['success' => true, 'asignados' => [], 'historial' => []]);
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
                 t.estado AS estado
          FROM traslados t
          INNER JOIN clientes c ON t.id_cliente = c.id_cliente
          INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
          INNER JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
          INNER JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
          WHERE t.id_chofer = :id_chofer
          ORDER BY t.id_traslado DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':id_chofer' => $id_chofer]);
  $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $asignados = [];
  $historial = [];

  foreach ($viajes as $v) {
    if (in_array($v['estado'], ['pendiente', 'en_curso'])) {
      $asignados[] = $v;
    } else {
      $historial[] = $v;
    }
  }

  echo json_encode(['success' => true, 'asignados' => $asignados, 'historial' => $historial]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
