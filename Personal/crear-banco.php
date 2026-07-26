<?php
header('Content-Type: application/json');
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$nombre  = trim($_POST['nombre'] ?? '');
$prefijo = strtoupper(trim($_POST['prefijo'] ?? ''));

if (empty($nombre) || empty($prefijo)) {
    echo json_encode(['success' => false, 'message' => 'Nombre y prefijo son obligatorios.']);
    exit;
}

if (strlen($prefijo) !== 4) {
    echo json_encode(['success' => false, 'message' => 'El prefijo debe tener exactamente 4 caracteres.']);
    exit;
}

$stmt = $conn->prepare("SELECT id_banco FROM bancos WHERE nombre_banco = :nombre LIMIT 1");
$stmt->execute([':nombre' => $nombre]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Ya existe un banco con ese nombre.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO bancos (nombre_banco, prefijo, estado) VALUES (:nombre, :prefijo, 'activo')");
$stmt->execute([':nombre' => $nombre, ':prefijo' => $prefijo]);

echo json_encode(['success' => true, 'message' => 'Banco registrado exitosamente.']);
?>
