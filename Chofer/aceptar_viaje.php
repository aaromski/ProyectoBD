<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  die(json_encode(['success' => false, 'message' => 'No autorizado']));
}

$id_traslado = $_POST['id'];
$id_vehiculo = $_POST['id_vehiculo'];

try {
  /** @var PDO $conn */
  // 1. Obtener ID del chofer
  $stmt = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
  $stmt->execute([$_SESSION['id_usuario']]);
  $chofer = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    die(json_encode(['success' => false, 'message' => 'No se encontró el registro de chofer para este usuario']));
  }
  // 2. Ejecutar actualización con condición de seguridad (estado debe ser pendiente)
  $sql = "UPDATE traslados
        SET estado = 'en_curso',
            id_chofer = ?,
            id_vehiculo = ?,
            fecha = NOW()
        WHERE id_traslado = ? AND estado = 'pendiente'";

  $stmt = $conn->prepare($sql);
  $stmt->execute([$chofer['id_chofer'], $id_vehiculo, $id_traslado]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar: El viaje ya no está pendiente o no existe.']);  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);}
?>
