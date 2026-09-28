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
<div class="card border">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: #405189; color: white;">
        <h6 class="card-title mb-0 text-white">
            <i class="ri-history-line me-1"></i> Mis Solicitudes de Afiliacion
        </h6>
        <button type="button" class="btn btn-sm btn-light" onclick="afiliacionMisSolicitudesCargarDatos()">
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
                        <th style="width: 100px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyMisSolicitudes">
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: #405189; color: white;">
                <h5 class="modal-title text-white" id="modalVerMiSolicitudLabel">
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
            for (var i = 0; i < lista.length; i++) {
                html += afiliacionCrearFilaMiSolicitud(lista[i]);
            }
            tbody.innerHTML = html;
        }
    });
}

// Crear una fila de la tabla de solicitudes con backticks
function afiliacionCrearFilaMiSolicitud(item) {
    var num = item._NUM_;
    var nombre = afiliacionEsc(item.deportista_nombre);
    var badgeEstado = afiliacionBadgeEstado(item.estado);
    var barraProgreso = afiliacionBarraProgreso(item.porcentaje_completado);
    var fecha = afiliacionEsc(item.fecha_solicitud || item.created_at || '-');
    var obs = afiliacionEsc(item.observaciones || 'Sin observaciones');
    var depId = item.deportista_id;

    return `
    <tr>
        <td><strong>${num}</strong></td>
        <td class="fw-medium">${nombre}</td>
        <td>${badgeEstado}</td>
        <td style="min-width: 120px;">${barraProgreso}</td>
        <td>${fecha}</td>
        <td><small class="text-muted">${obs}</small></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-info" onclick="afiliacionVerMiDetalle(${depId})" title="Ver ficha completa">
                <i class="ri-eye-line"></i> Ver
            </button>
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
function afiliacionArmarHtmlFichaCompleta(d) {
    var depNombre = afiliacionEsc((d.nombre1 || '') + ' ' + (d.nombre2 || '') + ' ' + (d.apellido1 || '') + ' ' + (d.apellido2 || ''));
    var depDoc = afiliacionEsc((d.tipo_documento || '') + ' ' + (d.identificacion || ''));
    var depFechaNac = afiliacionEsc(d.fecha_nacimiento || '-');
    var depGenero = (d.genero === 'F') ? 'Femenino' : 'Masculino';
    var depEps = afiliacionEsc(d.eps || '-');
    var depRh = afiliacionEsc(d.rh || '-');
    var depCat = afiliacionEsc(d.categoria_nombre || '-');
    var depAlergias = afiliacionEsc(d.alergias || 'Ninguna');
    var depContacto = afiliacionEsc((d.contacto_emergencia_nombre || '-') + ' (' + (d.contacto_emergencia_telefono || '-') + ')');
    var depObs = afiliacionEsc(d.observaciones || 'Sin observaciones');
    var badgeEstado = afiliacionBadgeEstado(d.estado);

    // Datos del acudiente
    var acu = d.acudiente || {};
    var acuNombre = afiliacionEsc((acu.nombre1 || '') + ' ' + (acu.nombre2 || '') + ' ' + (acu.apellido1 || '') + ' ' + (acu.apellido2 || ''));
    var acuDoc = afiliacionEsc((acu.tipo_documento || '') + ' ' + (acu.identificacion || ''));
    var acuTel = afiliacionEsc(acu.celular || '-');
    var acuCorreo = afiliacionEsc(acu.correo || '-');
    var parentesco = afiliacionEsc(d.parentesco || 'Acudiente');

    // Documentos adjuntos
    var docs = d.documentos || [];
    var docsHtml = '';
    if (docs.length === 0) {
        docsHtml = '<li class="list-group-item text-muted">No se han adjuntado documentos aun.</li>';
    } else {
        for (var i = 0; i < docs.length; i++) {
            var doc = docs[i];
            var docTipo = afiliacionEsc(doc.tipo_nombre);
            var docArchivo = afiliacionEsc(doc.archivo);
            var docEstado = afiliacionEsc(doc.estado);
            docsHtml += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>${docTipo}</strong>
                    <span class="badge bg-light text-dark ms-2">${docEstado}</span>
                </div>
                <a href="${docArchivo}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-download-line me-1"></i>Ver / Descargar</a>
            </li>`;
        }
    }

    return `
    <div class="row g-3">
        <div class="col-12 text-center pb-2 border-bottom">
            <h5 class="mb-1 text-primary">${depNombre}</h5>
            <div>${badgeEstado}</div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title text-primary"><i class="ri-user-smile-line me-1"></i> Datos del Deportista</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width: 140px;">Documento:</td><td class="fw-medium">${depDoc}</td></tr>
                        <tr><td class="text-muted">Nacimiento:</td><td>${depFechaNac}</td></tr>
                        <tr><td class="text-muted">Genero:</td><td>${depGenero}</td></tr>
                        <tr><td class="text-muted">Categoria:</td><td>${depCat}</td></tr>
                        <tr><td class="text-muted">EPS / SISBEN:</td><td>${depEps}</td></tr>
                        <tr><td class="text-muted">RH:</td><td>${depRh}</td></tr>
                        <tr><td class="text-muted">Alergias:</td><td>${depAlergias}</td></tr>
                        <tr><td class="text-muted">Emergencia:</td><td>${depContacto}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title text-primary"><i class="ri-parent-line me-1"></i> Datos del Acudiente</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width: 140px;">Nombre:</td><td class="fw-medium">${acuNombre}</td></tr>
                        <tr><td class="text-muted">Parentesco:</td><td class="text-capitalize">${parentesco}</td></tr>
                        <tr><td class="text-muted">Documento:</td><td>${acuDoc}</td></tr>
                        <tr><td class="text-muted">Celular:</td><td>${acuTel}</td></tr>
                        <tr><td class="text-muted">Correo:</td><td>${acuCorreo}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h6 class="text-primary mt-2"><i class="ri-folder-shield-line me-1"></i> Documentos Adjuntos</h6>
            <ul class="list-group">
                ${docsHtml}
            </ul>
        </div>

        <div class="col-12">
            <div class="alert alert-secondary mb-0">
                <strong>Observaciones del Club:</strong><br>
                ${depObs}
            </div>
        </div>
    </div>`;
}
</script>