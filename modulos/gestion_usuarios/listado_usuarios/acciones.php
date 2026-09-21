<?php
// ============================================================
// LISTADO USUARIOS — Backend (CRUD)
// Modulo de gestion de usuarios adaptado a base voley_plus
// ============================================================

require_once("php/formulario_basico.php");

class Persona extends formulario_basico
{
    function validar()
    {
        $v = new Validation($_POST);
        $v->addRules('user', 'Usuario de ingreso', array('required' => true, 'maxLength' => 115));
        $v->addRules('identifica', 'Documento de identificacion', array('required' => true, 'maxLength' => 20));
        $v->addRules('apellido1', 'Primer Apellido', array('required' => true, 'maxLength' => 80));
        $v->addRules('nombre1', 'Primer Nombre', array('required' => true, 'maxLength' => 80));
        $v->addRules('telefono', 'Celular', array('required' => true, 'maxLength' => 120));
        $v->addRules('correo', 'Correo', array('required' => true, 'maxLength' => 145));

        $result = $v->validate();

        if ($result['messages'] == "") {
            return true;
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $result['messages'];
            $r['bad_fields'] = $result['bad_fields'];
            $r['errors'] = $result['errors'];
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            exit(0);
        }
    }

    function agregar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        // 1. Extraer datos del formulario
        $user = trim($_POST['user'] ?? '');
        $identificacion = trim($_POST['identifica'] ?? '');
        $nombre1 = trim($_POST['nombre1'] ?? '');
        $nombre2 = trim($_POST['nombre2'] ?? '');
        $apellido1 = trim($_POST['apellido1'] ?? '');
        $apellido2 = trim($_POST['apellido2'] ?? '');
        $celular = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo_id = $_POST['sexo_id'] ?? '1';
        $genero = ($sexo_id == '2' || $sexo_id == 'F') ? 'F' : 'M';
        $tipo_doc = $_POST['tipoide'] ?? 'CC';
        $rol_id = intval($_POST['rol'] ?? 3);
        $clave_raw = trim($_POST['clave'] ?? '12345');
        if ($clave_raw == '') {
            $clave_raw = '12345';
        }

        // Datos laborales
        $dl_fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
        $dl_salario = $_POST['salario'] ?? null;
        $dl_auxilio = $_POST['auxilio_alimentacion'] ?? null;
        $dl_tipo_contrato = $_POST['tipo_contrato'] ?? null;

        // 2. Insertar en tabla persona
        $persona_data = array();
        $persona_data['user'] = $user;
        $persona_data['identificacion'] = $identificacion;
        $persona_data['nombre1'] = $nombre1;
        $persona_data['nombre2'] = $nombre2;
        $persona_data['apellido1'] = $apellido1;
        $persona_data['apellido2'] = $apellido2;
        $persona_data['celular'] = $celular;
        $persona_data['correo'] = $correo;
        $persona_data['genero'] = $genero;
        $persona_data['tipo_documento'] = (is_numeric($tipo_doc) ? 'CC' : $tipo_doc);
        $persona_data['foto'] = 'img/user.png';

        $this->db->insert('persona', $persona_data);
        $persona_id = $this->db->last_insert_id();

        if ($this->db->error()) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            die;
        }

        // 3. Insertar en admin_usuario (permisos y roles)
        $this->db->query("DELETE FROM admin_usuario WHERE persona_id = '$persona_id'");
        if ($rol_id > 0) {
            $admin_u = array();
            $admin_u['persona_id'] = $persona_id;
            $admin_u['rol_id'] = $rol_id;
            $admin_u['_usuario'] = $_SESSION['usuario'] ?? 'ADMIN';
            $admin_u['_fecha'] = date('Y-m-d H:i:s');
            $this->db->insert('admin_usuario', $admin_u);
        }

        // 4. Insertar en usuario (login del sistema)
        $this->db->query("DELETE FROM usuario WHERE persona_id = '$persona_id'");
        $pass_hash = password_hash($clave_raw, PASSWORD_BCRYPT);
        $u_data = array();
        $u_data['persona_id'] = $persona_id;
        $u_data['rol_id'] = ($rol_id > 0 ? $rol_id : 3);
        $u_data['login'] = $user;
        $u_data['password_hash'] = $pass_hash;
        $u_data['activo'] = 1;
        $this->db->insert('usuario', $u_data);

        // 5. Insertar datos laborales
        $rol_nombre = null;
        if ($rol_id > 0) {
            $row_rol = $this->db->select_row("SELECT nombre FROM admin_rol WHERE id = '$rol_id'");
            $rol_nombre = $row_rol['nombre'] ?? null;
        }
        $dl_fields = array();
        $dl_fields['persona_id'] = $persona_id;
        $dl_fields['cargo'] = $rol_nombre;
        $dl_fields['nombre_completo'] = trim($nombre1 . ' ' . $nombre2 . ' ' . $apellido1 . ' ' . $apellido2);
        if ($dl_fecha_ingreso !== null && $dl_fecha_ingreso !== '') {
            $dl_fields['fecha_ingreso'] = $dl_fecha_ingreso;
        }
        if ($dl_salario !== null && $dl_salario !== '') {
            $dl_fields['salario'] = str_replace(',', '.', $dl_salario);
        }
        if ($dl_auxilio !== null && $dl_auxilio !== '') {
            $dl_fields['auxilio_alimentacion'] = str_replace(',', '.', $dl_auxilio);
        }
        if ($dl_tipo_contrato !== null && $dl_tipo_contrato !== '') {
            $dl_fields['tipo_contrato'] = $dl_tipo_contrato;
        }
        $this->db->insert('datos_laborales', $dl_fields);

        // 6. Bitacora
        insertar_bitacora(1, $_POST, "Registro agregado con exito", false);

        $r = array();
        $r['error'] = false;
        $r['msg'] = "Registro agregado con exito";
        $r['row'] = $this->fila(true);
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    function modificar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        $pk = $this->clave_primaria;
        $v_pk = $_POST[$pk];

        // 1. Extraer datos
        $user = trim($_POST['user'] ?? '');
        $identificacion = trim($_POST['identifica'] ?? '');
        $nombre1 = trim($_POST['nombre1'] ?? '');
        $nombre2 = trim($_POST['nombre2'] ?? '');
        $apellido1 = trim($_POST['apellido1'] ?? '');
        $apellido2 = trim($_POST['apellido2'] ?? '');
        $celular = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo_id = $_POST['sexo_id'] ?? '1';
        $genero = ($sexo_id == '2' || $sexo_id == 'F') ? 'F' : 'M';
        $tipo_doc = $_POST['tipoide'] ?? 'CC';
        $rol_id = intval($_POST['rol'] ?? 3);
        $clave_raw = trim($_POST['clave'] ?? '');

        // Datos laborales
        $dl_fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
        $dl_salario = $_POST['salario'] ?? null;
        $dl_auxilio = $_POST['auxilio_alimentacion'] ?? null;
        $dl_tipo_contrato = $_POST['tipo_contrato'] ?? null;

        $viejos = $this->db->select_row("SELECT * FROM persona WHERE id = '$v_pk'");

        // 2. Actualizar persona
        $persona_data = array();
        $persona_data['user'] = $user;
        $persona_data['identificacion'] = $identificacion;
        $persona_data['nombre1'] = $nombre1;
        $persona_data['nombre2'] = $nombre2;
        $persona_data['apellido1'] = $apellido1;
        $persona_data['apellido2'] = $apellido2;
        $persona_data['celular'] = $celular;
        $persona_data['correo'] = $correo;
        $persona_data['genero'] = $genero;
        $persona_data['tipo_documento'] = (is_numeric($tipo_doc) ? 'CC' : $tipo_doc);

        $this->db->update('persona', $persona_data, array('id' => $v_pk));

        if ($this->db->error()) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            die;
        }

        // 3. Actualizar admin_usuario
        $this->db->query("DELETE FROM admin_usuario WHERE persona_id = '$v_pk'");
        if ($rol_id > 0) {
            $admin_u = array();
            $admin_u['persona_id'] = $v_pk;
            $admin_u['rol_id'] = $rol_id;
            $admin_u['_usuario'] = $_SESSION['usuario'] ?? 'ADMIN';
            $admin_u['_fecha'] = date('Y-m-d H:i:s');
            $this->db->insert('admin_usuario', $admin_u);
        }

        // 4. Actualizar usuario
        $u_update = array();
        $u_update['login'] = $user;
        $u_update['rol_id'] = ($rol_id > 0 ? $rol_id : 3);
        if (!empty($clave_raw)) {
            $u_update['password_hash'] = password_hash($clave_raw, PASSWORD_BCRYPT);
        }
        $existe_u = $this->db->select_one("SELECT COUNT(*) FROM usuario WHERE persona_id = '$v_pk'");
        if (!empty($existe_u) && intval($existe_u) > 0) {
            $this->db->update('usuario', $u_update, array('persona_id' => $v_pk));
        } else {
            $u_update['persona_id'] = $v_pk;
            if (empty($u_update['password_hash'])) {
                $u_update['password_hash'] = password_hash('12345', PASSWORD_BCRYPT);
            }
            $u_update['activo'] = 1;
            $this->db->insert('usuario', $u_update);
        }

        // 5. Actualizar datos laborales
        $rol_nombre = null;
        if ($rol_id > 0) {
            $row_rol = $this->db->select_row("SELECT nombre FROM admin_rol WHERE id = '$rol_id'");
            $rol_nombre = $row_rol['nombre'] ?? null;
        }
        $dl_fields = array();
        $dl_fields['cargo'] = $rol_nombre;
        $dl_fields['nombre_completo'] = trim($nombre1 . ' ' . $nombre2 . ' ' . $apellido1 . ' ' . $apellido2);
        if ($dl_fecha_ingreso !== null && $dl_fecha_ingreso !== '') {
            $dl_fields['fecha_ingreso'] = $dl_fecha_ingreso;
        }
        if ($dl_salario !== null && $dl_salario !== '') {
            $dl_fields['salario'] = str_replace(',', '.', $dl_salario);
        }
        if ($dl_auxilio !== null && $dl_auxilio !== '') {
            $dl_fields['auxilio_alimentacion'] = str_replace(',', '.', $dl_auxilio);
        }
        if ($dl_tipo_contrato !== null && $dl_tipo_contrato !== '') {
            $dl_fields['tipo_contrato'] = $dl_tipo_contrato;
        }

        $existe_dl = $this->db->select_one("SELECT COUNT(*) FROM datos_laborales WHERE persona_id = '$v_pk'");
        if (!empty($existe_dl) && intval($existe_dl) > 0) {
            $this->db->update('datos_laborales', $dl_fields, array('persona_id' => $v_pk));
        } else {
            $dl_fields['persona_id'] = $v_pk;
            $this->db->insert('datos_laborales', $dl_fields);
        }

        // 6. Bitacora
        insertar_bitacora(3, $_POST, "Registro modificado con exito", $viejos);

        $r = array();
        $r['error'] = false;
        $r['msg'] = "Registro modificado con exito.";
        $r['row'] = $this->fila(false);
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    function asignar()
    {
        $id = intval($_GET['id'] ?? 0);
        $sql = "SELECT p.*,
                p.identificacion as identifica,
                p.celular as telefono,
                IF(p.genero = 'F', 2, 1) as sexo_id,
                p.tipo_documento as tipoide,
                CONCAT_WS(' ', p.nombre1, p.apellido1, p.apellido2, CONCAT('[', p.identificacion, ']')) as nombre_completo,
                admin_usuario.rol_id as rol,
                dl.fecha_ingreso, dl.salario, dl.auxilio_alimentacion, dl.tipo_contrato
                FROM persona p
                LEFT JOIN admin_usuario ON admin_usuario.persona_id = p.id
                LEFT JOIN datos_laborales dl ON dl.persona_id = p.id
                WHERE p.id = " . $id;
        $rw = $this->db->select_row($sql);
        if ($rw) {
            $rw['id'] = urlsafe_b64encode($rw['id']);
        }
        echo json_encode($rw, JSON_UNESCAPED_UNICODE);
    }

    function eliminar()
    {
        $pk = $this->clave_primaria;
        $id = intval($_POST[$pk] ?? 0);
        $this->db->query("DELETE FROM datos_laborales WHERE persona_id = '$id'");
        $this->db->query("DELETE FROM admin_usuario WHERE persona_id = '$id'");
        $this->db->query("DELETE FROM usuario WHERE persona_id = '$id'");
        parent::eliminar();
    }

    function getSQL()
    {
        $s = "";

        if (isset($_GET["user"]) && $_GET["user"] != "" && $_GET["user"] != "NULL") {
            $s .= " AND p.user LIKE '%" . str_replace(" ", "%", $this->db->escape_string($_GET['user'])) . "%' ";
        }
        if (isset($_GET["identifica"]) && $_GET["identifica"] != "" && $_GET["identifica"] != "NULL") {
            $s .= " AND p.identificacion LIKE '%" . str_replace(" ", "%", $this->db->escape_string($_GET['identifica'])) . "%' ";
        }

        $sql = "SELECT p.*,
                p.identificacion as identifica,
                p.celular as telefono,
                CONCAT_WS(' ', p.nombre1, p.apellido1, p.apellido2) as nombre_completo,
                p.id as _NUM_,
                admin_usuario.rol_id as rol,
                admin_rol.nombre as rol_nombre
                FROM persona p
                LEFT JOIN admin_usuario ON admin_usuario.persona_id = p.id
                LEFT JOIN admin_rol ON admin_rol.id = admin_usuario.rol_id
                WHERE 1=1 $s ORDER BY p.id ASC";

        return $sql;
    }
}

if (isset($_POST['id'])) {
    $_POST['id'] = urlsafe_b64decode($_POST['id']);
}
if (isset($_GET['id'])) {
    $_GET['id'] = urlsafe_b64decode($_GET['id']);
}

$accion = ACCION;
$f = new Persona("persona", "id", true);
$f->$accion();
?>
