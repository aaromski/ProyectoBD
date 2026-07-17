<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'msg' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';
/** @var PDO $conn */

try {
  $stmt = $conn->prepare("
    SELECT ce.id_cuenta, ce.id_banco, b.nombre_banco, b.prefijo, ce.numero_cuenta,
           ce.identificacion_titular, ce.nombre_titular, ce.telefono, ce.estado
    FROM cuentas_empresa ce
    INNER JOIN bancos b ON ce.id_banco = b.id_banco
    WHERE ce.id_banco != 1
    ORDER BY b.nombre_banco ASC
  ");
  $stmt->execute();
  $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $cuentas]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
