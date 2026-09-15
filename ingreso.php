<?php
// ============================================================
// INGRESO.PHP — Carga la pagina completa del sistema (logueado)
//
// Estructura:
//   cabeza.php  (head, topbar, sidebar, scripts)
//   formulario.php del modulo (contenido)
//   pie.php     (footer, JS global, permisos)
// ============================================================

include_once 'cabeza.php';

if (is_login()) {
    $r = obtener_ruta_menu(MENU, ACCION);
    if ($r['error'] == false) {
        require_once($r['ruta']);
    } else {
        insertar_log('404.php', 2, $r['msg']);
        alerta($r['msg']);
    }
} else {
    insertar_log('404.php', 2, 'Error 404');
    include_once '404.php';
}

include_once 'pie.php';
