<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Método no permitido']);
  exit();
}

require_once 'conexion.php';

$marca = isset($_POST['marca']) ? $_POST['marca'] : '';
$modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
$placa = isset($_POST['placa']) ? $_POST['placa'] : '';
$anio = isset($_POST['anio']) ? $_POST['anio'] : '';

if (empty($marca) || empty($modelo) || empty($placa) || empty($anio)) {
  echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
  exit();
}

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  // Conseguimos el id_chofer
  $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = :id_usuario");
  $stmtChofer->execute([':id_usuario' => $id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  if (!$chofer) {
    echo json_encode(['success' => false, 'message' => 'Chofer no vinculado']);
    exit();
  }

  // Iniciamos transacción para asegurar que ambos inserts se hagan correctamente
  $conn->beginTransaction();

  // 1. Insertamos la nueva unidad con estado inicial 'Revision'
  $sql = "INSERT INTO vehiculos (id_chofer, marca, modelo, placa, anio) VALUES (:id_chofer, :marca, :modelo, :placa, :anio)";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':id_chofer' => $chofer['id_chofer'],
    ':marca'     => $marca,
    ':modelo'    => $modelo,
    ':placa'     => $placa,
    ':anio'      => $anio
  ]);

  // Capturamos el ID del vehículo recién insertado
  $id_vehiculo = $conn->lastInsertId();

  // 2. Creamos la orden técnica inicial en estado 'Pendiente'
  $sqlEvaluacion = "INSERT INTO evaluaciones_vehiculos (id_vehiculo, id_personal, nota_tecnica, estado, fecha)
                    VALUES (:id_vehiculo, NULL, NULL, 'Pendiente', NOW())";
  $stmtE = $conn->prepare($sqlEvaluacion);
  $stmtE->execute([
    ':id_vehiculo' => $id_vehiculo
  ]);

  // Confirmamos los cambios en la base de datos
  $conn->commit();

  echo json_encode(['success' => true]);

} catch (PDOException $e) {
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
