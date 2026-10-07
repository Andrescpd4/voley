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
    // 1. Candado de consentimientos obligatorios (cola completa, no un solo slug)
    // Si falta la firma de CUALQUIER documento obligatorio, se redirige a
    // politica-privacidad, pagina que permite firmar toda la cola pendiente.
    if (MENU !== 'politica-privacidad' && MENU !== 'cerrar-sesion') {
        global $db;
        if (isset($_SESSION['persona_id'])) {
            $persona_id_actual = intval($_SESSION['persona_id']);
        } else {
            $persona_id_actual = 0;
        }

        if ($persona_id_actual > 0) {
            $sql_docs = "SELECT id, version FROM tipo_autorizacion WHERE activo = 1 AND clase = 'obligatorio' ORDER BY orden ASC, id ASC";
            $docs_obligatorios = $db->select_all($sql_docs);

            $falta_alguno = false;
            if (is_array($docs_obligatorios)) {
                for ($i = 0; $i < count($docs_obligatorios); $i++) {
                    $tipo_id_doc = intval($docs_obligatorios[$i]['id']);
                    $version_doc = $docs_obligatorios[$i]['version'];
                    $sql_firma = "SELECT id FROM autorizacion_firmada WHERE acudiente_id = " . $persona_id_actual . " AND tipo_autorizacion_id = " . $tipo_id_doc . " AND version_firmada = '" . $db->escape_string($version_doc) . "' AND aceptada = 1";
                    $firma_doc = $db->select_row($sql_firma);

                    $tiene_firma_vigente = false;
                    if (is_array($firma_doc)) {
                        if (isset($firma_doc['id'])) {
                            $tiene_firma_vigente = true;
                        }
                    }

                    if (!$tiene_firma_vigente) {
                        $falta_alguno = true;
                    }
                }
            }

            if ($falta_alguno) {
                header("Location: " . WEB_ROOT . "politica-privacidad");
                exit();
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
