<?php
// afiliacion_helpers.php - Utilidades comunes para el modulo Afiliacion
// Uso: use afiliacion_helpers; en la clase Formulario

trait afiliacion_helpers
{
    // ============================================================
    // VALIDACION DE TOKEN
    // ============================================================

    // 1. Validar token de autorizacion (para peticiones JSON normales)
    function validar_token()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->_error('Error en TOKEN');
            return false;
        }
        return true;
    }

    // 2. Validar token simple (para peticiones con FormData / archivos)
    function validar_token_simple()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->_error('Error en TOKEN');
            return false;
        }
        return true;
    }

    // ============================================================
    // SESION Y ROLES
    // ============================================================

    // 3. Obtener ID de usuario de la sesion (tabla usuario.id)
    function _obtener_usuario_id()
    {
        if (isset($_SESSION['usuario_id'])) {
            return intval($_SESSION['usuario_id']);
        }
        return 0;
    }

    // 4. Obtener persona_id del usuario en sesion (tabla persona.id)
    function _obtener_persona_id()
    {
        if (isset($_SESSION['persona_id'])) {
            return intval($_SESSION['persona_id']);
        }
        return 0;
    }

    // 5. Obtener rol numerico del usuario en sesion
    function _obtener_usuario_rol()
    {
        if (isset($_SESSION['usuario_rol'])) {
            return intval($_SESSION['usuario_rol']);
        }
        return 0;
    }

    // 6. Verificar si es admin (rol 1 o 4)
    function _es_admin()
    {
        $rol = $this->_obtener_usuario_rol();
        if ($rol === 1 || $rol === 4) {
            return true;
        }
        return false;
    }

    // 7. Verificar si es acudiente (rol 3)
    function _es_acudiente()
    {
        $rol = $this->_obtener_usuario_rol();
        if ($rol === 3) {
            return true;
        }
        return false;
    }

    // ============================================================
    // CONSULTAS SEGURAS (con marcadores ?)
    // ============================================================

    // 8. SELECT multiples filas con marcadores ?
    function _consultar($sql, $params = array())
    {
        $resultado = $this->db->getConnection()->select($sql, $params);
        return json_decode(json_encode($resultado), true);
    }

    // 9. SELECT una sola fila con marcadores ?
    function _obtener_fila($sql, $params = array())
    {
        $resultado = $this->db->getConnection()->select($sql, $params);
        if (!empty($resultado)) {
            $fila = json_decode(json_encode($resultado[0]), true);
            return $fila;
        }
        return array();
    }

    // 10. SELECT un solo valor con marcadores ?
    function _obtener_valor($sql, $params = array())
    {
        $resultado = $this->db->getConnection()->select($sql, $params);
        if (!empty($resultado)) {
            $fila = json_decode(json_encode($resultado[0]), true);
            $valores = array_values($fila);
            if (!empty($valores)) {
                return $valores[0];
            }
        }
        return '';
    }

    // 11. Contar filas con consulta segura
    function _contar($sql, $params = array())
    {
        $valor = $this->_obtener_valor($sql, $params);
        return intval($valor);
    }

    // 12. Ejecutar INSERT/UPDATE/DELETE con marcadores ?
    function _ejecutar($sql, $params = array())
    {
        return $this->db->getConnection()->statement($sql, $params);
    }

    // 13. Insertar con helper seguro (retorna ID)
    function _insertar($tabla, $datos)
    {
        return $this->db->insert($tabla, $datos);
    }

    // 14. Actualizar con helper seguro
    function _actualizar($tabla, $datos, $where)
    {
        return $this->db->update($tabla, $datos, $where);
    }

    // ============================================================
    // RESPUESTAS JSON
    // ============================================================

    // 15. Respuesta exitosa estandar
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

    // 16. Respuesta de error estandar
    function _error($msg)
    {
        echo json_encode(array('error' => true, 'msg' => $msg));
    }

    // ============================================================
    // BITACORA Y AUDITORIA
    // ============================================================

    // 17. Registrar en bitacora del sistema
    function _historiar($tipo, $descripcion, $id_registro = 0, $observacion = '')
    {
        if (function_exists('insertar_bitacora')) {
            insertar_bitacora($tipo, $descripcion, $observacion);
        }
    }

    // ============================================================
    // PARAMETROS DATATABLES
    // ============================================================

    // 18. Obtener parametros de DataTables server-side (POST)
    function _obtener_datatables_params()
    {
        if (isset($_POST['start'])) {
            $start = intval($_POST['start']);
        } else {
            $start = 0;
        }

        if (isset($_POST['length'])) {
            $length = intval($_POST['length']);
        } else {
            $length = 50;
        }

        if ($length > 200) {
            $length = 200;
        }

        if (isset($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
        } else {
            $search = '';
        }

        if (isset($_POST['draw'])) {
            $draw = intval($_POST['draw']);
        } else {
            $draw = 1;
        }

        return array(
            'start' => $start,
            'length' => $length,
            'search' => $search,
            'draw' => $draw
        );
    }

    // ============================================================
    // ARCHIVOS Y DOCUMENTOS
    // ============================================================

    // 19. Nombre seguro para archivos (quitar caracteres peligrosos)
    function _nombre_seguro($nombre)
    {
        $nombre = basename($nombre);
        $nombre = preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombre);
        return $nombre;
    }

    // 20. Validar archivo PDF
    function _validar_pdf($archivo, $max_mb = 10)
    {
        $max_bytes = $max_mb * 1024 * 1024;

        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return array('ok' => false, 'msg' => 'No se recibio ningun archivo');
        }

        if ($archivo['size'] > $max_bytes) {
            return array('ok' => false, 'msg' => 'El archivo excede el tamano maximo de ' . $max_mb . 'MB');
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

    // 21. Validar archivo imagen o PDF (para documentos generales)
    function _validar_documento($archivo, $max_mb = 10)
    {
        $max_bytes = $max_mb * 1024 * 1024;

        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return array('ok' => false, 'msg' => 'No se recibio ningun archivo');
        }

        if ($archivo['size'] > $max_bytes) {
            return array('ok' => false, 'msg' => 'El archivo excede el tamano maximo de ' . $max_mb . 'MB');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        $permitidos = array(
            'application/pdf',
            'application/x-pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        );
        if (!in_array($mime, $permitidos)) {
            return array('ok' => false, 'msg' => 'Tipo de archivo no permitido (PDF, JPG, PNG, GIF, WEBP)');
        }

        return array('ok' => true, 'mime' => $mime);
    }

    // 22. Mover archivo subido de forma segura
    function _mover_archivo($origen, $destino)
    {
        $directorio = dirname($destino);
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        if (is_uploaded_file($origen)) {
            return move_uploaded_file($origen, $destino);
        }
        return copy($origen, $destino);
    }

    // ============================================================
    // GENERACION DE USUARIO AUTOMATICO
    // ============================================================

    // 23. Generar usuario para deportista
    // Formato: primera letra nombre1 + apellido1 + ultimos 4 digitos documento
    // Ejemplo: Brayan Montanez 1072649849 = bmontanez9849
    function _generar_usuario_deportista($nombre1, $apellido1, $documento)
    {
        // 1. Primera letra del primer nombre en minuscula
        $nombre1 = trim($nombre1);
        if ($nombre1 === '') {
            $letra = 'x';
        } else {
            $letra = mb_strtolower(mb_substr($nombre1, 0, 1, 'UTF-8'), 'UTF-8');
        }

        // 2. Primer apellido completo en minuscula
        $apellido1 = trim($apellido1);
        $apellido_lower = mb_strtolower($apellido1, 'UTF-8');

        // 3. Ultimos 4 digitos del documento
        $documento = trim($documento);
        if (strlen($documento) >= 4) {
            $ultimos4 = substr($documento, -4);
        } else {
            $ultimos4 = $documento;
        }

        // 4. Armar usuario base
        $usuario_base = $letra . $apellido_lower . $ultimos4;

        // 5. Verificar que no exista en persona.user
        $usuario_final = $usuario_base;
        $contador = 1;
        $existe = $this->_obtener_valor(
            "SELECT COUNT(*) FROM persona WHERE LOWER(user) = LOWER(?)",
            array($usuario_final)
        );
        while (intval($existe) > 0) {
            $usuario_final = $usuario_base . $contador;
            $existe = $this->_obtener_valor(
                "SELECT COUNT(*) FROM persona WHERE LOWER(user) = LOWER(?)",
                array($usuario_final)
            );
            $contador = $contador + 1;
        }

        return $usuario_final;
    }

    // 24. Generar password hasheado (cedula del deportista)
    function _generar_password($documento)
    {
        if (function_exists('clave')) {
            return clave($documento);
        }
        return password_hash($documento, PASSWORD_BCRYPT, array('cost' => 10));
    }

    // ============================================================
    // FIRMA DEL ACUDIENTE (dato sensible: PNG sin URL directa)
    // ============================================================

    // 25. Guardar la imagen PNG de la firma en storage/firmas/
    // Devuelve array(ok, msg, ruta). La ruta es relativa: storage/firmas/xxx.png
    function guardar_firma_acudiente($persona_id_acudiente, $data_url)
    {
        $id_acudiente = intval($persona_id_acudiente);

        // 1. Validar prefijo del dataURL
        if (strpos($data_url, 'data:image/png;base64,') !== 0) {
            return array('ok' => false, 'msg' => 'La firma recibida no tiene un formato valido', 'ruta' => '');
        }

        // 2. Decodificar base64 en modo estricto
        $base64_limpio = substr($data_url, strlen('data:image/png;base64,'));
        $binario = base64_decode($base64_limpio, true);
        if ($binario === false) {
            return array('ok' => false, 'msg' => 'La firma recibida no se pudo decodificar', 'ruta' => '');
        }

        // 3. Limite de tamano: 500 KB
        if (strlen($binario) > 500 * 1024) {
            return array('ok' => false, 'msg' => 'La imagen de la firma excede el tamano permitido', 'ruta' => '');
        }

        // 4. Verificar magia PNG real (no confiar solo en el prefijo)
        $magia_png = "\x89PNG\r\n\x1a\n";
        if (substr($binario, 0, 8) !== $magia_png) {
            return array('ok' => false, 'msg' => 'La firma recibida no es una imagen PNG valida', 'ruta' => '');
        }

        // 5. Crear carpeta protegida si no existe
        $carpeta = 'storage/firmas/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // 6. Nombre unico construido en servidor (sin nada del POST)
        $nombre_archivo = 'firma_' . $id_acudiente . '_' . date('YmdHis') . '.png';
        $ruta_relativa = $carpeta . $nombre_archivo;

        $escritos = file_put_contents($ruta_relativa, $binario);
        if ($escritos === false) {
            return array('ok' => false, 'msg' => 'No se pudo guardar la imagen de la firma', 'ruta' => '');
        }

        return array('ok' => true, 'msg' => 'Firma guardada', 'ruta' => $ruta_relativa);
    }

    // 26. Firmar toda la cola obligatoria pendiente con la misma imagen
    // Una sola firma respalda todos los documentos aceptados en el envio.
    function firmar_cola_acudiente($persona_id_acudiente, $ruta_firma)
    {
        $id_acudiente = intval($persona_id_acudiente);

        // 1. Listar documentos obligatorios activos
        $docs = $this->_consultar(
            "SELECT id, version FROM tipo_autorizacion WHERE activo = 1 AND clase = 'obligatorio' ORDER BY orden ASC, id ASC",
            array()
        );
        if (empty($docs)) {
            return 0;
        }

        // 2. Datos de evidencia de auditoria
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agente = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agente = '';
        }
        if (isset($_SESSION['usuario'])) {
            $documento_usuario = $_SESSION['usuario'];
        } else {
            $documento_usuario = '';
        }
        if (isset($_SESSION['nombre_usuario_c'])) {
            $nombre_completo = $_SESSION['nombre_usuario_c'];
        } else if (isset($_SESSION['nombre_usuario'])) {
            $nombre_completo = $_SESSION['nombre_usuario'];
        } else {
            $nombre_completo = '';
        }
        $ip_cliente = verIP();
        $fecha_firma = date('Y-m-d H:i:s');

        // 3. Por cada documento: crear o actualizar la firma aceptada
        $firmados = 0;
        for ($i = 0; $i < count($docs); $i++) {
            $tipo_id = intval($docs[$i]['id']);
            $version_doc = $docs[$i]['version'];

            $existe = $this->_obtener_fila(
                "SELECT id, aceptada FROM autorizacion_firmada WHERE acudiente_id = ? AND tipo_autorizacion_id = ? AND version_firmada = ?",
                array($id_acudiente, $tipo_id, $version_doc)
            );

            $hash_evidencia = hash('sha256', $id_acudiente . '|' . $documento_usuario . '|' . $version_doc . '|' . $fecha_firma . '|' . $ip_cliente . '|ACEPTADA');
            $evidencia = array(
                'ip' => $ip_cliente,
                'user_agent' => $agente,
                'fecha_hora' => $fecha_firma,
                'persona_id' => $id_acudiente,
                'documento' => $documento_usuario,
                'nombre_completo' => $nombre_completo,
                'version_documento' => $version_doc,
                'estado' => 'ACEPTADA',
                'hash_evidencia' => $hash_evidencia
            );
            $firma_json = json_encode($evidencia, JSON_UNESCAPED_UNICODE);

            if (!empty($existe)) {
                if (isset($existe['id'])) {
                    $this->_actualizar('autorizacion_firmada', array(
                        'aceptada' => 1,
                        'fecha_firma' => $fecha_firma,
                        'firma_electronica' => $firma_json,
                        'firma_imagen' => $ruta_firma
                    ), array('id' => intval($existe['id'])));
                    $firmados = $firmados + 1;
                }
            } else {
                $this->_insertar('autorizacion_firmada', array(
                    'deportista_id' => null,
                    'acudiente_id' => $id_acudiente,
                    'tipo_autorizacion_id' => $tipo_id,
                    'fecha_firma' => $fecha_firma,
                    'firma_electronica' => $firma_json,
                    'firma_imagen' => $ruta_firma,
                    'aceptada' => 1,
                    'version_firmada' => $version_doc
                ));
                $firmados = $firmados + 1;
            }
        }

        return $firmados;
    }
}