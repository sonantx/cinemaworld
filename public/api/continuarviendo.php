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
        // Contenido que el usuario ha empezado a ver y no ha terminado (progreso < 100)
        $res = pg_query_params($conn, "
            SELECT c.idContenido, c.Titulo, c.Duracion, c.Descripcion, c.Imagen, c.TrailerURL,
                   t.Tipo AS TipoContenido, hv.Progreso
            FROM HistorialVisualizacion hv
            JOIN Contenido c ON hv.idContenido = c.idContenido
            JOIN TipoContenido t ON c.idTipoContenido = t.idTipoContenido
            WHERE hv.idUsuario = $1 AND hv.Progreso < 100
            ORDER BY hv.Fecha DESC, hv.Hora DESC
        ", [$idUsuario]);
        $datos = pg_fetch_all($res) ?: [];
        echo json_encode(['ok' => true, 'datos' => $datos]);
        break;

    case 'registrar':
        // Se llama cuando el usuario da clic en "Reproducir".
        // Si no existe el registro, lo crea con progreso 0.
        // Si ya existe, solo actualiza la fecha/hora (no toca el progreso).
        $idContenido = (int) ($_POST['idContenido'] ?? 0);

        if ($idContenido <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Contenido inválido.']);
            break;
        }

        $r = pg_query_params($conn, "
            INSERT INTO HistorialVisualizacion (idUsuario, idContenido, Progreso, Fecha, Hora)
            VALUES ($1, $2, 0, CURRENT_DATE, CURRENT_TIME)
            ON CONFLICT (idUsuario, idContenido)
            DO UPDATE SET Fecha = CURRENT_DATE, Hora = CURRENT_TIME
        ", [$idUsuario, $idContenido]);

        if ($r) {
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false, 'error' => pg_last_error($conn)]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acción no reconocida.']);
}

pg_close($conn);
