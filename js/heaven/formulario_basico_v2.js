// INGENIERO - FABIO GARCIA ALVAREZ ( 1077461284 )
function formulario(dialogo_flotante, ancho_dialgo)
{
    var obj = this; //Para hacer referencia al objecto principal(this) dentro de las funcniones internas		
    this.ancho_dialgo = ancho_dialgo;
    this.dialogo_flotante = dialogo_flotante;

    $("#btn_cancelar").click(function() {
        obj.ocultar_dialogo();
    });
    this.validar = function() {
        return true;
        //return validar("formulario");
    };

    this.validar_agregar = function() {
        return this.validar();
    };
    this.validar_modificar = function() {
        return this.validar();
    };

    this.iniciar_agregar = function() {
        return true;
    };
    this.iniciar_modificar = function() {
        return true;
    };


    this.mostrar_dialogo = function(titulo)
    {
        $('#btn_aceptar').unbind('click');
        if (this.dialogo_flotante == true)
        {
            $("#myModal").modal("show");
            $(".modal-title").html(titulo);
        }
        else
        {
            var t = $(".titulo-formulario").text().trim();
            t = (t == "") ? titulo : titulo + " (" + t + ")";
            $("#formulario").prepend("<div class='ui-widget-header titulo2'>" +
                    t.toUpperCase() + "</div>");
            $("#formulario").css("width", this.ancho_dialgo + "px");
            $("#formulario").css("margin", "auto");

            //$("#dialog").show();
            $("#myModal").modal("show");
            $("#grid").parent().hide();
        }
    }


    this.ocultar_dialogo = function()
    {
        $('#btn_aceptar').unbind('click');
        if (this.dialogo_flotante == true) {
            //$('#dialog').dialog('destroy');
            $("#myModal").modal("hide");
        } else {
            $("#dialog div.titulo2").remove();
            $("#myModal").modal("hide");
            //$("#dialog").hide();
            $("#grid").parent().show();
        }
    }

    this.limpiar = function() {
        $("#formulario .select_auto").each(function(){
           $("#"+this.id).select2('destroy');
        });

        $("#formulario .select_auto2").each(function(){
            $("#"+this.id)[0].sumo.unSelectAll();
        });

        document.getElementById("formulario").reset();
        $('.select_auto').select2({ placeholder: 'Seleccione una opción...', });
       
       // $(".error").removeClass("error"); //Limpiar errores de validación
       // $("tr.soporte").remove(); // Codigo agregado para sistema de investigacion       
    }

    /* *********************************************************************************************************** */
    this.agregar = function($table)
    {
        obj.limpiar();
        obj.mostrar_dialogo('Agregar');
        desbloquear_entradas("formulario");

        //Ejecutar lo siguiente al darle click en el boton Aceptar
        $("#btn_aceptar").click(function()
        {
            if (obj.validar_agregar() == false)
                return; //Llamar a la funcion de validaci�n
            //Mostrar barra de progreso
            $("#formulario").append("<div class='progreso'> <img src='" + web_root + "img/pb.gif' /> <br/> Agregando... </div>");
            //Ocultar botones
            $("#formulario .btn").hide();

            $.ajax({
                type: "POST",
                url: page_root + "agregar",
                dataType: 'json',
                data: $("#formulario").values(),
                success: function(data)
                {
                    $("#formulario .btn").show(); //Mostrar botones
                    $("#formulario .progreso").remove(); //Eliminar barra de progrreso

                    try
                    {
                        var r = data;
                        for (ind in r.bad_fields) //Marcar campos con error
                        {
                            $("#formulario *[name=" + r.bad_fields[ind] + "]").addClass("error");
                        }

                        msg(r.msg,'Error de validación','warning');
                        if (r.error == false)
                        {
                            obj.ocultar_dialogo();
                            msg(r.msg);
                             $table.bootstrapTable('insertRow', {
                             index: 0,
                                row: r.row
                             });
                            //var tr = grid.addRow(r.row, 0); //Agregar la nueva fila al principio
                            //$(tr).addClass("recentAdd"); //Ponerle color a la nueva fila mediante clases
                            //tr.setAttribute("id", 'tr_'+r.row.id);
                        }
                    }
                    catch (ex)
                    {
                        msg("Error desconocido",'Error',"error");
                        console.log(ex);
                    }

                }
            });
        });

    };

    /* *********************************************************************************************************** */
    this.asignar = function(id)
    {
        this.limpiar();        
        $.get(page_root + "asignar", "id=" + id, function(data) {

            try {
                asignar_json("formulario", data);

                //Agregar soportes.
                var json = data;
                for (indSop in json.soportes) {
                    var sop = json.soportes[indSop];
                    agregar_archivo(sop.id, sop.descripcion, "");
                }
                return data;
            }
            catch (ex)
            {
                msg("Error desconocido",'Error',"error");
                console.log(ex);
            }


        });

    };

    /* *********************************************************************************************************** */
    this.mostrar = function(id){
       
        if (id == ""){
            alert("Debe seleccionar primero el registro a mostrar.");
        }else{
            this.mostrar_dialogo("Mostrar");
            $('#btn_aceptar').click(function() {
                obj.ocultar_dialogo();
            });
            this.asignar(id);
            bloquear_entradas("formulario");
        }
    };

    /* *********************************************************************************************************** */
    this.modificar = function(id,$table)
    {
         if (id == ""){
             msg("Debe seleccionar primero el registro a eliminar.",'Seleccionar registro','info');
        }
        else
        {
            desbloquear_entradas("formulario");
            bloquear_no_modifibles("formulario");

            if (this.iniciar_modificar() != true)
                return;

            obj.mostrar_dialogo("Modificar");
            obj.asignar(id);


            $("#btn_aceptar").click(function()
            {
                if (obj.validar_agregar() == false)
                    return; //Llamar a la funcion de 

                Swal.fire({
                   title:"Confirmar",
                   html: '¿Realmente desea modificar el registro seleccionado?',
                   icon: "question",
                   showDenyButton: true,
                   showCancelButton: false,
                   confirmButtonText: 'SI',
                   denyButtonText: 'NO',
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                           
                           $("#formulario").append("<div class='progreso'> <img src='" + web_root + "img/pb.gif' /> <br/> Modificando... </div>");
                           //Ocultar botones
                           $("#formulario .btn").hide();

                             $.ajax({
                                 type: "POST",
                                 url: page_root + "modificar",
                                 dataType:"JSON",
                                 data: $("#formulario").values(),
                                 success: function(r)
                                 {
                                     $("#formulario .btn").show(); //Mostrar botones
                                     $("#formulario .progreso").remove(); //Eliminar barra de progrreso
                                     try {
                            
                                         if (r.error == false)
                                         {
                                             $table.bootstrapTable('remove', {
                                                 field: 'id',
                                                 values: [id]
                                              });

                                              $table.bootstrapTable('insertRow', {
                                                index: 0,
                                                row: r.row
                                              });
                                             obj.ocultar_dialogo();
                                             msg(r.msg);
                                              
                                         }else{
                                            msg(r.msg,'Error al modificar elemento','error');
                                         }
                                     } catch (ex) {
                                         msg("Error desconocido",'Error',"error");
                                         console.log(ex);
                                     }
                                 }
                             });


                    } else if (result.isDenied) {
                       msg("Solicitud cancelada",'Cancelado','info');
                    }
                })


            });
        }

    };

    /* *********************************************************************************************************** */
    this.eliminar = function(id,$table)
    {
        if (id == ""){
            msg("Debe seleccionar primero el registro a eliminar.",'Seleccionar registro','info');
        }
        else
        {
            this.mostrar_dialogo("Eliminar");
            this.asignar(id);
            bloquear_entradas("formulario");


            $("#btn_aceptar").click(function()
            {
                
                Swal.fire({
                   title:"Confirmar",
                   html: '¿Realmente desea eliminar el registro seleccionado?',
                   icon: "question",
                   showDenyButton: true,
                   showCancelButton: false,
                   confirmButtonText: 'SI',
                   denyButtonText: 'NO',
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                           
                           $("#formulario").append("<div class='progreso'> <img src='" + web_root + "img/pb.gif' /> <br/> Eliminando... </div>");
                           //Ocultar botones
                           $("#formulario input:button").hide();

                             $.ajax({
                                 type: "POST",
                                 url: page_root + "eliminar",
                                 dataType:"JSON",
                                 data: $("#formulario").values(),
                                 success: function(r)
                                 {
                                     $("#formulario input:button").show(); //Mostrar botones
                                     $("#formulario .progreso").remove(); //Eliminar barra de progrreso
                                     try {
                            
                                         if (r.error == false)
                                         {
                                             $table.bootstrapTable('remove', {
                                                 field: 'id',
                                                 values: [id]
                                              });
                                             obj.ocultar_dialogo();
                                             msg(r.msg);
                                              
                                         }else{
                                            msg(r.msg,'Error al eliminar elemento','error');
                                         }
                                     } catch (ex) {
                                         msg("Error desconocido",'Error',"error");
                                         console.log(ex);
                                     }
                                 }
                             });


                    } else if (result.isDenied) {
                       msg("Solicitud cancelada",'Cancelado','info');
                    }
                })


               


            });
        }
    };

    this.buscar = function() {
        var filtros = $("#form-busqueda").values();
        grid.url = page_root + 'listar?' + filtros;
        grid.moveFirst();
    };
}
 