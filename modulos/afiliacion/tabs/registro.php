<?php
// afiliacion/tabs/registro.php - Tab 1: Formulario de Registro de Deportista
// Visible para Acudientes (Rol 3) y Administradores (Roles 1, 4)

$usuario_rol = isset($_SESSION['usuario_rol']) ? intval($_SESSION['usuario_rol']) : 0;
$es_admin = ($usuario_rol === 1 || $usuario_rol === 4);
$es_acudiente = ($usuario_rol === 3);

// Obtener datos del acudiente en sesion para prellenar
$persona_id_sesion = isset($_SESSION['persona_id']) ? intval($_SESSION['persona_id']) : 0;
$datos_acudiente_actual = array();
if ($persona_id_sesion > 0) {
    $datos_acudiente_actual = $GLOBALS['db']->select_row("SELECT * FROM persona WHERE id = '$persona_id_sesion'");
}
?>

<!-- ===== FORMULARIO DE REGISTRO DE AFILIACION VOLEY+ ===== -->
<div class="row">
    <div class="col-lg-12">
        <form id="formRegistroAfiliacion" novalidate>
            <!-- ID oculto si se esta editando un borrador existente -->
            <input type="hidden" id="reg_deportista_id" name="deportista_id" value="0">
            <input type="hidden" id="reg_modo_guardado" name="modo_guardado" value="borrador">

            <!-- ============================================================ -->
            <!-- SECCION 1: DATOS DEL DEPORTISTA                              -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header" style="background: #1e1328; color: white;">
                    <h6 class="card-title mb-0 text-black">
                        <i class="ri-user-smile-line me-1"></i> 1. Datos Personales del Deportista (Niño / Niña)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Primer Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_nombre1" name="dep_nombre1" placeholder="Primer nombre" required maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" id="reg_dep_nombre2" name="dep_nombre2" placeholder="Segundo nombre (opcional)" maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Primer Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_apellido1" name="dep_apellido1" placeholder="Primer apellido" required maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" id="reg_dep_apellido2" name="dep_apellido2" placeholder="Segundo apellido (opcional)" maxlength="50">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium">Tipo de Documento <span class="text-danger">*</span></label>
                            <select class="form-select" id="reg_dep_tipo_documento" name="dep_tipo_documento" required>
                                <option value="TI">Tarjeta de Identidad (TI)</option>
                                <option value="RC">Registro Civil (RC)</option>
                                <option value="CC">Cedula de Ciudadania (CC)</option>
                                <option value="CE">Cedula de Extranjeria (CE)</option>
                                <option value="PASAPORTE">Pasaporte</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Numero de Documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_identificacion" name="dep_identificacion" placeholder="Numero de documento" required maxlength="20">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reg_dep_fecha_nacimiento" name="dep_fecha_nacimiento" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Genero <span class="text-danger">*</span></label>
                            <select class="form-select" id="reg_dep_genero" name="dep_genero" required>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium">Categoria</label>
                            <select class="form-select" id="reg_dep_categoria_id" name="dep_categoria_id">
                                <option value="">Seleccione categoria...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">EPS / SISBEN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_eps" name="dep_eps" placeholder="Nombre de EPS" required maxlength="100">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Grupo RH</label>
                            <select class="form-select" id="reg_dep_rh" name="dep_rh">
                                <option value="">RH...</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alergias o Condiciones Medicas</label>
                            <input type="text" class="form-control" id="reg_dep_alergias" name="dep_alergias" placeholder="Ninguna o especificar...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SECCION 2: CONTACTO Y UBICACION DEL DEPORTISTA               -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 text-primary">
                        <i class="ri-map-pin-user-line me-1"></i> 2. Informacion de Contacto y Residencia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Celular del Deportista</label>
                            <input type="tel" class="form-control" id="reg_dep_celular" name="dep_celular" placeholder="Numero de celular" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Correo Electronico</label>
                            <input type="email" class="form-control" id="reg_dep_correo" name="dep_correo" placeholder="correo@ejemplo.com" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Direccion de Residencia</label>
                            <input type="text" class="form-control" id="reg_dep_direccion" name="dep_direccion" placeholder="Barrio, Calle, Numero">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SECCION 3: DATOS DEL PADRE / MADRE / ACUDIENTE               -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 text-primary">
                        <i class="ri-parent-line me-1"></i> 3. Datos del Padre, Madre o Acudiente Responsable
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Nombre Completo del Acudiente</label>
                            <input type="text" class="form-control bg-light" readonly
                                   value="<?php echo htmlspecialchars(($datos_acudiente_actual['nombre1'] ?? '') . ' ' . ($datos_acudiente_actual['nombre2'] ?? '') . ' ' . ($datos_acudiente_actual['apellido1'] ?? '') . ' ' . ($datos_acudiente_actual['apellido2'] ?? '')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Documento Acudiente</label>
                            <input type="text" class="form-control bg-light" readonly
                                   value="<?php echo htmlspecialchars(($datos_acudiente_actual['tipo_documento'] ?? '') . ' ' . ($datos_acudiente_actual['identificacion'] ?? '')); ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-medium">Parentesco con el Deportista <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="parentesco" id="parentesco_padre" value="padre" checked>
                                    <label class="form-check-label" for="parentesco_padre">Padre</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="parentesco" id="parentesco_madre" value="madre">
                                    <label class="form-check-label" for="parentesco_madre">Madre</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="parentesco" id="parentesco_tutor" value="tutor">
                                    <label class="form-check-label" for="parentesco_tutor">Tutor Legal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="parentesco" id="parentesco_otro" value="otro">
                                    <label class="form-check-label" for="parentesco_otro">Otro</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Celular Acudiente (Editable)</label>
                            <input type="tel" class="form-control" id="reg_acu_celular" name="acu_celular"
                                   value="<?php echo htmlspecialchars($datos_acudiente_actual['celular'] ?? ''); ?>" placeholder="Celular de contacto">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Correo Acudiente (Editable)</label>
                            <input type="email" class="form-control" id="reg_acu_correo" name="acu_correo"
                                   value="<?php echo htmlspecialchars($datos_acudiente_actual['correo'] ?? ''); ?>" placeholder="Correo de notificaciones">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Direccion Acudiente (Editable)</label>
                            <input type="text" class="form-control" id="reg_acu_direccion" name="acu_direccion"
                                   value="<?php echo htmlspecialchars($datos_acudiente_actual['direccion'] ?? ''); ?>" placeholder="Direccion del hogar">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SECCION 4: CONTACTO DE EMERGENCIA                            -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 text-primary">
                        <i class="ri-alarm-warning-line me-1"></i> 4. Contacto en Caso de Emergencia
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nombre de Contacto de Emergencia <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_contacto_nombre" name="dep_contacto_emergencia_nombre" placeholder="Nombre completo" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefono de Emergencia <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="reg_dep_contacto_telefono" name="dep_contacto_emergencia_telefono" placeholder="Numero de telefono / celular" required maxlength="20">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SECCION 5: OBSERVACIONES Y DOCUMENTACION                     -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 text-primary">
                        <i class="ri-folder-upload-line me-1"></i> 5. Observaciones y Documentos Requeridos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Observaciones Adicionales</label>
                            <textarea class="form-control" id="reg_dep_observaciones" name="dep_observaciones" rows="2" placeholder="Indique informacion relevante para el club (experiencia previa, horarios de preferencia, etc.)..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-info d-flex align-items-center mb-2" role="alert">
                                <i class="ri-information-line fs-4 me-2"></i>
                                <div>
                                    Guarde primero el registro como <strong>Borrador</strong> para poder adjuntar los documentos individuales en el modal.
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="btnAbrirModalDocs" onclick="afiliacionAbrirModalDocumentos()" disabled>
                                <i class="ri-file-list-3-line me-1"></i> Adjuntar / Ver Documentos Requeridos
                            </button>
                            <span class="ms-2 text-muted" id="textoEstadoDocs">Debe guardar el borrador primero.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- BOTONES DE ACCION: GUARDAR BORRADOR / ENVIAR                 -->
            <!-- ============================================================ -->
            <div class="card mb-4 border-0">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <button type="button" class="btn btn-secondary" onclick="afiliacionLimpiarFormularioRegistro()">
                            <i class="ri-eraser-line me-1"></i> Limpiar Formulario
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnGuardarBorrador" onclick="afiliacionGuardarRegistro('borrador')">
                            <i class="ri-save-line me-1"></i> Guardar Borrador
                        </button>
                        <button type="button" class="btn btn-success" id="btnEnviarRevision" onclick="afiliacionGuardarRegistro('enviar')">
                            <i class="ri-send-plane-line me-1"></i> Enviar a Revision
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: DOCUMENTOS REQUERIDOS (LISTA INDIVIDUAL)              -->
<!-- ============================================================ -->
<div class="modal fade" id="modalDocumentosRequeridos" tabindex="-1" aria-labelledby="modalDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: #405189; color: white;">
                <h5 class="modal-title text-white" id="modalDocumentosLabel">
                    <i class="ri-folder-shield-line me-1"></i> Documentacion del Deportista
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Suba los documentos solicitados en formato PDF o imagen (JPG, PNG). Tamano maximo por archivo: 10 MB.
                </p>
                <div class="list-group" id="contenedorListaDocumentos">
                    <!-- Se carga dinamicamente con JavaScript -->
                    <div class="text-center py-4">
                        <span class="spinner-border spinner-border-sm text-primary"></span>
                        <span class="ms-2">Cargando requisitos...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT EXCLUSIVO DEL TAB REGISTRO                        -->
<!-- ============================================================ -->
<script type="text/javascript">
var modalDocsInstancia = null;

jQuery(document).ready(function($) {
    // 1. Inicializar modal de documentos
    var modalElemento = document.getElementById('modalDocumentosRequeridos');
    if (modalElemento) {
        modalDocsInstancia = bootstrap.Modal.getOrCreateInstance(modalElemento);
    }

    // 2. Cargar categorias en el select
    afiliacionCargarCategoriasSelect();
});

// Cargar categorias activas desde el backend
function afiliacionCargarCategoriasSelect() {
    afiliacionAjax('listar_categorias', {}, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var select = document.getElementById('reg_dep_categoria_id');
            select.innerHTML = '<option value="">Seleccione categoria...</option>';
            var lista = respuesta.data;
            for (var i = 0; i < lista.length; i++) {
                var item = lista[i];
                var opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.nombre + ' (' + item.edad_minima + '-' + item.edad_maxima + ' anos)';
                select.appendChild(opt);
            }
        }
    });
}

// Guardar formulario (borrador o envio a revision)
function afiliacionGuardarRegistro(modo) {
    var form = document.getElementById('formRegistroAfiliacion');
    document.getElementById('reg_modo_guardado').value = modo;

    // Validacion basica en cliente para envio
    if (modo === 'enviar') {
        var nombre1 = document.getElementById('reg_dep_nombre1').value.trim();
        var apellido1 = document.getElementById('reg_dep_apellido1').value.trim();
        var identificacion = document.getElementById('reg_dep_identificacion').value.trim();
        var fechaNac = document.getElementById('reg_dep_fecha_nacimiento').value.trim();
        var eps = document.getElementById('reg_dep_eps').value.trim();
        var contactoNom = document.getElementById('reg_dep_contacto_nombre').value.trim();
        var contactoTel = document.getElementById('reg_dep_contacto_telefono').value.trim();

        if (nombre1 === '' || apellido1 === '' || identificacion === '' || fechaNac === '' || eps === '' || contactoNom === '' || contactoTel === '') {
            afiliacionMostrarMsg('Por favor complete todos los campos obligatorios (*) antes de enviar a revision', 'warning');
            return;
        }
    }

    var formData = new FormData(form);
    var deportistaId = parseInt(document.getElementById('reg_deportista_id').value) || 0;
    var accion = (deportistaId > 0) ? 'actualizar_registro_acudiente' : 'crear_registro_acudiente';

    // Deshabilitar botones mientras se procesa
    document.getElementById('btnGuardarBorrador').disabled = true;
    document.getElementById('btnEnviarRevision').disabled = true;

    afiliacionAjaxFormData(accion, formData, function(respuesta) {
        document.getElementById('btnGuardarBorrador').disabled = false;
        document.getElementById('btnEnviarRevision').disabled = false;

        if (!respuesta.error) {
            afiliacionMostrarMsg(respuesta.msg, 'success');

            // Si se creo uno nuevo, guardar el ID asignado para poder subir documentos
            if (respuesta.data && respuesta.data.deportista_id) {
                document.getElementById('reg_deportista_id').value = respuesta.data.deportista_id;
                document.getElementById('btnAbrirModalDocs').disabled = false;
                document.getElementById('textoEstadoDocs').innerHTML = '<span class="text-success fw-medium">Borrador listo. Ya puede adjuntar documentos.</span>';
            }

            // Si se envio a revision, sugerir pasar al historial
            if (modo === 'enviar') {
                if (typeof afiliacionMisSolicitudesCargarDatos === 'function') {
                    afiliacionMisSolicitudesCargarDatos();
                }
            }
        }
    });
}

// Abrir modal de documentos requeridos
function afiliacionAbrirModalDocumentos() {
    var deportistaId = parseInt(document.getElementById('reg_deportista_id').value) || 0;
    if (deportistaId <= 0) {
        afiliacionMostrarMsg('Debe guardar el borrador antes de adjuntar documentos', 'warning');
        return;
    }

    afiliacionCargarListaDocumentos(deportistaId);
    if (modalDocsInstancia) {
        modalDocsInstancia.show();
    }
}

// Cargar la lista de documentos y su estado
function afiliacionCargarListaDocumentos(deportistaId) {
    var contenedor = document.getElementById('contenedorListaDocumentos');
    contenedor.innerHTML = '<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2">Cargando...</span></div>';

    afiliacionAjax('listar_tipos_documento', { deportista_id: deportistaId }, function(respuesta) {
        if (!respuesta.error && respuesta.data) {
            var lista = respuesta.data;
            if (lista.length === 0) {
                contenedor.innerHTML = '<div class="alert alert-info">No hay tipos de documentos configurados.</div>';
                return;
            }

            var html = '';
            for (var i = 0; i < lista.length; i++) {
                html += afiliacionCrearFilaTipoDocumento(lista[i], deportistaId);
            }
            contenedor.innerHTML = html;
        }
    });
}

// Crear fila HTML de un tipo de documento con template backticks
function afiliacionCrearFilaTipoDocumento(tipo, deportistaId) {
    var idTipo = tipo.id;
    var nombre = afiliacionEsc(tipo.nombre);
    var obligatorio = (parseInt(tipo.obligatorio) === 1);
    var subido = tipo.subido;
    var badgeHtml = '';
    var accionesHtml = '';

    if (subido && tipo.documento) {
        var rutaArchivo = afiliacionEsc(tipo.documento.archivo);
        var docId = tipo.documento.id;
        badgeHtml = `<span class="badge bg-success"><i class="ri-check-line me-1"></i>Subido</span>`;
        accionesHtml = `
            <a href="${rutaArchivo}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver documento"><i class="ri-eye-line"></i></a>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="afiliacionEliminarDoc(${docId}, ${deportistaId})" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
        `;
    } else {
        if (obligatorio) {
            badgeHtml = `<span class="badge bg-danger">Requerido</span>`;
        } else {
            badgeHtml = `<span class="badge bg-secondary">Opcional</span>`;
        }
        accionesHtml = `
            <input type="file" id="input_file_${idTipo}" class="d-none" accept=".pdf,image/*" onchange="afiliacionSubirArchivoTipo(${idTipo}, ${deportistaId})">
            <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('input_file_${idTipo}').click()"><i class="ri-upload-cloud-line me-1"></i>Subir</button>
        `;
    }

    return `
    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0 fw-medium">${nombre}</h6>
                ${badgeHtml}
            </div>
            <small class="text-muted">Formatos: PDF, JPG, PNG &middot; Max. 10MB</small>
        </div>
        <div class="btn-group">
            ${accionesHtml}
        </div>
    </div>`;
}

// Subir archivo al seleccionar del input
function afiliacionSubirArchivoTipo(tipoDocId, deportistaId) {
    var input = document.getElementById('input_file_' + tipoDocId);
    if (!input.files || input.files.length === 0) {
        return;
    }

    var archivo = input.files[0];
    if (archivo.size > 10 * 1024 * 1024) {
        afiliacionMostrarMsg('El archivo excede el limite de 10 MB', 'error');
        input.value = '';
        return;
    }

    var formData = new FormData();
    formData.append('deportista_id', deportistaId);
    formData.append('tipo_documento_id', tipoDocId);
    formData.append('archivo', archivo);

    afiliacionAjaxFormData('subir_documento_acudiente', formData, function(respuesta) {
        if (!respuesta.error) {
            afiliacionMostrarMsg(respuesta.msg, 'success');
            afiliacionCargarListaDocumentos(deportistaId);
        }
    });
}

// Eliminar documento
function afiliacionEliminarDoc(documentoId, deportistaId) {
    if (!confirm('Esta seguro de eliminar este documento?')) {
        return;
    }

    afiliacionAjax('eliminar_documento_acudiente', { documento_id: documentoId }, function(respuesta) {
        if (!respuesta.error) {
            afiliacionMostrarMsg(respuesta.msg, 'success');
            afiliacionCargarListaDocumentos(deportistaId);
        }
    });
}

// Limpiar formulario completo para un nuevo registro
function afiliacionLimpiarFormularioRegistro() {
    document.getElementById('formRegistroAfiliacion').reset();
    document.getElementById('reg_deportista_id').value = '0';
    document.getElementById('reg_modo_guardado').value = 'borrador';
    document.getElementById('btnAbrirModalDocs').disabled = true;
    document.getElementById('textoEstadoDocs').innerHTML = 'Debe guardar el borrador primero.';
}
</script>