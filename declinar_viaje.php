<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

$id_traslado = $_POST['id'] ?? 0;
$id_chofer_actual = $_SESSION['id_usuario']; // Asegúrate de obtener el id_chofer correcto aquí

try {
  /** @var PDO $conn */
  // 1. Buscamos un chofer que NO sea el actual y que esté disponible
  // (Puedes añadir aquí condiciones como 'estado = activo' si tienes esa columna)
  $stmt = $conn->prepare("SELECT id_chofer FROM choferes
                            WHERE id_usuario != ?
                            ORDER BY RAND()
                            LIMIT 1");
  $stmt->execute([$id_chofer_actual]);
  $nuevo_chofer = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($nuevo_chofer) {
    // 2. Si encontramos a alguien, le asignamos el viaje directamente
    $sql = "UPDATE traslados SET id_chofer = ? WHERE id_traslado = ?";
    $conn->prepare($sql)->execute([$nuevo_chofer['id_chofer'], $id_traslado]);

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
