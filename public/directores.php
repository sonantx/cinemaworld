<?php
require_once __DIR__ . '/../includes/sesion.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directores - Cinema World</title>
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
            <h1>Directores</h1>

            <div id="mensaje"></div>

            <h2 id="form-titulo">Nuevo director</h2>
            <form id="form-director">
                <input type="hidden" id="accion" name="accion" value="crear">
                <input type="hidden" id="idDirector" name="idDirector" value="">

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required maxlength="30">

                <label for="paterno">Apellido paterno</label>
                <input type="text" id="paterno" name="paterno" required maxlength="30">

                <label for="materno">Apellido materno (opcional)</label>
                <input type="text" id="materno" name="materno" maxlength="30">

                <button type="submit" id="btn-submit">Crear director</button>
                <button type="button" id="btn-cancelar" class="btn" style="background:#475569; display:none;">Cancelar</button>
            </form>

            <h2>Listado de directores</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido paterno</th>
                        <th>Apellido materno</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-directores">
                    <tr><td colspan="5">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </main>

    <script src="js/comun.js"></script>
    <script src="js/directores.js"></script>
</body>
</html>
