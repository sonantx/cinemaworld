<?php
require_once __DIR__ . '/../includes/sesion.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - Cinema World</title>
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
            <h1>Historial de visualización</h1>

            <div id="mensaje"></div>

            <h2 id="form-titulo">Nuevo registro</h2>
            <form id="form-historial">
                <input type="hidden" id="accion" name="accion" value="crear">

                <label for="idUsuario">Usuario</label>
                <select id="idUsuario" name="idUsuario" required>
                    <option value="">-- Selecciona un usuario --</option>
                </select>

                <label for="idContenido">Contenido</label>
                <select id="idContenido" name="idContenido" required>
                    <option value="">-- Selecciona un contenido --</option>
                </select>

                <label for="progreso">Progreso (%)</label>
                <input type="number" id="progreso" name="progreso" min="0" max="100" required value="0">

                <button type="submit" id="btn-submit">Crear registro</button>
                <button type="button" id="btn-cancelar" class="btn" style="background:#475569; display:none;">Cancelar</button>
            </form>

            <h2>Listado de historial</h2>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Contenido</th>
                        <th>Progreso</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-historial">
                    <tr><td colspan="6">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </main>

    <script src="js/comun.js"></script>
    <script src="js/historial.js"></script>
</body>
</html>
