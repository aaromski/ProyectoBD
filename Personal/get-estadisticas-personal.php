<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

try {
    /** @var PDO $conn */
    // 1. Choferes pendientes (Pruebas psicológicas pendientes)
    $stmt1 = $conn->query("SELECT COUNT(*) FROM evaluaciones_choferes WHERE estado = 'pendiente'");
    $choferes = $stmt1->fetchColumn();

    // 2. Pagos pendientes (Consultando la tabla correcta)
    $stmt2 = $conn->query("SELECT COUNT(*) FROM pago_chofer WHERE estado = 'pendiente'");
    $pagos = $stmt2->fetchColumn();

    // 3. Vehículos pendientes (Revisiones técnicas)
    $stmt3 = $conn->query("SELECT COUNT(*) FROM evaluaciones_vehiculos WHERE estado = 'pendiente'");
    $vehiculos = $stmt3->fetchColumn();

    echo json_encode([
        'error' => false,
        'choferes_pendientes' => $choferes,
        'pagos_pendientes' => $pagos,
        'vehiculos_pendientes' => $vehiculos
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
?>