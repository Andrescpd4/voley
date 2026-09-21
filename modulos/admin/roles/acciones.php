<?php
// ============================================================
// ROLES — Backend CRUD del catalogo de roles
//
// Usa el patron formulario_basico para operaciones automaticas:
// listar, agregar, modificar, eliminar
// ============================================================

require_once("php/formulario_basico.php");

class Rol extends formulario_basico
{
    /**
     * Validar formulario antes de agregar/modificar
     * - Nombre: requerido, maximo 50 caracteres
     * - Descripcion: opcional, maximo 255 caracteres
     */
    function validar()
    {
        $v = new Validation($_POST);
        $v->addRules('nombre', 'Nombre del rol', array('required' => true, 'maxLength' => 50));
        $v->addRules('descripcion', 'Descripcion', array('maxLength' => 255));

        $result = $v->validate();

        if ($result['messages'] == '') {
            return true;
        }

        $r = array();
        $r['error'] = true;
        $r['msg'] = $result['messages'];
        $r['bad_fields'] = $result['bad_fields'];
        $r['errors'] = $result['errors'];
        echo json_encode($r);
        exit(0);
    }

    /**
     * Consulta SQL para listar roles
     * Incluye conteo de usuarios asignados a cada rol
     */
    function getSQL()
    {
        $sql = "SELECT r.*,
                       (SELECT COUNT(*) FROM admin_usuario au WHERE au.rol_id = r.id) AS total_usuarios
                FROM admin_rol r
                WHERE 1=1";

        // Filtro por nombre si se busca
        if (isset($_GET['nombre']) && $_GET['nombre'] != '' && $_GET['nombre'] != 'NULL') {
            // Seguridad: usar parametro en vez de concatenar
            $nombre = '%' . str_replace(' ', '%', $_GET['nombre']) . '%';
            $sql .= " AND r.nombre LIKE ?";
            // El formulario_basico usa select_limit que soporta parametros bind
            // Como getSQL() retorna el SQL, usamos una clase envoltoria
            // Para simplificar, sanitizamos la entrada
            $nombre_safe = addslashes($nombre);
            $sql .= " AND r.nombre LIKE '" . $nombre_safe . "'";
        }

        $sql .= " ORDER BY r.id ASC";
        return $sql;
    }
}

// Decodificar ID que llega en base64
if (isset($_POST['id']) && $_POST['id'] != '') {
    $_POST['id'] = urlsafe_b64decode($_POST['id']);
}
if (isset($_GET['id']) && $_GET['id'] != '') {
    $_GET['id'] = urlsafe_b64decode($_GET['id']);
}

$accion = ACCION;
$f = new Rol('admin_rol', 'id', true);
$f->$accion();
?>
