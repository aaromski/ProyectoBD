<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

$id_cliente = $_SESSION['id_usuario'];

$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

/** @var PDO $conn */
$sql = "SELECT t.id_traslado, t.costo, t.estado, t.fecha,
               z1.nombre_zona AS nombre_origen,
               z2.nombre_zona AS nombre_destino
        FROM traslados t
        JOIN zonas z1 ON t.id_zona_origen = z1.id_zona
        JOIN zonas z2 ON t.id_zona_destino = z2.id_zona
        WHERE t.id_cliente = ?";

$params = [$id_cliente];

if ($desde && preg_match('/^\d{4}-\d{2}$/', $desde)) {
  $sql .= " AND DATE_FORMAT(t.fecha, '%Y-%m') >= ?";
  $params[] = $desde;
}
if ($hasta && preg_match('/^\d{4}-\d{2}$/', $hasta)) {
  $sql .= " AND DATE_FORMAT(t.fecha, '%Y-%m') <= ?";
  $params[] = $hasta;
}

$sql .= " ORDER BY t.id_traslado DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$traslados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $traslados]);
?>
