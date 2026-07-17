<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

$id_traslado = $_POST['id'] ?? 0;
$id_chofer_actual = $_SESSION['id_usuario']; // Asegúrate de obtener el id_chofer correcto aquí

try {
  /** @var PDO $conn */
  // 1. Buscamos un chofer que NO sea el actual, que tenga evaluaciones aprobadas
  $stmt = $conn->prepare("SELECT c.id_usuario
    FROM choferes c
    WHERE c.id_usuario != ?
    AND EXISTS (
        SELECT 1 FROM evaluaciones_choferes ec
        WHERE ec.id_chofer = c.id_usuario AND ec.estado = 'aprobado'
    )
    AND EXISTS (
        SELECT 1 FROM vehiculos v
        INNER JOIN evaluaciones_vehiculos ev ON v.id_vehiculo = ev.id_vehiculo
        WHERE v.id_chofer = c.id_usuario AND ev.estado = 'apto'
    )
    ORDER BY RAND()
    LIMIT 1");
  $stmt->execute([$id_chofer_actual]);
  $nuevo_chofer = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($nuevo_chofer) {
    // 2. Si encontramos a alguien, le asignamos el viaje directamente
    $sql = "UPDATE traslados SET id_chofer = ? WHERE id_traslado = ?";
    $conn->prepare($sql)->execute([$nuevo_chofer['id_usuario'], $id_traslado]);

    echo json_encode(['success' => true, 'message' => 'Viaje reasignado a otro conductor.']);
  } else {
    // 3. Si no hay más choferes, lo dejamos pendiente (NULL)
    $conn->prepare("UPDATE traslados SET id_chofer = NULL, estado = 'pendiente' WHERE id_traslado = ?")
      ->execute([$id_traslado]);

    echo json_encode(['success' => true, 'message' => 'No hay más choferes, viaje liberado a pendientes.']);
  }

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
