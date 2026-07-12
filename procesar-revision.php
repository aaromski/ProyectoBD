<?php
session_start();
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

// Lógica de negocio: 65 o más es Apto
$nota = (int)$data['nota'];
$estado = ($nota >= 65) ? 'apto' : 'no_apto';

try {
  $sql = "UPDATE evaluaciones_vehiculos
            SET nota_tecnica = :nota,
                observacion = :observacion,
                estado = :estado,
                id_personal = :id_personal
            WHERE id_evaluacion = :id";

  /** @var PDO $conn */

  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':nota' => $nota,
    ':observacion' => $data['observacion'],
    ':estado' => $estado,
    ':id_personal' => $_SESSION['id_usuario'],
    ':id' => $data['id']
  ]);

  echo json_encode(['success' => true, 'estado' => $estado]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
