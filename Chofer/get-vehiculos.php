<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([]);
    exit;
}

try {
    /** @var PDO $conn */
    $id_usuario = $_SESSION['id_usuario'];

    // 1. Buscamos el id_chofer del usuario logueado
    $stmt_chofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
    $stmt_chofer->execute([$id_usuario]);
    $chofer = $stmt_chofer->fetch(PDO::FETCH_ASSOC);

    if (!$chofer) {
        echo json_encode([]);
        exit;
    }

    // 2. Consulta exacta apuntando a 'nota_tecnica' y 'observacion' tal como está en tu BD
    $sql = "SELECT v.id_vehiculo, v.marca, v.modelo, v.anio, v.placa, 
                   e.estado AS estado_evaluacion, 
                   e.fecha AS fecha_evaluacion,
                   e.nota_tecnica AS nota_tecnica, 
                   e.observacion AS observaciones
            FROM vehiculos v
            LEFT JOIN (
                SELECT id_vehiculo, MAX(id_evaluacion) as max_id
                FROM evaluaciones_vehiculos
                GROUP BY id_vehiculo
            ) ult_eval ON v.id_vehiculo = ult_eval.id_vehiculo
            LEFT JOIN evaluaciones_vehiculos e ON ult_eval.max_id = e.id_evaluacion
            WHERE v.id_chofer = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([$chofer['id_chofer']]);
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode([]);
}
?>