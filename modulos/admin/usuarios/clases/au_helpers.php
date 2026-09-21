<?php
// ============================================================
// TRAIT au_helpers — utilidades del módulo Administración de Usuarios
//
// Funciones compartidas para validación, consultas seguras,
// respuestas JSON y escape de datos.
//
// REGLA DE ORO: NINGUNA consulta concatena variables.
// Todos los SELECT/UPDATE usan marcadores ?.
// Todo id que llega por POST pasa por intval().
// ============================================================

trait au_helpers
{

    // --------------------------------------------------------
    // RESPUESTAS JSON
    // --------------------------------------------------------

    // Imprime un arreglo como JSON y termina la ejecución
    private function _json($datos)
    {
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Respuesta de error: corta la ejecución con mensaje
    private function _error($mensaje)
    {
        $this->_json(array('error' => true, 'msg' => $mensaje));
    }

    // Respuesta exitosa con datos opcionales incluidos
    private function _success($mensaje, $datos = null)
    {
        $respuesta = array('error' => false, 'msg' => $mensaje);
        if ($datos !== null) {
            $respuesta['data'] = $datos;
        }
        $this->_json($respuesta);
    }

    // Verifica que llegue el header Authorization en el request
    function validar_token_simple()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $this->_error('Error en TOKEN de seguridad');
        }
    }

    // --------------------------------------------------------
    // CONSULTAS SEGURAS (prepared statements con marcadores ?)
    // --------------------------------------------------------

    // SELECT que devuelve varias filas como arreglo
    private function _consultar($sql, $parametros = array())
    {
        $filas = $this->db->getConnection()->select($sql, $parametros);
        return json_decode(json_encode($filas), true);
    }

    // SELECT que devuelve una sola fila (o arreglo vacío)
    private function _obtener_fila($sql, $parametros = array())
    {
        $filas = $this->db->getConnection()->select($sql, $parametros);
        if (empty($filas)) {
            return array();
        }
        $fila = $filas[0];
        return (array) $fila;
    }

    // SELECT que devuelve un solo valor (primera columna de la primera fila)
    private function _obtener_valor($sql, $parametros = array())
    {
        $fila = $this->_obtener_fila($sql, $parametros);
        if ($fila === array()) {
            return null;
        }
        $valores = array_values($fila);
        return $valores[0];
    }

    // --------------------------------------------------------
    // ESCAPE DE DATOS
    // --------------------------------------------------------

    // Escapa un texto para imprimirlo seguro dentro del HTML (anti-XSS)
    function au_esc($texto)
    {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }

}
?>
