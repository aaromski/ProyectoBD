<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['error' => 'No autorizado']);
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
    echo json_encode(['data' => []]);
    exit();
  }

  $id_chofer = $chofer['id_chofer'];

  // SQL ajustado para traer solo los viajes de este chofer
  $sql = "SELECT t.id_traslado AS id,
                 t.costo AS ganancia_bs,
                 t.estado AS estado,
                 CONCAT(u.nombres, ' ', u.apellidos) AS pasajero,
                 z1.nombre_zona AS origen,
                 z2.nombre_zona AS destino
          FROM traslados t
          INNER JOIN clientes c ON t.id_cliente = c.id_cliente
          INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
          -- JOIN para el origen
          INNER JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
          -- JOIN para el destino
          INNER JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
          WHERE t.id_chofer = :id_chofer
          ORDER BY CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 2 END, t.id_traslado DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':id_chofer' => $id_chofer]);
  $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $viajes]);

} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>
