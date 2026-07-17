<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

$rol = trim($_GET['tipo_rol'] ?? '');
$rolesValidos = ['admin', 'personal', 'chofer', 'cliente'];

if (!in_array($rol, $rolesValidos)) {
  echo json_encode(['success' => false, 'message' => 'Rol inválido.']);
  exit();
}

try {
  /** @var PDO $conn */
  $sql = "SELECT u.id_usuario, u.nombres, u.apellidos, u.cedula, u.correo, u.telefono
          FROM usuarios u
          INNER JOIN roles_asignados ra ON u.id_usuario = ra.id_usuario
          WHERE ra.tipo_rol = :rol
          ORDER BY u.id_usuario ASC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':rol' => $rol]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>
