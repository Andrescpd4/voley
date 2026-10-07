<?php
// ============================================================
// CONSENTIMIENTOS — Backend del modulo
//
// Reglas AGENTS.md (§10 y §11):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con intval y escape_string (este db no usa ?)
//   - Cero emojis
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
        if (isset($_POST['id'])) {
            $id_limpio = intval($_POST['id']);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $tipo = $this->helpers->obtener_tipo($id_limpio);
        $r = array();
        $r['error'] = false;
        $r['data'] = $tipo;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 4. Leer los campos del formulario de tipo con valores por defecto
    function leer_datos_tipo()
    {
        if (isset($_POST['nombre'])) {
            $nombre_txt = trim($_POST['nombre']);
        } else {
            $nombre_txt = '';
        }
        if (isset($_POST['slug'])) {
            $slug_txt = trim($_POST['slug']);
        } else {
            $slug_txt = '';
        }
        if (isset($_POST['contenido'])) {
            $contenido_txt = $_POST['contenido'];
        } else {
            $contenido_txt = '';
        }
        if (isset($_POST['version'])) {
            $version_txt = trim($_POST['version']);
        } else {
            $version_txt = '';
        }
        if (isset($_POST['clase'])) {
            $clase_txt = trim($_POST['clase']);
        } else {
            $clase_txt = '';
        }
        if (isset($_POST['resumen'])) {
            $resumen_txt = trim($_POST['resumen']);
        } else {
            $resumen_txt = '';
        }
        if (isset($_POST['archivo_url'])) {
            $archivo_txt = trim($_POST['archivo_url']);
        } else {
            $archivo_txt = '';
        }
        if (isset($_POST['orden'])) {
            $orden_int = intval($_POST['orden']);
        } else {
            $orden_int = 0;
        }
        if (isset($_POST['activo'])) {
            $activo_int = 1;
        } else {
            $activo_int = 0;
        }
        if (isset($_POST['mostrar_popup'])) {
            $popup_int = 1;
        } else {
            $popup_int = 0;
        }

        $datos = array(
            'nombre' => $nombre_txt,
            'slug' => $slug_txt,
            'contenido' => $contenido_txt,
            'version' => $version_txt,
            'activo' => $activo_int,
            'clase' => $clase_txt,
            'resumen' => $resumen_txt,
            'archivo_url' => $archivo_txt,
            'orden' => $orden_int,
            'mostrar_popup' => $popup_int
        );
        return $datos;
    }

    // 5. Guardar tipo (crear o actualizar)
    function guardar_tipo()
    {
        $this->validar_token_simple();

        $v = new Validation($_POST);
        $v->addRules('nombre', 'Nombre', array('required' => true, 'length' => array(1, 200)));
        $v->addRules('slug', 'Slug', array('required' => true, 'length' => array(1, 50), 'regExp' => '/^[a-z0-9_]+$/'));
        $v->addRules('contenido', 'Contenido', array('required' => true, 'length' => array(1, 65535)));
        $v->addRules('version', 'Versión', array('required' => true, 'length' => array(1, 20)));

        $res = $v->validate();
        if ($res['messages'] !== '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $res['messages'];
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $datos = $this->leer_datos_tipo();

        // Validar clase permitida con if/else
        if ($datos['clase'] === 'obligatorio') {
            $clase_ok = true;
        } else if ($datos['clase'] === 'informativo') {
            $clase_ok = true;
        } else {
            $clase_ok = false;
        }

        if (!$clase_ok) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'La clase debe ser obligatorio o informativo.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        if (isset($_POST['id'])) {
            $id_editar = intval($_POST['id']);
        } else {
            $id_editar = 0;
        }

        $slug_limpio = $this->db->escape_string($datos['slug']);

        if ($id_editar > 0) {
            // Verificar duplicado slug excluyendo el actual
            $sql_check = "SELECT id FROM tipo_autorizacion WHERE slug = '" . $slug_limpio . "' AND id != " . $id_editar;
            $dup = $this->db->select_row($sql_check);
            if (is_array($dup)) {
                if (isset($dup['id'])) {
                    $r = array();
                    $r['error'] = true;
                    $r['msg'] = 'El slug ya existe en otro documento.';
                    echo json_encode($r, JSON_UNESCAPED_UNICODE);
                    return;
                }
            }
            $this->helpers->actualizar_tipo($id_editar, $datos);
            $msg = 'Consentimiento actualizado correctamente.';
        } else {
            // Verificar duplicado slug
            $sql_check = "SELECT id FROM tipo_autorizacion WHERE slug = '" . $slug_limpio . "'";
            $dup = $this->db->select_row($sql_check);
            if (is_array($dup)) {
                if (isset($dup['id'])) {
                    $r = array();
                    $r['error'] = true;
                    $r['msg'] = 'El slug ya existe en otro documento.';
                    echo json_encode($r, JSON_UNESCAPED_UNICODE);
                    return;
                }
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

    // 6. Eliminar tipo (soft delete)
    function eliminar_tipo()
    {
        $this->validar_token_simple();
        if (isset($_POST['id'])) {
            $id_limpio = intval($_POST['id']);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'ID requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $this->helpers->eliminar_tipo($id_limpio);
        insertar_bitacora(2, 'Consentimiento eliminado', 'ID: ' . $id_limpio);
        $r = array();
        $r['error'] = false;
        $r['msg'] = 'Consentimiento desactivado correctamente.';
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 7. Subir archivo PDF adjunto
    function subir_archivo()
    {
        $this->validar_token_simple();
        if (isset($_FILES['archivo'])) {
            $hay_archivo = true;
        } else {
            $hay_archivo = false;
        }
        if (!$hay_archivo) {
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

    // 8. Listar firmas (para tab Historial)
    function listar_firmas()
    {
        $this->validar_token_simple();
        $filtros = array();
        if (isset($_POST['tipo_id'])) {
            $filtros['tipo_id'] = intval($_POST['tipo_id']);
        }
        if (isset($_POST['persona_id'])) {
            $filtros['persona_id'] = intval($_POST['persona_id']);
        }
        if (isset($_POST['persona_doc'])) {
            if (trim($_POST['persona_doc']) !== '') {
                $doc_limpio = $this->db->escape_string(trim($_POST['persona_doc']));
                $sql_per = "SELECT id FROM persona WHERE identificacion = '" . $doc_limpio . "'";
                $fila_per = $this->db->select_row($sql_per);
                if (is_array($fila_per)) {
                    if (isset($fila_per['id'])) {
                        $filtros['persona_id'] = intval($fila_per['id']);
                    }
                } else {
                    $filtros['persona_id'] = -1;
                }
            }
        }
        if (isset($_POST['aceptada'])) {
            if ($_POST['aceptada'] !== '') {
                $filtros['aceptada'] = intval($_POST['aceptada']);
            }
        }
        if (isset($_POST['fecha_desde'])) {
            $filtros['fecha_desde'] = $_POST['fecha_desde'];
        }
        if (isset($_POST['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $_POST['fecha_hasta'];
        }

        $firmas = $this->helpers->listar_firmas($filtros);
        $r = array();
        $r['error'] = false;
        $r['data'] = $firmas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 9. Verificar pendientes de la persona actual (para login)
    function verificar_pendientes()
    {
        if (isset($_SESSION['persona_id'])) {
            $persona_sesion = intval($_SESSION['persona_id']);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Sin sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $pendientes = $this->helpers->verificar_pendientes($persona_sesion);
        $r = array();
        $r['error'] = false;
        $r['data'] = $pendientes;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 10. Guardar consentimiento generico (aceptar/rechazar por tipo_id)
    function guardar_consentimiento()
    {
        if (isset($_SESSION['persona_id'])) {
            $persona_sesion = intval($_SESSION['persona_id']);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe iniciar sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        if (isset($_POST['tipo_id'])) {
            $tipo_post = intval($_POST['tipo_id']);
        } else {
            $tipo_post = 0;
        }
        if (isset($_POST['aceptada'])) {
            $aceptada_post = intval($_POST['aceptada']);
        } else {
            $aceptada_post = -1;
        }
        if ($tipo_post <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Documento requerido.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        if ($aceptada_post !== 0 && $aceptada_post !== 1) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Decisión no válida.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $res = $this->helpers->guardar_consentimiento($persona_sesion, $tipo_post, $aceptada_post);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    // 11. Buscar el tipo_id de la politica por slug (sin IDs fijos)
    function buscar_tipo_politica()
    {
        $tipo = $this->helpers->obtener_tipo_por_slug('politica_privacidad');
        if (isset($tipo['id'])) {
            return intval($tipo['id']);
        } else {
            return 0;
        }
    }

    // 12. Mantener compatibilidad: aceptar_politica
    function aceptar_politica()
    {
        if (isset($_SESSION['persona_id'])) {
            $persona_sesion = intval($_SESSION['persona_id']);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe iniciar sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $tipo_id = $this->buscar_tipo_politica();
        if ($tipo_id <= 0) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'No se encontró una política de privacidad activa.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $res = $this->helpers->guardar_consentimiento($persona_sesion, $tipo_id, 1);
        if ($res['error'] === false) {
            $res['redirect'] = WEB_ROOT . 'inicio';
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    // 13. Mantener compatibilidad: rechazar_politica (guarda rechazo y expulsa)
    function rechazar_politica()
    {
        if (isset($_SESSION['persona_id'])) {
            $persona_sesion = intval($_SESSION['persona_id']);
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe iniciar sesión.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }
        $tipo_id = $this->buscar_tipo_politica();
        if ($tipo_id > 0) {
            $this->helpers->guardar_consentimiento($persona_sesion, $tipo_id, 0);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $r = array();
        $r['error'] = false;
        $r['msg'] = 'Has rechazado la política de privacidad. Tu sesión no puede continuar.';
        $r['redirect'] = WEB_ROOT . 'iniciar-sesion';
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }
}

$accion = ACCION;
$c = new Consentimientos();
$c->$accion();
