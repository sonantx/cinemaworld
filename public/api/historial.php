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
        $res = pg_query($conn, "
            SELECT hv.idUsuario, hv.idContenido, hv.Progreso, hv.Fecha, hv.Hora,
                   u.Nombre AS NombreUsuario, c.Titulo
            FROM HistorialVisualizacion hv
            JOIN Usuario u ON hv.idUsuario = u.idUsuario
            JOIN Contenido c ON hv.idContenido = c.idContenido
            ORDER BY hv.Fecha DESC, hv.Hora DESC
        ");
        $datos = pg_fetch_all($res) ?: [];
        echo json_encode(['ok' => true, 'datos' => $datos]);
        break;

    case 'opciones':
        $resUsuarios = pg_query($conn, "SELECT idUsuario, Nombre FROM Usuario ORDER BY Nombre");
        $usuarios = pg_fetch_all($resUsuarios) ?: [];

        $resContenido = pg_query($conn, "SELECT idContenido, Titulo FROM Contenido ORDER BY Titulo");
        $contenidos = pg_fetch_all($resContenido) ?: [];

        echo json_encode(['ok' => true, 'usuarios' => $usuarios, 'contenidos' => $contenidos]);
        break;

    case 'crear':
        $idUsuario   = (int) ($_POST['idUsuario'] ?? 0);
        $idContenido = (int) ($_POST['idContenido'] ?? 0);
        $progreso    = (int) ($_POST['progreso'] ?? 0);

        if ($idUsuario <= 0 || $idContenido <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Selecciona un usuario y un contenido.']);
            break;
        }
        if ($progreso < 0 || $progreso > 100) {
            echo json_encode(['ok' => false, 'error' => 'El progreso debe estar entre 0 y 100.']);
            break;
        }

        $sql = "INSERT INTO HistorialVisualizacion (idUsuario, idContenido, Progreso, Fecha, Hora)
                VALUES ($idUsuario, $idContenido, $progreso, CURRENT_DATE, CURRENT_TIME)";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Registro de historial creado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al crear historial: ' . pg_last_error($conn)]);
        }
        break;

    case 'editar':
        $idUsuario   = (int) ($_POST['idUsuario'] ?? 0);
        $idContenido = (int) ($_POST['idContenido'] ?? 0);
        $progreso    = (int) ($_POST['progreso'] ?? 0);

        if ($idUsuario <= 0 || $idContenido <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
            break;
        }
        if ($progreso < 0 || $progreso > 100) {
            echo json_encode(['ok' => false, 'error' => 'El progreso debe estar entre 0 y 100.']);
            break;
        }

        $sql = "UPDATE HistorialVisualizacion
                SET Progreso = $progreso, Fecha = CURRENT_DATE, Hora = CURRENT_TIME
                WHERE idUsuario = $idUsuario AND idContenido = $idContenido";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Registro actualizado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al actualizar registro: ' . pg_last_error($conn)]);
        }
        break;

    case 'eliminar':
        $idUsuario   = (int) ($_POST['idUsuario'] ?? 0);
        $idContenido = (int) ($_POST['idContenido'] ?? 0);

        if ($idUsuario <= 0 || $idContenido <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
            break;
        }

        $r = pg_query($conn, "DELETE FROM HistorialVisualizacion WHERE idUsuario = $idUsuario AND idContenido = $idContenido");

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Registro eliminado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al eliminar registro: ' . pg_last_error($conn)]);
        }
        break;

    case 'obtener':
        $idUsuario   = (int) ($_GET['idUsuario'] ?? 0);
        $idContenido = (int) ($_GET['idContenido'] ?? 0);

        $res = pg_query($conn, "SELECT * FROM HistorialVisualizacion WHERE idUsuario = $idUsuario AND idContenido = $idContenido");
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
