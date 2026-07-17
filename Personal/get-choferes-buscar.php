<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
  /** @var PDO $conn */
  $sql = "SELECT c.id_chofer, u.id_usuario, u.nombres, u.apellidos
          FROM choferes c
          INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
          WHERE u.nombres LIKE :q OR u.apellidos LIKE :q OR CAST(u.id_usuario AS CHAR) LIKE :q
          ORDER BY u.nombres ASC
          LIMIT 10";

  $stmt = $conn->prepare($sql);
  $stmt->execute([':q' => "%$q%"]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
  echo json_encode(['error' => 'Error en el servidor']);
}
?>
