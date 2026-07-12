<?php
session_start();
require_once 'conexion.php'; // Tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
  $correo = $_POST['correo'];
  $tlf = $_POST['tlf'];
  $ci = $_SESSION['id_usuario'];

  try {
    $sql = "UPDATE clientes SET Correo = :correo, Tlf = :tlf WHERE Cedula = :ci";
    /** @var PDO $conn */
    $stmt = $conn->prepare($sql);
    $stmt->execute([':correo' => $correo, ':tlf' => $tlf, ':ci' => $ci]);

    echo json_encode(['success' => true]);
  } catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar base de datos']);
  }
}
?>
