<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';


// 1. Intentamos capturar el ID que viene de la petición (POST o FormData)
$id_usuario = intval($_POST['id_usuario'] ?? 0);

// 2. Si no llegó ningún ID por POST (como pasa en guardarCambiosCuenta),
// usamos el ID de la sesión del administrador que está navegando.
if ($id_usuario <= 0) {
  $id_usuario = intval($_SESSION['id_usuario'] ?? 0);
}

// El resto de tus variables y validaciones quedan exactamente igual:
$correo     = trim($_POST['correo'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$password   = $_POST['password'] ?? '';

if ($id_usuario <= 0 ) {
  echo json_encode(['success' => false, 'message' => 'usuario Datos inválidos.']);
  exit();
}


try {
  /** @var PDO $conn */

  $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo AND id_usuario != :id LIMIT 1");
  $stmt->execute([':correo' => $correo, ':id' => $id_usuario]);
  if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'El correo ya está registrado por otro usuario.']);
    exit();
  }

  if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET correo = :correo, telefono = :telefono, password = :password WHERE id_usuario = :id");
    $stmt->execute([':correo' => $correo, ':telefono' => $telefono, ':password' => $hash, ':id' => $id_usuario]);
  } else {
    $stmt = $conn->prepare("UPDATE usuarios SET correo = :correo, telefono = :telefono WHERE id_usuario = :id");
    $stmt->execute([':correo' => $correo, ':telefono' => $telefono, ':id' => $id_usuario]);
  }

  echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente.']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}
?>
