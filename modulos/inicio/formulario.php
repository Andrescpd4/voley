<?php
// ============================================================
// INICIO — Muro social y landing principal de Voley+ (post-login)
//
// Reglas AGENTS.md (§10 y §11):
//   - Cero consultas SQL en la vista (los datos se piden por AJAX)
//   - Archivo unico auto-contenido (CSS <style> + HTML + JS <script>)
//   - JS con plantillas backticks y funciones pequenas por componente
//   - Cero concatenacion HTML con operador +
//   - Bucles for tradicionales, variables con var, if/else
//   - Cero PHP interpolado dentro de la logica JS
// ============================================================

// 1. Datos basicos de sesion para personalizacion estatica del hero
if (isset($_SESSION['usuario_rol'])) {
    $rol_usuario = intval($_SESSION['usuario_rol']);
} else {
    $rol_usuario = 0;
}

$es_administrador = ($rol_usuario === 1 || $rol_usuario === 4);
$es_acudiente = ($rol_usuario === 3);

if (isset($_SESSION['nombre_usuario']) && trim($_SESSION['nombre_usuario']) !== '') {
    $saludo_usuario = trim($_SESSION['nombre_usuario']);
} else {
    $saludo_usuario = 'Bienvenido';
}

// 2. Textos y enlaces del hero segun rol
if ($es_acudiente) {
    $texto_cta_principal = 'Registrar deportista';
    $enlace_cta_principal = WEB_ROOT . 'afiliacion';
    $texto_cta_secundario = 'Ver novedades';
    $enlace_cta_secundario = '#seccionMuro';
    $descripcion_hero = 'Afiliación de tus hijos y novedades del club en un solo lugar.';
} else if ($es_administrador) {
    $texto_cta_principal = 'Revisar afiliaciones';
    $enlace_cta_principal = WEB_ROOT . 'afiliacion';
    $texto_cta_secundario = 'Abrir dashboard';
    $enlace_cta_secundario = WEB_ROOT . 'dashboard';
    $descripcion_hero = 'Solicitudes por revisar y novedades del club.';
} else {
    $texto_cta_principal = 'Ver novedades';
    $enlace_cta_principal = '#seccionMuro';
    $texto_cta_secundario = '';
    $enlace_cta_secundario = '';
    $descripcion_hero = 'Novedades, avisos y eventos del club.';
}
?>

<!-- ============================================================ -->
<!-- ESTILOS CSS DEL MURO                                         -->
<!-- ============================================================ -->
<style type="text/css">
    /* Titulares con balanceo tipografico */
    .inicio-hero-titulo, .inicio-seccion-titulo {
        text-wrap: balance;
    }
    /* Ritmo vertical entre bloques */
    .inicio-bloque {
        margin-bottom: 24px;
    }
    /* Fila de acceso directo con divisor de lista */
    .inicio-acceso-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 4px;
        border-bottom: 1px solid var(--vz-border-color, #e9ebec);
    }
    .inicio-acceso-item:last-child {
        border-bottom: none;
    }
    /* Foco accesible */
    .inicio-bloque a:focus-visible,
    .inicio-bloque button:focus-visible,
    .inicio-bloque input:focus-visible,
    .inicio-bloque textarea:focus-visible,
    .inicio-bloque select:focus-visible {
        outline: 2px solid #405189;
        outline-offset: 2px;
    }
    /* Avatar circular del autor */
    .muro-avatar-autor {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #405189;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }
    /* Boton Me gusta activo */
    .muro-like-activo {
        background: #405189;
        border-color: #405189;
        color: #ffffff;
    }
</style>

<!-- ============================================================ -->
<!-- HERO: SALUDO Y CTA PRINCIPAL POR ROL                         -->
<!-- ============================================================ -->
<div class="row inicio-bloque">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge p-3" style="background: #405189;">
                        <i aria-hidden="true" class="ri-team-line fs-4 text-white"></i>
                    </span>
                    <div class="flex-grow-1" style="min-width: 220px;">
                        <h2 class="inicio-hero-titulo card-title mb-1 fs-4">Hola, <?php echo htmlspecialchars($saludo_usuario); ?></h2>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($descripcion_hero); ?></p>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex">
                        <a href="<?php echo $enlace_cta_principal; ?>" class="btn btn-primary">
                            <?php echo htmlspecialchars($texto_cta_principal); ?> <i aria-hidden="true" class="ri-arrow-right-line ms-1"></i>
                        </a>
                        <?php if ($texto_cta_secundario !== '') : ?>
                        <a href="<?php echo $enlace_cta_secundario; ?>" class="btn btn-outline-primary">
                            <?php echo htmlspecialchars($texto_cta_secundario); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ACCESOS DIRECTOS (se llenan por AJAX segun permisos)         -->
<!-- ============================================================ -->
<div class="row inicio-bloque d-none" id="bloqueAccesosDirectos">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="inicio-seccion-titulo card-title mb-1 fs-5">Accesos directos</h3>
                <p class="text-muted small mb-2">Tus módulos principales disponibles.</p>
                <div id="contenedorListaAccesos">
                    <!-- Se inyecta por JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- COMPOSER: PUBLICAR NOVEDAD (visible solo para admin)         -->
<!-- ============================================================ -->
<?php if ($es_administrador) : ?>
<div class="row inicio-bloque" id="bloqueComposerMuro">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="inicio-seccion-titulo card-title mb-1 fs-5">Publicar novedad</h3>
                <p class="text-muted small mb-3">Visible para todo el club según el destinatario que elijas.</p>
                <div class="mb-3">
                    <label class="form-label fw-medium" for="campoMuroTitulo">Título <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="campoMuroTitulo" maxlength="200" placeholder="Ej: Convocatoria torneo juvenil">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" for="campoMuroContenido">Contenido <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="campoMuroContenido" rows="3" placeholder="Escribe el aviso para el club…"></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium" for="selectorMuroDestino">Destinatario</label>
                        <select class="form-select" id="selectorMuroDestino" onchange="inicioCambioDestinatario()">
                            <option value="todos">Todo el club</option>
                            <option value="categoria">Una categoría</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-none" id="envolturaMuroCategoria">
                        <label class="form-label fw-medium" for="selectorMuroCategoria">Categoría</label>
                        <select class="form-select" id="selectorMuroCategoria">
                            <option value="0">Seleccione…</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="casillaMuroConfirmar">
                            <label class="form-check-label" for="casillaMuroConfirmar">Pedir confirmación de lectura</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary" id="botonMuroPublicar" onclick="inicioPublicarNovedad()">
                        <i aria-hidden="true" class="ri-send-plane-line me-1"></i> Publicar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ============================================================ -->
<!-- FEED DEL MURO DE NOVEDADES                                   -->
<!-- ============================================================ -->
<div class="row inicio-bloque" id="seccionMuro">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h3 class="inicio-seccion-titulo mb-1 fs-5">Novedades del club</h3>
                <p class="text-muted small mb-0" id="etiquetaEstadoMuro" role="status">Cargando publicaciones…</p>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="inicioCargarFeed()">
                <i aria-hidden="true" class="ri-refresh-line me-1"></i> Actualizar
            </button>
        </div>
        <div id="contenedorFeedMuro">
            <!-- Se inyecta por JavaScript -->
            <div class="text-center py-4 text-muted">
                <span class="spinner-border spinner-border-sm text-primary"></span>
                <span class="ms-2">Cargando publicaciones…</span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- PIE DE PAGINA                                                -->
<!-- ============================================================ -->
<div class="row">
    <div class="col-12 text-center">
        <p class="text-muted small mb-0">Voley+ · Sistema de gestión integral del club · <?php echo date('Y'); ?></p>
    </div>
</div>

<!-- ============================================================ -->
<!-- LOGICA JAVASCRIPT DEL MODULO (cumple AGENTS.md §10 y §11)    -->
<!-- ============================================================ -->
<script type="text/javascript">
// Variable global de rol admin manejada en memoria JS
var inicioEsAdmin = false;

// ============================================================
// FUNCION AJAX CON TOKEN
// ============================================================

// Peticion AJAX estandar hacia el backend del modulo inicio
function inicioAjax(nombre_accion, datos_formulario, funcion_retorno) {
    var peticion_http = new XMLHttpRequest();
    peticion_http.open('POST', page_root + nombre_accion, true);
    peticion_http.setRequestHeader('Authorization', TOKEN_GLOBAL);
    peticion_http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    peticion_http.onload = function() {
        if (peticion_http.status === 200) {
            try {
                var respuesta_servidor = JSON.parse(peticion_http.responseText);
                funcion_retorno(respuesta_servidor);
            } catch (error_parseo) {
                funcion_retorno({ error: true, msg: 'Respuesta no válida del servidor' });
            }
        } else {
            funcion_retorno({ error: true, msg: 'Error en el servidor (' + peticion_http.status + ')' });
        }
    };

    peticion_http.onerror = function() {
        funcion_retorno({ error: true, msg: 'Error de conexión con el servidor' });
    };

    var parametros_url = new URLSearchParams();
    for (var clave_campo in datos_formulario) {
        var valor_campo = datos_formulario[clave_campo];
        if (valor_campo !== null && valor_campo !== undefined) {
            parametros_url.append(clave_campo, valor_campo);
        }
    }
    peticion_http.send(parametros_url.toString());
}

// Saneamiento de cadenas contra ataques XSS
function inicioSanearTexto(texto_crudo) {
    if (!texto_crudo && texto_crudo !== 0) {
        return '';
    }
    return texto_crudo.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Convertir saltos de linea en etiquetas br
function inicioFormatearSaltos(texto_parrafo) {
    var texto_seguro = inicioSanearTexto(texto_parrafo);
    return texto_seguro.replace(/\n/g, '<br>');
}

// Actualizar texto de estado del muro
function inicioMostrarNotaEstado(mensaje_estado) {
    var elemento_nota = document.getElementById('etiquetaEstadoMuro');
    elemento_nota.textContent = mensaje_estado;
}

// ============================================================
// PLANTILLAS CON BACKTICKS PARA ACCESOS DIRECTOS
// ============================================================

// Plantilla para una sola fila de acceso directo
function inicioCrearFilaAcceso(acceso) {
    var ruta_modulo = web_root + inicioSanearTexto(acceso.url);
    var titulo_modulo = inicioSanearTexto(acceso.titulo);
    var descripcion_modulo = inicioSanearTexto(acceso.descripcion);
    var icono_modulo = inicioSanearTexto(acceso.icono);

    return `<a href="${ruta_modulo}" class="inicio-acceso-item text-reset text-decoration-none" aria-label="Ir a ${titulo_modulo}">
        <span class="badge bg-primary-subtle text-primary p-2">
            <i aria-hidden="true" class="${icono_modulo} fs-5"></i>
        </span>
        <span class="flex-grow-1">
            <span class="d-block fw-semibold">${titulo_modulo}</span>
            <span class="d-block text-muted small">${descripcion_modulo}</span>
        </span>
        <i aria-hidden="true" class="ri-arrow-right-s-line text-muted fs-5"></i>
    </a>`;
}

// Renderizar lista de accesos directos
function inicioRenderizarAccesos(lista_accesos) {
    var contenedor_accesos = document.getElementById('contenedorListaAccesos');
    var bloque_accesos = document.getElementById('bloqueAccesosDirectos');

    if (lista_accesos.length === 0) {
        bloque_accesos.classList.add('d-none');
        return;
    }

    var html_accesos = '';
    for (var i = 0; i < lista_accesos.length; i++) {
        var item_acceso = lista_accesos[i];
        html_accesos = html_accesos + inicioCrearFilaAcceso(item_acceso);
    }

    contenedor_accesos.innerHTML = html_accesos;
    bloque_accesos.classList.remove('d-none');
}

// ============================================================
// PLANTILLAS CON BACKTICKS PARA EL MURO DE NOVEDADES
// ============================================================

// Plantilla para una fila de comentario
function inicioCrearFilaComentario(comentario) {
    var nombre_autor = inicioSanearTexto(comentario.autor || '');
    var texto_comentario = inicioSanearTexto(comentario.comentario || '');

    return `<div class="border-top pt-2 mt-2">
        <div class="fw-semibold small">${nombre_autor}</div>
        <div class="small">${texto_comentario}</div>
    </div>`;
}

// Plantilla para insignia de destinatario
function inicioCrearInsigniaDestinatario(tipo_destinatario) {
    if (tipo_destinatario === 'categoria') {
        return `<span class="badge bg-info-subtle text-info ms-2">Categoría</span>`;
    }
    if (tipo_destinatario === 'individual') {
        return `<span class="badge bg-warning-subtle text-warning ms-2">Personal</span>`;
    }
    return '';
}

// Plantilla para boton o insignia de lectura
function inicioCrearControlLectura(id_publicacion, exige_confirmacion, leido_por_usuario) {
    if (exige_confirmacion !== 1) {
        return '';
    }
    if (leido_por_usuario) {
        return `<span class="badge bg-success-subtle text-success"><i aria-hidden="true" class="ri-check-double-line me-1"></i>Leído</span>`;
    }
    return `<button type="button" class="btn btn-sm btn-outline-success" id="botonLectura${id_publicacion}" onclick="inicioMarcarLectura(${id_publicacion})">
        <i aria-hidden="true" class="ri-check-line me-1"></i>Marcar leído
    </button>`;
}

// Plantilla para boton de eliminar (solo administradores)
function inicioCrearBotonEliminar(id_publicacion) {
    if (!inicioEsAdmin) {
        return '';
    }
    return `<button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="inicioEliminarPublicacion(${id_publicacion})" title="Eliminar publicación">
        <i aria-hidden="true" class="ri-delete-bin-line"></i>
    </button>`;
}

// Plantilla para una tarjeta completa de publicacion
function inicioCrearTarjetaPublicacion(publicacion) {
    var id_publicacion = parseInt(publicacion.id, 10) || 0;
    var titulo_publicacion = inicioSanearTexto(publicacion.titulo);
    var contenido_publicacion = inicioFormatearSaltos(publicacion.contenido);
    var autor_publicacion = inicioSanearTexto(publicacion.autor || 'Club Voley+');
    var fecha_publicacion = inicioSanearTexto(publicacion.fecha_publicacion || '');
    var total_likes = parseInt(publicacion.total_likes, 10) || 0;
    var total_comentarios = parseInt(publicacion.total_comentarios, 10) || 0;
    var confirmacion_lectura = parseInt(publicacion.confirmacion_lectura, 10) || 0;
    var leido_por_mi = publicacion.leido_por_mi;
    var me_gusta = publicacion.me_gusta;

    // Inicial del autor para el avatar
    var letra_inicial = 'V';
    if (autor_publicacion.length > 0) {
        letra_inicial = inicioSanearTexto(autor_publicacion.charAt(0).toUpperCase());
    }

    // Piezas secundarias delegadas a funciones pequenas
    var insignia_destino = inicioCrearInsigniaDestinatario(publicacion.destinatario_tipo);
    var control_lectura = inicioCrearControlLectura(id_publicacion, confirmacion_lectura, leido_por_mi);
    var boton_eliminar = inicioCrearBotonEliminar(id_publicacion);

    // Clase e icono del boton Me gusta
    var clase_boton_like = 'btn btn-sm btn-outline-primary';
    var icono_like = 'ri-thumb-up-line';
    if (me_gusta) {
        clase_boton_like = 'btn btn-sm muro-like-activo';
        icono_like = 'ri-thumb-up-fill';
    }

    // Comentarios previos
    var html_comentarios = '';
    var lista_comentarios = publicacion.comentarios || [];
    for (var c = 0; c < lista_comentarios.length; c++) {
        var item_comentario = lista_comentarios[c];
        html_comentarios = html_comentarios + inicioCrearFilaComentario(item_comentario);
    }
    if (html_comentarios === '' && total_comentarios > 0) {
        html_comentarios = '<div class="text-muted small">Hay comentarios anteriores.</div>';
    }

    return `<div class="card mb-3" id="tarjetaPublicacion${id_publicacion}">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="muro-avatar-autor" aria-hidden="true">${letra_inicial}</span>
                <div class="flex-grow-1">
                    <div class="fw-semibold">${autor_publicacion}${insignia_destino}</div>
                    <div class="text-muted small">${fecha_publicacion}</div>
                </div>
                ${boton_eliminar}
            </div>
            <h5 class="card-title fs-6">${titulo_publicacion}</h5>
            <p class="card-text">${contenido_publicacion}</p>
            <div class="d-flex align-items-center gap-2 flex-wrap pt-1">
                <button type="button" class="${clase_boton_like}" id="botonLike${id_publicacion}" onclick="inicioAlternarMeGusta(${id_publicacion})">
                    <i aria-hidden="true" class="${icono_like} me-1"></i><span id="contadorLikes${id_publicacion}">${total_likes}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="inicioAlternarCajaComentarios(${id_publicacion})">
                    <i aria-hidden="true" class="ri-chat-3-line me-1"></i><span id="contadorComentarios${id_publicacion}">${total_comentarios}</span>
                </button>
                ${control_lectura}
            </div>
            <div class="d-none mt-3" id="cajaComentarios${id_publicacion}">
                <div id="listaComentarios${id_publicacion}">${html_comentarios}</div>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" id="campoTextoComentario${id_publicacion}" maxlength="500" placeholder="Escribe un comentario… (máx 500)">
                    <button type="button" class="btn btn-primary" id="botonEnviarComentario${id_publicacion}" onclick="inicioGuardarComentario(${id_publicacion})">
                        <i aria-hidden="true" class="ri-send-plane-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>`;
}

// Renderizar lista completa de publicaciones en el DOM
function inicioRenderizarMuro(lista_publicaciones) {
    var contenedor_feed = document.getElementById('contenedorFeedMuro');

    if (lista_publicaciones.length === 0) {
        contenedor_feed.innerHTML = '<div class="alert alert-light border text-center mb-0"><i aria-hidden="true" class="ri-information-line me-1"></i>Aún no hay publicaciones del club.</div>';
        inicioMostrarNotaEstado('Sin publicaciones por ahora.');
        return;
    }

    if (lista_publicaciones.length === 1) {
        inicioMostrarNotaEstado('1 publicación.');
    } else {
        inicioMostrarNotaEstado(lista_publicaciones.length + ' publicaciones.');
    }

    var html_final_feed = '';
    for (var p = 0; p < lista_publicaciones.length; p++) {
        var item_pub = lista_publicaciones[p];
        html_final_feed = html_final_feed + inicioCrearTarjetaPublicacion(item_pub);
    }

    contenedor_feed.innerHTML = html_final_feed;
}

// ============================================================
// CARGA Y CONTROLADORES DE EVENTOS
// ============================================================

// 1. Cargar contexto del usuario (accesos y estado admin)
function inicioCargarContexto() {
    inicioAjax('contexto', {}, function(respuesta_contexto) {
        if (!respuesta_contexto.error && respuesta_contexto.data) {
            inicioEsAdmin = respuesta_contexto.data.es_admin;
            var lista_accesos = respuesta_contexto.data.accesos || [];
            inicioRenderizarAccesos(lista_accesos);
        }
        // Despues de tener el contexto, cargamos el feed
        inicioCargarFeed();
    });
}

// 2. Cargar feed del muro
function inicioCargarFeed() {
    var contenedor_feed = document.getElementById('contenedorFeedMuro');
    contenedor_feed.innerHTML = '<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Cargando publicaciones…</span></div>';
    inicioMostrarNotaEstado('Cargando publicaciones…');

    inicioAjax('feed', {}, function(respuesta_feed) {
        if (respuesta_feed.error) {
            contenedor_feed.innerHTML = '<div class="alert alert-light border text-center">No se pudieron cargar las publicaciones. <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="inicioCargarFeed()">Reintentar</button></div>';
            inicioMostrarNotaEstado('Sin conexión con el muro.');
            return;
        }
        inicioRenderizarMuro(respuesta_feed.data || []);
    });
}

// 3. Dar o quitar Me gusta
function inicioAlternarMeGusta(id_publicacion) {
    var boton_like = document.getElementById('botonLike' + id_publicacion);
    boton_like.disabled = true;

    inicioAjax('toggle_like', { comunicado_id: id_publicacion }, function(respuesta_like) {
        boton_like.disabled = false;
        if (respuesta_like.error) {
            inicioMostrarNotaEstado(respuesta_like.msg || 'No se pudo registrar el Me gusta.');
            return;
        }
        document.getElementById('contadorLikes' + id_publicacion).textContent = respuesta_like.data.total_likes;
        if (respuesta_like.data.me_gusta) {
            boton_like.className = 'btn btn-sm muro-like-activo';
        } else {
            boton_like.className = 'btn btn-sm btn-outline-primary';
        }
    });
}

// 4. Alternar visibilidad de la caja de comentarios
function inicioAlternarCajaComentarios(id_publicacion) {
    var caja_comentarios = document.getElementById('cajaComentarios' + id_publicacion);
    caja_comentarios.classList.toggle('d-none');
}

// 5. Guardar comentario nuevo
function inicioGuardarComentario(id_publicacion) {
    var campo_texto = document.getElementById('campoTextoComentario' + id_publicacion);
    var boton_enviar = document.getElementById('botonEnviarComentario' + id_publicacion);
    var texto_comentario = campo_texto.value.trim();

    if (texto_comentario === '') {
        inicioMostrarNotaEstado('Escribe un comentario primero.');
        campo_texto.focus();
        return;
    }
    if (texto_comentario.length > 500) {
        inicioMostrarNotaEstado('El comentario no puede pasar de 500 caracteres.');
        return;
    }

    boton_enviar.disabled = true;
    inicioAjax('comentar', { comunicado_id: id_publicacion, comentario: texto_comentario }, function(respuesta_comentario) {
        boton_enviar.disabled = false;
        if (respuesta_comentario.error) {
            inicioMostrarNotaEstado(respuesta_comentario.msg || 'No se pudo publicar el comentario.');
            return;
        }

        var lista_comentarios = document.getElementById('listaComentarios' + id_publicacion);
        var datos_nuevo = {
            autor: respuesta_comentario.data.autor || '',
            comentario: texto_comentario
        };
        lista_comentarios.insertAdjacentHTML('afterbegin', inicioCrearFilaComentario(datos_nuevo));
        document.getElementById('contadorComentarios' + id_publicacion).textContent = respuesta_comentario.data.total_comentarios;
        campo_texto.value = '';
        inicioMostrarNotaEstado('Comentario publicado.');
    });
}

// 6. Confirmar lectura
function inicioMarcarLectura(id_publicacion) {
    var boton_lectura = document.getElementById('botonLectura' + id_publicacion);
    boton_lectura.disabled = true;

    inicioAjax('marcar_leido', { comunicado_id: id_publicacion }, function(respuesta_lectura) {
        if (respuesta_lectura.error) {
            boton_lectura.disabled = false;
            inicioMostrarNotaEstado(respuesta_lectura.msg || 'No se pudo confirmar la lectura.');
            return;
        }
        boton_lectura.outerHTML = '<span class="badge bg-success-subtle text-success"><i aria-hidden="true" class="ri-check-double-line me-1"></i>Leído</span>';
        inicioMostrarNotaEstado('Lectura confirmada.');
    });
}

// 7. Eliminar publicacion (admin)
function inicioEliminarPublicacion(id_publicacion) {
    if (!confirm('¿Eliminar esta publicación con sus likes y comentarios?')) {
        return;
    }

    inicioAjax('eliminar_publicacion', { comunicado_id: id_publicacion }, function(respuesta_eliminar) {
        if (respuesta_eliminar.error) {
            inicioMostrarNotaEstado(respuesta_eliminar.msg || 'No se pudo eliminar.');
            return;
        }
        var tarjeta_publicacion = document.getElementById('tarjetaPublicacion' + id_publicacion);
        if (tarjeta_publicacion) {
            tarjeta_publicacion.remove();
        }
        inicioMostrarNotaEstado('Publicación eliminada.');
    });
}

// 8. Selector de tipo de destinatario en el composer
function inicioCambioDestinatario() {
    var tipo_elegido = document.getElementById('selectorMuroDestino').value;
    var envoltura_categoria = document.getElementById('envolturaMuroCategoria');
    if (tipo_elegido === 'categoria') {
        envoltura_categoria.classList.remove('d-none');
        inicioCargarCategoriasComposer();
    } else {
        envoltura_categoria.classList.add('d-none');
    }
}

// 9. Cargar categorias para el composer
function inicioCargarCategoriasComposer() {
    var selector_categoria = document.getElementById('selectorMuroCategoria');
    if (selector_categoria.options.length > 1) {
        return;
    }
    inicioAjax('categorias', {}, function(respuesta_categorias) {
        if (respuesta_categorias.error || !respuesta_categorias.data) {
            return;
        }
        var lista_categorias = respuesta_categorias.data;
        for (var k = 0; k < lista_categorias.length; k++) {
            var item_cat = lista_categorias[k];
            var opcion_nueva = document.createElement('option');
            opcion_nueva.value = item_cat.id;
            opcion_nueva.textContent = item_cat.nombre;
            selector_categoria.appendChild(opcion_nueva);
        }
    });
}

// 10. Publicar aviso nuevo (admin)
function inicioPublicarNovedad() {
    var campo_titulo = document.getElementById('campoMuroTitulo');
    var campo_contenido = document.getElementById('campoMuroContenido');
    var selector_destino = document.getElementById('selectorMuroDestino');
    var boton_publicar = document.getElementById('botonMuroPublicar');

    var texto_titulo = campo_titulo.value.trim();
    var texto_contenido = campo_contenido.value.trim();
    var tipo_destino = selector_destino.value;

    if (texto_titulo === '' || texto_contenido === '') {
        inicioMostrarNotaEstado('Título y contenido son obligatorios.');
        return;
    }

    var datos_envio = {
        titulo: texto_titulo,
        contenido: texto_contenido,
        destinatario_tipo: tipo_destino,
        destinatario_id: 0,
        confirmacion_lectura: 0
    };

    if (tipo_destino === 'categoria') {
        var id_categoria = parseInt(document.getElementById('selectorMuroCategoria').value, 10) || 0;
        if (id_categoria <= 0) {
            inicioMostrarNotaEstado('Elige una categoría destino.');
            return;
        }
        datos_envio.destinatario_id = id_categoria;
    }

    if (document.getElementById('casillaMuroConfirmar').checked) {
        datos_envio.confirmacion_lectura = 1;
    }

    boton_publicar.disabled = true;
    inicioAjax('publicar', datos_envio, function(respuesta_publicar) {
        boton_publicar.disabled = false;
        if (respuesta_publicar.error) {
            inicioMostrarNotaEstado(respuesta_publicar.msg || 'No se pudo publicar.');
            return;
        }
        campo_titulo.value = '';
        campo_contenido.value = '';
        document.getElementById('casillaMuroConfirmar').checked = false;
        inicioMostrarNotaEstado('Publicación creada.');
        inicioCargarFeed();
    });
}

// Inicializar al cargar el documento
jQuery(document).ready(function() {
    inicioCargarContexto();
});
</script>
