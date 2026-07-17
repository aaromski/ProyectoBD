<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$id_usuario = intval($_POST['id_usuario'] ?? 0);

if ($id_usuario <= 0) {
  echo json_encode(['success' => false, 'message' => 'ID inválido.']);
  exit();
}

try {
  /** @var PDO $conn */

  $stmt = $conn->prepare("SELECT tipo_rol FROM roles_asignados WHERE id_usuario = ? LIMIT 1");
  $stmt->execute([$id_usuario]);
  $rol = $stmt->fetchColumn();

  if (!$rol) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    exit();
  }

  $tablasRol = ['chofer' => 'choferes', 'cliente' => 'clientes'];

  $conn->beginTransaction();

  if (isset($tablasRol[$rol])) {
    $conn->prepare("DELETE FROM {$tablasRol[$rol]} WHERE id_usuario = ?")->execute([$id_usuario]);
  }

  $conn->prepare("DELETE FROM roles_asignados WHERE id_usuario = ?")->execute([$id_usuario]);
  $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?")->execute([$id_usuario]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>
