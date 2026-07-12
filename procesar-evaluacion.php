<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

// Se asume que el personal tiene $_SESSION['personal_id'] y $_SESSION['rol'] === 'personal' o 'admin'
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin', 'personal'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Método no permitido']);
  exit();
}

require_once 'conexion.php';

// Recibimos los datos del formulario de evaluación del inspector
$id_evaluacion = isset($_POST['id_evaluacion']) ? $_POST['id_evaluacion'] : ''; // ID del ticket que estaba pendiente
$nota          = isset($_POST['nota']) ? $_POST['nota'] : '';          // Observaciones mecánicas/estéticas
$resultado     = isset($_POST['resultado']) ? $_POST['resultado'] : '';     // 'Apto' o 'No Apto'
$id_personal   = $_SESSION['id_usuario'];       // Quién la firma

if (empty($id_evaluacion) || empty($resultado)) {
  echo json_encode(['success' => false, 'message' => 'Datos incompletos para dictaminar la evaluación']);
  exit();
}

try {
  /** @var PDO $conn */
  $conn->beginTransaction();

  // 1. Buscamos a qué vehículo pertenece esta evaluación para actualizarlo
  $stmtCheck = $conn->prepare("SELECT id_vehiculo FROM evaluacion_carro WHERE id_evaluacion = :id_evaluacion");
  $stmtCheck->execute([':id_evaluacion' => $id_evaluacion]);
  $evaluacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

  if (!$evaluacion) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'La orden de evaluación no existe']);
    exit();
  }

  $id_vehiculo = $evaluacion['id_vehiculo'];

  // 2. Actualizamos la evaluación rellenando los datos que faltaban y cambiando el estado
  $sqlUpEvaluacion = "UPDATE evaluacion_carro
                        SET id_personal = :id_personal, nota = :nota, estado = :estado, fecha_evaluacion = NOW()
                        WHERE id_evaluacion = :id_evaluacion";
  $stmtUpE = $conn->prepare($sqlUpEvaluacion);
  $stmtUpE->execute([
    ':id_personal'   => $id_personal,
    ':nota'          => $nota,
    ':estado'        => $resultado,
    ':id_evaluacion' => $id_evaluacion
  ]);

  // 3. Sincronizamos el estado operativo del vehículo para las búsquedas rápidas en los viajes
  $nuevoEstatusCarro = ($resultado === 'Apto') ? 'Aprobado' : 'Rechazado';

  $sqlUpVehiculo = "UPDATE vehiculos SET estatus = :estatus WHERE id_vehiculo = :id_vehiculo";
  $stmtUpV = $conn->prepare($sqlUpVehiculo);
  $stmtUpV->execute([
    ':estatus'     => $nuevoEstatusCarro,
    ':id_vehiculo' => $id_vehiculo
  ]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Evaluación asentada correctamente']);

} catch (PDOException $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
