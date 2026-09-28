<?php
// afiliacion/tabs/gestion.php - Tab 1: Gestion de Afiliaciones (Admin)
// Solo visible para roles 1 y 4 (Admin)

$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_admin = ($usuario_rol === 1 || $usuario_rol === 4);
if (!$es_admin) {
    echo '<div class="alert alert-warning">No tiene permisos para acceder a esta seccion.</div>';
    return;
}
?>
<!-- ===== TAB 1: GESTION DE AFILIACIONES ===== -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">
                <i class="ri-user-add-line me-1"></i> Gestion de Afiliaciones
            </h5>
            <button type="button" class="btn btn-primary btn-sm accion-agregar" onclick="afiliacionGestionAbrirModalNuevo()">
                <i class="ri-add-line me-1"></i> Nueva Solicitud
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="accordion mb-3" id="accordionFiltrosGestion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltrosGestion">
                        <i class="ri-filter-line me-1"></i> Filtros
                    </button>
                </h2>
                <div id="collapseFiltrosGestion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltrosGestion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <select class="form-select form-select-sm" id="filtro_estado_gestion" onchange="afiliacionGestionCargarDatos()">
                                    <option value="">Todos</option>
                                    <option value="borrador">Borrador</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_revision">En revision</option>
                                    <option value="informacion_adicional">Requiere informacion</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="activo">Activo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-striped table-hover afiliacion-table align-middle" id="afiliacionGestionTabla">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Acudiente</th>
                        <th>Deportista</th>
                        <th>Estado</th>
                        <th>Progreso</th>
                        <th>Fecha Solicitud</th>
                        <th style="width: 180px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="afiliacionGestionTbody">
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="text-muted ms-2">Cargando...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Crear Solicitud -->
<div class="modal fade" id="afiliacionGestionModalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Solicitud de Afiliacion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="afiliacionGestionFormNuevo">
                    <div class="mb-3">
                        <label class="form-label">Acudiente <span class="text-danger">*</span></label>
                        <select class="form-select" id="afiliacion_gestion_acudiente_id" name="acudiente_id" required>
                            <option value="">Seleccione un acudiente...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deportista <span class="text-danger">*</span></label>
                        <select class="form-select" id="afiliacion_gestion_deportista_id" name="deportista_id" required>
                            <option value="">Seleccione un deportista...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado Inicial</label>
                        <select class="form-select" id="afiliacion_gestion_estado" name="estado">
                            <option value="borrador">Borrador</option>
                            <option value="pendiente" selected>Pendiente</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="afiliacionGestionGuardarNueva()">Crear Solicitud</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Ver Detalle -->
<div class="modal fade" id="afiliacionGestionModalVer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="afiliacionGestionModalContenido">
                <!-- Se llena dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cambiar Estado -->
<div class="modal fade" id="afiliacionGestionModalEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="afiliacionGestionFormEstado">
                    <input type="hidden" id="afiliacion_gestion_estado_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="afiliacion_gestion_nuevo_estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_revision">En revision</option>
                            <option value="informacion_adicional">Requiere informacion</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="activo">Activo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="afiliacion_gestion_observaciones" name="observaciones" rows="3" placeholder="Observaciones sobre la decision..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="afiliacionGestionGuardarEstado()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript>
// ===== JAVASCRIPT TAB GESTION =====

var afiliacionGestionModalNuevo = null;
var afiliacionGestionModalVer = null;
var afiliacionGestionModalEstado = null;
var afiliacionGestionTabla = null;

jQuery(document).ready(function($) {
    afiliacionGestionModalNuevo = bootstrap.Modal.getOrCreateInstance(document.getElementById('afiliacionGestionModalNuevo'));
    afiliacionGestionModalVer = bootstrap.Modal.getOrCreateInstance(document.getElementById('afiliacionGestionModalVer'));
    afiliacionGestionModalEstado = bootstrap.Modal.getOrCreateInstance(document.getElementById('afiliacionGestionModalEstado'));

    // Inicializar DataTable
    afiliacionGestionTabla = $('#afiliacionGestionTabla').DataTable({
        ajax: {
            url: page_root + 'listar_gestion',
            type: 'POST',
            data: function(d) {
                d.estado = $('#filtro_estado_gestion').val();
            },
            headers: { 'Authorization': TOKEN_GLOBAL }
        },
        columns: [
            { data: '_NUM_' },
            { data: 'acudiente_nombre' },
            { data: 'deportista_nombre' },
            {
                data: 'estado',
                render: function(data) {
                    var fmt = afiliacionFormatearEstado(data);
                    return '<span class="afiliacion-estado-badge " + fmt.class + "">' + afiliacionEsc(fmt.texto) + '</span>';
                }
            },
            {
                data: 'porcentaje_completado',
                render: function(data) {
                    return afiliacionCrearProgreso(data);
                }
            },
            { data: 'fecha_solicitud' },
            {
                data: null,
                render: function(data) {
                    return afiliacionCrearBotonesAccion(data.id, true);
                },
                orderable: false,
                className: 'text-center'
            }
        ],
        language: { url: 'js/datatable/spanish.json' },
        pageLength: 50,
        processing: true,
        serverSide: true,
        order: [[5, 'desc']],
        dom: '<"top">rt<"bottom"lp>',
        initComplete: function() {
            console.log('Tabla Gestion inicializada correctamente');
        }
    });
});

// Cargar selects para modal nuevo
function afiliacionGestionCargarSelectAcudientes() {
    afiliacionAjax('listarAcudientes', {}, function(r) {
        var select = document.getElementById('afiliacion_gestion_acudiente_id');
        select.innerHTML = '<option value="">Seleccione un acudiente...</option>';
        for (var i = 0; i < r.data.length; i++) {
            var op = document.createElement('option');
            op.value = r.data[i].id;
            op.textContent = r.data[i].nombre;
            select.appendChild(op);
        }
    });
}

function afiliacionGestionCargarSelectDeportistas() {
    afiliacionAjax('listarDeportistas', {}, function(r) {
        var select = document.getElementById('afiliacion_gestion_deportista_id');
        select.innerHTML = '<option value="">Seleccione un deportista...</option>';
        for (var i = 0; i < r.data.length; i++) {
            var op = document.createElement('option');
            op.value = r.data[i].id;
            op.textContent = r.data[i].nombre;
            select.appendChild(op);
        }
    });
}

// Abrir modal nuevo
function afiliacionGestionAbrirModalNuevo() {
    afiliacionGestionCargarSelectAcudientes();
    afiliacionGestionCargarSelectDeportistas();
    document.getElementById('afiliacionGestionFormNuevo').reset();
    afiliacionGestionModalNuevo.show();
}

// Guardar nueva solicitud
function afiliacionGestionGuardarNueva() {
    var acudienteId = document.getElementById('afiliacion_gestion_acudiente_id').value;
    var deportistaId = document.getElementById('afiliacion_gestion_deportista_id').value;
    var estado = document.getElementById('afiliacion_gestion_estado').value;

    if (acudienteId == '' || deportistaId == '') {
        afiliacionMostrarMsg('Debe seleccionar acudiente y deportista', 'warning');
        return;
    }

    var formData = new FormData();
    formData.append('acudiente_id', acudienteId);
    formData.append('deportista_id', deportistaId);
    formData.append('estado', estado);

    afiliacionAjaxFormData('agregar_gestion', formData, function(r) {
        if (!r.error) {
            afiliacionMostrarMsg(r.msg, 'success');
            afiliacionGestionModalNuevo.hide();
            if (afiliacionGestionTabla) {
                afiliacionGestionTabla.ajax.reload();
            }
        }
    });
}

// Ver detalle
function afiliacionGestionVer(id) {
    afiliacionAjax('asignar_gestion', { id: id }, function(r) {
        if (!r.error) {
            var d = r.data;
            var html = '<table class="table table-borderless">';
            html += '<tr><td class="fw-bold">Acudiente ID:</td><td>' + d.acudiente_id + '</td></tr>';
            html += '<tr><td class="fw-bold">Deportista ID:</td><td>' + d.deportista_id + '</td></tr>';
            html += '<tr><td class="fw-bold">Parentesco:</td><td>' + afiliacionEsc(d.parentesco || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Estado:</td><td>' + afiliacionEsc(d.estado) + '</td></tr>';
            html += '<tr><td class="fw-bold">EPS:</td><td>' + afiliacionEsc(d.eps || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Contacto Emergencia:</td><td>' + afiliacionEsc(d.contacto_emergencia || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Telefono Emergencia:</td><td>' + afiliacionEsc(d.telefono_emergencia || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Fecha Solicitud:</td><td>' + (d.fecha_solicitud || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Fecha Aprobacion:</td><td>' + (d.fecha_aprobacion || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Observaciones:</td><td>' + afiliacionEsc(d.observaciones || '-') + '</td></tr>';
            html += '</table>';
            document.getElementById('afiliacionGestionModalContenido').innerHTML = html;
            afiliacionGestionModalVer.show();
        }
    });
}

// Abrir modal cambiar estado
function afiliacionGestionAbrirEstado(id) {
    document.getElementById('afiliacion_gestion_estado_id').value = id;
    document.getElementById('afiliacion_gestion_nuevo_estado').value = 'pendiente';
    document.getElementById('afiliacion_gestion_observaciones').value = '';
    afiliacionGestionModalEstado.show();
}

// Guardar cambio de estado
function afiliacionGestionGuardarEstado() {
    var id = document.getElementById('afiliacion_gestion_estado_id').value;
    var estado = document.getElementById('afiliacion_gestion_nuevo_estado').value;
    var observaciones = document.getElementById('afiliacion_gestion_observaciones').value;

    var formData = new FormData();
    formData.append('id', id);
    formData.append('estado', estado);
    formData.append('observaciones', observaciones);

    afiliacionAjaxFormData('modificar_gestion', formData, function(r) {
        if (!r.error) {
            afiliacionMostrarMsg(r.msg, 'success');
            afiliacionGestionModalEstado.hide();
            if (afiliacionGestionTabla) {
                afiliacionGestionTabla.ajax.reload();
            }
        }
    });
}

// Eliminar solicitud
function afiliacionGestionEliminar(id) {
    if (!confirm('Esta seguro de eliminar esta solicitud?')) {
        return;
    }

    var formData = new FormData();
    formData.append('id', id);

    afiliacionAjaxFormData('eliminar_gestion', formData, function(r) {
        if (!r.error) {
            afiliacionMostrarMsg(r.msg, 'success');
            if (afiliacionGestionTabla) {
                afiliacionGestionTabla.ajax.reload();
            }
        }
    });
}

// Recargar tabla (para filtros)
function afiliacionGestionCargarDatos() {
    if (afiliacionGestionTabla) {
        afiliacionGestionTabla.ajax.reload();
    }
}
</script>