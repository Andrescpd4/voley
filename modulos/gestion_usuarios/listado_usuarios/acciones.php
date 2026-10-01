<?php
// ============================================================
// LISTADO USUARIOS — Backend Voley+ (sin herencia de logistics)
// Solo toca: persona + usuario + admin_usuario (el rol basta, sin tablas extra)
// No usa: datos_laborales, plantas, tipo_contrato, sexo, estado
// ============================================================

require_once("php/formulario_basico.php");

class Persona extends formulario_basico
{
    // 1. Leer un campo del POST sin usar ?? ni ternarios
    function leer_post($nombre_campo, $valor_defecto)
    {
        if (isset($_POST[$nombre_campo])) {
            return trim($_POST[$nombre_campo]);
        } else {
            return $valor_defecto;
        }
    }

    // 2. Validar campos minimos del formulario
    function validar()
    {
        $v = new Validation($_POST);
        $v->addRules('identifica', 'Documento', array('required' => true, 'maxLength' => 20));
        $v->addRules('nombre1', 'Primer Nombre', array('required' => true, 'maxLength' => 50));
        $v->addRules('apellido1', 'Primer Apellido', array('required' => true, 'maxLength' => 50));
        $v->addRules('telefono', 'Celular', array('required' => true, 'maxLength' => 20));
        $v->addRules('correo', 'Correo', array('required' => true, 'maxLength' => 100));
        $v->addRules('login', 'Usuario de ingreso', array('required' => true, 'maxLength' => 50));

        $resultado = $v->validate();

        if ($resultado['messages'] == "") {
            return true;
        } else {
            $respuesta = array();
            $respuesta['error'] = true;
            $respuesta['msg'] = $resultado['messages'];
            $respuesta['bad_fields'] = $resultado['bad_fields'];
            $respuesta['errors'] = $resultado['errors'];
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
            exit(0);
        }
    }

    // 3. Revisar duplicados de documento y login
    function validar_duplicados($documento, $login, $persona_id)
    {
        // 3.1 Documento repetido en otra persona
        $documento_seguro = $this->db->escape_string($documento);
        $sql_documento = "SELECT id FROM persona WHERE identificacion = '" . $documento_seguro . "' AND id <> " . intval($persona_id);
        $existe_documento = $this->db->select_row($sql_documento);
        if (is_array($existe_documento)) {
            if (isset($existe_documento['id'])) {
                $respuesta = array();
                $respuesta['error'] = true;
                $respuesta['msg'] = "El documento ya esta registrado en otro usuario.";
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
                exit(0);
            }
        }

        // 3.2 Login repetido en otro usuario
        $login_seguro = $this->db->escape_string($login);
        $sql_login = "SELECT persona_id FROM usuario WHERE login = '" . $login_seguro . "' AND persona_id <> " . intval($persona_id);
        $existe_login = $this->db->select_row($sql_login);
        if (is_array($existe_login)) {
            if (isset($existe_login['persona_id'])) {
                $respuesta = array();
                $respuesta['error'] = true;
                $respuesta['msg'] = "El usuario de ingreso ya existe. Elija otro.";
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
                exit(0);
            }
        }
    }

    // 4. Crear usuario nuevo
    function agregar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        // 4.1 Leer campos del formulario
        $tipo_documento = $this->leer_post('tipo_documento', 'CC');
        $documento = $this->leer_post('identifica', '');
        $nombre1 = $this->leer_post('nombre1', '');
        $nombre2 = $this->leer_post('nombre2', '');
        $apellido1 = $this->leer_post('apellido1', '');
        $apellido2 = $this->leer_post('apellido2', '');
        $genero = $this->leer_post('genero', 'M');
        $celular = $this->leer_post('telefono', '');
        $correo = $this->leer_post('correo', '');
        $login = $this->leer_post('login', '');
        $rol_id = intval($this->leer_post('rol', '3'));
        $activo = intval($this->leer_post('activo', '1'));
        $clave_texto = $this->leer_post('clave', '');

        // 4.2 Validar rol permitido (1, 2, 3, 4)
        if ($rol_id < 1) {
            $rol_id = 3;
        }
        if ($rol_id > 4) {
            $rol_id = 3;
        }

        // 4.3 Validar genero permitido
        if ($genero !== 'M') {
            if ($genero !== 'F') {
                if ($genero !== 'OTRO') {
                    $genero = 'M';
                }
            }
        }

        // 4.4 Validar tipo de documento permitido
        if ($tipo_documento !== 'CC') {
            if ($tipo_documento !== 'TI') {
                if ($tipo_documento !== 'RC') {
                    if ($tipo_documento !== 'CE') {
                        if ($tipo_documento !== 'PASAPORTE') {
                            if ($tipo_documento !== 'OTRO') {
                                $tipo_documento = 'CC';
                            }
                        }
                    }
                }
            }
        }

        // 4.5 Revisar duplicados
        $this->validar_duplicados($documento, $login, 0);

        // 4.6 Clave por defecto si viene vacia
        if ($clave_texto == '') {
            $clave_texto = '12345';
        }

        // 4.7 Insertar persona
        $datos_persona = array();
        $datos_persona['tipo_documento'] = $tipo_documento;
        $datos_persona['identificacion'] = $documento;
        $datos_persona['user'] = $login;
        $datos_persona['nombre1'] = $nombre1;
        $datos_persona['nombre2'] = $nombre2;
        $datos_persona['apellido1'] = $apellido1;
        $datos_persona['apellido2'] = $apellido2;
        $datos_persona['genero'] = $genero;
        $datos_persona['celular'] = $celular;
        $datos_persona['correo'] = $correo;
        $datos_persona['foto'] = 'img/user.png';

        $this->db->insert('persona', $datos_persona);
        $persona_id = intval($this->db->last_insert_id());

        if ($this->db->error()) {
            $respuesta = array();
            $respuesta['error'] = true;
            $respuesta['msg'] = $this->db->error();
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
            die;
        }

        // 4.8 Insertar rol en admin_usuario (columna real: rol)
        $datos_admin = array();
        $datos_admin['persona_id'] = $persona_id;
        $datos_admin['rol'] = $rol_id;
        if (isset($_SESSION['usuario'])) {
            $datos_admin['_usuario'] = $_SESSION['usuario'];
        } else {
            $datos_admin['_usuario'] = 'ADMIN';
        }
        $datos_admin['_fecha'] = date('Y-m-d H:i:s');
        $this->db->insert('admin_usuario', $datos_admin);

        // 4.9 Insertar acceso en usuario
        $datos_usuario = array();
        $datos_usuario['persona_id'] = $persona_id;
        $datos_usuario['rol_id'] = $rol_id;
        $datos_usuario['login'] = $login;
        $datos_usuario['password_hash'] = password_hash($clave_texto, PASSWORD_BCRYPT);
        $datos_usuario['activo'] = $activo;
        $this->db->insert('usuario', $datos_usuario);

        // 4.10 Bitacora
        insertar_bitacora(1, $_POST, "Registro agregado con exito", false);

        $respuesta = array();
        $respuesta['error'] = false;
        $respuesta['msg'] = "Registro agregado con exito";
        $respuesta['row'] = $this->fila(true);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }

    // 5. Modificar usuario existente
    function modificar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        // 5.1 Leer id y campos
        $persona_id = intval($this->leer_post('id', '0'));
        $tipo_documento = $this->leer_post('tipo_documento', 'CC');
        $documento = $this->leer_post('identifica', '');
        $nombre1 = $this->leer_post('nombre1', '');
        $nombre2 = $this->leer_post('nombre2', '');
        $apellido1 = $this->leer_post('apellido1', '');
        $apellido2 = $this->leer_post('apellido2', '');
        $genero = $this->leer_post('genero', 'M');
        $celular = $this->leer_post('telefono', '');
        $correo = $this->leer_post('correo', '');
        $login = $this->leer_post('login', '');
        $rol_id = intval($this->leer_post('rol', '3'));
        $activo = intval($this->leer_post('activo', '1'));
        $clave_texto = $this->leer_post('clave', '');

        if ($rol_id < 1) {
            $rol_id = 3;
        }
        if ($rol_id > 4) {
            $rol_id = 3;
        }

        if ($genero !== 'M') {
            if ($genero !== 'F') {
                if ($genero !== 'OTRO') {
                    $genero = 'M';
                }
            }
        }

        // 5.2 Revisar duplicados excluyendo este registro
        $this->validar_duplicados($documento, $login, $persona_id);

        // 5.3 Guardar datos viejos para bitacora
        $datos_viejos = $this->db->select_row("SELECT * FROM persona WHERE id = " . intval($persona_id));

        // 5.4 Actualizar persona
        $datos_persona = array();
        $datos_persona['tipo_documento'] = $tipo_documento;
        $datos_persona['identificacion'] = $documento;
        $datos_persona['user'] = $login;
        $datos_persona['nombre1'] = $nombre1;
        $datos_persona['nombre2'] = $nombre2;
        $datos_persona['apellido1'] = $apellido1;
        $datos_persona['apellido2'] = $apellido2;
        $datos_persona['genero'] = $genero;
        $datos_persona['celular'] = $celular;
        $datos_persona['correo'] = $correo;

        $this->db->update('persona', $datos_persona, array('id' => intval($persona_id)));

        if ($this->db->error()) {
            $respuesta = array();
            $respuesta['error'] = true;
            $respuesta['msg'] = $this->db->error();
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
            die;
        }

        // 5.5 Reemplazar rol en admin_usuario
        $this->db->query("DELETE FROM admin_usuario WHERE persona_id = " . intval($persona_id));
        $datos_admin = array();
        $datos_admin['persona_id'] = $persona_id;
        $datos_admin['rol'] = $rol_id;
        if (isset($_SESSION['usuario'])) {
            $datos_admin['_usuario'] = $_SESSION['usuario'];
        } else {
            $datos_admin['_usuario'] = 'ADMIN';
        }
        $datos_admin['_fecha'] = date('Y-m-d H:i:s');
        $this->db->insert('admin_usuario', $datos_admin);

        // 5.6 Actualizar o crear acceso en usuario
        $datos_usuario = array();
        $datos_usuario['login'] = $login;
        $datos_usuario['rol_id'] = $rol_id;
        $datos_usuario['activo'] = $activo;
        if ($clave_texto !== '') {
            $datos_usuario['password_hash'] = password_hash($clave_texto, PASSWORD_BCRYPT);
        }

        $sql_cuenta = "SELECT COUNT(*) AS total FROM usuario WHERE persona_id = " . intval($persona_id);
        $fila_cuenta = $this->db->select_row($sql_cuenta);
        $existe_usuario = 0;
        if (is_array($fila_cuenta)) {
            if (isset($fila_cuenta['total'])) {
                $existe_usuario = intval($fila_cuenta['total']);
            }
        }

        if ($existe_usuario > 0) {
            $this->db->update('usuario', $datos_usuario, array('persona_id' => intval($persona_id)));
        } else {
            $datos_usuario['persona_id'] = $persona_id;
            if (isset($datos_usuario['password_hash']) == false) {
                $datos_usuario['password_hash'] = password_hash('12345', PASSWORD_BCRYPT);
            }
            $this->db->insert('usuario', $datos_usuario);
        }

        // 5.7 Bitacora
        insertar_bitacora(3, $_POST, "Registro modificado con exito", $datos_viejos);

        $respuesta = array();
        $respuesta['error'] = false;
        $respuesta['msg'] = "Registro modificado con exito.";
        $respuesta['row'] = $this->fila(false);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }

    // 6. Cargar un registro para editar
    function asignar()
    {
        // 6.1 Leer id por GET
        if (isset($_GET['id'])) {
            $persona_id = intval($_GET['id']);
        } else {
            $persona_id = 0;
        }

        $sql = "SELECT p.*,"
            . " p.identificacion AS identifica,"
            . " p.celular AS telefono,"
            . " u.login AS login,"
            . " u.activo AS activo,"
            . " u.rol_id AS rol"
            . " FROM persona p"
            . " LEFT JOIN usuario u ON u.persona_id = p.id"
            . " WHERE p.id = " . intval($persona_id);

        $fila = $this->db->select_row($sql);
        if (is_array($fila)) {
            if (isset($fila['id'])) {
                $fila['id'] = urlsafe_b64encode($fila['id']);
            }
        }
        echo json_encode($fila, JSON_UNESCAPED_UNICODE);
    }

    // 7. Eliminar usuario y sus accesos
    function eliminar()
    {
        // 7.1 Leer id
        if (isset($_POST['id'])) {
            $persona_id = intval($_POST['id']);
        } else {
            $persona_id = 0;
        }

        // 7.2 Borrar tablas hijas primero (solo las que existen en el servidor)
        $this->db->query("DELETE FROM admin_usuario WHERE persona_id = " . intval($persona_id));
        $this->db->query("DELETE FROM usuario WHERE persona_id = " . intval($persona_id));

        // 7.3 Borrar persona con metodo del padre
        parent::eliminar();
    }

    // 8. SQL del listado con filtros simples
    function getSQL()
    {
        $filtro = "";

        if (isset($_GET["login"])) {
            if ($_GET["login"] != "" && $_GET["login"] != "NULL") {
                $texto_login = $this->db->escape_string($_GET["login"]);
                $filtro = $filtro . " AND u.login LIKE '%" . str_replace(" ", "%", $texto_login) . "%' ";
            }
        }

        if (isset($_GET["identifica"])) {
            if ($_GET["identifica"] != "" && $_GET["identifica"] != "NULL") {
                $texto_documento = $this->db->escape_string($_GET["identifica"]);
                $filtro = $filtro . " AND p.identificacion LIKE '%" . str_replace(" ", "%", $texto_documento) . "%' ";
            }
        }

        if (isset($_GET["rol"])) {
            if ($_GET["rol"] != "" && $_GET["rol"] != "NULL") {
                $filtro = $filtro . " AND u.rol_id = " . intval($_GET["rol"]) . " ";
            }
        }

        $sql = "SELECT p.*,"
            . " p.identificacion AS identifica,"
            . " p.celular AS telefono,"
            . " CONCAT_WS(' ', p.nombre1, p.apellido1, p.apellido2) AS nombre_completo,"
            . " p.id AS _NUM_,"
            . " u.login AS user,"
            . " u.activo AS activo,"
            . " u.rol_id AS rol,"
            . " r.nombre AS rol_nombre"
            . " FROM persona p"
            . " LEFT JOIN usuario u ON u.persona_id = p.id"
            . " LEFT JOIN admin_rol r ON r.id = u.rol_id"
            . " WHERE 1=1 " . $filtro . " ORDER BY p.id ASC";

        return $sql;
    }
}

// 9. Decodificar id que llega en base64 seguro
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
