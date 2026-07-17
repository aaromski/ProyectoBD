<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

// Validamos si existe una sesión de usuario activa y legítima
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit();
}

require_once 'conexion.php';

try {
  /** @var PDO $conn */
  $id_usuario = $_SESSION['id_usuario'];
  $rol = $_SESSION['rol'];

  if ($rol === 'cliente') {
    // Consultamos uniendo la tabla usuarios y clientes mediante el id_usuario común
    $sql = "SELECT u.nombres, u.apellidos, u.cedula, u.correo, u.telefono, c.id_cliente, c.saldo
                FROM usuarios u
                INNER JOIN clientes c ON u.id_usuario = c.id_usuario
                WHERE u.id_usuario = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
      echo json_encode([
        'success' => true,
        'rol' => 'cliente',
        'id_cliente' => $usuario['id_cliente'],
        'nombres' => $usuario['nombres'],
        'apellidos' => $usuario['apellidos'],
        'cedula' => $usuario['cedula'],
        'correo' => $usuario['correo'],
        'tlf' => $usuario['telefono'],
        'saldo' => $usuario['saldo']
      ]);
    } else {
      echo json_encode(['error' => 'Perfil de cliente no encontrado']);
    }
  }
  elseif ($rol === 'chofer') {
    // CORRECCIÓN AQUÍ: Buscamos en la tabla usuarios vinculando a choferes por el ID de usuario único, evitando colisiones de IDs autoincrementales de otras tablas
    $sql = "SELECT u.nombres, u.apellidos, u.cedula, u.correo, u.telefono, c.id_chofer, c.id_banco AS banco, c.nro_cuenta, c.saldo
                FROM usuarios u
                INNER JOIN choferes c ON u.id_usuario = c.id_usuario
                WHERE u.id_usuario = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
      echo json_encode([
        'success' => true,
        'rol' => 'chofer',
        'id_chofer' => $usuario['id_chofer'],
        'nombres' => $usuario['nombres'],
        'apellidos' => $usuario['apellidos'],
        'cedula' => $usuario['cedula'],
        'correo' => $usuario['correo'],
        'tlf' => $usuario['telefono'],
        'banco' => $usuario['banco'],
        'nro_cuenta' => $usuario['nro_cuenta'],
        'saldo' => $usuario['saldo']
      ]);
    } else {
      echo json_encode(['error' => 'Perfil de chofer no encontrado']);
    }
  } else if ($rol === 'personal' || $rol === 'admin') {
    $sql = "SELECT nombres, apellidos, cedula, telefono, correo
            FROM usuarios
            WHERE id_usuario = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificamos qué rol tiene para confirmar que es personal o admin
    $sql_rol = "SELECT tipo_rol FROM roles_asignados WHERE id_usuario = :id";
    $stmt_rol = $conn->prepare($sql_rol);
    $stmt_rol->execute([':id' => $id_usuario]);
    $rol = $stmt_rol->fetchColumn();

    // Solo permitimos el acceso si es personal o admin
    if ($rol === 'personal' || $rol === 'admin') {
      echo json_encode([
        'success' => true,
        'data' => $usuario,
        'rol' => $rol
      ]);
    } else {
      echo json_encode(['error' => 'Acceso denegado']);
    }
  }

} catch (PDOException $e) {
  echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
