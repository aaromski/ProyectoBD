<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['success' => false, 'message' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];
  $rol = $_SESSION['rol']; // Asumiendo que guardas el rol en la sesión

  // Construimos la consulta base
  // Usamos LEFT JOIN para obtener el nombre del banco desde la tabla bancos
  $sql = "SELECT t.*, b.nombre_banco
            FROM transacciones t
            LEFT JOIN bancos b ON t.id_banco = b.id_banco";

  if ($rol === 'cliente') {
    $sql .= " WHERE t.id_usuario = :id AND t.tipo = 'recarga'";
  } elseif ($rol === 'chofer') {
    $sql .= " WHERE t.id_usuario = :id AND t.tipo = 'pago_chofer'";
  } else {
    $sql .= " ORDER BY t.fecha DESC"; // Admin ve todo
  }

  $stmt = $conn->prepare($sql);
  $params = ($rol === 'cliente' || $rol === 'chofer') ? [':id' => $id_usuario] : [];
  $stmt->execute($params);

  echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
