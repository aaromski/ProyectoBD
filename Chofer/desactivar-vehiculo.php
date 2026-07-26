<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'chofer') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$id_vehiculo = intval($_POST['id_vehiculo'] ?? 0);

if ($id_vehiculo <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de vehículo inválido.']);
    exit();
}

try {
    /** @var PDO $conn */

    $stmtChofer = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
    $stmtChofer->execute([$id_usuario]);
    $chofer = $stmtChofer->fetch(PDO::FETCH_ASSOC);

    if (!$chofer) {
        echo json_encode(['success' => false, 'message' => 'Chofer no encontrado.']);
        exit();
    }

    $id_chofer = $chofer['id_chofer'];

    $stmtVehiculo = $conn->prepare("SELECT id_vehiculo, activo FROM vehiculos WHERE id_vehiculo = ? AND id_chofer = ?");
    $stmtVehiculo->execute([$id_vehiculo, $id_chofer]);
    $vehiculo = $stmtVehiculo->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo) {
        echo json_encode(['success' => false, 'message' => 'Vehículo no encontrado o no pertenece a tu cuenta.']);
        exit();
    }

    if (!$vehiculo['activo']) {
        echo json_encode(['success' => false, 'message' => 'Este vehículo ya está desactivado.']);
        exit();
    }

    $conn->prepare("UPDATE vehiculos SET activo = 0 WHERE id_vehiculo = ? AND id_chofer = ?")->execute([$id_vehiculo, $id_chofer]);

    echo json_encode(['success' => true, 'message' => 'Vehículo desactivado correctamente.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
