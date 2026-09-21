<!-- ============================================================
     ROLES — Orquestador (formulario.php)

     Interfaz:
       - Tabla con columnas: ID, Nombre, Usuarios, Acciones
       - Modal CRUD para agregar/modificar roles

     Usa la clase formulario_basico para CRUD automatico.
     ============================================================ -->
<style>
    .gcr-table th {
        font-weight: 600;
        background-color: var(--vz-light);
    }
    .gcr-badge-usuarios {
        background: var(--vz-primary);
        color: #fff;
        padding: 2px 10px;
        border-radius: 99px;
        font-size: 0.75rem;
    }
    .gcr-btn-accion {
        padding: 4px 8px;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ri-shield-user-line me-1"></i> Gestion de Roles
                        </h5>
                        <button type="button" class="btn btn-primary btn-sm accion-agregar" onclick="gcAbrirModalNuevo()">
                            <i class="ri-add-line me-1"></i> Nuevo rol
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabla de roles -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover gcr-table align-middle" id="gcTablaRoles">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Nombre</th>
                                    <th style="width: 120px;">Usuarios</th>
                                    <th style="width: 140px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="gcTbodyRoles">
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <span class="spinner-border spinner-border-sm text-primary"></span>
                                        <span class="text-muted ms-2">Cargando roles...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginacion -->
                    <div id="gcPaginacion" class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted" id="gcTotalRoles">Total: 0 roles</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="gcPaginas"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal CRUD: Crear / Editar rol
     ============================================================ -->
<div class="modal fade" id="gcModalRol" tabindex="-1" aria-labelledby="gcModalRolLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gcModalRolLabel">Nuevo rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="gcFormRol">
                    <input type="hidden" id="gc_rol_id" name="id" value="">
                    <div class="mb-3">
                        <label for="gc_rol_nombre" class="form-label">Nombre del rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="gc_rol_nombre" name="nombre"
                               placeholder="Ej: Entrenador, Jugador" maxlength="50" required>
                    </div>
                    <div class="mb-3">
                        <label for="gc_rol_descripcion" class="form-label">Descripcion</label>
                        <textarea class="form-control" id="gc_rol_descripcion" name="descripcion"
                                  rows="2" maxlength="255" placeholder="Descripcion del rol (opcional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="gcGuardarRol()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmacion para eliminar -->
<div class="modal fade" id="gcModalEliminar" tabindex="-1" aria-labelledby="gcModalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gcModalEliminarLabel">Confirmar eliminacion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Desea eliminar el rol <strong id="gcNombreRolEliminar"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="gcBtnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // ============================================================
    // ROLES — JavaScript del modulo
    // ============================================================

    // Variables del modulo
    var gcModalRol = null;
    var gcModalEliminar = null;
    var gcPaginaActual = 1;
    var gcPorPagina = 10;

    // Inicializar modals al cargar
    $(document).ready(function() {
        gcModalRol = bootstrap.Modal.getOrCreateInstance(document.getElementById('gcModalRol'));
        gcModalEliminar = bootstrap.Modal.getOrCreateInstance(document.getElementById('gcModalEliminar'));
        gcCargarRoles();
    });

    // ============================================================
    // Funcion para mostrar mensajes (Toastify o fallback a alert)
    // ============================================================
    function gcMostrarMsg(texto, titulo, tipo) {
        if (typeof Toastify !== 'undefined') {
            var color = '#405189';
            if (tipo == 'success') color = '#0ab39c';
            if (tipo == 'error') color = '#f06548';
            if (tipo == 'warning') color = '#f7b84b';
            Toastify({
                text: texto,
                duration: 3000,
                gravity: 'top',
                position: 'right',
                style: { background: color }
            }).showToast();
        } else {
            alert(texto);
        }
    }

    // ============================================================
    // Escapar HTML para evitar XSS
    // ============================================================
    function gcEscaparHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    // ============================================================
    // Cargar lista de roles desde el servidor
    // ============================================================
    function gcCargarRoles() {
        var offset = (gcPaginaActual - 1) * gcPorPagina;

        $.ajax({
            url: page_root + 'listar',
            type: 'GET',
            data: {
                offset: offset,
                limit: gcPorPagina
            },
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    gcMostrarMsg(r.msg, 'Error', 'error');
                    return;
                }
                gcRenderizarTabla(r.rows || []);
                gcRenderizarPaginacion(r.total || 0);
            },
            error: function() {
                gcMostrarMsg('Error al cargar roles', 'Error', 'error');
                var tbody = document.getElementById('gcTbodyRoles');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Error al cargar los datos.</td></tr>';
            }
        });
    }

    // ============================================================
    // Renderizar la tabla de roles
    // ============================================================
    function gcRenderizarTabla(roles) {
        var tbody = document.getElementById('gcTbodyRoles');

        if (roles.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay roles registrados.</td></tr>';
            return;
        }

        var html = '';
        for (var i = 0; i < roles.length; i++) {
            var rol = roles[i];
            var nombreEscapado = gcEscaparHtml(rol.nombre);
            var descripcionEscapada = gcEscaparHtml(rol.descripcion);
            var totalUsuarios = rol.total_usuarios || 0;

            // Boton editar con permiso de accion
            var botonEditar = '<button class="btn btn-sm btn-soft-primary gcr-btn-accion" onclick="gcAbrirModalEditar(\'' + rol.id + '\')" title="Editar"><i class="ri-edit-line"></i></button>';
            // Boton eliminar con permiso de accion
            var botonEliminar = '<button class="btn btn-sm btn-soft-danger gcr-btn-accion" onclick="gcAbrirModalEliminar(\'' + rol.id + '\', \'' + nombreEscapado + '\')" title="Eliminar"><i class="ri-delete-bin-line"></i></button>';

            html += '<tr>';
            html += '<td>' + rol.id + '</td>';
            html += '<td title="' + descripcionEscapada + '">' + nombreEscapado + '</td>';
            html += '<td><span class="gcr-badge-usuarios">' + totalUsuarios + '</span></td>';
            html += '<td class="text-center"><div class="d-flex justify-content-center gap-1 accion-modificar accion-eliminar">' + botonEditar + ' ' + botonEliminar + '</div></td>';
            html += '</tr>';
        }

        tbody.innerHTML = html;
    }

    // ============================================================
    // Renderizar la paginacion
    // ============================================================
    function gcRenderizarPaginacion(total) {
        var totalPaginas = Math.ceil(total / gcPorPagina);
        var paginas = document.getElementById('gcPaginas');

        document.getElementById('gcTotalRoles').textContent = 'Total: ' + total + ' rol(es)';

        if (totalPaginas <= 1) {
            paginas.innerHTML = '';
            return;
        }

        var html = '';
        // Boton anterior
        if (gcPaginaActual > 1) {
            html += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="gcIrPagina(' + (gcPaginaActual - 1) + ')">&laquo;</a></li>';
        }
        // Numeros de pagina
        for (var p = 1; p <= totalPaginas; p++) {
            var active = '';
            if (p == gcPaginaActual) {
                active = ' active';
            }
            html += '<li class="page-item' + active + '"><a class="page-link" href="javascript:void(0)" onclick="gcIrPagina(' + p + ')">' + p + '</a></li>';
        }
        // Boton siguiente
        if (gcPaginaActual < totalPaginas) {
            html += '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="gcIrPagina(' + (gcPaginaActual + 1) + ')">&raquo;</a></li>';
        }

        paginas.innerHTML = html;
    }

    // Ir a una pagina especifica
    function gcIrPagina(pagina) {
        gcPaginaActual = pagina;
        gcCargarRoles();
    }

    // ============================================================
    // Abrir modal para crear nuevo rol
    // ============================================================
    function gcAbrirModalNuevo() {
        // Limpiar formulario
        document.getElementById('gc_rol_id').value = '';
        document.getElementById('gc_rol_nombre').value = '';
        document.getElementById('gc_rol_descripcion').value = '';
        document.getElementById('gcModalRolLabel').textContent = 'Nuevo rol';
        gcModalRol.show();
    }

    // ============================================================
    // Abrir modal para editar un rol existente
    // ============================================================
    function gcAbrirModalEditar(id) {
        // Cargar datos del rol por AJAX
        $.ajax({
            url: page_root + 'asignar',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(rol) {
                if (rol.id) {
                    document.getElementById('gc_rol_id').value = rol.id;
                    document.getElementById('gc_rol_nombre').value = rol.nombre || '';
                    document.getElementById('gc_rol_descripcion').value = rol.descripcion || '';
                    document.getElementById('gcModalRolLabel').textContent = 'Editar rol';
                    gcModalRol.show();
                }
            },
            error: function() {
                gcMostrarMsg('Error al cargar el rol', 'Error', 'error');
            }
        });
    }

    // ============================================================
    // Guardar rol (agregar o modificar)
    // ============================================================
    function gcGuardarRol() {
        // Validar nombre
        var nombre = document.getElementById('gc_rol_nombre').value.trim();
        if (nombre === '') {
            gcMostrarMsg('El nombre del rol es obligatorio', 'Validacion', 'warning');
            return;
        }

        var id = document.getElementById('gc_rol_id').value;
        var url = page_root + 'agregar';
        if (id !== '') {
            url = page_root + 'modificar';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: $('#gcFormRol').serialize(),
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    gcMostrarMsg(r.msg, 'Error', 'error');
                    return;
                }
                gcMostrarMsg(r.msg, 'Exito', 'success');
                gcModalRol.hide();
                gcCargarRoles();
            },
            error: function() {
                gcMostrarMsg('Error al guardar el rol', 'Error', 'error');
            }
        });
    }

    // ============================================================
    // Abrir modal de confirmacion para eliminar
    // ============================================================
    function gcAbrirModalEliminar(id, nombre) {
        document.getElementById('gcNombreRolEliminar').textContent = nombre;
        document.getElementById('gcBtnConfirmarEliminar').onclick = function() {
            gcConfirmarEliminar(id);
        };
        gcModalEliminar.show();
    }

    // ============================================================
    // Confirmar y ejecutar eliminacion
    // ============================================================
    function gcConfirmarEliminar(id) {
        $.ajax({
            url: page_root + 'eliminar',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            headers: { 'Authorization': TOKEN_GLOBAL },
            success: function(r) {
                if (r.error) {
                    gcMostrarMsg(r.msg, 'Error', 'error');
                    return;
                }
                gcMostrarMsg(r.msg, 'Exito', 'success');
                gcModalEliminar.hide();
                gcCargarRoles();
            },
            error: function() {
                gcMostrarMsg('Error al eliminar el rol', 'Error', 'error');
            }
        });
    }
</script>
