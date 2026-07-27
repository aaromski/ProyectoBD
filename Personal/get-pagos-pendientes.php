<?php
session_start();
header('Content-Type: application/json');
require_once '../conexion.php';

/** @var PDO $conn */
$sql = "SELECT
            c.id_chofer,
            u.nombres,
            u.apellidos,
            u.cedula,
            b.nombre_banco,
            c.nro_cuenta AS numero_cuenta,
            ROUND(SUM(t.costo * 0.70), 2) AS saldo
        FROM choferes c
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        JOIN bancos b ON c.id_banco = b.id_banco
        JOIN traslados t ON t.id_chofer = c.id_chofer
        WHERE t.estado = 'finalizado'
          AND t.id_pago_chofer IS NULL
        GROUP BY c.id_chofer, u.nombres, u.apellidos, u.cedula, b.nombre_banco, c.nro_cuenta
        HAVING saldo > 0
        ORDER BY saldo DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
