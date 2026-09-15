<?php
// ============================================================
// PAGINA.PHP — Puerta de entrada segun estado de sesion
//
// Si hay sesion activa -> ingreso.php (sistema)
// Si no hay sesion    -> ingresar.php (login)
// ============================================================

if (!function_exists('is_login')) {
    include_once '404.php';
    exit();
}

ob_start();

if (is_login()) {
    // Usuario logueado -> mostrar sistema
    include_once 'ingreso.php';
} else {
    // Usuario invitado -> mostrar login
    include_once 'ingresar.php';
}
