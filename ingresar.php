<?php
// ============================================================
// INGRESAR.PHP — Wrapper del login
//
// Se carga cuando el usuario NO tiene sesion.
// Verifica que exista la funcion is_login y carga la vista
// de inicio de sesion (modulos/sesion/iniciar_sesion.php).
// ============================================================

if (!function_exists('is_login')) {
    include_once '404.php';
    exit();
}

ob_start();
$r = obtener_ruta_menu(MENU, ACCION);

if ($r['error'] == false) {
    require_once($r['ruta']);
} else {
    // Si no encontro ruta, mostrar formulario de login por defecto
    include_once 'modulos/sesion/iniciar_sesion.php';
}
