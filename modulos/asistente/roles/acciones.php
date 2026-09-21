<?php
require_once("php/formulario_basico.php");
//require_once("php/formulario_basicoSQLSERVER.php");
class Admin_rol extends formulario_basico {

    function validar() {
        $v = new Validation($_POST);
		$v->addRules('nombre', 'Nombre', array('required' => true, 'maxLength' => 60) );

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

        if ($_GET["nombre"] != "" && $_GET["nombre"] != "NULL") { 
            $s.= " AND nombre LIKE '%" . str_replace(" ","%",$_GET['nombre']) ."%' ";
        }
        $sql = "SELECT * FROM admin_rol WHERE 1=1  $s ORDER BY id ASC ";
        return $sql;
    }

}
//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA
$_POST['_usuario'] = $_SESSION['usuario'];
$_POST['_fecha'] = date('Y-m-d H:i:s');


//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA
$_POST['id']= urlsafe_b64decode($_POST['id']);
$_GET['id']= urlsafe_b64decode($_GET['id']);


$accion = ACCION;
$f = new Admin_rol("admin_rol", "id", true);
$f->$accion();
?>