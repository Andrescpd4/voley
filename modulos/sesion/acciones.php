<?php
// ============================================================
// SESION — Backend del módulo de autenticación
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - Cero emojis
//   - SQL seguro con intval y escape_string
//   - Trazabilidad y versionamiento de autorizaciones
// ============================================================

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once("php/clase_base.php");

class Sesion extends clase_base
{
    // 1. Validar formulario de login
    function validar()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $hash = $_SERVER['HTTP_AUTHORIZATION'];
        } else {
            $hash = '';
        }

        // Si viene con cifrado AES (login con token previo)
        if (!empty($hash) && function_exists('validar_token_login')) {
            if (validar_token_login($hash)) {
                $_POST = desencriptar_post($_POST, $hash);
            }
        }

        $v = new Validation($_POST);
        $v->addRules('usuario', 'Usuario', array('required' => true, 'length' => array(1, 50)));
        $v->addRules('clave', 'Clave', array('required' => true, 'length' => array(4, 100)));

        $resultado = $v->validate();
        if ($resultado['messages'] == "") {
            return true;
        } else {
            $this->denegarSesion($resultado['messages'], 4);
        }
    }

    // 2. Comprobar si el usuario tiene pendiente la aceptacion de la politica de privacidad
    function verificar_politica_pendiente($persona_id)
    {
        $id_limpio = intval($persona_id);
        $sql_tipo = "SELECT id, nombre, version FROM tipo_autorizacion WHERE slug = 'politica_privacidad' AND activo = 1";
        $tipo = $this->db->select_row($sql_tipo);

        if (!is_array($tipo)) {
            return array('pendiente' => false, 'version' => '1.0', 'tipo_id' => 0, 'nombre' => '');
        }
        if (!isset($tipo['id'])) {
            return array('pendiente' => false, 'version' => '1.0', 'tipo_id' => 0, 'nombre' => '');
        }

        $tipo_id = intval($tipo['id']);
        $version_vigente = $tipo['version'];
        $nombre_doc = $tipo['nombre'];

        // Buscar firma vigente aceptada
        $sql_firma = "SELECT id FROM autorizacion_firmada WHERE acudiente_id = " . $id_limpio . " AND tipo_autorizacion_id = " . $tipo_id . " AND version_firmada = '" . $this->db->escape_string($version_vigente) . "' AND aceptada = 1";
        $firma = $this->db->select_row($sql_firma);

        if (is_array($firma)) {
            if (isset($firma['id'])) {
                return array('pendiente' => false, 'version' => $version_vigente, 'tipo_id' => $tipo_id, 'nombre' => $nombre_doc);
            }
        }

        return array('pendiente' => true, 'version' => $version_vigente, 'tipo_id' => $tipo_id, 'nombre' => $nombre_doc);
    }

    // 3. Iniciar sesion
    function iniciar()
    {
        $this->validar();

        if (isset($_SESSION['usuario'])) {
            $this->denegarSesion("Ya existe una sesión activa", 3);
        }

        if (isset($_POST['usuario']) && isset($_POST['clave'])) {
            $login = strtoupper(trim($_POST['usuario']));
            $clave = $_POST['clave'];

            // 3.1 Buscar usuario activo en BD
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

            // 3.2 Verificar contrasena con password_verify / verificar_clave
            if (verificar_clave($clave, $rw['password_hash'])) {
                if (isset($rw['rol_id'])) {
                    $rol_id = intval($rw['rol_id']);
                } else {
                    $rol_id = 3;
                }

                // 3.3 Registrar inicio de sesion en admin_sesion
                if (isset($_SERVER['HTTP_USER_AGENT'])) {
                    $user_agent = $this->db->escape_string($_SERVER['HTTP_USER_AGENT']);
                } else {
                    $user_agent = '';
                }

                if (isset($_SERVER['HTTP_REFERER'])) {
                    $refer = $_SERVER['HTTP_REFERER'];
                } else {
                    $refer = '';
                }

                $sesion_data = array();
                $sesion_data['usuario'] = $rw['identificacion'];
                $sesion_data['session_id'] = session_id();
                $sesion_data['user_agent'] = $user_agent;
                $sesion_data['refer'] = $refer;
                $sesion_data['ip'] = verIP();
                $sesion_data['inicio'] = date('Y-m-d H:i:s');
                $sesion_data['fin'] = date('Y-m-d H:i:s');
                $sesion_data['salida'] = "N";

                $this->db->insert("admin_sesion", $sesion_data);
                $sesion_id = $this->db->last_insert_id();

                // 3.4 Actualizar ultimo acceso del usuario
                $this->db->getConnection()->update(
                    "UPDATE usuario SET ultimo_acceso = ? WHERE id = ?",
                    array(date('Y-m-d H:i:s'), $rw['usuario_id'])
                );

                // 3.5 Configurar $_SESSION
                if (isset($rw['rol_nombre'])) {
                    $nombre_rol = $rw['rol_nombre'];
                } else {
                    $nombre_rol = 'Usuario';
                }

                if (isset($rw['rol_slug'])) {
                    $slug_rol = $rw['rol_slug'];
                } else {
                    $slug_rol = 'usuario';
                }

                if (!empty($rw['foto'])) {
                    $foto_usuario = $rw['foto'];
                } else {
                    $foto_usuario = 'img/user.png';
                }

                $_SESSION['sesion_id'] = $sesion_id;
                $_SESSION['usuario_id'] = $rw['usuario_id'];
                $_SESSION['usuario'] = $rw['identificacion'];
                $_SESSION['login'] = $rw['login'];
                $_SESSION['nombre_usuario'] = trim($rw['nombre1'] . " " . $rw['apellido1']);
                $_SESSION['nombre_usuario_c'] = trim($rw['nombre1'] . " " . $rw['nombre2'] . " " . $rw['apellido1'] . " " . $rw['apellido2']);
                $_SESSION['persona_id'] = $rw['persona_id'];
                $_SESSION['usuario_rol'] = $rol_id;
                $_SESSION['rol'] = $nombre_rol;
                $_SESSION['rol_slug'] = $slug_rol;
                $_SESSION['foto'] = $foto_usuario;
                $_SESSION['TOKEN_ID'] = strtotime(date('Y-m-d H:i:s')) * 2;
                $_SESSION['TOKEN'] = get_token(strtotime(date('Y-m-d H:i:s')) * 3, $_SESSION['TOKEN_ID']);
                $_SESSION['acceso_menu'] = array(1, 3, 7);

                // 3.6 Comprobar si tiene la politica pendiente de firma
                $estado_politica = $this->verificar_politica_pendiente($rw['persona_id']);

                // 3.7 Generar JWT para el cliente
                $next_token = $this->set_login($_SESSION['persona_id'], true);

                $payload = $_SESSION;
                $payload['user_agent'] = $user_agent;
                $payload['next_token'] = $next_token;

                if (isset($_ENV['JWT_SECRET'])) {
                    $clave_jwt = $_ENV['JWT_SECRET'];
                } else {
                    $clave_jwt = 'clave_secreta_voleyplus_2026';
                }
                $jwt = JWT::encode($payload, $clave_jwt, 'HS256');

                $r = array();
                $r['error'] = false;
                $r['msg'] = "Bienvenido " . $_SESSION['nombre_usuario'];
                $r['token'] = $jwt;
                $r['politica_pendiente'] = $estado_politica['pendiente'];
                $r['politica_version'] = $estado_politica['version'];
                $r['politica_nombre'] = $estado_politica['nombre'];
                $r['politica_url'] = WEB_ROOT . 'politica-privacidad';
                $r['redirect'] = WEB_ROOT . 'inicio';
                echo json_encode($r, JSON_UNESCAPED_UNICODE);
                exit(0);
            } else {
                $this->denegarSesion("Contraseña incorrecta", 1);
            }
        }

        $this->denegarSesion("Credenciales requeridas", 1);
    }

    // 4. Aceptar la version vigente de la politica de privacidad
    function aceptar_politica()
    {
        if (!isset($_SESSION['persona_id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = "Debe iniciar sesión para registrar su consentimiento.";
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            exit(0);
        }

        $persona_id = intval($_SESSION['persona_id']);

        // 4.1 Buscar politica vigente
        $sql_tipo = "SELECT id, nombre, version FROM tipo_autorizacion WHERE slug = 'politica_privacidad' AND activo = 1";
        $tipo = $this->db->select_row($sql_tipo);

        if (!is_array($tipo) || !isset($tipo['id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = "No se encontró una política de privacidad activa en el sistema.";
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            exit(0);
        }

        $tipo_id = intval($tipo['id']);
        $version_vigente = $tipo['version'];

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agente_navegador = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agente_navegador = '';
        }

        if (isset($_SESSION['usuario'])) {
            $documento_usuario = $_SESSION['usuario'];
        } else {
            $documento_usuario = '';
        }

        if (isset($_SESSION['nombre_usuario_c'])) {
            $nombre_completo = $_SESSION['nombre_usuario_c'];
        } else if (isset($_SESSION['nombre_usuario'])) {
            $nombre_completo = $_SESSION['nombre_usuario'];
        } else {
            $nombre_completo = '';
        }

        $ip_cliente = verIP();
        $fecha_firma = date('Y-m-d H:i:s');
        $hash_evidencia = hash('sha256', $persona_id . '|' . $documento_usuario . '|' . $version_vigente . '|' . $fecha_firma . '|' . $ip_cliente . '|ACEPTADA');

        $evidencia_array = array(
            'ip' => $ip_cliente,
            'user_agent' => $agente_navegador,
            'fecha_hora' => $fecha_firma,
            'persona_id' => $persona_id,
            'documento' => $documento_usuario,
            'nombre_completo' => $nombre_completo,
            'version_documento' => $version_vigente,
            'estado' => 'ACEPTADA',
            'hash_evidencia' => $hash_evidencia
        );

        $firma_json = json_encode($evidencia_array, JSON_UNESCAPED_UNICODE);

        // 4.2 Revisar si ya existe registro para esta persona, tipo y version
        $sql_existe = "SELECT id FROM autorizacion_firmada WHERE acudiente_id = " . $persona_id . " AND tipo_autorizacion_id = " . $tipo_id . " AND version_firmada = '" . $this->db->escape_string($version_vigente) . "'";
        $fila_existe = $this->db->select_row($sql_existe);

        if (is_array($fila_existe) && isset($fila_existe['id'])) {
            $datos_actualizar = array(
                'aceptada' => 1,
                'fecha_firma' => $fecha_firma,
                'firma_electronica' => $firma_json
            );
            $this->db->update('autorizacion_firmada', $datos_actualizar, array('id' => intval($fila_existe['id'])));
        } else {
            $datos_insertar = array(
                'deportista_id' => null,
                'acudiente_id' => $persona_id,
                'tipo_autorizacion_id' => $tipo_id,
                'fecha_firma' => $fecha_firma,
                'firma_electronica' => $firma_json,
                'aceptada' => 1,
                'version_firmada' => $version_vigente
            );
            $this->db->insert('autorizacion_firmada', $datos_insertar);
        }

        $r = array();
        $r['error'] = false;
        $r['msg'] = "Política de privacidad aceptada correctamente.";
        $r['redirect'] = WEB_ROOT . 'inicio';
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    // 5. Rechazar la politica de privacidad (destruye la sesion y expulsa al usuario)
    function rechazar_politica()
    {
        if (isset($_SESSION['persona_id'])) {
            $persona_id = intval($_SESSION['persona_id']);

            $sql_tipo = "SELECT id, version FROM tipo_autorizacion WHERE slug = 'politica_privacidad' AND activo = 1";
            $tipo = $this->db->select_row($sql_tipo);

            if (is_array($tipo) && isset($tipo['id'])) {
                $tipo_id = intval($tipo['id']);
                $version_vigente = $tipo['version'];

                if (isset($_SERVER['HTTP_USER_AGENT'])) {
                    $agente_navegador = $_SERVER['HTTP_USER_AGENT'];
                } else {
                    $agente_navegador = '';
                }

                if (isset($_SESSION['usuario'])) {
                    $documento_usuario = $_SESSION['usuario'];
                } else {
                    $documento_usuario = '';
                }

                $ip_cliente = verIP();
                $fecha_firma = date('Y-m-d H:i:s');
                $hash_evidencia = hash('sha256', $persona_id . '|' . $documento_usuario . '|' . $version_vigente . '|' . $fecha_firma . '|' . $ip_cliente . '|RECHAZADA');

                $evidencia_array = array(
                    'ip' => $ip_cliente,
                    'user_agent' => $agente_navegador,
                    'fecha_hora' => $fecha_firma,
                    'persona_id' => $persona_id,
                    'documento' => $documento_usuario,
                    'version_documento' => $version_vigente,
                    'estado' => 'RECHAZADA',
                    'hash_evidencia' => $hash_evidencia
                );

                $firma_json = json_encode($evidencia_array, JSON_UNESCAPED_UNICODE);

                // Guardar constancia de rechazo para auditoria
                $sql_existe = "SELECT id FROM autorizacion_firmada WHERE acudiente_id = " . $persona_id . " AND tipo_autorizacion_id = " . $tipo_id . " AND version_firmada = '" . $this->db->escape_string($version_vigente) . "'";
                $fila_existe = $this->db->select_row($sql_existe);

                if (is_array($fila_existe) && isset($fila_existe['id'])) {
                    $datos_actualizar = array(
                        'aceptada' => 0,
                        'fecha_firma' => $fecha_firma,
                        'firma_electronica' => $firma_json
                    );
                    $this->db->update('autorizacion_firmada', $datos_actualizar, array('id' => intval($fila_existe['id'])));
                } else {
                    $datos_insertar = array(
                        'deportista_id' => null,
                        'acudiente_id' => $persona_id,
                        'tipo_autorizacion_id' => $tipo_id,
                        'fecha_firma' => $fecha_firma,
                        'firma_electronica' => $firma_json,
                        'aceptada' => 0,
                        'version_firmada' => $version_vigente
                    );
                    $this->db->insert('autorizacion_firmada', $datos_insertar);
                }
            }
        }

        // Destruir sesion completamente
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $r = array();
        $r['error'] = false;
        $r['msg'] = "Has rechazado la política de privacidad. Tu sesión no puede continuar.";
        $r['redirect'] = WEB_ROOT . 'iniciar-sesion';
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    // 6. Denegar sesion: registra intento fallido y devuelve error
    private function denegarSesion($msg, $tipo)
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $this->db->escape_string($_SERVER['HTTP_USER_AGENT']);
        } else {
            $user_agent = '';
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $refer = $_SERVER['HTTP_REFERER'];
        } else {
            $refer = '';
        }

        if (isset($_POST['usuario'])) {
            $usuario_post = $_POST['usuario'];
        } else {
            $usuario_post = '';
        }

        $row = array();
        $row['session_id'] = session_id();
        $row['user_agent'] = $user_agent;
        $row['refer'] = $refer;
        $row['ip'] = verIP();
        $row['fecha'] = date('Y-m-d H:i:s');
        $row['usuario'] = $usuario_post;
        $row['tipo'] = $tipo;

        $this->db->insert("admin_sesion_denegada", $row);

        $r = array();
        $r['error'] = true;
        $r['msg'] = $msg;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    // 7. Emitir token inicial de sesion
    function set_login($id_user = 0, $local = false)
    {
        $hash = strtotime(date('Y-m-d H:i:s')) * 27;
        $token_hash = md5($hash);

        if ($id_user !== null) {
            $id_final = $id_user;
        } else {
            $id_final = 0;
        }

        $token = cifrar_texto(json_encode(array(
            'id_user' => $id_final,
            'token_hash' => $token_hash
        ), JSON_UNESCAPED_UNICODE));

        $insert = array();
        $insert['token'] = $token;
        $insert['caduca'] = date('Y-m-d');
        $insert['id_user'] = $id_final;
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
