<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin'])) {
  echo json_encode(['success' => false, 'msg' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

$id_cuenta = intval($_POST['id_cuenta'] ?? 0);
$id_banco = intval($_POST['id_banco'] ?? 0);
$numero_cuenta = trim($_POST['numero_cuenta'] ?? '');
$identificacion_titular = trim($_POST['identificacion_titular'] ?? '');
$nombre_titular = trim($_POST['nombre_titular'] ?? '');
$telefono = trim($_POST['telefono'] ?? null);
$estado = trim($_POST['estado'] ?? 'activo');

if ($id_cuenta <= 0 || $id_banco <= 0 || $numero_cuenta === '' || $identificacion_titular === '' || $nombre_titular === '') {
  echo json_encode(['success' => false, 'msg' => 'Todos los campos obligatorios deben ser completados.']);
  exit();
}

if (!preg_match('/^\d{16}$/', $numero_cuenta)) {
  echo json_encode(['success' => false, 'msg' => 'El número de cuenta debe contener exactamente 16 dígitos.']);
  exit();
}

if (!in_array($estado, ['activo', 'inactivo'])) {
  $estado = 'activo';
}
/** @var PDO $conn */

try {
  $stmt = $conn->prepare("
    UPDATE cuentas_empresa
    SET id_banco = :id_banco,
        numero_cuenta = :numero_cuenta,
        identificacion_titular = :identificacion,
        nombre_titular = :nombre,
        telefono = :telefono,
        estado = :estado
    WHERE id_cuenta = :id_cuenta
  ");
  $stmt->execute([
    ':id_banco' => $id_banco,
    ':numero_cuenta' => $numero_cuenta,
    ':identificacion' => $identificacion_titular,
    ':nombre' => $nombre_titular,
    ':telefono' => $telefono !== '' ? $telefono : null,
    ':estado' => $estado,
    ':id_cuenta' => $id_cuenta
  ]);

  echo json_encode(['success' => true, 'message' => 'Cuenta de empresa actualizada correctamente.']);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
