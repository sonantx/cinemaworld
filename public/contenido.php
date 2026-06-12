<?php
require_once __DIR__ . '/../includes/sesion.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido - Cinema World</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <div class="logo">Cinema <span>World</span></div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="admin.php">Administración</a>
        </nav>
    </header>

    <main>
        <div class="panel">
            <h1>Contenido (Películas / Series / Documentales)</h1>

            <div id="mensaje"></div>

            <h2 id="form-titulo">Nuevo contenido</h2>
            <form id="form-contenido" enctype="multipart/form-data">
                <input type="hidden" id="accion" name="accion" value="crear">
                <input type="hidden" id="idContenido" name="idContenido" value="">

                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" required maxlength="100">

                <label for="duracion">Duración (HH:MM:SS)</label>
<input type="text" id="duracion" name="duracion" pattern="^([0-9]{2}):([0-5][0-9]):([0-5][0-9])$"
       placeholder="02:28:00" required title="Formato HH:MM:SS, ejemplo: 02:28:00">

                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"></textarea>

                <label for="imagen">Imagen (portada)</label>
                <div id="imagen-actual"></div>
                <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.gif,.webp,.svg">

                <label for="trailer">URL del tráiler (YouTube embed)</label>
                <input type="url" id="trailer" name="trailer" placeholder="https://www.youtube.com/embed/XXXXXXXXXXX">
                <small>Usa el formato "embed", ej: https://www.youtube.com/embed/VIDEO_ID</small>

                <label for="idTipoContenido">Tipo de contenido</label>
                <select id="idTipoContenido" name="idTipoContenido" required>
                    <option value="">-- Selecciona un tipo --</option>
                </select>
                <small id="aviso-tipo" style="display:none;">El tipo de contenido no se puede modificar una vez creado.</small>

                <label for="idDirector">Director</label>
                <select id="idDirector" name="idDirector">
                    <option value="">-- Sin director --</option>
                </select>

                <button type="submit" id="btn-submit">Crear contenido</button>
                <button type="button" id="btn-cancelar" class="btn" style="background:#475569; display:none;">Cancelar</button>
            </form>

            <h2>Listado de contenido</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Duración</th>
                        <th>Director</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-contenido">
                    <tr><td colspan="7">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </main>

    <script src="js/comun.js"></script>
    <script src="js/contenido.js"></script>
</body>
</html>
