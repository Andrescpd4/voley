<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/treeview/jquery.treeview.css" type="text/css"/> 
<script type="text/javascript" src="<?php echo WEB_ROOT ?>js/jquery/jquery.treeview.min.js"></script>
<script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/general.js"></script>
<script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/jquery.extra.js"></script>

<script type="text/javascript">
    $().ready(function()
    {
        $("#arbol").treeview({
            collapsed: true
        });

        $("input:checkbox").click(verificar);
    });


    function marcar(chk){

        $(chk).parent().find("input:checkbox").each(function(ind, obj) {
            obj.checked = chk.checked;
        });

    }

    function cargar() {
        if ($("#rol").val() == "")
        {
            $("#arbol").hide();
            $("#div-acciones").hide();
            return;
        }

        $("#div_cargando").html("Cargando...");
        $("#div_cargando").css("display", "");
        $("#arbol").css("display", "none");

        limpiar();
        $.post(page_root + "cargar", $("#formulario").values(), function(data) {
            var r = data;
            for (var i = 0; i < r.length; i++)
            {
                
                if (r[i].menu)
                {
                    $("#menu_" + r[i].menu).prop("checked", true);
                }

                if (r[i].accion)
                {
                    $("#accion_" + r[i].accion).prop("checked", true);
                }

            }

            verificar();

            $("#div_cargando").css("display", "none");
            $("#arbol").css("display", "");
            $("#div-acciones").show();
            msg("Se han cargado los permisos del rol seleccionado.");

        });

    }

    function guardar()
    {

        if (document.getElementById("rol").value == ""){
            msg("Debe seleccionar un rol primero.");
            return;
        }
        Swal.fire({
                    title:"Confirmar",
                    html: '¿Realmente guardar los cambios en el rol seleccionado?',
                    icon: "question",
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'SI',
                    denyButtonText: 'NO',
        }).then((result) => {
            if (result.isConfirmed) {
                $("#div-acciones").hide();
                $("#div_cargando").html("Guardando...");
                $("#div_cargando").css("display", "");
                $("#arbol").css("display", "none");
                $.post(page_root + "guardar", $("#formulario").values(), function(data) {
                    var r = data;
                    $("#div_cargando").css("display", "none");
                    $("#arbol").css("display", "");
                    msg(r.msg);
                    $("#div-acciones").show();
                });
            }
        })
        
            
    }

    function limpiar()
    {
        $("#arbol input:checkbox").prop("checked", false);
    }

    function verificar()
    {
        var o = $("#arbol input:checkbox");

        for (i = o.length - 1; i >= -1; i--)
        {
            var obj = o.get(i);
            var parent = $(obj).parent();
            var marcados = parent.find("input:checkbox:checked");
            
            if (marcados.length == 0)
            {
                obj.checked = false;
            }
            else
            {
                obj.checked = true;

            }
        }
    }

</script>


<br/> 

<div class="col-md-12 grid-margin stretch-card">
          <div class="card position-relative">
            <div class="card-body">


                    <div   style="width:570px; margin:auto">
                        <form id="formulario">
                           
                            <select style="width: 100%" name="rol" id="rol" onchange="cargar();">
                                <option  value="">Seleccione un rol de la lista</option>
                                <?php
                                llenar_combo("SELECT id, nombre FROM admin_rol ORDER BY nombre");
                                ?>
                            </select><hr>

                            <div style="border: 1px solid silver; height: 300px; overflow: auto">
                                <div  style="display:none; font-size:12pt; font-weight:bold" id="div_cargando">
                                    Cargando...
                                </div>
                                <div id="arbol" style="display: none;">
                                    <?php
                                        listarMenu(NULL);
                                    ?>
                                </div>
                            </div>




                            <hr />


                            <div style="text-align:right; padding-bottom:3px; display:none" id="div-acciones">
                                <input type="button" value="Limpiar" onclick="limpiar();" class="btn btn-block btn-warning" style="margin-left:10px;width:120px;float:right;"/>
                                <input type="button" value="Recargar" onclick="cargar();" class="btn btn-block btn-primary" style="margin-left:10px;width:120px;float:right"/>
                                 <input type="button" value="Guardar" onclick="guardar()" class="btn btn-block btn-success" style="margin-left:10px;width:120px;float:right;"/>
                            </div>
                        </form>
                    </div>


            </div>
          </div>
        </div>


<?php

function listarMenu($padre) {
    global $db;

    if ($padre == NULL) {
        $sql = "select * from admin_menu where padre is null AND acceso='7' order by orden";
    } else {
        $sql = "select * from admin_menu where padre = '" . $db->escape_string($padre) . "' AND acceso='7' order by orden";
    }

    $rs =  $db->select_all($sql);

    echo("<ul>");
    foreach ($rs as $key => $rw) {  
    
        echo("<li>");
        echo("<input type='checkbox'  onclick='marcar(this)' id='menu_" . $rw['menu'] . "' name='menu[" . $rw['menu'] . "]' value='S'/>");
        echo("<label   for='menu_" . $rw["menu"] . "'>" . $rw["nombre"] . "</label>");

        $rs2 = $db->select_all("select * from admin_accion where requiere_permiso='S' AND menu='" . $db->escape_string($rw["menu"]) . "' order by accion");

        if (count($rs2)>0) {
            echo ("<ul>");
            foreach ($rs2 as $key => $rw2) {        
                $rw2['accion'][0] = strtoupper($rw2['accion'][0]);
                echo("<li>");
                echo("<input type='checkbox' class='accion' id='accion_{$rw2["id"]}' name='accion[{$rw2["id"]}]' value='S' />");
                echo("<label for='accion_" . $rw2["id"] . "'>" . $rw2["accion"] . "</label>");
                echo("</li>");
            }
            echo("</ul>");
        }

        $hijos = $db->select_one("SELECT COUNT(*) FROM admin_menu WHERE padre='" . $db->escape_string($rw["menu"]) . "'");
        if ($hijos > 0) {
            listarMenu($rw["menu"]);
        }
        echo("</li>");
    }
    echo("</ul>");
}
?>