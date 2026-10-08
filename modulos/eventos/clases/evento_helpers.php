<?php
// ============================================================
// EVENTOS — Helpers de logica de negocio y consultas
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con intval y escape_string (este db no usa ?)
//   - Cero emojis
//   - Sin LIMIT 1 manual en select_row o select_one
//   - Eliminacion suave (activo = 0)
// ============================================================

class EventosHelpers
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    // 1. Listar eventos con filtros opcionales (tipo, categoria)
    function listar_eventos($filtros = array())
    {
        $where_clauses = array();
        $where_clauses[] = "e.activo = 1";

        if (isset($filtros['tipo'])) {
            $tipo_filtro = trim($filtros['tipo']);
            if ($tipo_filtro !== '' && $tipo_filtro !== 'todos') {
                $tipo_esc = $this->db->escape_string($tipo_filtro);
                $where_clauses[] = "e.tipo = '" . $tipo_esc . "'";
            }
        }

        if (isset($filtros['categoria_id'])) {
            $cat_id = intval($filtros['categoria_id']);
            if ($cat_id > 0) {
                $where_clauses[] = "e.categoria_id = " . $cat_id;
            }
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "SELECT e.id,
                       e.nombre,
                       e.tipo,
                       e.fecha,
                       e.fecha_fin,
                       e.lugar,
                       e.categoria_id,
                       c.nombre AS categoria_nombre,
                       e.horario,
                       e.recomendaciones,
                       e.requiere_autorizacion,
                       e.activo,
                       e.creado_por,
                       e.fecha_creacion,
                       (SELECT COUNT(*) FROM evento_convocada ec WHERE ec.evento_id = e.id AND ec.activo = 1) AS total_convocadas,
                       (SELECT COUNT(*) FROM autorizacion_evento ae WHERE ae.evento_id = e.id AND ae.autoriza = 1) AS total_autorizadas
                FROM evento e
                LEFT JOIN categoria c ON c.id = e.categoria_id
                WHERE " . $where_sql . "
                ORDER BY e.fecha DESC, e.id DESC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }

    // 2. Obtener un evento por ID
    function obtener_evento($evento_id)
    {
        $id_limpio = intval($evento_id);
        if ($id_limpio <= 0) {
            return null;
        }

        $sql = "SELECT e.id,
                       e.nombre,
                       e.tipo,
                       e.fecha,
                       e.fecha_fin,
                       e.lugar,
                       e.categoria_id,
                       c.nombre AS categoria_nombre,
                       e.horario,
                       e.recomendaciones,
                       e.requiere_autorizacion,
                       e.activo,
                       e.creado_por,
                       e.fecha_creacion,
                       (SELECT COUNT(*) FROM evento_convocada ec WHERE ec.evento_id = e.id AND ec.activo = 1) AS total_convocadas,
                       (SELECT COUNT(*) FROM autorizacion_evento ae WHERE ae.evento_id = e.id AND ae.autoriza = 1) AS total_autorizadas
                FROM evento e
                LEFT JOIN categoria c ON c.id = e.categoria_id
                WHERE e.id = " . $id_limpio . " AND e.activo = 1";

        $evento = $this->db->select_row($sql);
        if (!is_array($evento) || !isset($evento['id'])) {
            return null;
        }
        return $evento;
    }

    // 3. Guardar evento (crear o actualizar)
    function guardar_evento($datos, $evento_id = 0, $persona_id = 0)
    {
        $id_limpio = intval($evento_id);

        $registro = array();

        if (isset($datos['nombre'])) {
            $registro['nombre'] = trim($datos['nombre']);
        } else {
            $registro['nombre'] = '';
        }

        if (isset($datos['tipo'])) {
            $registro['tipo'] = trim($datos['tipo']);
        } else {
            $registro['tipo'] = 'torneo';
        }

        if (isset($datos['fecha'])) {
            $registro['fecha'] = trim($datos['fecha']);
        } else {
            $registro['fecha'] = date('Y-m-d');
        }

        if (isset($datos['fecha_fin'])) {
            $fecha_fin_val = trim($datos['fecha_fin']);
            if ($fecha_fin_val !== '') {
                $registro['fecha_fin'] = $fecha_fin_val;
            } else {
                $registro['fecha_fin'] = null;
            }
        } else {
            $registro['fecha_fin'] = null;
        }

        if (isset($datos['lugar'])) {
            $registro['lugar'] = trim($datos['lugar']);
        } else {
            $registro['lugar'] = '';
        }

        if (isset($datos['categoria_id'])) {
            $cat_val = intval($datos['categoria_id']);
            if ($cat_val > 0) {
                $registro['categoria_id'] = $cat_val;
            } else {
                $registro['categoria_id'] = null;
            }
        } else {
            $registro['categoria_id'] = null;
        }

        if (isset($datos['horario'])) {
            $registro['horario'] = trim($datos['horario']);
        } else {
            $registro['horario'] = '';
        }

        if (isset($datos['recomendaciones'])) {
            $registro['recomendaciones'] = trim($datos['recomendaciones']);
        } else {
            $registro['recomendaciones'] = '';
        }

        if (isset($datos['requiere_autorizacion'])) {
            $req_val = intval($datos['requiere_autorizacion']);
            if ($req_val === 1) {
                $registro['requiere_autorizacion'] = 1;
            } else {
                $registro['requiere_autorizacion'] = 0;
            }
        } else {
            $registro['requiere_autorizacion'] = 0;
        }

        $registro['activo'] = 1;

        if ($id_limpio > 0) {
            // Actualizar
            $this->db->update('evento', $registro, array('id' => $id_limpio));
            insertar_bitacora(3, 'Actualizar evento', 'Evento id: ' . $id_limpio . ' - Nombre: ' . $registro['nombre']);
            return array('error' => false, 'msg' => 'Evento actualizado correctamente.', 'id' => $id_limpio);
        } else {
            // Crear
            if ($persona_id > 0) {
                $registro['creado_por'] = intval($persona_id);
            }
            $nuevo_id = $this->db->insert('evento', $registro);
            if ($nuevo_id && intval($nuevo_id) > 0) {
                insertar_bitacora(1, 'Crear evento', 'Evento id: ' . $nuevo_id . ' - Nombre: ' . $registro['nombre']);
                return array('error' => false, 'msg' => 'Evento creado correctamente.', 'id' => intval($nuevo_id));
            } else {
                return array('error' => true, 'msg' => 'No se pudo crear el evento.');
            }
        }
    }

    // 4. Eliminar evento (eliminacion suave)
    function eliminar_evento($evento_id)
    {
        $id_limpio = intval($evento_id);
        if ($id_limpio <= 0) {
            return array('error' => true, 'msg' => 'ID de evento no valido.');
        }

        $evento = $this->obtener_evento($id_limpio);
        if (!$evento) {
            return array('error' => true, 'msg' => 'El evento no existe o ya fue eliminado.');
        }

        $this->db->update('evento', array('activo' => 0), array('id' => $id_limpio));
        insertar_bitacora(2, 'Eliminar evento', 'Evento id: ' . $id_limpio . ' - Nombre: ' . $evento['nombre']);
        return array('error' => false, 'msg' => 'Evento eliminado correctamente.');
    }

    // 5. Listar categorias activas para combos
    function listar_categorias()
    {
        $sql = "SELECT id, nombre, slug, genero FROM categoria WHERE activo = 1 ORDER BY nombre ASC";
        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }

    // 6. Listar deportistas activos con estado de convocatoria para un evento
    function listar_deportistas_para_convocar($evento_id, $categoria_id = 0)
    {
        $id_evento = intval($evento_id);
        $id_cat = intval($categoria_id);

        $where_cat = "";
        if ($id_cat > 0) {
            $where_cat = " AND d.categoria_id = " . $id_cat;
        }

        $sql = "SELECT d.id AS deportista_id,
                       p.id AS persona_id,
                       CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre_completo,
                       p.identificacion,
                       p.genero,
                       d.categoria_id,
                       c.nombre AS categoria_nombre,
                       d.eps,
                       d.rh,
                       ec.id AS convocada_id,
                       ec.estado AS convocada_estado,
                       ec.observaciones AS convocada_observaciones,
                       ec.activo AS convocada_activa
                FROM deportista d
                INNER JOIN persona p ON p.id = d.persona_id
                LEFT JOIN categoria c ON c.id = d.categoria_id
                LEFT JOIN evento_convocada ec ON ec.deportista_id = d.id AND ec.evento_id = " . $id_evento . " AND ec.activo = 1
                WHERE d.estado IN ('aprobado', 'activo')" . $where_cat . "
                ORDER BY p.apellido1 ASC, p.nombre1 ASC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }

    // 7. Listar solo las deportistas convocadas a un evento
    function listar_convocadas_evento($evento_id)
    {
        $id_evento = intval($evento_id);
        if ($id_evento <= 0) {
            return array();
        }

        $sql = "SELECT ec.id AS convocada_id,
                       ec.evento_id,
                       ec.deportista_id,
                       ec.estado,
                       ec.observaciones,
                       ec.fecha_creacion,
                       CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre_completo,
                       p.identificacion,
                       p.genero,
                       p.celular,
                       d.categoria_id,
                       c.nombre AS categoria_nombre,
                       d.eps,
                       d.rh,
                       d.contacto_emergencia_nombre,
                       d.contacto_emergencia_telefono,
                       (SELECT ae.autoriza FROM autorizacion_evento ae WHERE ae.evento_id = ec.evento_id AND ae.deportista_id = ec.deportista_id ORDER BY ae.id DESC LIMIT 1) AS autorizacion_estado
                FROM evento_convocada ec
                INNER JOIN deportista d ON d.id = ec.deportista_id
                INNER JOIN persona p ON p.id = d.persona_id
                LEFT JOIN categoria c ON c.id = d.categoria_id
                WHERE ec.evento_id = " . $id_evento . " AND ec.activo = 1
                ORDER BY p.apellido1 ASC, p.nombre1 ASC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }

    // 8. Convocar deportistas masivamente a un evento
    function convocar_masivo($evento_id, $deportista_ids, $persona_id = 0)
    {
        $id_evento = intval($evento_id);
        if ($id_evento <= 0) {
            return array('error' => true, 'msg' => 'Evento no valido.');
        }

        $evento = $this->obtener_evento($id_evento);
        if (!$evento) {
            return array('error' => true, 'msg' => 'El evento no existe.');
        }

        if (!is_array($deportista_ids) || count($deportista_ids) === 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar al menos una deportista.');
        }

        $total_agregadas = 0;
        $total_reactivadas = 0;

        for ($i = 0; $i < count($deportista_ids); $i++) {
            $dep_id = intval($deportista_ids[$i]);
            if ($dep_id > 0) {
                // Verificar si ya existe en evento_convocada
                $sql_existe = "SELECT id, activo FROM evento_convocada WHERE evento_id = " . $id_evento . " AND deportista_id = " . $dep_id;
                $fila_existe = $this->db->select_row($sql_existe);

                if (is_array($fila_existe) && isset($fila_existe['id'])) {
                    $conv_id = intval($fila_existe['id']);
                    $conv_activa = intval($fila_existe['activo']);
                    if ($conv_activa === 0) {
                        $this->db->update('evento_convocada', array('activo' => 1, 'estado' => 'convocada'), array('id' => $conv_id));
                        $total_reactivadas = $total_reactivadas + 1;
                    }
                } else {
                    $nuevo_registro = array(
                        'evento_id' => $id_evento,
                        'deportista_id' => $dep_id,
                        'estado' => 'convocada',
                        'activo' => 1,
                        'creado_por' => ($persona_id > 0 ? intval($persona_id) : null)
                    );
                    $this->db->insert('evento_convocada', $nuevo_registro);
                    $total_agregadas = $total_agregadas + 1;
                }
            }
        }

        $suma_total = $total_agregadas + $total_reactivadas;
        insertar_bitacora(1, 'Convocar deportistas', 'Evento id: ' . $id_evento . ' - Total convocadas: ' . $suma_total);

        return array(
            'error' => false,
            'msg' => 'Se han convocado ' . $suma_total . ' deportistas correctamente.',
            'total' => $suma_total
        );
    }

    // 9. Quitar a una deportista de la convocatoria (eliminacion suave)
    function quitar_convocada($convocada_id)
    {
        $id_limpio = intval($convocada_id);
        if ($id_limpio <= 0) {
            return array('error' => true, 'msg' => 'ID de convocatoria no valido.');
        }

        $sql_check = "SELECT id, evento_id, deportista_id FROM evento_convocada WHERE id = " . $id_limpio . " AND activo = 1";
        $fila = $this->db->select_row($sql_check);
        if (!is_array($fila) || !isset($fila['id'])) {
            return array('error' => true, 'msg' => 'La convocatoria no existe o ya fue removida.');
        }

        $this->db->update('evento_convocada', array('activo' => 0), array('id' => $id_limpio));
        insertar_bitacora(2, 'Quitar deportista de convocatoria', 'Convocatoria id: ' . $id_limpio . ' - Evento: ' . $fila['evento_id']);
        return array('error' => false, 'msg' => 'Deportista removida de la convocatoria.');
    }

    // 10. Cambiar estado de una convocatoria (convocada, confirmada, rechazada, asistio, no_asistio)
    function cambiar_estado_convocada($convocada_id, $nuevo_estado, $observaciones = '')
    {
        $id_limpio = intval($convocada_id);
        if ($id_limpio <= 0) {
            return array('error' => true, 'msg' => 'ID no valido.');
        }

        $estados_permitidos = array('convocada', 'confirmada', 'rechazada', 'asistio', 'no_asistio');
        if (!in_array($nuevo_estado, $estados_permitidos)) {
            return array('error' => true, 'msg' => 'Estado no valido.');
        }

        $datos_update = array('estado' => $nuevo_estado);
        if ($observaciones !== '') {
            $datos_update['observaciones'] = trim($observaciones);
        }

        $this->db->update('evento_convocada', $datos_update, array('id' => $id_limpio));
        insertar_bitacora(3, 'Cambiar estado convocatoria', 'Convocatoria id: ' . $id_limpio . ' - Nuevo estado: ' . $nuevo_estado);
        return array('error' => false, 'msg' => 'Estado actualizado correctamente.');
    }
}
