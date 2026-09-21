<!-- FORMULARIO -->
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
                                            <table style="width:100%">
                                                <div class="form-group row">
                                                    <label for="accion" class="col-sm-2 col-form-label">Usuario de ingreso</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="buser" name="user" title="Usuario" placeholder="Usuario" maxlength="115" value="" />
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="accion" class="col-sm-2 col-form-label">Documento</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" id="bidentifica" name="identifica" title="Documento" placeholder="Documento" maxlength="20" value="" />
                                                    </div>
                                                </div>
                                            </table>

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
                            class="btn btn-square btn-outline-success"
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
                                <th data-field="telefono">Celular</th>
                                <th data-field="correo">Correo</th>
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
                    <h4 class="card-title mb-0 flex-grow-1">Datos personales</h4>
                    <hr>
                    <div class="form-group row" style="display:none">
                        <label for="accion" class="col-sm-2 col-form-label">Id</label>
                        <div class="col-sm-10">
                            <input type="text" id="id" name="id" title="Id" placeholder="Id" maxlength="10" value="" class="no-modificable" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Usuario de ingreso</label>
                        <div class="col-sm-10">
                            <input type="text" id="user" name="user" title="Usuario" placeholder="Usuario de ingreso" maxlength="115" value="" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Documento</label>
                        <div class="col-sm-10">
                            <input type="text" id="identifica" name="identifica" title="Documento" placeholder="Documento" maxlength="20" value="" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Sexo</label>
                        <div class="col-sm-4">
                            <select id="sexo_id" name="sexo_id" title="Sexo" placeholder="Sexo">
                                <?php llenar_combo("SELECT * FROM sexo", true) ?>
                            </select>

                        </div>

                        <label for="accion" class="col-sm-2 col-form-label">Tipo de documento</label>
                        <div class="col-sm-4">

                            <select id="tipoide" name="tipoide" title="Tipo de documento" placeholder="Tipo de documento">
                                <?php llenar_combo("SELECT * FROM tipo_documento", true) ?>
                            </select>

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Primer nombre</label>
                        <div class="col-sm-4">
                            <input type="text" id="nombre1" name="nombre1" title="Primer nombre" placeholder="Primer nombre" maxlength="80" value="" />
                        </div>

                        <label for="accion" class="col-sm-2 col-form-label">Segundo nombre</label>
                        <div class="col-sm-4">
                            <input type="text" id="nombre2" name="nombre2" title="Segundo nombre" placeholder="Segundo nombre" maxlength="80" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Primero apellido</label>
                        <div class="col-sm-4">
                            <input type="text" id="apellido1" name="apellido1" title="Primero apellido" placeholder="Primero apellido" maxlength="80" value="" />
                        </div>

                        <label for="accion" class="col-sm-2 col-form-label">Segundo apellido</label>
                        <div class="col-sm-4">
                            <input type="text" id="apellido2" name="apellido2" title="Segundo apellido" placeholder="Segundo apellido" maxlength="80" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Celular</label>
                        <div class="col-sm-10">
                            <input type="number" id="telefono" name="telefono" title="Celular" placeholder="Celular" maxlength="120" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Correo</label>
                        <div class="col-sm-10">
                            <input type="email" id="correo" name="correo" title="Correo" placeholder="Correo" maxlength="145" value="" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Estado de ingreso</label>
                        <div class="col-sm-10">

                            <select id="estado" name="estado" title="Estado" placeholder="Estado">
                                <?php llenar_combo("SELECT * FROM estado", true) ?>
                            </select>

                        </div>
                    </div>

                    <hr>
                    <h4 class="card-title mb-0 flex-grow-1">Rol</h4>
                    <hr>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Rol</label>
                        <div class="col-sm-10">

                            <select id="rol" name="rol" title="Rol" placeholder="Rol">
                                <option value="">SIN ROL</option>
                                <?php llenar_combo("SELECT * FROM admin_rol", false) ?>
                            </select>

                        </div>
                    </div>




                    <hr>
                    <h4 class="card-title mb-0 flex-grow-1">Datos Laborales</h4>
                    <hr>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                        <div class="col-sm-4">
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Salario</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="salario" name="salario" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Auxilio Alimentaci&oacute;n</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="auxilio_alimentacion" name="auxilio_alimentacion" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tipo de Contrato</label>
                        <div class="col-sm-4">
                            <select id="tipo_contrato" name="tipo_contrato" class="form-control">
                                <?php llenar_combo("SELECT nombre AS value, nombre AS text FROM tipo_contrato WHERE visible = 1 ORDER BY id", true); ?>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h4 class="card-title mb-0 flex-grow-1">Permisos de planta</h4>
                    <hr>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Plantas</label>
                        <div class="col-sm-10">

                            <select id="plantas" name="plantas[]" title="Rol" placeholder="Rol" multiple class="select_auto2">
                                <?php llenar_combo("SELECT * FROM plantas", true) ?>
                            </select>

                        </div>
                    </div>




                    <hr>
                    <h4 class="card-title mb-0 flex-grow-1">Asignar contraseña</h4>
                    <hr>
                    <div class="form-group row">
                        <label for="accion" class="col-sm-2 col-form-label">Contraseña</label>
                        <div class="col-sm-10">
                            <input type="number" id="clave" name="clave" title="Contraseña" placeholder="Contraseña" maxlength="120" value="" />
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

    function ajaxRequest(params) {

        let elements = document.getElementById('form-busqueda').elements; // TOMO LOS ELEMENTOS DEL FORM
        for (var i = 0, element; element = elements[i++];) {
            params.data[element.name] = $.trim(element.value);
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
                        //$("table").resizableColumns();
                    }, 2000);
                })
                .fail(function() {
                    console.log("error");
                })

        }, 1000);


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

    function imageFormatter(value) {
        return '<img src="' + value + '" style="    width: 60px;">'
    }

    jQuery(document).ready(function($) {
        setTimeout(function() {
            $table.bootstrapTable('refresh');
        }, 2100);
    });
</script>