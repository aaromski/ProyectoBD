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
    // 1. Buscamos el id_chofer real asociado a este usuario
    $stmt_ch = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
    $stmt_ch->execute([$id_usuario]);
    $chofer = $stmt_ch->fetch(PDO::FETCH_ASSOC);

    if (!$chofer) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }
    
    $id_chofer = $chofer['id_chofer'];
    $movimientos = [];

    // 2. INGRESOS: Consultamos los viajes realizados para calcular su 70% de ganancia
    $stmt_viajes = $conn->prepare("
        SELECT id_traslado, fecha, costo, estado
        FROM traslados 
        WHERE id_chofer = ? AND estado IN ('finalizado', 'realizado')
    ");
    $stmt_viajes->execute([$id_chofer]);
    
    while ($row = $stmt_viajes->fetch(PDO::FETCH_ASSOC)) {
        // La empresa retiene 30%, el chofer gana 70%
        $ganancia = $row['costo'] * 0.70; 
        
        $movimientos[] = [
            'id_ref' => '#V-' . $row['id_traslado'],
            'fecha' => $row['fecha'],
            'tipo' => 'VIAJE REALIZADO',
            'monto' => $ganancia, // Monto positivo (suma a su saldo)
            'detalles' => 'Ganancia por traslado (70%)'
        ];
    }

    // 3. EGRESOS (LIQUIDACIÓN): Consultamos los pagos que le ha hecho el administrador
    $stmt_pagos = $conn->prepare("
        SELECT id_pago, nro_ref, fecha, monto, detalles, estado
        FROM pago_chofer 
        WHERE id_chofer = ?
    ");
    $stmt_pagos->execute([$id_chofer]);
    
    while ($row = $stmt_pagos->fetch(PDO::FETCH_ASSOC)) {
        $referencia = !empty($row['nro_ref']) ? 'REF-' . $row['nro_ref'] : '#P-' . $row['id_pago'];
        
        $movimientos[] = [
            'id_ref' => $referencia,
            'fecha' => $row['fecha'],
            'tipo' => 'PAGO DE EMPRESA',
            'monto' => -abs($row['monto']), // Monto negativo (se descuenta de su saldo virtual porque ya se le envió al banco)
            'detalles' => !empty($row['detalles']) ? $row['detalles'] : 'Liquidación enviada a cuenta bancaria'
        ];
    }

    // 4. Ordenamos todo el historial cronológicamente (los más recientes primero)
    usort($movimientos, function($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });

    echo json_encode(['success' => true, 'data' => $movimientos]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar historial: ' . $e->getMessage()]);
}
?>