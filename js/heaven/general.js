// INGENIERO - FABIO GARCIA ALVAREZ ( 1077461284 )
var color_input_bloqueado = "#e5e5e5";


function encriptar_form(id,TOKEN_GLOBAL) {
   

   var elements = document.getElementById(id).elements; // TOMO LOS ELEMENTOS DEL FORM
   console.log(elements);
   var array = {}; 
    for (var i = 0, element; element = elements[i++];) {
      var id=element.id;
      var name=element.name;
      var valor = $.trim(element.value);
      var input = document.getElementById(id);
      
         try {
             var encrypt = input.getAttribute('encrypt'); // TOMO EL ATRIBUTO
         }
         catch(err) {
            var encrypt = 'false'; // POR SI NO TIENE EL ATRIBUTO
         }
     
      if (encrypt=='true') {
         if (valor=="") {
            var valor_encrypt ="";
         }else{
            var valor_encrypt =  CryptoJS.AES.encrypt(valor,TOKEN_GLOBAL).toString(); //ENCRYPT EL VALOR
         }
        
      }else{
        var valor_encrypt =  valor;
      }
      name = CryptoJS.AES.encrypt(name,TOKEN_GLOBAL).toString(); //ENCRYPT KEY
      array[name]=valor_encrypt;
    }
     datos = array;
     return datos;
}




function set_token() {
    $.ajax({
        url: web_root + 'inicio/set_token',
        type: 'POST',
        dataType: 'json',
        async: true,
        data: {},
    })
    .done(function(r) {

       if (r.error==true) {
         if (r.cod_error==2) {
            msg(r.msg,'Información','warning');
            window.open(web_root + "iniciar-sesion",'_blank');
         }else{
            msg(r.msg,'error','error');
            setTimeout(function() {  window.location.href = web_root + 'cerrar-sesion'; }, 3000);
         }
       }else{
        localStorage.setItem("stp_k_l_t", r.data);      
       }
       
    }).fail(function() {
       msg('No se pudo validar el TOKEN','error','error');
       setTimeout(function() {  window.location.href = web_root + 'cerrar-sesion'; }, 3000);
   });
    
}



function dialogo(id, titulo, ancho)
{

    id = id.replace("#", "");

    try
    {
        $("#" + id).dialog("destroy");
    }
    catch (e) {
    }
    $("#" + id).dialog(
            {
                modal: true,
                minHeight: 120,
                width: ancho,
                closeOnEscape: false,
                title: titulo,
                resizable: false,
                open: function() {
                    //Codigo para centrar manualmente, el centrado por defecto del jQueryUI no centra del todo bien
                    //$(this).dialog('option', 'position',"top");
                    var t = $(this).parent(), w = $("body");

                    var left2 = $(w).width() / 2 - $(t).width() / 2;
                    left2 = parseInt(left2);

                    $(t).css("left", left2 + "px");

                    var top2 = $(w).height() / 2 - $(t).height() / 2;
                    top2 = parseInt(top2);

                    $(t).css("top", top2 + "px");
                }
            });

}

function stripHTML(cadena)
{
    cadena = cadena.replace(/<[^>]+>/g, '\n');
    cadena = cadena.replace(/\n\n/g, '\n');
    return jQuery.trim(cadena);
}



function cargar_input(id, url, param)
{
    var ip;
    if (ip = document.getElementById(id))
    {
        ip.value = "Cargando...";
        var xhr = post(url, param);
        ip.value = xhr.responseText;
    }
}




function URLDecode(encodedString)
{
    var output = encodedString;
    output = output.replace(/\+/g, '%20');
    var binVal, thisString;
    var myregexp = /(%[^%]{2})/;
    while ((match = myregexp.exec(output)) != null
            && match.length > 1
            && match[1] != '')
    {
        binVal = parseInt(match[1].substr(1), 16);
        thisString = String.fromCharCode(binVal);
        output = output.replace(match[1], thisString);
    }
    return output;
}

function URLEncode(str)
{
    
    var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
    var ret = str.toString();

    var replacer = function(search, replace, str)
    {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };
    // The histogram is identical to the one in urldecode.  
    histogram['!'] = '%21';
    histogram['%20'] = '+';
    // Begin with encodeURIComponent, which most resembles PHP's encoding functions  
    ret = encodeURIComponent(ret);
    for (search in histogram)
    {
        replace = histogram[search];
        ret = replacer(search, replace, ret) // Custom replace. No regexing  
    }
    // Uppercase for full PHP compatibility  
    return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
        return "%" + m2.toUpperCase();
    });
    return ret;
}



/******************************************************************/


function validar(form_id)
{

    if (!form_id)
        form_id = "formulario";

    $("#" + form_id + " div.error").html("");

    if ($("#" + form_id).valid() == false)
    {
        var html = $("#" + form_id + " div.error").html();
        alert("Por favor revise los siguientes campos: \n\n" + stripHTML(html));
        return false;
    }
    return true;
}

function bloquear_entradas(formulario)
{
    $("#" + formulario).find("input, select, textarea").each(function(index, element) {
        element.disabled = true;
    });
}

function bloquear_no_modifibles(formulario)
{
    $(".no-modificable").attr("disabled", true);
}

function desbloquear_entradas(formulario)
{
    $("#" + formulario).find("input, select, textarea").each(function(index, element) {
        element.disabled = false;
    });
}


function asignar_json(formulario, s_json)
{
    $("#formulario .select_auto").each(function(){
        $("#"+this.id).select2('destroy');
    });

    var datos = s_json; //Convertir los datos a una estructura
    var campo;
    for (campo in datos) //Recorrer estructura para asignar los datos
    {
       asignar_valor(formulario, campo, datos[campo]);  
    }
     $('.select_auto').select2({ placeholder: 'Seleccione una opción...', });
}

function asignar_valor(formulario, nombre, valor)
{
    var obj = $("#" + formulario).find("*[id=" + nombre + "]");
    if (obj.length == 0){
        
    }else if (obj.length == 1){

        if ($(obj[0]).hasClass("tinymce"))
        {
            //tinyMCE.get(e.name).getContent();
            console.log(obj[0]);
            console.log(obj[0].name);
            tinyMCE.get(obj[0].name).setContent(valor);
        }
        else if ($(obj).hasClass("select2-offscreen"))
        {
            $(obj).select2("data", valor);
        }else if ($(obj[0]).hasClass("select_auto2"))
        {
            $.each(valor.split(","), function(i,e){
                $('#'+nombre)[0].sumo.selectItem(e);
            });            
        }
        else
        {
            if (obj[0].tagName == "SELECT")
            {
                var v = String(valor == null ? "" : valor);
                var opts = obj[0].options;
                var idx = -1;
                for (var i = 0; i < opts.length; i++)
                {
                    if (String(opts[i].value) == v) { idx = i; break; }
                }
                if (idx < 0)
                {
                    v = v.toLowerCase();
                    for (var i = 0; i < opts.length; i++)
                    {
                        if (String(opts[i].value).toLowerCase() == v) { idx = i; break; }
                    }
                }
                if (idx >= 0)
                    obj[0].selectedIndex = idx;
            }
            else
            {
                obj[0].value = valor;
            }
        }
    }
    else
    {
        for (i = 0; i < obj.length; i++)
        {
            if (obj[i].value == valor)
                obj[i].checked = true;
        }
    }
}


function cargarCombo(url, id_formulario, id_combo, blanco, predeterminado, funcion)
{
    var cb = document.getElementById(id_combo);
    cb.options.length = 0; //Limpiar el combo
    cb.options.add(new Option("Cargando...", "Cargando..."));

    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        data: $("#" + id_formulario).serialize(), // Pasar todos los datos del formulario como parametro
        success: function(data) //funcion que se llama al terminar la petición AJAX
        {
            cb.options.length = 0; //Limpiar el combo
            if (blanco == true)
                cb.options.add(new Option("", ""));// Primera opcion en blanco

            var json = data;  //Pasar los datos al formato JSON, que es el que interpreta javascript
            for (i = 0; i < json.length; i++)
            {
                cb.options.add(new Option(json[i].nombre, json[i].codigo));
            }

            if (predeterminado)
                cb.value = predeterminado;
            if (funcion)
                funcion(cb, predeterminado);

        }
    });

}


function msg_cargando(op){
   if (op==true) {
     $("#libre_acciones").hide();
     $("#cargando_libre").show();
   }
    $("#cargando_libre").hide();
    $("#libre_acciones").show();

}

function msg(msg,title='Éxito',tipo='success'){
    Swal.fire({
       title: title,
       html: msg,
       icon: tipo,
       confirmButtonText:'<i class="fa fa-thumbs-up"></i> Entendido!',
       confirmButtonAriaLabel: 'Entendido!',
       cancelButtonText:'<i class="fa fa-thumbs-down"></i>',
       cancelButtonAriaLabel: ''
    })
}

function msg_error_button(msg){
    swal({
       title: "Error",
       text: msg,
       icon: "error",
       button: "Cerrar",
       dangerMode: true,
    })
}


function createCaptcha() {
  
  document.getElementById('captcha_form').innerHTML = "";
  var charsArray =
  "0123456789abcdefhijklmnsrstuvwxzABCDEFGHIJKLMNOPQRSTUVWXYZ@!#$%&*";
  var lengthOtp = 5;
  var captcha = [];
  for (var i = 0; i < lengthOtp; i++) {
    //below code will not allow Repetition of Characters
    var index = Math.floor(Math.random() * charsArray.length + 1); //get the next character from the array
    if (captcha.indexOf(charsArray[index]) == -1)
      captcha.push(charsArray[index]);
    else i--;
  }
  var canv = document.createElement("canvas");
  canv.id = "captcha";
  canv.width = 120;
  canv.height = 35;
  var ctx = canv.getContext("2d");
  ctx.font = "30px Georgia";
  ctx.fillStyle  = "#000000";
  //ctx.strokeRect(20, 20, 150, 100);
  ctx.fillText(captcha.join(""), 0, 30);
 
  //storing captcha so that can validate you can save it somewhere else according to your specific requirements
  code = captcha.join("");
  document.getElementById("captcha_form").appendChild(canv); // adds the canvas to the body element
  $("#validador").attr("aria-label","Campo obligatorio,  escribir aquí el código de validación "+code);
}

function validateCaptcha() {
  if (document.getElementById("validador").value == code) {
     return true;
  }else{
    return false;
  }
  return false;
}