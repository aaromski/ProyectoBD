<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard-admin.php');
    exit();
}

if (!isset($_POST['nueva_tasa'])) {
    header('Location: dashboard-admin.php?error=sin_valor');
    exit();
}

$nuevaTasa = str_replace([',', ' '], ['.', ''], trim($_POST['nueva_tasa']));
if (!is_numeric($nuevaTasa) || floatval($nuevaTasa) <= 0) {
    header('Location: dashboard-admin.php?error=valor_invalido');
    exit();
}

$tasaBCV = number_format(floatval($nuevaTasa), 2, '.', '');
$contenido = "<?php\n// config_tasa.php\n$tasaBCV = $tasaBCV; // Este valor será modificado por el admin\n?>\n";

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (file_put_contents('config_tasa.php', $contenido) === false) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al escribir la configuración de la tasa.']);
    } else {
        header('Location: dashboard-admin.php?error=escritura_fallida');
    }
    exit();
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Tasa BCV actualizada correctamente.']);
    exit();
}

header('Location: dashboard-admin.php?success=tasa_actualizada');
exit();
