<?php
include('conexion.php');
header('Content-Type: application/json');

try {
  /** @var PDO $conn */
  $stmt = $conn->prepare("SELECT id_zona, nombre_zona FROM zonas ORDER BY nombre_zona ASC");
  $stmt->execute();
  $zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(["success" => true, "data" => $zonas]);
} catch(PDOException $e) {
  echo json_encode(["success" => false, "message" => "Error al cargar zonas: " . $e->getMessage()]);
}
?>
