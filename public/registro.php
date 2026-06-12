<?php
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/conexion.php';
$conn = conectar();

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = pg_escape_string($conn, trim($_POST['nombre']));
    $paterno  = pg_escape_string($conn, trim($_POST['paterno']));
    $materno  = pg_escape_string($conn, trim($_POST['materno']));
    $correo   = pg_escape_string($conn, trim($_POST['correo']));
    $password = $_POST['password'];

    if ($nombre === '' || $correo === '' || $password === '') {
        $mensaje = "Nombre, correo y contraseña son obligatorios.";
        $tipoMensaje = "error";
    } else {
        // Verificar si el correo ya existe
        $check = pg_query_params($conn, "SELECT idUsuario FROM Usuario WHERE Correo = $1", [trim($_POST['correo'])]);

        if (pg_num_rows($check) > 0) {
            $mensaje = "Ya existe una cuenta con ese correo.";
            $tipoMensaje = "error";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO Usuario (Nombre, A_Paterno, A_Materno, Correo, Contrasena)
                    VALUES ('$nombre', '$paterno', '$materno', '$correo', $1)";
            $r = pg_query_params($conn, $sql, [$hash]);

            if ($r) {
                $row = pg_fetch_assoc(pg_query($conn, "SELECT idUsuario, Nombre FROM Usuario WHERE Correo = '$correo'"));
                $_SESSION['idUsuario'] = $row['idusuario'];
                $_SESSION['nombreUsuario'] = $row['nombre'];
                $_SESSION['esAdmin'] = false;
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Error al crear la cuenta: " . pg_last_error($conn);
                $tipoMensaje = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Cinema World</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <div class="logo">Cinema <span>World</span></div>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="login.php">Iniciar sesión</a>
        </nav>
    </header>

    <main>
        <div class="panel" style="max-width:500px; margin:60px auto;">
            <h1>Crear cuenta</h1>

            <?php if ($mensaje): ?>
                <div class="mensaje <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" action="registro.php">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required maxlength="30"
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">

                <label for="paterno">Apellido paterno</label>
                <input type="text" id="paterno" name="paterno" maxlength="30"
                       value="<?= htmlspecialchars($_POST['paterno'] ?? '') ?>">

                <label for="materno">Apellido materno</label>
                <input type="text" id="materno" name="materno" maxlength="30"
                       value="<?= htmlspecialchars($_POST['materno'] ?? '') ?>">

                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" required maxlength="100"
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required minlength="6">

                <button type="submit">Registrarme</button>
            </form>

            <p style="margin-top:15px;">¿Ya tienes cuenta? <a href="login.php" style="color:#8b5cf6;">Inicia sesión</a></p>
        </div>
    </main>

</body>
</html>
<?php pg_close($conn); ?>
