<?php
header('Content-Type: application/json');
require_once '../conexion.php';

$stmt = $conn->query("SELECT id_zona, nombre_zona, coord_x, coord_y, activo FROM zonas ORDER BY id_zona ASC");
$zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $zonas]);
?>
