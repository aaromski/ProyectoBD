<?php
// get-pagos-pendientes.php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

$sql = "SELECT
            t.id_transaccion,
            CONCAT(
                SUBSTRING_INDEX(u.nombres, ' ', 1),
                ' ',
                SUBSTRING_INDEX(u.apellidos, ' ', 1)
            ) AS nombres,
            t.nro_ref,
            t.monto
        FROM transacciones t
        JOIN usuarios u ON t.id_usuario = u.id_usuario
        WHERE t.estado = 'pendiente'
        AND t.tipo = 'pago_viaje'";

/** @var PDO $conn */
$stmt = $conn->prepare($sql);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
