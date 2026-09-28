<?php
// afiliacion/acciones.php - Backend del modulo de afiliacion (Patron Libre con Traits)
require_once __DIR__ . '/../../../php/clase_base.php';
require_once __DIR__ . '/clases/afiliacion_helpers.php';

class Formulario extends Base
{
    use afiliacion_helpers;

    // ============================================================
    // TAB 1: GESTION (Admin - Roles 1, 4)
    // ============================================================

    // Listar solicitudes para DataTable (server-side)
    function listar_gestion()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para acceder a esta seccion');
            return;
        }

        $params_dt = $this->_obtener_datatables_params();
        $start = $params_dt['start'];
        $length = $params_dt['length'];
        $search = $params_dt['search'];
        $draw = $params_dt['draw'];

        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

        // Consulta base
        $sql_base = "SELECT a.id,
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

        // Filtro por estado
        if ($estado !== '' && $estado !== 'NULL' && $estado !== null) {
            $sql_base .= " AND a.estado = ?";
            $params[] = $estado;
        }

        // Filtro por busqueda
        if ($search !== '') {
            $sql_base .= " AND (p1.nombre1 LIKE ? OR p1.apellido1 LIKE ? OR p2.nombre1 LIKE ? OR p2.apellido1 LIKE ?)";
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // Total registros (sin filtro de busqueda)
        $sql_total = str_replace(
            "SELECT a.id,\n                            CONCAT_WS(' ', p1.nombre1, p1.nombre2, p1.apellido1, p1.apellido2) AS acudiente_nombre,\n                            CONCAT_WS(' ', p2.nombre1, p2.nombre2, p2.apellido1, p2.apellido2) AS deportista_nombre,\n                            a.estado,\n                            a.porcentaje_completado,\n                            a.fecha_solicitud,\n                            a.fecha_aprobacion,\n                            a.pdf_ruta",
            "SELECT COUNT(*)",
            $sql_base
        );
        $recordsTotal = $this->_contar($sql_total, $params);

        // Total filtrados (con filtro de busqueda)
        $recordsFiltered = $recordsTotal;

        // Paginacion
        $sql_data = $sql_base . " ORDER BY a.fecha_solicitud DESC LIMIT ? OFFSET ?";
        $params_data = $params;
        $params_data[] = $length;
        $params_data[] = $start;

        $rows = $this->_consultar($sql_data, $params_data);

        // Formatear respuesta
        $data = array();
        $num = $start + 1;
        for ($i = 0; $i < count($rows); $i++) {
            $rw = $rows[$i];
            $rw['_NUM_'] = $num++;
            $rw['id'] = urlsafe_b64encode($rw['id']);
            $data[] = $rw;
        }

        $resultado = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        );

        echo json_encode($resultado);
    }

    // Obtener solicitud por ID (para ver detalle)
    function asignar_gestion()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos');
            return;
        }

        $id = isset($_GET['id']) ? urlsafe_b64decode($_GET['id']) : 0;
        $id = intval($id);

        if ($id <= 0) {
            $this->_error('ID invalido');
            return;
        }

        $solicitud = $this->_obtener_detalle_solicitud($id);

        if (empty($solicitud)) {
            $this->_error('Solicitud no encontrada');
            return;
        }

        $solicitud['id'] = urlsafe_b64encode($solicitud['id']);
        $this->_success('', $solicitud);
    }

    // Crear nueva solicitud
    function agregar_gestion()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para crear solicitudes');
            return;
        }

        $acudiente_id = isset($_POST['acudiente_id']) ? intval($_POST['acudiente_id']) : 0;
        $deportista_id = isset($_POST['deportista_id']) ? intval($_POST['deportista_id']) : 0;
        $estado = isset($_POST['estado']) ? $_POST['estado'] : 'borrador';

        if ($acudiente_id <= 0 || $deportista_id <= 0) {
            $this->_error('Debe seleccionar acudiente y deportista');
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

        $id = $this->_insertar('v_afiliacion', $datos);

        if ($id > 0) {
            $this->_historiar(1, 'Crear solicitud afiliacion', $id, 'Solicitud creada: ' . $id);
            $this->_success('Solicitud creada correctamente', array('id' => urlsafe_b64encode($id)));
        } else {
            $this->_error('Error al crear la solicitud');
        }
    }

    // Actualizar estado de solicitud
    function modificar_gestion()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para modificar solicitudes');
            return;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
        $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

        if ($id <= 0) {
            $this->_error('ID invalido');
            return;
        }

        if ($estado === '') {
            $this->_error('Debe seleccionar un estado');
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

        $this->_actualizar('v_afiliacion', $datos, array('id' => $id));

        $this->_historiar(3, 'Modificar solicitud afiliacion', $id, 'Estado cambiado a: ' . $estado);
        $this->_success('Solicitud actualizada correctamente');
    }

    // Eliminar solicitud
    function eliminar_gestion()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_admin()) {
            $this->_error('No tiene permisos para eliminar solicitudes');
            return;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $this->_error('ID invalido');
            return;
        }

        $this->db->query("DELETE FROM v_afiliacion WHERE id = ?", array($id));

        $this->_historiar(2, 'Eliminar solicitud afiliacion', $id, 'Solicitud eliminada');
        $this->_success('Solicitud eliminada');
    }

    // ============================================================
    // TAB 2: MIS AFILIACIONES (Acudiente - Rol 3)
    // ============================================================

    // Listar mis solicitudes
    function listar_mis_afiliaciones()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_acudiente()) {
            $this->_error('No tiene permisos para acceder a esta seccion');
            return;
        }

        $usuario_id = $this->_obtener_usuario_id();

        if ($usuario_id <= 0) {
            $this->_error('Usuario no identificado');
            return;
        }

        $estado = isset($_GET['estado']) ? $_GET['estado'] : '';

        $solicitudes = $this->_obtener_solicitudes_usuario($usuario_id, $estado);
        $total = $this->_contar_solicitudes_usuario($usuario_id, $estado);

        $rows = array();
        $num = 1;
        foreach ($solicitudes as $s) {
            $s['_NUM_'] = $num++;
            $s['id'] = urlsafe_b64encode($s['id']);
            $rows[] = $s;
        }

        echo json_encode(array('error' => false, 'total' => $total, 'rows' => $rows));
    }

    // Obtener detalle de una solicitud propia
    function obtener_mis_afiliaciones()
    {
        if (!$this->validar_token()) {
            return;
        }

        if (!$this->_es_acudiente()) {
            $this->_error('No tiene permisos');
            return;
        }

        $usuario_id = $this->_obtener_usuario_id();
        $solicitud_id = isset($_POST['solicitud_id']) ? intval($_POST['solicitud_id']) : 0;

        if ($solicitud_id <= 0) {
            $this->_error('ID de solicitud requerido');
            return;
        }

        if (!$this->_solicitud_pertenece_usuario($solicitud_id, $usuario_id)) {
            $this->_error('Solicitud no encontrada o no tiene acceso');
            return;
        }

        $solicitud = $this->_obtener_detalle_solicitud($solicitud_id);

        if (empty($solicitud)) {
            $this->_error('Solicitud no encontrada');
            return;
        }

        $this->_success('', $solicitud);
    }

    // Subir PDF para una solicitud
    function subir_pdf_mis_afiliaciones()
    {
        if (!$this->validar_token_simple()) {
            return;
        }

        if (!$this->_es_acudiente()) {
            $this->_error('No tiene permisos para subir documentos');
            return;
        }

        $usuario_id = $this->_obtener_usuario_id();

        if ($usuario_id <= 0) {
            $this->_error('Usuario no identificado');
            return;
        }

        $solicitud_id = isset($_POST['solicitud_id']) ? intval($_POST['solicitud_id']) : 0;

        if ($solicitud_id <= 0) {
            $this->_error('ID de solicitud requerido');
            return;
        }

        if (!$this->_solicitud_pertenece_usuario($solicitud_id, $usuario_id)) {
            $this->_error('Solicitud no encontrada o no tiene acceso');
            return;
        }

        // Validar archivo
        if (!isset($_FILES['pdf'])) {
            $this->_error('No se recibio ningun archivo');
            return;
        }

        $validacion = $this->_validar_pdf($_FILES['pdf']);
        if (!$validacion['ok']) {
            $this->_error($validacion['msg']);
            return;
        }

        // Directorio de subida
        $upload_dir = 'storage/afiliaciones/' . $usuario_id;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
        $archivo_nombre = uniqid() . '.' . $ext;
        $destino = $upload_dir . '/' . $archivo_nombre;

        if ($this->_mover_archivo($_FILES['pdf']['tmp_name'], $destino)) {
            // Actualizar la solicitud con la referencia del PDF
            $this->_actualizar('v_afiliacion', array('pdf_ruta' => $archivo_nombre), array('id' => $solicitud_id));

            $this->_historiar(1, 'Subir PDF afiliacion', $solicitud_id, 'PDF subido por acudiente: ' . $archivo_nombre);
            $this->_success('PDF subido correctamente', array('pdf_ruta' => $archivo_nombre));
        } else {
            $this->_error('Error al mover el archivo');
        }
    }

    // ============================================================
    // FUNCIONES COMPARTIDAS
    // ============================================================

    // Listar acudientes para select
    function listarAcudientes()
    {
        if (!$this->validar_token()) {
            return;
        }

        $sql = "SELECT ac.id, CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS nombre
                FROM v_acudiente ac
                INNER JOIN persona p ON ac.persona_id = p.id
                WHERE ac.activo = 1
                ORDER BY p.nombre1, p.apellido1";

        $acudientes = $this->_consultar($sql);
        $this->_success('', $acudientes);
    }

    // Listar deportistas para select
    function listarDeportistas()
    {
        if (!$this->validar_token()) {
            return;
        }

        $sql = "SELECT d.id, CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS nombre
                FROM v_deportista d
                INNER JOIN persona p ON d.persona_id = p.id
                WHERE d.estado = 'activo'
                ORDER BY p.nombre1, p.apellido1";

        $deportistas = $this->_consultar($sql);
        $this->_success('', $deportistas);
    }
}

$accion = ACCION;
$f = new Formulario();
$f->$accion();