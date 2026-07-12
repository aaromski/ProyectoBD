<?php
$host = "localhost";
$dbname = "carreritabd"; // Cambia por el nombre de tu base de datos
$username = "root";
$password = ""; // Por defecto en XAMPP está vacío

$conn = null;

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}
?>
