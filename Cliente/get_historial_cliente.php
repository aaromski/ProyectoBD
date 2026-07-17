<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

$id_cliente = $_SESSION['id_usuario'];

/** @var PDO $conn */
$sql = "SELECT t.id_traslado, t.costo, t.estado, t.fecha,
               z1.nombre_zona AS nombre_origen,
               z2.nombre_zona AS nombre_destino
        FROM traslados t
        JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
        JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
        WHERE t.id_cliente = ?
        ORDER BY t.id_traslado DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$id_cliente]);
$traslados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $traslados]);
?>
