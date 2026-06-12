/**
 * Helpers comunes para los módulos de administración.
 * Evitan recargar la página: usan fetch() y muestran mensajes/tablas
 * actualizando solo el HTML necesario.
 */

function mostrarMensaje(idContenedor, texto, tipo) {
    const cont = document.getElementById(idContenedor);
    cont.innerHTML = `<div class="mensaje ${tipo}">${texto}</div>`;
    // El mensaje se oculta solo después de unos segundos si fue éxito
    if (tipo === 'exito') {
        setTimeout(() => { cont.innerHTML = ''; }, 4000);
    }
}

async function peticionJSON(url, opciones = {}) {
    const respuesta = await fetch(url, opciones);
    if (!respuesta.ok && respuesta.status === 401) {
        // Sesión expirada: regresar al login
        window.location.href = 'login.php';
        return null;
    }
    return respuesta.json();
}

function escapeHTML(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}
