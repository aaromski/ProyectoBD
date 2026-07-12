<?php
session_start();
require_once 'conexion.php';
$id_cliente = $_SESSION['id_usuario'];

$sql = "SELECT t.id_traslado, t.precio, t.estado, z1.nombre_zona AS origen, z2.nombre_zona AS destino
        FROM traslados t
        JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
        JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
        WHERE t.id_cliente = ? ORDER BY t.id_traslado DESC";

/** @var PDO $conn */
$stmt = $conn->prepare($sql);
$stmt->execute([$id_cliente]);
echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
?>
