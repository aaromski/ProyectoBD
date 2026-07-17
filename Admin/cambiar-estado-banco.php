<?php
require_once '../conexion.php';
header('Content-Type: application/json');

$id_banco = (int)($_POST['id_banco'] ?? 0);

if (!$id_banco) {
  echo json_encode(['success' => false, 'message' => 'ID de banco inválido.']);
  exit;
}
/** @var PDO $conn */
try {
  $stmt = $conn->prepare("SELECT estado FROM bancos WHERE id_banco = ?");
  $stmt->execute([$id_banco]);
  $banco = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$banco) {
    echo json_encode(['success' => false, 'message' => 'Banco no encontrado.']);
    exit;
  }

  $nuevo_estado = ($banco['estado'] === 'activo') ? 'inactivo' : 'activo';

  $stmt = $conn->prepare("UPDATE bancos SET estado = ? WHERE id_banco = ?");
  $stmt->execute([$nuevo_estado, $id_banco]);

  $mensaje = ($nuevo_estado === 'activo') ? 'Banco activado correctamente.' : 'Banco desactivado correctamente.';
  echo json_encode(['success' => true, 'message' => $mensaje, 'nuevo_estado' => $nuevo_estado]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al cambiar el estado del banco.']);
}
?>
