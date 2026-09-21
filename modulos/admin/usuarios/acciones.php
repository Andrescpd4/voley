<?php
// ============================================================
// ADMIN USUARIOS — Backend del módulo
//
// El framework llega aquí así:
//   index.php -> descarga.php -> define('ACCION', '...') -> este archivo
//
// ACCION puede ser: listar, agregar, modificar, eliminar, asignar,
//                   listarPersonasSinUsuario, listarRoles
// ============================================================

require_once __DIR__ . '/../../../php/clase_base.php';
require_once __DIR__ . '/clases/au_helpers.php';
require_once __DIR__ . '/clases/au_usuarios.php';

class Formulario extends Base
{
    use au_helpers;
    use au_usuarios;
}

// El despachador ejecuta el método cuyo nombre llega en ACCION
$accion = ACCION;
$f = new Formulario();
$f->$accion();
?>
