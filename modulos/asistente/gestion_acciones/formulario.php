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
                                                     <label for="accion" class="col-sm-2 col-form-label">Menú</label>
                                                     <div class="col-sm-10">
                                                       <select id="bmenu" name="menu" title="Menú" class="select_auto2">
                                                        <?php llenar_combo("SELECT menu, concat_ws(' : ',nombre,menu) as nombre FROM admin_menu ORDER BY menu",true); ?>
                                                      </select> 
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Acción</label>
                                                     <div class="col-sm-10">
                                                        <input type="text" id="baccion" name="accion" title="Acción" placeholder="Acción" maxlength="60" value="" />
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
                            <th data-field="nombre">Menú</th><th data-field="accion">Acción</th><th data-field="tipo_accion">Tipo acción</th><th data-field="archivo">Archivo</th>
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

 <div class="modal fade bd-example-modal-lg" tabindex="-1" id="myModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel"></h4>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                              <div class="modal-body dark-modal">
                              
                              <form id="formulario">

                                   <div class="form-group row" style="display:none">
                                                     <label for="accion" class="col-sm-2 col-form-label">Id</label>
                                                     <div class="col-sm-10">
                                                        <input type="text" id="id" name="id" title="Id" placeholder="Id" maxlength="10" value=""  class="no-modificable"/>
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Menú</label>
                                                     <div class="col-sm-10">
                                                      <select id="menu" name="menu" title="Menú" class="select_auto2">
                                                        <?php llenar_combo("SELECT menu, concat_ws(' : ',nombre,menu) as nombre FROM admin_menu ORDER BY menu",true); ?>
                                                      </select> 
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Acción</label>
                                                     <div class="col-sm-10">
                                                        <input type="text" id="accion" name="accion" title="Acción" placeholder="Acción" maxlength="60" value="" />
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Tipo acción</label>
                                                     <div class="col-sm-10">
                                                      <select id="tipo_accion" name="tipo_accion" title="Tipo acción">
                                                        <?php llenar_combo("SELECT codigo, codigo as nombre FROM admin_tipo_accion ORDER BY archivo",true); ?>
                                                      </select> 
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Archivo</label>
                                                     <div class="col-sm-10">
                                                        <input type="text" id="archivo" name="archivo" title="Archivo" placeholder="Archivo" maxlength="100" value="" />
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Requiere permiso</label>
                                                     <div class="col-sm-10">
                                                        <select id="requiere_permiso" name="requiere_permiso" title="Requiere permiso">
                                                            <option></option>
                                                            <option value="S">Si</option>
                                                            <option value="N">No</option>
                                                        </select>    
                                                     </div>
                                                 </div><div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Descripción</label>
                                                     <div class="col-sm-10">
                                                        <textarea id="descripcion" name="descripcion" title="Descripción" placeholder="Descripción" maxlength="500"></textarea>
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
      params.data[element.name]=$.trim(element.value);
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
           setTimeout(function() { $('[data-toggle="tooltip"]').tooltip({ container: 'body' }); }, 2000);
       })
       .fail(function() {
           console.log("error");
       })

      }, 1000);
      
       
   }

   function crud_editar(id) {
      f.modificar(id,$table);
   }

    function crud_mostrar(id) {
      f.mostrar(id,$table);
   }

    function crud_eliminar(id) {
      f.eliminar(id,$table);
   }

   function crud_buscar() {
      $table.bootstrapTable('refresh');
   }
   function crud_limpiar() {
      document.getElementById("form-busqueda").reset();
      $table.bootstrapTable('refresh');
   }

</script>