document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-usuario');
    const tabla = document.getElementById('tabla-usuarios');
    const btnSubmit = document.getElementById('btn-submit');
    const btnCancelar = document.getElementById('btn-cancelar');
    const formTitulo = document.getElementById('form-titulo');

    cargarUsuarios();

    // ----- Enviar formulario (alta o edición) -----
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = new FormData(form);
        const resp = await peticionJSON('api/usuarios.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            resetFormulario();
            cargarUsuarios();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    });

    // ----- Cancelar edición -----
    btnCancelar.addEventListener('click', resetFormulario);

    function resetFormulario() {
        form.reset();
        document.getElementById('accion').value = 'crear';
        document.getElementById('idUsuario').value = '';
        formTitulo.textContent = 'Nuevo usuario';
        btnSubmit.textContent = 'Crear usuario';
        btnCancelar.style.display = 'none';
    }

    // ----- Cargar listado -----
    async function cargarUsuarios() {
        const resp = await peticionJSON('api/usuarios.php?accion=listar');
        if (!resp) return;

        if (!resp.ok) {
            tabla.innerHTML = `<tr><td colspan="5">Error al cargar usuarios.</td></tr>`;
            return;
        }

        if (resp.datos.length === 0) {
            tabla.innerHTML = `<tr><td colspan="5">No hay usuarios registrados.</td></tr>`;
            return;
        }

        tabla.innerHTML = resp.datos.map(u => `
            <tr>
                <td>${u.idusuario}</td>
                <td>${escapeHTML(u.nombre)}</td>
                <td>${escapeHTML(u.a_paterno)}</td>
                <td>${escapeHTML(u.a_materno)}</td>
                <td class="acciones">
                    <button class="btn-edit" data-id="${u.idusuario}">Editar</button>
                    <button class="btn-delete" data-id="${u.idusuario}">Eliminar</button>
                </td>
            </tr>
        `).join('');

        // Botones "Editar"
        tabla.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => editarUsuario(btn.dataset.id));
        });

        // Botones "Eliminar"
        tabla.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => eliminarUsuario(btn.dataset.id));
        });
    }

    // ----- Editar -----
    async function editarUsuario(id) {
        const resp = await peticionJSON(`api/usuarios.php?accion=obtener&id=${id}`);
        if (!resp || !resp.ok) {
            mostrarMensaje('mensaje', 'No se pudo cargar el usuario.', 'error');
            return;
        }

        const u = resp.dato;
        document.getElementById('accion').value = 'editar';
        document.getElementById('idUsuario').value = u.idusuario;
        document.getElementById('nombre').value = u.nombre || '';
        document.getElementById('paterno').value = u.a_paterno || '';
        document.getElementById('materno').value = u.a_materno || '';

        formTitulo.textContent = 'Editar usuario';
        btnSubmit.textContent = 'Guardar cambios';
        btnCancelar.style.display = 'inline-block';

        form.scrollIntoView({ behavior: 'smooth' });
    }

    // ----- Eliminar -----
    async function eliminarUsuario(id) {
        if (!confirm('¿Eliminar este usuario y su historial asociado?')) return;

        const datos = new FormData();
        datos.append('accion', 'eliminar');
        datos.append('id', id);

        const resp = await peticionJSON('api/usuarios.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            cargarUsuarios();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    }
});
