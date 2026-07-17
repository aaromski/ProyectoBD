<?php
require_once 'conexion.php';
header('Content-Type: application/json');

$nombre_banco = trim($_POST['nombre_banco'] ?? '');
$numero_cuenta = trim($_POST['numero_cuenta'] ?? '');

if (!$nombre_banco || !$numero_cuenta) {
  echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
  exit;
}
/** @var PDO $conn */
try {
  $stmt = $conn->prepare("INSERT INTO bancos (nombre_banco, numero_cuenta, estado) VALUES (?, ?, 'inactivo')");
  $stmt->execute([$nombre_banco, $numero_cuenta]);
  echo json_encode(['success' => true, 'message' => 'Banco registrado correctamente.']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al registrar el banco.']);
}
?>
