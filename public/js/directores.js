document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-director');
    const tabla = document.getElementById('tabla-directores');
    const btnSubmit = document.getElementById('btn-submit');
    const btnCancelar = document.getElementById('btn-cancelar');
    const formTitulo = document.getElementById('form-titulo');

    cargarDirectores();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = new FormData(form);
        const resp = await peticionJSON('api/directores.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            resetFormulario();
            cargarDirectores();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    });

    btnCancelar.addEventListener('click', resetFormulario);

    function resetFormulario() {
        form.reset();
        document.getElementById('accion').value = 'crear';
        document.getElementById('idDirector').value = '';
        formTitulo.textContent = 'Nuevo director';
        btnSubmit.textContent = 'Crear director';
        btnCancelar.style.display = 'none';
    }

    async function cargarDirectores() {
        const resp = await peticionJSON('api/directores.php?accion=listar');
        if (!resp) return;

        if (!resp.ok) {
            tabla.innerHTML = `<tr><td colspan="5">Error al cargar directores.</td></tr>`;
            return;
        }

        if (resp.datos.length === 0) {
            tabla.innerHTML = `<tr><td colspan="5">No hay directores registrados.</td></tr>`;
            return;
        }

        tabla.innerHTML = resp.datos.map(d => `
            <tr>
                <td>${d.iddirector}</td>
                <td>${escapeHTML(d.nombre)}</td>
                <td>${escapeHTML(d.a_paterno)}</td>
                <td>${escapeHTML(d.a_materno)}</td>
                <td class="acciones">
                    <button class="btn-edit" data-id="${d.iddirector}">Editar</button>
                    <button class="btn-delete" data-id="${d.iddirector}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        tabla.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => editarDirector(btn.dataset.id));
        });

        tabla.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => eliminarDirector(btn.dataset.id));
        });
    }

    async function editarDirector(id) {
        const resp = await peticionJSON(`api/directores.php?accion=obtener&id=${id}`);
        if (!resp || !resp.ok) {
            mostrarMensaje('mensaje', 'No se pudo cargar el director.', 'error');
            return;
        }

        const d = resp.dato;
        document.getElementById('accion').value = 'editar';
        document.getElementById('idDirector').value = d.iddirector;
        document.getElementById('nombre').value = d.nombre || '';
        document.getElementById('paterno').value = d.a_paterno || '';
        document.getElementById('materno').value = d.a_materno || '';

        formTitulo.textContent = 'Editar director';
        btnSubmit.textContent = 'Guardar cambios';
        btnCancelar.style.display = 'inline-block';

        form.scrollIntoView({ behavior: 'smooth' });
    }

    async function eliminarDirector(id) {
        if (!confirm('¿Eliminar este director?')) return;

        const datos = new FormData();
        datos.append('accion', 'eliminar');
        datos.append('id', id);

        const resp = await peticionJSON('api/directores.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            cargarDirectores();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    }
});
