<script type="text/javascript">

    $(document).ready(function() {
        //$("#tabla").val("persona");
        //seleccionar_tabla();
    });
    
    function todos_los_roles() {
     if ($( "#rol_g").is( ":checked" )) { 
             $( ".rol_hijos").prop( "checked", true );
        }else{
             $( ".rol_hijos").prop( "checked", false );
        }
  }


    function set_ruta(argument) {
       var tabla = $("#tabla").val();      
        var padre = $("#menu_principal").val();
        if (padre==="") {
            $("#ruta").val("modulos/" + tabla);
        }else{
            $("#ruta").val("modulos/"+padre+'/'+tabla);
        }
    }
    function seleccionar_tabla()
    {     
        set_ruta();
        var tabla = $("#tabla").val();
        $("#menu").val(tabla.replace("_","-"));
        $("#titulo").val(tabla.toUpperCase().replace("_"," "));
        tabla = tabla.substring(0, 1).toUpperCase() + tabla.substring(1);
        $("#titulo_menu").val(tabla.replace("_"," "));

        $.ajax({
            url: page_root+'cargarInfoFormulario',
            type: 'POST',
            dataType: 'json',
            data:$("#formulario_crud").serialize(),
        })
        .done(function(r) {
            $("#f2").html(r.table);
            verificar();
            $("input[type='text']").addClass("form-control");
            $("input[type='checkbox']").addClass("check");
        })

    }

    function generar() {

        $.ajax({
            url: page_root+'generar',
            type: 'POST',
            dataType: 'json',
            data:$("#formulario_crud").serialize(),
        })
        .done(function(r) {
           alert(r.msg);
        });
        
    }

    function verificar() {

         $.ajax({
            url: page_root+'verificar',
            type: 'POST',
            dataType: 'json',
            data:$("#formulario_crud").serialize(),
        })
        .done(function(r) {
           $("#div-verificar").html(r.error);
        });

    }

    function seleccionar_tipo_acceso(v) {
        if (v == 7) {
            $("#div-roles").show();
        } else {
            $("#div-roles").hide();
        }

    }
</script>


        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">ASISTENTE DE CREACIÓN DE FORMULARIO CRUD</h5>






<form id="formulario_crud" >

  <div class="row">
    <div class="form-group col-md-6">
      <label for="base_datos">Base de datos</label>
      <select id="base_datos" name="base_datos" readonly>
        <?php
            llenar_combo("SELECT SCHEMA_NAME as id, SCHEMA_NAME as nombre FROM information_schema.SCHEMATA", false, $cfg['db_database_name']);
        ?>
       </select>
    </div>

    <div class="form-group col-md-6">
      <label for="tabla">Tabla</label>
      <select id="tabla" name="tabla" onchange="seleccionar_tabla()">
           <?php
            llenar_combo("SELECT TABLE_NAME as id, TABLE_NAME as nombre  FROM information_schema.TABLES "
                                    . "WHERE TABLE_SCHEMA='$cfg[db_database_name]'", true);
            ?>
     </select>
    </div>


    <div class="form-group col-md-4">
      <label for="titulo_formulario">Titulo Formulario</label>
      <input type="text" name="titulo_formulario" id="titulo" />
    </div>

    <div class="form-group col-md-4">
      <label for="menu_principal">Menú Padre</label>
       <select id="menu_principal" name="menu_principal" onchange="set_ruta()">
            <?php
                llenar_combo("SELECT menu, nombre FROM admin_menu WHERE ruta IS NULL ORDER BY nombre", true);
            ?>
        </select>
    </div>


    <div class="form-group col-md-4">
      <label for="menu">URL</label>
      <input type="text" name="menu" id="menu" placeholder="Ingrese url sin espacio" onblur="verificar()"/>
    </div>


     <div class="form-group col-md-6">
      <label for="titulo_formulario">Título menú</label>
      <input type="text" name="titulo_menu" id="titulo_menu" placeholder="Nombre del elemento en el menú"/>
    </div>

    <div class="form-group col-md-6">
      <label for="tipo_acceso">Tipo de Acceso</label>
       <select id="tipo_acceso" name="tipo_acceso" onchange="set_ruta()">
           <?php
                llenar_combo("SELECT codigo, descripcion FROM admin_acceso ", false, '7');
            ?>
        </select>
    </div>

    <div class="form-group col-md-12">
      <label for="titulo_formulario">Ruta archivos</label>
      <input type="text" name="ruta" id="ruta" onblur="verificar()"/>
    </div>


    <div class="form-group col-md-12">
      <label for="titulo_formulario">Descripción</label>
      <textarea name="descripcion_menu" id="descripcion_menu" style="min-height: 100px">Permite...</textarea>
    </div>


    <div id="div-verificar" class="form-group col-md-12"></div>


    <div id="div-roles" class="form-group col-md-12">
        <label for="roles">Roles</label>
        <select id="roles" name="rol[]" multiple="true" class="select_auto2_full">
            <?php
                llenar_combo("SELECT * FROM admin_rol ORDER BY nombre ", false,4);
            ?>
        </select>
    </div>

    <div id="f2" class="form-group col-md-12"></div>


  </div>

  


</form>



    <div class="acciones" style="margin-top:10px;width:100%;border:none;overflow:hidden">
        <button type="button" name="accion" value="Generar" onclick="generar()" class="btn btn-block btn-success w-100"/> Generar </button>
    </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

   