<!-- ============================================================
  INICIO — Landing principal de Voley+ (post-login)

  Estructura (ver PRODUCT.md, register = brand):
    - Hero con saludo por rol + CTA principal
    - Accesos directos por audiencia (lista, no card-grid)
    - Tira de 4 estadisticas con datos reales (dashboard())
    - Pie de pagina sobrio
============================================================ -->

<?php
// 1. Datos de sesion para personalizar por rol
if (isset($_SESSION['usuario_rol'])) {
    $rol_actual = intval($_SESSION['usuario_rol']);
} else {
    $rol_actual = 0;
}
$es_admin = ($rol_actual === 1 || $rol_actual === 4);
$es_entrenador = ($rol_actual === 2);
$es_acudiente = ($rol_actual === 3);

if (isset($_SESSION['nombre_usuario']) && trim($_SESSION['nombre_usuario']) !== '') {
    $nombre_saludo = trim($_SESSION['nombre_usuario']);
} else {
    $nombre_saludo = 'Bienvenido';
}

// 2. CTA principal del hero segun rol (un camino por audiencia)
if ($es_acudiente) {
    $cta_texto = 'Registrar deportista';
    $cta_url = WEB_ROOT . 'afiliacion';
    $cta_secundario_texto = 'Ver comunicados';
    $cta_secundario_url = WEB_ROOT . 'comunicados';
    $hero_descripcion = 'Afiliación de tus hijos, documentos y estado de tus solicitudes en un solo lugar.';
} elseif ($es_entrenador) {
    $cta_texto = 'Tomar asistencia';
    $cta_url = WEB_ROOT . 'asistencia';
    $cta_secundario_texto = 'Ver eventos';
    $cta_secundario_url = WEB_ROOT . 'eventos';
    $hero_descripcion = 'Asistencia por clase y categoría, eventos y convocatorias de tus grupos.';
} else {
    $cta_texto = 'Revisar afiliaciones';
    $cta_url = WEB_ROOT . 'afiliacion';
    $cta_secundario_texto = 'Ver deportistas';
    $cta_secundario_url = WEB_ROOT . 'deportistas';
    $hero_descripcion = 'Solicitudes por revisar, documentos pendientes y estado general del club.';
}
?>

<style type="text/css">
    /* Titulares con balanceo para evitar huerfanos */
    .inicio-hero-titulo, .inicio-seccion-titulo {
        text-wrap: balance;
    }
    /* Espaciado generoso entre bloques (ritmo vertical) */
    .inicio-bloque {
        margin-bottom: 24px;
    }
    /* Fila de acceso con divisor: lista, no grilla de cards */
    .inicio-acceso {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 4px;
        border-bottom: 1px solid var(--vz-border-color, #e9ebec);
    }
    .inicio-acceso:last-child {
        border-bottom: none;
    }
    /* Cifras con ancho tabular para que no vibren al animar */
    .inicio-stat-numero {
        font-variant-numeric: tabular-nums;
    }
    /* Foco visible con el primario del club */
    .inicio-bloque a:focus-visible,
    .inicio-bloque button:focus-visible {
        outline: 2px solid #405189;
        outline-offset: 2px;
    }
</style>

<!-- ============ HERO: saludo + CTA por rol ============ -->
<div class="row inicio-bloque">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge p-3" style="background: #405189;">
                        <i aria-hidden="true" class="ri-team-line fs-4 text-white"></i>
                    </span>
                    <div class="flex-grow-1" style="min-width: 220px;">
                        <h2 class="inicio-hero-titulo card-title mb-1 fs-4">Hola, <?php echo htmlspecialchars($nombre_saludo); ?></h2>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($hero_descripcion); ?></p>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex">
                        <a href="<?php echo $cta_url; ?>" class="btn btn-primary">
                            <?php echo htmlspecialchars($cta_texto); ?> <i aria-hidden="true" class="ri-arrow-right-line ms-1"></i>
                        </a>
                        <a href="<?php echo $cta_secundario_url; ?>" class="btn btn-outline-primary">
                            <?php echo htmlspecialchars($cta_secundario_texto); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ ACCESOS DIRECTOS por audiencia ============ -->
<div class="row inicio-bloque">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="inicio-seccion-titulo card-title mb-1 fs-5">Accesos directos</h3>
                <p class="text-muted small mb-2">Tus módulos principales.</p>
                <div>
                    <?php if ($es_acudiente || $es_admin) : ?>
                    <a href="<?php echo WEB_ROOT; ?>afiliacion" class="inicio-acceso text-reset text-decoration-none" aria-label="Ir a Afiliación">
                        <span class="badge bg-primary-subtle text-primary p-2">
                            <i aria-hidden="true" class="ri-user-add-line fs-5"></i>
                        </span>
                        <span class="flex-grow-1">
                            <span class="d-block fw-semibold">Afiliación</span>
                            <span class="d-block text-muted small">Registro de deportistas, documentos y autorizaciones.</span>
                        </span>
                        <i aria-hidden="true" class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($es_entrenador || $es_admin) : ?>
                    <a href="<?php echo WEB_ROOT; ?>asistencia" class="inicio-acceso text-reset text-decoration-none" aria-label="Ir a Asistencia">
                        <span class="badge bg-primary-subtle text-primary p-2">
                            <i aria-hidden="true" class="ri-calendar-check-line fs-5"></i>
                        </span>
                        <span class="flex-grow-1">
                            <span class="d-block fw-semibold">Asistencia</span>
                            <span class="d-block text-muted small">Control de asistencia por clase y categoría.</span>
                        </span>
                        <i aria-hidden="true" class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($es_entrenador || $es_admin || $es_acudiente) : ?>
                    <a href="<?php echo WEB_ROOT; ?>eventos" class="inicio-acceso text-reset text-decoration-none" aria-label="Ir a Eventos">
                        <span class="badge bg-primary-subtle text-primary p-2">
                            <i aria-hidden="true" class="ri-trophy-line fs-5"></i>
                        </span>
                        <span class="flex-grow-1">
                            <span class="d-block fw-semibold">Eventos</span>
                            <span class="d-block text-muted small">Torneos, salidas y convocatorias.</span>
                        </span>
                        <i aria-hidden="true" class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo WEB_ROOT; ?>comunicados" class="inicio-acceso text-reset text-decoration-none" aria-label="Ir a Comunicados">
                        <span class="badge bg-primary-subtle text-primary p-2">
                            <i aria-hidden="true" class="ri-broadcast-line fs-5"></i>
                        </span>
                        <span class="flex-grow-1">
                            <span class="d-block fw-semibold">Comunicados</span>
                            <span class="d-block text-muted small">Avisos e información oficial del club.</span>
                        </span>
                        <i aria-hidden="true" class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ ESTADISTICAS con datos reales ============ -->
<div class="row inicio-bloque">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h3 class="inicio-seccion-titulo card-title mb-1 fs-5">Estado del club</h3>
                        <p class="text-muted small mb-0" id="inicioStatsNota" role="status">Cargando cifras actuales…</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary d-none" id="inicioStatsReintentar" onclick="inicioCargarStats()">
                        <i aria-hidden="true" class="ri-refresh-line me-1"></i> Reintentar
                    </button>
                </div>
                <div class="row g-3 text-center">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 border rounded h-100">
                            <i aria-hidden="true" class="ri-team-line fs-3" style="color: #405189;"></i>
                            <h3 class="mb-0 mt-2 inicio-stat-numero" id="inicioStatDeportistas">–</h3>
                            <p class="text-muted small mb-0">Deportistas activos</p>
                            <small class="text-muted d-none" id="inicioAyudaDeportistas">Aún no hay registros.</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 border rounded h-100">
                            <i aria-hidden="true" class="ri-file-list-3-line fs-3 text-warning"></i>
                            <h3 class="mb-0 mt-2 inicio-stat-numero" id="inicioStatSolicitudes">–</h3>
                            <p class="text-muted small mb-0">Solicitudes pendientes</p>
                            <small class="text-muted d-none" id="inicioAyudaSolicitudes">Sin solicitudes por revisar.</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 border rounded h-100">
                            <i aria-hidden="true" class="ri-folder-shield-line fs-3 text-info"></i>
                            <h3 class="mb-0 mt-2 inicio-stat-numero" id="inicioStatDocumentos">–</h3>
                            <p class="text-muted small mb-0">Documentos por revisar</p>
                            <small class="text-muted d-none" id="inicioAyudaDocumentos">Sin documentos pendientes.</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 border rounded h-100">
                            <i aria-hidden="true" class="ri-trophy-line fs-3 text-success"></i>
                            <h3 class="mb-0 mt-2 inicio-stat-numero" id="inicioStatEventos">–</h3>
                            <p class="text-muted small mb-0">Próximos eventos</p>
                            <small class="text-muted d-none" id="inicioAyudaEventos">No hay eventos programados.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============ PIE sobrio ============ -->
<div class="row">
    <div class="col-12 text-center">
        <p class="text-muted small mb-0">Voley+ · Sistema de gestión integral del club · <?php echo date('Y'); ?></p>
    </div>
</div>

<script type="text/javascript">
// Cargar las 4 cifras desde la accion dashboard() del modulo
function inicioCargarStats() {
    // Ocultar el boton de reintentar mientras se consulta
    document.getElementById('inicioStatsReintentar').classList.add('d-none');
    document.getElementById('inicioStatsNota').textContent = 'Cargando cifras actuales…';

    $.ajax({
        url: page_root + 'dashboard',
        type: 'POST',
        dataType: 'json',
        data: {}
    })
    .done(function(respuesta) {
        // Si el backend responde error, mostrar guiones sin expulsar al usuario
        if (!respuesta || respuesta.error) {
            inicioMostrarErrorStats();
            return;
        }
        document.getElementById('inicioStatsNota').textContent = 'Cifras actualizadas.';
        inicioAnimarStats(respuesta.data || {});
    })
    .fail(function() {
        // Sin alertas en bucle ni redirecciones: solo estado de error local
        inicioMostrarErrorStats();
    });
}

// Estado de error: guiones + boton de reintentar
function inicioMostrarErrorStats() {
    var ids = ['inicioStatDeportistas', 'inicioStatSolicitudes', 'inicioStatDocumentos', 'inicioStatEventos'];
    for (var i = 0; i < ids.length; i++) {
        document.getElementById(ids[i]).textContent = '–';
    }
    document.getElementById('inicioStatsNota').textContent = 'No se pudieron cargar las cifras.';
    document.getElementById('inicioStatsReintentar').classList.remove('d-none');
}

// Pintar cada cifra con conteo animado (o directo si no hay movimiento)
function inicioAnimarStats(datos) {
    var pares = [
        { numero: 'inicioStatDeportistas', ayuda: 'inicioAyudaDeportistas', valor: datos.total_deportistas },
        { numero: 'inicioStatSolicitudes', ayuda: 'inicioAyudaSolicitudes', valor: datos.nuevas_solicitudes },
        { numero: 'inicioStatDocumentos', ayuda: 'inicioAyudaDocumentos', valor: datos.documentacion_pendiente },
        { numero: 'inicioStatEventos', ayuda: 'inicioAyudaEventos', valor: datos.proximos_total }
    ];

    var sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    for (var i = 0; i < pares.length; i++) {
        inicioPintarCifra(pares[i].numero, pares[i].ayuda, pares[i].valor, sinMovimiento);
    }
}

// Pintar una cifra: anima de 0 al valor o la deja fija si es 0 o sin movimiento
function inicioPintarCifra(idNumero, idAyuda, valorCrudo, sinMovimiento) {
    var valor = parseInt(valorCrudo, 10);
    if (isNaN(valor) || valor < 0) {
        valor = 0;
    }

    var elNumero = document.getElementById(idNumero);
    var elAyuda = document.getElementById(idAyuda);

    // Estado vacio: mostrar 0 con mensaje de ayuda
    if (valor === 0) {
        elNumero.textContent = '0';
        elAyuda.classList.remove('d-none');
        return;
    }
    elAyuda.classList.add('d-none');

    // Sin animacion para quienes prefieren movimiento reducido
    if (sinMovimiento) {
        elNumero.textContent = valor;
        return;
    }

    // Conteo animado simple en ~800ms
    var pasos = 20;
    var paso = 0;
    var intervalo = setInterval(function() {
        paso = paso + 1;
        var parcial = Math.round(valor * paso / pasos);
        elNumero.textContent = parcial;
        if (paso >= pasos) {
            clearInterval(intervalo);
            elNumero.textContent = valor;
        }
    }, 40);
}

jQuery(document).ready(function() {
    inicioCargarStats();
});
</script>
