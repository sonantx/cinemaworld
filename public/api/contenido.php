<?php
require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/conexion.php';

if (!esAdmin()) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
    exit;
}

$conn = conectar();
$accion = $_REQUEST['accion'] ?? '';

/**
 * Procesa un archivo subido (input type="file" name="imagen").
 * Devuelve el nombre del archivo guardado en public/img/, o null si no
 * se subió ningún archivo.
 */
function subirImagen($archivo) {
    if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $permitidas)) {
        return null;
    }

    $nombreNuevo = 'contenido_' . uniqid() . '.' . $ext;
    $destino = __DIR__ . '/../img/' . $nombreNuevo;

    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
        return $nombreNuevo;
    }
    return null;
}

header('Content-Type: application/json; charset=utf-8');

switch ($accion) {

    case 'listar':
        $res = pg_query($conn, "
            SELECT c.idContenido, c.Titulo, c.Duracion, c.Descripcion, c.Imagen,
                   c.idTipoContenido, c.idDirector,
                   t.Tipo AS TipoContenido,
                   d.Nombre AS NombreDirector, d.A_Paterno AS ApellidoDirector
            FROM Contenido c
            JOIN TipoContenido t ON c.idTipoContenido = t.idTipoContenido
            LEFT JOIN Director d ON c.idDirector = d.idDirector
            ORDER BY c.idContenido
        ");
        $datos = pg_fetch_all($res) ?: [];
        echo json_encode(['ok' => true, 'datos' => $datos]);
        break;

    case 'opciones':
        // Tipos y directores para llenar los <select> del formulario
        $resTipos = pg_query($conn, "SELECT * FROM TipoContenido ORDER BY idTipoContenido");
        $tipos = pg_fetch_all($resTipos) ?: [];

        $resDirectores = pg_query($conn, "SELECT idDirector, Nombre, A_Paterno FROM Director ORDER BY Nombre");
        $directores = pg_fetch_all($resDirectores) ?: [];

        echo json_encode(['ok' => true, 'tipos' => $tipos, 'directores' => $directores]);
        break;

    case 'crear':
        $titulo      = pg_escape_string($conn, $_POST['titulo'] ?? '');
        $duracion    = pg_escape_string($conn, $_POST['duracion'] ?? '');
        $descripcion = pg_escape_string($conn, $_POST['descripcion'] ?? '');
        $idTipo      = (int) ($_POST['idTipoContenido'] ?? 0);
        $idDirector  = ($_POST['idDirector'] ?? '') === '' ? 'NULL' : (int) $_POST['idDirector'];
        $trailer     = pg_escape_string($conn, trim($_POST['trailer'] ?? ''));
        $trailer_sql = $trailer === '' ? 'NULL' : "'$trailer'";

        if ($titulo === '' || $duracion === '' || $idTipo <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Título, duración y tipo son obligatorios.']);
            break;
        }

        $nombreImagen = subirImagen($_FILES['imagen'] ?? null);
        $imagen_sql = $nombreImagen === null ? 'NULL' : "'" . pg_escape_string($conn, $nombreImagen) . "'";

        $sql = "INSERT INTO Contenido (Titulo, Duracion, Descripcion, Imagen, TrailerURL, idTipoContenido, idDirector)
                VALUES ('$titulo', '$duracion', '$descripcion', $imagen_sql, $trailer_sql, $idTipo, $idDirector)";
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Contenido creado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al crear contenido: ' . pg_last_error($conn)]);
        }
        break;

    case 'editar':
        $id          = (int) ($_POST['idContenido'] ?? 0);
        $titulo      = pg_escape_string($conn, $_POST['titulo'] ?? '');
        $duracion    = pg_escape_string($conn, $_POST['duracion'] ?? '');
        $descripcion = pg_escape_string($conn, $_POST['descripcion'] ?? '');
        $idDirector  = ($_POST['idDirector'] ?? '') === '' ? 'NULL' : (int) $_POST['idDirector'];
        $trailer     = pg_escape_string($conn, trim($_POST['trailer'] ?? ''));
        $trailer_sql = $trailer === '' ? 'NULL' : "'$trailer'";

        if ($id <= 0 || $titulo === '' || $duracion === '') {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
            break;
        }

        $nombreImagen = subirImagen($_FILES['imagen'] ?? null);

        // El tipo de contenido no se modifica para no romper la relación
        // ya creada en Pelicula/Serie/Documental.
        if ($nombreImagen !== null) {
            $imagen_sql = "'" . pg_escape_string($conn, $nombreImagen) . "'";
            $sql = "UPDATE Contenido
                    SET Titulo = '$titulo', Duracion = '$duracion',
                        Descripcion = '$descripcion', Imagen = $imagen_sql,
                        TrailerURL = $trailer_sql, idDirector = $idDirector
                    WHERE idContenido = $id";
        } else {
            $sql = "UPDATE Contenido
                    SET Titulo = '$titulo', Duracion = '$duracion',
                        Descripcion = '$descripcion', TrailerURL = $trailer_sql,
                        idDirector = $idDirector
                    WHERE idContenido = $id";
        }
        $r = pg_query($conn, $sql);

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Contenido actualizado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al actualizar contenido: ' . pg_last_error($conn)]);
        }
        break;

    case 'eliminar':
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['ok' => false, 'error' => 'ID inválido.']);
            break;
        }

        // ON DELETE CASCADE elimina automáticamente el registro relacionado
        // en Pelicula/Serie/Documental y en HistorialVisualizacion.
        $r = pg_query($conn, "DELETE FROM Contenido WHERE idContenido = $id");

        if ($r) {
            echo json_encode(['ok' => true, 'mensaje' => 'Contenido eliminado correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al eliminar contenido: ' . pg_last_error($conn)]);
        }
        break;

    case 'obtener':
        $id = (int) ($_GET['id'] ?? 0);
        $res = pg_query($conn, "SELECT * FROM Contenido WHERE idContenido = $id");
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
