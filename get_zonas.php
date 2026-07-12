<?php
// Incluimos la conexión donde definiste $conn
include('conexion.php');

try {
  // Usamos el objeto $conn (de tipo PDO) para preparar y ejecutar la consulta
  /** @var PDO $conn */
  $stmt = $conn->prepare("SELECT id_zona, nombre_zona FROM zonas ORDER BY nombre_zona ASC");
  $stmt->execute();

  // Obtenemos todos los resultados
  $zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Imprimimos las opciones
  foreach ($zonas as $zona) {
    echo "<option value='".$zona['id_zona']."'>".$zona['nombre_zona']."</option>";
  }
} catch(PDOException $e) {
  // Si hay error en la consulta, lo mostramos discretamente
  echo "<option value=''>Error al cargar zonas</option>";
}
?>
