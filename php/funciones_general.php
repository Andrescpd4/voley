<?php
// ============================================================
// FUNCIONES_GENERAL.PHP — Helpers globales del sistema
// ============================================================

// --------------------------------------------------------//
// SESION Y AUTENTICACION
// --------------------------------------------------------//

function is_login()
{
    if (isset($_SESSION['nombre_usuario'])) {
        return true;
    }
    return false;
}

function verIP()
{
    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

// --------------------------------------------------------//
// SISTEMA DE RUTAS Y PERMISOS
// --------------------------------------------------------//

function obtener_ruta_menu($menu, $accion)
{
    global $db;

    // Buscar archivo de la accion
    $sql = "SELECT CONCAT_WS('/', m.ruta, a.archivo) as ruta, m.acceso
            FROM admin_accion a, admin_menu m
            WHERE a.menu = m.menu AND a.menu = '" . $db->escape_string($menu) . "' AND a.accion = '" . $db->escape_string($accion) . "'";

    if ($rw = $db->select_row($sql)) {
        // Verificar nivel de acceso
        if (in_array($rw['acceso'], $_SESSION['acceso_menu'])) {
            // Nivel 7: requiere permiso especifico por rol
            if ($rw['acceso'] == "7") {
                $sql2 = "SELECT p.menu FROM admin_permiso_menu p
                         WHERE p.menu = '" . $db->escape_string($menu) . "' AND p.rol = '" . ($_SESSION['usuario_rol'] ?? 0) . "'";
                $p = $db->select_one($sql2);

                if (!$p) {
                    return array("error" => true, "msg" => "Acceso denegado al menu");
                }

                // Verificar permiso para la accion especifica
                $sql3 = "SELECT id, requiere_permiso FROM admin_accion WHERE menu = '" . $db->escape_string($menu) . "' AND accion = '" . $db->escape_string($accion) . "'";
                $datos_accion = $db->select_row($sql3);
                $requiere_permiso = trim(strtoupper($datos_accion['requiere_permiso'] ?? 'N'));

                if ($requiere_permiso == 'S') {
                    $sql4 = "SELECT id FROM admin_permiso_accion WHERE rol = '" . ($_SESSION['usuario_rol'] ?? 0) . "' AND accion = '" . $datos_accion['id'] . "'";
                    $permiso_accion = $db->select_one($sql4);
                    if (!$permiso_accion) {
                        return array("error" => true, "msg" => "Acceso denegado a la accion");
                    }
                }
            }

            $r = str_replace("//", "/", $rw['ruta']);
            if (file_exists($r)) {
                return array("error" => false, "ruta" => $r);
            } else {
                return array("error" => true, "msg" => "Archivo no encontrado");
            }
        } else {
            return array("error" => true, "msg" => "Nivel de acceso insuficiente");
        }
    } else {
        return array("error" => true, "msg" => "Menu o accion no encontrados");
    }
}

// --------------------------------------------------------//
// LOGS Y AUDITORIA
// --------------------------------------------------------//

function insertar_log($archivo, $tipo, $mensaje)
{
    global $db;

    $insertar = array();
    $insertar['id_persona'] = $_SESSION['persona_id'] ?? 0;
    $insertar['archivo'] = $archivo;
    $insertar['tipo'] = $tipo;
    $insertar['mensaje'] = $mensaje;
    $insertar['menu'] = MENU ?? '';
    $insertar['accion'] = ACCION ?? '';

    if (isset($_SESSION['nombre_usuario'])) {
        $sql_log = "INSERT INTO admin_log (id_persona, archivo, tipo, mensaje, menu, accion) VALUES (
            '" . $insertar['id_persona'] . "',
            '" . $db->escape_string($archivo) . "',
            '$tipo',
            '" . $db->escape_string($mensaje) . "',
            '" . $db->escape_string(MENU ?? '') . "',
            '" . $db->escape_string(ACCION ?? '') . "'
        )";
        $db->query($sql_log);
    }
}

function insertar_bitacora($tipo, $nuevos, $mensaje, $viejos = false)
{
    global $db;

    $insertar = array();
    $insertar['id_tipo_bitacora'] = $tipo;
    $insertar['datos_anteriores'] = serialize($viejos);
    $insertar['datos_insertados'] = serialize($nuevos);
    $insertar['observacion'] = $mensaje;
    $insertar['menu'] = MENU ?? '';
    $insertar['id_usuario'] = $_SESSION['persona_id'] ?? 0;

    $db->insert("admin_bitacoras_sistema", $insertar);
}

// --------------------------------------------------------//
// CIFRADO / DESCIFRADO AES (para POST de login)
// --------------------------------------------------------//

function AES_encrypt($data, $password)
{
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';

    // Salt the key(32) and iv(16) = 48
    while (strlen($salted) < 48) {
        $dx = md5($dx . $password . $salt, true);
        $salted .= $dx;
    }

    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32, 16);

    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    return base64_encode('Salted__' . $salt . $encryptedData);
}

function AES_decrypt($data, $password)
{
    $data = base64_decode($data);
    $salt = substr($data, 8, 8);
    $ciphertext = substr($data, 16);

    // CryptoJS usa EVP_BytesToKey con 3 rondas de MD5
    $data00 = $password . $salt;
    $D1 = md5($data00, true);
    $D2 = md5($D1 . $data00, true);
    $D3 = md5($D2 . $data00, true);

    $derived = $D1 . $D2 . $D3;
    $key = substr($derived, 0, 32);
    $iv  = substr($derived, 32, 16);

    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, true, $iv);
}

function cifrar_texto($string, $pass = 'voley_plus_key_2024')
{
    $method = 'aes256';
    return openssl_encrypt($string, $method, $pass);
}

function decifrar_texto($string, $pass = 'voley_plus_key_2024')
{
    $method = 'aes256';
    return openssl_decrypt($string, $method, $pass);
}

function desencriptar_post($data, $hash)
{
    $formateo_post = array();
    foreach ($data as $k => $v) {
        $valor = AES_decrypt($v, $hash);
        if (!$valor) {
            $valor = $v;
        }
        $k = AES_decrypt($k, $hash);
        $formateo_post[$k] = $valor;
    }
    return $formateo_post;
}

function get_token($data, $pass)
{
    return AES_encrypt($data, $pass);
}

// --------------------------------------------------------//
// HASH DE CONTRASEÑAS (BCRYPT NATIVO)
// --------------------------------------------------------//

function clave($clave, $extra1 = "voley", $extra2 = "plus2024")
{
    // Retorna hash bcrypt seguro para contraseñas
    return password_hash($clave, PASSWORD_BCRYPT, ['cost' => 10]);
}

function verificar_clave($clave, $hash)
{
    // 1. Si es hash bcrypt/argon (empieza con $)
    if (str_starts_with($hash, '$')) {
        return password_verify($clave, $hash);
    }
    
    // 2. Compatibilidad con SHA512 legado si existiera
    $salt1 = "voleybP5MrcqS7wsMXUPJ";
    $salt2 = "voleyQvMQcHJXNhCtAmvy";
    $v = crypt($clave, '$6$rounds=6000$' . $salt1 . '$');
    $legacy = md5(sha1($v) . md5($salt2) . sha1("plus2024"));
    if ($hash === $legacy || $hash === md5($clave)) {
        return true;
    }
    
    return false;
}

// --------------------------------------------------------//
// VALIDACION DE TOKEN JWT
// --------------------------------------------------------//

function validar_token($token)
{
    global $db;
    $caduca = date('Y-m-d');
    $rw = $db->select_row("SELECT * FROM admin_token WHERE token = '" . $db->escape_string($token) . "' AND caduca >= '$caduca'");
    if ($rw) {
        return true;
    }
    return false;
}

function validar_token_login($token)
{
    global $db;
    $caduca = date('Y-m-d');
    $rw = $db->select_row("SELECT * FROM admin_token WHERE token = '" . $db->escape_string($token) . "' AND caduca >= '$caduca' AND estado = 1");
    if ($rw) {
        $update = array();
        $update['estado'] = 2;
        $db->update("admin_token", $update, array('id' => $rw['id']));
        return true;
    }
    return false;
}

// --------------------------------------------------------//
// HELPERS DE UI
// --------------------------------------------------------//

function alerta($msg)
{
    if (is_login()) {
        echo '<div class="container h-100-vh" style="background: white">
            <div class="row align-items-md-center h-100-vh">
                <div class="col-lg-6">
                    <img class="img-fluid" src="' . WEB_ROOT . 'img/error.jpg" alt="image">
                </div>
                <div class="col-lg-4 offset-lg-1">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div>
                                <figure class="avatar avatar-sm mr-3 bring-forward">
                                    <span class="avatar-title bg-warning-bright text-warning rounded-circle">
                                        <i class="ti-bell"></i>
                                    </span>
                                </figure>
                            </div>
                            <div>
                                <h6 class="d-flex justify-content-between mb-4">
                                    <span>Error al procesar solicitud</span>
                                    <span class="text-muted font-weight-normal">' . date('Y-m-d') . '</span>
                                </h6>
                                <a href="#">
                                    <div class="mb-3 border p-3 border-radius-1">
                                        ' . htmlspecialchars($msg) . '
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    } else {
        include_once("404.php");
    }
}

function llenar_combo($sql, $blanco = false, $predeterminado = "")
{
    global $db;
    if ($blanco == true) {
        echo '<option value="">Seleccione...</option>';
    }
    $rs = @$db->select_all($sql);
    if (!is_array($rs)) {
        return;
    }
    foreach ($rs as $key => $rw) {
        $rw = array_values($rw);
        $sel = (trim($rw[0]) == $predeterminado) ? "selected='selected'" : "";
        $texto = (isset($rw[1]) && $rw[1] !== null && $rw[1] !== '') ? $rw[1] : $rw[0];
        echo "<option $sel value='" . htmlspecialchars($rw[0]) . "'>" . htmlspecialchars($texto) . "</option>";
    }
}

// --------------------------------------------------------//
// UTILIDADES
// --------------------------------------------------------//

function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', '.'), $data);
    return $data;
}

function urlsafe_b64decode($string)
{
    $data = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

function log_system($msg, $line = 0, $file = false, $type = 0)
{
    error_log($msg);
}

function pre($array = array())
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function fechaesp($date)
{
    if ($date == '1969-12-31' || $date == '0000-00-00' || $date == '') {
        return '';
    }

    $dia = explode("-", $date, 3);
    $year = $dia[0];
    $month = (string)(int)$dia[1];
    $day = (string)(int)$dia[2];

    $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
    $tomadia = $dias[intval((date("w", mktime(0, 0, 0, $month, $day, $year))))];
    $meses = array("", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
    return $tomadia . ", " . $day . " de " . $meses[$month] . " de " . $year;
}

function formatear_fecha($fecha)
{
    if ($fecha == '1969-12-31' || $fecha == '0000-00-00' || $fecha == '') {
        return "";
    }
    return $fecha;
}

function validar_fecha($fecha)
{
    if ($fecha == '1969-12-31' || $fecha == '0000-00-00' || $fecha == '') {
        return 0;
    }
    return strtotime($fecha);
}

function eliminar_tildes($cadena)
{
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena
    );
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena
    );
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena
    );
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena
    );
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $cadena
    );
    return $cadena;
}

function limpiarString($texto){
    $texto = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL,$texto);
    $texto = str_replace('\r\n', "",$texto); 
    $texto = trim($texto);   

    $texto = str_replace('\n',PHP_EOL,$texto);
    $texto = str_replace('\r',PHP_EOL,$texto);
    $texto = str_replace("'", "\'",$texto);
    $texto = str_replace("\\"," ", $texto);  
    return $texto; 
}
