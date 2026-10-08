<?php
// consentimientos/tabs/tipos.php - Tab 1: Documentos de consentimiento
// Solo admin (roles 1 y 4), verificado en formulario.php
?>

<!-- ===== TAB 1: LISTA DE DOCUMENTOS ===== -->
<div class="card border">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="card-title mb-0">
            <i class="ri-file-text-line me-1"></i> Documentos de consentimiento
        </h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary accion-listar" onclick="consTiposCargar()" title="Actualizar lista">
                <i class="ri-refresh-line me-1"></i> Actualizar
            </button>
            <button type="button" class="btn btn-sm btn-primary accion-agregar" onclick="consTiposNuevo()" title="Crear documento">
                <i class="ri-add-line me-1"></i> Nuevo documento
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Orden</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Clase</th>
                        <th>Versión</th>
                        <th>Popup</th>
                        <th>Estado</th>
                        <th style="width: 150px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="consTiposCuerpo" aria-live="polite">
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="ms-2">Cargando documentos...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: CREAR / EDITAR DOCUMENTO                              -->
<!-- ============================================================ -->
<div class="modal fade" id="consTiposModal" tabindex="-1" aria-labelledby="consTiposModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title text-primary" id="consTiposModalLabel">
                    <i class="ri-file-edit-line me-1"></i> <span id="consTiposModalTitulo">Nuevo documento</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="consTiposForm" autocomplete="off">
                    <input type="hidden" id="consTipoId" value="0">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="consTipoNombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="consTipoNombre" maxlength="200" placeholder="Ej: Política de Privacidad">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="consTipoSlug">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="consTipoSlug" maxlength="50" placeholder="solo_minusculas">
                            <div class="form-text">Solo letras minúsculas, números y guion bajo.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="consTipoResumen">Resumen corto</label>
                            <input type="text" class="form-control" id="consTipoResumen" maxlength="300" placeholder="Una frase que explique de qué trata">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="consTipoContenido">Contenido <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="consTipoContenido" rows="8" placeholder="Texto legal completo del documento"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="consTipoVersion">Versión <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="consTipoVersion" maxlength="20" value="1.0">
                            <div class="form-text">Cambiarla pide firma de nuevo.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="consTipoClase">Clase <span class="text-danger">*</span></label>
                            <select class="form-select" id="consTipoClase">
                                <option value="obligatorio">Obligatorio</option>
                                <option value="informativo">Informativo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="consTipoOrden">Orden</label>
                            <input type="number" class="form-control" id="consTipoOrden" value="0" min="0">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="consTipoPopup" checked>
                                <label class="form-check-label" for="consTipoPopup">Mostrar al ingresar</label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label" for="consTipoArchivo">Archivo PDF adjunto (opcional)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="consTipoArchivo" readonly placeholder="Sin archivo">
                                <button type="button" class="btn btn-outline-secondary" id="consTipoSubirBtn" onclick="consTiposSubirArchivo()">Subir PDF</button>
                            </div>
                            <input type="file" id="consTipoArchivoInput" accept="application/pdf" class="d-none">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="consTipoActivo" checked>
                                <label class="form-check-label" for="consTipoActivo">Activo</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary accion-agregar" id="consTiposGuardarBtn" onclick="consTiposGuardar()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT TAB TIPOS                                         -->
<!-- ============================================================ -->
<script type="text/javascript">
var consTiposModalInstancia = null;

jQuery(document).ready(function($) {
    var modalElemento = document.getElementById('consTiposModal');
    if (modalElemento) {
        consTiposModalInstancia = bootstrap.Modal.getOrCreateInstance(modalElemento);
    }
    consTiposCargar();
});

// Cargar la lista de documentos desde el backend
function consTiposCargar() {
    var cuerpo = document.getElementById('consTiposCuerpo');
    if (!cuerpo) {
        return;
    }
    cuerpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Cargando documentos...</span></td></tr>';

    consAjax('listar_tipos', {}, function(respuesta) {
        if (respuesta.error) {
            return;
        }
        var lista = respuesta.data;
        if (!lista || lista.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No hay documentos registrados.</td></tr>';
            return;
        }
        var html = '';
        for (var i = 0; i < lista.length; i++) {
            html += consTiposCrearFila(lista[i]);
        }
        cuerpo.innerHTML = html;
    });
}

// Armar una fila de la tabla de documentos
function consTiposCrearFila(item) {
    var idNum = parseInt(item.id);
    var nombre = consEsc(item.nombre);
    var slug = consEsc(item.slug);
    var version = consEsc(item.version);
    var badgeClase = consBadgeClase(item.clase);
    var badgeActivo = consBadgeActivo(item.activo);
    var popupTxt = 'No';
    if (parseInt(item.mostrar_popup) === 1) {
        popupTxt = 'Sí';
    }
    var ordenTxt = consEsc(item.orden);

    return `
    <tr>
        <td><strong>${ordenTxt}</strong></td>
        <td class="fw-medium">${nombre}</td>
        <td><code>${slug}</code></td>
        <td>${badgeClase}</td>
        <td>${version}</td>
        <td>${popupTxt}</td>
        <td>${badgeActivo}</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-warning accion-guardar_tipo" onclick="consTiposRePedir(${idNum}, '${version}')" title="Solicitar firma de nuevo (sube versión)">
                <i class="ri-refresh-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary accion-guardar_tipo" onclick="consTiposEditar(${idNum})" title="Editar">
                <i class="ri-pencil-line"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger accion-eliminar_tipo" onclick="consTiposEliminar(${idNum})" title="Desactivar">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    </tr>`;
}

// Abrir el modal en modo nuevo
function consTiposNuevo() {
    document.getElementById('consTiposModalTitulo').textContent = 'Nuevo documento';
    document.getElementById('consTipoId').value = '0';
    document.getElementById('consTiposForm').reset();
    document.getElementById('consTipoVersion').value = '1.0';
    document.getElementById('consTipoPopup').checked = true;
    document.getElementById('consTipoActivo').checked = true;
    if (consTiposModalInstancia) {
        consTiposModalInstancia.show();
    }
}

// Abrir el modal con los datos del documento
function consTiposEditar(tipoId) {
    consAjax('obtener_tipo', { id: tipoId }, function(respuesta) {
        if (respuesta.error) {
            return;
        }
        var d = respuesta.data;
        if (!d || !d.id) {
            consMostrarMsg('Documento no encontrado', 'error');
            return;
        }
        document.getElementById('consTiposModalTitulo').textContent = 'Editar documento';
        document.getElementById('consTipoId').value = d.id;
        document.getElementById('consTipoNombre').value = d.nombre || '';
        document.getElementById('consTipoSlug').value = d.slug || '';
        document.getElementById('consTipoResumen').value = d.resumen || '';
        document.getElementById('consTipoContenido').value = d.contenido || '';
        document.getElementById('consTipoVersion').value = d.version || '1.0';
        document.getElementById('consTipoClase').value = d.clase || 'obligatorio';
        document.getElementById('consTipoOrden').value = d.orden || 0;
        document.getElementById('consTipoArchivo').value = d.archivo_url || '';
        if (parseInt(d.mostrar_popup) === 1) {
            document.getElementById('consTipoPopup').checked = true;
        } else {
            document.getElementById('consTipoPopup').checked = false;
        }
        if (parseInt(d.activo) === 1) {
            document.getElementById('consTipoActivo').checked = true;
        } else {
            document.getElementById('consTipoActivo').checked = false;
        }
        if (consTiposModalInstancia) {
            consTiposModalInstancia.show();
        }
    });
}

// Guardar el documento (crear o actualizar)
function consTiposGuardar() {
    var boton = document.getElementById('consTiposGuardarBtn');
    if (boton) {
        boton.disabled = true;
    }

    var datos = {
        id: document.getElementById('consTipoId').value,
        nombre: document.getElementById('consTipoNombre').value.trim(),
        slug: document.getElementById('consTipoSlug').value.trim(),
        resumen: document.getElementById('consTipoResumen').value.trim(),
        contenido: document.getElementById('consTipoContenido').value,
        version: document.getElementById('consTipoVersion').value.trim(),
        clase: document.getElementById('consTipoClase').value,
        orden: document.getElementById('consTipoOrden').value,
        archivo_url: document.getElementById('consTipoArchivo').value.trim()
    };
    if (document.getElementById('consTipoPopup').checked) {
        datos.mostrar_popup = 1;
    }
    if (document.getElementById('consTipoActivo').checked) {
        datos.activo = 1;
    }

    if (datos.nombre === '' || datos.slug === '' || datos.contenido === '' || datos.version === '') {
        consMostrarMsg('Nombre, slug, contenido y versión son obligatorios', 'error');
        if (boton) {
            boton.disabled = false;
        }
        return;
    }

    consAjax('guardar_tipo', datos, function(respuesta) {
        if (boton) {
            boton.disabled = false;
        }
        if (respuesta.error) {
            return;
        }
        consMostrarMsg(respuesta.msg, 'success');
        if (consTiposModalInstancia) {
            consTiposModalInstancia.hide();
        }
        consTiposCargar();
    });
}

// Pedir confirmacion y subir la version para solicitar firma a todos
function consTiposRePedir(tipoId, versionActual) {
    if (typeof Swal === 'undefined') {
        return;
    }
    Swal.fire({
        title: '¿Solicitar firma de nuevo?',
        text: 'Se incrementará la versión del documento y volverá a aparecer como pendiente a todos los usuarios.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, incrementar versión',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f7b84b',
        cancelButtonColor: '#405189'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            consAjax('incrementar_version', { id: tipoId }, function(respuesta) {
                if (respuesta.error) {
                    return;
                }
                consMostrarMsg(respuesta.msg, 'success');
                consTiposCargar();
            });
        }
    });
}

// Pedir confirmacion y desactivar el documento
function consTiposEliminar(tipoId) {
    if (typeof Swal === 'undefined') {
        return;
    }
    Swal.fire({
        title: '¿Desactivar documento?',
        text: 'Ya no se pedirá su firma a los usuarios.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#405189'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            consAjax('eliminar_tipo', { id: tipoId }, function(respuesta) {
                if (respuesta.error) {
                    return;
                }
                consMostrarMsg(respuesta.msg, 'success');
                consTiposCargar();
            });
        }
    });
}

// Subir el PDF adjunto al servidor
function consTiposSubirArchivo() {
    var input = document.getElementById('consTipoArchivoInput');
    if (!input) {
        return;
    }
    input.onchange = function() {
        if (input.files.length === 0) {
            return;
        }
        var formData = new FormData();
        formData.append('archivo', input.files[0]);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', page_root + 'subir_archivo', true);
        xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var respuesta = JSON.parse(xhr.responseText);
                    if (respuesta.error) {
                        consMostrarMsg(respuesta.msg, 'error');
                    } else {
                        document.getElementById('consTipoArchivo').value = respuesta.url;
                        consMostrarMsg('Archivo subido correctamente', 'success');
                    }
                } catch (e) {
                    consMostrarMsg('Error al subir el archivo', 'error');
                }
            } else {
                consMostrarMsg('Error en el servidor al subir', 'error');
            }
        };
        xhr.send(formData);
    };
    input.click();
}
</script>
