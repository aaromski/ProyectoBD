<?php
header('Content-Type: application/json');
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $modo = isset($_POST['modo_registro']) ? $_POST['modo_registro'] : 'nuevo';

  /** @var PDO $conn */
if($modo === 'nuevo') {
  $rol_registro = isset($_POST['reg_rol']) ? $_POST['reg_rol'] : 'cliente';
  $nombres = trim($_POST['nombres']);
  $apellidos = trim($_POST['apellidos']);
  $cedula = trim($_POST['cedula']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $correo = trim($_POST['correo']);
  $telefono = trim($_POST['prefijo'] . $_POST['telefono']);

  if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($password) || empty($correo) || empty($telefono)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos básicos son obligatorios.']);
    exit();
  }
    try {
      // 1. Validar si la CÉDULA ya existe en el sistema
      $check_ci_sql = "SELECT id_usuario FROM usuarios WHERE cedula = :cedula";
      $check_ci_stmt = $conn->prepare($check_ci_sql);
      $check_ci_stmt->execute([':cedula' => $cedula]);
      if ($check_ci_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un usuario registrado con ese número de Cédula.']);
        exit();
      }

  // 2. Validar si el CORREO ya existe en el sistema
      $check_mail_sql = "SELECT id_usuario FROM usuarios WHERE correo = :correo";
      $check_mail_stmt = $conn->prepare($check_mail_sql);
      $check_mail_stmt->execute([':correo' => $correo]);
      if ($check_mail_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El Correo Electrónico ya se encuentra registrado por otro usuario.']);
        exit();
      }
  // -------------------------------------------------------------------------

  // Encriptamos la contraseña de manera segura
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


      $conn->beginTransaction();

      // 1. Insertar en tabla usuarios
      $sql_user = "INSERT INTO usuarios (nombres, apellidos, cedula, password, correo, telefono) VALUES (?, ?, ?, ?, ?, ?)";
      $stmt_user = $conn->prepare($sql_user);
      $stmt_user->execute([$nombres, $apellidos, $cedula, $password, $correo, $telefono]);
      $id_usuario_creado = $conn->lastInsertId();

      // 2. Insertar según el rol (Entidad + Rol Asignado)
      if ($rol_registro === 'chofer') {
        // Registrar en tabla choferes (asumiendo campos requeridos)
        $sql_chofer = "INSERT INTO choferes (id_usuario, id_banco, nro_cuenta, saldo, contacto1, contacto2, nombre_contacto1, nombre_contacto2) VALUES (?, ?, ?, 0, ?, ?, ?, ?)";
        $stmt_chofer = $conn->prepare($sql_chofer);
        $contacto1 = trim($_POST['prefijo_contacto1'] . $_POST['contacto1']);
        $contacto2 = trim($_POST['prefijo_contacto2'] . $_POST['contacto2']);
        $stmt_chofer->execute([$id_usuario_creado, intval($_POST['banco']), $_POST['nro_cuenta'], $contacto1, $contacto2, $_POST['nombre_contacto1'], $_POST['nombre_contacto2']]);

        $id_chofer_nuevo = $conn->lastInsertId();

        // Crear registro en evaluaciones
        $conn->prepare("INSERT INTO evaluaciones_choferes (id_chofer, id_personal, nota_psicologica, fecha, estado) VALUES (?, NULL, NULL, NOW(), 'pendiente')")
          ->execute([$id_chofer_nuevo]);

        // Asignar rol en roles_asignados
        $conn->prepare("INSERT INTO roles_asignados (id_usuario, tipo_rol) VALUES (?, 'chofer')")
          ->execute([$id_usuario_creado]);
      } elseif ($rol_registro === 'cliente') {
        // Registrar en clientes
        $conn->prepare("INSERT INTO clientes (id_usuario, saldo) VALUES (?, 0)")->execute([$id_usuario_creado]);

        // Asignar rol (ESTO FALTABA)
        $conn->prepare("INSERT INTO roles_asignados (id_usuario, tipo_rol) VALUES (?, 'cliente')")->execute([$id_usuario_creado]);
      } else {
        // Para personal o admin
        $tipo_rol = ($rol_registro === 'personal') ? 'personal' : 'admin';
        $conn->prepare("INSERT INTO roles_asignados (id_usuario, tipo_rol) VALUES (?, ?)")->execute([$id_usuario_creado, $tipo_rol]);
      }

      $conn->commit();
      echo json_encode(['success' => true, 'message' => '¡Registro completado con éxito!']);

    } catch (Exception $e) {
      $conn->rollBack();
      echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
  } else if ($modo === 'rol') {
    // 2. LÓGICA PARA "YA TENGO CUENTA"
    $cedula = trim($_POST['cedula_ya']);
    $correo = trim($_POST['correo_ya']);
    $password = $_POST['password_ya']; // Se valida con password_verify
    $rol_nuevo = $_POST['reg_rol2'];

    if (empty($cedula) || empty($correo) || empty($password)) {
      echo json_encode(['success' => false, 'message' => 'Cédula, correo y contraseña son obligatorios.']);
      exit();
    }

    // Buscar al usuario
    $stmt = $conn->prepare("SELECT id_usuario, password FROM usuarios WHERE cedula = ? AND correo = ?");
    $stmt->execute([$cedula, $correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
      $id_usuario = $usuario['id_usuario'];

      try {

        $stmt_roles = $conn->prepare("SELECT tipo_rol FROM roles_asignados WHERE id_usuario = ?");
        $stmt_roles->execute([$id_usuario]);
        $roles_actuales = $stmt_roles->fetchAll(PDO::FETCH_COLUMN);

        // 2. LOGICA DE NEGOCIO Y CONTROL DE ROLES CRUZADOS
        if ($rol_nuevo === 'cliente') {
          // Si quiere registrarse como cliente pero YA es cliente, se bloquea.
          if (in_array('cliente', $roles_actuales)) {
            echo json_encode(['success' => false, 'message' => 'Esta cuenta ya está registrada con el perfil de Pasajero/Cliente. Elige un perfil complementario (Conductor).']);
            exit();
          }
        } else if ($rol_nuevo === 'chofer') {
          // Si quiere registrarse como chofer pero YA es chofer, se bloquea.
          if (in_array('chofer', $roles_actuales)) {
            echo json_encode(['success' => false, 'message' => 'Esta cuenta ya está registrada con el perfil de Conductor/Chofer. Elige un perfil complementario (Pasajero).']);
            exit();
          }
        }

        $conn->beginTransaction();

        // 2. Insertar nuevo rol
        if ($rol_nuevo === 'chofer') {
          // Verificar si ya tiene el rol de chofer para evitar duplicados
          $stmt_check = $conn->prepare("SELECT id_usuario FROM roles_asignados WHERE id_usuario = ? AND tipo_rol = 'chofer'");
          $stmt_check->execute([$id_usuario]);

          if (!$stmt_check->fetch()) {
            // Insertar en choferes
            $sql_chofer = "INSERT INTO choferes (id_usuario, id_banco, nro_cuenta, saldo, contacto1, contacto2, nombre_contacto1, nombre_contacto2) VALUES (?, ?, ?, 0, ?, ?, ?, ?)";
            $stmt_chofer = $conn->prepare($sql_chofer);
            $contacto1_ya = trim($_POST['prefijo_contacto1_ya'] . $_POST['contacto1_ya']);
            $contacto2_ya = trim($_POST['prefijo_contacto2_ya'] . $_POST['contacto2_ya']);
            $stmt_chofer->execute([$id_usuario, intval($_POST['banco_ya']), $_POST['nro_cuenta_ya'], $contacto1_ya, $contacto2_ya, $_POST['nombre_contacto1_ya'], $_POST['nombre_contacto2_ya']]);

            $id_chofer_nuevo = $conn->lastInsertId();
            $conn->prepare("INSERT INTO evaluaciones_choferes (id_chofer, id_personal, nota_psicologica, fecha, estado) VALUES (?, NULL, NULL, NOW(), 'pendiente')")->execute([$id_chofer_nuevo]);

            // Asignar rol
            $conn->prepare("INSERT INTO roles_asignados (id_usuario, tipo_rol) VALUES (?, 'chofer')")->execute([$id_usuario]);
          }
        } else if ($rol_nuevo === 'cliente') {
          // Asignar rol cliente
          $conn->prepare("INSERT IGNORE INTO roles_asignados (id_usuario, tipo_rol) VALUES (?, 'cliente')")->execute([$id_usuario]);
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Rol añadido correctamente.', 'rol' => $rol_nuevo]);

      } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al asignar rol: ' . $e->getMessage()]);
      }
    } else {
      echo json_encode(['success' => false, 'message' => 'Credenciales inválidas.']);
    }
  }
}
?>




