<?php
// ============================================================
// INGRESO.PHP — Carga la pagina completa del sistema (logueado)
//
// Estructura:
//   1. Verificacion de sesion activa
//   2. Candado de politica de privacidad (redireccion si pendiente)
//   3. cabeza.php  (head, topbar, sidebar, scripts)
//   4. formulario.php del modulo (contenido)
//   5. pie.php     (footer, JS global, permisos)
// ============================================================

if (!function_exists('is_login')) {
    include_once '404.php';
    exit();
}

if (is_login()) {
    // 1. Candado de aceptacion de politica de privacidad
    if (MENU !== 'politica-privacidad' && MENU !== 'cerrar-sesion') {
        global $db;
        if (isset($_SESSION['persona_id'])) {
            $persona_id_actual = intval($_SESSION['persona_id']);
        } else {
            $persona_id_actual = 0;
        }

        if ($persona_id_actual > 0) {
            $sql_pol = "SELECT id, version FROM tipo_autorizacion WHERE slug = 'politica_privacidad' AND activo = 1";
            $tipo_pol = $db->select_row($sql_pol);

            if (is_array($tipo_pol) && isset($tipo_pol['id'])) {
                $tipo_id_pol = intval($tipo_pol['id']);
                $version_pol = $tipo_pol['version'];
                $sql_firma = "SELECT id FROM autorizacion_firmada WHERE acudiente_id = " . $persona_id_actual . " AND tipo_autorizacion_id = " . $tipo_id_pol . " AND version_firmada = '" . $db->escape_string($version_pol) . "' AND aceptada = 1";
                $firma_pol = $db->select_row($sql_firma);

                $tiene_firma_vigente = false;
                if (is_array($firma_pol)) {
                    if (isset($firma_pol['id'])) {
                        $tiene_firma_vigente = true;
                    }
                }

                if (!$tiene_firma_vigente) {
                    header("Location: " . WEB_ROOT . "politica-privacidad");
                    exit();
                }
            }
        }
    }

    include_once 'cabeza.php';

    $r = obtener_ruta_menu(MENU, ACCION);
    if ($r['error'] == false) {
        require_once($r['ruta']);
    } else {
        insertar_log('404.php', 2, $r['msg']);
        alerta($r['msg']);
    }

    include_once 'pie.php';
} else {
    insertar_log('404.php', 2, 'Error 404');
    include_once '404.php';
}
