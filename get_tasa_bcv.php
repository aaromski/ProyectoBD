<?php
// get_tasa_bcv.php
include('config_tasa.php'); // Esto trae la variable $tasaBCV

header('Content-Type: application/json');
if (!isset($tasaBCV)) { $tasaBCV = 0.00; }
echo json_encode(['tasa' => (float)$tasaBCV]);
?>
