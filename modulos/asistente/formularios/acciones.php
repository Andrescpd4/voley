<?php
// acciones.php - Listar formularios creados
require_once("php/clase_base.php");

class Formularios extends Base
{
    function listar()
    {
        // 1. Validar token
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        // 2. Consultar menús que tienen ruta = 'modulos/...' (formularios generados)
        $sql = "SELECT menu, nombre, ruta, accion, orden FROM admin_menu WHERE ruta LIKE 'modulos/%' AND padre IS NOT NULL ORDER BY orden";
        $formularios = $this->db->select_all($sql);

        echo json_encode(['error' => false, 'msg' => 'OK', 'data' => $formularios]);
    }
}

$accion = ACCION;
$f = new Formularios();
$f->$accion();
?>
