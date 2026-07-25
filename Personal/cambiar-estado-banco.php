<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

$id_banco = (int)($_POST['id_banco'] ?? 0);

if (!$id_banco) {
    echo json_encode(['success' => false, 'message' => 'ID de banco no proporcionado.']);
    exit;
}

try {
    /** @var PDO $conn */
    $stmtActual = $conn->prepare("SELECT estado FROM bancos WHERE id_banco = ?");
    $stmtActual->execute([$id_banco]);
    $banco = $stmtActual->fetch(PDO::FETCH_ASSOC);

    if (!$banco) {
        echo json_encode(['success' => false, 'message' => 'Banco no encontrado.']);
        exit;
    }

    $nuevoEstado = ($banco['estado'] === 'activo') ? 'inactivo' : 'activo';

    $stmt = $conn->prepare("UPDATE bancos SET estado = ? WHERE id_banco = ?");
    $stmt->execute([$nuevoEstado, $id_banco]);

    echo json_encode(['success' => true, 'message' => "Banco actualizado a '$nuevoEstado'.", 'nuevo_estado' => $nuevoEstado]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cambiar estado del banco.']);
}
?>
