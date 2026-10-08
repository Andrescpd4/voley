<?php
// ============================================================
// CONSENTIMIENTOS — Helpers y consultas seguras
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con intval y escape_string (este db no usa ?)
//   - select_row ya trae LIMIT 1: prohibido agregarlo a mano
//   - Cero emojis
//   - Funciones cortas, una sola cosa
// ============================================================

class ConsentimientosHelpers
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    // 1. Listar todos los tipos de consentimiento (activos e inactivos para admin)
    function listar_tipos()
    {
        $sql = "SELECT id, nombre, slug, contenido, version, activo, clase, resumen, archivo_url, orden, mostrar_popup
                FROM tipo_autorizacion
                ORDER BY orden ASC, id ASC";
        $filas = $this->db->select_all($sql);
        if (is_array($filas)) {
            return $filas;
        } else {
            return array();
        }
    }

    // 2. Obtener un tipo por ID
    function obtener_tipo($tipo_id)
    {
        $id_limpio = intval($tipo_id);
        $sql = "SELECT id, nombre, slug, contenido, version, activo, clase, resumen, archivo_url, orden, mostrar_popup
                FROM tipo_autorizacion
                WHERE id = " . $id_limpio;
        $fila = $this->db->select_row($sql);
        if (is_array($fila)) {
            return $fila;
        } else {
            return array();
        }
    }

    // 3. Obtener un tipo por slug
    function obtener_tipo_por_slug($slug)
    {
        $slug_limpio = $this->db->escape_string($slug);
        $sql = "SELECT id, nombre, slug, contenido, version, activo, clase, resumen, archivo_url, orden, mostrar_popup
                FROM tipo_autorizacion
                WHERE slug = '" . $slug_limpio . "' AND activo = 1";
        $fila = $this->db->select_row($sql);
        if (is_array($fila)) {
            return $fila;
        } else {
            return array();
        }
    }

    // 4. Crear nuevo tipo de consentimiento
    function crear_tipo($datos)
    {
        if (isset($datos['activo'])) {
            $activo_int = intval($datos['activo']);
        } else {
            $activo_int = 1;
        }
        if (isset($datos['mostrar_popup'])) {
            $popup_int = intval($datos['mostrar_popup']);
        } else {
            $popup_int = 1;
        }
        $insertar = array(
            'nombre' => $datos['nombre'],
            'slug' => $datos['slug'],
            'contenido' => $datos['contenido'],
            'version' => $datos['version'],
            'activo' => $activo_int,
            'clase' => $datos['clase'],
            'resumen' => $datos['resumen'],
            'archivo_url' => $datos['archivo_url'],
            'orden' => intval($datos['orden']),
            'mostrar_popup' => $popup_int
        );
        $nuevo_id = $this->db->insert('tipo_autorizacion', $insertar);
        return $nuevo_id;
    }

    // 5. Actualizar tipo de consentimiento
    function actualizar_tipo($tipo_id, $datos)
    {
        $id_limpio = intval($tipo_id);
        if (isset($datos['activo'])) {
            $activo_int = intval($datos['activo']);
        } else {
            $activo_int = 1;
        }
        if (isset($datos['mostrar_popup'])) {
            $popup_int = intval($datos['mostrar_popup']);
        } else {
            $popup_int = 1;
        }
        $actualizar = array(
            'nombre' => $datos['nombre'],
            'slug' => $datos['slug'],
            'contenido' => $datos['contenido'],
            'version' => $datos['version'],
            'activo' => $activo_int,
            'clase' => $datos['clase'],
            'resumen' => $datos['resumen'],
            'archivo_url' => $datos['archivo_url'],
            'orden' => intval($datos['orden']),
            'mostrar_popup' => $popup_int
        );
        $this->db->update('tipo_autorizacion', $actualizar, array('id' => $id_limpio));
        return true;
    }

    // 6. Eliminar (soft delete: activo=0)
    function eliminar_tipo($tipo_id)
    {
        $id_limpio = intval($tipo_id);
        $this->db->update('tipo_autorizacion', array('activo' => 0), array('id' => $id_limpio));
        return true;
    }

    // 6b. Incrementar la versión de un documento para solicitar firma de nuevo
    // Calcula la siguiente versión (ej: 1.0 -> 1.1, 1.9 -> 2.0) y la guarda.
    // Al cambiar la versión, todos los usuarios quedan pendientes automáticamente.
    function incrementar_version($tipo_id)
    {
        $id_limpio = intval($tipo_id);
        $tipo = $this->obtener_tipo($id_limpio);
        if (empty($tipo)) {
            return array('error' => true, 'msg' => 'Documento no encontrado');
        }

        $version_actual = trim($tipo['version']);
        $nueva_version = $this->_calcular_siguiente_version($version_actual);

        $this->db->update('tipo_autorizacion', array(
            'version' => $nueva_version
        ), array('id' => $id_limpio));

        return array(
            'error' => false,
            'msg' => 'Se incrementó la versión a ' . $nueva_version . '. Se pedirá la firma a todos los usuarios.',
            'version_anterior' => $version_actual,
            'nueva_version' => $nueva_version
        );
    }

    // Calcular siguiente versión con formato mayor.menor
    function _calcular_siguiente_version($version)
    {
        $partes = explode('.', $version);
        if (count($partes) === 2) {
            $mayor = intval($partes[0]);
            $menor = intval($partes[1]);
            $menor = $menor + 1;
            if ($menor >= 10) {
                $mayor = $mayor + 1;
                $menor = 0;
            }
            return $mayor . '.' . $menor;
        } else if (count($partes) === 1) {
            $mayor = intval($partes[0]);
            return $mayor . '.1';
        } else {
            return $version . '.1';
        }
    }

    // 7. Armar el WHERE de firmas desde filtros (devuelve texto ya escapado)
    function armar_where_firmas($filtros)
    {
        $partes = array();

        if (isset($filtros['tipo_id'])) {
            if (intval($filtros['tipo_id']) > 0) {
                $partes[] = "af.tipo_autorizacion_id = " . intval($filtros['tipo_id']);
            }
        }

        if (isset($filtros['persona_id'])) {
            if (intval($filtros['persona_id']) !== 0) {
                $partes[] = "af.acudiente_id = " . intval($filtros['persona_id']);
            }
        }

        if (isset($filtros['aceptada'])) {
            if ($filtros['aceptada'] !== '') {
                $partes[] = "af.aceptada = " . intval($filtros['aceptada']);
            }
        }

        if (isset($filtros['fecha_desde'])) {
            if ($filtros['fecha_desde'] !== '') {
                $fecha_desde = $this->db->escape_string($filtros['fecha_desde']);
                $partes[] = "af.fecha_firma >= '" . $fecha_desde . "'";
            }
        }

        if (isset($filtros['fecha_hasta'])) {
            if ($filtros['fecha_hasta'] !== '') {
                $fecha_hasta = $this->db->escape_string($filtros['fecha_hasta']);
                $partes[] = "af.fecha_firma <= '" . $fecha_hasta . "'";
            }
        }

        if (count($partes) > 0) {
            return 'WHERE ' . implode(' AND ', $partes);
        } else {
            return '';
        }
    }

    // 8. Listar firmas con filtros (para historial)
    function listar_firmas($filtros = array())
    {
        $where_sql = $this->armar_where_firmas($filtros);

        $sql = "SELECT
                    af.id,
                    af.fecha_firma,
                    af.aceptada,
                    af.version_firmada,
                    af.firma_imagen,
                    ta.nombre AS tipo_nombre,
                    ta.slug AS tipo_slug,
                    ta.clase AS tipo_clase,
                    CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS persona_nombre,
                    p.identificacion AS persona_documento,
                    p.correo AS persona_correo,
                    r.nombre AS rol_nombre
                FROM autorizacion_firmada af
                INNER JOIN tipo_autorizacion ta ON ta.id = af.tipo_autorizacion_id
                INNER JOIN persona p ON p.id = af.acudiente_id
                LEFT JOIN admin_usuario au ON au.persona_id = p.id
                LEFT JOIN admin_rol r ON r.id = au.rol
                " . $where_sql . "
                ORDER BY af.fecha_firma DESC";

        $filas = $this->db->select_all($sql);
        if (is_array($filas)) {
            return $filas;
        } else {
            return array();
        }
    }

    // 9. Verificar si un tipo ya tiene firma aceptada vigente
    function tiene_firma_vigente($persona_id, $tipo_id, $version_vigente)
    {
        $id_persona = intval($persona_id);
        $id_tipo = intval($tipo_id);
        $version_limpia = $this->db->escape_string($version_vigente);

        $sql_firma = "SELECT id, aceptada, fecha_firma FROM autorizacion_firmada
                      WHERE acudiente_id = " . $id_persona . " AND tipo_autorizacion_id = " . $id_tipo . " AND version_firmada = '" . $version_limpia . "' AND aceptada = 1";
        $firma = $this->db->select_row($sql_firma);

        if (is_array($firma)) {
            if (isset($firma['id'])) {
                return true;
            }
        }
        return false;
    }

    // 10. Verificar pendientes de una persona (cola ordenada: obligatorios primero)
    function verificar_pendientes($persona_id)
    {
        $id_limpio = intval($persona_id);

        $sql = "SELECT id, nombre, slug, contenido, version, clase, resumen, archivo_url, mostrar_popup
                FROM tipo_autorizacion
                WHERE activo = 1 AND clase = 'obligatorio'
                ORDER BY orden ASC, id ASC";

        $tipos = $this->db->select_all($sql);
        if (!is_array($tipos)) {
            return array();
        }

        $pendientes = array();
        for ($i = 0; $i < count($tipos); $i++) {
            $ta = $tipos[$i];
            $tipo_id = intval($ta['id']);
            $version_vigente = $ta['version'];

            if (!$this->tiene_firma_vigente($id_limpio, $tipo_id, $version_vigente)) {
                if (isset($ta['resumen'])) {
                    $resumen_txt = $ta['resumen'];
                } else {
                    $resumen_txt = '';
                }
                if (isset($ta['archivo_url'])) {
                    $archivo_txt = $ta['archivo_url'];
                } else {
                    $archivo_txt = '';
                }
                $pendientes[] = array(
                    'tipo_id' => $tipo_id,
                    'nombre' => $ta['nombre'],
                    'slug' => $ta['slug'],
                    'contenido' => $ta['contenido'],
                    'version' => $version_vigente,
                    'clase' => $ta['clase'],
                    'resumen' => $resumen_txt,
                    'archivo_url' => $archivo_txt,
                    'mostrar_popup' => intval($ta['mostrar_popup'])
                );
            }
        }

        return $pendientes;
    }

    // 11. Guardar consentimiento (generico para aceptar/rechazar)
    function guardar_consentimiento($persona_id, $tipo_id, $aceptada)
    {
        $id_persona = intval($persona_id);
        $id_tipo = intval($tipo_id);
        $aceptada_int = intval($aceptada);

        // 1. Obtener tipo vigente
        $tipo = $this->obtener_tipo($id_tipo);
        if (empty($tipo)) {
            return array('error' => true, 'msg' => 'Tipo de consentimiento no encontrado.');
        }

        $version_vigente = $tipo['version'];

        // 2. Armar evidencia de auditoria
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agente_navegador = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agente_navegador = '';
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
        if ($aceptada_int === 1) {
            $estado_str = 'ACEPTADA';
        } else {
            $estado_str = 'RECHAZADA';
        }
        $hash_evidencia = hash('sha256', $id_persona . '|' . $documento_usuario . '|' . $version_vigente . '|' . $fecha_firma . '|' . $ip_cliente . '|' . $estado_str);

        $evidencia_array = array(
            'ip' => $ip_cliente,
            'user_agent' => $agente_navegador,
            'fecha_hora' => $fecha_firma,
            'persona_id' => $id_persona,
            'documento' => $documento_usuario,
            'nombre_completo' => $nombre_completo,
            'version_documento' => $version_vigente,
            'estado' => $estado_str,
            'hash_evidencia' => $hash_evidencia
        );

        $firma_json = json_encode($evidencia_array, JSON_UNESCAPED_UNICODE);

        // 3. Verificar si existe registro para esta persona, tipo y version
        $version_limpia = $this->db->escape_string($version_vigente);
        $sql_existe = "SELECT id FROM autorizacion_firmada
                       WHERE acudiente_id = " . $id_persona . " AND tipo_autorizacion_id = " . $id_tipo . " AND version_firmada = '" . $version_limpia . "'";
        $existe = $this->db->select_row($sql_existe);

        if (is_array($existe)) {
            if (isset($existe['id'])) {
                $this->db->update('autorizacion_firmada', array(
                    'aceptada' => $aceptada_int,
                    'fecha_firma' => $fecha_firma,
                    'firma_electronica' => $firma_json
                ), array('id' => intval($existe['id'])));
            } else {
                $this->db->insert('autorizacion_firmada', array(
                    'deportista_id' => null,
                    'acudiente_id' => $id_persona,
                    'tipo_autorizacion_id' => $id_tipo,
                    'fecha_firma' => $fecha_firma,
                    'firma_electronica' => $firma_json,
                    'aceptada' => $aceptada_int,
                    'version_firmada' => $version_vigente
                ));
            }
        } else {
            $this->db->insert('autorizacion_firmada', array(
                'deportista_id' => null,
                'acudiente_id' => $id_persona,
                'tipo_autorizacion_id' => $id_tipo,
                'fecha_firma' => $fecha_firma,
                'firma_electronica' => $firma_json,
                'aceptada' => $aceptada_int,
                'version_firmada' => $version_vigente
            ));
        }

        return array('error' => false, 'msg' => 'Consentimiento registrado correctamente.');
    }

    // 12. Subir archivo adjunto (PDF) a storage/autorizaciones/ de forma segura
    // Valida extension, MIME real finfo y limite de 10 MB
    function subir_archivo($elemento)
    {
        if (!isset($_FILES[$elemento])) {
            return array('error' => true, 'msg' => 'No se recibió ningún archivo');
        }
        $archivo = $_FILES[$elemento];

        if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return array('error' => true, 'msg' => 'Error al subir el archivo (código ' . $archivo['error'] . ')');
        }

        // Límite de 10 MB
        $max_bytes = 10 * 1024 * 1024;
        if ($archivo['size'] > $max_bytes) {
            return array('error' => true, 'msg' => 'El archivo excede el tamaño máximo permitido de 10 MB');
        }

        // Validar extensión
        $nombre_original = $archivo['name'];
        $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            return array('error' => true, 'msg' => 'Solo se permiten archivos en formato PDF');
        }

        // Validar MIME real con finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_real = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        $mimes_validos = array('application/pdf', 'application/x-pdf');
        if (!in_array($mime_real, $mimes_validos)) {
            return array('error' => true, 'msg' => 'El contenido del archivo no corresponde a un PDF válido');
        }

        // Crear carpeta destino si no existe
        $carpeta_destino = 'storage/autorizaciones/';
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0755, true);
        }

        // Nombre único generado en el servidor
        $nombre_unico = 'doc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $ruta_final = $carpeta_destino . $nombre_unico;

        if (is_uploaded_file($archivo['tmp_name'])) {
            $movido = move_uploaded_file($archivo['tmp_name'], $ruta_final);
        } else {
            $movido = copy($archivo['tmp_name'], $ruta_final);
        }

        if (!$movido) {
            return array('error' => true, 'msg' => 'No se pudo guardar el archivo en el servidor');
        }

        return array(
            'error' => false,
            'msg' => 'Archivo subido correctamente',
            'nombre' => $nombre_unico,
            'url' => WEB_ROOT . 'storage/autorizaciones/' . $nombre_unico
        );
    }
}
