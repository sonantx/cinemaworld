<?php
require_once __DIR__ . '/../includes/sesion.php';
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema World - Administración</title>
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
            <h1 style="margin-bottom:25px;">Panel de administración</h1>

            <div class="menu-admin">
                <a href="usuarios.php">Usuarios</a>
                <a href="perfiles.php">Perfiles</a>
                <a href="directores.php">Directores</a>
                <a href="contenido.php">Contenido (Películas / Series / Documentales)</a>
                <a href="historial.php">Historial de visualización</a>
            </div>

            <p>Selecciona un módulo para consultar, dar de alta, modificar o eliminar registros.</p>
        </div>
    </main>

</body>
</html>
