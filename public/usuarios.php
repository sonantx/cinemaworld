<?php
require_once __DIR__ . '/../includes/sesion.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Cinema World</title>
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
            <h1>Usuarios</h1>

            <div id="mensaje"></div>

            <h2 id="form-titulo">Nuevo usuario</h2>
            <form id="form-usuario">
                <input type="hidden" id="accion" name="accion" value="crear">
                <input type="hidden" id="idUsuario" name="idUsuario" value="">

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required maxlength="30">

                <label for="paterno">Apellido paterno</label>
                <input type="text" id="paterno" name="paterno" maxlength="30">

                <label for="materno">Apellido materno</label>
                <input type="text" id="materno" name="materno" maxlength="30">

                <button type="submit" id="btn-submit">Crear usuario</button>
                <button type="button" id="btn-cancelar" class="btn" style="background:#475569; display:none;">Cancelar</button>
            </form>

            <h2>Listado de usuarios</h2>
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
                <tbody id="tabla-usuarios">
                    <tr><td colspan="5">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </main>

    <script src="js/comun.js"></script>
    <script src="js/usuarios.js"></script>
</body>
</html>
