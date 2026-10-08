<?php
// ============================================================
// EVENTOS — Tab 2: Convocatorias de Deportistas por Evento
//
// Reglas AGENTS.md (§10 y §11):
//   - Plantillas con backticks
//   - Una funcion pequena por pieza de HTML
//   - Bucles for tradicionales
//   - if/else en vez de ternarios
//   - Cero emojis
// ============================================================
?>

<!-- Selector de Evento y Boton Convocar -->
<div class="row g-3 mb-3 align-items-center">
    <div class="col-md-6">
        <label class="form-label text-muted small mb-1">Seleccionar Evento / Torneo</label>
        <select id="eveSelectEventoConvocadas" class="form-select" onchange="eveCargarConvocadasEvento()">
            <option value="0">-- Seleccione un evento --</option>
        </select>
    </div>
    <div class="col-md-6 text-md-end pt-3">
        <button type="button" id="btnAbrirModalConvocar" class="btn btn-primary btn-sm" onclick="eveAbrirModalConvocar()" disabled>
            <i class="ri-user-add-line me-1"></i> Convocar Deportistas
        </button>
    </div>
</div>

<!-- Tarjeta de Resumen del Evento Seleccionado -->
<div id="eveTarjetaResumenEvento" class="card border mb-3 bg-light-subtle d-none">
    <div class="card-body py-2">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="text-muted small d-block">Evento seleccionado:</span>
                <strong id="eveResumenNombre" class="text-primary">-</strong>
            </div>
            <div class="col-md-3">
                <span class="text-muted small d-block">Fecha y Lugar:</span>
                <span id="eveResumenFechaLugar" class="small">-</span>
            </div>
            <div class="col-md-3">
                <span class="text-muted small d-block">Tipo y Categoría:</span>
                <span id="eveResumenTipoCat">-</span>
            </div>
            <div class="col-md-2 text-md-end">
                <span class="text-muted small d-block">Total Convocadas:</span>
                <span id="eveResumenTotal" class="badge bg-primary fs-13">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Convocadas -->
<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col">Deportista</th>
                <th scope="col">Identificación</th>
                <th scope="col">Categoría</th>
                <th scope="col">Contacto Emergencia</th>
                <th scope="col" class="text-center">Estado Convocatoria</th>
                <th scope="col" class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody id="eveTablaConvocadasCuerpo">
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    <i class="ri-arrow-up-line me-1"></i> Seleccione un evento arriba para ver las deportistas convocadas
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ============================================================ -->
<!-- MODAL: CONVOCAR DEPORTISTAS (MULTISELECCION)                  -->
<!-- ============================================================ -->
<div class="modal fade" id="modalConvocarDeportistas" tabindex="-1" aria-labelledby="modalConvocarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConvocarLabel">Convocar Deportistas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Filtros del selector -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" id="eveBuscarDeportistaModal" class="form-control form-control-sm" placeholder="Buscar por nombre o identificación..." onkeyup="eveFiltrarDeportistasModal()">
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="eveSeleccionarTodasDeportistas(true)">
                            <i class="ri-checkbox-line me-1"></i> Marcar todas
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-1" onclick="eveSeleccionarTodasDeportistas(false)">
                            <i class="ri-checkbox-blank-line me-1"></i> Desmarcar
                        </button>
                    </div>
                </div>

                <!-- Lista con casillas -->
                <div class="border rounded p-2" style="max-height: 380px; overflow-y: auto;">
                    <div id="eveListaDeportistasModal" class="list-group list-group-flush">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Cargando deportistas activas...
                        </div>
                    </div>
                </div>

                <div class="mt-2 text-muted small d-flex justify-content-between">
                    <span id="eveModalConteoSeleccionadas">0 seleccionadas</span>
                    <span>Solo deportistas con afiliación activa o aprobada</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGuardarConvocatoria" class="btn btn-primary" onclick="eveGuardarConvocatoriaMasiva()">
                    <i class="ri-check-line me-1"></i> Guardar Convocatoria
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCRIPT DEL TAB CONVOCADAS                                    -->
<!-- ============================================================ -->
<script type="text/javascript">
// Variables del tab convocadas
var eveEventoActualId = 0;
var eveEventoActualDatos = null;
var eveDeportistasDisponibles = [];

// 1. Crear fila de la tabla de convocadas con backticks
function eveCrearFilaConvocada(item) {
    var convocada_id = eveEsc(item.convocada_id);
    var nombre = eveEsc(item.nombre_completo);
    var identificacion = eveEsc(item.identificacion);
    var categoria = (item.categoria_nombre ? eveEsc(item.categoria_nombre) : 'Sin categoría');
    var badge_estado = eveBadgeEstadoConvocada(item.estado);

    var contacto_txt = '-';
    if (item.contacto_emergencia_nombre) {
        contacto_txt = eveEsc(item.contacto_emergencia_nombre);
        if (item.contacto_emergencia_telefono) {
            contacto_txt = contacto_txt + ' (' + eveEsc(item.contacto_emergencia_telefono) + ')';
        }
    }

    var opciones_estado = `<select class="form-select form-select-sm d-inline-block w-auto ms-2" onchange="eveCambiarEstadoConvocada(${convocada_id}, this.value)">
        <option value="convocada" ${item.estado === 'convocada' ? 'selected' : ''}>Convocada</option>
        <option value="confirmada" ${item.estado === 'confirmada' ? 'selected' : ''}>Confirmada</option>
        <option value="rechazada" ${item.estado === 'rechazada' ? 'selected' : ''}>Rechazada</option>
        <option value="asistio" ${item.estado === 'asistio' ? 'selected' : ''}>Asistió</option>
        <option value="no_asistio" ${item.estado === 'no_asistio' ? 'selected' : ''}>No asistió</option>
    </select>`;

    return `<tr>
        <td>
            <div class="fw-semibold text-dark">${nombre}</div>
            <small class="text-muted">${item.genero === 'M' ? 'Masculino' : (item.genero === 'F' ? 'Femenino' : 'Mixto')}</small>
        </td>
        <td><code>${identificacion}</code></td>
        <td><span class="badge bg-light text-dark">${categoria}</span></td>
        <td><small class="text-muted">${contacto_txt}</small></td>
        <td class="text-center">
            ${badge_estado}
            ${opciones_estado}
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-ghost-danger btn-icon btn-sm" title="Quitar de convocatoria" onclick="eveQuitarConvocada(${convocada_id}, '${nombre}')">
                <i class="ri-user-unfollow-line"></i>
            </button>
        </td>
    </tr>`;
}

// 2. Renderizar lista de convocadas en la tabla
function eveRenderizarConvocadas(lista) {
    var cuerpo = document.getElementById('eveTablaConvocadasCuerpo');
    if (!cuerpo) {
        return;
    }

    if (!lista || lista.length === 0) {
        cuerpo.innerHTML = `<tr>
            <td colspan="6" class="text-center py-4 text-muted">
                <i class="ri-team-line fs-24 d-block mb-2"></i>
                No hay deportistas convocadas para este evento aún.
                <br>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="eveAbrirModalConvocar()">
                    <i class="ri-user-add-line me-1"></i> Convocar ahora
                </button>
            </td>
        </tr>`;
        return;
    }

    var html_final = '';
    for (var i = 0; i < lista.length; i++) {
        html_final = html_final + eveCrearFilaConvocada(lista[i]);
    }
    cuerpo.innerHTML = html_final;
}

// 3. Cargar combo de eventos para seleccionar
function eveCargarEventosComboConvocadas() {
    eveAjax('listar', {}, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var select = document.getElementById('eveSelectEventoConvocadas');
            if (select) {
                var valor_previo = select.value;
                var html_opciones = '<option value="0">-- Seleccione un evento --</option>';
                for (var i = 0; i < respuesta.data.length; i++) {
                    var ev = respuesta.data[i];
                    var fecha_txt = eveFormatearFecha(ev.fecha);
                    html_opciones = html_opciones + `<option value="${ev.id}">${eveEsc(ev.nombre)} (${fecha_txt} - ${eveEsc(ev.lugar)})</option>`;
                }
                select.innerHTML = html_opciones;
                if (valor_previo && valor_previo !== '0') {
                    select.value = valor_previo;
                }
            }
        }
    });
}

// 4. Seleccionar un evento especifico desde otra vista
function eveSeleccionarEventoConvocadas(evento_id) {
    var select = document.getElementById('eveSelectEventoConvocadas');
    if (select) {
        select.value = evento_id;
        eveCargarConvocadasEvento();
    }
}

// 5. Cargar convocadas para el evento seleccionado
function eveCargarConvocadasEvento() {
    var select = document.getElementById('eveSelectEventoConvocadas');
    var btnConvocar = document.getElementById('btnAbrirModalConvocar');
    var tarjetaResumen = document.getElementById('eveTarjetaResumenEvento');

    if (!select) {
        return;
    }

    var evento_id = parseInt(select.value);
    eveEventoActualId = evento_id;

    if (isNaN(evento_id) || evento_id <= 0) {
        if (btnConvocar) { btnConvocar.disabled = true; }
        if (tarjetaResumen) { tarjetaResumen.classList.add('d-none'); }
        document.getElementById('eveTablaConvocadasCuerpo').innerHTML = `<tr>
            <td colspan="6" class="text-center py-4 text-muted">
                <i class="ri-arrow-up-line me-1"></i> Seleccione un evento arriba para ver las deportistas convocadas
            </td>
        </tr>`;
        return;
    }

    if (btnConvocar) { btnConvocar.disabled = false; }

    // Obtener datos del evento para el resumen
    eveAjax('obtener', { id: evento_id }, function(resp_evento) {
        if (!resp_evento.error && resp_evento.data) {
            eveEventoActualDatos = resp_evento.data;
            if (tarjetaResumen) {
                tarjetaResumen.classList.remove('d-none');
                document.getElementById('eveResumenNombre').textContent = resp_evento.data.nombre;
                var f_txt = eveFormatearFecha(resp_evento.data.fecha);
                if (resp_evento.data.fecha_fin) {
                    f_txt = f_txt + ' al ' + eveFormatearFecha(resp_evento.data.fecha_fin);
                }
                document.getElementById('eveResumenFechaLugar').textContent = f_txt + ' | ' + resp_evento.data.lugar;

                var cat_nombre = (resp_evento.data.categoria_nombre ? resp_evento.data.categoria_nombre : 'General');
                document.getElementById('eveResumenTipoCat').innerHTML = eveBadgeTipo(resp_evento.data.tipo) + ' <span class="badge bg-light text-dark ms-1">' + eveEsc(cat_nombre) + '</span>';
                document.getElementById('eveResumenTotal').textContent = resp_evento.data.total_convocadas || 0;
            }
        }
    });

    // Cargar tabla de convocadas
    var cuerpo = document.getElementById('eveTablaConvocadasCuerpo');
    cuerpo.innerHTML = `<tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            Cargando convocadas...
        </td>
    </tr>`;

    eveAjax('listar_convocadas', { evento_id: evento_id }, function(respuesta) {
        if (!respuesta.error) {
            eveRenderizarConvocadas(respuesta.data);
            if (tarjetaResumen && respuesta.data) {
                document.getElementById('eveResumenTotal').textContent = respuesta.data.length;
            }
        }
    });
}

// 6. Cambiar estado de una convocada
function eveCambiarEstadoConvocada(convocada_id, nuevo_estado) {
    eveAjax('cambiar_estado_convocada', { convocada_id: convocada_id, estado: nuevo_estado }, function(respuesta) {
        if (!respuesta.error) {
            eveMostrarMsg(respuesta.msg, 'success');
            eveCargarConvocadasEvento();
        }
    });
}

// 7. Quitar a una deportista de la convocatoria
function eveQuitarConvocada(convocada_id, nombre) {
    Swal.fire({
        title: '¿Remover de la convocatoria?',
        text: 'Se quitará a "' + nombre + '" de este evento.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, remover',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#405189'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            eveAjax('quitar_convocada', { convocada_id: convocada_id }, function(respuesta) {
                if (!respuesta.error) {
                    eveMostrarMsg(respuesta.msg, 'success');
                    eveCargarConvocadasEvento();
                }
            });
        }
    });
}

// 8. Crear item individual de deportista en el modal con backticks
function eveCrearItemDeportistaModal(item) {
    var dep_id = eveEsc(item.deportista_id);
    var nombre = eveEsc(item.nombre_completo);
    var identificacion = eveEsc(item.identificacion);
    var categoria = (item.categoria_nombre ? eveEsc(item.categoria_nombre) : 'Sin categoría');

    var ya_convocada = (item.convocada_activa && parseInt(item.convocada_activa) === 1);
    var checked_attr = ya_convocada ? 'checked disabled' : '';
    var badge_estado = ya_convocada ? '<span class="badge bg-success-subtle text-success ms-2">Ya convocada</span>' : '';

    return `<label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 eve-item-dep" data-nombre="${nombre.toLowerCase()}" data-identificacion="${identificacion}">
        <div class="form-check mb-0">
            <input class="form-check-input eve-check-deportista" type="checkbox" value="${dep_id}" id="chkDep${dep_id}" ${checked_attr} onchange="eveActualizarConteoModal()">
            <label class="form-check-label ms-2" for="chkDep${dep_id}">
                <strong class="text-dark">${nombre}</strong>
                <small class="text-muted d-block">${identificacion} &bull; ${categoria}</small>
            </label>
        </div>
        <div>${badge_estado}</div>
    </label>`;
}

// 9. Renderizar lista de deportistas en el modal
function eveRenderizarDeportistasModal(lista) {
    var contenedor = document.getElementById('eveListaDeportistasModal');
    if (!contenedor) {
        return;
    }

    if (!lista || lista.length === 0) {
        contenedor.innerHTML = '<div class="text-center py-4 text-muted">No se encontraron deportistas activas para convocar.</div>';
        return;
    }

    var html_final = '';
    for (var i = 0; i < lista.length; i++) {
        html_final = html_final + eveCrearItemDeportistaModal(lista[i]);
    }
    contenedor.innerHTML = html_final;
    eveActualizarConteoModal();
}

// 10. Abrir modal para convocar deportistas
function eveAbrirModalConvocar() {
    if (eveEventoActualId <= 0) {
        eveMostrarMsg('Seleccione un evento primero', 'warning');
        return;
    }

    var cat_id = 0;
    if (eveEventoActualDatos && eveEventoActualDatos.categoria_id) {
        cat_id = eveEventoActualDatos.categoria_id;
    }

    var buscador = document.getElementById('eveBuscarDeportistaModal');
    if (buscador) {
        buscador.value = '';
    }

    var contenedor = document.getElementById('eveListaDeportistasModal');
    if (contenedor) {
        contenedor.innerHTML = `<div class="text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            Cargando deportistas...
        </div>`;
    }

    var modalEl = document.getElementById('modalConvocarDeportistas');
    var modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInst.show();

    eveAjax('listar_deportistas_convocatoria', { evento_id: eveEventoActualId, categoria_id: 0 }, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            eveDeportistasDisponibles = respuesta.data;
            eveRenderizarDeportistasModal(respuesta.data);
        }
    });
}

// 11. Filtrar lista del modal por texto de busqueda
function eveFiltrarDeportistasModal() {
    var input = document.getElementById('eveBuscarDeportistaModal');
    var filtro = (input ? input.value.toLowerCase().trim() : '');

    var items = document.querySelectorAll('.eve-item-dep');
    for (var i = 0; i < items.length; i++) {
        var el = items[i];
        var nombre = el.getAttribute('data-nombre') || '';
        var ident = el.getAttribute('data-identificacion') || '';

        if (nombre.indexOf(filtro) !== -1 || ident.indexOf(filtro) !== -1) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    }
}

// 12. Seleccionar o deseleccionar todas las no-deshabilitadas
function eveSeleccionarTodasDeportistas(marcar) {
    var checks = document.querySelectorAll('.eve-check-deportista:not(:disabled)');
    for (var i = 0; i < checks.length; i++) {
        checks[i].checked = marcar;
    }
    eveActualizarConteoModal();
}

// 13. Actualizar conteo de seleccionadas en el modal
function eveActualizarConteoModal() {
    var checks = document.querySelectorAll('.eve-check-deportista:checked:not(:disabled)');
    var label = document.getElementById('eveModalConteoSeleccionadas');
    if (label) {
        label.textContent = checks.length + ' nuevas seleccionadas para convocar';
    }
}

// 14. Guardar convocatoria masiva
function eveGuardarConvocatoriaMasiva() {
    var checks = document.querySelectorAll('.eve-check-deportista:checked:not(:disabled)');
    var ids_seleccionados = [];

    for (var i = 0; i < checks.length; i++) {
        ids_seleccionados.push(checks[i].value);
    }

    if (ids_seleccionados.length === 0) {
        eveMostrarMsg('Debe marcar al menos una deportista para convocar', 'warning');
        return;
    }

    var boton = document.getElementById('btnGuardarConvocatoria');
    if (boton) {
        boton.disabled = true;
    }

    eveAjax('convocar_masivo', {
        evento_id: eveEventoActualId,
        deportista_ids: ids_seleccionados.join(',')
    }, function(respuesta) {
        if (boton) {
            boton.disabled = false;
        }
        if (!respuesta.error) {
            eveMostrarMsg(respuesta.msg, 'success');
            var modalEl = document.getElementById('modalConvocarDeportistas');
            var modalInst = bootstrap.Modal.getInstance(modalEl);
            if (modalInst) {
                modalInst.hide();
            }
            eveCargarConvocadasEvento();
            if (typeof eveCargarEventos === 'function') {
                eveCargarEventos();
            }
        }
    });
}

// Inicializar combos al cargar
document.addEventListener('DOMContentLoaded', function() {
    eveCargarEventosComboConvocadas();
});
</script>
