<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

$nombres   = trim($_POST['nombres'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$cedula    = trim($_POST['cedula'] ?? '');
$correo    = trim($_POST['correo'] ?? '');
$telefono  = trim($_POST['telefono'] ?? '');
$password  = $_POST['password'] ?? '';
$tipo_rol       = trim($_POST['rol'] ?? 'personal');

if (!in_array($tipo_rol, ['personal', 'admin'])) $rol = 'personal';

if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($correo) || empty($password)) {
  echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben estar completos.']);
  exit();
}

try {
  /** @var PDO $conn */

  $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE cedula = :cedula OR correo = :correo LIMIT 1");
  $stmt->execute([':cedula' => $cedula, ':correo' => $correo]);
  if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'La cédula o el correo ya están registrados.']);
    exit();
  }

  $conn->beginTransaction();

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT INTO usuarios (nombres, apellidos, cedula, correo, telefono, password) VALUES (:nombres, :apellidos, :cedula, :correo, :telefono, :password)");
  $stmt->execute([
    ':nombres'   => $nombres,
    ':apellidos' => $apellidos,
    ':cedula'    => $cedula,
    ':correo'    => $correo,
    ':telefono'  => $telefono,
    ':password'  => $hash
  ]);
  $id_usuario = $conn->lastInsertId();

  $stmt = $conn->prepare("INSERT INTO roles_asignados (id_usuario, tipo_rol) VALUES (:id_usuario, :rol)");
  $stmt->execute([':id_usuario' => $id_usuario, ':rol' => $tipo_rol]);

  $conn->commit();
  echo json_encode(['success' => true, 'message' => 'Usuario ' . $tipo_rol . ' creado exitosamente.']);
} catch (PDOException $e) {
  $conn->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>
