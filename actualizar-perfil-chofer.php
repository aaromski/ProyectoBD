<?php
header('Content-Type: application/json');
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'msg' => 'No hay sesión activa.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$tlf = trim($_POST['tlf'] ?? '');
$banco = intval($_POST['banco'] ?? 0);
$cuenta = trim($_POST['cuenta'] ?? '');

if ($tlf === '' || $banco <= 0 || $cuenta === '') {
    echo json_encode(['success' => false, 'msg' => 'Todos los campos son obligatorios.']);
    exit;
}

if (!preg_match('/^\d{20}$/', $cuenta)) {
    echo json_encode(['success' => false, 'msg' => 'La cuenta bancaria debe contener exactamente 20 dígitos.']);
    exit;
}
/** @var PDO $conn */

try {
    $stmt = $conn->prepare("UPDATE choferes SET banco = :banco, nro_cuenta = :cuenta WHERE id_usuario = :id");
    $stmt->execute([':banco' => $banco, ':cuenta' => $cuenta, ':id' => $id_usuario]);

    $stmt2 = $conn->prepare("UPDATE usuarios SET telefono = :telefono WHERE id_usuario = :id");
    $stmt2->execute([':telefono' => $tlf, ':id' => $id_usuario]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'msg' => 'Error de base de datos.']);
}
