<?php
// afiliacion_helpers.php - Utilidades comunes para el modulo Afiliacion
// Uso: use afiliacion_helpers; en la clase Formulario

trait afiliacion_helpers
{
    // 1. Validar token de autorizacion
    function validar_token()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->_error('Error en TOKEN');
            return false;
        }
        return true;
    }

    // 2. Validar token simple (para FormData)
    function validar_token_simple()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->_error('Error en TOKEN');
            return false;
        }
        return true;
    }

    // 3. Obtener ID de usuario de la sesion
    function _obtener_usuario_id()
    {
        return isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
    }

    // 4. Obtener rol de usuario de la sesion
    function _obtener_usuario_rol()
    {
        return isset($_SESSION['rol']) ? intval($_SESSION['rol']) : 0;
    }

    // 5. Verificar si es admin (rol 1 o 4)
    function _es_admin()
    {
        $rol = $this->_obtener_usuario_rol();
        return ($rol === 1 || $rol === 4);
    }

    // 6. Verificar si es acudiente (rol 3)
    function _es_acudiente()
    {
        $rol = $this->_obtener_usuario_rol();
        return ($rol === 3);
    }

    // 7. Escape basico para SQL (usar marcadores ? en consultas preparadas)
    function _escape($valor)
    {
        return $this->db->escape_string($valor);
    }

    // 8. Consulta segura con marcadores ? - SELECT
    function _consultar($sql, $params = array())
    {
        return $this->db->getConnection()->select($sql, $params);
    }

    // 9. Consulta segura - una sola fila
    function _obtener_fila($sql, $params = array())
    {
        return $this->db->getConnection()->select_row($sql, $params);
    }

    // 10. Consulta segura - un solo valor
    function _obtener_valor($sql, $params = array())
    {
        $resultado = $this->db->getConnection()->select_one($sql, $params);
        if (is_string($resultado)) {
            return $resultado;
        }
        return '';
    }

    // 11. Contar filas con consulta segura
    function _contar($sql, $params = array())
    {
        $resultado = $this->db->getConnection()->select_one($sql, $params);
        if (is_string($resultado)) {
            return intval($resultado);
        }
        return 0;
    }

    // 12. Insertar con helper seguro
    function _insertar($tabla, $datos)
    {
        return $this->db->insert($tabla, $datos);
    }

    // 13. Actualizar con helper seguro
    function _actualizar($tabla, $datos, $where)
    {
        return $this->db->update($tabla, $datos, $where);
    }

    // 14. Respuesta exitosa estandar
    function _success($msg = '', $data = array())
    {
        $respuesta = array('error' => false);
        if ($msg !== '') {
            $respuesta['msg'] = $msg;
        }
        if (!empty($data)) {
            $respuesta['data'] = $data;
        }
        echo json_encode($respuesta);
    }

    // 15. Respuesta de error estandar
    function _error($msg)
    {
        echo json_encode(array('error' => true, 'msg' => $msg));
    }

    // 16. Registrar en bitacora
    function _historiar($tipo, $descripcion, $id_registro = 0, $observacion = '')
    {
        if (function_exists('insertar_bitacora')) {
            insertar_bitacora($tipo, $descripcion, $observacion);
        }
    }

    // 17. Construir filtro de estado para SQL
    function _construir_filtro_estado($estado)
    {
        if ($estado !== '' && $estado !== 'NULL' && $estado !== null) {
            return " AND a.estado = ?";
        }
        return '';
    }

    // 18. Obtener parametros de paginacion
    function _obtener_paginacion()
    {
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
        if ($limit > 200) {
            $limit = 200;
        }
        return array('offset' => $offset, 'limit' => $limit);
    }

    // 19. Obtener parametros de DataTables
    function _obtener_datatables_params()
    {
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 50;
        if ($length > 200) {
            $length = 200;
        }
        $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

        return array(
            'start' => $start,
            'length' => $length,
            'search' => $search,
            'draw' => $draw
        );
    }

    // 20. Nombre seguro para archivos
    function _nombre_seguro($nombre)
    {
        $nombre = basename($nombre);
        $nombre = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombre);
        return $nombre;
    }

    // 21. Validar archivo PDF
    function _validar_pdf($archivo, $max_mb = 10)
    {
        $max_bytes = $max_mb * 1024 * 1024;

        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return array('ok' => false, 'msg' => 'No se recibio ningun archivo');
        }

        if ($archivo['size'] > $max_bytes) {
            return array('ok' => false, 'msg' => 'El archivo excede el tamaño maximo de ' . $max_mb . 'MB');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        $permitidos = array('application/pdf', 'application/x-pdf');
        if (!in_array($mime, $permitidos)) {
            return array('ok' => false, 'msg' => 'Tipo de archivo no permitido (solo PDF)');
        }

        return array('ok' => true, 'mime' => $mime);
    }

    // 22. Mover archivo subido de forma segura
    function _mover_archivo($origen, $destino)
    {
        if (function_exists('move_uploaded_file')) {
            return move_uploaded_file($origen, $destino);
        }
        return copy($origen, $destino);
    }

    // 23. Obtener solicitudes del usuario actual (acudiente)
    function _obtener_solicitudes_usuario($usuario_id, $estado = '')
    {
        $sql = "SELECT a.id,
                       CONCAT_WS(' ', p1.nombre1, p1.nombre2, p1.apellido1, p1.apellido2) AS acudiente_nombre,
                       CONCAT_WS(' ', p2.nombre1, p2.nombre2, p2.apellido1, p2.apellido2) AS deportista_nombre,
                       a.estado,
                       a.porcentaje_completado,
                       a.fecha_solicitud,
                       a.fecha_aprobacion,
                       a.pdf_ruta
                FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                INNER JOIN persona p1 ON ac.persona_id = p1.id
                INNER JOIN v_deportista d ON a.deportista_id = d.id
                INNER JOIN persona p2 ON d.persona_id = p2.id
                WHERE ac.usuario_id = ?";

        $params = array($usuario_id);

        if ($estado !== '') {
            $sql .= " AND a.estado = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY a.fecha_solicitud DESC";

        return $this->_consultar($sql, $params);
    }

    // 24. Obtener total de solicitudes del usuario
    function _contar_solicitudes_usuario($usuario_id, $estado = '')
    {
        $sql = "SELECT COUNT(*) FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                WHERE ac.usuario_id = ?";

        $params = array($usuario_id);

        if ($estado !== '') {
            $sql .= " AND a.estado = ?";
            $params[] = $estado;
        }

        return $this->_contar($sql, $params);
    }

    // 25. Obtener todas las solicitudes (admin)
    function _obtener_todas_solicitudes($estado = '', $offset = 0, $limit = 50)
    {
        $sql = "SELECT a.id,
                       CONCAT_WS(' ', p1.nombre1, p1.nombre2, p1.apellido1, p1.apellido2) AS acudiente_nombre,
                       CONCAT_WS(' ', p2.nombre1, p2.nombre2, p2.apellido1, p2.apellido2) AS deportista_nombre,
                       a.estado,
                       a.porcentaje_completado,
                       a.fecha_solicitud,
                       a.fecha_aprobacion,
                       a.pdf_ruta
                FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                INNER JOIN persona p1 ON ac.persona_id = p1.id
                INNER JOIN v_deportista d ON a.deportista_id = d.id
                INNER JOIN persona p2 ON d.persona_id = p2.id
                WHERE 1=1";

        $params = array();

        if ($estado !== '' && $estado !== 'NULL' && $estado !== null) {
            $sql .= " AND a.estado = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY a.fecha_solicitud DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->_consultar($sql, $params);
    }

    // 26. Contar todas las solicitudes (admin)
    function _contar_todas_solicitudes($estado = '')
    {
        $sql = "SELECT COUNT(*) FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                INNER JOIN persona p1 ON ac.persona_id = p1.id
                INNER JOIN v_deportista d ON a.deportista_id = d.id
                INNER JOIN persona p2 ON d.persona_id = p2.id
                WHERE 1=1";

        $params = array();

        if ($estado !== '' && $estado !== 'NULL' && $estado !== null) {
            $sql .= " AND a.estado = ?";
            $params[] = $estado;
        }

        return $this->_contar($sql, $params);
    }

    // 27. Obtener detalle de una solicitud
    function _obtener_detalle_solicitud($id)
    {
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
                WHERE a.id = ?";

        return $this->_obtener_fila($sql, array($id));
    }

    // 28. Verificar que solicitud pertenece al usuario
    function _solicitud_pertenece_usuario($solicitud_id, $usuario_id)
    {
        $sql = "SELECT a.id FROM v_afiliacion a
                INNER JOIN v_acudiente ac ON a.acudiente_id = ac.id
                WHERE a.id = ? AND ac.usuario_id = ?";

        $resultado = $this->_obtener_fila($sql, array($solicitud_id, $usuario_id));
        return !empty($resultado);
    }
}