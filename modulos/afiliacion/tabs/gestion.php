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
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFiltrosGestion">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFiltrosGestion" aria-expanded="false" aria-controls="collapseFiltrosGestion">
                        <i class="ri-filter-3-line me-2"></i> Filtros de Busqueda
                    </button>
                </h2>
                <div id="collapseFiltrosGestion" class="accordion-collapse collapse" aria-labelledby="headingFiltrosGestion" data-bs-parent="#accordionFiltrosGestion">
                    <div class="accordion-body">
                        <form id="formFiltrosGestion">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label" for="filtro_estado_admin">Estado de la Solicitud</label>
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
                                    <label class="form-label" for="filtro_fecha_inicio">Fecha Desde</label>
                                    <input type="date" class="form-control" id="filtro_fecha_inicio" name="fecha_inicio">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="filtro_fecha_fin">Fecha Hasta</label>
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
                        <thead class="afili-encabezado-oscuro">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Deportista</th>
                                <th>Doc. Deportista</th>
                                <th>Acudiente Responsable</th>
                                <th>Estado</th>
                                <th>Progreso</th>
                                <th>Fecha Solicitud</th>
                                <th style="width: 150px;" class="text-center">Acciones</th>
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
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header afili-encabezado">
                <h5 class="modal-title" id="modalAdminVerFichaLabel">
                    <i class="ri-file-search-line me-1"></i> Revision de Solicitud de Afiliacion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoAdminVerFicha" aria-live="polite">
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
            <div class="modal-header afili-encabezado">
                <h5 class="modal-title" id="modalAdminCambiarEstadoLabel">
                    <i class="ri-edit-2-line me-1"></i> Dictaminar Estado de la Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formAdminCambiarEstado">
                    <input type="hidden" id="estado_solicitud_id" name="id" value="0">

                    <div class="mb-3">
                        <label class="form-label fw-medium" for="estado_nuevo_valor">Nuevo Estado <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-medium" for="estado_observaciones">Observaciones Administrativas</label>
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

    // 2. Inicializar DataTable server-side (solo si el CDN cargo bien)
    if (typeof DataTable === 'undefined') {
        var aviso = document.getElementById('tablaGestionAfiliaciones');
        if (aviso) {
            aviso.outerHTML = '<div class="alert alert-warning m-3">No se pudo cargar la tabla (libreria DataTables no disponible). Revise su conexion e intente de nuevo.</div>';
        }
        return;
    }
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
        language: { url: web_root + "js/datatable/spanish.json" },
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
// Usa el constructor compartido de utilerias_js.php (una sola plantilla para admin y acudiente)
function afiliacionArmarHtmlFichaAdmin(d) {
    var ficha = afiliacionNormalizarFicha(d);
    return afiliacionArmarFichaHtml(ficha, true);
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

// Desactivar / Eliminar solicitud (pide confirmacion con el estilo del sistema)
function afiliacionGestionEliminar(id) {
    afiliacionConfirmar('Desactivar solicitud', 'Esta seguro de desactivar esta solicitud de afiliacion?', 'Si, desactivar', function() {
        afiliacionAjax('eliminar_gestion', { id: id }, function(respuesta) {
            if (!respuesta.error) {
                afiliacionMostrarMsg(respuesta.msg, 'success');
                if (tablaGestionDT) {
                    tablaGestionDT.ajax.reload();
                }
            }
        });
    });
}
</script>