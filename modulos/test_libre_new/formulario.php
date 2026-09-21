<script type="text/javascript">
  
$(function(){
    $("#formulario").submit(function(){
        $.ajax({
                url:page_root + "aceptar",
                type: "POST",
                dataType: "JSON",
                data: data,
        beforeSend: function(xhr){
               xhr.setRequestHeader("Authorization",TOKEN_GLOBAL); 
            },
        success: function(data){            
            var r = data;
            if (r.error == true)
            {
                for (ind in r.bad_fields)
                {
                    $("#" + r.bad_fields[ind]).addClass("error");
                }
                msg(r.msg,"Error al procesar solicitud","error");              
            } else
            {
                msg(r.msg);
                document.getElementById("formulario").reset();
            }
        },
        error: function(msg) {
               msg("Error desconocido","Error","error");
            }
        });
        return false;
    })

 })

</script>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <form id="formulario" method="POST" class="form-horizontal" style="margin:auto;width:95%">

                          <table style="width:100%">                          
                             
            <tr> 
                <td class="tdi">Campo 1</td>
                <td class="tdc">:</td>
                <td class="tdd">
                    <input type="text" encrypt="true" name="campo1" id="campo1"  value="" title="Campo 1" placeholder="Campo 1" maxlength="30" required/>
                </td>            
            </tr>

            <tr> 
                <td class="tdi">Campo 2</td>
                <td class="tdc">:</td>
                <td class="tdd">
                    <input type="text" encrypt="true" name="campo2" id="campo2"  value="" title="Campo 2" placeholder="Campo 2" maxlength="50" required/>
                </td>            
            </tr>
                          
                          </table>
                          
                          <div class="cargando_libre"></div>
                          <div class="box-footer acciones" id="libre_acciones">
                             		<button type="submit" name="accion" value="Aceptar" class="btn btn-success btn-icon-text accion-Aceptar" style="float:right;"><i class="icon-check"></i> Aceptar</button>

                          </div>
                          <div class="respuesta" style="margin-top:70px"><i class="textomsg"></i></div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>