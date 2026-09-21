<?php
// ============================================================
// SESION — Backend del módulo de autenticación
//
// Valida credenciales contra la tabla `usuario` y `persona`,
// usando password_verify() (bcrypt).
// Emite JWT y registra la sesión.
// ============================================================

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once("php/clase_base.php");

class Sesion extends clase_base
{
    /**
     * Validar formulario de login
     */
    function validar()
    {
        $hash = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Si viene con cifrado AES (login con token previo)
        if (!empty($hash) && validar_token_login($hash)) {
            $_POST = desencriptar_post($_POST, $hash);
        }

        $v = new Validation($_POST);
        $v->addRules('usuario', 'Usuario', array('required' => true, 'length' => array(1, 50)));
        $v->addRules('clave', 'Clave', array('required' => true, 'length' => array(4, 100)));

        $result = $v->validate();
        if ($result['messages'] == "") {
            return true;
        } else {
            $this->denegarSesion($result['messages'], 4);
        }
    }

    /**
     * Iniciar sesión
     * - Busca en tabla `usuario` con JOIN a `persona` y `admin_rol`
     * - Comprueba bcrypt con verificar_clave()
     * - Configura $_SESSION y emite JWT
     */
    function iniciar()
    {
        $this->validar();

        if (isset($_SESSION['usuario'])) {
            $this->denegarSesion("Ya existe una sesión activa", 3);
        }

        if (isset($_POST['usuario']) && isset($_POST['clave'])) {
            $login = strtoupper(trim($_POST['usuario']));
            $clave = $_POST['clave'];

            // 1. Buscar usuario activo en BD
            $sql = "SELECT 
                        u.id AS usuario_id,
                        u.login,
                        u.password_hash,
                        u.activo,
                        u.rol_id,
                        r.nombre AS rol_nombre,
                        r.slug AS rol_slug,
                        p.id AS persona_id,
                        p.identificacion,
                        p.nombre1,
                        p.nombre2,
                        p.apellido1,
                        p.apellido2,
                        p.correo,
                        p.foto
                    FROM usuario u
                    INNER JOIN persona p ON p.id = u.persona_id
                    LEFT JOIN admin_rol r ON r.id = u.rol_id
                    WHERE (u.login = ? OR p.identificacion = ? OR p.correo = ?) AND u.activo = 1";

            $row = $this->db->getConnection()->select($sql, array($login, $login, $login));

            if (empty($row)) {
                $this->denegarSesion("Usuario no encontrado o inactivo", 1);
            }

            $rw = (array) $row[0];

            // 2. Verificar contraseña con password_verify / verificar_clave
            if (verificar_clave($clave, $rw['password_hash'])) {
                $rol_id = intval($rw['rol_id'] ?? 3);

                // 3. Registrar inicio de sesión en admin_sesion
                $sesion_data = array();
                $sesion_data['usuario'] = $rw['identificacion'];
                $sesion_data['session_id'] = session_id();
                $sesion_data['user_agent'] = $this->db->escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');
                $sesion_data['refer'] = $_SERVER['HTTP_REFERER'] ?? '';
                $sesion_data['ip'] = verIP();
                $sesion_data['inicio'] = date('Y-m-d H:i:s');
                $sesion_data['fin'] = date('Y-m-d H:i:s');
                $sesion_data['salida'] = "N";

                $this->db->insert("admin_sesion", $sesion_data);
                $sesion_id = $this->db->last_insert_id();

                // 4. Actualizar último acceso del usuario
                $this->db->getConnection()->update(
                    "UPDATE usuario SET ultimo_acceso = ? WHERE id = ?",
                    array(date('Y-m-d H:i:s'), $rw['usuario_id'])
                );

                // 5. Configurar $_SESSION
                $_SESSION['sesion_id'] = $sesion_id;
                $_SESSION['usuario_id'] = $rw['usuario_id'];
                $_SESSION['usuario'] = $rw['identificacion'];
                $_SESSION['login'] = $rw['login'];
                $_SESSION['nombre_usuario'] = trim($rw['nombre1'] . " " . $rw['apellido1']);
                $_SESSION['nombre_usuario_c'] = trim($rw['nombre1'] . " " . $rw['nombre2'] . " " . $rw['apellido1'] . " " . $rw['apellido2']);
                $_SESSION['persona_id'] = $rw['persona_id'];
                $_SESSION['usuario_rol'] = $rol_id;
                $_SESSION['rol'] = $rw['rol_nombre'] ?? 'Usuario';
                $_SESSION['rol_slug'] = $rw['rol_slug'] ?? 'usuario';
                $_SESSION['foto'] = !empty($rw['foto']) ? $rw['foto'] : 'img/user.png';
                $_SESSION['TOKEN_ID'] = strtotime(date('Y-m-d H:i:s')) * 2;
                $_SESSION['TOKEN'] = get_token(strtotime(date('Y-m-d H:i:s')) * 3, $_SESSION['TOKEN_ID']);
                $_SESSION['acceso_menu'] = array(1, 3, 7);

                // 6. Generar JWT para el cliente
                $next_token = $this->set_login($_SESSION['persona_id'], true);

                $payload = $_SESSION;
                $payload['user_agent'] = $this->db->escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');
                $payload['next_token'] = $next_token;

                $key = $_ENV['JWT_SECRET'] ?? 'clave_secreta_voleyplus_2026';
                $jwt = JWT::encode($payload, $key, 'HS256');

                $r = array();
                $r['error'] = false;
                $r['msg'] = "Bienvenido " . $_SESSION['nombre_usuario'];
                $r['token'] = $jwt;
                $r['redirect'] = WEB_ROOT . 'inicio';
                echo json_encode($r, JSON_UNESCAPED_UNICODE);
                exit(0);
            } else {
                $this->denegarSesion("Contraseña incorrecta", 1);
            }
        }

        $this->denegarSesion("Credenciales requeridas", 1);
    }

    /**
     * Denegar sesión: registra intento fallido y devuelve error
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
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    /**
     * Emitir token inicial de sesión
     */
    function set_login($id_user = 0, $local = false)
    {
        $hash = strtotime(date('Y-m-d H:i:s')) * 27;
        $token_hash = md5($hash);

        $token = cifrar_texto(json_encode(array(
            'id_user' => $id_user ?? 0,
            'token_hash' => $token_hash
        ), JSON_UNESCAPED_UNICODE));

        $insert = array();
        $insert['token'] = $token;
        $insert['caduca'] = date('Y-m-d');
        $insert['id_user'] = $id_user ?? 0;
        $insert['estado'] = 1;
        $this->db->insert('admin_token', $insert);

        if ($local == true) {
            return $token;
        }

        $r = array();
        $r['error'] = false;
        $r['token'] = $token;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }
}

$accion = ACCION;
$c = new Sesion();
$c->$accion();
