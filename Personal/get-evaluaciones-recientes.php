<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['personal', 'admin'])) {
  echo json_encode(['success' => false, 'msg' => 'No autorizado']);
  exit();
}

require_once '../conexion.php';
/** @var PDO $conn */

try {
  $sql = "SELECT *
          FROM (
            SELECT 'psicologica' AS tipo,
                   CONCAT(UPPER(SUBSTRING(SUBSTRING_INDEX(uc.nombres, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(uc.nombres, ' ', 1), 2)),
                          ' ', UPPER(SUBSTRING(SUBSTRING_INDEX(uc.apellidos, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(uc.apellidos, ' ', 1), 2))) AS evaluado,
                   CONCAT(UPPER(SUBSTRING(SUBSTRING_INDEX(up.nombres, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(up.nombres, ' ', 1), 2)),
                          ' ', UPPER(SUBSTRING(SUBSTRING_INDEX(up.apellidos, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(up.apellidos, ' ', 1), 2))) AS evaluador,
                   ec.nota_psicologica AS nota,
                   ec.estado,
                   ec.fecha,
                   NULL AS observacion
            FROM evaluaciones_choferes ec
            INNER JOIN choferes ch ON ec.id_chofer = ch.id_chofer
            INNER JOIN usuarios uc ON ch.id_usuario = uc.id_usuario
            INNER JOIN usuarios up ON ec.id_personal = up.id_usuario

            UNION ALL

            SELECT 'tecnica' AS tipo,
                   CONCAT(v.marca, ' ', v.modelo, ' (', v.placa, ')') AS evaluado,
                   CONCAT(UPPER(SUBSTRING(SUBSTRING_INDEX(up.nombres, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(up.nombres, ' ', 1), 2)),
                          ' ', UPPER(SUBSTRING(SUBSTRING_INDEX(up.apellidos, ' ', 1), 1, 1)), LOWER(SUBSTRING(SUBSTRING_INDEX(up.apellidos, ' ', 1), 2))) AS evaluador,
                   ev.nota_tecnica AS nota,
                   ev.estado,
                   ev.fecha,
                   ev.observacion
            FROM evaluaciones_vehiculos ev
            INNER JOIN vehiculos v ON ev.id_vehiculo = v.id_vehiculo
            INNER JOIN usuarios up ON ev.id_personal = up.id_usuario
          ) AS evaluaciones
          ORDER BY evaluaciones.fecha DESC
          LIMIT 10";

  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $evaluaciones]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
