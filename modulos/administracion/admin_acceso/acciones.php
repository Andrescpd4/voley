<?php
require_once("php/formulario_basico.php");
//require_once("php/formulario_basicoSQLSERVER.php");
class Admin_acceso extends formulario_basico {

    function validar() {
        $v = new Validation($_POST);
		$v->addRules('codigo', 'Codigo', array('required' => true) );
		$v->addRules('descripcion', 'Descripcion', array('required' => true, 'maxLength' => 45) );

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

    function getSQL() {
        $s="";

        if ($_GET["descripcion"] != "" && $_GET["descripcion"] != "NULL") { 
            $s.= " AND descripcion LIKE '%" . str_replace(" ","%",$_GET['descripcion']) ."%' ";
        }
        $sql = "SELECT * FROM admin_acceso WHERE 1=1  $s ORDER BY codigo ASC ";
        return $sql;
    }

}
//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA


//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA
$_POST['id']= urlsafe_b64decode($_POST['id']);
$_GET['id']= urlsafe_b64decode($_GET['id']);


$accion = ACCION;
$f = new Admin_acceso("admin_acceso", "codigo", false);
$f->$accion();
?>