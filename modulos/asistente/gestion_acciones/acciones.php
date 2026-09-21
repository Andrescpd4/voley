<?php
require_once("php/formulario_basico.php");
//require_once("php/formulario_basicoSQLSERVER.php");
class Admin_accion extends formulario_basico {

    function validar() {
        $v = new Validation($_POST);
		$v->addRules('menu', 'Menú', array('required' => true) );
		$v->addRules('accion', 'Acción', array('required' => true, 'maxLength' => 60) );
		$v->addRules('tipo_accion', 'Tipo acción', array('required' => true) );
		$v->addRules('archivo', 'Archivo', array('required' => true, 'maxLength' => 100) );
		$v->addRules('requiere_permiso', 'Requiere permiso', array('required' => true, 'maxLength' => 1) );

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

        if ($_GET["menu"] != "" && $_GET["menu"] != "NULL") { 
            $s.= " AND a.menu = '$_GET[menu]'";
        }
        if ($_GET["accion"] != "" && $_GET["accion"] != "NULL") { 
            $s.= " AND a.accion LIKE '%" . str_replace(" ","%",$_GET['accion']) ."%' ";
        }
         $sql = "SELECT a.*, m.nombre
                FROM admin_accion a, admin_menu m 
                WHERE a.menu=m.menu $s
                ORDER BY m.nombre, a.archivo
                ";
        return $sql;
    }

}
//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA


//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA
$_POST['id']= urlsafe_b64decode($_POST['id']);
$_GET['id']= urlsafe_b64decode($_GET['id']);


$accion = ACCION;
$f = new Admin_accion("admin_accion", "id", true);
$f->$accion();
?>