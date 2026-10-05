<?php
// afiliacion/tabs/mis_solicitudes.php - Tab 2: Mis Solicitudes / Historial de Afiliaciones
// Visible para Acudientes (Rol 3)

$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_acudiente = ($usuario_rol === 3);
$es_admin = ($usuario_rol === 1 || $usuario_rol === 4);

if (!$es_acudiente && !$es_admin) {
    echo '<div class="alert alert-warning">No tiene permisos para acceder a esta seccion.</div>';
    return;
}
?>

<!-- ===== TAB 2: HISTORIAL DE SOLICITUDES DEL ACUDIENTE ===== -->
<!-- Aviso cuando el club devolvio solicitudes para corregir -->
<div class="alert alert-warning d-none" id="avisoSolicitudesDevueltas" role="status"></div>

<div class="card border">
    <div class="card-header afili-encabezado d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            <i class="ri-history-line me-1"></i> Mis Solicitudes de Afiliacion
        </h6>
        <button type="button" class="btn btn-sm btn-light btn-afili-accion" onclick="afiliacionMisSolicitudesCargarDatos()" aria-label="Actualizar lista de solicitudes">
            <i class="ri-refresh-line me-1"></i> Actualizar
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" id="tablaMisSolicitudes">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nombre del Deportista</th>
                        <th>Estado Actual</th>
                        <th>Progreso</th>
                        <th>Fecha de Solicitud</th>
                        <th>Observaciones del Club</th>
                        <th style="width: 180px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyMisSolicitudes" aria-live="polite">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="ms-2">Cargando sus solicitudes...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: VER DETALLE DE MI SOLICITUD                           -->
<!-- ============================================================ -->
<div class="modal fade" id="modalVerMiSolicitud" tabindex="-1" aria-labelledby="modalVerMiSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header afili-encabezado">
                <h5 class="modal-title" id="modalVerMiSolicitudLabel">
                    <i class="ri-file-user-line me-1"></i> Ficha de Solicitud de Afiliacion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoVerMiSolicitud">
                <div class="text-center py-4">
                    <span class="spinner-border spinner-border-sm text-primary"></span>
                    <span class="ms-2">Cargando ficha...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT TAB MIS SOLICITUDES                               -->
<!-- ============================================================ -->
<script type="text/javascript">
var modalVerMiSolInstancia = null;

jQuery(document).ready(function($) {
    var modalElemento = document.getElementById('modalVerMiSolicitud');
    if (modalElemento) {
        modalVerMiSolInstancia = bootstrap.Modal.getOrCreateInstance(modalElemento);
    }

    // Cargar datos al inicio
    afiliacionMisSolicitudesCargarDatos();
});

// Cargar la lista de solicitudes desde el backend
function afiliacionMisSolicitudesCargarDatos() {
    var tbody = document.getElementById('tbodyMisSolicitudes');
    if (!tbody) {
        return;
    }

    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Cargando solicitudes...</span></td></tr>';

    afiliacionAjax('listar_mis_solicitudes', {}, function(respuesta) {
        if (!respuesta.error && respuesta.rows) {
            var lista = respuesta.rows;
            if (lista.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="ri-information-line fs-3 d-block mb-1"></i>Aun no ha registrado solicitudes de afiliacion. Puede hacerlo en la pestana <strong>Registro</strong>.</td></tr>';
                return;
            }

            var html = '';
            var totalDevueltas = 0;
            for (var i = 0; i < lista.length; i++) {
                html += afiliacionCrearFilaMiSolicitud(lista[i]);
                if (lista[i].estado === 'requiere_info') {
                    totalDevueltas = totalDevueltas + 1;
                }
            }
            tbody.innerHTML = html;
            afiliacionMostrarAvisoDevueltas(totalDevueltas);
        }
    });
}

// Mostrar u ocultar el aviso de solicitudes devueltas por el club
function afiliacionMostrarAvisoDevueltas(total) {
    var aviso = document.getElementById('avisoSolicitudesDevueltas');
    if (!aviso) {
        return;
    }
    if (total > 0) {
        aviso.innerHTML = '<i class="ri-error-warning-line me-1"></i> Tiene <strong>' + total + '</strong> solicitud(es) devuelta(s) por el club. Use el boton <strong>Corregir</strong> para editar los datos o documentos y reenviarla.';
        aviso.classList.remove('d-none');
    } else {
        aviso.innerHTML = '';
        aviso.classList.add('d-none');
    }
}

// Crear una fila de la tabla de solicitudes con backticks
function afiliacionCrearFilaMiSolicitud(item) {
    var num = item._NUM_;
    var nombre = afiliacionEsc(item.deportista_nombre);
    var badgeEstado = afiliacionBadgeEstado(item.estado);
    var barraProgreso = afiliacionBarraProgreso(item.porcentaje_completado);
    var fecha = afiliacionEsc(afiliacionTexto(item.fecha_solicitud, afiliacionTexto(item.created_at, '-')));
    var obs = afiliacionEsc(afiliacionTexto(item.observaciones_revision, 'Sin observaciones'));
    var depId = parseInt(item.deportista_id) || 0;

    // Las solicitudes en borrador o devueltas se pueden abrir de nuevo en el formulario
    var claseFila = '';
    var botonEditar = '';
    if (item.estado === 'requiere_info') {
        claseFila = 'table-warning';
        botonEditar = `<button type="button" class="btn btn-sm btn-warning btn-afili-accion" onclick="afiliacionCargarSolicitudEnFormulario(${depId})" title="Corregir y reenviar" aria-label="Corregir solicitud de ${nombre}"><i class="ri-edit-line me-1"></i>Corregir</button>`;
    } else if (item.estado === 'borrador') {
        botonEditar = `<button type="button" class="btn btn-sm btn-outline-primary btn-afili-accion" onclick="afiliacionCargarSolicitudEnFormulario(${depId})" title="Continuar borrador" aria-label="Continuar borrador de ${nombre}"><i class="ri-edit-line me-1"></i>Continuar</button>`;
    }

    return `
    <tr class="${claseFila}">
        <td><strong>${num}</strong></td>
        <td class="fw-medium">${nombre}</td>
        <td>${badgeEstado}</td>
        <td style="min-width: 120px;">${barraProgreso}</td>
        <td>${fecha}</td>
        <td><small class="text-muted">${obs}</small></td>
        <td class="text-center">
            <div class="d-flex justify-content-center flex-wrap gap-1">
                <button type="button" class="btn btn-sm btn-outline-info btn-afili-accion" onclick="afiliacionVerMiDetalle(${depId})" title="Ver ficha completa" aria-label="Ver ficha completa de ${nombre}">
                    <i class="ri-eye-line"></i> Ver
                </button>
                ${botonEditar}
            </div>
        </td>
    </tr>`;
}

// Ver detalle completo de una solicitud en modal
function afiliacionVerMiDetalle(deportistaId) {
    var contenedor = document.getElementById('contenidoVerMiSolicitud');
    contenedor.innerHTML = '<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Cargando informacion...</span></div>';

    if (modalVerMiSolInstancia) {
        modalVerMiSolInstancia.show();
    }

    afiliacionAjax('obtener_mis_solicitud', { deportista_id: deportistaId }, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var d = respuesta.data;
            contenedor.innerHTML = afiliacionArmarHtmlFichaCompleta(d);
        }
    });
}

// Armar el HTML completo de la ficha de afiliacion
// Usa el constructor compartido de utilerias_js.php (una sola plantilla para admin y acudiente)
function afiliacionArmarHtmlFichaCompleta(d) {
    var ficha = afiliacionNormalizarFicha(d);
    return afiliacionArmarFichaHtml(ficha, false);
}
</script>