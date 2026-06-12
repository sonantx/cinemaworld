<?php
/**
 * Conexión a la base de datos PostgreSQL "streamtec".
 *
 * En local (XAMPP) usa los valores por defecto de abajo.
 * En producción (Render/Neon) se usan variables de entorno para no
 * exponer credenciales en el código:
 *   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_SSLMODE
 */

$DB_HOST    = getenv('DB_HOST')    ?: "localhost";
$DB_PORT    = getenv('DB_PORT')    ?: "5433";
$DB_NAME    = getenv('DB_NAME')    ?: "streamtec";
$DB_USER    = getenv('DB_USER')    ?: "app_streamtec";
$DB_PASS    = getenv('DB_PASS')    ?: "streamtec2026";
$DB_SSLMODE = getenv('DB_SSLMODE') ?: "prefer"; // Neon requiere "require"

function conectar() {
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS, $DB_SSLMODE;

    // Los valores con espacios (ej. "Cinema World") deben ir entre comillas
    // simples dentro de la cadena de conexión de pg_connect.
    $dbNameEsc = "'" . str_replace("'", "\\'", $DB_NAME) . "'";

    $conn = pg_connect(
        "host=$DB_HOST port=$DB_PORT dbname=$dbNameEsc user=$DB_USER password=$DB_PASS sslmode=$DB_SSLMODE"
    );

    if (!$conn) {
        die("Error: no se pudo conectar a la base de datos.");
    }

    return $conn;
}
