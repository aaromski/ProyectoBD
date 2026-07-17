<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit;
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];

  // Paso 1: Buscar el id_chofer que corresponde a este id_usuario
  $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
  $stmtChofer->execute([$id_usuario]);
  $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

  // Si por alguna razón el usuario tiene rol chofer pero no está en la tabla choferes
  if (!$chofer) {
    echo json_encode(['success' => false, 'message' => 'No se encontró el registro de chofer asociado a este usuario.']);
    exit;
  }

  $id_chofer = $chofer['id_chofer'];

  // Paso 2: Insertar la nueva evaluación usando el id_chofer real
  $stmt = $conn->prepare("INSERT INTO evaluaciones_choferes (id_chofer, fecha, estado) VALUES (?, NOW(), 'pendiente')");
  $stmt->execute([$id_chofer]);

  echo json_encode(['success' => true, 'message' => 'Solicitud de evaluación psicológica registrada. Pendiente de revisión por personal.']);
} catch (PDOException $e) {
  // Puedes usar $e->getMessage() temporalmente si necesitas ver el error exacto en consola
  echo json_encode(['success' => false, 'message' => 'Error al registrar la solicitud.']);
}
?>
