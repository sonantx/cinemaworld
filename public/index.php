<?php
require_once __DIR__ . '/../includes/sesion.php';
requerirLogin();
require_once __DIR__ . '/../includes/conexion.php';
$conn = conectar();

// Películas
$resPeliculas = pg_query($conn, "SELECT * FROM vista_peliculas ORDER BY titulo");

// Series
$resSeries = pg_query($conn, "SELECT * FROM vista_series ORDER BY titulo");

// Documentales
$resDocumentales = pg_query($conn, "SELECT * FROM vista_documentales ORDER BY titulo");

// Último contenido agregado (para el banner principal)
$resHero = pg_query($conn, "
    SELECT c.idContenido, c.Titulo, c.Descripcion, c.Imagen, c.TrailerURL
    FROM Contenido c
    ORDER BY c.idContenido DESC
    LIMIT 1
");
$hero = pg_fetch_assoc($resHero);

/**
 * Imprime una card de contenido con los atributos data-* necesarios para
 * que el JS pueda reproducir el tráiler y manejar "Mi Lista" sin recargar.
 */
function imprimirCard($fila, $extraInfo) {
    $id      = (int) $fila['idcontenido'];
    $titulo  = htmlspecialchars($fila['titulo']);
    $imagen  = htmlspecialchars($fila['imagen'] ?? 'placeholder.svg');
    $trailer = htmlspecialchars($fila['trailerurl'] ?? '');
    ?>
    <div class="card" data-id="<?= $id ?>" data-trailer="<?= $trailer ?>" data-titulo="<?= $titulo ?>">
        <img src="img/<?= $imagen ?>" alt="<?= $titulo ?>" class="poster">
        <div style="padding:15px;">
            <h3><?= $titulo ?></h3>
            <?= $extraInfo ?>
            <div class="card-acciones">
                <?php if ($trailer): ?>
                    <button class="btn btn-play" type="button">▶ Reproducir</button>
                <?php endif; ?>
                <button class="btn btn-favorito" type="button" data-id="<?= $id ?>">+ Mi Lista</button>
            </div>
        </div>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema World</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

    <header>
        <a href="index.php" class="logo">
            Cinema <span>World</span>
        </a>

        <nav>
            <a href="index.php">Inicio</a>
            <a href="#peliculas">Películas</a>
            <a href="#series">Series</a>
            <a href="#documentales">Documentales</a>
            <a href="#milista">Mi Lista</a>
        </nav>

        <input type="search" id="buscador" placeholder="Buscar...">

        <div class="menu-usuario">
            <button id="btn-usuario" class="btn-usuario" type="button">
                👤 <?= htmlspecialchars($_SESSION['nombreUsuario']) ?>
            </button>
            <div id="dropdown-usuario" class="dropdown-usuario">
                <?php if (esAdmin()): ?>
                    <a href="admin.php">Administración</a>
                <?php endif; ?>
                <a href="logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>

    <main>
        <div id="mensaje"></div>

        <?php if ($hero): ?>
        <section class="hero" style="background-image:url('img/<?= htmlspecialchars($hero['imagen'] ?? 'placeholder.svg') ?>');"
                 data-id="<?= (int) $hero['idcontenido'] ?>"
                 data-trailer="<?= htmlspecialchars($hero['trailerurl'] ?? '') ?>"
                 data-titulo="<?= htmlspecialchars($hero['titulo']) ?>">
            <div class="hero-overlay"></div>
            <div class="info">
                <h1><?= htmlspecialchars($hero['titulo']) ?></h1>
                <p><?= htmlspecialchars($hero['descripcion'] ?? '') ?></p>
                <?php if ($hero['trailerurl']): ?>
                    <button class="btn btn-play-hero" type="button">▶ Reproducir</button>
                <?php endif; ?>
                <button class="btn btn-favorito btn-favorito-hero" type="button" data-id="<?= (int) $hero['idcontenido'] ?>">+ Mi Lista</button>
            </div>
        </section>
        <?php endif; ?>

        <section id="continuar">
            <h2>Continuar viendo</h2>
            <div class="contenedor" id="contenedor-continuar">
                <p>Cargando...</p>
            </div>
        </section>

        <section id="peliculas">
            <h2>Películas</h2>
            <div class="contenedor">
                <?php while ($fila = pg_fetch_assoc($resPeliculas)):
                    $extra = '<p>Duración: ' . htmlspecialchars($fila['duracion']) . '</p>'
                           . '<p>Director: ' . htmlspecialchars($fila['director']) . '</p>';
                    imprimirCard($fila, $extra);
                endwhile; ?>
            </div>
        </section>

        <section id="series">
            <h2>Series</h2>
            <div class="contenedor">
                <?php while ($fila = pg_fetch_assoc($resSeries)):
                    $extra = '<p>Duración por episodio: ' . htmlspecialchars($fila['duracion']) . '</p>'
                           . '<p>Director: ' . htmlspecialchars($fila['director']) . '</p>';
                    imprimirCard($fila, $extra);
                endwhile; ?>
            </div>
        </section>

        <section id="documentales">
            <h2>Documentales</h2>
            <div class="contenedor">
                <?php while ($fila = pg_fetch_assoc($resDocumentales)):
                    $extra = '<p>Duración: ' . htmlspecialchars($fila['duracion']) . '</p>'
                           . '<p>Director: ' . htmlspecialchars($fila['director']) . '</p>';
                    imprimirCard($fila, $extra);
                endwhile; ?>
            </div>
        </section>

        <section id="milista">
            <h2>Mi Lista</h2>
            <div class="contenedor" id="contenedor-milista">
                <p>Cargando...</p>
            </div>
        </section>

    </main>

    <footer>
        <p>Cinema World &copy; 2026 - Proyecto final</p>
        <p>Elaborado por: Mónica López Hernández y Alfredo López</p>
    </footer>

    <!-- Modal del reproductor de tráiler -->
    <div id="modal-trailer" class="modal-trailer" style="display:none;">
        <div class="modal-contenido">
            <button id="cerrar-modal" class="btn-cerrar" type="button">&times;</button>
            <h3 id="modal-titulo"></h3>
            <div class="modal-video">
                <iframe id="iframe-trailer" width="100%" height="100%"
                        src="" title="Tráiler" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script src="js/comun.js"></script>
    <script src="js/index.js"></script>
</body>
</html>
<?php pg_close($conn); ?>
