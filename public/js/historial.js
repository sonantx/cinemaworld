document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-historial');
    const tabla = document.getElementById('tabla-historial');
    const btnSubmit = document.getElementById('btn-submit');
    const btnCancelar = document.getElementById('btn-cancelar');
    const formTitulo = document.getElementById('form-titulo');
    const selectUsuario = document.getElementById('idUsuario');
    const selectContenido = document.getElementById('idContenido');

    cargarOpciones().then(cargarHistorial);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = new FormData(form);
        const resp = await peticionJSON('api/historial.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            resetFormulario();
            cargarHistorial();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    });

    btnCancelar.addEventListener('click', resetFormulario);

    function resetFormulario() {
        form.reset();
        document.getElementById('accion').value = 'crear';
        document.getElementById('progreso').value = '0';
        Array.from(selectUsuario.options).forEach(opt => opt.disabled = false);
        Array.from(selectContenido.options).forEach(opt => opt.disabled = false);
        formTitulo.textContent = 'Nuevo registro';
        btnSubmit.textContent = 'Crear registro';
        btnCancelar.style.display = 'none';
    }

    async function cargarOpciones() {
        const resp = await peticionJSON('api/historial.php?accion=opciones');
        if (!resp || !resp.ok) return;

        selectUsuario.innerHTML = '<option value="">-- Selecciona un usuario --</option>' +
            resp.usuarios.map(u => `<option value="${u.idusuario}">${escapeHTML(u.nombre)}</option>`).join('');

        selectContenido.innerHTML = '<option value="">-- Selecciona un contenido --</option>' +
            resp.contenidos.map(c => `<option value="${c.idcontenido}">${escapeHTML(c.titulo)}</option>`).join('');
    }

    async function cargarHistorial() {
        const resp = await peticionJSON('api/historial.php?accion=listar');
        if (!resp) return;

        if (!resp.ok) {
            tabla.innerHTML = `<tr><td colspan="6">Error al cargar historial.</td></tr>`;
            return;
        }

        if (resp.datos.length === 0) {
            tabla.innerHTML = `<tr><td colspan="6">No hay registros de historial.</td></tr>`;
            return;
        }

        tabla.innerHTML = resp.datos.map(h => `
            <tr>
                <td>${escapeHTML(h.nombreusuario)}</td>
                <td>${escapeHTML(h.titulo)}</td>
                <td>${h.progreso}%</td>
                <td>${escapeHTML(h.fecha)}</td>
                <td>${escapeHTML(h.hora)}</td>
                <td class="acciones">
                    <button class="btn-edit" data-usuario="${h.idusuario}" data-contenido="${h.idcontenido}">Editar</button>
                    <button class="btn-delete" data-usuario="${h.idusuario}" data-contenido="${h.idcontenido}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        tabla.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => editarRegistro(btn.dataset.usuario, btn.dataset.contenido));
        });

        tabla.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => eliminarRegistro(btn.dataset.usuario, btn.dataset.contenido));
        });
    }

    async function editarRegistro(idUsuario, idContenido) {
        const resp = await peticionJSON(`api/historial.php?accion=obtener&idUsuario=${idUsuario}&idContenido=${idContenido}`);
        if (!resp || !resp.ok) {
            mostrarMensaje('mensaje', 'No se pudo cargar el registro.', 'error');
            return;
        }

        const h = resp.dato;
        document.getElementById('accion').value = 'editar';
        selectUsuario.value = h.idusuario;
        selectContenido.value = h.idcontenido;
        document.getElementById('progreso').value = h.progreso;

        // No se puede cambiar la combinación usuario/contenido (es la llave primaria),
        // pero se deja habilitado para que FormData lo envíe; se bloquea visualmente
        // con readOnly/estilo si se desea. Aquí lo dejamos seleccionado y deshabilitamos
        // las opciones restantes para evitar cambios accidentales.
        Array.from(selectUsuario.options).forEach(opt => {
            if (opt.value !== '' && opt.value !== String(h.idusuario)) opt.disabled = true;
        });
        Array.from(selectContenido.options).forEach(opt => {
            if (opt.value !== '' && opt.value !== String(h.idcontenido)) opt.disabled = true;
        });

        formTitulo.textContent = 'Editar progreso';
        btnSubmit.textContent = 'Guardar cambios';
        btnCancelar.style.display = 'inline-block';

        form.scrollIntoView({ behavior: 'smooth' });
    }

    async function eliminarRegistro(idUsuario, idContenido) {
        if (!confirm('¿Eliminar este registro de historial?')) return;

        const datos = new FormData();
        datos.append('accion', 'eliminar');
        datos.append('idUsuario', idUsuario);
        datos.append('idContenido', idContenido);

        const resp = await peticionJSON('api/historial.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            cargarHistorial();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    }
});
