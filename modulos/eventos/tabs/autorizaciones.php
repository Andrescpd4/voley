<?php
// ============================================================
// EVENTOS / TAB AUTORIZACIONES — Matriz por evento (Fase B)
//
// Solo lectura: admin (1, 4) y entrenador (2).
// Las respuestas las da el acudiente en inicio (Fase C).
// El boton de firma lleva accion-ver_firma_evento: pie.php lo
// oculta solo si el rol no tiene ese permiso (sin roles fijos).
// ============================================================
?>

<div class="row">
    <div class="col-12">
        <!-- Selector de evento -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-medium" for="eveMatrizEvento">Evento</label>
                        <select class="form-select" id="eveMatrizEvento" onchange="eveCargarMatriz()">
                            <option value="0">Seleccione un evento</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div id="eveMatrizResumen" class="d-flex gap-2 flex-wrap"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de la matriz -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="ri-shield-check-line me-1"></i> Autorizaciones por deportista
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Deportista</th>
                                <th>Documento</th>
                                <th>Acudiente</th>
                                <th>Estado</th>
                                <th>Respuesta</th>
                                <th>Fecha respuesta</th>
                                <th class="text-center">Firma</th>
                            </tr>
                        </thead>
                        <tbody id="eveMatrizCuerpo">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Seleccione un evento para ver la matriz.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver la firma de una respuesta -->
<div class="modal fade accion-ver_firma_evento" id="eveFirmaModal" tabindex="-1" aria-labelledby="eveFirmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eveFirmaModalLabel">
                    <i class="ri-pen-nib-line me-1"></i> Firma del acudiente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <img id="eveFirmaImagen" src="" alt="Firma manuscrita del acudiente" class="img-fluid border rounded" style="max-height: 220px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// Cargar la lista de eventos en el selector de la matriz
function eveCargarEventosMatriz() {
    eveAjax('listar', {}, function(respuesta) {
        var selector = document.getElementById('eveMatrizEvento');
        if (!selector) {
            return;
        }
        var html_opciones = '<option value="0">Seleccione un evento</option>';
        if (respuesta.error === false && respuesta.data) {
            for (var i = 0; i < respuesta.data.length; i++) {
                var evento = respuesta.data[i];
                html_opciones = html_opciones + `<option value="${evento.id}">${eveEsc(evento.nombre)} (${eveFormatearFecha(evento.fecha)})</option>`;
            }
        }
        selector.innerHTML = html_opciones;
    });
}

// Insignia de la respuesta del acudiente
function eveBadgeRespuesta(fila) {
    // Si el evento no exige autorizacion, no hay nada que responder
    if (fila.requiere_autorizacion === 0 || fila.requiere_autorizacion === '0') {
        return '<span class="badge bg-secondary-subtle text-secondary">No requiere</span>';
    }
    if (fila.respuesta_id === null || fila.respuesta_id === 0) {
        return '<span class="badge bg-warning-subtle text-warning"><i class="ri-time-line me-1"></i> Pendiente</span>';
    }
    if (fila.autoriza === 1 || fila.autoriza === '1') {
        return '<span class="badge bg-success-subtle text-success"><i class="ri-check-line me-1"></i> Autorizó</span>';
    }
    return '<span class="badge bg-danger-subtle text-danger"><i class="ri-close-line me-1"></i> No autorizó</span>';
}

// Armar una fila de la matriz
function eveCrearFilaMatriz(fila) {
    var deportista = eveEsc(fila.deportista_nombre);
    var documento = eveEsc(fila.deportista_documento);
    var acudiente = eveEsc(fila.acudiente_nombre);
    if (acudiente === '') {
        acudiente = '<span class="text-muted">Sin vinculo</span>';
    }
    var estado = eveBadgeEstadoConvocada(fila.estado_convocada);
    var respuesta = eveBadgeRespuesta(fila);
    var fecha = eveEsc(fila.fecha_respuesta);
    if (fecha === '') {
        fecha = '<span class="text-muted">-</span>';
    }

    // Boton de firma solo si hay imagen; el permiso lo controla accion-ver_firma_evento
    var celda_firma = '<span class="text-muted">-</span>';
    if (fila.tiene_firma === 1 || fila.tiene_firma === '1') {
        celda_firma = `<button type="button" class="btn btn-sm btn-outline-secondary accion-ver_firma_evento" onclick="eveVerFirmaEvento(${fila.respuesta_id})" title="Ver firma manuscrita" aria-label="Ver firma manuscrita"><i class="ri-eye-line"></i></button>`;
    }

    return `<tr>
        <td class="fw-medium">${deportista}</td>
        <td>${documento}</td>
        <td>${acudiente}</td>
        <td>${estado}</td>
        <td>${respuesta}</td>
        <td>${fecha}</td>
        <td class="text-center">${celda_firma}</td>
    </tr>`;
}

// Pintar el resumen de conteos sobre la matriz
function evePintarResumenMatriz(filas) {
    var contenedor = document.getElementById('eveMatrizResumen');
    if (!contenedor) {
        return;
    }
    var total = filas.length;
    var autorizadas = 0;
    var negadas = 0;
    var pendientes = 0;
    for (var i = 0; i < filas.length; i++) {
        var fila = filas[i];
        if (fila.requiere_autorizacion === 0 || fila.requiere_autorizacion === '0') {
            continue;
        }
        if (fila.respuesta_id === null || fila.respuesta_id === 0) {
            pendientes = pendientes + 1;
        } else if (fila.autoriza === 1 || fila.autoriza === '1') {
            autorizadas = autorizadas + 1;
        } else {
            negadas = negadas + 1;
        }
    }
    contenedor.innerHTML = `<span class="badge bg-primary-subtle text-primary">Convocadas: ${total}</span>
        <span class="badge bg-success-subtle text-success">Autorizó: ${autorizadas}</span>
        <span class="badge bg-danger-subtle text-danger">No autorizó: ${negadas}</span>
        <span class="badge bg-warning-subtle text-warning">Pendientes: ${pendientes}</span>`;
}

// Cargar la matriz del evento elegido
function eveCargarMatriz() {
    var selector = document.getElementById('eveMatrizEvento');
    var cuerpo = document.getElementById('eveMatrizCuerpo');
    if (!selector || !cuerpo) {
        return;
    }
    var evento_id = parseInt(selector.value, 10);
    if (!evento_id) {
        cuerpo.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Seleccione un evento para ver la matriz.</td></tr>';
        document.getElementById('eveMatrizResumen').innerHTML = '';
        return;
    }
    cuerpo.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Cargando matriz...</td></tr>';
    eveAjax('listar_autorizaciones', { evento_id: evento_id }, function(respuesta) {
        if (respuesta.error !== false || !respuesta.data) {
            cuerpo.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No se pudo cargar la matriz.</td></tr>';
            return;
        }
        var filas = respuesta.data;
        if (filas.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Este evento no tiene deportistas convocadas.</td></tr>';
            document.getElementById('eveMatrizResumen').innerHTML = '';
            return;
        }
        var html_final = '';
        for (var i = 0; i < filas.length; i++) {
            html_final = html_final + eveCrearFilaMatriz(filas[i]);
        }
        cuerpo.innerHTML = html_final;
        evePintarResumenMatriz(filas);
    });
}

// Ver la firma manuscrita en el modal (requiere permiso ver_firma_evento)
function eveVerFirmaEvento(respuesta_id) {
    eveAjax('ver_firma_evento', { id: respuesta_id }, function(respuesta) {
        if (respuesta.error !== false || !respuesta.data_url) {
            eveMostrarMsg('No se pudo cargar la firma', 'error');
            return;
        }
        document.getElementById('eveFirmaImagen').src = respuesta.data_url;
        var modal_elemento = document.getElementById('eveFirmaModal');
        var modal_instancia = bootstrap.Modal.getOrCreateInstance(modal_elemento);
        modal_instancia.show();
    });
}

// Cargar el selector al abrir el modulo
eveCargarEventosMatriz();
</script>
