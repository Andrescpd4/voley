<?php
// ============================================================
// ASISTENCIA — Backend del modulo (solo responde JSON)
//
// Reglas AGENTS.md (§10):
//   - if/else en vez de ternarios y ??
//   - SQL seguro con intval y escape_string (este db no usa ?)
//   - Cero emojis
//   - Solo responde JSON, nunca HTML
// ============================================================

require_once("php/clase_base.php");
require_once("modulos/asistencia/clases/asistencia_helpers.php");

class Asistencia extends Base
{
    private $helpers;

    function __construct()
    {
        parent::__construct();
        $this->helpers = new AsistenciaHelpers($this->db);
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

    // 2. Rol de la sesion actual
    function obtener_rol_sesion()
    {
        if (isset($_SESSION['usuario_rol'])) {
            return intval($_SESSION['usuario_rol']);
        }
        return 0;
    }

    // 3. Persona de la sesion actual
    function obtener_persona_sesion()
    {
        if (isset($_SESSION['persona_id'])) {
            return intval($_SESSION['persona_id']);
        }
        return 0;
    }

    // 4. Verificar que sea personal del club (admin o entrenador)
    function exigir_staff()
    {
        $rol_actual = $this->obtener_rol_sesion();
        if ($rol_actual === 1) {
            return true;
        }
        if ($rol_actual === 4) {
            return true;
        }
        if ($rol_actual === 2) {
            return true;
        }
        $r = array();
        $r['error'] = true;
        $r['msg'] = 'No tiene permisos para esta accion.';
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    // 5. Listar clases (staff: admin y entrenador)
    function listar_clases()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        $rol_actual = $this->obtener_rol_sesion();
        $persona_actual = $this->obtener_persona_sesion();
        $clases = $this->helpers->listar_clases($rol_actual, $persona_actual);

        $r = array();
        $r['error'] = false;
        $r['data'] = $clases;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 6. Listar categorias activas (staff)
    function listar_categorias()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        $categorias = $this->helpers->listar_categorias();
        $r = array();
        $r['error'] = false;
        $r['data'] = $categorias;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 7. Crear o editar una clase (staff)
    function guardar_clase()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['id'])) {
            $id_clase = intval($_POST['id']);
        } else {
            $id_clase = 0;
        }

        $datos = array();
        if (isset($_POST['nombre'])) {
            $datos['nombre'] = trim($_POST['nombre']);
        }
        if (isset($_POST['categoria_id'])) {
            $datos['categoria_id'] = intval($_POST['categoria_id']);
        }
        if (isset($_POST['fecha'])) {
            $datos['fecha'] = trim($_POST['fecha']);
        }
        if (isset($_POST['hora_inicio'])) {
            $datos['hora_inicio'] = trim($_POST['hora_inicio']);
        }
        if (isset($_POST['hora_fin'])) {
            $datos['hora_fin'] = trim($_POST['hora_fin']);
        }
        if (isset($_POST['lugar'])) {
            $datos['lugar'] = trim($_POST['lugar']);
        }

        $rol_actual = $this->obtener_rol_sesion();
        $persona_actual = $this->obtener_persona_sesion();
        $resultado = $this->helpers->guardar_clase($datos, $id_clase, $rol_actual, $persona_actual);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 8. Iniciar la jornada de hoy (staff)
    function iniciar_jornada()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['tema'])) {
            $tema = trim($_POST['tema']);
        } else {
            $tema = '';
        }
        if (isset($_POST['categoria_id'])) {
            $categoria_id = intval($_POST['categoria_id']);
        } else {
            $categoria_id = 0;
        }

        $rol_actual = $this->obtener_rol_sesion();
        $persona_actual = $this->obtener_persona_sesion();
        $resultado = $this->helpers->iniciar_jornada($tema, $categoria_id, $rol_actual, $persona_actual);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 9. Eliminar una clase sin registros (staff)
    function eliminar_clase()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['id'])) {
            $id_clase = intval($_POST['id']);
        } else {
            $id_clase = 0;
        }

        $resultado = $this->helpers->eliminar_clase($id_clase);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 9. Foto en vivo de la clase: presentes y pendientes (staff)
    function sesion_en_vivo()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['clase_id'])) {
            $id_clase = intval($_POST['clase_id']);
        } else {
            $id_clase = 0;
        }

        $resultado = $this->helpers->sesion_en_vivo($id_clase);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 10. Marcar entrada o salida por documento (staff, origen qr o manual)
    function marcar()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['clase_id'])) {
            $id_clase = intval($_POST['clase_id']);
        } else {
            $id_clase = 0;
        }
        if (isset($_POST['documento'])) {
            $documento = trim($_POST['documento']);
        } else {
            $documento = '';
        }
        if (isset($_POST['origen'])) {
            $origen = trim($_POST['origen']);
        } else {
            $origen = 'qr';
        }

        $resultado = $this->helpers->marcar($id_clase, $documento, $origen);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 11. Marcar excusa a una deportista pendiente (staff)
    function marcar_excusa()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['clase_id'])) {
            $id_clase = intval($_POST['clase_id']);
        } else {
            $id_clase = 0;
        }
        if (isset($_POST['deportista_id'])) {
            $id_dep = intval($_POST['deportista_id']);
        } else {
            $id_dep = 0;
        }
        if (isset($_POST['observaciones'])) {
            $observaciones = trim($_POST['observaciones']);
        } else {
            $observaciones = '';
        }

        $resultado = $this->helpers->marcar_excusa($id_clase, $id_dep, $observaciones);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 12. Finalizar la clase (staff): cierra e inasistencias automaticas
    function finalizar()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['clase_id'])) {
            $id_clase = intval($_POST['clase_id']);
        } else {
            $id_clase = 0;
        }

        $resultado = $this->helpers->finalizar($id_clase);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // 13. Consolidado por periodo (staff)
    function consolidado()
    {
        $this->validar_token_simple();
        $this->exigir_staff();

        if (isset($_POST['desde'])) {
            $desde = trim($_POST['desde']);
        } else {
            $desde = '';
        }
        if (isset($_POST['hasta'])) {
            $hasta = trim($_POST['hasta']);
        } else {
            $hasta = '';
        }
        if (isset($_POST['categoria_id'])) {
            $categoria_id = intval($_POST['categoria_id']);
        } else {
            $categoria_id = 0;
        }

        $filas = $this->helpers->consolidado($desde, $hasta, $categoria_id);
        $r = array();
        $r['error'] = false;
        $r['data'] = $filas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    // 14. Resumen para el acudiente (solo sus hijos)
    function resumen_hijos()
    {
        $this->validar_token_simple();

        $rol_actual = $this->obtener_rol_sesion();
        $persona_actual = $this->obtener_persona_sesion();

        $permitido = false;
        if ($rol_actual === 3) {
            $permitido = true;
        }
        if ($rol_actual === 1) {
            $permitido = true;
        }
        if ($rol_actual === 4) {
            $permitido = true;
        }
        if (!$permitido) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'No tiene permisos para esta accion.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $filas = $this->helpers->resumen_hijos($persona_actual);
        $r = array();
        $r['error'] = false;
        $r['data'] = $filas;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }
}

$accion = ACCION;
$f = new Asistencia();
$f->$accion();
