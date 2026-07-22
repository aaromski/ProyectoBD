<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

$sql = "SELECT
            pc.id_pago,
            SUBSTRING_INDEX(u.nombres, ' ', 1) AS nombre,
            SUBSTRING_INDEX(u.apellidos, ' ', 1) AS apellido,
            u.cedula,
            pc.nro_ref,
            pc.monto,
            ch.nro_cuenta,
            b.nombre_banco,
            pc.detalles
        FROM pago_chofer pc
        JOIN choferes ch ON pc.id_chofer = ch.id_chofer
        JOIN usuarios u ON ch.id_usuario = u.id_usuario
        LEFT JOIN bancos b ON ch.id_banco = b.id_banco
        WHERE pc.estado = 'pendiente'";

/** @var PDO $conn */
$stmt = $conn->prepare($sql);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
