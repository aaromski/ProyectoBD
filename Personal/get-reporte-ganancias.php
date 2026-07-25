<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

if (!$desde || !$hasta) {
    echo json_encode(['success' => false, 'message' => 'Faltan fechas de búsqueda.']);
    exit;
}

$fecha_desde = $desde . ' 00:00:00';
$fecha_hasta = $hasta . ' 23:59:59';

try {
    /** @var PDO $conn */
    $stmt = $conn->prepare("
        SELECT 
            id_traslado AS id_transaccion, 
            'N/A' AS nro_ref, 
            fecha, 
            (costo * 0.30) AS comision 
        FROM traslados 
        WHERE fecha BETWEEN ? AND ? 
        ORDER BY fecha DESC
    ");
    
    $stmt->execute([$fecha_desde, $fecha_hasta]);
    $ganancias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $ganancias]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>