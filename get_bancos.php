<?php
include('conexion.php');
header('Content-Type: application/json');

/** @var PDO $conn */
try {
  // Consultamos todos los bancos, ordenados por nombre
  $stmt = $conn->query("SELECT id_banco, nombre_banco, numero_cuenta, estado FROM bancos WHERE id_banco != 1 ORDER BY nombre_banco ASC");
  $bancos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $bancos]);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Error al obtener bancos']);
}
?>
