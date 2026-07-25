<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
// Recibimos las fechas si el chofer usa el filtro
$desde = isset($_GET['desde']) ? $_GET['desde'] : '';
$hasta = isset($_GET['hasta']) ? $_GET['hasta'] : '';

try {
    /** @var PDO $conn */
    // 1. Validar al chofer
    $stmt_ch = $conn->prepare("SELECT id_chofer FROM choferes WHERE id_usuario = ?");
    $stmt_ch->execute([$id_usuario]);
    $chofer = $stmt_ch->fetch(PDO::FETCH_ASSOC);

    if (!$chofer) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }
    
    $id_chofer = $chofer['id_chofer'];

    // 2. Consulta base (Sin datos del pasajero, solo la información de la ruta y fecha)
    $sql = "
        SELECT 
            t.id_traslado, 
            zo.nombre_zona AS origen, 
            zd.nombre_zona AS destino, 
            t.estado, 
            t.costo AS costo_total,
            t.fecha
        FROM traslados t
        INNER JOIN zonas zo ON t.id_zona_origen = zo.id_zona
        INNER JOIN zonas zd ON t.id_zona_destino = zd.id_zona
        WHERE t.id_chofer = ?
    ";

    $params = [$id_chofer];

    // 3. Aplicamos el filtro de fechas si existen
    if (!empty($desde) && !empty($hasta)) {
        $sql .= " AND DATE(t.fecha) BETWEEN ? AND ?";
        $params[] = $desde;
        $params[] = $hasta;
    }

    $sql .= " ORDER BY t.fecha DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $viajes]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar viajes: ' . $e->getMessage()]);
}
?>