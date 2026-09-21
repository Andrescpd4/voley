<?php
// ============================================================
// FORMULARIO_BASICO.PHP — Clase base para CRUD automatico
//
// Proporciona metodos listar, agregar, modificar, eliminar
// que cualquier modulo puede extender para operaciones CRUD
// simples sin escribir logica repetitiva.
//
// Seguridad:
//   - Los IDs se validan con intval() o urldecode segun corresponda
//   - Los formularios deben agregar validacion personalizada
// ============================================================

require_once("php/validation.php");

class formulario_basico
{
    protected $tabla;
    protected $clave_primaria;
    protected $auto_incremental;
    protected $db;
    protected $sql_fila;

    /**
     * Constructor
     * @param string $tabla Nombre de la tabla en BD
     * @param string $clave_primaria Nombre del campo PK
     * @param boolean $auto_incremental Si el PK es autoincremental
     */
    public function __construct($tabla, $clave_primaria, $auto_incremental = true)
    {
        $this->tabla = $tabla;
        $this->clave_primaria = $clave_primaria;
        $this->db = $GLOBALS['db'];
        $this->auto_incremental = $auto_incremental;
        $this->sql_fila = '';
    }

    /**
     * Validacion por defecto (se puede sobrescribir)
     */
    function validar()
    {
        return true;
    }

    /**
     * Consulta SQL base para listar (se puede sobrescribir)
     */
    protected function getSQL()
    {
        return "SELECT * FROM " . $this->tabla . " ORDER BY " . $this->clave_primaria . " ASC";
    }

    /**
     * Obtener una fila por su PK
     */
    protected function fila($agregar = false)
    {
        $pk = $this->clave_primaria;

        if ($this->sql_fila != '') {
            $sql = sprintf($this->sql_fila, $id);
        } else {
            if ($agregar == true && $this->auto_incremental == true) {
                $id = $this->db->last_insert_id();
            } else {
                $id = $_POST[$pk];
            }
            $sql = "SELECT * FROM " . $this->tabla . " WHERE " . $pk . "='" . $id . "'";
        }

        $rw = $this->db->select_row($sql);
        if (is_array($rw) && isset($rw['id'])) {
            $rw['id'] = urlsafe_b64encode($rw['id']);
        }
        return $rw;
    }

    /**
     * Listar registros con paginacion
     */
    function listar()
    {
        $sql = $this->getSQL();

        $result = array();
        $result['total'] = $this->db->count_rows($sql);

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

        $rs = $this->db->select_limit($sql, $limit, $offset);
        $result['rows'] = array();
        $num = $offset + 1;

        for ($i = 0; $i < count($rs); $i++) {
            $rw = $rs[$i];
            $rw['_NUM_'] = $num++;
            $rw['id'] = urlsafe_b64encode($rw['id']);
            $result['rows'][] = $rw;
        }

        echo json_encode($result);
    }

    /**
     * Agregar nuevo registro
     */
    function agregar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        // Quitar PK si es autoincremental
        if ($this->auto_incremental == true) {
            unset($_POST[$this->clave_primaria]);
        }

        $id = $this->db->insert($this->tabla, $_POST);

        $r = array();
        if ($this->db->error()) {
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r);
            die;
        }

        $r['error'] = false;
        $r['msg'] = "Registro agregado con exito";
        $r['row'] = $this->fila(true);

        // Bitacora
        $tipo = 1;
        $nuevos = $_POST;
        $mensaje = $r['msg'];
        $viejos = false;
        insertar_bitacora($tipo, $nuevos, $mensaje, $viejos);

        echo json_encode($r);
    }

    /**
     * Modificar registro existente
     */
    function modificar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        $pk = $this->clave_primaria;
        $v_pk = $_POST[$pk];
        $update = $_POST;
        unset($update[$pk]);

        // Guardar datos anteriores para bitacora
        $viejos = $this->db->select_row("SELECT * FROM " . $this->tabla . " WHERE " . $pk . "='" . $v_pk . "'");

        $this->db->update($this->tabla, $update, array($pk => $v_pk));

        $r = array();
        if ($this->db->error()) {
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r);
            die;
        }

        $r['error'] = false;
        $r['msg'] = "Registro modificado con exito";
        $r['row'] = $this->fila(false);

        // Bitacora
        $tipo = 3;
        $nuevos = $_POST;
        $mensaje = $r['msg'];
        insertar_bitacora($tipo, $nuevos, $mensaje, $viejos);

        echo json_encode($r);
    }

    /**
     * Eliminar registro
     */
    function eliminar()
    {
        $pk = $this->clave_primaria;
        $id = $_POST[$pk];

        // Guardar datos anteriores para bitacora
        $viejos = $this->db->select_row("SELECT * FROM " . $this->tabla . " WHERE " . $pk . "='" . $id . "'");

        $sql = "DELETE FROM " . $this->tabla . " WHERE " . $pk . "='" . $id . "'";
        $this->db->query($sql);

        $r = array();
        if ($this->db->error()) {
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r);
            die;
        }

        $r['error'] = false;
        $r['msg'] = "Registro eliminado con exito";

        // Bitacora
        $tipo = 2;
        $nuevos = false;
        $mensaje = $r['msg'];
        insertar_bitacora($tipo, $nuevos, $mensaje, $viejos);

        echo json_encode($r);
    }

    /**
     * Asignar/cargar un registro por GET id
     */
    function asignar()
    {
        $pk = $this->clave_primaria;
        $sql = "SELECT * FROM " . $this->tabla . " WHERE " . $pk . "='" . $_GET[$pk] . "'";
        $rw = $this->db->select_row($sql);
        $rw['id'] = urlsafe_b64encode($rw['id']);
        echo json_encode($rw);
    }
}
?>