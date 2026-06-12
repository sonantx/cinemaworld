<?php
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/conexion.php';
$conn = conectar();

$mensaje = "";
$tipoMensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo']);
    $password = $_POST['password'];

    $res = pg_query_params($conn, "SELECT idUsuario, Nombre, Contrasena, Es_Admin FROM Usuario WHERE Correo = $1", [$correo]);
    $usuario = pg_fetch_assoc($res);

    if ($usuario && password_verify($password, $usuario['contrasena'])) {
        $_SESSION['idUsuario'] = $usuario['idusuario'];
        $_SESSION['nombreUsuario'] = $usuario['nombre'];
        $_SESSION['esAdmin'] = ($usuario['es_admin'] === 't' || $usuario['es_admin'] === true);
        header("Location: index.php");
        exit;
    } else {
        $mensaje = "Correo o contraseña incorrectos.";
        $tipoMensaje = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Cinema World</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <div class="logo">Cinema <span>World</span></div>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="registro.php">Registrarme</a>
        </nav>
    </header>

    <main>
        <div class="panel" style="max-width:500px; margin:60px auto;">
            <h1>Iniciar sesión</h1>

            <?php if ($mensaje): ?>
                <div class="mensaje <?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" required
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Entrar</button>
            </form>

            <p style="margin-top:15px;">¿No tienes cuenta? <a href="registro.php" style="color:#8b5cf6;">Regístrate</a></p>
            <p style="margin-top:5px; font-size:.85rem; color:#94a3b8;">
                Cuentas de ejemplo: norma@correo.com / hugo@correo.com ... contraseña: 123456
            </p>
        </div>
    </main>

</body>
</html>
<?php pg_close($conn); ?>
