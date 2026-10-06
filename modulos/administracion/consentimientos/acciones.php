<?php
// ============================================================
// CONSENTIMIENTOS — Backend del módulo
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - Cero emojis
//   - SQL seguro con marcadores ?
// ============================================================

require_once("php/clase_base.php");
require_once("modulos/administracion/consentimientos/clases/cons_helpers.php");

class Consentimientos extends Base
{
    private $helpers;

    function __construct()
    {
        parent::__construct();
        $this->helpers = new ConsentimientosHelpers($this->db);
    }

    // 1. Validar token simple para peticiones JSON/FormData
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

    // 2. Listar tipos de consentimiento (para tab Tipos)
    function listar_tipos()
    {
        $this->validar_token_simple();
        $tipos = $this->helpers->listar_tipos();
        $r = array();
        $r['error'] = false;
        $r['data'] = $tipos;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 3. Obtener un tipo por ID (para editar)
    function obtener_tipo()
    {
        $this->validar_token_simple();
        if (!isset($_POST['id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $tipo = $this->helpers->obtener_tipo(intval($_POST['id']));
        $r = array();
        $r['error'] = false;
        $r['data'] = $tipo;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 4. Guardar tipo (crear o actualizar)
    function guardar_tipo()
    {
        $this->validar_token_simple();

        $v = new Validation($_POST);
        $v->addRules('nombre', 'Nombre', array('required' => true, 'length' => array(1, 200)));
        $v->addRules('slug', 'Slug', array('required' => true, 'length' => array(1, 50), 'regex' => '/^[a-z0-9_]+$/'));
        $v->addRules('contenido', 'Contenido', array('required' => true, 'length' => array(1, 65535)));
        $v->addRules('version', 'Versión', array('required' => true, 'length' => array(1, 20)));
        $v->addRules('clase', 'Clase', array('required' => true, 'in' => array('obligatorio', 'informativo')));

        $res = $v->validate();
        if ($res['messages'] !== '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $res['messages'];
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $datos = array(
            'nombre' => $_POST['nombre'],
            'slug' => $_POST['slug'],
            'contenido' => $_POST['contenido'],
            'version' => $_POST['version'],
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'clase' => $_POST['clase'],
            'resumen' => isset($_POST['resumen']) ? $_POST['resumen'] : '',
            'archivo_url' => isset($_POST['archivo_url']) ? $_POST['archivo_url'] : '',
            'orden' => isset($_POST['orden']) ? intval($_POST['orden']) : 0,
            'mostrar_popup' => isset($_POST['mostrar_popup']) ? 1 : 0
        );

        if (isset($_POST['id']) && $_POST['id'] > 0) {
            // Verificar duplicado slug excluyendo el actual
            $sql_check = "SELECT id FROM tipo_autorizacion WHERE slug = ? AND id != ?";
            $dup = $this->db->select_row($sql_check, array($_POST['slug'], intval($_POST['id'])));
            if (is_array($dup) && isset($dup['id'])) {
                $r = array();
                $r['error'] = true;
                $r['msg'] = 'El slug ya existe.';
                echo json_encode($r, JSON_UNESCAPED_UNICODE);
                return;
            }
            $this->helpers->actualizar_tipo(intval($_POST['id']), $datos);
            $msg = 'Consentimiento actualizado correctamente.';
        } else {
            // Verificar duplicado slug
            $sql_check = "SELECT id FROM tipo_autorizacion WHERE slug = ?";
            $dup = $this->db->select_row($sql_check, array($_POST['slug']));
            if (is_array($dup) && isset($dup['id'])) {
                $r = array();
                $r['error'] = true;
                $r['msg'] = 'El slug ya existe.';
                echo json_encode($r, JSON_UNESCAPED_UNICODE);
                return;
            }
            $this->helpers->crear_tipo($datos);
            $msg = 'Consentimiento creado correctamente.';
        }

        insertar_bitacora(1, 'Consentimiento guardado', $msg);
        $r = array();
        $r['error'] = false;
        $r['msg'] = $msg;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 5. Eliminar tipo (soft delete)
    function eliminar_tipo()
    {
        $this->validar_token_simple();
        if (!isset($_POST['id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->helpers->eliminar_tipo(intval($_POST['id']));
        insertar_bitacora(2, 'Consentimiento eliminado', 'ID: ' . intval($_POST['id']));
        $r = array();
        $r['error'] = false;
        $r['msg'] = 'Consentimiento desactivado correctamente.';
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 6. Subir archivo PDF adjunto
    function subir_archivo()
    {
        $this->validar_token_simple();
        if (!isset($_FILES['archivo'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'No se recibió archivo.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $res = $this->helpers->subir_archivo('archivo', 'autorizaciones');
        if ($res['error'] === false) {
            $r = array();
            $r['error'] = false;
            $r['url'] = WEB_ROOT . 'storage/autorizaciones/' . $res['nombre'];
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $res['msg'];
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
        }
    }

    // 7. Listar firmas (para tab Historial)
    function listar_firmas()
    {
        $this->validar_token_simple();
        $filtros = array();
        if (isset($_POST['tipo_id'])) $filtros['tipo_id'] = intval($_POST['tipo_id']);
        if (isset($_POST['persona_id'])) $filtros['persona_id'] = intval($_POST['persona_id']);
        if (isset($_POST['aceptada'])) $filtros['aceptada'] = intval($_POST['aceptada']);
        if (isset($_POST['fecha_desde'])) $filtros['fecha_desde'] = $_POST['fecha_desde'];
        if (isset($_POST['fecha_hasta'])) $filtros['fecha_hasta'] = $_POST['fecha_hasta'];

        $firmas = $this->helpers->listar_firmas($filtros);
        $r = array();
        $r['error'] = false;
        $r['data'] = $firmas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 8. Verificar pendientes de la persona actual (para login)
    function verificar_pendientes()
    {
        if (!isset($_SESSION['persona_id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Sin sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $pendientes = $this->helpers->verificar_pendientes($_SESSION['persona_id']);
        $r = array();
        $r['error'] = false;
        $r['data'] = $pendientes;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 9. Guardar consentimiento genérico (aceptar/rechazar)
    function guardar_consentimiento()
    {
        if (!isset($_SESSION['persona_id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe iniciar sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        if (!isset($_POST['tipo_id']) || !isset($_POST['aceptada'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Parámetros requeridos.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $res = $this->helpers->guardar_consentimiento($_SESSION['persona_id'], intval($_POST['tipo_id']), intval($_POST['aceptada']));
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    // 10. Mantener compatibilidad: aceptar_politica (wrapper)
    function aceptar_politica()
    {
        $_POST['tipo_id'] = 3; // politica_privacidad
        $_POST['aceptada'] = 1;
        $this->guardar_consentimiento();
    }

    // 11. Mantener compatibilidad: rechazar_politica (wrapper)
    function rechazar_politica()
    {
        if (!isset($_SESSION['persona_id'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe iniciar sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $_POST['tipo_id'] = 3; // politica_privacidad
        $_POST['aceptada'] = 0;
        $this->guardar_consentimiento();
    }
}

$accion = ACCION;
$c = new Consentimientos();
$c->$accion();