<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

$id_traslado = $_GET['id'] ?? 0;

// Usamos SUBSTRING_INDEX para tomar solo la primera palabra
$sql = "SELECT
            SUBSTRING_INDEX(u.nombres, ' ', 1) AS nombre,
            SUBSTRING_INDEX(u.apellidos, ' ', 1) AS apellido,
            u.telefono,
            v.marca,
            v.modelo,
            v.placa
        FROM traslados t
        JOIN choferes ch ON t.id_chofer = ch.id_chofer
        JOIN usuarios u ON ch.id_usuario = u.id_usuario
        JOIN vehiculos v ON t.id_vehiculo = v.id_vehiculo
        WHERE t.id_traslado = ?";

/** @var PDO $conn */
$stmt = $conn->prepare($sql);
$stmt->execute([$id_traslado]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $data]);
?>
