<?php
// ============================================================
// EVENTOS — Tab 1: Listado de Eventos y Torneos
//
// Reglas AGENTS.md (§10 y §11):
//   - Plantillas con backticks
//   - Una funcion pequena por pieza de HTML
//   - Bucles for tradicionales
//   - if/else en vez de ternarios
//   - Cero emojis
// ============================================================
?>

<!-- Barra de filtros y boton nuevo -->
<div class="row g-3 mb-3 align-items-center">
    <div class="col-md-3">
        <label class="form-label text-muted small mb-1">Filtrar por tipo</label>
        <select id="eveFiltroTipo" class="form-select form-select-sm" onchange="eveCargarEventos()">
            <option value="todos">Todos los tipos</option>
            <option value="torneo">Torneo</option>
            <option value="entrenamiento">Entrenamiento</option>
            <option value="salida">Salida</option>
            <option value="viaje">Viaje</option>
            <option value="reunion">Reunión</option>
            <option value="actividad">Actividad</option>
            <option value="otro">Otro</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label text-muted small mb-1">Filtrar por categoría</label>
        <select id="eveFiltroCategoria" class="form-select form-select-sm" onchange="eveCargarEventos()">
            <option value="0">Todas las categorías</option>
        </select>
    </div>
    <div class="col-md-6 text-md-end pt-3">
        <button type="button" class="btn btn-primary btn-sm" onclick="eveAbrirModalCrear()">
            <i class="ri-add-line me-1"></i> Nuevo Evento
        </button>
    </div>
</div>

<!-- Contenedor de la tabla de eventos -->
<div class="table-responsive">
    <table class="table table-hover align-middle table-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col">Evento</th>
                <th scope="col">Tipo</th>
                <th scope="col">Categoría</th>
                <th scope="col">Fecha</th>
                <th scope="col">Lugar</th>
                <th scope="col" class="text-center">Convocadas</th>
                <th scope="col" class="text-center">Autorización</th>
                <th scope="col" class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody id="eveTablaCuerpo">
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Cargando eventos...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ============================================================ -->
<!-- MODAL: CREAR / EDITAR EVENTO                                 -->
<!-- ============================================================ -->
<div class="modal fade" id="modalEventoForm" tabindex="-1" aria-labelledby="modalEventoFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEventoFormLabel">Nuevo Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEvento" onsubmit="eveGuardarEvento(event)">
                <div class="modal-body">
                    <input type="hidden" id="eveFormId" value="0">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label required">Nombre del evento</label>
                            <input type="text" id="eveFormNombre" class="form-control" placeholder="Ej: Torneo Nacional Sub-15 Medellín" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Tipo de evento</label>
                            <select id="eveFormTipo" class="form-select" required>
                                <option value="torneo">Torneo</option>
                                <option value="entrenamiento">Entrenamiento</option>
                                <option value="salida">Salida</option>
                                <option value="viaje">Viaje</option>
                                <option value="reunion">Reunión</option>
                                <option value="actividad">Actividad</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Categoría asignada</label>
                            <select id="eveFormCategoria" class="form-select">
                                <option value="0">General / Todas las categorías</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Lugar / Sede</label>
                            <input type="text" id="eveFormLugar" class="form-control" placeholder="Ej: Coliseo Yesid Santos" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Fecha inicio</label>
                            <input type="date" id="eveFormFecha" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha fin (opcional si es de varios días)</label>
                            <input type="date" id="eveFormFechaFin" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Horario / Programación</label>
                            <input type="text" id="eveFormHorario" class="form-control" placeholder="Ej: Sábado 8:00 AM - 12:00 PM / Domingo 2:00 PM">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Recomendaciones e instrucciones</label>
                            <textarea id="eveFormRecomendaciones" class="form-control" rows="3" placeholder="Ej: Llevar uniforme alterno, hidratación, documento de identidad..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch form-switch-md">
                                <input class="form-check-input" type="checkbox" id="eveFormRequiereAutorizacion">
                                <label class="form-check-label" for="eveFormRequiereAutorizacion">
                                    Requiere autorización firmada de los padres / acudientes
                                </label>
                            </div>
                            <small class="text-muted d-block ps-4">Si se activa, el acudiente deberá firmar la autorización correspondiente para su deportista convocada.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarEvento" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Guardar Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: DETALLE DEL EVENTO                                   -->
<!-- ============================================================ -->
<div class="modal fade" id="modalEventoDetalle" tabindex="-1" aria-labelledby="modalEventoDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEventoDetalleLabel">Detalle del Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="eveDetalleCuerpo">
                <!-- Se llena dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCRIPT DEL TAB LISTA                                         -->
<!-- ============================================================ -->
<script type="text/javascript">
// Variable global con lista de categorias activas
var eveListaCategorias = [];

// 1. Crear fila de la tabla de eventos con backticks
function eveCrearFilaEvento(item) {
    var id = eveEsc(item.id);
    var nombre = eveEsc(item.nombre);
    var tipo_badge = eveBadgeTipo(item.tipo);
    var lugar = eveEsc(item.lugar);
    var fecha_formateada = eveFormatearFecha(item.fecha);

    if (item.fecha_fin) {
        fecha_formateada = fecha_formateada + ' al ' + eveFormatearFecha(item.fecha_fin);
    }

    var categoria_txt = 'General';
    if (item.categoria_nombre) {
        categoria_txt = eveEsc(item.categoria_nombre);
    }

    var total_convocadas = parseInt(item.total_convocadas);
    if (isNaN(total_convocadas)) {
        total_convocadas = 0;
    }

    var autorizacion_badge = '<span class="badge bg-light text-muted">No requerida</span>';
    if (parseInt(item.requiere_autorizacion) === 1) {
        autorizacion_badge = '<span class="badge bg-warning-subtle text-warning"><i class="ri-shield-check-line me-1"></i> Requerida</span>';
    }

    return `<tr>
        <td>
            <div class="fw-semibold text-dark">${nombre}</div>
            <small class="text-muted"><i class="ri-map-pin-line me-1"></i>${lugar}</small>
        </td>
        <td>${tipo_badge}</td>
        <td><span class="badge bg-light text-dark">${categoria_txt}</span></td>
        <td><i class="ri-calendar-line text-muted me-1"></i>${fecha_formateada}</td>
        <td>${lugar}</td>
        <td class="text-center">
            <button type="button" class="btn btn-soft-primary btn-sm" onclick="eveIrAConvocadas(${id})">
                <i class="ri-team-line me-1"></i> ${total_convocadas} convocadas
            </button>
        </td>
        <td class="text-center">${autorizacion_badge}</td>
        <td class="text-end">
            <button type="button" class="btn btn-ghost-info btn-icon btn-sm" title="Ver detalle" onclick="eveVerDetalle(${id})">
                <i class="ri-eye-line"></i>
            </button>
            <button type="button" class="btn btn-ghost-primary btn-icon btn-sm" title="Editar" onclick="eveEditarEvento(${id})">
                <i class="ri-pencil-line"></i>
            </button>
            <button type="button" class="btn btn-ghost-danger btn-icon btn-sm" title="Eliminar" onclick="eveEliminarEvento(${id}, '${nombre}')">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    </tr>`;
}

// 2. Renderizar lista de eventos
function eveRenderizarEventos(lista) {
    var cuerpo = document.getElementById('eveTablaCuerpo');
    if (!cuerpo) {
        return;
    }

    if (!lista || lista.length === 0) {
        cuerpo.innerHTML = `<tr>
            <td colspan="8" class="text-center py-4 text-muted">
                <i class="ri-calendar-event-line fs-24 d-block mb-2"></i>
                No se encontraron eventos registrados
            </td>
        </tr>`;
        return;
    }

    var html_final = '';
    for (var i = 0; i < lista.length; i++) {
        html_final = html_final + eveCrearFilaEvento(lista[i]);
    }
    cuerpo.innerHTML = html_final;
}

// 3. Cargar eventos desde el backend
function eveCargarEventos() {
    var tipo_select = document.getElementById('eveFiltroTipo');
    var cat_select = document.getElementById('eveFiltroCategoria');

    var tipo_val = 'todos';
    if (tipo_select) {
        tipo_val = tipo_select.value;
    }

    var cat_val = 0;
    if (cat_select) {
        cat_val = cat_select.value;
    }

    var cuerpo = document.getElementById('eveTablaCuerpo');
    if (cuerpo) {
        cuerpo.innerHTML = `<tr>
            <td colspan="8" class="text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                Cargando eventos...
            </td>
        </tr>`;
    }

    eveAjax('listar', { tipo: tipo_val, categoria_id: cat_val }, function(respuesta) {
        if (!respuesta.error) {
            eveRenderizarEventos(respuesta.data);
        }
    });
}

// 4. Cargar combo de categorias
function eveCargarCategorias() {
    eveAjax('listar_categorias', {}, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            eveListaCategorias = respuesta.data;

            // Llenar select de filtro
            var select_filtro = document.getElementById('eveFiltroCategoria');
            if (select_filtro) {
                var html_filtro = '<option value="0">Todas las categorías</option>';
                for (var i = 0; i < eveListaCategorias.length; i++) {
                    var cat = eveListaCategorias[i];
                    html_filtro = html_filtro + `<option value="${cat.id}">${eveEsc(cat.nombre)}</option>`;
                }
                select_filtro.innerHTML = html_filtro;
            }

            // Llenar select del formulario
            var select_form = document.getElementById('eveFormCategoria');
            if (select_form) {
                var html_form = '<option value="0">General / Todas las categorías</option>';
                for (var j = 0; j < eveListaCategorias.length; j++) {
                    var c = eveListaCategorias[j];
                    html_form = html_form + `<option value="${c.id}">${eveEsc(c.nombre)}</option>`;
                }
                select_form.innerHTML = html_form;
            }
        }
    });
}

// 5. Abrir modal para crear nuevo evento
function eveAbrirModalCrear() {
    document.getElementById('eveFormId').value = '0';
    document.getElementById('eveFormNombre').value = '';
    document.getElementById('eveFormTipo').value = 'torneo';
    document.getElementById('eveFormCategoria').value = '0';
    document.getElementById('eveFormLugar').value = '';
    document.getElementById('eveFormFecha').value = '';
    document.getElementById('eveFormFechaFin').value = '';
    document.getElementById('eveFormHorario').value = '';
    document.getElementById('eveFormRecomendaciones').value = '';
    document.getElementById('eveFormRequiereAutorizacion').checked = false;

    document.getElementById('modalEventoFormLabel').textContent = 'Nuevo Evento';

    var modalEl = document.getElementById('modalEventoForm');
    var modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInst.show();
}

// 6. Abrir modal para editar evento
function eveEditarEvento(id) {
    eveAjax('obtener', { id: id }, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var ev = respuesta.data;
            document.getElementById('eveFormId').value = ev.id;
            document.getElementById('eveFormNombre').value = ev.nombre;
            document.getElementById('eveFormTipo').value = ev.tipo;

            if (ev.categoria_id) {
                document.getElementById('eveFormCategoria').value = ev.categoria_id;
            } else {
                document.getElementById('eveFormCategoria').value = '0';
            }

            document.getElementById('eveFormLugar').value = ev.lugar;
            document.getElementById('eveFormFecha').value = ev.fecha;
            document.getElementById('eveFormFechaFin').value = (ev.fecha_fin ? ev.fecha_fin : '');
            document.getElementById('eveFormHorario').value = (ev.horario ? ev.horario : '');
            document.getElementById('eveFormRecomendaciones').value = (ev.recomendaciones ? ev.recomendaciones : '');
            document.getElementById('eveFormRequiereAutorizacion').checked = (parseInt(ev.requiere_autorizacion) === 1);

            document.getElementById('modalEventoFormLabel').textContent = 'Editar Evento';

            var modalEl = document.getElementById('modalEventoForm');
            var modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInst.show();
        }
    });
}

// 7. Guardar evento (crear o editar)
function eveGuardarEvento(evento) {
    evento.preventDefault();

    var boton = document.getElementById('btnGuardarEvento');
    if (boton) {
        boton.disabled = true;
    }

    var id = document.getElementById('eveFormId').value;
    var nombre = document.getElementById('eveFormNombre').value;
    var tipo = document.getElementById('eveFormTipo').value;
    var categoria_id = document.getElementById('eveFormCategoria').value;
    var lugar = document.getElementById('eveFormLugar').value;
    var fecha = document.getElementById('eveFormFecha').value;
    var fecha_fin = document.getElementById('eveFormFechaFin').value;
    var horario = document.getElementById('eveFormHorario').value;
    var recomendaciones = document.getElementById('eveFormRecomendaciones').value;
    var requiere_autorizacion = document.getElementById('eveFormRequiereAutorizacion').checked ? 1 : 0;

    var datos = {
        id: id,
        nombre: nombre,
        tipo: tipo,
        categoria_id: categoria_id,
        lugar: lugar,
        fecha: fecha,
        fecha_fin: fecha_fin,
        horario: horario,
        recomendaciones: recomendaciones,
        requiere_autorizacion: requiere_autorizacion
    };

    eveAjax('guardar', datos, function(respuesta) {
        if (boton) {
            boton.disabled = false;
        }
        if (!respuesta.error) {
            eveMostrarMsg(respuesta.msg, 'success');
            var modalEl = document.getElementById('modalEventoForm');
            var modalInst = bootstrap.Modal.getInstance(modalEl);
            if (modalInst) {
                modalInst.hide();
            }
            eveCargarEventos();
            if (typeof eveCargarEventosComboConvocadas === 'function') {
                eveCargarEventosComboConvocadas();
            }
        }
    });
}

// 8. Ver detalle de evento
function eveVerDetalle(id) {
    eveAjax('obtener', { id: id }, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var ev = respuesta.data;
            var fecha_texto = eveFormatearFecha(ev.fecha);
            if (ev.fecha_fin) {
                fecha_texto = fecha_texto + ' al ' + eveFormatearFecha(ev.fecha_fin);
            }

            var cat_texto = 'General / Todas';
            if (ev.categoria_nombre) {
                cat_texto = eveEsc(ev.categoria_nombre);
            }

            var horario_texto = (ev.horario ? eveEsc(ev.horario) : 'No especificado');
            var rec_texto = (ev.recomendaciones ? eveEsc(ev.recomendaciones) : 'Sin recomendaciones especiales');

            var html_detalle = `<div class="mb-3">
                <h6 class="fw-bold text-primary mb-1">${eveEsc(ev.nombre)}</h6>
                <div>${eveBadgeTipo(ev.tipo)} <span class="badge bg-light text-dark ms-1">${cat_texto}</span></div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <small class="text-muted d-block">Fecha</small>
                    <span class="fw-medium">${fecha_texto}</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Lugar</small>
                    <span class="fw-medium">${eveEsc(ev.lugar)}</span>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block">Horario</small>
                    <span>${horario_texto}</span>
                </div>
            </div>
            <div class="mb-3">
                <small class="text-muted d-block mb-1">Recomendaciones</small>
                <div class="p-2 bg-light rounded text-muted small">${rec_texto}</div>
            </div>
            <div class="p-2 border rounded d-flex justify-content-between align-items-center">
                <span>Total deportistas convocadas:</span>
                <span class="badge bg-primary fs-12">${ev.total_convocadas || 0}</span>
            </div>`;

            document.getElementById('eveDetalleCuerpo').innerHTML = html_detalle;

            var modalEl = document.getElementById('modalEventoDetalle');
            var modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInst.show();
        }
    });
}

// 9. Eliminar evento con confirmacion Swal
function eveEliminarEvento(id, nombre) {
    Swal.fire({
        title: '¿Eliminar evento?',
        text: 'Se ocultará el evento "' + nombre + '" del sistema.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#405189'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            eveAjax('eliminar', { id: id }, function(respuesta) {
                if (!respuesta.error) {
                    eveMostrarMsg(respuesta.msg, 'success');
                    eveCargarEventos();
                    if (typeof eveCargarEventosComboConvocadas === 'function') {
                        eveCargarEventosComboConvocadas();
                    }
                }
            });
        }
    });
}

// 10. Cambiar a la pestana de convocadas para un evento
function eveIrAConvocadas(evento_id) {
    var tabBoton = document.getElementById('tab-convocadas-link');
    if (tabBoton) {
        var tabInst = bootstrap.Tab.getOrCreateInstance(tabBoton);
        tabInst.show();
    }
    if (typeof eveSeleccionarEventoConvocadas === 'function') {
        eveSeleccionarEventoConvocadas(evento_id);
    }
}

// Inicializar lista y categorias al cargar
document.addEventListener('DOMContentLoaded', function() {
    eveCargarCategorias();
    eveCargarEventos();
});
</script>
