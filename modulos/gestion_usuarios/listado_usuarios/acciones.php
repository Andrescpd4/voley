<?php
require_once("php/formulario_basico.php");
//require_once("php/formulario_basicoSQLSERVER.php");
class Persona extends formulario_basico {

    function validar() {
        $v = new Validation($_POST);
		$v->addRules('user', 'User', array('required' => true, 'maxLength' => 115) );
		$v->addRules('identifica', 'Documento', array('required' => true, 'maxLength' => 20) );
		$v->addRules('sexo_id', 'Sexo', array('required' => true) );
		$v->addRules('tipoide', 'Tipo de documento', array('integer' => true) );
		$v->addRules('apellido1', 'Primer Apellido', array('required' => true,'maxLength' => 80) );
		$v->addRules('apellido2', 'Segundo Apellido', array('maxLength' => 80) );
		$v->addRules('nombre1', 'Primer Nombre', array('required' => true,'maxLength' => 80) );
		$v->addRules('nombre2', 'Segundo Nombre', array('maxLength' => 80) );
		$v->addRules('telefono', 'Celular', array('required' => true,'maxLength' => 120) );
		$v->addRules('correo', 'Correo', array('required' => true,'maxLength' => 145) );
		$v->addRules('estado', 'Estado', array('required' => true, 'integer' => true) );
		$v->addRules('rol', 'Rol', array('maxLength' => 100) );


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

    function modificar() {
        if ($this->validar() == false)
            exit(0); //No seguir si no se supera la validación

        $pk = $this->clave_primaria;
        $v_pk = $_POST[$pk];
        $update = $_POST;
        unset($update[$pk]);

        $rol_id = $_POST['rol'];
        unset($update['rol']);

        $rol_nombre = null;
        if ($rol_id) {
            $row_rol = $this->db->select_row("SELECT nombre FROM admin_rol WHERE id='$rol_id'");
            $rol_nombre = $row_rol['nombre'] ?? null;
        }
        $update['rol'] = $rol_id;

        $dl_fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
        $dl_salario = $_POST['salario'] ?? null;
        $dl_auxilio = $_POST['auxilio_alimentacion'] ?? null;
        $dl_tipo_contrato = $_POST['tipo_contrato'] ?? null;
        unset($update['fecha_ingreso'], $update['salario'], $update['auxilio_alimentacion'], $update['tipo_contrato']);

        $viejos= $this->db->select_row("SELECT * from $this->tabla where $pk='$v_pk'");
        $sql = $this->db->make_update($this->tabla, $update) . " where $pk='$v_pk'";

        @$this->db->query($sql);

        $r = array();
        if ($this->db->error()) {
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r);
            die;
        } else {
            $r['error'] = false;
            $r['msg'] = "Registro modificado con éxito.";

            $r['row'] = $this->fila(false);
            //$r['row']=$this->db->select_row("select * from general.persona where identifica='$_POST[identifica]'");   
        }

           $this->db->query("DELETE FROM admin_usuario WHERE persona_id='$_POST[id]' ");
           $insert = array();
           $insert['persona_id'] =$v_pk;
           $insert['rol_id'] = $rol_id;
           $insert['_usuario'] = $_SESSION['usuario'];
           $insert['_fecha'] = date('Y-m-d H:i:s');
           if (floatval($rol_id)>0 and floatval($insert['persona_id']) ) {
               $this->db->insert('admin_usuario',$insert);
           }

          // Datos Laborales
          $dl_fields = array();
          $dl_fields['cargo'] = $rol_nombre;
          $dl_fields['nombre_completo'] = trim(
              ($_POST['nombre1'] ?? '') . ' ' . ($_POST['nombre2'] ?? '') . ' ' .
              ($_POST['apellido1'] ?? '') . ' ' . ($_POST['apellido2'] ?? '')
          );
          if ($dl_fecha_ingreso !== null && $dl_fecha_ingreso !== '') $dl_fields['fecha_ingreso'] = $dl_fecha_ingreso;
          if ($dl_salario !== null && $dl_salario !== '') $dl_fields['salario'] = str_replace(',', '.', $dl_salario);
          if ($dl_auxilio !== null && $dl_auxilio !== '') $dl_fields['auxilio_alimentacion'] = str_replace(',', '.', $dl_auxilio);
          if ($dl_tipo_contrato !== null && $dl_tipo_contrato !== '') $dl_fields['tipo_contrato'] = $dl_tipo_contrato;
          $existe = $this->db->select_one("SELECT COUNT(*) FROM datos_laborales WHERE persona_id='$v_pk'");
          if ($existe) {
              $this->db->update('datos_laborales', $dl_fields, array('persona_id' => $v_pk));
          } else {
              $dl_fields['persona_id'] = $v_pk;
              $this->db->insert('datos_laborales', $dl_fields);
          }

         //BITACORA
         $tipo=3;
         $nuevos=$_POST;
         $mensaje=$r['msg'];
         insertar_bitacora($tipo,$nuevos,$mensaje,$viejos);

        echo json_encode($r);
    }


    function agregar() {
        if ($this->validar() == false)
            exit(0); //No seguir si no se supera la validación

        if ($this->auto_incremental == true)
            unset($_POST[$this->clave_primaria]);

        $rol_id = $_POST['rol'];
        unset($_POST['rol']);

        $rol_nombre = null;
        if ($rol_id) {
            $row_rol = $this->db->select_row("SELECT nombre FROM admin_rol WHERE id='$rol_id'");
            $rol_nombre = $row_rol['nombre'] ?? null;
        }
        $_POST['rol'] = $rol_id;

        $dl_fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
        $dl_salario = $_POST['salario'] ?? null;
        $dl_auxilio = $_POST['auxilio_alimentacion'] ?? null;
        $dl_tipo_contrato = $_POST['tipo_contrato'] ?? null;
        unset($_POST['fecha_ingreso'], $_POST['salario'], $_POST['auxilio_alimentacion'], $_POST['tipo_contrato']);

        $sql = $this->db->make_insert($this->tabla, $_POST);
        $this->db->query($sql);
        $persona_id=  $this->db->last_insert_id();

        $r = array();
        if ($this->db->error()) {
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r);
            die;
        } else {
            $r['error'] = false;
            $r['msg'] = "Registro agregado con éxito";
            $r['row'] = $this->fila(true);
        }

           $this->db->query("DELETE FROM admin_usuario WHERE persona_id='$_POST[id]' ");
           $insert = array();
           $insert['persona_id'] =$persona_id;
           $insert['rol_id'] = $rol_id;
           $insert['_usuario'] = $_SESSION['usuario'];
           $insert['_fecha'] = date('Y-m-d H:i:s');
           if (floatval($rol_id)>0 and floatval($insert['persona_id']) ) {
               $this->db->insert('admin_usuario',$insert);
           }

          // Datos Laborales
          $dl_fields = array();
          $dl_fields['cargo'] = $rol_nombre;
          $dl_fields['nombre_completo'] = trim(
              ($_POST['nombre1'] ?? '') . ' ' . ($_POST['nombre2'] ?? '') . ' ' .
              ($_POST['apellido1'] ?? '') . ' ' . ($_POST['apellido2'] ?? '')
          );
          if ($dl_fecha_ingreso !== null && $dl_fecha_ingreso !== '') $dl_fields['fecha_ingreso'] = $dl_fecha_ingreso;
          if ($dl_salario !== null && $dl_salario !== '') $dl_fields['salario'] = str_replace(',', '.', $dl_salario);
          if ($dl_auxilio !== null && $dl_auxilio !== '') $dl_fields['auxilio_alimentacion'] = str_replace(',', '.', $dl_auxilio);
          if ($dl_tipo_contrato !== null && $dl_tipo_contrato !== '') $dl_fields['tipo_contrato'] = $dl_tipo_contrato;
          $dl_fields['persona_id'] = $persona_id;
          $this->db->insert('datos_laborales', $dl_fields);

          //BITACORA
          $tipo=1;
          $nuevos=$_POST;
          $mensaje=$r['msg'];
          $viejos=false;
          insertar_bitacora($tipo,$nuevos,$mensaje,$viejos);

         echo json_encode($r);
     }


    function asignar() {
        $sql = "SELECT p.*, CONCAT_WS('',p.nombre1,' ',p.apellido1,' ',p.apellido2, ' [',p.identificacion,']') as nombre_completo,
                admin_usuario.rol_id,
                dl.fecha_ingreso, dl.salario, dl.auxilio_alimentacion, dl.tipo_contrato
                FROM persona p
                LEFT JOIN admin_usuario ON admin_usuario.persona_id=p.id
                LEFT JOIN datos_laborales dl ON dl.persona_id=p.id
                WHERE p.id='$_GET[id]'";
        $rw = $this->db->select_row($sql);
        $rw['id'] = urlsafe_b64encode($rw['id']);
        echo json_encode($rw);
    }

    function eliminar() {
        $pk = $this->clave_primaria;
        $id = $_POST[$pk];
        $this->db->query("DELETE FROM datos_laborales WHERE persona_id='$id'");
        parent::eliminar();
    }

    function getSQL() {
        $s="";

        if ($_GET["user"] != "" && $_GET["user"] != "NULL") { 
            $s.= " AND user LIKE '%" . str_replace(" ","%",$_GET['user']) ."%' ";
        }
        if ($_GET["identifica"] != "" && $_GET["identifica"] != "NULL") { 
            $s.= " AND identificacion LIKE '%" . str_replace(" ","%",$_GET['identifica']) ."%' ";
        }
         $sql = "SELECT p.*, CONCAT_WS('',p.nombre1,' ',p.apellido1,' ',p.apellido2, ' [',p.identificacion,']') as nombre_completo ,admin_usuario.rol_id
         FROM persona p 
         LEFT JOIN admin_usuario ON admin_usuario.persona_id=p.id
         WHERE 1=1 $s";

        return $sql;
    }

}
//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA

//$_POST = array_map("strtoupper", $_POST); //CONVERTIR TODO EN MAYUSCULA
$_POST['id']= urlsafe_b64decode($_POST['id']);
$_GET['id']= urlsafe_b64decode($_GET['id']);


if (isset($_POST['plantas'])) {
    $_POST['plantas'] = implode(",",$_POST['plantas']);
    $_POST['plantas'] = trim($_POST['plantas'],',');
    #$_SESSION['plantas'] =$_POST['plantas'];
}

if (isset($_POST['clave']) and !empty($_POST['clave'])) {
    $_POST['clave']=clave($_POST['clave']);
}else{
    unset($_POST['clave']);
}








$accion = ACCION;
$f = new Persona("persona", "id", true);
$f->$accion();
?>