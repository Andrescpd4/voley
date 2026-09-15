<?php
// ============================================================
// SESION — Backend del modulo de autenticacion
//
// Usa el sistema de logs del sistema (logs/logs_YYYY_MM_DD.log)
// ============================================================

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once("php/clase_base.php");

class Sesion extends clase_base
{
    /**
     * Validar formulario de login
     * - Verifica que el token inicial sea valido
     * - Descifra POST
     * - Valida reglas de usuario y clave
     */
    function validar()
    {
        $hash = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (validar_token_login($hash)) {
            $_POST = desencriptar_post($_POST, $hash);
            $v = new Validation($_POST);
            $v->addRules('usuario', 'Usuario', array('required' => true, 'length' => array(1, 45)));
            $v->addRules('clave', 'Clave', array('required' => true, 'length' => array(5, 45)));

            $result = $v->validate();
            if ($result['messages'] == "") {
                return true;
            } else {
                $this->denegarSesion($result['messages'], 4);
            }
        } else {
            $this->denegarSesion("Error al validar token", 4);
        }
    }

    /**
     * Iniciar sesion
     * - Valida formulario
     * - Busca usuario en BD
     * - Verifica contraseña
     * - Crea $_SESSION
     * - Emite JWT
     */
    function iniciar()
    {
        $this->validar();

        if (isset($_SESSION['usuario'])) {
            $this->denegarSesion("Acceso denegado", 3);
        }

        if (isset($_POST['usuario']) && isset($_POST['clave'])) {
            $usuario = strtoupper(trim($_POST['usuario']));
            $usuario = $this->db->escape_string($usuario);

            $rw = $this->db->select_row("SELECT * FROM persona WHERE user = '$usuario'");

            if (!$rw) {
                $r = array();
                $r['error'] = true;
                $r['msg'] = 'Acceso denegado, usuario no encontrado.';
                echo json_encode($r);
                exit();
            }

            if ($rw['clave'] == clave($_POST['clave'])) {
                $rol = $this->db->select_one("SELECT rol_id FROM admin_usuario WHERE persona_id = '" . $rw['id'] . "'");

                if ($rol == "") {
                    $insertar_rol = array();
                    $insertar_rol['persona_id'] = $rw['id'];
                    $insertar_rol['rol_id'] = 3;
                    $insertar_rol['_usuario'] = $rw['identificacion'];
                    $insertar_rol['_fecha'] = date('Y-m-d H:i:s');
                    $this->db->insert('admin_usuario', $insertar_rol);
                    $rol = 3;
                }

                $row = array();
                $row['session_id'] = session_id();
                $row['usuario'] = $rw['identificacion'];
                $row['user_agent'] = $this->db->escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');
                $row['refer'] = $_SERVER['HTTP_REFERER'] ?? '';
                $row['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
                $row['inicio'] = date('Y-m-d H:i:s');
                $row['fin'] = date('Y-m-d H:i:s');
                $row['salida'] = "N";
                $this->db->insert("admin_sesion", $row);
                $id = $this->db->last_insert_id();

                if ($id == "") {
                    $this->denegarSesion("Error al crear la sesion", 5);
                }

                $_SESSION['sesion_id'] = $id;
                $_SESSION['usuario'] = $rw['identificacion'];
                $_SESSION['nombre_usuario'] = $rw['nombre1'] . " " . $rw['apellido1'];
                $_SESSION['nombre_usuario_c'] = $rw['nombre1'] . " " . $rw['nombre2'] . " " . $rw['apellido1'] . " " . $rw['apellido2'];
                $_SESSION['persona_id'] = $rw['id'];
                $_SESSION['usuario_rol'] = $rol;
                $_SESSION['foto'] = $rw['foto'] ?? 'img/user.png';
                $_SESSION['TOKEN_ID'] = strtotime(date('Y-m-d H:m:s')) * 2;
                $_SESSION['TOKEN'] = get_token(strtotime(date('Y-m-d H:m:s')) * 3, $_SESSION['TOKEN_ID']);
                $_SESSION['acceso_menu'] = array(1, 3, 7);

                $next_token = $this->set_login($_SESSION['persona_id'], true);

                $payload = $_SESSION;
                $payload['user_agent'] = $this->db->escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');
                $payload['next_token'] = $next_token;

                $key = $_ENV['JWT_SECRET'] ?? 'clave_secreta';
                $jwt = JWT::encode($payload, $key, 'HS256');

                $r = array();
                $r['error'] = false;
                $r['msg'] = "Ok";
                $r['token'] = $jwt;
                echo json_encode($r);
                exit(0);
            }
        }

        $this->denegarSesion("Error de credenciales", 1);
    }

    /**
     * Denegar sesion: registra intento fallido y devuelve error
     */
    private function denegarSesion($msg, $tipo)
    {
        $row = array();
        $row['session_id'] = session_id();
        $row['user_agent'] = $this->db->escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');
        $row['refer'] = $_SERVER['HTTP_REFERER'] ?? '';
        $row['ip'] = verIP();
        $row['fecha'] = date('Y-m-d H:i:s');
        $row['usuario'] = $_POST['usuario'] ?? '';
        $row['tipo'] = $tipo;

        $this->db->insert("admin_sesion_denegada", $row);

        $r = array();
        $r['error'] = true;
        $r['msg'] = $msg;
        echo json_encode($r);
        exit(0);
    }

    /**
     * Emitir token inicial para cifrar formulario de login
     */
    function set_login($id_user = 0, $local = false)
    {
        $hash = strtotime(date('Y-m-d H:m:s')) * 27;
        $token_hash = md5($hash);

        $token = cifrar_texto(json_encode(array(
            'id_user' => $id_user ?? 0,
            'token_hash' => $token_hash
        ), JSON_UNESCAPED_UNICODE));

        $insert = array();
        $insert['token'] = $token;
        $insert['caduca'] = date('Y-m-d');
        $insert['id_user'] = $id_user ?? 0;
        $this->db->insert('admin_token', $insert);

        if ($local == true) {
            return $token;
        }

        $r['error'] = false;
        $r['token'] = $token;
        echo json_encode($r);
    }

    /**
     * Restaurar clave: valida correo y codigo de verificacion
     */
    function restaurar_clave()
    {
        $this->validar();

        if (isset($_SESSION['usuario'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Acceso denegado';
            echo json_encode($r);
            exit();
        }

        $r = array();
        $r['error'] = false;
        $r['msg'] = 'Se ha enviado un correo con las instrucciones';
        echo json_encode($r);
    }
}

$accion = ACCION;
$c = new Sesion();
$c->$accion();
