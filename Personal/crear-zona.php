<?php
header('Content-Type: application/json');
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$coordX = $_POST['coord_x'] ?? '';
$coordY = $_POST['coord_y'] ?? '';

if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'El nombre de la zona es obligatorio.']);
    exit;
}

if ($coordX === '' || $coordY === '' || !is_numeric($coordX) || !is_numeric($coordY)) {
    echo json_encode(['success' => false, 'message' => 'Las coordenadas X e Y deben ser valores numéricos válidos.']);
    exit;
}

$stmt = $conn->prepare("SELECT id_zona FROM zonas WHERE nombre_zona = :nombre LIMIT 1");
$stmt->execute([':nombre' => $nombre]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Ya existe una zona con ese nombre.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO zonas (nombre_zona, coord_x, coord_y, activo) VALUES (:nombre, :coord_x, :coord_y, 0)");
$stmt->execute([':nombre' => $nombre, ':coord_x' => $coordX, ':coord_y' => $coordY]);

echo json_encode(['success' => true, 'message' => 'Zona registrada exitosamente.']);
?>
