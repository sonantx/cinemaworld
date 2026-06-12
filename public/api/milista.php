<?php
require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
    exit;
}

$conn = conectar();
$accion = $_REQUEST['accion'] ?? '';
$idUsuario = (int) $_SESSION['idUsuario'];

switch ($accion) {

    case 'listar':
        $res = pg_query_params($conn, "
            SELECT c.idContenido, c.Titulo, c.Duracion, c.Descripcion, c.Imagen, c.TrailerURL,
                   t.Tipo AS TipoContenido
            FROM MiLista m
            JOIN Contenido c ON m.idContenido = c.idContenido
            JOIN TipoContenido t ON c.idTipoContenido = t.idTipoContenido
            WHERE m.idUsuario = $1
            ORDER BY m.FechaAgregado DESC
        ", [$idUsuario]);
        $datos = pg_fetch_all($res) ?: [];
        echo json_encode(['ok' => true, 'datos' => $datos]);
        break;

    case 'ids':
        // Devuelve solo los idContenido del usuario, para marcar botones "+ Mi Lista"
        $res = pg_query_params($conn, "SELECT idContenido FROM MiLista WHERE idUsuario = $1", [$idUsuario]);
        $filas = pg_fetch_all($res) ?: [];
        $ids = array_map(fn($f) => (int) $f['idcontenido'], $filas);
        echo json_encode(['ok' => true, 'ids' => $ids]);
        break;

    case 'agregar':
        $idContenido = (int) ($_POST['idContenido'] ?? 0);

        if ($idContenido <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Contenido inválido.']);
            break;
        }

        $r = pg_query_params($conn, "
            INSERT INTO MiLista (idUsuario, idContenido)
            VALUES ($1, $2)
            ON CONFLICT (idUsuario, idContenido) DO NOTHING
        ", [$idUsuario, $idContenido]);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Agregado a Mi Lista.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al agregar: ' . pg_last_error($conn)]);
        }
        break;

    case 'quitar':
        $idContenido = (int) ($_POST['idContenido'] ?? 0);

        if ($idContenido <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Contenido inválido.']);
            break;
        }

        $r = pg_query_params($conn, "DELETE FROM MiLista WHERE idUsuario = $1 AND idContenido = $2", [$idUsuario, $idContenido]);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Eliminado de Mi Lista.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al eliminar: ' . pg_last_error($conn)]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acción no reconocida.']);
}

pg_close($conn);
