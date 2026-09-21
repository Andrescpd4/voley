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
                                                      <input type="text" id="bmenu" name="menu" title="Menu" class="filtro_crud" maxlength="100" value="" />
                                                     </div>
                                                 </div>


                                                 <div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Padre</label>
                                                     <div class="col-sm-10">
                                                       <select id="bpadre" name="padre" title="Padre" class="filtro_crud">
                                                            <?php llenar_combo("SELECT menu as id, nombre FROM admin_menu ORDER BY nombre",true); ?>
                                                        </select>   
                                                     </div>
                                                 </div>

                                                 <div class="form-group row">
                                                     <label for="accion" class="col-sm-2 col-form-label">Nombre</label>
                                                     <div class="col-sm-10">
                                                      <input type="text" id="bnombre" name="nombre" class="filtro_crud" title="Nombre" maxlength="50" value="" />
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
                          <th data-field="nombre" data-filter-control="input">Nombre</th>
                          <th data-field="menu" data-filter-control="input">Menú</th>
                          <th data-field="padre">Padre</th>
                          <th data-field="btn">Acción</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>



  <div class="modal fade bd-example-modal-lg" tabindex="-1" id="myModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel"></h4>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                              <div class="modal-body dark-modal">
                              
                              <form id="formulario">

                                   <input type="hidden" id="id" name="id" title="Id" maxlength="11" value=""  class="no-modificable"/>
                                   

                                   <div class="form-group row">
                                      <label for="padre" class="col-sm-2 col-form-label">Padre</label>
                                      <div class="col-sm-10">
                                         <select id="padre" name="padre" title="Padre"><?php llenar_combo("SELECT menu as id, nombre FROM admin_menu ORDER BY nombre",true); ?></select>  
                                      </div>
                                   </div>


                                   <div class="form-group row">
                                      <label for="menu" class="col-sm-2 col-form-label">Menú</label>
                                      <div class="col-sm-10">
                                         <input type="text" id="menu" name="menu" title="Menú" maxlength="100" value="" placeholder="Por favor ingrese menú" />
                                      </div>
                                   </div>


                                   <div class="form-group row">
                                      <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                      <div class="col-sm-10">
                                         <input type="text" id="nombre" name="nombre" title="Nombre" maxlength="100" value="" placeholder="Por favor ingrese nombre" />
                                      </div>
                                   </div>


                                   <div class="form-group row">
                                      <label for="ruta" class="col-sm-2 col-form-label">Ruta</label>
                                      <div class="col-sm-10">
                                         <input type="text" id="ruta" name="ruta" title="Ruta" maxlength="100" value="" placeholder="Por favor ingrese ruta" />
                                      </div>
                                   </div>

                                   <div class="form-group row">
                                      <label for="accion" class="col-sm-2 col-form-label">Acción</label>
                                      <div class="col-sm-10">
                                         <input type="text" id="accion" name="accion" title="Acción" maxlength="100" value="" placeholder="Por favor ingrese acción" />
                                      </div>
                                   </div>


                                   <div class="form-group row">
                                      <label for="orden" class="col-sm-2 col-form-label">Orden</label>
                                      <div class="col-sm-10">
                                         <input type="number" id="orden" name="orden" title="Orden" maxlength="6" value="" placeholder="Por favor ingrese orden" />
                                      </div>
                                   </div>

                                   <div class="form-group row">
                                      <label for="visible" class="col-sm-2 col-form-label">Visible</label>
                                      <div class="col-sm-10">
                                          <select id="visible" name="visible" title="Visible">
                                            <option value="S">Si</option>
                                            <option value="N">No</option>
                                          </select>
                                      </div>
                                   </div>


                                   <div class="form-group row">
                                      <label for="acceso" class="col-sm-2 col-form-label">Acceso</label>
                                      <div class="col-sm-10">
                                         <select id="acceso" name="acceso" title="Acceso"> <?php llenar_combo("SELECT codigo, descripcion FROM admin_acceso ORDER BY descripcion",true); ?></select>
                                      </div>
                                   </div>

                                    <div class="form-group row">
                                      <label for="target" class="col-sm-2 col-form-label">Target</label>
                                      <div class="col-sm-10">
                                         <input type="text" id="orden" name="target" title="Target" maxlength="46" value="" placeholder="Por favor ingrese target" />
                                      </div>
                                   </div>

                                    <div class="form-group row">
                                      <label for="icono" class="col-sm-2 col-form-label">Icono</label>
                                      <div class="col-sm-10">
                                         <input type="text" id="icono" name="icono" title="Icono" maxlength="46" value="" placeholder="Por favor ingrese icono" />
                                      </div>
                                   </div>

                                    <div class="form-group row">
                                      <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                                      <div class="col-sm-10">
                                        <textarea name="descripcion"  title="Descripción" id="descripcion" placeholder="Por favor ingrese descripción" ></textarea>
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



