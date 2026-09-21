<!-- ============================================================
  ADMIN USUARIOS — ORQUESTADOR DE LA VISTA
  Estructura del módulo (patrón AGENTS.md §2):
    formulario.php (este archivo) -> CSS, utilerías JS compartidas,
                                    modales comunes e include de tabs
    tabs/listado.php             -> tabla de usuarios con acciones
    tabs/crear_editar.php        -> modal para crear/editar usuario
    tabs/asignar_rol.php          -> modal para asignar rol

  REGLAS §10: plantillas con backticks, funciones pequeñas que arman
  cada pieza de HTML, bucles for normales, if/else, var, comentarios
  en español. CERO emojis: solo íconos Remixicon del tema Velzon.

  Las utilerías se declaran ANTES de los includes para que las
  funciones estén disponibles en los tabs.
============================================================ -->

<style>
    /* ---------- Estilos para la tabla de usuarios ---------- */

    /* Fila de la tabla al pasar el mouse */
    .au-fila:hover {
        background-color: #f8f9fa;
    }

    /* Botones de acción dentro de la tabla */
    .au-btn-accion {
        border: none;
        background: transparent;
        padding: .25rem .5rem;
        border-radius: .35rem;
        cursor: pointer;
    }
    .au-btn-accion:hover {
        background-color: #f3f6f9;
    }
    .au-btn-editar { color: #1565c0; }
    .au-btn-eliminar { color: #dc0606; }
    .au-btn-asignar { color: #405189; }

    /* Estado vacío de la tabla */
    .au-zona-vacia {
        padding: 56px 16px;
        text-align: center;
        color: #878a99;
    }
    .au-zona-vacia i {
        font-size: 54px;
        display: block;
        margin-bottom: 10px;
        color: #cbd0dc;
    }

    /* Spinner de carga */
    .au-spinner {
        display: inline-block;
        width: 24px;
        height: 24px;
        border: 3px solid #e9ecef;
        border-top-color: #405189;
        border-radius: 50%;
        animation: au-girar 0.8s linear infinite;
    }
    @keyframes au-girar {
        to { transform: rotate(360deg); }
    }

    /* Encabezado de la tabla */
    .au-encabezado th {
        background-color: #f3f6f9;
        font-weight: 600;
        color: #405189;
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.5px;
    }
</style>

<script type="text/javascript">
// ============================================================
// UTILERÍAS COMPARTIDAS DEL MÓDULO (prefijo au)
// ============================================================

// Lista completa de usuarios cargada desde el backend
var AU_USUARIOS = [];

// Lista de roles disponibles para los selects
var AU_ROLES = [];

// Lista de personas sin usuario (para crear nuevo)
var AU_PERSONAS_DISPONIBLES = [];

// ID del usuario actualmente en edición (0 = ninguno)
var AU_EDITANDO_ID = 0;

// ------------------------------------------------------------
// AJAX compartido.
// accion:     listar | agregar | modificar | eliminar | asignar
//             listarPersonasSinUsuario | listarRoles
// datosExtra: objeto {campo: valor}. Se envía por POST.
// ------------------------------------------------------------
function auAjax(accion, datosExtra, callback) {
    var formData = new FormData();
    if (datosExtra) {
        for (var clave in datosExtra) {
            formData.append(clave, datosExtra[clave]);
        }
    }
    $.ajax({
        url: page_root + accion,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', TOKEN_GLOBAL);
        },
        success: function(respuesta) {
            callback(respuesta);
        },
        error: function() {
            auMensaje('No hubo respuesta del servidor', 'Error', 'error');
        }
    });
}

// ------------------------------------------------------------
// MENSAJES CON SWEETALERT2
// ------------------------------------------------------------

// Muestra un mensaje con SweetAlert2
function auMensaje(titulo, texto, tipo) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: tipo,
        confirmButtonText: 'Aceptar'
    });
}

// Muestra un diálogo de confirmación antes de una acción destructiva
function auConfirmar(titulo, texto, al_confirmar) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    }).then(function(resultado) {
        if (resultado.isConfirmed) {
            al_confirmar();
        }
    });
}

// ------------------------------------------------------------
// ESCAPE DE TEXTO PARA EVITAR XSS
// ------------------------------------------------------------

function auEsc(texto) {
    // Convertir a string y reemplazar los caracteres peligrosos uno por uno
    var seguro = String(texto);
    seguro = seguro.split('&').join('&amp;');
    seguro = seguro.split('<').join('&lt;');
    seguro = seguro.split('>').join('&gt;');
    seguro = seguro.split('"').join('&quot;');
    seguro = seguro.split("'").join('&#039;');
    return seguro;
}

// ------------------------------------------------------------
// ABRIR Y CERRAR MODALES BOOTSTRAP POR SU ID
// ------------------------------------------------------------

function auAbrirModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
}

function auCerrarModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).hide();
}

// ------------------------------------------------------------
// PIEZA DE ESTADO VACÍO PARA TABLAS SIN DATOS
// ------------------------------------------------------------

function auVacio(titulo, mensaje) {
    return `<div class="au-zona-vacia">
                <i class="ri-user-line"></i>
                <h6>${titulo}</h6>
                <p class="mb-0 small">${mensaje}</p>
            </div>`;
}

// ------------------------------------------------------------
// UNA FILA DE LA TABLA DE USUARIOS
// Recibe un objeto usuario y devuelve el HTML de la fila
// ------------------------------------------------------------

function auFilaUsuario(usuario) {
    // Columna de acciones (editar, eliminar, asignar rol)
    var acciones = `
        <div class="d-flex gap-1">
            <button type="button" class="au-btn-accion au-btn-editar"
                    title="Editar usuario" aria-label="Editar"
                    onclick="auEditarUsuario(${usuario.persona_id})">
                <i class="ri-edit-line fs-5"></i>
            </button>
            <button type="button" class="au-btn-accion au-btn-asignar"
                    title="Cambiar rol" aria-label="Asignar rol"
                    onclick="auAbrirModalAsignar(${usuario.persona_id})">
                <i class="ri-user-settings-line fs-5"></i>
            </button>
            <button type="button" class="au-btn-accion au-btn-eliminar"
                    title="Eliminar usuario" aria-label="Eliminar"
                    onclick="auEliminarUsuario(${usuario.persona_id})">
                <i class="ri-delete-bin-line fs-5"></i>
            </button>
        </div>`;

    return `<tr class="au-fila">
                <td>${auEsc(usuario.identificacion)}</td>
                <td>${auEsc(usuario.nombre)}</td>
                <td><span class="badge bg-primary-subtle text-primary">${auEsc(usuario.rol)}</span></td>
                <td>${acciones}</td>
            </tr>`;
}

// ------------------------------------------------------------
// PINTAR LA TABLA COMPLETA DE USUARIOS
// ------------------------------------------------------------

function auPintarTabla() {
    var contenedor = document.getElementById('auCuerpoTabla');

    // Caso 1: no hay usuarios
    if (AU_USUARIOS.length == 0) {
        contenedor.innerHTML = `<tr><td colspan="4">${auVacio('Sin usuarios registrados', 'No hay usuarios con rol asignado todavía.')}</td></tr>`;
        return;
    }

    // Caso 2: armar una fila por cada usuario
    var html = '';
    for (var i = 0; i < AU_USUARIOS.length; i++) {
        html += auFilaUsuario(AU_USUARIOS[i]);
    }
    contenedor.innerHTML = html;
}

// ------------------------------------------------------------
// REFRESCAR LA TABLA LLAMANDO AL BACKEND
// ------------------------------------------------------------

function auCargarUsuarios() {
    // Mostrar spinner mientras carga
    document.getElementById('auCuerpoTabla').innerHTML =
        `<tr><td colspan="4" class="text-center py-4">
            <span class="au-spinner"></span>
            <p class="text-muted mt-2">Cargando usuarios...</p>
        </td></tr>`;

    auAjax('listar', {}, function(respuesta) {
        if (respuesta.error == true) {
            auMensaje('Error', respuesta.msg, 'error');
            return;
        }
        AU_USUARIOS = respuesta.data;
        auPintarTabla();
    });
}

// ------------------------------------------------------------
// CARGAR ROLES PARA LOS SELECTS
// ------------------------------------------------------------

function auCargarRoles() {
    auAjax('listarRoles', {}, function(respuesta) {
        if (respuesta.error == true) {
            return;
        }
        AU_ROLES = respuesta.data;
        auLlenarSelectRoles();
    });
}

// Llena todos los selects que usan roles (crear, editar, asignar)
function auLlenarSelectRoles() {
    var selects = document.querySelectorAll('.au-select-rol');
    for (var i = 0; i < selects.length; i++) {
        var select = selects[i];
        var valor_actual = select.value;
        var html = '<option value="">Seleccione un rol...</option>';
        for (var j = 0; j < AU_ROLES.length; j++) {
            html += `<option value="${AU_ROLES[j].id}">${auEsc(AU_ROLES[j].nombre)}</option>`;
        }
        select.innerHTML = html;
        // Restaurar valor si existía
        if (valor_actual) {
            select.value = valor_actual;
        }
    }
}

// ------------------------------------------------------------
// CREAR NUEVO USUARIO
// ------------------------------------------------------------

function auAbrirModalCrear() {
    // Resetear el formulario y el estado de edición
    AU_EDITANDO_ID = 0;
    document.getElementById('auFormularioUsuario').reset();
    document.getElementById('auTituloModal').textContent = 'Nuevo Usuario';
    document.getElementById('auBtnGuardar').textContent = 'Crear usuario';

    // Cargar personas disponibles
    auAjax('listarPersonasSinUsuario', {}, function(respuesta) {
        if (respuesta.error == true) {
            auMensaje('Error', respuesta.msg, 'error');
            return;
        }
        AU_PERSONAS_DISPONIBLES = respuesta.data;

        // Llenar el select de personas
        var select = document.getElementById('auPersona');
        var html = '<option value="">Seleccione una persona...</option>';
        for (var i = 0; i < AU_PERSONAS_DISPONIBLES.length; i++) {
            html += `<option value="${AU_PERSONAS_DISPONIBLES[i].id}">${auEsc(AU_PERSONAS_DISPONIBLES[i].text)}</option>`;
        }
        select.innerHTML = html;

        // Abrir el modal
        auAbrirModal('auModalUsuario');
    });
}

// ------------------------------------------------------------
// EDITAR USUARIO EXISTENTE
// ------------------------------------------------------------

function auEditarUsuario(persona_id) {
    // Buscar el usuario en la lista cargada
    var encontrado = null;
    for (var i = 0; i < AU_USUARIOS.length; i++) {
        if (AU_USUARIOS[i].persona_id == persona_id) {
            encontrado = AU_USUARIOS[i];
            break;
        }
    }

    if (encontrado === null) {
        auMensaje('Error', 'No se encontró el usuario', 'error');
        return;
    }

    // Configurar el modal en modo edición
    AU_EDITANDO_ID = persona_id;
    document.getElementById('auTituloModal').textContent = 'Editar Usuario';
    document.getElementById('auBtnGuardar').textContent = 'Guardar cambios';

    // Cargar la persona en el select (aunque esté ocupada)
    auAjax('listarPersonasSinUsuario', {}, function(respuesta) {
        if (respuesta.error == true) {
            return;
        }
        AU_PERSONAS_DISPONIBLES = respuesta.data;

        // Agregar la persona actual al select (porque ya tiene usuario)
        AU_PERSONAS_DISPONIBLES.push({
            id: encontrado.persona_id,
            text: encontrado.nombre + ' [' + encontrado.identificacion + '] (actual)'
        });

        // Llenar el select de personas
        var select = document.getElementById('auPersona');
        var html = '<option value="">Seleccione una persona...</option>';
        for (var i = 0; i < AU_PERSONAS_DISPONIBLES.length; i++) {
            html += `<option value="${AU_PERSONAS_DISPONIBLES[i].id}">${auEsc(AU_PERSONAS_DISPONIBLES[i].text)}</option>`;
        }
        select.innerHTML = html;
        select.value = encontrado.persona_id;

        // Seleccionar el rol actual
        document.getElementById('auRol').value = encontrado.rol_id;

        // Abrir el modal
        auAbrirModal('auModalUsuario');
    });
}

// ------------------------------------------------------------
// GUARDAR USUARIO (crear o modificar según AU_EDITANDO_ID)
// ------------------------------------------------------------

function auGuardarUsuario() {
    // Obtener los valores del formulario
    var persona_id = document.getElementById('auPersona').value;
    var rol = document.getElementById('auRol').value;

    // Validar que ambos campos estén completos
    if (persona_id == '' || persona_id == '0') {
        auMensaje('Aviso', 'Debe seleccionar una persona', 'warning');
        return;
    }
    if (rol == '' || rol == '0') {
        auMensaje('Aviso', 'Debe seleccionar un rol', 'warning');
        return;
    }

    // Determinar si es crear o modificar
    var accion = '';
    var datos = { persona_id: persona_id, rol: rol };

    if (AU_EDITANDO_ID > 0) {
        // Modificar usuario existente
        accion = 'modificar';
        datos.persona_id = AU_EDITANDO_ID;
    } else {
        // Crear nuevo usuario
        accion = 'agregar';
    }

    // Enviar al backend
    auAjax(accion, datos, function(respuesta) {
        if (respuesta.error == true) {
            auMensaje('Error', respuesta.msg, 'error');
            return;
        }
        auCerrarModal('auModalUsuario');
        auMensaje('Listo', respuesta.msg, 'success');
        auCargarUsuarios();
    });
}

// ------------------------------------------------------------
// ELIMINAR USUARIO
// ------------------------------------------------------------

function auEliminarUsuario(persona_id) {
    // Buscar el usuario para mostrar su nombre en el mensaje
    var encontrado = null;
    for (var i = 0; i < AU_USUARIOS.length; i++) {
        if (AU_USUARIOS[i].persona_id == persona_id) {
            encontrado = AU_USUARIOS[i];
            break;
        }
    }

    var mensaje = 'Esta acción no se puede deshacer.';
    if (encontrado !== null) {
        mensaje = 'Se eliminará el usuario de: ' + encontrado.nombre;
    }

    auConfirmar('¿Eliminar usuario?', mensaje, function() {
        auAjax('eliminar', { persona_id: persona_id }, function(respuesta) {
            if (respuesta.error == true) {
                auMensaje('Error', respuesta.msg, 'error');
                return;
            }
            auMensaje('Listo', respuesta.msg, 'success');
            auCargarUsuarios();
        });
    });
}

// ------------------------------------------------------------
// MODAL ASIGNAR ROL — abre el modal para cambiar solo el rol
// ------------------------------------------------------------

function auAbrirModalAsignar(persona_id) {
    // Buscar el usuario
    var encontrado = null;
    for (var i = 0; i < AU_USUARIOS.length; i++) {
        if (AU_USUARIOS[i].persona_id == persona_id) {
            encontrado = AU_USUARIOS[i];
            break;
        }
    }

    if (encontrado === null) {
        auMensaje('Error', 'No se encontró el usuario', 'error');
        return;
    }

    // Llenar los campos del modal
    document.getElementById('auAsignarPersonaId').value = encontrado.persona_id;
    document.getElementById('auAsignarNombre').value = encontrado.nombre;
    document.getElementById('auAsignarIdentifica').value = encontrado.identificacion;
    document.getElementById('auAsignarRolActual').value = encontrado.rol;
    document.getElementById('auAsignarNuevoRol').value = '';

    // Abrir el modal
    auAbrirModal('auModalAsignar');
}

// Guardar el nuevo rol desde el modal de asignar
function auGuardarAsignacion() {
    var persona_id = document.getElementById('auAsignarPersonaId').value;
    var nuevo_rol = document.getElementById('auAsignarNuevoRol').value;

    if (nuevo_rol == '' || nuevo_rol == '0') {
        auMensaje('Aviso', 'Debe seleccionar un nuevo rol', 'warning');
        return;
    }

    auAjax('modificar', { persona_id: persona_id, rol: nuevo_rol }, function(respuesta) {
        if (respuesta.error == true) {
            auMensaje('Error', respuesta.msg, 'error');
            return;
        }
        auCerrarModal('auModalAsignar');
        auMensaje('Listo', 'Rol actualizado correctamente', 'success');
        auCargarUsuarios();
    });
}

// ============================================================
// INICIALIZACIÓN CUANDO EL DOM ESTÁ LISTO
// ============================================================

$(document).ready(function() {
    // Cargar datos iniciales
    auCargarUsuarios();
    auCargarRoles();

    // Botón superior para crear nuevo usuario
    $('#auBtnNuevo').on('click', function() {
        auAbrirModalCrear();
    });

    // Botón guardar dentro del modal de crear/editar
    $('#auBtnGuardar').on('click', function() {
        auGuardarUsuario();
    });

    // Botón guardar dentro del modal de asignar rol
    $('#auBtnGuardarAsignacion').on('click', function() {
        auGuardarAsignacion();
    });
});
</script>

<!-- ============================================================
     CONTENIDO PRINCIPAL: tarjeta con tabla de usuarios
     ============================================================ -->

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- Encabezado de la tarjeta con botón para crear -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="ri-user-line me-1"></i>
                            Administración de Usuarios
                        </h5>
                        <small class="text-muted">Gestione los usuarios y sus roles en el sistema</small>
                    </div>
                    <button type="button" class="btn btn-primary" id="auBtnNuevo">
                        <i class="ri-add-line me-1"></i> Nuevo Usuario
                    </button>
                </div>

                <!-- Cuerpo de la tarjeta: tabla de usuarios -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="au-encabezado">
                                <tr>
                                    <th>Identificación</th>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th style="width: 140px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="auCuerpoTabla">
                                <!-- Las filas se llenan dinámicamente con JavaScript -->
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <span class="au-spinner"></span>
                                        <p class="text-muted mt-2">Cargando usuarios...</p>
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

<!-- ============================================================
     MODAL: CREAR / EDITAR USUARIO
     ============================================================ -->

<div class="modal fade" id="auModalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="auTituloModal">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="auFormularioUsuario">
                    <!-- Campo oculto para modo edición -->
                    <input type="hidden" id="auEditandoId" value="0">

                    <!-- Selección de persona -->
                    <div class="mb-3">
                        <label class="form-label" for="auPersona">Persona</label>
                        <select class="form-select" id="auPersona">
                            <option value="">Seleccione una persona...</option>
                        </select>
                        <small class="text-muted">Solo aparecen personas sin usuario asignado.</small>
                    </div>

                    <!-- Selección de rol -->
                    <div class="mb-3">
                        <label class="form-label" for="auRol">Rol</label>
                        <select class="form-select au-select-rol" id="auRol">
                            <option value="">Seleccione un rol...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="auBtnGuardar">Crear usuario</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL: ASIGNAR ROL (cambiar solo el rol de un usuario existente)
     ============================================================ -->

<div class="modal fade" id="auModalAsignar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="auFormularioAsignar">
                    <!-- ID oculto -->
                    <input type="hidden" id="auAsignarPersonaId" value="0">

                    <!-- Datos de la persona (solo lectura) -->
                    <div class="mb-3">
                        <label class="form-label">Identificación</label>
                        <input type="text" class="form-control" id="auAsignarIdentifica" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="auAsignarNombre" readonly>
                    </div>

                    <!-- Rol actual (solo lectura) -->
                    <div class="mb-3">
                        <label class="form-label">Rol actual</label>
                        <input type="text" class="form-control" id="auAsignarRolActual" readonly>
                    </div>

                    <!-- Nuevo rol (editable) -->
                    <div class="mb-3">
                        <label class="form-label" for="auAsignarNuevoRol">Nuevo rol</label>
                        <select class="form-select au-select-rol" id="auAsignarNuevoRol">
                            <option value="">Seleccione un rol...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="auBtnGuardarAsignacion">Guardar</button>
            </div>
        </div>
    </div>
</div>
