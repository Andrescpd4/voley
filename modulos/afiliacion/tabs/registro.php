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

<style>
    /* Firma del acudiente: recuadro punteado estilo libreta */
    .afili-firma-marco {
        border: 2px dashed #adb5bd;
        border-radius: 8px;
        background-color: #ffffff;
        touch-action: none;
    }
    #firmaCanvas {
        width: 100%;
        height: 180px;
        display: block;
        cursor: crosshair;
    }
</style>

<!-- ===== FORMULARIO DE REGISTRO DE AFILIACION VOLEY+ ===== -->
<div class="row">
    <div class="col-lg-12">
        <form id="formRegistroAfiliacion" novalidate>
            <!-- ID oculto si se esta editando un borrador existente -->
            <input type="hidden" id="reg_deportista_id" name="deportista_id" value="0">
            <input type="hidden" id="reg_modo_guardado" name="modo_guardado" value="borrador">

            <?php if ($es_admin): ?>
                <!-- BLOQUE SOLO ADMIN: registrar a nombre de otro acudiente (pruebas y soporte) -->
                <div class="alert alert-warning d-flex flex-column gap-2 mb-3" role="alert">
                    <div>
                        <i class="ri-shield-user-line me-1"></i>
                        <strong>Modo administrador:</strong> por defecto el registro queda a tu propia persona (para pruebas).
                        Si quieres registrar a nombre de un acudiente real, seleccionalo aqui.
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium mb-1" for="reg_acudiente_override">Registrar a nombre de</label>
                        <select class="form-select" id="reg_acudiente_override" name="acudiente_id_override">
                            <option value="0">Mi propia persona (prueba rapida)</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================================================ -->
            <!-- SECCION 1: DATOS DEL DEPORTISTA                              -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header afili-encabezado-oscuro">
                    <h6 class="card-title mb-0">
                        <i class="ri-user-smile-line me-1"></i> 1. Datos Personales del Deportista (Niño / Niña)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_nombre1">Primer Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_nombre1" name="dep_nombre1" placeholder="Primer nombre" required maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="reg_dep_nombre2">Segundo Nombre</label>
                            <input type="text" class="form-control" id="reg_dep_nombre2" name="dep_nombre2" placeholder="Segundo nombre (opcional)" maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_apellido1">Primer Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_apellido1" name="dep_apellido1" placeholder="Primer apellido" required maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="reg_dep_apellido2">Segundo Apellido</label>
                            <input type="text" class="form-control" id="reg_dep_apellido2" name="dep_apellido2" placeholder="Segundo apellido (opcional)" maxlength="50">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_tipo_documento">Tipo de Documento <span class="text-danger">*</span></label>
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
                            <label class="form-label fw-medium" for="reg_dep_identificacion">Numero de Documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_identificacion" name="dep_identificacion" placeholder="Numero de documento" required maxlength="20">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_fecha_nacimiento">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reg_dep_fecha_nacimiento" name="dep_fecha_nacimiento" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_genero">Genero <span class="text-danger">*</span></label>
                            <select class="form-select" id="reg_dep_genero" name="dep_genero" required>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_categoria_id">Categoria</label>
                            <select class="form-select" id="reg_dep_categoria_id" name="dep_categoria_id">
                                <option value="">Seleccione categoria...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium" for="reg_dep_eps">EPS / SISBEN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_eps" name="dep_eps" placeholder="Nombre de EPS" required maxlength="100">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="reg_dep_rh">Grupo RH</label>
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
                            <label class="form-label" for="reg_dep_alergias">Alergias o Condiciones Medicas</label>
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
                            <label class="form-label" for="reg_dep_celular">Celular del Deportista</label>
                            <input type="tel" class="form-control" id="reg_dep_celular" name="dep_celular" placeholder="Numero de celular" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="reg_dep_correo">Correo Electronico</label>
                            <input type="email" class="form-control" id="reg_dep_correo" name="dep_correo" placeholder="correo@ejemplo.com" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="reg_dep_direccion">Direccion de Residencia</label>
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
                            <label class="form-label" for="reg_acu_celular">Celular Acudiente (Editable)</label>
                            <input type="tel" class="form-control" id="reg_acu_celular" name="acu_celular"
                                value="<?php echo htmlspecialchars($datos_acudiente_actual['celular'] ?? ''); ?>" placeholder="Celular de contacto">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="reg_acu_correo">Correo Acudiente (Editable)</label>
                            <input type="email" class="form-control" id="reg_acu_correo" name="acu_correo"
                                value="<?php echo htmlspecialchars($datos_acudiente_actual['correo'] ?? ''); ?>" placeholder="Correo de notificaciones">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="reg_acu_direccion">Direccion Acudiente (Editable)</label>
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
                            <label class="form-label fw-medium" for="reg_dep_contacto_nombre">Nombre de Contacto de Emergencia <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reg_dep_contacto_nombre" name="dep_contacto_emergencia_nombre" placeholder="Nombre completo" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium" for="reg_dep_contacto_telefono">Telefono de Emergencia <span class="text-danger">*</span></label>
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
                            <label class="form-label" for="reg_dep_observaciones">Observaciones Adicionales</label>
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
            <!-- FIRMA DEL ACUDIENTE (padre, madre o representante)      -->
            <!-- Una sola firma que respalda todos los documentos.      -->
            <!-- Viaja dentro del FormData principal (sin form aparte). -->
            <!-- ============================================================ -->
            <div class="card mb-3 border">
                <div class="card-header afili-encabezado-oscuro">
                    <h6 class="card-title mb-0">
                        <i class="ri-pen-nib-line me-1"></i> Firma del Acudiente
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        Con su firma usted acepta el tratamiento de datos, las políticas del club
                        y los demás documentos obligatorios. Dibuje con el dedo o el mouse.
                    </p>
                    <div class="afili-firma-marco">
                        <canvas id="firmaCanvas"></canvas>
                    </div>
                    <input type="hidden" id="firmaImagenData" name="firma_imagen_data" value="">
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="afiliacionFirmaLimpiar()">
                            <i class="ri-eraser-line me-1"></i> Borrar y firmar de nuevo
                        </button>
                        <span class="text-muted small align-self-center">Solo se pide al Enviar a Revisión. El borrador no la necesita.</span>
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
                                <button type="button" class="btn btn-success text-dark" id="btnEnviarRevision" onclick="afiliacionGuardarRegistro('enviar')">
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header afili-encabezado">
                <h5 class="modal-title" id="modalDocumentosLabel">
                    <i class="ri-folder-shield-line me-1"></i> Documentacion del Deportista
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Suba los documentos solicitados en formato PDF o imagen (JPG, PNG). Tamano maximo por archivo: 10 MB.
                </p>
                <div class="list-group" id="contenedorListaDocumentos" aria-live="polite">
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
    // Cache local: evita pedir categorias y acudientes al servidor mas de una vez
    var afiliCacheCategorias = null;
    var afiliCacheAcudientes = null;

    jQuery(document).ready(function($) {
        // 1. Inicializar modal de documentos
        var modalElemento = document.getElementById('modalDocumentosRequeridos');
        if (modalElemento) {
            modalDocsInstancia = bootstrap.Modal.getOrCreateInstance(modalElemento);
        }

        // 2. Cargar categorias en el select
        afiliacionCargarCategoriasSelect();

        // 3. Si es admin, cargar acudientes para el select de pruebas (ver PHP $es_admin)
        var selectOverride = document.getElementById('reg_acudiente_override');
        if (selectOverride) {
            afiliacionCargarAcudientesOverride();
        }

        // 4. Preparar el canvas de firma del acudiente
        afiliacionFirmaIniciar();
    });

    // Cargar acudientes para el select solo-admin (usa cache si ya se pidio antes)
    function afiliacionCargarAcudientesOverride() {
        // Si ya tenemos la lista en memoria, pintarla sin ir al servidor
        if (afiliCacheAcudientes !== null) {
            afiliacionPintarAcudientesOverride(afiliCacheAcudientes);
            return;
        }
        afiliacionAjax('listar_acudientes_admin', {}, function(respuesta) {
            if (!respuesta.error && respuesta.data) {
                afiliCacheAcudientes = respuesta.data;
                afiliacionPintarAcudientesOverride(afiliCacheAcudientes);
            }
        });
    }

    // Pintar las opciones del select solo-admin
    function afiliacionPintarAcudientesOverride(lista) {
        var select = document.getElementById('reg_acudiente_override');
        if (!select) {
            return;
        }
        for (var i = 0; i < lista.length; i++) {
            var item = lista[i];
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nombre;
            select.appendChild(opt);
        }
    }

    // Cargar categorias activas desde el backend (usa cache si ya se pidio antes)
    function afiliacionCargarCategoriasSelect() {
        // Si ya tenemos la lista en memoria, pintarla sin ir al servidor
        if (afiliCacheCategorias !== null) {
            afiliacionPintarCategoriasSelect(afiliCacheCategorias);
            return;
        }
        afiliacionAjax('listar_categorias', {}, function(respuesta) {
            if (!respuesta.error && respuesta.data) {
                afiliCacheCategorias = respuesta.data;
                afiliacionPintarCategoriasSelect(afiliCacheCategorias);
            }
        });
    }

    // Pintar las opciones del select de categorias
    function afiliacionPintarCategoriasSelect(lista) {
        var select = document.getElementById('reg_dep_categoria_id');
        if (!select) {
            return;
        }
        select.innerHTML = '<option value="">Seleccione categoria...</option>';
        for (var i = 0; i < lista.length; i++) {
            var item = lista[i];
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nombre + ' (' + item.edad_minima + '-' + item.edad_maxima + ' años)';
            select.appendChild(opt);
        }
    }

    // Quitar todas las marcas de error del formulario
    function afiliacionLimpiarErrores() {
        var form = document.getElementById('formRegistroAfiliacion');
        var marcados = form.querySelectorAll('.afili-invalido');
        for (var i = 0; i < marcados.length; i++) {
            marcados[i].classList.remove('afili-invalido');
            marcados[i].removeAttribute('aria-invalid');
        }
        var textos = form.querySelectorAll('.afili-error-texto');
        for (var j = 0; j < textos.length; j++) {
            textos[j].parentNode.removeChild(textos[j]);
        }
    }

    // Marcar un campo con error visible y texto de ayuda
    function afiliacionMarcarCampo(id_campo, mensaje) {
        var campo = document.getElementById(id_campo);
        if (!campo) {
            return;
        }
        campo.classList.add('afili-invalido');
        campo.setAttribute('aria-invalid', 'true');
        // Crear el texto de ayuda debajo del campo
        var ayuda = document.createElement('div');
        ayuda.className = 'afili-error-texto';
        ayuda.textContent = mensaje;
        campo.parentNode.appendChild(ayuda);
    }

    // Validar los campos obligatorios antes de enviar a revision
    // Marca cada campo vacio en rojo y lleva el foco al primero
    function afiliacionValidarEnvio() {
        var obligatorios = [{
                id: 'reg_dep_nombre1',
                nombre: 'Primer nombre'
            },
            {
                id: 'reg_dep_apellido1',
                nombre: 'Primer apellido'
            },
            {
                id: 'reg_dep_identificacion',
                nombre: 'Numero de documento'
            },
            {
                id: 'reg_dep_fecha_nacimiento',
                nombre: 'Fecha de nacimiento'
            },
            {
                id: 'reg_dep_eps',
                nombre: 'EPS / SISBEN'
            },
            {
                id: 'reg_dep_contacto_nombre',
                nombre: 'Nombre de contacto de emergencia'
            },
            {
                id: 'reg_dep_contacto_telefono',
                nombre: 'Telefono de emergencia'
            }
        ];
        var primer_campo_vacio = null;
        var total_vacios = 0;
        for (var i = 0; i < obligatorios.length; i++) {
            var campo = document.getElementById(obligatorios[i].id);
            var valor = '';
            if (campo) {
                valor = campo.value.trim();
            }
            if (valor === '') {
                afiliacionMarcarCampo(obligatorios[i].id, 'El campo ' + obligatorios[i].nombre + ' es obligatorio.');
                total_vacios = total_vacios + 1;
                if (primer_campo_vacio === null) {
                    primer_campo_vacio = campo;
                }
            }
        }
        // Llevar el foco al primer campo con error
        if (primer_campo_vacio !== null) {
            primer_campo_vacio.focus();
        }
        if (total_vacios > 0) {
            return false;
        }
        return true;
    }

    // Guardar formulario (borrador o envio a revision)
    function afiliacionGuardarRegistro(modo) {
        var form = document.getElementById('formRegistroAfiliacion');
        document.getElementById('reg_modo_guardado').value = modo;

        // Quitar marcas de error anteriores
        afiliacionLimpiarErrores();

        // Validacion en cliente solo para envio (el borrador permite incompletos)
        if (modo === 'enviar') {
            var valido = afiliacionValidarEnvio();
            if (!valido) {
                afiliacionMostrarMsg('Por favor complete los campos marcados en rojo antes de enviar a revision', 'warning');
                return;
            }
            // La firma del acudiente es obligatoria solo al enviar
            var firma_lista = afiliacionFirmaGuardarEnCampo();
            if (!firma_lista) {
                afiliacionMostrarMsg('Debe dibujar su firma en el recuadro antes de enviar a revisión', 'warning');
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

        afiliacionAjax('listar_tipos_documento', {
            deportista_id: deportistaId
        }, function(respuesta) {
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
            badgeHtml = `<span class="badge bg-success text-dark"><i class="ri-check-line me-1"></i>Subido</span>`;
            accionesHtml = `
            <a href="${rutaArchivo}" target="_blank" class="btn btn-sm btn-outline-info btn-afili-accion" title="Ver documento" aria-label="Ver documento adjunto"><i class="ri-eye-line"></i></a>
            <button type="button" class="btn btn-sm btn-outline-danger btn-afili-accion" onclick="afiliacionEliminarDoc(${docId}, ${deportistaId})" title="Eliminar" aria-label="Eliminar documento adjunto"><i class="ri-delete-bin-line"></i></button>
        `;
        } else {
            if (obligatorio) {
                badgeHtml = `<span class="badge bg-danger text-dark">Requerido</span>`;
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

    // Eliminar documento (pide confirmacion con el estilo del sistema)
    function afiliacionEliminarDoc(documentoId, deportistaId) {
        afiliacionConfirmar('Eliminar documento', 'Esta seguro de eliminar este documento?', 'Si, eliminar', function() {
            afiliacionAjax('eliminar_documento_acudiente', {
                documento_id: documentoId
            }, function(respuesta) {
                if (!respuesta.error) {
                    afiliacionMostrarMsg(respuesta.msg, 'success');
                    afiliacionCargarListaDocumentos(deportistaId);
                }
            });
        });
    }

    // ============================================================
    // FIRMA DEL ACUDIENTE (canvas local del tab, sin archivos aparte)
    // ============================================================
    var afiliFirmaDibujando = false;
    var afiliFirmaPrevioX = 0;
    var afiliFirmaPrevioY = 0;
    var afiliFirmaTrazo = false;

    // Preparar el canvas: tamano real, eventos de mouse y tactil
    function afiliacionFirmaIniciar() {
        var canvas = document.getElementById('firmaCanvas');
        if (!canvas) {
            return;
        }
        afiliacionFirmaAjustar(canvas);

        canvas.addEventListener('mousedown', afiliacionFirmaEmpezar);
        canvas.addEventListener('mousemove', afiliacionFirmaMover);
        canvas.addEventListener('mouseup', afiliacionFirmaTerminar);
        canvas.addEventListener('mouseleave', afiliacionFirmaTerminar);
        canvas.addEventListener('touchstart', afiliacionFirmaEmpezar, { passive: false });
        canvas.addEventListener('touchmove', afiliacionFirmaMover, { passive: false });
        canvas.addEventListener('touchend', afiliacionFirmaTerminar);

        window.addEventListener('resize', function() {
            afiliacionFirmaAjustar(canvas);
        });
    }

    // Ajustar el canvas al ancho visible con nitidez en pantallas HD
    function afiliacionFirmaAjustar(canvas) {
        var escala = 1;
        if (window.devicePixelRatio && window.devicePixelRatio > 1) {
            escala = window.devicePixelRatio;
        }
        var ancho_css = canvas.clientWidth;
        var alto_css = 180;
        if (ancho_css <= 0) {
            ancho_css = 300;
        }
        canvas.width = ancho_css * escala;
        canvas.height = alto_css * escala;
        var ctx = canvas.getContext('2d');
        ctx.scale(escala, escala);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#212529';
    }

    // Punto donde empieza el trazo (sirve para mouse y dedo)
    function afiliacionFirmaPunto(evento, canvas) {
        var rect = canvas.getBoundingClientRect();
        var punto_x = 0;
        var punto_y = 0;
        if (evento.touches && evento.touches.length > 0) {
            punto_x = evento.touches[0].clientX - rect.left;
            punto_y = evento.touches[0].clientY - rect.top;
        } else {
            punto_x = evento.clientX - rect.left;
            punto_y = evento.clientY - rect.top;
        }
        return { x: punto_x, y: punto_y };
    }

    // Empezar a dibujar
    function afiliacionFirmaEmpezar(evento) {
        var canvas = document.getElementById('firmaCanvas');
        if (!canvas) {
            return;
        }
        if (evento.cancelable) {
            evento.preventDefault();
        }
        afiliFirmaDibujando = true;
        var punto = afiliacionFirmaPunto(evento, canvas);
        afiliFirmaPrevioX = punto.x;
        afiliFirmaPrevioY = punto.y;
    }

    // Seguir el trazo mientras se mueve
    function afiliacionFirmaMover(evento) {
        if (!afiliFirmaDibujando) {
            return;
        }
        var canvas = document.getElementById('firmaCanvas');
        if (!canvas) {
            return;
        }
        if (evento.cancelable) {
            evento.preventDefault();
        }
        var punto = afiliacionFirmaPunto(evento, canvas);
        var ctx = canvas.getContext('2d');
        ctx.beginPath();
        ctx.moveTo(afiliFirmaPrevioX, afiliFirmaPrevioY);
        ctx.lineTo(punto.x, punto.y);
        ctx.stroke();
        afiliFirmaPrevioX = punto.x;
        afiliFirmaPrevioY = punto.y;
        afiliFirmaTrazo = true;
    }

    // Soltar el lapiz
    function afiliacionFirmaTerminar() {
        afiliFirmaDibujando = false;
    }

    // Borrar el canvas por completo
    function afiliacionFirmaLimpiar() {
        var canvas = document.getElementById('firmaCanvas');
        if (!canvas) {
            return;
        }
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        afiliFirmaTrazo = false;
        var campo = document.getElementById('firmaImagenData');
        if (campo) {
            campo.value = '';
        }
    }

    // Revisar si el canvas esta vacio (sin ningun trazo)
    function afiliacionFirmaVacia() {
        if (afiliFirmaTrazo) {
            return false;
        }
        return true;
    }

    // Copiar la imagen del canvas al campo oculto antes de enviar
    function afiliacionFirmaGuardarEnCampo() {
        var canvas = document.getElementById('firmaCanvas');
        var campo = document.getElementById('firmaImagenData');
        if (!canvas || !campo) {
            return false;
        }
        if (afiliacionFirmaVacia()) {
            campo.value = '';
            return false;
        }
        campo.value = canvas.toDataURL('image/png');
        return true;
    }

    // Cargar una solicitud existente (borrador o devuelta / requiere_info) para editar
    function afiliacionCargarParaEditar(deportistaId) {
        afiliacionAjax('obtener_mis_solicitud', { deportista_id: deportistaId }, function(respuesta) {
            if (respuesta.error) {
                return;
            }
            var d = respuesta.data;
            if (!d || !d.id) {
                afiliacionMostrarMsg('No se pudieron cargar los datos de la solicitud', 'error');
                return;
            }

            // 1. Limpiar marcas y formulario
            afiliacionLimpiarErrores();
            afiliacionFirmaLimpiar();

            // 2. Setear IDs y modo
            document.getElementById('reg_deportista_id').value = d.id;
            document.getElementById('reg_modo_guardado').value = 'borrador';

            // 3. Prellenar datos del deportista
            document.getElementById('reg_dep_nombre1').value = d.nombre1 || '';
            document.getElementById('reg_dep_nombre2').value = d.nombre2 || '';
            document.getElementById('reg_dep_apellido1').value = d.apellido1 || '';
            document.getElementById('reg_dep_apellido2').value = d.apellido2 || '';
            document.getElementById('reg_dep_tipo_documento').value = d.tipo_documento || 'TI';
            document.getElementById('reg_dep_identificacion').value = d.identificacion || '';
            document.getElementById('reg_dep_fecha_nacimiento').value = d.fecha_nacimiento || '';
            document.getElementById('reg_dep_genero').value = d.genero || 'M';
            document.getElementById('reg_dep_celular').value = d.celular || '';
            document.getElementById('reg_dep_correo').value = d.correo || '';
            document.getElementById('reg_dep_direccion').value = d.direccion || '';
            document.getElementById('reg_dep_categoria_id').value = d.categoria_id || '';
            document.getElementById('reg_dep_eps').value = d.eps || '';
            document.getElementById('reg_dep_rh').value = d.rh || '';
            document.getElementById('reg_dep_alergias').value = d.alergias || '';
            document.getElementById('reg_dep_contacto_nombre').value = d.contacto_emergencia_nombre || '';
            document.getElementById('reg_dep_contacto_telefono').value = d.contacto_emergencia_telefono || '';
            document.getElementById('reg_dep_observaciones').value = d.observaciones || '';

            // 4. Prellenar datos del acudiente si vienen
            if (d.acudiente) {
                var acu = d.acudiente;
                var campoCel = document.getElementById('reg_acu_celular');
                if (campoCel && acu.celular) { campoCel.value = acu.celular; }
                var campoCorreo = document.getElementById('reg_acu_correo');
                if (campoCorreo && acu.correo) { campoCorreo.value = acu.correo; }
                var campoDir = document.getElementById('reg_acu_direccion');
                if (campoDir && acu.direccion) { campoDir.value = acu.direccion; }
            }
            if (d.parentesco) {
                var campoParentesco = document.getElementById('reg_parentesco');
                if (campoParentesco) { campoParentesco.value = d.parentesco; }
            }

            // 5. Habilitar boton de documentos
            document.getElementById('btnAbrirModalDocs').disabled = false;
            document.getElementById('textoEstadoDocs').innerHTML = '<span class="text-success fw-medium">Borrador cargado. Puede adjuntar o modificar documentos.</span>';

            // 6. Cambiar a la pestaña de Registro
            var tabLink = document.getElementById('tab-registro-link');
            if (tabLink) {
                var tabInstancia = bootstrap.Tab.getOrCreateInstance(tabLink);
                tabInstancia.show();
            }

            afiliacionMostrarMsg('Solicitud cargada para edición. Realice los cambios y firme al final para volver a enviar.', 'info');
        });
    }

    // Limpiar formulario completo para un nuevo registro
    function afiliacionLimpiarFormularioRegistro() {
        document.getElementById('formRegistroAfiliacion').reset();
        afiliacionLimpiarErrores();
        afiliacionFirmaLimpiar();
        document.getElementById('reg_deportista_id').value = '0';
        document.getElementById('reg_modo_guardado').value = 'borrador';
        document.getElementById('btnAbrirModalDocs').disabled = true;
        document.getElementById('textoEstadoDocs').innerHTML = 'Debe guardar el borrador primero.';
    }
</script>