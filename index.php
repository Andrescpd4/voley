<?php
// ============================================================
// INDEX.PHP — Front Controller
//
// Recibe todas las peticiones (gracias a .htaccess), parsea
// la URL para obtener el menu y la accion, y despacha:
//   - tipo_accion = 'pagina'  -> pagina.php (HTML completo)
//   - tipo_accion = 'json'    -> descarga.php (JSON para AJAX)
// ============================================================

// 1. Calcular raíz web (ej: /voley/)
$web_root = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));
$path_root = __DIR__;

// 2. Parsear URL: /voley/usuarios/listar -> params[0]='usuarios', params[1]='listar'
$p = substr($_SERVER['REQUEST_URI'], strlen($web_root));
$p = str_replace("?", "//?", $p);
$_PARAMS = explode("/", $p);

// 3. Definir constantes globales
define("WEB_ROOT", $web_root);
define("PATH_ROOT", $path_root);

// 4. Cargar bootstrap (externos.php conecta DB, inicia sesion, etc.)
require_once("externos.php");
define("TOKEN", $_SESSION['TOKEN'] ?? '');
define("TOKEN_ID", $_SESSION['TOKEN_ID'] ?? 0);
define("HASH_TOKEN", TOKEN);

// 5. Determinar menu y accion
$_PARAMS[0] = $_PARAMS[0] == "" ? ($cfg['menu_inicio'] ?? 'inicio') : $_PARAMS[0];

// 6. Si hay parametro 1, buscar accion exacta; si no, usar la accion por defecto del menu
$sql_extra = $_PARAMS[1] == "" ? "  AND a.accion = m.accion" : " AND a.accion='" . $_PARAMS[1] . "' ";

// 7. Consultar en BD: menu + accion + tipo de archivo que debe cargar
$sql = "SELECT tm.archivo, a.accion, m.nombre as ruta
        FROM
           admin_menu m,
           admin_accion a,
           admin_tipo_accion tm
        WHERE
            m.menu = a.menu
            AND a.tipo_accion = tm.codigo
            AND a.menu = '" . $_PARAMS[0] . "'
            $sql_extra";

if ($rw = $db->select_row($sql)) {
    // 8. Encontro la ruta -> definir constantes y cargar archivo
    define("RUTA_MENU", $rw['ruta']);
    define("MENU", $_PARAMS[0]);
    define("PAGE_ROOT", WEB_ROOT . MENU . "/");
    define("ACCION", ($_PARAMS[1] == "") ? $rw['accion'] : $_PARAMS[1]);
    insertar_log($rw['archivo'], 1, 'EXITO');
    require_once($rw['archivo']);
} else {
    // 9. No encontro -> error 404
    define("RUTA_MENU", "Vinculo no valido");
    define("MENU", $_PARAMS[0]);
    define("PAGE_ROOT", WEB_ROOT . MENU . "/");
    define("ACCION", "");
    require_once("404.php");
}
