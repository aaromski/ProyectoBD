<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin'])) {
  echo json_encode(['success' => false, 'msg' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$id_cuenta = intval($_POST['id_cuenta'] ?? 0);

if ($id_cuenta <= 0) {
  echo json_encode(['success' => false, 'msg' => 'ID de cuenta inválido.']);
  exit();
}
/** @var PDO $conn */
try {
  $stmt = $conn->prepare("SELECT estado FROM cuentas_empresa WHERE id_cuenta = :id_cuenta");
  $stmt->execute([':id_cuenta' => $id_cuenta]);
  $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$cuenta) {
    echo json_encode(['success' => false, 'msg' => 'Cuenta no encontrada.']);
    exit();
  }

  $nuevoEstado = ($cuenta['estado'] === 'activo') ? 'inactivo' : 'activo';

  $stmt2 = $conn->prepare("UPDATE cuentas_empresa SET estado = :estado WHERE id_cuenta = :id_cuenta");
  $stmt2->execute([':estado' => $nuevoEstado, ':id_cuenta' => $id_cuenta]);

  $texto = ($nuevoEstado === 'activo') ? 'activada' : 'desactivada';
  echo json_encode(['success' => true, 'message' => "Cuenta $texto correctamente."]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
