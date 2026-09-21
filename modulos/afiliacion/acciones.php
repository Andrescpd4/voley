<?php
// afiliacion/acciones.php - Backend del modulo de afiliacion
require_once("php/clase_base.php");

class Afiliacion extends clase_base
{
    // Listar solicitudes de afiliacion
    function listar()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        $sql = "SELECT a.id,
                       CONCAT_WS(' ', p1.nombre1, p1.nombre2, p1.apellido1, p1.apellido2) AS acudiente_nombre,
                       CONCAT_WS(' ', p2.nombre1, p2.nombre2, p2.apellido1, p2.apellido2) AS deportista_nombre,
                       a.estado,
                       a.porcentaje_completado,
                       a.fecha_solicitud,
                       a.fecha_aprobacion
                FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                INNER JOIN persona p1 ON ac.persona_id = p1.id
                INNER JOIN v_deportista d ON a.deportista_id = d.id
                INNER JOIN persona p2 ON d.persona_id = p2.id
                WHERE 1=1";

        // Filtro por estado
        if (isset($_GET['estado']) && $_GET['estado'] != '' && $_GET['estado'] != 'NULL') {
            $estado = $this->db->escape_string($_GET['estado']);
            $sql .= " AND a.estado = '$estado'";
        }

        $sql .= " ORDER BY a.fecha_solicitud DESC";

        $total = $this->db->count_rows($sql);
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $rs = $this->db->select_limit($sql, $limit, $offset);

        $rows = array();
        $num = $offset + 1;
        for ($i = 0; $i < count($rs); $i++) {
            $rw = $rs[$i];
            $rw['_NUM_'] = $num++;
            $rw['id'] = urlsafe_b64encode($rw['id']);
            $rows[] = $rw;
        }

        echo json_encode(['total' => $total, 'rows' => $rows]);
    }

    // Obtener una solicitud por ID
    function asignar()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        $id = urlsafe_b64decode($_GET['id']);
        $id = intval($id);

        if ($id <= 0) {
            echo json_encode(['error' => true, 'msg' => 'ID invalido']);
            return;
        }

        $sql = "SELECT a.*,
                       ac.persona_id as acudiente_persona_id,
                       ac.deportista_id,
                       ac.parentesco,
                       ac.es_principal,
                       d.categoria_id,
                       d.eps,
                       d.contacto_emergencia,
                       d.telefono_emergencia,
                       d.observaciones as deportista_observaciones
                FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                INNER JOIN v_deportista d ON a.deportista_id = d.id
                WHERE a.id = $id";

        $solicitud = $this->db->select_row($sql);
        if (empty($solicitud)) {
            echo json_encode(['error' => true, 'msg' => 'Solicitud no encontrada']);
            return;
        }

        echo json_encode(['error' => false, 'data' => $solicitud]);
    }

    // Guardar/Crear solicitud
    function agregar()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        // Obtener datos
        $acudiente_id = intval($_POST['acudiente_id']);
        $deportista_id = intval($_POST['deportista_id']);
        $estado = isset($_POST['estado']) ? $_POST['estado'] : 'borrador';

        if ($acudiente_id <= 0 || $deportista_id <= 0) {
            echo json_encode(['error' => true, 'msg' => 'Datos invalidos']);
            return;
        }

        $datos = array(
            'acudiente_id' => $acudiente_id,
            'deportista_id' => $deportista_id,
            'estado' => $estado,
            'porcentaje_completado' => 0,
            'fecha_solicitud' => date('Y-m-d H:i:s'),
            '_usuario' => $_SESSION['usuario'] ?? '',
            '_fecha' => date('Y-m-d H:i:s')
        );

        $id = $this->db->insert('v_afiliacion', $datos);
        if ($id > 0) {
            echo json_encode(['error' => false, 'msg' => 'Solicitud creada correctamente', 'id' => urlsafe_b64encode($id)]);
        } else {
            echo json_encode(['error' => true, 'msg' => 'Error al crear la solicitud']);
        }
    }

    // Actualizar estado de solicitud (aprobar/rechazar)
    function modificar()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        $id = intval($_POST['id']);
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
        $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

        if ($id <= 0) {
            echo json_encode(['error' => true, 'msg' => 'ID invalido']);
            return;
        }

        $datos = array(
            'estado' => $estado,
            'observaciones' => $observaciones,
            '_usuario' => $_SESSION['usuario'] ?? '',
            '_fecha' => date('Y-m-d H:i:s')
        );

        if ($estado == 'aprobado') {
            $datos['fecha_aprobacion'] = date('Y-m-d H:i:s');
            $datos['porcentaje_completado'] = 100;
        }

        $this->db->update('v_afiliacion', $datos, array('id' => $id));
        echo json_encode(['error' => false, 'msg' => 'Solicitud actualizada correctamente']);
    }

    // Eliminar solicitud
    function eliminar()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        $id = intval($_POST['id']);
        if ($id <= 0) {
            echo json_encode(['error' => true, 'msg' => 'ID invalido']);
            return;
        }

        $this->db->query("DELETE FROM v_afiliacion WHERE id = $id");
        echo json_encode(['error' => false, 'msg' => 'Solicitud eliminada']);
    }

    // Listar acudientes para select
    function listarAcudientes()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        $sql = "SELECT ac.id, CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS nombre
                FROM v_acudiente ac
                INNER JOIN persona p ON ac.persona_id = p.id
                WHERE ac.activo = 1
                ORDER BY p.nombre1, p.apellido1";

        $acudientes = $this->db->select_all($sql);
        echo json_encode(['error' => false, 'data' => $acudientes]);
    }

    // Listar deportistas para select
    function listarDeportistas()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(['error' => true, 'msg' => 'Error en TOKEN']);
            return;
        }

        $sql = "SELECT d.id, CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS nombre
                FROM v_deportista d
                INNER JOIN persona p ON d.persona_id = p.id
                WHERE d.estado = 'activo'
                ORDER BY p.nombre1, p.apellido1";

        $deportistas = $this->db->select_all($sql);
        echo json_encode(['error' => false, 'data' => $deportistas]);
    }
}

$accion = ACCION;
$f = new Afiliacion();
$f->$accion();
?>
