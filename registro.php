<?php
header('Content-Type: application/json');
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recibimos los datos comunes obligatorios del formulario
  $rol_registro = isset($_POST['reg_rol']) ? $_POST['reg_rol'] : 'cliente';
  $nombres = trim($_POST['nombres']);
  $apellidos = trim($_POST['apellidos']);
  $cedula = trim($_POST['cedula']);
  $password = $_POST['password'];
  $correo = trim($_POST['correo']);
  $telefono = $_POST['prefijo'] . $_POST['telefono'];

  if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($password) || empty($correo) || empty($telefono)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos básicos son obligatorios.']);
    exit();
  }

  /** @var PDO $conn */
  try {
    // -------------------------------------------------------------------------
    // VALIDACIÓN DE DUPLICADOS: Evita pantallas de error de MySQL
    // -------------------------------------------------------------------------

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

    // Iniciamos una transacción para asegurar la integridad relacional
    $conn->beginTransaction();

    // 1. Insertamos primero en la tabla central unificada de USUARIOS
    $sql_usuario = "INSERT INTO usuarios (correo, password, nombres, apellidos, cedula, telefono)
                        VALUES (:correo, :password, :nombres, :apellidos, :cedula, :telefono)";

    $stmt_user = $conn->prepare($sql_usuario);
    $stmt_user->execute([
      ':correo'    => $correo,
      ':password'  => $hashedPassword,
      ':nombres'   => $nombres,
      ':apellidos' => $apellidos,
      ':cedula'    => $cedula,
      ':telefono'  => $telefono
    ]);

    // Obtenemos el ID autoincremental asignado
    $id_usuario_creado = $conn->lastInsertId();

    /// 2. Lógica de asignación de Roles
    // Dentro de registro.php, en la sección de if ($rol_registro === 'chofer')

    // ... después de insertar en la tabla usuarios ($id_usuario_creado)

    if ($rol_registro === 'chofer') {
      // 1. Asignar como chofer
      $sql_chofer = "INSERT INTO choferes (id_usuario, banco, nro_cuenta, saldo) VALUES (:id, :banco, :cuenta, 0)";
      $stmt_chofer = $conn->prepare($sql_chofer);
      $stmt_chofer->execute([':id' => $id_usuario_creado, ':banco' => $_POST['banco'], ':cuenta' => $_POST['nro_cuenta']]);

      // 2. OBTENER EL ID QUE SE GENERÓ EN LA TABLA CHOFERES
      $id_chofer_nuevo = $conn->lastInsertId();

      // 3. Crear el registro en evaluaciones_choferes usando ese ID
      $sql_evaluacion = "INSERT INTO evaluaciones_choferes (id_chofer, id_personal, nota_psicologica, fecha, estado)
                       VALUES (:id_chofer, NULL, NULL, NOW(), 'pendiente')";
      $stmt_evaluacion = $conn->prepare($sql_evaluacion);
      $stmt_evaluacion->execute([':id_chofer' => $id_chofer_nuevo]);
    }
    elseif ($rol_registro === 'cliente') {
      // Asignar como cliente
      $conn->prepare("INSERT INTO clientes (id_usuario, saldo) VALUES (:id, 0)")->execute([':id' => $id_usuario_creado]);
    }
    else {
      $tipo_rol = ($rol_registro === 'personal') ? 'personal' : 'admin';
      $conn->prepare("INSERT INTO roles_asignados (id_usuario, tipo_rol) VALUES (?, ?)")->execute([$id_usuario_creado, $tipo_rol]);
    }

    // Si todo va bien, guardamos cambios de forma definitiva
    $conn->commit();

    echo json_encode(['success' => true, 'message' => '¡Registro completado con éxito!', 'rol' => $rol_registro]);

  } catch (Exception $e) {
    if ($conn->inTransaction()) {
      $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error operativo en base de datos: ' . $e->getMessage()]);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Método de petición no permitido.']);
}
?>
