<script type="text/javascript">
    $(document).ready(function(e) {
                 
        $("#persona_nombre").autocompletar2(page_root + "listarPersonas", {
            form: "formulario",
            inputId: "persona_id",
            minLength: 3});        
    });
</script>

<script type="text/javascript>
function ver() {
    $(".cargar").css({"display":"none","font-size":"100"});
    $(".acciones").css({"display":"block","font-size":"100"});
}


$(function(){
      $(".cargar").css({"display":"none","font-size":"100"});
      $("#formulario").submit(function(){
            $.ajax({
            url:page_root + "aceptar",
            type: "POST",
            dataType: "JSON",
            data: $("#formulario").serialize(),

            beforeSend: function(){
                 $(".cargar").css({"display":"block","font-size":"100"});
                 $(".acciones").css({"display":"none","font-size":"100"});
            },

            success: function(data){
            var r = data;
            if (r.error == true)
            {
                for (ind in r.bad_fields)
                {
                    $("#" + r.bad_fields[ind]).addClass("error");
                }
                msg($(".respuesta"),r.msg,"error");
                ver();
            } else
            {
                msg($(".respuesta"),r.msg,"exito");
                ver();
                document.getElementById("formulario").reset();
            }

            },
            error: function(msg) {
            alert("ATENCION, envia de nuevo tu informacion.");
            ver();
            }
            });
            return false;
            })
 })



</script>

<!-- FORMULARIO -->
 <div class="card">
    <div class="card-body">
        <form id="formulario" method="POST" class="form-horizontal" style="margin:auto;width:85%">


        <div class="form-group row">
            <label for="accion" class="col-sm-2 col-form-label">Usuario</label>
             <div class="col-sm-10">
                <input type="hidden" name="persona_id" id="persona_id" 
                           class="no-modificable" title="Persona">
                <input type="text" name="persona_nombre" id="persona_nombre" 
                           class="no-modificable" title="Persona">
             </div>
        </div>

        <div class="form-group row">
           <label for="accion" class="col-sm-2 col-form-label">Contraseña</label>
            <div class="col-sm-10">
               <input type="text" name="clave" id="clave"  value="" title="Contraseña" maxlength="30" required placeholder="Por favor ingrese la clave que desea asignar..." />
            </div>
        </div>




    <div class="error"></div>
    <div class="cargar" style="float:right;">  <img src="<?php echo WEB_ROOT ?>img/loader.gif" class="img-responsive"></div>
    <div class="box-footer">
        <input type="submit" name="accion" value="Aceptar" class="btn btn-block btn-success" style="float:right;width:80px" />

    </div>
    </div>
    <div class="respuesta" style="margin-top:0px"><i class="textomsg"></i></div>
</form>

    </div>
</div>

<!-- FIN FORMULARIO -->