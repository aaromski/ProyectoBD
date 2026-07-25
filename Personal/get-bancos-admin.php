<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

try {
    /** @var PDO $conn */
    $stmt = $conn->query("SELECT id_banco, nombre_banco, prefijo, estado FROM bancos ORDER BY nombre_banco ASC");
    $bancos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $bancos]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
