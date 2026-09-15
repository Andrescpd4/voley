<?php
// ============================================================
// EXTERNOS.PHP — Bootstrap de la aplicacion
//
// Hace TODO lo necesario ANTES de cargar cualquier pagina:
//   1. Cargar Composer (autoload)
//   2. Cargar variables de entorno (.env)
//   3. Headers de seguridad
//   4. Manejo anti-fingerprint de cookies
//   5. Sesion PHP
//   6. Cargar configuracion.php (define EMPRESA, rutas, etc.)
//   7. Conectar a base de datos
//   8. Cargar funciones generales (helpers)
//   9. Cargar clase base
// ============================================================

// 1. Composer autoload
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// 2. Headers de seguridad (se envian ANTES de session_start)
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// 3. Configuracion de cookies y sesion
$nombre_sistema = "voley-plus-v1";
ini_set('session.use_trans_sid', 0);
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set("session.cookie_secure", 0); // 0 en local, 1 en produccion con HTTPS
ini_set("session.use_cookies", 1);

session_set_cookie_params(0);
error_reporting(E_ERROR | E_PARSE);

// Logging: configurar ruta de logs (como en logistics)
$log_dir = __DIR__ . '/storage/logs/';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0777, true);
}
$name_logs = $log_dir . 'logs_' . date('Y_m_d') . '.log';
ini_set('log_errors', TRUE);
ini_set('error_log', $name_logs);

// 5. Manejo anti-fingerprint: cookies con nombres aleatorios
if (isset($_COOKIE['_one_page']) && $_COOKIE['_one_page'] == 'false') {
    // Segunda carga de pagina: recuperar sesion por cookie cifrada
    $refrescar = array();
    foreach ($_COOKIE as $k => $valor) {
        $data = openssl_decrypt($valor, "aes256", "@64_9aeF");
        $data = json_decode($data, true);
        if (isset($data['nombre_sesion'])) {
            $nombre_sesion = $data['nombre_sesion'];
        } else {
            if (!isset($data['nombre_sesion_data'])) {
                $refrescar[$k] = $valor;
            }
        }
    }
    session_name($nombre_sesion);
    session_start();
    foreach ($refrescar as $k => $valor) {
        if (!isset($_SESSION['nombre_usuario'])) {
            $valor = hash("sha256", "$nombre_sistema-" . uniqid());
            setcookie($k, substr($valor, 0, 32), 0, "/", "", false, true);
        }
    }
    if (!isset($_SESSION['nombre_usuario'])) {
        session_regenerate_id(true);
    }
} else {
    // Primera carga: generar cookies senuelo + cookie real con nombre de sesion
    for ($i = 0; $i <= rand(1, 2); $i++) {
        $nombre = hash("sha256", "$nombre_sistema-" . uniqid());
        $valor = hash("sha256", "$nombre_sistema-" . uniqid());
        setcookie($nombre, substr($valor, 0, 32), 0, "/", "", false, true);
    }

    $nombre_sesion = hash("sha256", "$nombre_sistema-" . uniqid());
    session_name($nombre_sesion);
    session_start();

    for ($i = 0; $i <= rand(1, 2); $i++) {
        $nombre = hash("sha256", "$nombre_sistema-" . uniqid());
        $valor = hash("sha256", "$nombre_sistema-" . uniqid());
        setcookie($nombre, substr($valor, 0, 32), 0, "/", "", false, true);
    }

    for ($i = 0; $i <= 2; $i++) {
        $nombre = hash("sha256", "$nombre_sistema-" . uniqid());
        if ($i == 2) {
            $string = json_encode(array('nombre_sesion' => $nombre_sesion));
        } else {
            $string = json_encode(array('nombre_sesion_data' => "{$nombre_sistema}_{$i}", 'encrypt' => "sha512"));
        }
        $valor = openssl_encrypt($string, "aes256", "@64_9aeF");
        setcookie($nombre, $valor, 0, "/", "", false, true);
    }
}

// 6. Zona horaria y locale
date_default_timezone_set('America/Bogota');
setlocale(LC_ALL, "esm", "es_CO.utf8");
setcookie('_one_page', "false", 0, "/", "", false, true);

// 7. Cargar configuracion
require_once("configuracion.php");

// 8. Conectar a BD y cargar funciones/helpers
require_once("php/db_conect.php");
require_once("php/funciones_general.php");
require_once("php/clase_base.php");

$db = new db_conect();
@$db->pconnect(
    $cfg['db_host'],
    $cfg['db_user'],
    $cfg['db_password'],
    $cfg['db_database_name'],
    $cfg['db_port'],
    $cfg['db_driver'],
    $cfg['charset'],
    $cfg['collation']
);

if ($db->connect_error()) {
    echo "<h1>Error de conexion con el servidor de base de datos.</h1>";
    exit(0);
}

// 9. Niveles de acceso por defecto
if (!isset($_SESSION['acceso_menu'])) {
    $_SESSION['acceso_menu'] = array(1, 2);
}

// 10. Backups de GET/POST originales
$__POST = $_POST;
$__GET = $_GET;
