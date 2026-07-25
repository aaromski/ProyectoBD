<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

try {
    /** @var PDO $conn */
    // Buscamos a los choferes a los que la empresa les debe dinero (saldo > 0)
    $sql = "SELECT 
                c.id_chofer, 
                u.nombres, 
                u.apellidos, 
                b.nombre_banco,
                c.nro_cuenta AS numero_cuenta,
                c.saldo
            FROM choferes c
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            JOIN bancos b ON c.id_banco = b.id_banco
            WHERE c.saldo > 0
            ORDER BY c.saldo DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>