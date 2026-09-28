<?php
// afiliacion/tabs/gestion.php - Tab 3: Administracion de Afiliaciones (Admin - Roles 1, 4)

$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_admin = ($usuario_rol === 1 || $usuario_rol === 4);

if (!$es_admin) {
    echo '<div class="alert alert-warning">No tiene permisos de administrador para acceder a esta seccion.</div>';
    return;
}
?>

<!-- ===== TAB 3: GESTION DE AFILIACIONES (ADMINISTRACION) ===== -->
<div class="row">
    <div class="col-12">
        <!-- ============================================================ -->
        <!-- FILTROS ACCORDION (Estilo lavado_cubetas)                   -->
        <!-- ============================================================ -->
        <div class="accordion mb-3" id="accordionFiltrosGestion">
            <div class="accordion-item accordion-wrapper">
                <h2 class="accordion-header" id="headingFiltrosGestion">
                    <button class="accordion-button collapsed accordion-light-primary txt-primary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFiltrosGestion" aria-expanded="false" aria-controls="collapseFiltrosGestion">
                        <i class="ri-filter-3-line me-2"></i> Filtros de Busqueda
                    </button>
                </h2>
                <div id="collapseFiltrosGestion" class="accordion-collapse collapse" aria-labelledby="headingFiltrosGestion" data-bs-parent="#accordionFiltrosGestion">
                    <div class="accordion-body">
                        <form id="formFiltrosGestion">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Estado de la Solicitud</label>
                                    <select class="form-select" id="filtro_estado_admin" name="estado">
                                        <option value="">Todos los estados</option>
                                        <option value="borrador">Borrador</option>
                                        <option value="pendiente_revision">Pendiente de Revision</option>
                                        <option value="requiere_info">Requiere Informacion Adicional</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="no_aprobado">No Aprobado</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Desde</label>
                                    <input type="date" class="form-control" id="filtro_fecha_inicio" name="fecha_inicio">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Hasta</label>
                                    <input type="date" class="form-control" id="filtro_fecha_fin" name="fecha_fin">
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="button" class="btn btn-primary w-50" onclick="afiliacionGestionBuscar()">
                                        <i class="ri-search-line me-1"></i> Buscar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary w-50" onclick="afiliacionGestionLimpiarFiltros()">
                                        <i class="ri-eraser-line me-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- TABLA PRINCIPAL DE GESTION                                   -->
        <!-- ============================================================ -->
        <div class="card border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0" id="tablaGestionAfiliaciones" style="width: 100%;">
                        <thead>
                            <tr style="background: #405189; color: white;">
                                <th style="width: 40px; color: white;">#</th>
                                <th style="color: white;">Deportista</th>
                                <th style="color: white;">Doc. Deportista</th>
                                <th style="color: white;">Acudiente Responsable</th>
                                <th style="color: white;">Estado</th>
                                <th style="color: white;">Progreso</th>
                                <th style="color: white;">Fecha Solicitud</th>
                                <th style="width: 140px; text-align: center; color: white;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables server-side lo llena -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: VER FICHA COMPLETA (ADMIN)                            -->
<!-- ============================================================ -->
<div class="modal fade" id="modalAdminVerFicha" tabindex="-1" aria-labelledby="modalAdminVerFichaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: #405189; color: white;">
                <h5 class="modal-title text-white" id="modalAdminVerFichaLabel">
                    <i class="ri-file-search-line me-1"></i> Revision de Solicitud de Afiliacion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoAdminVerFicha">
                <div class="text-center py-5">
                    <span class="spinner-border spinner-border-sm text-primary"></span>
                    <span class="ms-2">Cargando datos completos...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: CAMBIAR ESTADO Y DEJAR OBSERVACIONES (ADMIN)          -->
<!-- ============================================================ -->
<div class="modal fade" id="modalAdminCambiarEstado" tabindex="-1" aria-labelledby="modalAdminCambiarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #405189; color: white;">
                <h5 class="modal-title text-white" id="modalAdminCambiarEstadoLabel">
                    <i class="ri-edit-2-line me-1"></i> Dictaminar Estado de la Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formAdminCambiarEstado">
                    <input type="hidden" id="estado_solicitud_id" name="id" value="0">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nuevo Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="estado_nuevo_valor" name="estado" required>
                            <option value="pendiente_revision">Pendiente de Revision</option>
                            <option value="requiere_info">Requiere Informacion Adicional (Devolver)</option>
                            <option value="aprobado">Aprobado (Aceptar Afiliacion)</option>
                            <option value="no_aprobado">No Aprobado (Rechazar)</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Observaciones Administrativas</label>
                        <textarea class="form-control" id="estado_observaciones" name="observaciones" rows="4"
                                  placeholder="Escriba el motivo, correcciones requeridas para el acudiente o detalles de la aprobacion..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="afiliacionGestionGuardarEstado()">
                    <i class="ri-save-line me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT EXCLUSIVO DEL TAB GESTION                         -->
<!-- ============================================================ -->
<script type="text/javascript">
var tablaGestionDT = null;
var modalVerFichaInstancia = null;
var modalCambiarEstadoInstancia = null;

jQuery(document).ready(function($) {
    // 1. Modales
    var elVer = document.getElementById('modalAdminVerFicha');
    if (elVer) {
        modalVerFichaInstancia = bootstrap.Modal.getOrCreateInstance(elVer);
    }
    var elEst = document.getElementById('modalAdminCambiarEstado');
    if (elEst) {
        modalCambiarEstadoInstancia = bootstrap.Modal.getOrCreateInstance(elEst);
    }

    // 2. Inicializar DataTable server-side con new DataTable()
    tablaGestionDT = new DataTable('#tablaGestionAfiliaciones', {
        ajax: {
            url: page_root + 'listar_gestion',
            type: 'POST',
            data: function(d) {
                d.estado = document.getElementById('filtro_estado_admin').value;
                d.fecha_inicio = document.getElementById('filtro_fecha_inicio').value;
                d.fecha_fin = document.getElementById('filtro_fecha_fin').value;
            },
            headers: { 'Authorization': TOKEN_GLOBAL }
        },
        columns: [
            { data: 'num' },
            { data: 'deportista_nombre', className: 'fw-medium' },
            { data: 'deportista_identificacion' },
            { data: 'acudiente_nombre' },
            {
                data: 'estado',
                render: function(data) {
                    return afiliacionBadgeEstado(data);
                }
            },
            {
                data: 'progreso',
                render: function(data) {
                    return afiliacionBarraProgreso(data);
                }
            },
            { data: 'fecha_solicitud' },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    var btnVer = row.btn_ver || '';
                    var btnEst = row.btn_estado || '';
                    var btnEli = row.btn_eliminar || '';
                    return `<div class="btn-group">${btnVer} ${btnEst} ${btnEli}</div>`;
                }
            }
        ],
        language: { url: "js/datatable/spanish.json" },
        pageLength: 50,
        serverSide: true,
        processing: true,
        scrollX: true,
        order: [[6, 'desc']]
    });
});

// Buscar con filtros
function afiliacionGestionBuscar() {
    if (tablaGestionDT) {
        tablaGestionDT.ajax.reload();
    }
}

// Limpiar filtros
function afiliacionGestionLimpiarFiltros() {
    document.getElementById('formFiltrosGestion').reset();
    if (tablaGestionDT) {
        tablaGestionDT.ajax.reload();
    }
}

// Abrir modal de visualizacion de ficha
function afiliacionGestionVer(id) {
    var contenedor = document.getElementById('contenidoAdminVerFicha');
    contenedor.innerHTML = '<div class="text-center py-5"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Cargando...</span></div>';

    if (modalVerFichaInstancia) {
        modalVerFichaInstancia.show();
    }

    afiliacionAjax('asignar_gestion', { id: id }, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var d = respuesta.data;
            contenedor.innerHTML = afiliacionArmarHtmlFichaAdmin(d);
        }
    });
}

// Armar HTML de la ficha para el Administrador
function afiliacionArmarHtmlFichaAdmin(d) {
    var depNombre = afiliacionEsc((d.deportista_nombre1 || '') + ' ' + (d.deportista_nombre2 || '') + ' ' + (d.deportista_apellido1 || '') + ' ' + (d.deportista_apellido2 || ''));
    var depDoc = afiliacionEsc((d.deportista_tipo_documento || '') + ' ' + (d.deportista_identificacion || ''));
    var depFechaNac = afiliacionEsc(d.deportista_fecha_nacimiento || '-');
    var depGenero = (d.deportista_genero === 'F') ? 'Femenino' : 'Masculino';
    var depEps = afiliacionEsc(d.deportista_eps || '-');
    var depRh = afiliacionEsc(d.deportista_rh || '-');
    var depCat = afiliacionEsc(d.categoria_nombre || '-');
    var depAlergias = afiliacionEsc(d.deportista_alergias || 'Ninguna');
    var depContacto = afiliacionEsc((d.deportista_contacto_emergencia || '-') + ' (' + (d.deportista_telefono_emergencia || '-') + ')');
    var depDireccion = afiliacionEsc(d.deportista_direccion || '-');
    var depCel = afiliacionEsc(d.deportista_celular || '-');
    var depCorreo = afiliacionEsc(d.deportista_correo || '-');
    var depObs = afiliacionEsc(d.observaciones || 'Sin observaciones');
    var badgeEstado = afiliacionBadgeEstado(d.estado);

    // Datos del acudiente
    var acuNombre = afiliacionEsc((d.acudiente_nombre1 || '') + ' ' + (d.acudiente_nombre2 || '') + ' ' + (d.acudiente_apellido1 || '') + ' ' + (d.acudiente_apellido2 || ''));
    var acuDoc = afiliacionEsc((d.acudiente_tipo_documento || '') + ' ' + (d.acudiente_identificacion || ''));
    var acuTel = afiliacionEsc(d.acudiente_celular || '-');
    var acuCorreo = afiliacionEsc(d.acudiente_correo || '-');
    var acuDir = afiliacionEsc(d.acudiente_direccion || '-');
    var parentesco = afiliacionEsc(d.acudiente_parentesco || 'Acudiente');

    // Documentos adjuntos
    var docs = d.documentos || [];
    var docsHtml = '';
    if (docs.length === 0) {
        docsHtml = '<li class="list-group-item text-muted">No hay documentos adjuntos a esta solicitud.</li>';
    } else {
        for (var i = 0; i < docs.length; i++) {
            var doc = docs[i];
            var docTipo = afiliacionEsc(doc.tipo_nombre);
            var docArchivo = afiliacionEsc(doc.archivo);
            var docOriginal = afiliacionEsc(doc.archivo_original);
            var docEstado = afiliacionEsc(doc.estado);
            docsHtml += `
            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                <div>
                    <strong>${docTipo}</strong>
                    <small class="text-muted d-block">${docOriginal}</small>
                </div>
                <div>
                    <span class="badge bg-light text-dark me-2">${docEstado}</span>
                    <a href="${docArchivo}" target="_blank" class="btn btn-sm btn-primary"><i class="ri-eye-line me-1"></i>Ver</a>
                </div>
            </li>`;
        }
    }

    return `
    <div class="row g-3">
        <div class="col-12 d-flex justify-content-between align-items-center pb-2 border-bottom">
            <div>
                <h4 class="mb-0 text-primary">${depNombre}</h4>
                <small class="text-muted">Documento: ${depDoc} &middot; Solicitado: ${d.fecha_solicitud || '-'}</small>
            </div>
            <div>${badgeEstado}</div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary"><i class="ri-user-smile-line me-1"></i> Ficha del Deportista</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width: 140px;">Documento:</td><td class="fw-medium">${depDoc}</td></tr>
                        <tr><td class="text-muted">Nacimiento:</td><td>${depFechaNac}</td></tr>
                        <tr><td class="text-muted">Genero:</td><td>${depGenero}</td></tr>
                        <tr><td class="text-muted">Categoria:</td><td class="fw-medium">${depCat}</td></tr>
                        <tr><td class="text-muted">EPS / SISBEN:</td><td>${depEps}</td></tr>
                        <tr><td class="text-muted">Grupo RH:</td><td>${depRh}</td></tr>
                        <tr><td class="text-muted">Celular:</td><td>${depCel}</td></tr>
                        <tr><td class="text-muted">Correo:</td><td>${depCorreo}</td></tr>
                        <tr><td class="text-muted">Direccion:</td><td>${depDireccion}</td></tr>
                        <tr><td class="text-muted">Alergias:</td><td>${depAlergias}</td></tr>
                        <tr><td class="text-muted">Emergencia:</td><td>${depContacto}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary"><i class="ri-parent-line me-1"></i> Informacion del Acudiente</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width: 140px;">Nombre:</td><td class="fw-medium">${acuNombre}</td></tr>
                        <tr><td class="text-muted">Parentesco:</td><td class="text-capitalize">${parentesco}</td></tr>
                        <tr><td class="text-muted">Documento:</td><td>${acuDoc}</td></tr>
                        <tr><td class="text-muted">Celular:</td><td>${acuTel}</td></tr>
                        <tr><td class="text-muted">Correo:</td><td>${acuCorreo}</td></tr>
                        <tr><td class="text-muted">Direccion:</td><td>${acuDir}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary"><i class="ri-folder-shield-line me-1"></i> Documentos Verificables</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        ${docsHtml}
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border border-warning-subtle">
                <div class="card-header bg-warning-subtle text-dark">
                    <h6 class="mb-0"><i class="ri-feedback-line me-1"></i> Observaciones y Dictamen Administrativo</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">${depObs}</p>
                </div>
            </div>
        </div>
    </div>`;
}

// Abrir modal para cambiar estado
function afiliacionGestionAbrirEstado(id) {
    document.getElementById('estado_solicitud_id').value = id;
    document.getElementById('estado_observaciones').value = '';
    if (modalCambiarEstadoInstancia) {
        modalCambiarEstadoInstancia.show();
    }
}

// Guardar cambio de estado
function afiliacionGestionGuardarEstado() {
    var id = document.getElementById('estado_solicitud_id').value;
    var estado = document.getElementById('estado_nuevo_valor').value;
    var obs = document.getElementById('estado_observaciones').value;

    if (!id || id === '0') {
        afiliacionMostrarMsg('ID de solicitud no valido', 'error');
        return;
    }

    afiliacionAjax('modificar_gestion', { id: id, estado: estado, observaciones: obs }, function(respuesta) {
        if (!respuesta.error) {
            afiliacionMostrarMsg(respuesta.msg, 'success');
            if (modalCambiarEstadoInstancia) {
                modalCambiarEstadoInstancia.hide();
            }
            if (tablaGestionDT) {
                tablaGestionDT.ajax.reload();
            }
        }
    });
}

// Desactivar / Eliminar solicitud
function afiliacionGestionEliminar(id) {
    if (!confirm('Esta seguro de desactivar esta solicitud de afiliacion?')) {
        return;
    }

    afiliacionAjax('eliminar_gestion', { id: id }, function(respuesta) {
        if (!respuesta.error) {
            afiliacionMostrarMsg(respuesta.msg, 'success');
            if (tablaGestionDT) {
                tablaGestionDT.ajax.reload();
            }
        }
    });
}
</script>