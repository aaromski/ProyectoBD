<?php
session_start();
require_once '../conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_personal = $_SESSION['id_usuario'];
$data = json_decode(file_get_contents('php://input'), true);

// Ahora recibimos el ID del chofer directamente
$id_chofer = $data['id_chofer'] ?? null;
$monto_pagar = $data['monto'] ?? null;
$nro_ref = $data['nro_ref'] ?? null;
$fecha_pago = $data['fecha'] ?? null;

if (!$id_chofer || !$monto_pagar || !$nro_ref || !$fecha_pago) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

try {
    /** @var PDO $conn */
    $conn->beginTransaction();

    // 1. Obtener los datos del chofer y verificar que la empresa sí le deba ese dinero
    $stmt = $conn->prepare("SELECT saldo, id_banco, nro_cuenta FROM choferes WHERE id_chofer = ?");
    $stmt->execute([$id_chofer]);
    $chofer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chofer) {
        throw new Exception("El chofer no existe.");
    }
    if ($monto_pagar <= 0 || $monto_pagar > $chofer['saldo']) {
        throw new Exception("Monto inválido. El saldo disponible a pagar es Bs. " . $chofer['saldo']);
    }

    // 2. Descontar el dinero pagado de la deuda (saldo) que tiene la empresa con el chofer
    $stmt_saldo = $conn->prepare("UPDATE choferes SET saldo = saldo - ? WHERE id_chofer = ?");
    $stmt_saldo->execute([$monto_pagar, $id_chofer]);

    // 3. Crear el comprobante del pago ya finalizado
    $detalles = "Liquidación procesada. Ref: " . $nro_ref;
    $stmt_insert = $conn->prepare("
        INSERT INTO pago_chofer (id_chofer, id_personal, id_banco, numero_cuenta, monto, nro_ref, fecha, estado, detalles) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'finalizado', ?)
    ");
    $stmt_insert->execute([
        $id_chofer, $id_personal, $chofer['id_banco'], $chofer['nro_cuenta'], 
        $monto_pagar, $nro_ref, $fecha_pago, $detalles
    ]);

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Pago registrado y liquidado exitosamente.']);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>