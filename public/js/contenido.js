document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-contenido');
    const tabla = document.getElementById('tabla-contenido');
    const btnSubmit = document.getElementById('btn-submit');
    const btnCancelar = document.getElementById('btn-cancelar');
    const formTitulo = document.getElementById('form-titulo');
    const selectTipo = document.getElementById('idTipoContenido');
    const selectDirector = document.getElementById('idDirector');
    const avisoTipo = document.getElementById('aviso-tipo');
    const imagenActual = document.getElementById('imagen-actual');

    let tiposCargados = [];
    let directoresCargados = [];

    cargarOpciones().then(cargarContenido);

    // ----- Enviar formulario (alta o edición) -----
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = new FormData(form);
        const resp = await peticionJSON('api/contenido.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            resetFormulario();
            cargarContenido();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    });

    btnCancelar.addEventListener('click', resetFormulario);

    function resetFormulario() {
        form.reset();
        document.getElementById('accion').value = 'crear';
        document.getElementById('idContenido').value = '';
        formTitulo.textContent = 'Nuevo contenido';
        btnSubmit.textContent = 'Crear contenido';
        btnCancelar.style.display = 'none';
        Array.from(selectTipo.options).forEach(opt => opt.disabled = false);
        avisoTipo.style.display = 'none';
        imagenActual.innerHTML = '';
    }

    // ----- Cargar selects de tipo y director -----
    async function cargarOpciones() {
        const resp = await peticionJSON('api/contenido.php?accion=opciones');
        if (!resp || !resp.ok) return;

        tiposCargados = resp.tipos;
        directoresCargados = resp.directores;

        selectTipo.innerHTML = '<option value="">-- Selecciona un tipo --</option>' +
            tiposCargados.map(t => `<option value="${t.idtipocontenido}">${escapeHTML(t.tipo)}</option>`).join('');

        selectDirector.innerHTML = '<option value="">-- Sin director --</option>' +
            directoresCargados.map(d => `<option value="${d.iddirector}">${escapeHTML(d.nombre + ' ' + d.a_paterno)}</option>`).join('');
    }

    // ----- Cargar listado -----
    async function cargarContenido() {
        const resp = await peticionJSON('api/contenido.php?accion=listar');
        if (!resp) return;

        if (!resp.ok) {
            tabla.innerHTML = `<tr><td colspan="7">Error al cargar contenido.</td></tr>`;
            return;
        }

        if (resp.datos.length === 0) {
            tabla.innerHTML = `<tr><td colspan="7">No hay contenido registrado.</td></tr>`;
            return;
        }

        tabla.innerHTML = resp.datos.map(c => {
            const imagen = c.imagen || 'placeholder.svg';
            const director = (c.nombredirector || c.apellidodirector)
                ? `${c.nombredirector ?? ''} ${c.apellidodirector ?? ''}`.trim()
                : 'Sin asignar';

            return `
                <tr>
                    <td>${c.idcontenido}</td>
                    <td><img src="img/${escapeHTML(imagen)}" alt="" style="width:50px; height:75px; object-fit:cover; border-radius:4px;"></td>
                    <td>${escapeHTML(c.titulo)}</td>
                    <td>${escapeHTML(c.tipocontenido)}</td>
                    <td>${escapeHTML(c.duracion)}</td>
                    <td>${escapeHTML(director)}</td>
                    <td class="acciones">
                        <button class="btn-edit" data-id="${c.idcontenido}">Editar</button>
                        <button class="btn-delete" data-id="${c.idcontenido}">Eliminar</button>
                    </td>
                </tr>
            `;
        }).join('');

        tabla.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => editarContenido(btn.dataset.id));
        });

        tabla.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => eliminarContenido(btn.dataset.id));
        });
    }

    // ----- Editar -----
    async function editarContenido(id) {
        const resp = await peticionJSON(`api/contenido.php?accion=obtener&id=${id}`);
        if (!resp || !resp.ok) {
            mostrarMensaje('mensaje', 'No se pudo cargar el contenido.', 'error');
            return;
        }

        const c = resp.dato;
        document.getElementById('accion').value = 'editar';
        document.getElementById('idContenido').value = c.idcontenido;
        document.getElementById('titulo').value = c.titulo || '';
        document.getElementById('duracion').value = (c.duracion || '').substring(0, 8);
        document.getElementById('descripcion').value = c.descripcion || '';
        document.getElementById('trailer').value = c.trailerurl || '';
        selectDirector.value = c.iddirector || '';

        // El tipo no se puede cambiar una vez creado: se deshabilitan las
        // demás opciones pero se deja el valor actual seleccionado y enviable.
        selectTipo.value = c.idtipocontenido || '';
        Array.from(selectTipo.options).forEach(opt => {
            if (opt.value !== '' && opt.value !== String(c.idtipocontenido)) opt.disabled = true;
        });
        avisoTipo.style.display = 'block';

        // Mostrar imagen actual si existe
        if (c.imagen) {
            imagenActual.innerHTML = `
                <div style="margin-bottom:8px;">
                    <img src="img/${escapeHTML(c.imagen)}" alt="Portada actual" style="max-width:120px; border-radius:6px;">
                    <small style="display:block;">Imagen actual. Sube otra para reemplazarla.</small>
                </div>`;
        } else {
            imagenActual.innerHTML = '';
        }

        formTitulo.textContent = 'Editar contenido';
        btnSubmit.textContent = 'Guardar cambios';
        btnCancelar.style.display = 'inline-block';

        form.scrollIntoView({ behavior: 'smooth' });
    }

    // ----- Eliminar -----
    async function eliminarContenido(id) {
        if (!confirm('¿Eliminar este contenido y su historial asociado?')) return;

        const datos = new FormData();
        datos.append('accion', 'eliminar');
        datos.append('id', id);

        const resp = await peticionJSON('api/contenido.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            mostrarMensaje('mensaje', resp.mensaje, 'exito');
            cargarContenido();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    }
});
