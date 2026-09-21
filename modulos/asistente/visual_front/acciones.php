<?php
class Formulario extends Base {
    function validar_token()
    {
        $hash = $_SERVER['HTTP_AUTHORIZATION'];
       
        if ($hash) {//No hay errores de validacion
            
        }else { //Errores de validación
            $r['error'] = true;
            $r['msg'] = 'Error en TOKEN';
            echo json_encode($r);
            exit(0);
        }
        return true;
    }
    function validar() {        
        $v = new Validation($_POST);
    
    
    
        $result = $v->validate();

        if ($result['messages'] == "") {//No hay errores de validacion
            return true;
        } else { //Errores de validación
            $r['error'] = true;
            $r['msg'] = $result['messages'];
            $r['bad_fields'] = $result['bad_fields'];
            $r['errors'] = $result['errors'];
            echo json_encode($r);
            exit(0);
        }
        return true;
    }

/* Aceptar 
     @Autor : FABIO  GARCIA ALVAREZ
     @Date : 2024-01-28 16:05:41
   */
   function aceptar() {
       $this->validar_token();       
       $this->validar();
       $db = $this->db; // HOMOLOGAR CODIGO EXTERNO
       $diseno = $_POST['diseno'];

       $update = array();
       $update['active']=2;
       $this->db->update('admin_front',$update,array('active'=>1));


       $update = array();
       $update['active']=1;
       $update['color_primario']= $_POST['color_primario_'.$diseno];
       $this->db->update('admin_front',$update,array('id'=>$diseno));

                          
       $result=array();
       $result["error"] = false;
       $result["msg"] = "Diseño actualizado."; 
       echo json_encode($result);
   }
   
   /* Listar diseños 
     @Autor : FABIO  GARCIA ALVAREZ
     @Date : 2024-01-28 16:05:41
   */

   function listar_disenos(){
       $this->validar_token();       
       $this->validar();
       $db = $this->db; // HOMOLOGAR CODIGO EXTERNO
       
                          
       $result=array();
       $result["error"] = false;
       $result["data"] = $db->select_all("SELECT * FROM admin_front WHERE visible=1"); 
       echo json_encode($result);
   }

}
//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA
 
$accion = ACCION;
$f = new Formulario();
$f->$accion();
?>