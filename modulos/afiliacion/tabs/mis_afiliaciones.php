<?php
// afiliacion/tabs/mis_afiliaciones.php - Tab 2: Mis Afiliaciones (Acudiente - Rol 3)
// Solo visible para rol 3 (Acudiente)

$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_acudiente = ($usuario_rol === 3);
if (!$es_acudiente) {
    echo '<div class="alert alert-warning">Esta seccion es solo para acudientes.</div>';
    return;
}
?>
<!-- ===== TAB 2: MIS AFILIACIONES ===== -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="ri-file-user-line me-1"></i> Mis Afiliaciones
        </h5>
        <small class="text-muted">Solicitudes asociadas a tu cuenta</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover afiliacion-table align-middle" id="misAfiliacionesTabla">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Deportista</th>
                        <th>Estado</th>
                        <th>Progreso</th>
                        <th>Fecha Solicitud</th>
                        <th>PDF</th>
                        <th style="width: 140px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="misAfiliacionesTbody">
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

<!-- Modal: Ver Detalle -->
<div class="modal fade" id="misAfiliacionesModalVer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de mi Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="misAfiliacionesModalContenido">
                <!-- Se llena dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Subir PDF -->
<div class="modal fade" id="misAfiliacionesModalPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Formulario de Afiliacion (PDF)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="misAfiliacionesFormPDF" enctype="multipart/form-data">
                    <input type="hidden" id="mis_afiliaciones_pdf_solicitud_id" name="solicitud_id">
                    <div class="mb-3">
                        <label class="form-label">Archivo PDF <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="mis_afiliaciones_pdf_archivo" name="pdf" accept=".pdf" required>
                        <div class="form-text">Solo archivos PDF. Tamaño maximo: 10 MB</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" id="mis_afiliaciones_pdf_obs" name="observaciones" rows="2" placeholder="Comentarios adicionales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="misAfiliacionesSubirPDF()">Subir PDF</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript>
// ===== JAVASCRIPT TAB MIS AFILIACIONES =====

var misAfiliacionesModalVer = null;
var misAfiliacionesModalPDF = null;
var misAfiliacionesTabla = null;

jQuery(document).ready(function($) {
    misAfiliacionesModalVer = bootstrap.Modal.getOrCreateInstance(document.getElementById('misAfiliacionesModalVer'));
    misAfiliacionesModalPDF = bootstrap.Modal.getOrCreateInstance(document.getElementById('misAfiliacionesModalPDF'));

    // Inicializar DataTable
    misAfiliacionesTabla = $('#misAfiliacionesTabla').DataTable({
        ajax: {
            url: page_root + 'listar_mis_afiliaciones',
            type: 'GET',
            data: function(d) {
                // DataTables envia start, length, draw, search...
                // El backend espera offset, limit, etc.
            },
            headers: { 'Authorization': TOKEN_GLOBAL },
            dataSrc: function(json) {
                if (json.error) {
                    afiliacionMostrarMsg(json.msg, 'error');
                    return [];
                }
                // json.rows ya viene formateado
                return json.rows || [];
            }
        },
        columns: [
            { data: '_NUM_' },
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
                data: 'pdf_ruta',
                render: function(data) {
                    return afiliacionCrearPDFLink(data, false);
                }
            },
            {
                data: null,
                render: function(data) {
                    // Botones: Ver, Subir PDF
                    var botones = '<div class="d-flex justify-content-center gap-1">';
                    botones += '<button class="btn btn-sm btn-soft-info" onclick="misAfiliacionesVer(' + data.id + ')" title="Ver detalle"><i class="ri-eye-line"></i></button>';
                    botones += '<button class="btn btn-sm btn-soft-primary" onclick="misAfiliacionesAbrirPDF(' + data.id + ')" title="Subir PDF"><i class="ri-upload-cloud-line"></i></button>';
                    botones += '</div>';
                    return botones;
                },
                orderable: false,
                className: 'text-center'
            }
        ],
        language: { url: 'js/datatable/spanish.json' },
        pageLength: 50,
        processing: true,
        serverSide: false,
        order: [[3, 'desc']],
        dom: '<"top">rt<"bottom"lp>',
        initComplete: function() {
            console.log('Tabla Mis Afiliaciones inicializada correctamente');
        }
    });
});

// Ver detalle de solicitud
function misAfiliacionesVer(id) {
    afiliacionAjax('obtener_mis_afiliaciones', { solicitud_id: id }, function(r) {
        if (!r.error) {
            var d = r.data;
            var html = '<table class="table table-borderless">';
            html += '<tr><td class="fw-bold">Deportista ID:</td><td>' + d.deportista_id + '</td></tr>';
            html += '<tr><td class="fw-bold">Parentesco:</td><td>' + afiliacionEsc(d.parentesco || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Es Principal:</td><td>' + (d.es_principal ? 'Si' : 'No') + '</td></tr>';
            html += '<tr><td class="fw-bold">Estado:</td><td>' + afiliacionEsc(d.estado) + '</td></tr>';
            html += '<tr><td class="fw-bold">Categoria ID:</td><td>' + (d.categoria_id || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">EPS:</td><td>' + afiliacionEsc(d.eps || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Contacto Emergencia:</td><td>' + afiliacionEsc(d.contacto_emergencia || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Telefono Emergencia:</td><td>' + afiliacionEsc(d.telefono_emergencia || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Fecha Solicitud:</td><td>' + (d.fecha_solicitud || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Fecha Aprobacion:</td><td>' + (d.fecha_aprobacion || '-') + '</td></tr>';
            html += '<tr><td class="fw-bold">Observaciones:</td><td>' + afiliacionEsc(d.observaciones || '-') + '</td></tr>';
            if (d.pdf_ruta) {
                html += '<tr><td class="fw-bold">PDF:</td><td><a href="' + page_root + 'descarga.php?archivo=' + d.pdf_ruta + '" target="_blank" class="btn btn-sm btn-link"><i class="ri-file-pdf-line"></i> Ver PDF</a></td></tr>';
            }
            html += '</table>';
            document.getElementById('misAfiliacionesModalContenido').innerHTML = html;
            misAfiliacionesModalVer.show();
        }
    });
}

// Abrir modal subir PDF
function misAfiliacionesAbrirPDF(solicitud_id) {
    document.getElementById('mis_afiliaciones_pdf_solicitud_id').value = solicitud_id;
    document.getElementById('mis_afiliaciones_pdf_archivo').value = '';
    document.getElementById('mis_afiliaciones_pdf_obs').value = '';
    misAfiliacionesModalPDF.show();
}

// Subir PDF
function misAfiliacionesSubirPDF() {
    var archivoInput = document.getElementById('mis_afiliaciones_pdf_archivo');
    var solicitudId = document.getElementById('mis_afiliaciones_pdf_solicitud_id').value;

    if (!archivoInput.files || archivoInput.files.length === 0) {
        afiliacionMostrarMsg('Debe seleccionar un archivo PDF', 'warning');
        return;
    }

    var archivo = archivoInput.files[0];

    // Validacion cliente
    if (archivo.type !== 'application/pdf') {
        afiliacionMostrarMsg('El archivo debe ser PDF', 'warning');
        return;
    }

    if (archivo.size > 10 * 1024 * 1024) {
        afiliacionMostrarMsg('El archivo excede 10 MB', 'warning');
        return;
    }

    var formData = new FormData();
    formData.append('solicitud_id', solicitudId);
    formData.append('pdf', archivo);
    formData.append('observaciones', document.getElementById('mis_afiliaciones_pdf_obs').value);

    afiliacionAjaxFormData('subir_pdf_mis_afiliaciones', formData, function(r) {
        if (!r.error) {
            afiliacionMostrarMsg(r.msg, 'success');
            misAfiliacionesModalPDF.hide();
            if (misAfiliacionesTabla) {
                misAfiliacionesTabla.ajax.reload();
            }
        }
    });
}
</script>