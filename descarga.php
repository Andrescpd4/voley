<?php
// ============================================================
// DESCARGA.PHP — Punto de entrada para peticiones JSON (AJAX)
//
// Flujo:
//   1. Determinar ACCION (desde GET o POST)
//   2. Verificar que sea binaria (ZIP, PDF) o JSON
//   3. Llamar a obtener_ruta_menu() (verifica permisos)
//   4. Incluir acciones.php del modulo
// ============================================================

try {
    set_time_limit(2000);

    // 1. Acciones que admiten GET (solo lectura)
    // avisos_tiempo_real es el canal SSE: EventSource solo habla por GET
    $acciones_get = ['estructura', 'carpetas', 'documentos', 'dashboard', 'avisos_tiempo_real'];

    // 2. Acciones que devuelven binario (ZIP, PDF, Excel)
    $acciones_binarias = [];

    // 2b. Acciones que ponen sus propias cabeceras (no son JSON)
    $acciones_sin_json = ['avisos_tiempo_real'];

    // 3. Determinar ACCION
    $accion = '';
    if (isset($_GET['accion']) && in_array($_GET['accion'], $acciones_get)) {
        $accion = $_GET['accion'];
    } elseif (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
    } elseif (isset($_GET['accion'])) {
        $accion = $_GET['accion'];
    }

    // 4. Si es GET y la accion permite GET -> unificar en $_POST
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && in_array($_GET['accion'], $acciones_get)) {
        foreach ($_GET as $clave => $valor) {
            if (!isset($_POST[$clave])) {
                $_POST[$clave] = $valor;
            }
        }
    }

    // 5. Definir ACCION para el despachador
    define('ACCION', $accion);

    // 6. Content-Type JSON solo si NO es binario ni pone sus cabeceras
    $es_binario = in_array($accion, $acciones_binarias);
    $pone_cabeceras = in_array($accion, $acciones_sin_json);
    if (!$es_binario && !$pone_cabeceras) {
        header('Content-Type: application/json; charset=utf-8');
    }

    // 7. Cargar helpers de validacion y verificar permisos
    require_once("php/validation.php");
    $r = obtener_ruta_menu(MENU, ACCION);
    if ($r['error'] == false) {
        require_once($r['ruta']);
    } else {
        if (!$es_binario) {
            echo json_encode(['error' => true, 'msg' => $r['msg']]);
        } else {
            http_response_code(500);
            echo $r['msg'];
        }
    }
} catch (Exception $e) {
    // En produccion: loguear $e->getMessage()
}
