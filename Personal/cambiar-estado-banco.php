<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

$id_banco = $_POST['id_banco'] ?? null;
$nuevo_estado = $_POST['nuevo_estado'] ?? null;

if (!$id_banco || !$nuevo_estado) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

try {
    /** @var PDO $conn */
    $stmt = $conn->prepare("UPDATE bancos SET estado = ? WHERE id_banco = ?");
    $stmt->execute([$nuevo_estado, $id_banco]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cambiar estado: ' . $e->getMessage()]);
}
?>