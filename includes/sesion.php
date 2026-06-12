<?php
/**
 * Inicia la sesión y expone helpers de autenticación.
 * Incluir SIEMPRE antes de imprimir cualquier salida HTML.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioAutenticado() {
    return isset($_SESSION['idUsuario']);
}

function esAdmin() {
    return !empty($_SESSION['esAdmin']);
}

function requerirLogin() {
    if (!usuarioAutenticado()) {
        header("Location: login.php");
        exit;
    }
}

function requerirAdmin() {
    requerirLogin();
    if (!esAdmin()) {
        header("Location: index.php");
        exit;
    }
}
