<?php
// v_categoria/acciones.php - CRUD de categorías
require_once("php/formulario_basico.php");

class Categoria extends formulario_basico
{
    public function __construct()
    {
        parent::__construct('v_categoria', 'id', true);
    }

    function validar()
    {
        $val = new Validation();
        $val->addRules('nombre', 'Nombre', 'required|max_len,50');

        $resultado = $val->validate();
        if ($resultado['errors'] > 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Errores: ' . implode(', ', $resultado['messages']);
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return false;
        }
        return true;
    }
}

$_POST['_usuario'] = $_SESSION['usuario'] ?? '';
$_POST['_fecha'] = date('Y-m-d H:i:s');
$_POST['id'] = urlsafe_b64decode($_POST['id'] ?? '');
$_GET['id'] = urlsafe_b64decode($_GET['id'] ?? '');

$accion = ACCION;
$f = new Categoria();
$f->$accion();
?>
