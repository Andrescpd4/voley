<!-- Modulo de Afiliacion - Formulario -->
<style>
    .afiliacion-table th {
        font-weight: 600;
        background-color: var(--vz-light);
    }
    .afiliacion-btn-accion {
        padding: 4px 8px;
        font-size: 0.85rem;
    }
    .afiliacion-estado-badge {
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ri-user-add-line me-1"></i> Gestion de Afiliaciones
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm accion-agregar" onclick="afiliacionAbrirModalNuevo()">
                            <i class="ri-add-line me-1"></i> Nueva Solicitud
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="accordion mb-3" id="accordionFiltros">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros">
                                    <i class="ri-filter-line me-1"></i> Filtros
                                </button>
                            </h2>
                            <div id="collapseFiltros" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select form-select-sm" id="filtro_estado" onchange="afiliacionCargarDatos()">
                                                <option value="">Todos</option>
                                                <option value="borrador">Borrador</option>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="en_revision">En revision</option>
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
                        <table class="table table-striped table-hover afiliacion-table align-middle" id="afiliacionTabla">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Acudiente</th>
                                    <th>Deportista</th>
                                    <th>Estado</th>
                                    <th>Progreso</th>
                                    <th>Fecha Solicitud</th>
                                    <th style="width: 160px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="afiliacionTbody">
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
        </div>
    </div>
</div>

<!-- Modal: Crear Solicitud -->
<div class="modal fade" id="afiliacionModalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Solicitud de Afiliacion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="afiliacionFormNuevo">
                    <div class="mb-3">
                        <label class="form-label">Acudiente</label>
                        <select class="form-select" id="afiliacion_acudiente_id" name="acudiente_id" required>
                            <option value="">Seleccione un acudiente...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deportista</label>
                        <select class="form-select" id="afiliacion_deportista_id" name="deportista_id" required>
                            <option value="">Seleccione un deportista...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="afiliacionGuardarNueva()">Crear Solicitud</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Ver/Editar Solicitud -->
<div class="modal fade" id="afiliacionModalVer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="afiliacionModalContenido">
                <!-- Se llena dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Aprobar/Rechazar -->
<div class="modal fade" id="afiliacionModalEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="afiliacionFormEstado">
                    <input type="hidden" id="afiliacion_estado_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado</label>
                        <select class="form-select" id="afiliacion_nuevo_estado" name="estado" required>
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
                        <textarea class="form-control" id="afiliacion_observaciones" name="observaciones" rows="3" placeholder="Observaciones sobre la decision..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="afiliacionGuardarEstado()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var afiliacionModalNuevo = null;
    var afiliacionModalVer = null;
    var afiliacionModalEstado = null;

    $(document).ready(function() {
        afiliacionModalNuevo = bootstrap.Modal.getOrCreateInstance(document.getElementById('afiliacionModalNuevo'));
        afiliacionModalVer = bootstrap.Modal.getOrCreateInstance(document.getElementById('afiliacionModalVer'));
        afiliacionModalEstado = bootstrap.Modal.getOrCreateInstance(document.getElementById('afiliacionModalEstado'));
        afiliacionCargarDatos();
    });

    function afiliacionMostrarMsg(texto, tipo) {
        if (typeof Toastify !== 'undefined') {
            var color = '#405189';
            if (tipo == 'success') color = '#0ab39c';
            if (tipo == 'error') color = '#f06548';
            if (tipo == 'warning') color = '#f7b84b';
            Toastify({ text: texto, duration: 3000, gravity: 'top', position: 'right', style: { background: color } }).showToast();
        } else {
            alert(texto);
        }
    }

    function afiliacionEscaparHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function afiliacionCargarDatos() {
        var estado = document.getElementById('filtro_estado').value;
        var params = { offset: 0, limit: 50 };
        if (estado != '') {
            params.estado = estado;
        }

        $.ajax({
            url: page_root + 'listar',
            type: 'GET',
            data: params,
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    afiliacionMostrarMsg(r.msg, 'error');
                    return;
                }
                afiliacionRenderizarTabla(r.rows || []);
            },
            error: function() {
                afiliacionMostrarMsg('Error al cargar datos', 'error');
            }
        });
    }

    function afiliacionRenderizarTabla(solicitudes) {
        var tbody = document.getElementById('afiliacionTbody');
        if (solicitudes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay solicitudes registradas.</td></tr>';
            return;
        }

        var html = '';
        for (var i = 0; i < solicitudes.length; i++) {
            var s = solicitudes[i];

            // Color del badge segun estado
            var estadoClass = 'bg-secondary';
            var estadoTexto = s.estado;
            if (s.estado == 'aprobado' || s.estado == 'activo') { estadoClass = 'bg-success'; }
            else if (s.estado == 'pendiente') { estadoClass = 'bg-info'; }
            else if (s.estado == 'en_revision') { estadoClass = 'bg-warning'; }
            else if (s.estado == 'rechazado' || s.estado == 'no_aprobado') { estadoClass = 'bg-danger'; }
            else if (s.estado == 'borrador') { estadoClass = 'bg-secondary'; }

            var botones = '<div class="d-flex justify-content-center gap-1">';
            botones += '<button class="btn btn-sm btn-soft-info afiliacion-btn-accion" onclick="afiliacionVer(' + s.id + ')" title="Ver detalle"><i class="ri-eye-line"></i></button>';
            botones += '<button class="btn btn-sm btn-soft-primary afiliacion-btn-accion accion-modificar" onclick="afiliacionAbrirEstado(' + s.id + ')" title="Cambiar estado"><i class="ri-edit-line"></i></button>';
            botones += '<button class="btn btn-sm btn-soft-danger afiliacion-btn-accion accion-eliminar" onclick="afiliacionEliminar(' + s.id + ')" title="Eliminar"><i class="ri-delete-bin-line"></i></button>';
            botones += '</div>';

            html += '<tr>';
            html += '<td>' + s._NUM_ + '</td>';
            html += '<td>' + afiliacionEscaparHtml(s.acudiente_nombre) + '</td>';
            html += '<td>' + afiliacionEscaparHtml(s.deportista_nombre) + '</td>';
            html += '<td><span class="afiliacion-estado-badge ' + estadoClass + '">' + afiliacionEscaparHtml(estadoTexto) + '</span></td>';
            html += '<td>';
            html += '<div class="progress" style="height: 20px;">';
            html += '<div class="progress-bar" role="progressbar" style="width: ' + (s.porcentaje_completado || 0) + '%;">' + (s.porcentaje_completado || 0) + '%</div>';
            html += '</div>';
            html += '</td>';
            html += '<td>' + (s.fecha_solicitud || '-') + '</td>';
            html += '<td class="text-center">' + botones + '</td>';
            html += '</tr>';
        }
        tbody.innerHTML = html;
    }

    function afiliacionAbrirModalNuevo() {
        // Cargar selects
        afiliacionCargarSelectAcudientes();
        afiliacionCargarSelectDeportistas();
        afiliacionModalNuevo.show();
    }

    function afiliacionCargarSelectAcudientes() {
        $.ajax({
            url: page_root + 'listarAcudientes',
            type: 'POST',
            headers: { 'Authorization': TOKEN_GLOBAL },
            dataType: 'json',
            success: function(r) {
                if (r.error === false) {
                    var select = document.getElementById('afiliacion_acudiente_id');
                    select.innerHTML = '<option value="">Seleccione un acudiente...</option>';
                    for (var i = 0; i < r.data.length; i++) {
                        var op = document.createElement('option');
                        op.value = r.data[i].id;
                        op.textContent = r.data[i].nombre;
                        select.appendChild(op);
                    }
                }
            }
        });
    }

    function afiliacionCargarSelectDeportistas() {
        $.ajax({
            url: page_root + 'listarDeportistas',
            type: 'POST',
            headers: { 'Authorization': TOKEN_GLOBAL },
            dataType: 'json',
            success: function(r) {
                if (r.error === false) {
                    var select = document.getElementById('afiliacion_deportista_id');
                    select.innerHTML = '<option value="">Seleccione un deportista...</option>';
                    for (var i = 0; i < r.data.length; i++) {
                        var op = document.createElement('option');
                        op.value = r.data[i].id;
                        op.textContent = r.data[i].nombre;
                        select.appendChild(op);
                    }
                }
            }
        });
    }

    function afiliacionGuardarNueva() {
        var acudienteId = document.getElementById('afiliacion_acudiente_id').value;
        var deportistaId = document.getElementById('afiliacion_deportista_id').value;

        if (acudienteId == '' || deportistaId == '') {
            afiliacionMostrarMsg('Debe seleccionar acudiente y deportista', 'warning');
            return;
        }

        var formData = new FormData();
        formData.append('acudiente_id', acudienteId);
        formData.append('deportista_id', deportistaId);
        formData.append('estado', 'pendiente');

        $.ajax({
            url: page_root + 'agregar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    afiliacionMostrarMsg(r.msg, 'error');
                    return;
                }
                afiliacionMostrarMsg(r.msg, 'success');
                afiliacionModalNuevo.hide();
                afiliacionCargarDatos();
            },
            error: function() {
                afiliacionMostrarMsg('Error al crear la solicitud', 'error');
            }
        });
    }

    function afiliacionVer(id) {
        $.ajax({
            url: page_root + 'asignar',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    afiliacionMostrarMsg(r.msg, 'error');
                    return;
                }
                var d = r.data;
                var html = '<table class="table table-borderless">';
                html += '<tr><td class="fw-bold">Acudiente ID:</td><td>' + d.acudiente_id + '</td></tr>';
                html += '<tr><td class="fw-bold">Deportista ID:</td><td>' + d.deportista_id + '</td></tr>';
                html += '<tr><td class="fw-bold">Parentesco:</td><td>' + afiliacionEscaparHtml(d.parentesco || '-') + '</td></tr>';
                html += '<tr><td class="fw-bold">Estado:</td><td>' + afiliacionEscaparHtml(d.estado) + '</td></tr>';
                html += '<tr><td class="fw-bold">EPS:</td><td>' + afiliacionEscaparHtml(d.eps || '-') + '</td></tr>';
                html += '<tr><td class="fw-bold">Contacto Emergencia:</td><td>' + afiliacionEscaparHtml(d.contacto_emergencia || '-') + '</td></tr>';
                html += '<tr><td class="fw-bold">Fecha Solicitud:</td><td>' + (d.fecha_solicitud || '-') + '</td></tr>';
                html += '<tr><td class="fw-bold">Fecha Aprobacion:</td><td>' + (d.fecha_aprobacion || '-') + '</td></tr>';
                html += '<tr><td class="fw-bold">Observaciones:</td><td>' + afiliacionEscaparHtml(d.observaciones || '-') + '</td></tr>';
                html += '</table>';
                document.getElementById('afiliacionModalContenido').innerHTML = html;
                afiliacionModalVer.show();
            },
            error: function() {
                afiliacionMostrarMsg('Error al cargar los datos', 'error');
            }
        });
    }

    function afiliacionAbrirEstado(id) {
        document.getElementById('afiliacion_estado_id').value = id;
        document.getElementById('afiliacion_nuevo_estado').value = 'pendiente';
        document.getElementById('afiliacion_observaciones').value = '';
        afiliacionModalEstado.show();
    }

    function afiliacionGuardarEstado() {
        var id = document.getElementById('afiliacion_estado_id').value;
        var estado = document.getElementById('afiliacion_nuevo_estado').value;
        var observaciones = document.getElementById('afiliacion_observaciones').value;

        var formData = new FormData();
        formData.append('id', id);
        formData.append('estado', estado);
        formData.append('observaciones', observaciones);

        $.ajax({
            url: page_root + 'modificar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    afiliacionMostrarMsg(r.msg, 'error');
                    return;
                }
                afiliacionMostrarMsg(r.msg, 'success');
                afiliacionModalEstado.hide();
                afiliacionCargarDatos();
            },
            error: function() {
                afiliacionMostrarMsg('Error al actualizar el estado', 'error');
            }
        });
    }

    function afiliacionEliminar(id) {
        if (!confirm('Esta seguro de eliminar esta solicitud?')) {
            return;
        }
        var formData = new FormData();
        formData.append('id', id);

        $.ajax({
            url: page_root + 'eliminar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    afiliacionMostrarMsg(r.msg, 'error');
                    return;
                }
                afiliacionMostrarMsg(r.msg, 'success');
                afiliacionCargarDatos();
            },
            error: function() {
                afiliacionMostrarMsg('Error al eliminar', 'error');
            }
        });
    }
</script>
