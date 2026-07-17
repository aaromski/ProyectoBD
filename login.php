<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $correo = trim($_POST['correo']);
  $password = $_POST['password'];
  $rol_solicitado = $_POST['rol']; // 'cliente', 'chofer', 'personal', 'admin'

  if (empty($correo) || empty($password) || empty($rol_solicitado)) {
    header("Location: login.html?error=campos_vacios");
    exit();
  }

  try {
    /** @var PDO $conn */
    // 1. Buscamos las credenciales globales en la tabla unificada de usuarios usando $conn
    $sql = "SELECT id_usuario, nombres, apellidos, password FROM usuarios WHERE correo = :correo";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificamos la contraseña encriptada
    if ($usuario && password_verify($password, $usuario['password'])) {
      $id_usuario = $usuario['id_usuario'];

      // 3. Verificación de Seguridad Relacional según el rol seleccionado
      if ($rol_solicitado === 'cliente') {
        $sql_rol = "SELECT id_cliente FROM clientes WHERE id_usuario = :id_usuario";
        $stmt_rol = $conn->prepare($sql_rol);
        $stmt_rol->execute([':id_usuario' => $id_usuario]);
        $perfil = $stmt_rol->fetch(PDO::FETCH_ASSOC);

        if ($perfil) {
          $_SESSION['id_usuario'] = $id_usuario;
          $_SESSION['cliente_id'] = $perfil['id_cliente'];
          $_SESSION['rol'] = 'cliente';
          $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['apellidos'];
          header("Location: Cliente/dashboard-cliente.html");
          exit();
        }
      }
      elseif ($rol_solicitado === 'chofer') {
        $sql_rol = "SELECT id_chofer FROM choferes WHERE id_usuario = :id_usuario";
        $stmt_rol = $conn->prepare($sql_rol);
        $stmt_rol->execute([':id_usuario' => $id_usuario]);
        $perfil = $stmt_rol->fetch(PDO::FETCH_ASSOC);

        if ($perfil) {
          $_SESSION['id_usuario'] = $id_usuario;
          $_SESSION['chofer_id'] = $perfil['id_chofer'];
          $_SESSION['rol'] = 'chofer';
          $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['apellidos'];
          header("Location: Chofer/dashboard-chofer.html");
          exit();
        }
      }
      elseif ($rol_solicitado === 'personal' || $rol_solicitado === 'admin') {
        // Para el personal administrativo o admin, validamos la tabla de permisos roles_asignados
        $sql_rol = "SELECT tipo_rol FROM roles_asignados WHERE id_usuario = :id_usuario AND tipo_rol = :rol";
        $stmt_rol = $conn->prepare($sql_rol);

        // Mapeamos los valores exactos definidos en tu ENUM de base de datos
        // Cambia esto:
        $rol_db = ($rol_solicitado === 'personal') ? 'personal' : 'admin';

        $stmt_rol->execute([
          ':id_usuario' => $id_usuario,
          ':rol' => $rol_db
        ]);
        $perfil = $stmt_rol->fetch(PDO::FETCH_ASSOC);

        if ($perfil) {
          $_SESSION['id_usuario'] = $id_usuario;
          $_SESSION['rol'] = $rol_solicitado;
          $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['apellidos'];

          $redirect = ($rol_solicitado === 'personal') ? "Personal/dashboard-personal.html" : "Admin/dashboard-admin.html";
          header("Location: " . $redirect);
          exit();
        }
      }
    }

    // Si llegó aquí, las credenciales fallaron o el usuario no posee la entidad asociada
    header("Location: login.html?error=incorrecto");
    exit();

  } catch (PDOException $e) {
    header("Location: login.html?error=db");
    exit();
  }
} else {
  header("Location: login.html");
  exit();
}
