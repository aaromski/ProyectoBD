<?php
include('../conexion.php');
include('../Admin/config_tasa.php');

// Si el editor sigue marcando error, puedes añadir esto para ayudarle:
if (!isset($tasaBCV)) { $tasaBCV = 0.00; }

/** @var PDO $conn */
$origen = $_POST['origen'];
$destino = $_POST['destino'];

$stmt = $conn->prepare("SELECT coord_x, coord_y FROM zonas WHERE id_zona IN (?, ?)");
$stmt->execute([$origen, $destino]);
$coords = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($coords) == 2) {
  $lat1 = deg2rad($coords[0]['coord_x']);
  $lon1 = deg2rad($coords[0]['coord_y']);
  $lat2 = deg2rad($coords[1]['coord_x']);
  $lon2 = deg2rad($coords[1]['coord_y']);

  $deltaLat = $lat2 - $lat1;
  $deltaLon = $lon2 - $lon1;



  $a = sin($deltaLat / 2)**2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2)**2;
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  $radioTierra = 6371;
  $distanciaKm = $radioTierra * $c;

  $tarifa_arranque = 2.00; // Ajusta esto para que el precio inicial suba

// 2. Tarifa por KM (La que tú definiste como 0.70)
  $tarifa_por_km = 0.70;

// 3. Cálculo del costo: (Arrancada + (Distancia * CostoPorKm)) * TasaBCV
  $precio_en_usd = $tarifa_arranque + ($distanciaKm * $tarifa_por_km);
  $precio_final = $precio_en_usd * $tasaBCV;

// 4. Tarifa mínima absoluta (Si el resultado es menor a 2.53, forzar a 2.53)
  $precio_final = max($precio_final, 2.53);

  echo number_format($precio_final, 2, '.', '');
} else {
  echo "0.00";
}
?>
