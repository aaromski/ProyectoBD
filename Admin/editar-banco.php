<?php
require_once '../conexion.php';
header('Content-Type: application/json');

$id_banco = (int)($_POST['id_banco'] ?? 0);
$nombre_banco = trim($_POST['nombre_banco'] ?? '');
$numero_cuenta = trim($_POST['numero_cuenta'] ?? '');

if (!$id_banco || !$nombre_banco || !$numero_cuenta) {
  echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
  exit;
}
/** @var PDO $conn */
try {
  $stmt = $conn->prepare("UPDATE bancos SET nombre_banco = ?, numero_cuenta = ? WHERE id_banco = ?");
  $stmt->execute([$nombre_banco, $numero_cuenta, $id_banco]);

  if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Banco no encontrado sin cambios.']);
  } else {
    echo json_encode(['success' => true, 'message' => 'Banco actualizado correctamente.']);
  }
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al actualizar el banco.']);
}
?>
