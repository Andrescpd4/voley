<?php
// ============================================================
// ASISTENCIA — Helpers de logica de negocio y consultas
//
// Reglas AGENTS.md (§10):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con intval y escape_string (este db no usa ?)
//   - Cero emojis
//   - Sin LIMIT manual en select_row o select_one
//   - Eliminacion suave en clase (activo = 0)
// ============================================================

class AsistenciaHelpers
{
    private $db;

    // Minutos de tolerancia antes de marcar llegada tarde
    private $tolerancia_tarde = 10;

    function __construct($db)
    {
        $this->db = $db;
    }

    // 1. Listar categorias activas para los combos
    function listar_categorias()
    {
        $sql = "SELECT id, nombre FROM categoria WHERE activo = 1 ORDER BY nombre ASC";
        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }

    // 2. Listar clases con conteos de presentes
    // Si el rol es entrenador (2), solo ve sus propias clases.
    function listar_clases($rol_sesion, $persona_sesion)
    {
        $rol_num = intval($rol_sesion);
        $persona_num = intval($persona_sesion);

        $where_extra = "";
        if ($rol_num === 2) {
            $where_extra = " AND cl.entrenador_id = " . $persona_num;
        }

        $sql = "SELECT cl.id,
                       cl.nombre,
                       cl.categoria_id,
                       cat.nombre AS categoria_nombre,
                       cl.entrenador_id,
                       CONCAT_WS(' ', pe.nombre1, pe.apellido1) AS entrenador_nombre,
                       cl.fecha,
                       cl.hora_inicio,
                       cl.hora_fin,
                       cl.lugar,
                       cl.estado,
                       (SELECT COUNT(*) FROM asistencia a WHERE a.clase_id = cl.id AND a.estado IN ('asistio', 'tarde')) AS total_presentes,
                       (SELECT COUNT(*) FROM asistencia a WHERE a.clase_id = cl.id) AS total_registros
                FROM clase cl
                LEFT JOIN categoria cat ON cat.id = cl.categoria_id
                LEFT JOIN persona pe ON pe.id = cl.entrenador_id
                WHERE cl.activo = 1" . $where_extra . "
                ORDER BY cl.fecha DESC, cl.hora_inicio DESC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }

    // 3. Obtener una clase abierta por ID (solo activas)
    function obtener_clase($clase_id)
    {
        $id_limpio = intval($clase_id);
        if ($id_limpio <= 0) {
            return null;
        }

        $sql = "SELECT cl.id,
                       cl.nombre,
                       cl.categoria_id,
                       cat.nombre AS categoria_nombre,
                       cl.entrenador_id,
                       cl.fecha,
                       cl.hora_inicio,
                       cl.hora_fin,
                       cl.lugar,
                       cl.estado
                FROM clase cl
                LEFT JOIN categoria cat ON cat.id = cl.categoria_id
                WHERE cl.id = " . $id_limpio . " AND cl.activo = 1";

        $fila = $this->db->select_row($sql);
        if (!is_array($fila)) {
            return null;
        }
        if (!isset($fila['id'])) {
            return null;
        }
        return $fila;
    }

    // 4. Crear o editar una clase
    // La categoria siempre es obligatoria (la asistencia es por categoria).
    // Si no llega fecha ni horas, se usan las de hoy y la hora actual.
    function guardar_clase($datos, $clase_id, $rol_sesion, $persona_sesion)
    {
        $id_limpio = intval($clase_id);
        $rol_num = intval($rol_sesion);
        $persona_num = intval($persona_sesion);

        // 4a. Validar campos minimos
        if (!isset($datos['nombre'])) {
            return array('error' => true, 'msg' => 'El nombre de la clase es obligatorio.');
        }
        $nombre_limpio = trim($datos['nombre']);
        if ($nombre_limpio === '') {
            return array('error' => true, 'msg' => 'Escriba el tema de la jornada. Ejemplo: Entrenamiento.');
        }

        if (isset($datos['categoria_id'])) {
            $cat_num = intval($datos['categoria_id']);
        } else {
            $cat_num = 0;
        }
        if ($cat_num <= 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar la categoria de la jornada.');
        }

        if (isset($datos['fecha'])) {
            $fecha_limpia = trim($datos['fecha']);
        } else {
            $fecha_limpia = '';
        }
        if ($fecha_limpia === '') {
            $fecha_limpia = date('Y-m-d');
        }

        // 4b. Armar el registro a guardar
        $registro = array();
        $registro['nombre'] = $nombre_limpio;
        $registro['fecha'] = $fecha_limpia;
        $registro['categoria_id'] = $cat_num;

        if (isset($datos['hora_inicio'])) {
            $hora_ini = trim($datos['hora_inicio']);
        } else {
            $hora_ini = '';
        }
        if ($hora_ini === '') {
            $hora_ini = date('H:i:s');
        }
        $registro['hora_inicio'] = $hora_ini;

        if (isset($datos['hora_fin'])) {
            $hora_fin = trim($datos['hora_fin']);
        } else {
            $hora_fin = '';
        }
        if ($hora_fin === '') {
            $hora_fin = $hora_ini;
        }
        $registro['hora_fin'] = $hora_fin;
        if (isset($datos['lugar'])) {
            $registro['lugar'] = trim($datos['lugar']);
        } else {
            $registro['lugar'] = 'Cancha Principal';
        }

        // 4c. Definir el entrenador responsable
        if ($rol_num === 2) {
            $registro['entrenador_id'] = $persona_num;
        } else {
            if (isset($datos['entrenador_id'])) {
                $ent_num = intval($datos['entrenador_id']);
            } else {
                $ent_num = 0;
            }
            if ($ent_num > 0) {
                $registro['entrenador_id'] = $ent_num;
            } else {
                $registro['entrenador_id'] = $persona_num;
            }
        }

        // 4d. Crear o actualizar
        if ($id_limpio > 0) {
            $clase = $this->obtener_clase($id_limpio);
            if (!$clase) {
                return array('error' => true, 'msg' => 'La clase no existe.');
            }
            if ($clase['estado'] === 'cerrada') {
                return array('error' => true, 'msg' => 'La clase ya esta cerrada y no se puede editar.');
            }
            $this->db->update('clase', $registro, array('id' => $id_limpio));
            insertar_bitacora(3, 'Actualizar clase', 'Clase id: ' . $id_limpio . ' - Nombre: ' . $nombre_limpio);
            return array('error' => false, 'msg' => 'Clase actualizada correctamente.', 'id' => $id_limpio);
        } else {
            $registro['estado'] = 'abierta';
            $registro['activo'] = 1;
            $nuevo_id = $this->db->insert('clase', $registro);
            if ($nuevo_id && intval($nuevo_id) > 0) {
                insertar_bitacora(1, 'Crear clase', 'Clase id: ' . $nuevo_id . ' - Nombre: ' . $nombre_limpio);
                return array('error' => false, 'msg' => 'Clase creada correctamente.', 'id' => intval($nuevo_id));
            } else {
                return array('error' => true, 'msg' => 'No se pudo crear la clase.');
            }
        }
    }

    // 5. Iniciar la jornada de hoy (tema + categoria)
    // Si ya hay una abierta para esa categoria hoy, se devuelve esa.
    function iniciar_jornada($tema, $categoria_id, $rol_sesion, $persona_sesion)
    {
        $tema_limpio = trim($tema);
        if ($tema_limpio === '') {
            $tema_limpio = 'Entrenamiento';
        }
        $cat_num = intval($categoria_id);
        if ($cat_num <= 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar la categoria de la jornada.');
        }

        $hoy = date('Y-m-d');

        // 5a. Buscar jornada abierta de hoy para esa categoria
        $sql_abierta = "SELECT id FROM clase
                        WHERE categoria_id = " . $cat_num . "
                        AND fecha = '" . $hoy . "'
                        AND estado = 'abierta'
                        AND activo = 1";
        $fila_abierta = $this->db->select_row($sql_abierta);
        if (is_array($fila_abierta)) {
            if (isset($fila_abierta['id'])) {
                $id_existente = intval($fila_abierta['id']);
                return array('error' => false, 'msg' => 'La jornada de hoy ya estaba iniciada.', 'id' => $id_existente);
            }
        }

        // 5b. Crear la jornada con la hora actual
        $datos = array(
            'nombre' => $tema_limpio,
            'categoria_id' => $cat_num,
            'fecha' => $hoy,
            'hora_inicio' => date('H:i:s'),
            'hora_fin' => date('H:i:s'),
            'lugar' => 'Cancha Principal'
        );
        return $this->guardar_clase($datos, 0, $rol_sesion, $persona_sesion);
    }

    // 6. Eliminar una clase (suave, solo si no tiene registros)
    function eliminar_clase($clase_id)
    {
        $id_limpio = intval($clase_id);
        if ($id_limpio <= 0) {
            return array('error' => true, 'msg' => 'ID de clase no valido.');
        }

        $clase = $this->obtener_clase($id_limpio);
        if (!$clase) {
            return array('error' => true, 'msg' => 'La clase no existe o ya fue eliminada.');
        }

        $sql_cuenta = "SELECT COUNT(*) AS total FROM asistencia WHERE clase_id = " . $id_limpio;
        $fila_cuenta = $this->db->select_row($sql_cuenta);
        $total_marcas = 0;
        if (is_array($fila_cuenta)) {
            if (isset($fila_cuenta['total'])) {
                $total_marcas = intval($fila_cuenta['total']);
            }
        }
        if ($total_marcas > 0) {
            return array('error' => true, 'msg' => 'La clase ya tiene asistencia registrada y no se puede eliminar.');
        }

        $this->db->update('clase', array('activo' => 0), array('id' => $id_limpio));
        insertar_bitacora(2, 'Eliminar clase', 'Clase id: ' . $id_limpio . ' - Nombre: ' . $clase['nombre']);
        return array('error' => false, 'msg' => 'Clase eliminada correctamente.');
    }

    // 6. Buscar deportista activa por numero de documento
    function buscar_deportista_por_documento($documento)
    {
        $doc_limpio = trim($documento);
        if ($doc_limpio === '') {
            return null;
        }
        $doc_esc = $this->db->escape_string($doc_limpio);

        $sql = "SELECT d.id AS deportista_id,
                       d.categoria_id,
                       cat.nombre AS categoria_nombre,
                       p.id AS persona_id,
                       p.identificacion,
                       CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre_completo
                FROM deportista d
                INNER JOIN persona p ON p.id = d.persona_id
                LEFT JOIN categoria cat ON cat.id = d.categoria_id
                WHERE p.identificacion = '" . $doc_esc . "'
                AND d.estado IN ('aprobado', 'activo')";

        $fila = $this->db->select_row($sql);
        if (!is_array($fila)) {
            return null;
        }
        if (!isset($fila['deportista_id'])) {
            return null;
        }
        return $fila;
    }

    // 7. Obtener el registro de asistencia de una deportista en una clase
    function obtener_registro($clase_id, $deportista_id)
    {
        $id_clase = intval($clase_id);
        $id_dep = intval($deportista_id);
        if ($id_clase <= 0) {
            return null;
        }
        if ($id_dep <= 0) {
            return null;
        }

        $sql = "SELECT id, clase_id, deportista_id, estado, observaciones,
                       fecha_marcacion, hora_entrada, hora_salida, minutos, origen
                FROM asistencia
                WHERE clase_id = " . $id_clase . " AND deportista_id = " . $id_dep;

        $fila = $this->db->select_row($sql);
        if (!is_array($fila)) {
            return null;
        }
        if (!isset($fila['id'])) {
            return null;
        }
        return $fila;
    }

    // 8. Marcar entrada o salida por documento (qr o manual)
    // Primer escaneo = entrada, segundo escaneo = salida con horas.
    function marcar($clase_id, $documento, $origen)
    {
        $id_clase = intval($clase_id);
        if ($id_clase <= 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar la clase o entrenamiento.');
        }

        $doc_limpio = trim($documento);
        if ($doc_limpio === '') {
            return array('error' => true, 'msg' => 'Debe ingresar o escanear el numero de documento.');
        }

        if ($origen === 'manual') {
            $origen_limpio = 'manual';
        } else {
            $origen_limpio = 'qr';
        }

        // 8a. La clase debe existir y estar abierta
        $clase = $this->obtener_clase($id_clase);
        if (!$clase) {
            return array('error' => true, 'msg' => 'La clase no existe.');
        }
        if ($clase['estado'] === 'cerrada') {
            return array('error' => true, 'msg' => 'La clase ya fue finalizada. No se puede marcar asistencia.');
        }

        // 8b. La deportista debe estar activa en el club
        $deportista = $this->buscar_deportista_por_documento($doc_limpio);
        if (!$deportista) {
            return array('error' => true, 'msg' => 'Documento no registrado como deportista activa del club.');
        }

        // 8c. Debe pertenecer a la categoria de la clase
        $cat_clase = intval($clase['categoria_id']);
        $cat_dep = intval($deportista['categoria_id']);
        if ($cat_clase > 0) {
            if ($cat_dep !== $cat_clase) {
                return array('error' => true, 'msg' => $deportista['nombre_completo'] . ' no pertenece a la categoria ' . $clase['categoria_nombre'] . '.');
            }
        }

        $id_dep = intval($deportista['deportista_id']);
        $registro = $this->obtener_registro($id_clase, $id_dep);
        $ahora = date('Y-m-d H:i:s');

        // 8d. Sin registro previo = ENTRADA
        if (!$registro) {
            $nuevo_estado = $this->calcular_estado_entrada($clase);
            $nuevo = array(
                'clase_id' => $id_clase,
                'deportista_id' => $id_dep,
                'estado' => $nuevo_estado,
                'fecha_marcacion' => $ahora,
                'hora_entrada' => $ahora,
                'origen' => $origen_limpio
            );
            $this->db->insert('asistencia', $nuevo);
            insertar_bitacora(1, 'Entrada asistencia', 'Clase: ' . $id_clase . ' - Deportista: ' . $deportista['nombre_completo']);
            return array('error' => false, 'movimiento' => 'entrada', 'estado' => $nuevo_estado, 'nombre' => $deportista['nombre_completo'], 'msg' => 'Entrada registrada: ' . $deportista['nombre_completo']);
        }

        // 8e. Con entrada pero sin salida = SALIDA (se suman las horas)
        $tiene_entrada = false;
        if (isset($registro['hora_entrada'])) {
            if ($registro['hora_entrada'] !== '' && $registro['hora_entrada'] !== null) {
                $tiene_entrada = true;
            }
        }
        $tiene_salida = false;
        if (isset($registro['hora_salida'])) {
            if ($registro['hora_salida'] !== '' && $registro['hora_salida'] !== null) {
                $tiene_salida = true;
            }
        }

        if ($tiene_entrada && !$tiene_salida) {
            $minutos = intval((strtotime($ahora) - strtotime($registro['hora_entrada'])) / 60);
            if ($minutos < 0) {
                $minutos = 0;
            }
            $cambio = array(
                'hora_salida' => $ahora,
                'minutos' => $minutos
            );
            $this->db->update('asistencia', $cambio, array('id' => intval($registro['id'])));
            insertar_bitacora(3, 'Salida asistencia', 'Clase: ' . $id_clase . ' - Deportista: ' . $deportista['nombre_completo'] . ' - Minutos: ' . $minutos);
            return array('error' => false, 'movimiento' => 'salida', 'estado' => $registro['estado'], 'nombre' => $deportista['nombre_completo'], 'minutos' => $minutos, 'msg' => 'Salida registrada: ' . $deportista['nombre_completo'] . ' (' . $minutos . ' min)');
        }

        // 8f. Sin hora de entrada (excusa o cierre previo) = se convierte en ENTRADA
        if (!$tiene_entrada) {
            $nuevo_estado = $this->calcular_estado_entrada($clase);
            $cambio = array(
                'estado' => $nuevo_estado,
                'fecha_marcacion' => $ahora,
                'hora_entrada' => $ahora,
                'origen' => $origen_limpio
            );
            $this->db->update('asistencia', $cambio, array('id' => intval($registro['id'])));
            insertar_bitacora(3, 'Entrada asistencia', 'Clase: ' . $id_clase . ' - Deportista: ' . $deportista['nombre_completo']);
            return array('error' => false, 'movimiento' => 'entrada', 'estado' => $nuevo_estado, 'nombre' => $deportista['nombre_completo'], 'msg' => 'Entrada registrada: ' . $deportista['nombre_completo']);
        }

        // 8g. Ya tiene entrada y salida = no se marca de nuevo
        return array('error' => true, 'msg' => $deportista['nombre_completo'] . ' ya registro entrada y salida en esta clase.');
    }

    // 9. Decidir si la entrada es a tiempo o tarde (10 min de tolerancia)
    function calcular_estado_entrada($clase)
    {
        $hoy = date('Y-m-d');
        if ($clase['fecha'] !== $hoy) {
            return 'asistio';
        }
        $hora_actual = date('H:i:s');
        $limite = date('H:i:s', strtotime($clase['hora_inicio'] . ' +' . $this->tolerancia_tarde . ' minutes'));
        if ($hora_actual > $limite) {
            return 'tarde';
        } else {
            return 'asistio';
        }
    }

    // 10. Marcar excusa a una deportista pendiente (sin entrada aun)
    function marcar_excusa($clase_id, $deportista_id, $observaciones)
    {
        $id_clase = intval($clase_id);
        $id_dep = intval($deportista_id);
        if ($id_clase <= 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar la clase.');
        }
        if ($id_dep <= 0) {
            return array('error' => true, 'msg' => 'Deportista no valida.');
        }

        $clase = $this->obtener_clase($id_clase);
        if (!$clase) {
            return array('error' => true, 'msg' => 'La clase no existe.');
        }
        if ($clase['estado'] === 'cerrada') {
            return array('error' => true, 'msg' => 'La clase ya fue finalizada.');
        }

        $obs_limpia = trim($observaciones);
        $ahora = date('Y-m-d H:i:s');
        $registro = $this->obtener_registro($id_clase, $id_dep);

        if (!$registro) {
            $nuevo = array(
                'clase_id' => $id_clase,
                'deportista_id' => $id_dep,
                'estado' => 'excusa',
                'observaciones' => $obs_limpia,
                'fecha_marcacion' => $ahora,
                'origen' => 'manual'
            );
            $this->db->insert('asistencia', $nuevo);
        } else {
            if (isset($registro['hora_entrada'])) {
                if ($registro['hora_entrada'] !== '' && $registro['hora_entrada'] !== null) {
                    return array('error' => true, 'msg' => 'La deportista ya registro entrada. No se puede poner excusa.');
                }
            }
            $cambio = array(
                'estado' => 'excusa',
                'observaciones' => $obs_limpia,
                'fecha_marcacion' => $ahora,
                'origen' => 'manual'
            );
            $this->db->update('asistencia', $cambio, array('id' => intval($registro['id'])));
        }

        insertar_bitacora(1, 'Excusa asistencia', 'Clase: ' . $id_clase . ' - Deportista id: ' . $id_dep);
        return array('error' => false, 'msg' => 'Excusa registrada correctamente.');
    }

    // 11. Foto en vivo de la clase: presentes + pendientes de la categoria
    function sesion_en_vivo($clase_id)
    {
        $id_clase = intval($clase_id);
        if ($id_clase <= 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar la clase.');
        }

        $clase = $this->obtener_clase($id_clase);
        if (!$clase) {
            return array('error' => true, 'msg' => 'La clase no existe.');
        }

        // 11a. Deportistas con marca en esta clase
        $sql_presentes = "SELECT a.id,
                                 a.estado,
                                 a.observaciones,
                                 a.fecha_marcacion,
                                 a.hora_entrada,
                                 a.hora_salida,
                                 a.minutos,
                                 a.origen,
                                 d.id AS deportista_id,
                                 p.identificacion,
                                 CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre_completo
                          FROM asistencia a
                          INNER JOIN deportista d ON d.id = a.deportista_id
                          INNER JOIN persona p ON p.id = d.persona_id
                          WHERE a.clase_id = " . $id_clase . "
                          ORDER BY a.hora_entrada ASC";
        $presentes = $this->db->select_all($sql_presentes);
        if (!is_array($presentes)) {
            $presentes = array();
        }

        // 11b. Deportistas de la categoria sin ninguna marca (pendientes)
        $cat_clase = intval($clase['categoria_id']);
        $where_cat = "";
        if ($cat_clase > 0) {
            $where_cat = " AND d.categoria_id = " . $cat_clase;
        }
        $sql_pendientes = "SELECT d.id AS deportista_id,
                                  p.identificacion,
                                  CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre_completo
                           FROM deportista d
                           INNER JOIN persona p ON p.id = d.persona_id
                           WHERE d.estado IN ('aprobado', 'activo')" . $where_cat . "
                           AND d.id NOT IN (SELECT a.deportista_id FROM asistencia a WHERE a.clase_id = " . $id_clase . ")
                           ORDER BY p.apellido1 ASC, p.nombre1 ASC";
        $pendientes = $this->db->select_all($sql_pendientes);
        if (!is_array($pendientes)) {
            $pendientes = array();
        }

        return array('error' => false, 'clase' => $clase, 'presentes' => $presentes, 'pendientes' => $pendientes);
    }

    // 12. Finalizar la clase: cierra y pone inasistencia a las pendientes
    function finalizar($clase_id)
    {
        $id_clase = intval($clase_id);
        if ($id_clase <= 0) {
            return array('error' => true, 'msg' => 'Debe seleccionar la clase.');
        }

        $clase = $this->obtener_clase($id_clase);
        if (!$clase) {
            return array('error' => true, 'msg' => 'La clase no existe.');
        }
        if ($clase['estado'] === 'cerrada') {
            return array('error' => true, 'msg' => 'La clase ya estaba finalizada.');
        }

        // 12a. Traer pendientes y marcarlas como no_asistio
        $datos_sesion = $this->sesion_en_vivo($id_clase);
        $pendientes = array();
        if (isset($datos_sesion['pendientes'])) {
            $pendientes = $datos_sesion['pendientes'];
        }

        $ahora = date('Y-m-d H:i:s');
        $total_ausentes = 0;
        for ($i = 0; $i < count($pendientes); $i++) {
            $id_dep = intval($pendientes[$i]['deportista_id']);
            if ($id_dep > 0) {
                $nuevo = array(
                    'clase_id' => $id_clase,
                    'deportista_id' => $id_dep,
                    'estado' => 'no_asistio',
                    'fecha_marcacion' => $ahora,
                    'origen' => 'cierre'
                );
                $this->db->insert('asistencia', $nuevo);
                $total_ausentes = $total_ausentes + 1;
            }
        }

        // 12b. Cerrar la jornada con la hora real de cierre
        $cierre = array(
            'estado' => 'cerrada',
            'hora_fin' => date('H:i:s'),
            'fecha_cierre' => $ahora
        );
        $this->db->update('clase', $cierre, array('id' => $id_clase));

        insertar_bitacora(3, 'Finalizar clase', 'Clase id: ' . $id_clase . ' - Inasistencias: ' . $total_ausentes);
        return array('error' => false, 'msg' => 'Entrenamiento finalizado. Inasistencias registradas: ' . $total_ausentes . '.', 'ausentes' => $total_ausentes);
    }

    // 13. Consolidado por deportista en un periodo (conteos, % y horas)
    function consolidado($fecha_desde, $fecha_hasta, $categoria_id)
    {
        $desde_limpio = trim($fecha_desde);
        $hasta_limpio = trim($fecha_hasta);
        if ($desde_limpio === '') {
            $desde_limpio = date('Y-m-01');
        }
        if ($hasta_limpio === '') {
            $hasta_limpio = date('Y-m-d');
        }
        $desde_esc = $this->db->escape_string($desde_limpio);
        $hasta_esc = $this->db->escape_string($hasta_limpio);

        $cat_num = intval($categoria_id);
        $where_cat = "";
        if ($cat_num > 0) {
            $where_cat = " AND d.categoria_id = " . $cat_num;
        }

        $sql = "SELECT d.id AS deportista_id,
                       CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre_completo,
                       p.identificacion,
                       cat.nombre AS categoria_nombre,
                       SUM(CASE WHEN a.estado = 'asistio' THEN 1 ELSE 0 END) AS total_asistio,
                       SUM(CASE WHEN a.estado = 'tarde' THEN 1 ELSE 0 END) AS total_tarde,
                       SUM(CASE WHEN a.estado = 'excusa' THEN 1 ELSE 0 END) AS total_excusa,
                       SUM(CASE WHEN a.estado = 'no_asistio' THEN 1 ELSE 0 END) AS total_no_asistio,
                       COUNT(a.id) AS total_marcas,
                       SUM(a.minutos) AS total_minutos
                FROM deportista d
                INNER JOIN persona p ON p.id = d.persona_id
                LEFT JOIN categoria cat ON cat.id = d.categoria_id
                LEFT JOIN (asistencia a
                    INNER JOIN clase cl ON cl.id = a.clase_id
                        AND cl.fecha >= '" . $desde_esc . "'
                        AND cl.fecha <= '" . $hasta_esc . "'
                        AND cl.activo = 1)
                    ON a.deportista_id = d.id
                WHERE d.estado IN ('aprobado', 'activo')" . $where_cat . "
                GROUP BY d.id, p.nombre1, p.nombre2, p.apellido1, p.apellido2, p.identificacion, cat.nombre
                ORDER BY p.apellido1 ASC, p.nombre1 ASC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }

        // 13a. Calcular % de asistencia y horas por deportista
        for ($i = 0; $i < count($filas); $i++) {
            $asistio = intval($filas[$i]['total_asistio']);
            $tarde = intval($filas[$i]['total_tarde']);
            $total = intval($filas[$i]['total_marcas']);
            if ($total > 0) {
                $filas[$i]['porcentaje'] = round(($asistio + $tarde) * 100 / $total);
            } else {
                $filas[$i]['porcentaje'] = 0;
            }
            $minutos = intval($filas[$i]['total_minutos']);
            $filas[$i]['horas'] = round($minutos / 60, 1);
        }

        return $filas;
    }

    // 14. Resumen para el acudiente: solo la asistencia de sus hijos
    function resumen_hijos($persona_acudiente)
    {
        $id_acudiente = intval($persona_acudiente);
        if ($id_acudiente <= 0) {
            return array();
        }

        $sql = "SELECT da.deportista_id,
                       CONCAT_WS(' ', p.nombre1, p.apellido1) AS hija_nombre,
                       cl.fecha,
                       cl.nombre AS clase_nombre,
                       cat.nombre AS categoria_nombre,
                       a.estado,
                       a.hora_entrada,
                       a.hora_salida,
                       a.minutos,
                       a.origen
                FROM deportista_acudiente da
                INNER JOIN deportista d ON d.id = da.deportista_id
                INNER JOIN persona p ON p.id = d.persona_id
                LEFT JOIN asistencia a ON a.deportista_id = d.id
                LEFT JOIN clase cl ON cl.id = a.clase_id AND cl.activo = 1
                LEFT JOIN categoria cat ON cat.id = d.categoria_id
                WHERE da.acudiente_id = " . $id_acudiente . "
                ORDER BY p.apellido1 ASC, cl.fecha DESC";

        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }
        return $filas;
    }
}
