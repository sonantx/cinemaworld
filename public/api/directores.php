<?php
require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!esAdmin()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
    exit;
}

$conn = conectar();
$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {

    case 'listar':
        $res = pg_query($conn, "SELECT * FROM Director ORDER BY idDirector");
        $datos = pg_fetch_all($res) ?: [];
        echo json_encode(['ok' => true, 'datos' => $datos]);
        break;

    case 'crear':
        $nombre  = pg_escape_string($conn, $_POST['nombre'] ?? '');
        $paterno = pg_escape_string($conn, $_POST['paterno'] ?? '');
        $materno = pg_escape_string($conn, $_POST['materno'] ?? '');
        $materno_sql = $materno === '' ? 'NULL' : "'$materno'";

        if ($nombre === '' || $paterno === '') {
            echo json_encode(['ok' => false, 'error' => 'Nombre y apellido paterno son obligatorios.']);
            break;
        }

        $sql = "INSERT INTO Director (Nombre, A_Paterno, A_Materno) VALUES ('$nombre', '$paterno', $materno_sql)";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Director creado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al crear director: ' . pg_last_error($conn)]);
        }
        break;

    case 'editar':
        $id      = (int) ($_POST['idDirector'] ?? 0);
        $nombre  = pg_escape_string($conn, $_POST['nombre'] ?? '');
        $paterno = pg_escape_string($conn, $_POST['paterno'] ?? '');
        $materno = pg_escape_string($conn, $_POST['materno'] ?? '');
        $materno_sql = $materno === '' ? 'NULL' : "'$materno'";

        if ($id <= 0 || $nombre === '' || $paterno === '') {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
            break;
        }

        $sql = "UPDATE Director SET Nombre = '$nombre', A_Paterno = '$paterno', A_Materno = $materno_sql WHERE idDirector = $id";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Director actualizado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al actualizar director: ' . pg_last_error($conn)]);
        }
        break;

    case 'eliminar':
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['ok' => false, 'error' => 'ID inválido.']);
            break;
        }

        $r = pg_query($conn, "DELETE FROM Director WHERE idDirector = $id");

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Director eliminado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar el director (puede tener contenido asociado).']);
        }
        break;

    case 'obtener':
        $id = (int) ($_GET['id'] ?? 0);
        $res = pg_query($conn, "SELECT * FROM Director WHERE idDirector = $id");
        $fila = pg_fetch_assoc($res);

        if ($fila) {
            echo json_encode(['ok' => true, 'dato' => $fila]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'No encontrado.']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acción no reconocida.']);
}

pg_close($conn);
