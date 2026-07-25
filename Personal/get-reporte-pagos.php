<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

$id_chofer = $_GET['id_usuario'] ?? null; 
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

if (!$id_chofer || !$desde || !$hasta) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros de búsqueda.']);
    exit;
}

$fecha_desde = $desde . ' 00:00:00';
$fecha_hasta = $hasta . ' 23:59:59';

try {
    /** @var PDO $conn */
    $stmt = $conn->prepare("
        SELECT nro_ref, monto, fecha, estado 
        FROM pago_chofer 
        WHERE id_chofer = ? 
        AND fecha BETWEEN ? AND ? 
        ORDER BY fecha DESC
    ");
    
    $stmt->execute([$id_chofer, $fecha_desde, $fecha_hasta]);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $pagos]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>