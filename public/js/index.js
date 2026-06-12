document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-trailer');
    const iframe = document.getElementById('iframe-trailer');
    const modalTitulo = document.getElementById('modal-titulo');
    const btnCerrar = document.getElementById('cerrar-modal');
    const contenedorMiLista = document.getElementById('contenedor-milista');
    const contenedorContinuar = document.getElementById('contenedor-continuar');
    const buscador = document.getElementById('buscador');

    let idsFavoritos = new Set();

    // ----- Reproducir tráiler -----
    function engancharBotonesPlay(contenedor) {
        contenedor.querySelectorAll('.card').forEach(card => {
            const trailer = card.dataset.trailer;
            const titulo = card.dataset.titulo;
            const id = card.dataset.id;
            const btnPlay = card.querySelector('.btn-play');

            if (btnPlay && trailer) {
                btnPlay.addEventListener('click', () => {
                    abrirTrailer(trailer, titulo);
                    registrarContinuarViendo(id);
                });
            }
        });
    }

    engancharBotonesPlay(document);

    // ----- Botones del banner principal (hero) -----
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        const trailer = heroSection.dataset.trailer;
        const titulo = heroSection.dataset.titulo;
        const id = heroSection.dataset.id;

        const btnPlayHero = heroSection.querySelector('.btn-play-hero');
        if (btnPlayHero && trailer) {
            btnPlayHero.addEventListener('click', () => {
                abrirTrailer(trailer, titulo);
                registrarContinuarViendo(id);
            });
        }
    }

    function abrirTrailer(url, titulo) {
        modalTitulo.textContent = titulo;
        iframe.src = url + (url.includes('?') ? '&' : '?') + 'autoplay=1';
        modal.style.display = 'flex';
    }

    function cerrarModal() {
        modal.style.display = 'none';
        iframe.src = '';
    }

    btnCerrar.addEventListener('click', cerrarModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) cerrarModal();
    });

    // ----- Continuar viendo -----
    async function registrarContinuarViendo(idContenido) {
        const datos = new FormData();
        datos.append('accion', 'registrar');
        datos.append('idContenido', idContenido);

        await peticionJSON('api/continuarviendo.php', {
            method: 'POST',
            body: datos
        });

        cargarContinuarViendo();
    }

    async function cargarContinuarViendo() {
        const resp = await peticionJSON('api/continuarviendo.php?accion=listar');
        if (!resp) return;

        if (!resp.ok) {
            contenedorContinuar.innerHTML = '<p>Error al cargar.</p>';
            return;
        }

        if (resp.datos.length === 0) {
            contenedorContinuar.innerHTML = '<p>Aún no has empezado a ver nada.</p>';
            return;
        }

        contenedorContinuar.innerHTML = resp.datos.map(c => crearCardHTML(c)).join('');
        engancharBotonesPlay(contenedorContinuar);
        engancharBotonesFavorito(contenedorContinuar);
    }

    // ----- Construcción de cards (usado por Mi Lista y Continuar viendo) -----
    function crearCardHTML(c) {
        const imagen = c.imagen || 'placeholder.svg';
        const trailer = c.trailerurl || '';
        const titulo = escapeHTML(c.titulo);
        const favoritoTexto = idsFavoritos.has(String(c.idcontenido)) ? '✓ En Mi Lista' : '+ Mi Lista';
        const favoritoClase = idsFavoritos.has(String(c.idcontenido)) ? 'btn-favorito activo' : 'btn-favorito';

        return `
            <div class="card" data-id="${c.idcontenido}" data-trailer="${escapeHTML(trailer)}" data-titulo="${titulo}">
                <img src="img/${escapeHTML(imagen)}" alt="${titulo}" class="poster">
                <div style="padding:15px;">
                    <h3>${titulo}</h3>
                    <p>Tipo: ${escapeHTML(c.tipocontenido)}</p>
                    <p>Duración: ${escapeHTML(c.duracion)}</p>
                    <div class="card-acciones">
                        ${trailer ? '<button class="btn btn-play" type="button">▶ Reproducir</button>' : ''}
                        <button class="btn ${favoritoClase}" type="button" data-id="${c.idcontenido}">${favoritoTexto}</button>
                    </div>
                </div>
            </div>
        `;
    }

    // ----- Mi Lista (favoritos) -----
    cargarFavoritos();

    async function cargarFavoritos() {
        const resp = await peticionJSON('api/milista.php?accion=ids');
        if (resp && resp.ok) {
            idsFavoritos = new Set(resp.ids.map(String));
        }
        actualizarBotonesFavorito();
        cargarMiLista();
        cargarContinuarViendo();
    }

    function actualizarBotonesFavorito() {
        document.querySelectorAll('.btn-favorito').forEach(btn => {
            const id = btn.dataset.id;
            if (idsFavoritos.has(id)) {
                btn.textContent = '✓ En Mi Lista';
                btn.classList.add('activo');
            } else {
                btn.textContent = '+ Mi Lista';
                btn.classList.remove('activo');
            }
        });
    }

    function engancharBotonesFavorito(contenedor) {
        contenedor.querySelectorAll('.btn-favorito').forEach(btn => {
            btn.addEventListener('click', () => alternarFavorito(btn));
        });
    }

    async function alternarFavorito(btn) {
        const id = btn.dataset.id;
        const enLista = idsFavoritos.has(id);

        const datos = new FormData();
        datos.append('accion', enLista ? 'quitar' : 'agregar');
        datos.append('idContenido', id);

        const resp = await peticionJSON('api/milista.php', {
            method: 'POST',
            body: datos
        });

        if (!resp) return;

        if (resp.ok) {
            if (enLista) {
                idsFavoritos.delete(id);
            } else {
                idsFavoritos.add(id);
            }
            actualizarBotonesFavorito();
            cargarMiLista();
        } else {
            mostrarMensaje('mensaje', resp.error, 'error');
        }
    }

    engancharBotonesFavorito(document);

    // ----- Sección "Mi Lista" -----
    async function cargarMiLista() {
        const resp = await peticionJSON('api/milista.php?accion=listar');
        if (!resp) return;

        if (!resp.ok) {
            contenedorMiLista.innerHTML = '<p>Error al cargar Mi Lista.</p>';
            return;
        }

        if (resp.datos.length === 0) {
            contenedorMiLista.innerHTML = '<p>Aún no has agregado nada a tu lista.</p>';
            return;
        }

        contenedorMiLista.innerHTML = resp.datos.map(c => crearCardHTML(c)).join('');
        engancharBotonesPlay(contenedorMiLista);
        engancharBotonesFavorito(contenedorMiLista);
    }

    // ----- Menú de usuario -----
    const btnUsuario = document.getElementById('btn-usuario');
    const dropdownUsuario = document.getElementById('dropdown-usuario');

    if (btnUsuario && dropdownUsuario) {
        btnUsuario.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownUsuario.classList.toggle('abierto');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownUsuario.contains(e.target) && e.target !== btnUsuario) {
                dropdownUsuario.classList.remove('abierto');
            }
        });
    }

});
