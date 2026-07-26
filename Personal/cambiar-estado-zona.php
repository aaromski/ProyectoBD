<?php
header('Content-Type: application/json');
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$idZona     = intval($_POST['id_zona'] ?? 0);
$nuevoEstado = intval($_POST['nuevo_estado'] ?? -1);

if ($idZona <= 0 || !in_array($nuevoEstado, [0, 1])) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos.']);
    exit;
}

$stmt = $conn->prepare("UPDATE zonas SET activo = :estado WHERE id_zona = :id");
$stmt->execute([':estado' => $nuevoEstado, ':id' => $idZona]);

echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
?>
