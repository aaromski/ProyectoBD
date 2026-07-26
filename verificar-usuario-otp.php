<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$cedula  = trim($_POST['cedula'] ?? '');
$correo  = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($cedula) || empty($correo) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

$stmt = $conn->prepare("SELECT id_usuario, telefono, password FROM usuarios WHERE cedula = :cedula AND correo = :correo LIMIT 1");
$stmt->execute([':cedula' => $cedula, ':correo' => $correo]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'No se encontró una cuenta con esos datos.']);
    exit;
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'La contraseña es incorrecta.']);
    exit;
}

$telefono = $user['telefono'];
$digits  = preg_replace('/\D/', '', $telefono);
$masked  = maskPhone($digits);

echo json_encode(['success' => true, 'telefono_enmascarado' => $masked]);

function maskPhone($d) {
    $len = strlen($d);
    if ($len < 7) return $d;
    $prefix    = substr($d, 0, 4);
    $visible   = substr($d, 4, 3);
    $hiddenLen = $len - 7;
    return $prefix . '-' . $visible . str_repeat('*', $hiddenLen);
}
?>
