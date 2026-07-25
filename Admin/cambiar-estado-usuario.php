<?php
session_start();
header('Content-Type: application/json');

// Validar que solo un administrador pueda hacer esto
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../conexion.php';

$id_usuario = intval($_POST['id_usuario'] ?? 0);

// ESCUDO: Prevención de auto-bloqueo
    if ($id_usuario === intval($_SESSION['id_usuario'])) {
        echo json_encode(['success' => false, 'message' => 'Seguridad: No puedes bloquear tu propia cuenta mientras estás en sesión.']);
        exit();
    }

if ($id_usuario <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit();
}

try {
    /** @var PDO $conn */
    // Buscar el estado actual del usuario
    $stmt = $conn->prepare("SELECT estado FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit();
    }

    // Alternar el estado según el estado actual
    $estado_actual = $usuario['estado'];
    if ($estado_actual === 'activo') {
        $nuevo_estado = 'bloqueado';
        $texto = 'bloqueado';
    } else {
        $nuevo_estado = 'activo';
        $texto = 'activado';
    }

    // Actualizar en la base de datos
    $conn->prepare("UPDATE usuarios SET estado = ? WHERE id_usuario = ?")->execute([$nuevo_estado, $id_usuario]);

    echo json_encode(['success' => true, 'message' => "Usuario $texto correctamente.", 'nuevo_estado' => $nuevo_estado]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
