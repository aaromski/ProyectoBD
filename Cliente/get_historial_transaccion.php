<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    /** @var PDO $conn */
    // 1. Buscamos el id_cliente asociado al usuario actual
    $stmt_c = $conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
    $stmt_c->execute([$id_usuario]);
    $cliente = $stmt_c->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    $id_cliente = $cliente['id_cliente'];

    // 2. Consultamos directamente la tabla oficial 'recargas' unida con 'bancos'
    // Usamos fecha_registro para que muestre la fecha y hora exacta
    $stmt = $conn->prepare("
        SELECT 
            r.id_recarga AS id_transaccion,
            r.fecha_registro AS fecha,
            r.monto,
            r.nro_ref,
            b.nombre_banco,
            'aprobado' AS estado
        FROM recargas r
        LEFT JOIN bancos b ON r.id_banco = b.id_banco
        WHERE r.id_cliente = ?
        ORDER BY r.fecha_registro DESC
    ");
    $stmt->execute([$id_cliente]);
    $recargas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $recargas]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar historial: ' . $e->getMessage()]);
}
?>