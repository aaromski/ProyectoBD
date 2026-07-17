<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */

  $clientes = $conn->query("SELECT COUNT(*) FROM roles_asignados WHERE tipo_rol = 'cliente'")->fetchColumn();
  $choferes = $conn->query("SELECT COUNT(*) FROM roles_asignados WHERE tipo_rol = 'chofer'")->fetchColumn();
  $bancos   = $conn->query("SELECT COUNT(*) FROM bancos")->fetchColumn();

  echo json_encode([
    'clientes' => intval($clientes),
    'choferes' => intval($choferes),
    'bancos'   => intval($bancos)
  ]);
} catch (PDOException $e) {
  echo json_encode(['error' => 'Error en el servidor.']);
}
?>
