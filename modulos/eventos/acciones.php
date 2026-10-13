<?php
// ============================================================
// EVENTOS — Backend del modulo
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con intval y escape_string (este db no usa ?)
//   - Cero emojis
//   - Solo responde JSON
// ============================================================

require_once("php/clase_base.php");
require_once("modulos/eventos/clases/evento_helpers.php");

class Eventos extends Base
{
    private $helpers;

    function __construct()
    {
        parent::__construct();
        $this->helpers = new EventosHelpers($this->db);
    }

    // 1. Validar token simple para peticiones AJAX con Authorization
    function validar_token_simple()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Token requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            exit(0);
        }
    }

    // 2. Obtener el ID de la persona en sesion
    function obtener_persona_sesion()
    {
        if (isset($_SESSION['persona_id'])) {
            return intval($_SESSION['persona_id']);
        }
        return 0;
    }

    // 3. Listar eventos con filtros
    function listar()
    {
        $this->validar_token_simple();

        $filtros = array();
        if (isset($_POST['tipo'])) {
            $filtros['tipo'] = trim($_POST['tipo']);
        }
        if (isset($_POST['categoria_id'])) {
            $filtros['categoria_id'] = intval($_POST['categoria_id']);
        }

        $eventos = $this->helpers->listar_eventos($filtros);

        $r = array();
        $r['error'] = false;
        $r['data'] = $eventos;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 4. Obtener detalle de un evento por ID
    function obtener()
    {
        $this->validar_token_simple();

        if (isset($_POST['id'])) {
            $id_limpio = intval($_POST['id']);
        } else {
            $id_limpio = 0;
        }

        if ($id_limpio <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID de evento requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $evento = $this->helpers->obtener_evento($id_limpio);
        if (!$evento) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'El evento no existe.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $r = array();
        $r['error'] = false;
        $r['data'] = $evento;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 5. Guardar evento (crear o actualizar)
    function guardar()
    {
        $this->validar_token_simple();

        if (isset($_POST['id'])) {
            $id_evento = intval($_POST['id']);
        } else {
            $id_evento = 0;
        }

        // Validacion de campos minimos
        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
        } else {
            $nombre = '';
        }

        if ($nombre === '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'El nombre del evento es obligatorio.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        if (isset($_POST['fecha'])) {
            $fecha = trim($_POST['fecha']);
        } else {
            $fecha = '';
        }

        if ($fecha === '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'La fecha del evento es obligatoria.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        if (isset($_POST['lugar'])) {
            $lugar = trim($_POST['lugar']);
        } else {
            $lugar = '';
        }

        if ($lugar === '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'El lugar del evento es obligatorio.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $datos = array(
            'nombre' => $nombre,
            'tipo' => (isset($_POST['tipo']) ? trim($_POST['tipo']) : 'torneo'),
            'fecha' => $fecha,
            'fecha_fin' => (isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : ''),
            'lugar' => $lugar,
            'categoria_id' => (isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : 0),
            'horario' => (isset($_POST['horario']) ? trim($_POST['horario']) : ''),
            'recomendaciones' => (isset($_POST['recomendaciones']) ? trim($_POST['recomendaciones']) : ''),
            'requiere_autorizacion' => (isset($_POST['requiere_autorizacion']) ? intval($_POST['requiere_autorizacion']) : 0)
        );

        $persona_id = $this->obtener_persona_sesion();
        $resultado = $this->helpers->guardar_evento($datos, $id_evento, $persona_id);

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 6. Eliminar evento (suave)
    function eliminar()
    {
        $this->validar_token_simple();

        if (isset($_POST['id'])) {
            $id_evento = intval($_POST['id']);
        } else {
            $id_evento = 0;
        }

        if ($id_evento <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $resultado = $this->helpers->eliminar_evento($id_evento);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 7. Listar categorias activas
    function listar_categorias()
    {
        $this->validar_token_simple();
        $categorias = $this->helpers->listar_categorias();
        $r = array();
        $r['error'] = false;
        $r['data'] = $categorias;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 8. Listar deportistas para selector de convocatoria
    function listar_deportistas_convocatoria()
    {
        $this->validar_token_simple();

        if (isset($_POST['evento_id'])) {
            $evento_id = intval($_POST['evento_id']);
        } else {
            $evento_id = 0;
        }

        if (isset($_POST['categoria_id'])) {
            $categoria_id = intval($_POST['categoria_id']);
        } else {
            $categoria_id = 0;
        }

        $deportistas = $this->helpers->listar_deportistas_para_convocar($evento_id, $categoria_id);
        $r = array();
        $r['error'] = false;
        $r['data'] = $deportistas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 9. Listar convocadas a un evento
    function listar_convocadas()
    {
        $this->validar_token_simple();

        if (isset($_POST['evento_id'])) {
            $evento_id = intval($_POST['evento_id']);
        } else {
            $evento_id = 0;
        }

        if ($evento_id <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID de evento requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $convocadas = $this->helpers->listar_convocadas_evento($evento_id);
        $r = array();
        $r['error'] = false;
        $r['data'] = $convocadas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 10. Convocar deportistas masivamente
    function convocar_masivo()
    {
        $this->validar_token_simple();

        if (isset($_POST['evento_id'])) {
            $evento_id = intval($_POST['evento_id']);
        } else {
            $evento_id = 0;
        }

        if ($evento_id <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID de evento requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $deportistas_raw = array();
        if (isset($_POST['deportista_ids'])) {
            if (is_array($_POST['deportista_ids'])) {
                $deportistas_raw = $_POST['deportista_ids'];
            } else if (is_string($_POST['deportista_ids'])) {
                $cadena_limpia = trim($_POST['deportista_ids']);
                if ($cadena_limpia !== '') {
                    $deportistas_raw = explode(',', $cadena_limpia);
                }
            }
        }

        if (count($deportistas_raw) === 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe seleccionar al menos una deportista para convocar.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $persona_id = $this->obtener_persona_sesion();
        $resultado = $this->helpers->convocar_masivo($evento_id, $deportistas_raw, $persona_id);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 11. Quitar deportista de la convocatoria
    function quitar_convocada()
    {
        $this->validar_token_simple();

        if (isset($_POST['convocada_id'])) {
            $convocada_id = intval($_POST['convocada_id']);
        } else {
            $convocada_id = 0;
        }

        if ($convocada_id <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID de convocatoria requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $resultado = $this->helpers->quitar_convocada($convocada_id);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 13. Matriz de autorizaciones de un evento (lectura para admin y entrenador)
    function listar_autorizaciones()
    {
        $this->validar_token_simple();

        if (isset($_POST['evento_id'])) {
            $evento_id = intval($_POST['evento_id']);
        } else {
            $evento_id = 0;
        }

        if ($evento_id <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID de evento requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $filas = $this->helpers->listar_autorizaciones_evento($evento_id);
        $r = array();
        $r['error'] = false;
        $r['data'] = $filas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 14. Ver la imagen de firma de una respuesta (permiso ver_firma_evento)
    // Devuelve dataURL en JSON: el PNG nunca tiene URL directa.
    function ver_firma_evento()
    {
        $this->validar_token_simple();

        if (isset($_POST['id'])) {
            $respuesta_id = intval($_POST['id']);
        } else {
            $respuesta_id = 0;
        }

        if ($respuesta_id <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $sql_firma = "SELECT firma_imagen FROM autorizacion_evento WHERE id = " . $respuesta_id;
        $fila = $this->db->select_row($sql_firma);

        if (!is_array($fila)) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Respuesta no encontrada.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        if (!isset($fila['firma_imagen'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Esta respuesta no tiene imagen guardada.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $ruta_guardada = $fila['firma_imagen'];
        if ($ruta_guardada === '' || $ruta_guardada === null) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Esta respuesta no tiene imagen guardada.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        // Solo permitir archivos dentro de storage/firmas/ con extension png
        $nombre_base = basename($ruta_guardada);
        if (substr($nombre_base, -4) !== '.png') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Archivo de firma no valido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $ruta_real = 'storage/firmas/' . $nombre_base;
        if (!file_exists($ruta_real)) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Archivo de firma no encontrado.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $binario = file_get_contents($ruta_real);
        if ($binario === false) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'No se pudo leer la firma.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        insertar_bitacora(3, 'Firma de autorizacion visualizada', 'ID respuesta: ' . $respuesta_id);

        $r = array();
        $r['error'] = false;
        $r['data_url'] = 'data:image/png;base64,' . base64_encode($binario);
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }
}

$accion = ACCION;
$f = new Eventos();
$f->$accion();
?>
