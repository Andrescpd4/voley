<!-- FORMULARIO USUARIOS VOLEY+ -->
<!-- Sin datos de logistics: no salario, no plantas, no contrato -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <!-- Filtros -->
                    <div class="accordion" id="accordionFiltros">
                        <div class="accordion-item accordion-wrapper">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed accordion-light-primary txt-primary" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree"
                                    aria-expanded="false"
                                    aria-controls="collapseThree"><i class="svg-wrapper mr-4" style="margin-right: 2px;" data-feather="check-square"></i> Filtros <i class="svg-color" data-feather="chevron-down"></i>
                                </button>
                            </h2>
                            <div class="accordion-collapse collapse" id="collapseThree" aria-labelledby="headingThree" data-bs-parent="#accordionFiltros">
                                <div class="accordion-body">
                                    <form class="div-form-busqueda" id="form-busqueda">
                                        <div style="width:100%; margin:auto;height: 100%">
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Usuario de ingreso</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="blogin" name="login" title="Usuario" placeholder="Usuario" maxlength="50" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Documento</label>
                                                <div class="col-sm-10">
                                                    <input type="text" id="bidentifica" name="identifica" title="Documento" placeholder="Documento" maxlength="20" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Rol</label>
                                                <div class="col-sm-10">
                                                    <select id="brol" name="rol" title="Rol">
                                                        <option value="">Todos</option>
                                                        <?php llenar_combo("SELECT id, nombre FROM admin_rol WHERE visible = 'S' ORDER BY id", false) ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-8"></div>
                                                <div class="col-md-2">
                                                    <button type="button" onclick="crud_buscar()"
                                                        class="btn btn-square btn-outline-info tooltip_btn w-100 mt-2">
                                                        <i class="fa fa-search"></i>
                                                        Buscar</button>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" onclick="crud_limpiar()"
                                                        class="btn btn-square btn-outline-warning tooltip_btn w-100 mt-2">
                                                        <i class="fa fa-reply"></i> Limpiar
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filtros -->

                    <div id="toolbar">
                        <button id="btn_agregar"
                            onclick="f.agregar($table)"
                            class="btn btn-square btn-outline-success accion-agregar"
                            title="Click para agregar registro"
                            data-bs-original-title="Click para agregar registro">
                            <i class="fa fa-plus"></i> Agregar Registro
                        </button>
                    </div>
                    <table
                        id="table_crud"
                        data-toolbar="#toolbar"
                        data-toggle="table"
                        data-locale="es-CL"
                        data-id-field="id"
                        data-show-refresh="true"
                        data-show-toggle="true"
                        data-show-fullscreen="true"
                        data-show-columns="true"
                        data-show-columns-toggle-all="true"
                        data-show-export="true"
                        data-click-to-select="true"
                        data-height="600"
                        data-page-list="[10, 25, 50, 100, all]"
                        data-ajax="ajaxRequest"
                        data-buttons-class="btn btn-light"
                        data-search="false"
                        data-resizable="true"
                        data-side-pagination="server"
                        data-show-pagination-switch="true"
                        data-mobile-responsive="true"
                        data-check-on-init="true"
                        data-pagination="true" class="table table-striped ">
                        <thead>
                            <tr>
                                <th data-field="_NUM_" data-filter-control="input">#</th>
                                <th data-field="foto" data-formatter="imageFormatter">Foto</th>
                                <th data-field="user">Usuario</th>
                                <th data-field="nombre_completo">Nombre de usuario</th>
                                <th data-field="rol_nombre">Rol</th>
                                <th data-field="telefono">Celular</th>
                                <th data-field="activo" data-formatter="formatoActivo">Estado</th>
                                <th data-field="btn">Acción</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN FORMULARIO -->

<div class="modal fade bd-example-modal-xl" tabindex="-1" id="myModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel"></h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body dark-modal">

                <form id="formulario">
                    <!-- BLOQUE 1: DATOS PERSONALES -->
                    <h4 class="card-title mb-0 flex-grow-1">1. Datos personales</h4>
                    <hr>
                    <div class="form-group row" style="display:none">
                        <label class="col-sm-2 col-form-label">Id</label>
                        <div class="col-sm-10">
                            <input type="text" id="id" name="id" title="Id" placeholder="Id" maxlength="10" value="" class="no-modificable" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tipo de documento</label>
                        <div class="col-sm-4">
                            <select id="tipo_documento" name="tipo_documento" title="Tipo de documento">
                                <option value="CC">CC - Cedula</option>
                                <option value="TI">TI - Tarjeta de identidad</option>
                                <option value="RC">RC - Registro civil</option>
                                <option value="CE">CE - Cedula de extranjeria</option>
                                <option value="PASAPORTE">Pasaporte</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <label class="col-sm-2 col-form-label">Documento</label>
                        <div class="col-sm-4">
                            <input type="text" id="identifica" name="identifica" title="Documento" placeholder="Numero de documento" maxlength="20" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Primer nombre</label>
                        <div class="col-sm-4">
                            <input type="text" id="nombre1" name="nombre1" title="Primer nombre" placeholder="Primer nombre" maxlength="50" value="" />
                        </div>
                        <label class="col-sm-2 col-form-label">Segundo nombre</label>
                        <div class="col-sm-4">
                            <input type="text" id="nombre2" name="nombre2" title="Segundo nombre" placeholder="Segundo nombre (opcional)" maxlength="50" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Primer apellido</label>
                        <div class="col-sm-4">
                            <input type="text" id="apellido1" name="apellido1" title="Primer apellido" placeholder="Primer apellido" maxlength="50" value="" />
                        </div>
                        <label class="col-sm-2 col-form-label">Segundo apellido</label>
                        <div class="col-sm-4">
                            <input type="text" id="apellido2" name="apellido2" title="Segundo apellido" placeholder="Segundo apellido (opcional)" maxlength="50" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Genero</label>
                        <div class="col-sm-4">
                            <select id="genero" name="genero" title="Genero">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="OTRO">Otro</option>
                            </select>
                        </div>
                        <label class="col-sm-2 col-form-label">Celular</label>
                        <div class="col-sm-4">
                            <input type="text" id="telefono" name="telefono" title="Celular" placeholder="Celular" maxlength="20" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Correo</label>
                        <div class="col-sm-10">
                            <input type="email" id="correo" name="correo" title="Correo" placeholder="Correo electronico" maxlength="100" value="" />
                        </div>
                    </div>

                    <!-- BLOQUE 2: ACCESO AL SISTEMA -->
                    <hr>
                    <h4 class="card-title mb-0 flex-grow-1">2. Acceso al sistema</h4>
                    <hr>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Usuario de ingreso</label>
                        <div class="col-sm-4">
                            <input type="text" id="login" name="login" title="Usuario" placeholder="Usuario para entrar (manual)" maxlength="50" value="" />
                        </div>
                        <label class="col-sm-2 col-form-label">Rol</label>
                        <div class="col-sm-4">
                            <select id="rol" name="rol" title="Rol">
                                <?php llenar_combo("SELECT id, nombre FROM admin_rol WHERE visible = 'S' ORDER BY id", false) ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Estado</label>
                        <div class="col-sm-4">
                            <select id="activo" name="activo" title="Estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <label class="col-sm-2 col-form-label">Contrasena</label>
                        <div class="col-sm-4">
                            <input type="password" id="clave" name="clave" title="Contrasena" placeholder="Vacia = no cambia (nuevo: 12345)" maxlength="120" value="" autocomplete="new-password" />
                        </div>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <div class="dlg-acciones">
                    <button class="btn btn-square btn-outline-info tooltip_btn"
                        id="btn_aceptar"
                        type="button"
                        title="Click para aceptar"
                        data-bs-original-title="Click para aceptar"><i class="fa fa-check"></i> Aceptar</button>
                    <button class="btn btn-square btn-outline-danger tooltip_btn"
                        data-bs-dismiss="modal"
                        type="button"
                        title="Click para cancelar"
                        data-bs-original-title="Click para cancelar"><i class="fa fa-close"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var $table = $('#table_crud');
    var f = new formulario(true, 650);

    // Pedir pagina de usuarios al backend con filtros de busqueda
    function ajaxRequest(params) {
        var elementos = document.getElementById('form-busqueda').elements;
        for (var i = 0; i < elementos.length; i++) {
            var campo = elementos[i];
            if (campo.name && campo.name !== '') {
                params.data[campo.name] = campo.value.trim();
            }
        }
        setTimeout(function() {
            $.ajax({
                    url: page_root + 'listar',
                    type: 'GET',
                    dataType: 'json',
                    data: $.param(params.data),
                })
                .done(function(r) {
                    params.success(r);
                    setTimeout(function() {
                        $('[data-toggle="tooltip"]').tooltip({
                            container: 'body'
                        });
                    }, 2000);
                })
                .fail(function() {
                    console.log("error");
                });
        }, 1000);
    }

    // Armar etiqueta de estado con plantilla de backticks
    function formatoActivo(valor_estado) {
        var numero_estado = parseInt(valor_estado, 10);
        if (numero_estado === 1) {
            return `<span class="badge bg-success">Activo</span>`;
        } else {
            return `<span class="badge bg-secondary">Inactivo</span>`;
        }
    }

    // Armar foto del usuario con plantilla de backticks
    function imageFormatter(valor_foto) {
        var ruta_foto = valor_foto;
        if (!ruta_foto || ruta_foto === '') {
            ruta_foto = 'img/user.png';
        }
        return `<img src="${ruta_foto}" style="width: 60px; border-radius: 50%;">`;
    }

    function crud_editar(id) {
        f.modificar(id, $table);
    }

    function crud_mostrar(id) {
        f.mostrar(id, $table);
    }

    function crud_eliminar(id) {
        f.eliminar(id, $table);
    }

    function crud_buscar() {
        $table.bootstrapTable('refresh');
    }

    function crud_limpiar() {
        document.getElementById("form-busqueda").reset();
        $table.bootstrapTable('refresh');
    }

    // Al abrir el modal, limpiar la clave por seguridad
    jQuery(document).ready(function($) {
        setTimeout(function() {
            $table.bootstrapTable('refresh');
        }, 2100);
        $('#myModal').on('shown.bs.modal', function() {
            var campo_clave = document.getElementById('clave');
            campo_clave.value = '';
        });
    });
</script>
