<?php
// afiliacion_admin.php - Logica de administracion (Tab 3: Gestion)

trait afiliacion_admin
{
    // ============================================================
    // TAB 3: GESTION / ADMINISTRACION (Admin - Roles 1, 4)
    // ============================================================

    // 1. Listar solicitudes para DataTable server-side
    function listar_gestion()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para acceder a esta seccion');
            return;
        }

        $params_dt = $this->_obtener_datatables_params();
        $start = $params_dt['start'];
        $length = $params_dt['length'];
        $search = $params_dt['search'];
        $draw = $params_dt['draw'];

        if (isset($_POST['estado'])) {
            $estado = $_POST['estado'];
        } else {
            $estado = '';
        }

        if (isset($_POST['fecha_inicio'])) {
            $fecha_inicio = $_POST['fecha_inicio'];
        } else {
            $fecha_inicio = '';
        }

        if (isset($_POST['fecha_fin'])) {
            $fecha_fin = $_POST['fecha_fin'];
        } else {
            $fecha_fin = '';
        }

        // 1. Consulta base sobre v_afiliacion
        $sql_base = "SELECT a.id,
                            CONCAT_WS(' ', a.acudiente_nombre1, a.acudiente_apellido1) AS acudiente_nombre,
                            CONCAT_WS(' ', a.deportista_nombre1, a.deportista_apellido1) AS deportista_nombre,
                            a.deportista_identificacion,
                            a.acudiente_identificacion,
                            a.estado,
                            a.porcentaje_completado,
                            a.fecha_solicitud,
                            a.fecha_aprobacion,
                            a.pdf_ruta,
                            a.deportista_id,
                            a.acudiente_id
                     FROM v_afiliacion a
                     WHERE 1=1";

        $params = array();

        // 2. Filtro por estado
        if ($estado !== '' && $estado !== 'NULL' && $estado !== null) {
            $sql_base .= " AND a.estado = ?";
            $params[] = $estado;
        }

        // 3. Filtro por rango de fechas
        if ($fecha_inicio !== '') {
            $sql_base .= " AND a.fecha_solicitud >= ?";
            $params[] = $fecha_inicio;
        }
        if ($fecha_fin !== '') {
            $sql_base .= " AND a.fecha_solicitud <= ?";
            $params[] = $fecha_fin;
        }

        // 4. Filtro por busqueda global
        if ($search !== '') {
            $sql_base .= " AND (a.deportista_nombre1 LIKE ? OR a.deportista_apellido1 LIKE ? OR a.deportista_identificacion LIKE ? OR a.acudiente_nombre1 LIKE ? OR a.acudiente_apellido1 LIKE ?)";
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // 5. Contar total de registros
        $sql_total = "SELECT COUNT(*) FROM v_afiliacion a WHERE 1=1";
        $params_total = array();
        if ($estado !== '' && $estado !== 'NULL' && $estado !== null) {
            $sql_total .= " AND a.estado = ?";
            $params_total[] = $estado;
        }
        $recordsTotal = $this->_contar($sql_total, $params_total);
        $recordsFiltered = $this->_contar(str_replace(
            "SELECT a.id,\n                            CONCAT_WS(' ', a.acudiente_nombre1, a.acudiente_apellido1) AS acudiente_nombre,\n                            CONCAT_WS(' ', a.deportista_nombre1, a.deportista_apellido1) AS deportista_nombre,\n                            a.deportista_identificacion,\n                            a.acudiente_identificacion,\n                            a.estado,\n                            a.porcentaje_completado,\n                            a.fecha_solicitud,\n                            a.fecha_aprobacion,\n                            a.pdf_ruta,\n                            a.deportista_id,\n                            a.acudiente_id",
            "SELECT COUNT(*)",
            $sql_base
        ), $params);

        // 6. Obtener datos paginados
        $sql_data = $sql_base . " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $params_data = $params;
        $params_data[] = $length;
        $params_data[] = $start;

        $rows = $this->_consultar($sql_data, $params_data);

        // 7. Formatear datos y botones para la tabla
        $data = array();
        $num = $start + 1;
        for ($i = 0; $i < count($rows); $i++) {
            $rw = $rows[$i];
            $id = $rw['id'];
            $dep_id = $rw['deportista_id'];

            // Botones de accion renderizados en el backend
            // Llevan aria-label para lectores de pantalla y area tactil minima de 44px
            $rw['btn_ver'] = '<button class="btn btn-sm btn-outline-info btn-afili-accion accion-ver" onclick="afiliacionGestionVer(' . $id . ')" title="Ver Ficha" aria-label="Ver ficha de la solicitud"><i class="ri-eye-line"></i></button>';
            $rw['btn_estado'] = '<button class="btn btn-sm btn-outline-primary btn-afili-accion accion-modificar" onclick="afiliacionGestionAbrirEstado(' . $id . ')" title="Cambiar Estado" aria-label="Cambiar estado de la solicitud"><i class="ri-edit-line"></i></button>';
            $rw['btn_eliminar'] = '<button class="btn btn-sm btn-outline-danger btn-afili-accion accion-eliminar" onclick="afiliacionGestionEliminar(' . $id . ')" title="Eliminar" aria-label="Desactivar solicitud de afiliacion"><i class="ri-delete-bin-line"></i></button>';

            $rw['num'] = $num++;
            $rw['progreso'] = $this->_calcular_progreso_afiliacion($dep_id, $rw['estado']);
            $rw['id_encoded'] = urlsafe_b64encode($id);
            $data[] = $rw;
        }

        // 8. Respuesta formato DataTables
        echo json_encode(array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ));
    }

    // 2. Obtener detalle completo para modal Ver
    function asignar_gestion()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        // 0. Leer ID desde POST (frontend afiliacionAjax) con fallback a GET
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
        } else if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        } else {
            $id = 0;
        }

        if ($id <= 0) {
            $this->_error('ID invalido');
            return;
        }

        // 1. Obtener datos completos de la vista
        $solicitud = $this->_obtener_fila(
            "SELECT * FROM v_afiliacion WHERE id = ?",
            array($id)
        );

        if (empty($solicitud)) {
            $this->_error('Solicitud no encontrada');
            return;
        }

        // 2. Obtener documentos del deportista (incluye los tipos que aun faltan)
        $documentos = $this->_listar_documentos_solicitud($solicitud['deportista_id']);
        $solicitud['documentos'] = $documentos;

        // 3. Obtener categoria
        if ($solicitud['deportista_categoria_id'] > 0) {
            $cat = $this->_obtener_fila(
                "SELECT nombre FROM categoria WHERE id = ?",
                array($solicitud['deportista_categoria_id'])
            );
            if (!empty($cat)) {
                $solicitud['categoria_nombre'] = $cat['nombre'];
            } else {
                $solicitud['categoria_nombre'] = '-';
            }
        } else {
            $solicitud['categoria_nombre'] = '-';
        }

        $this->_success('', $solicitud);
    }

    // 3. Cambiar estado de la solicitud + observaciones
    function modificar_gestion()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para modificar solicitudes');
            return;
        }

        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
        } else {
            $id = 0;
        }
        if (isset($_POST['estado'])) {
            $estado = trim($_POST['estado']);
        } else {
            $estado = '';
        }
        if (isset($_POST['observaciones'])) {
            $observaciones = trim($_POST['observaciones']);
        } else {
            $observaciones = '';
        }

        if ($id <= 0) {
            $this->_error('ID invalido');
            return;
        }

        if ($estado === '') {
            $this->_error('Debe seleccionar un estado');
            return;
        }

        // Solo se aceptan los estados definidos para la afiliacion
        $estados_validos = array('pendiente_revision', 'requiere_info', 'aprobado', 'no_aprobado', 'activo', 'inactivo');
        if (!in_array($estado, $estados_validos, true)) {
            $this->_error('Estado no valido');
            return;
        }

        // Al devolver la solicitud el acudiente necesita saber que corregir
        if ($estado === 'requiere_info' && $observaciones === '') {
            $this->_error('Indique en las observaciones que debe corregir el acudiente');
            return;
        }

        // 1. Obtener deportista_id vinculado
        $solicitud = $this->_obtener_fila(
            "SELECT deportista_id FROM deportista_acudiente WHERE id = ?",
            array($id)
        );
        if (empty($solicitud)) {
            $this->_error('Solicitud no encontrada');
            return;
        }
        $deportista_id = $solicitud['deportista_id'];

        // 2. Preparar actualizacion (las observaciones del club van aparte de las del acudiente)
        $datos = array(
            'estado' => $estado,
            'observaciones_revision' => $observaciones
        );

        if ($estado === 'aprobado' || $estado === 'activo') {
            $datos['fecha_afiliacion'] = date('Y-m-d');
        }

        // 3. Actualizar tabla deportista
        $this->_actualizar('deportista', $datos, array('id' => $deportista_id));

        // 4. Si se aprueba, activar usuario del deportista
        if ($estado === 'aprobado' || $estado === 'activo') {
            $persona_id_dep = $this->_obtener_valor(
                "SELECT persona_id FROM deportista WHERE id = ?",
                array($deportista_id)
            );
            if ($persona_id_dep > 0) {
                $this->_ejecutar(
                    "UPDATE usuario SET activo = 1 WHERE persona_id = ?",
                    array($persona_id_dep)
                );
            }
        }

        // 5. Bitacora
        $this->_historiar(3, 'Cambio estado afiliacion', $deportista_id, 'Nuevo estado: ' . $estado . ' - Obs: ' . $observaciones);

        $this->_success('Estado actualizado correctamente a: ' . $estado);
    }

    // 3.1 Revisar un documento individual (aprobar, rechazar o marcar en revision)
    function revisar_documento_gestion()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para revisar documentos');
            return;
        }

        if (isset($_POST['documento_id'])) {
            $documento_id = intval($_POST['documento_id']);
        } else {
            $documento_id = 0;
        }
        if (isset($_POST['estado'])) {
            $estado = trim($_POST['estado']);
        } else {
            $estado = '';
        }
        if (isset($_POST['observaciones'])) {
            $observaciones = trim($_POST['observaciones']);
        } else {
            $observaciones = '';
        }

        if ($documento_id <= 0) {
            $this->_error('ID de documento invalido');
            return;
        }

        // 1. Validar estado permitido para documentos
        $estados_validos = array('pendiente', 'en_revision', 'aprobado', 'rechazado');
        if (!in_array($estado, $estados_validos, true)) {
            $this->_error('Estado de documento no valido');
            return;
        }
        if ($estado === 'rechazado' && $observaciones === '') {
            $this->_error('Indique el motivo del rechazo para que el acudiente pueda corregirlo');
            return;
        }

        // 2. Verificar que el documento exista
        $doc = $this->_obtener_fila(
            "SELECT id, deportista_id FROM documento WHERE id = ?",
            array($documento_id)
        );
        if (empty($doc)) {
            $this->_error('Documento no encontrado');
            return;
        }

        // 3. Guardar revision
        $this->_ejecutar(
            "UPDATE documento SET estado = ?, observaciones = ?, revisado_por = ?, fecha_revision = ? WHERE id = ?",
            array($estado, $observaciones, $this->_obtener_usuario_id(), date('Y-m-d H:i:s'), $documento_id)
        );

        $this->_historiar(3, 'Revision documento afiliacion', $doc['deportista_id'], 'Documento: ' . $documento_id . ' - Estado: ' . $estado);
        $this->_success('Documento marcado como: ' . $estado);
    }

    // 4. Eliminar solicitud (cambiar estado a inactivo)
    function eliminar_gestion()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para eliminar solicitudes');
            return;
        }

        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
        } else {
            $id = 0;
        }

        if ($id <= 0) {
            $this->_error('ID invalido');
            return;
        }

        // 1. Obtener deportista_id
        $solicitud = $this->_obtener_fila(
            "SELECT deportista_id FROM deportista_acudiente WHERE id = ?",
            array($id)
        );
        if (empty($solicitud)) {
            $this->_error('Solicitud no encontrada');
            return;
        }
        $deportista_id = $solicitud['deportista_id'];

        // 2. Soft delete: cambiar estado a 'inactivo'
        $this->_actualizar('deportista', array('estado' => 'inactivo'), array('id' => $deportista_id));

        // 3. Desactivar usuario vinculado
        $persona_id_dep = $this->_obtener_valor(
            "SELECT persona_id FROM deportista WHERE id = ?",
            array($deportista_id)
        );
        if ($persona_id_dep > 0) {
            $this->_ejecutar(
                "UPDATE usuario SET activo = 0 WHERE persona_id = ?",
                array($persona_id_dep)
            );
        }

        $this->_historiar(2, 'Eliminar solicitud afiliacion', $deportista_id, 'Estado cambiado a inactivo');
        $this->_success('Solicitud desactivada correctamente');
    }

    // 5. Select acudientes para admin
    function listar_acudientes_admin()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $sql = "SELECT p.id, CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS nombre
                FROM persona p
                INNER JOIN usuario u ON u.persona_id = p.id
                WHERE u.rol_id = 3 AND u.activo = 1
                ORDER BY p.nombre1, p.apellido1";

        $acudientes = $this->_consultar($sql);
        $this->_success('', $acudientes);
    }

    // 6. Select deportistas para admin
    function listar_deportistas_admin()
    {
        $this->validar_token();

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $sql = "SELECT d.id, CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS nombre
                FROM deportista d
                INNER JOIN persona p ON d.persona_id = p.id
                ORDER BY p.nombre1, p.apellido1";

        $deportistas = $this->_consultar($sql);
        $this->_success('', $deportistas);
    }
}