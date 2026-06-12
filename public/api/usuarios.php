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
        $res = pg_query($conn, "SELECT * FROM Usuario ORDER BY idUsuario");
        $datos = pg_fetch_all($res) ?: [];
        echo json_encode(['ok' => true, 'datos' => $datos]);
        break;

    case 'crear':
        $nombre  = pg_escape_string($conn, $_POST['nombre'] ?? '');
        $paterno = pg_escape_string($conn, $_POST['paterno'] ?? '');
        $materno = pg_escape_string($conn, $_POST['materno'] ?? '');

        if ($nombre === '') {
            echo json_encode(['ok' => false, 'error' => 'El nombre es obligatorio.']);
            break;
        }

        $sql = "INSERT INTO Usuario (Nombre, A_Paterno, A_Materno) VALUES ('$nombre', '$paterno', '$materno')";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Usuario creado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al crear usuario: ' . pg_last_error($conn)]);
        }
        break;

    case 'editar':
        $id      = (int) ($_POST['idUsuario'] ?? 0);
        $nombre  = pg_escape_string($conn, $_POST['nombre'] ?? '');
        $paterno = pg_escape_string($conn, $_POST['paterno'] ?? '');
        $materno = pg_escape_string($conn, $_POST['materno'] ?? '');

        if ($id <= 0 || $nombre === '') {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
            break;
        }

        $sql = "UPDATE Usuario SET Nombre = '$nombre', A_Paterno = '$paterno', A_Materno = '$materno' WHERE idUsuario = $id";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Usuario actualizado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al actualizar usuario: ' . pg_last_error($conn)]);
        }
        break;

    case 'eliminar':
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['ok' => false, 'error' => 'ID inválido.']);
            break;
        }

        $r = pg_query($conn, "DELETE FROM Usuario WHERE idUsuario = $id");

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Usuario eliminado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar el usuario: ' . pg_last_error($conn)]);
        }
        break;

    case 'obtener':
        $id = (int) ($_GET['id'] ?? 0);
        $res = pg_query($conn, "SELECT * FROM Usuario WHERE idUsuario = $id");
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
