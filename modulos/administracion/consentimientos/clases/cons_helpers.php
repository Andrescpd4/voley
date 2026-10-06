<?php
// ============================================================
// CONSENTIMIENTOS — Helpers y consultas seguras
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con marcadores ?
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

    // 1. Listar todos los tipos de consentimiento activos
    function listar_tipos()
    {
        $sql = "SELECT id, nombre, slug, contenido, version, activo, clase, resumen, archivo_url, orden, mostrar_popup
                FROM tipo_autorizacion
                WHERE activo = 1
                ORDER BY orden ASC, id ASC";
        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            return array();
        }
        return $filas;
    }

    // 2. Obtener un tipo por ID
    function obtener_tipo($tipo_id)
    {
        $id_limpio = intval($tipo_id);
        $sql = "SELECT id, nombre, slug, contenido, version, activo, clase, resumen, archivo_url, orden, mostrar_popup
                FROM tipo_autorizacion
                WHERE id = ? AND activo = 1";
        $fila = $this->db->select_row($sql, array($id_limpio));
        if (!is_array($fila)) {
            return array();
        }
        return $fila;
    }

    // 3. Obtener un tipo por slug
    function obtener_tipo_por_slug($slug)
    {
        $slug_limpio = $this->db->escape_string($slug);
        $sql = "SELECT id, nombre, slug, contenido, version, activo, clase, resumen, archivo_url, orden, mostrar_popup
                FROM tipo_autorizacion
                WHERE slug = ? AND activo = 1";
        $fila = $this->db->select_row($sql, array($slug_limpio));
        if (!is_array($fila)) {
            return array();
        }
        return $fila;
    }

    // 4. Crear nuevo tipo de consentimiento
    function crear_tipo($datos)
    {
        $insertar = array(
            'nombre' => $datos['nombre'],
            'slug' => $datos['slug'],
            'contenido' => $datos['contenido'],
            'version' => $datos['version'],
            'activo' => isset($datos['activo']) ? intval($datos['activo']) : 1,
            'clase' => $datos['clase'],
            'resumen' => $datos['resumen'],
            'archivo_url' => $datos['archivo_url'],
            'orden' => intval($datos['orden']),
            'mostrar_popup' => isset($datos['mostrar_popup']) ? intval($datos['mostrar_popup']) : 1
        );
        $nuevo_id = $this->db->insert('tipo_autorizacion', $insertar);
        return $nuevo_id;
    }

    // 5. Actualizar tipo de consentimiento
    function actualizar_tipo($tipo_id, $datos)
    {
        $id_limpio = intval($tipo_id);
        $actualizar = array(
            'nombre' => $datos['nombre'],
            'slug' => $datos['slug'],
            'contenido' => $datos['contenido'],
            'version' => $datos['version'],
            'activo' => isset($datos['activo']) ? intval($datos['activo']) : 1,
            'clase' => $datos['clase'],
            'resumen' => $datos['resumen'],
            'archivo_url' => $datos['archivo_url'],
            'orden' => intval($datos['orden']),
            'mostrar_popup' => isset($datos['mostrar_popup']) ? intval($datos['mostrar_popup']) : 1
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

    // 7. Listar firmas con filtros (para historial)
    function listar_firmas($filtros = array())
    {
        $where = array();
        $params = array();

        if (isset($filtros['tipo_id']) && $filtros['tipo_id'] > 0) {
            $where[] = "af.tipo_autorizacion_id = ?";
            $params[] = intval($filtros['tipo_id']);
        }

        if (isset($filtros['persona_id']) && $filtros['persona_id'] > 0) {
            $where[] = "af.acudiente_id = ?";
            $params[] = intval($filtros['persona_id']);
        }

        if (isset($filtros['aceptada']) && $filtros['aceptada'] !== '') {
            $where[] = "af.aceptada = ?";
            $params[] = intval($filtros['aceptada']);
        }

        if (isset($filtros['fecha_desde']) && $filtros['fecha_desde'] !== '') {
            $where[] = "af.fecha_firma >= ?";
            $params[] = $filtros['fecha_desde'];
        }

        if (isset($filtros['fecha_hasta']) && $filtros['fecha_hasta'] !== '') {
            $where[] = "af.fecha_firma <= ?";
            $params[] = $filtros['fecha_hasta'];
        }

        $where_sql = '';
        if (count($where) > 0) {
            $where_sql = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT
                    af.id,
                    af.fecha_firma,
                    af.aceptada,
                    af.version_firmada,
                    af.firma_electronica,
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
                $where_sql
                ORDER BY af.fecha_firma DESC";

        $filas = $this->db->select_all($sql, $params);
        if (!is_array($filas)) {
            return array();
        }
        return $filas;
    }

    // 8. Verificar pendientes de una persona (cola ordenada)
    function verificar_pendientes($persona_id)
    {
        $id_limpio = intval($persona_id);

        $sql = "SELECT ta.id, ta.nombre, ta.slug, ta.version, ta.clase, ta.resumen, ta.archivo_url, ta.mostrar_popup
                FROM tipo_autorizacion ta
                WHERE ta.activo = 1 AND ta.mostrar_popup = 1
                ORDER BY
                    CASE WHEN ta.clase = 'obligatorio' THEN 0 ELSE 1 END,
                    ta.orden ASC";

        $tipos = $this->db->select_all($sql);
        if (!is_array($tipos)) {
            return array();
        }

        $pendientes = array();
        foreach ($tipos as $ta) {
            $tipo_id = intval($ta['id']);
            $version_vigente = $ta['version'];

            $sql_firma = "SELECT id, aceptada, fecha_firma FROM autorizacion_firmada
                          WHERE acudiente_id = ? AND tipo_autorizacion_id = ? AND version_firmada = ?
                          LIMIT 1";
            $firma = $this->db->select_row($sql_firma, array($id_limpio, $tipo_id, $version_vigente));

            $tiene_firma_vigente = false;
            $firma_aceptada = false;
            if (is_array($firma) && isset($firma['id'])) {
                $tiene_firma_vigente = true;
                $firma_aceptada = intval($firma['aceptada']) === 1;
            }

            if (!$tiene_firma_vigente || !$firma_aceptada) {
                $pendientes[] = array(
                    'tipo_id' => $tipo_id,
                    'nombre' => $ta['nombre'],
                    'slug' => $ta['slug'],
                    'version' => $version_vigente,
                    'clase' => $ta['clase'],
                    'resumen' => $ta['resumen'],
                    'archivo_url' => $ta['archivo_url'],
                    'url_lectura' => WEB_ROOT . 'politica-privacidad'
                );
            }
        }

        return $pendientes;
    }

    // 9. Guardar consentimiento (genérico para aceptar/rechazar)
    function guardar_consentimiento($persona_id, $tipo_id, $aceptada)
    {
        $id_persona = intval($persona_id);
        $id_tipo = intval($tipo_id);
        $aceptada_int = intval($aceptada);

        // Obtener tipo vigente
        $tipo = $this->obtener_tipo($id_tipo);
        if (empty($tipo)) {
            return array('error' => true, 'msg' => 'Tipo de consentimiento no encontrado.');
        }

        $version_vigente = $tipo['version'];

        // Evidencia
        $ip = verIP();
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $doc = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';
        $nom = isset($_SESSION['nombre_usuario_c']) ? $_SESSION['nombre_usuario_c'] : (isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : '');
        $fecha = date('Y-m-d H:i:s');
        $estado_str = $aceptada_int === 1 ? 'ACEPTADA' : 'RECHAZADA';
        $hash = hash('sha256', $id_persona . '|' . $doc . '|' . $version_vigente . '|' . $fecha . '|' . $ip . '|' . $estado_str);

        $evidencia = array(
            'ip' => $ip,
            'user_agent' => $ua,
            'fecha_hora' => $fecha,
            'persona_id' => $id_persona,
            'documento' => $doc,
            'nombre_completo' => $nom,
            'version_documento' => $version_vigente,
            'estado' => $estado_str,
            'hash_evidencia' => $hash
        );
        $firma_json = json_encode($evidencia, JSON_UNESCAPED_UNICODE);

        // Verificar si existe registro para esta persona, tipo y versión
        $sql_existe = "SELECT id FROM autorizacion_firmada
                       WHERE acudiente_id = ? AND tipo_autorizacion_id = ? AND version_firmada = ?";
        $existe = $this->db->select_row($sql_existe, array($id_persona, $id_tipo, $version_vigente));

        if (is_array($existe) && isset($existe['id'])) {
            $this->db->update('autorizacion_firmada', array(
                'aceptada' => $aceptada_int,
                'fecha_firma' => $fecha,
                'firma_electronica' => $firma_json
            ), array('id' => intval($existe['id'])));
        } else {
            $this->db->insert('autorizacion_firmada', array(
                'deportista_id' => null,
                'acudiente_id' => $id_persona,
                'tipo_autorizacion_id' => $id_tipo,
                'fecha_firma' => $fecha,
                'firma_electronica' => $firma_json,
                'aceptada' => $aceptada_int,
                'version_firmada' => $version_vigente
            ));
        }

        return array('error' => false, 'msg' => 'Consentimiento registrado correctamente.');
    }

    // 10. Subir archivo adjunto (PDF) a storage/autorizaciones/
    function subir_archivo($elemento, $carpeta = 'autorizaciones')
    {
        return $this->db->SubirArchivo($elemento, 10, 'pdf', $carpeta);
    }
}