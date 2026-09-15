<?php
// ============================================================
// INICIO — Backend del dashboard principal
//
// Metodos:
//   - set_token()  : Renueva JWT (llamado en cada cambio de pagina)
//   - dashboard()  : Retorna datos del dashboard (JSON)
// ============================================================

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once("php/clase_base.php");

class Formulario extends clase_base
{
    /**
     * Renueva el token JWT en cada cambio de pagina
     */
    function set_token()
    {
        if (isset($_SESSION['nombre_usuario'])) {
            // Limpiar tokens consumidos
            $this->db->query("DELETE FROM admin_token WHERE estado = 2");

            // Validar JWT actual
            $token_valido = false;
            try {
                $jwt = str_replace(" ", "", strval($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
                $decoded = $this->db->object_to_array(JWT::decode($jwt, new Key($_ENV['JWT_SECRET'] ?? 'clave_secreta', 'HS256')));

                if (is_array($decoded) && isset($decoded['next_token'])) {
                    $token_anterior = $decoded['next_token'];
                    $rw = $this->db->select_row("SELECT * FROM admin_token WHERE token = '" . $this->db->escape_string($token_anterior) . "' AND estado = 1 AND id_user = '" . ($_SESSION['persona_id'] ?? 0) . "'");
                    if ($rw) {
                        // Verificar expiracion por dia
                        if (isset($rw['caduca']) && $rw['caduca'] < date('Y-m-d')) {
                            $r['error'] = true;
                            $r['msg'] = "Su sesion ha expirado por inactividad";
                            echo json_encode($r);
                            return;
                        }
                        // Consumir token anterior
                        $update = array();
                        $update['estado'] = 2;
                        $this->db->update("admin_token", $update, array('id' => $rw['id']));
                        $token_valido = true;
                    }
                }
            } catch (Exception $e) {
                // JWT corrupto -> emitir uno nuevo
            }

            // Emitir token fresco
            $this->_emitir_token();
        } else {
            $r['error'] = true;
            $r['msg'] = "Usuario sin sesion activa";
            echo json_encode($r);
        }
    }

    /**
     * Emitir nuevo JWT para el usuario logueado
     */
    private function _emitir_token()
    {
        $hash = strtotime(date('Y-m-d H:m:s')) * 27;
        $token_hash = md5($hash);

        $token = cifrar_texto(json_encode(array(
            'caduca' => date('Y-m-d'),
            'id_user' => $_SESSION['persona_id'] ?? 0,
            'token_hash' => $token_hash
        ), JSON_UNESCAPED_UNICODE));

        $insert = array();
        $insert['token'] = $token;
        $insert['caduca'] = date('Y-m-d');
        $insert['id_user'] = $_SESSION['persona_id'] ?? 0;
        $this->db->insert('admin_token', $insert);

        $payload = array(
            'caduca' => date('Y-m-d'),
            'id_user' => $_SESSION['persona_id'] ?? 0,
            'token_hash' => $token_hash,
            'next_token' => $token
        );
        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'] ?? 'clave_secreta', 'HS256');

        $r['error'] = false;
        $r['data'] = $jwt;
        $r['msg'] = "ok";
        echo json_encode($r);
    }

    /**
     * Retorna datos para el dashboard
     */
    function dashboard()
    {
        // Ejemplo de datos para el dashboard
        $datos = array(
            'total_deportistas' => 0,
            'nuevas_solicitudes' => 0,
            'documentacion_pendiente' => 0,
            'autorizaciones_pendientes' => 0,
            'proximos_eventos' => array()
        );

        echo json_encode(array('error' => false, 'data' => $datos));
    }
}

$accion = ACCION;
$f = new Formulario();
$f->$accion();
