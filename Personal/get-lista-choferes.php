<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

try {
    /** @var PDO $conn */
    // Cruzamos la tabla choferes con usuarios para sacar el id_chofer y sus nombres reales
    $stmt = $conn->query("
        SELECT c.id_chofer, u.id_usuario, u.nombres, u.apellidos 
        FROM choferes c 
        INNER JOIN usuarios u ON c.id_usuario = u.id_usuario 
        ORDER BY u.nombres ASC
    ");
    $choferes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $choferes]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>