<?php
session_start();
header('Content-Type: application/json');


require_once 'conexion.php';
/** @var PDO $conn */

try {
  $stmt = $conn->prepare("SELECT id_banco, nombre_banco, prefijo FROM bancos WHERE id_banco != 1 ORDER BY nombre_banco ASC");
  $stmt->execute();
  $bancos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $bancos]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
