let hash_global;
$(document).ready(function () {
    $("body").on("contextmenu",function(e){
       //return false;
    });    
   $('body').bind('cut copy paste', function (e) {
      //e.preventDefault();
    });

   //createCaptcha();
});
$(document).ajaxStart(function(){
    $("#loading").show();
});

$(document).ajaxStop(function(){
    $("#loading").hide();
});


function encriptar_form(id,TOKEN_GLOBAL) {
   var elements = document.getElementById(id).elements; // TOMO LOS ELEMENTOS DEL FORM
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
     
       if (valor=="") {
            var valor_encrypt ="";
        }else{
            var valor_encrypt =  CryptoJS.AES.encrypt(valor,TOKEN_GLOBAL).toString(); //ENCRYPT EL VALOR
        }
      name = CryptoJS.AES.encrypt(name,TOKEN_GLOBAL).toString(); //ENCRYPT KEY
      array[name]=valor_encrypt;
    }
     datos = array;
     return datos;
}


function login()
{

    var obj = this; //Para hacer referencia al objecto principal(this) dentro de las funcniones internas    
    this.base_url = "";
    this.ruta_registro_historia = "";
    this.dominio = "";
    this.token = "";
    this.filtro = 0;
    this.programa = 0;

    this.async = false;
    this.crossDomain = false;
    this.method = "POST";
    this.processData = true;
    this.headers = {};

    // SET
    this.set_url= function(base_url){        
      this.base_url = base_url;
    };

    this.set_token= function(token){        
      this.token = token;
    };


    // CONFIGURACIÓN GENERAL DE AJAX
    this.getSettings= function(datos_form,servie){     

       var settings = { 
           "async":this.async, 
           "crossDomain":this.crossDomain,
           "url": this.base_url+servie,
           "method": this.method,
           "headers": this.headers,
           "dataType": "JSON",
           "processData": this.processData,
           "data": datos_form 
       };

      return settings;
    };

    this.cargar=function(){
      $("#formulario").hide();
      $("#cargar").show();
    };

    this.form=function(){
      $("#formulario").show();
      $("#cargar").hide();
    };


    this.enviar_form=function(){
       const data = encriptar_form("formulario",obj.token);
       obj.cargar();

       const settings = this.getSettings(data,'iniciar');
        $.ajaxSetup(settings);
        $.ajax({
            beforeSend: function(xhr){
              xhr.setRequestHeader("Authorization",obj.token); 
              obj.cargar();
            },
            success: function(r){            
            
              if (r.error==true) {
                msg(r.msg,'Error','error');
                for (ind in r.bad_fields)
                {
                    $("#" + r.bad_fields[ind]).addClass("error");
                }
                obj.form();
                obj.get_token();
                return false;
              }else{  
                localStorage.setItem("stp_k_l_t", r.token);              
                window.location.href = web_root;
              }

            },
            error: function(msg) {
                obj.form();
                msg('Error al iniciar sesión','Error','error');
                obj.get_token();
                return false;
            }
        });
       return false;
    };
    this.ingresar=function(){
       $("#formulario").submit(function(){
          let r = true; //validateCaptcha();
          if (r==false) {
              createCaptcha();
              msg('Código de validación incorrecto, por favor validar el código que aparece en pantalla.','Información','warning');
           }else{
              obj.cargar();
              setTimeout(function() { obj.enviar_form(); }, 800);
          }    
        return false;
       });
    };



    this.enviar_form_restaurar=function(){
       const data = encriptar_form("formulario_restaurar_clave",obj.token);
       obj.cargar();

       const settings = this.getSettings(data,'restaurar');
        $.ajaxSetup(settings);
        $.ajax({
            beforeSend: function(xhr){
              xhr.setRequestHeader("Authorization",obj.token); 
              obj.cargar();
            },
            success: function(r){            
            
              if (r.error==true) {
                msg(r.msg,'Error','error');
                for (ind in r.bad_fields)
                {
                    $("#" + r.bad_fields[ind]).addClass("error");
                }
                obj.form();
                obj.get_token();
                return false;
              }else{                
                swal(r.msg, {
                  content: "input",
                })
                .then((value) => {
                   obj.validar_codigo(value);
                    return false;
                });
                obj.get_token();
                 return false;
              }

            },
            error: function(msg) {
                 msg(r.msg,'Error al iniciar','error');
                obj.get_token();
                return false;
            }
        });
       return false;
    };
    this.restaurar=function(){
       $("#formulario_restaurar_clave").submit(function(){
         obj.cargar();
         setTimeout(function() { obj.enviar_form_restaurar(); return false;  }, 100);    
        return false;
       });
    };


    this.get_token=function(){
        const settings = this.getSettings({},'set_login');
        $.ajaxSetup(settings);
        $.ajax({
            beforeSend: function(xhr){
              xhr.setRequestHeader("Authorization",obj.token); 
            },
            success: function(r){            
              if (r.error==true) {
                msg("Error al crear token, por favor actualizar la pagina",'Error fatal','error');
                $("#formulario").hide();
                return false;
              }else{                
                obj.set_token(r.token);
                return false;
              }
            },
            error: function(msg) {
                msg('Error fatal', "Contactar al administrador", "error");
                return false;
            }
        });
       return false;
    };

     this.validar_codigo=function(c){
       const data = encriptar_form("formulario_restaurar_clave",obj.token);
       obj.cargar();

       const settings = this.getSettings(data,'validar_codigo?q='+c);
        $.ajaxSetup(settings);
        $.ajax({
            beforeSend: function(xhr){
              xhr.setRequestHeader("Authorization",obj.token); 
              obj.cargar();
            },
            success: function(r){            
            
              if (r.error==true) {
                msg(r.msg,'Error','error');
                for (ind in r.bad_fields)
                {
                    $("#" + r.bad_fields[ind]).addClass("error");
                }
                setTimeout(function() { location.reload() }, 5000);
                obj.form();
                obj.get_token();
                return false;
              }else{                
                obj.form();
                msg(r.msg,'Éxito','error');
                //activar_login();
                obj.get_token();
                setTimeout(function() { location.reload() }, 10000);
                
              }

            },
            error: function(msg) {
                  msg(r.msg,'Error al iniciar','error');
                obj.get_token();
                return false;
            }
        });
       return false;
    };
    


} // FIN OBJETO




const l = new login(); 
$(document).ready(function() {
     l.set_url(page_root);
     l.get_token();
     l.ingresar();
     l.restaurar();
});