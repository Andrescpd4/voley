<?php
require_once("php/formulario_basico.php");

class TemaDiseno extends formulario_basico
{
    function validar()
    {
        $v = new Validation($_POST);
        // Validar colores hexadecimales
        $colorRules = array('regex' => '/^#[0-9A-Fa-f]{6}$/');
        $v->addRules('primaria', 'Color Primario', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('secundaria', 'Color Secundario', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('exito', 'Color Éxito', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('info', 'Color Info', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('advertencia', 'Color Advertencia', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('peligro', 'Color Peligro', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('claro', 'Color Claro', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        $v->addRules('oscuro', 'Color Oscuro', array('required' => true, 'regex' => '/^#[0-9A-Fa-f]{6}$/'));
        // Grises opcionales: si se envían, validar
        for ($i = 100; $i <= 900; $i += 100) {
            $campo = 'gris' . $i;
            if (isset($_POST[$campo]) && $_POST[$campo] != '') {
                $v->addRules($campo, 'Gris ' . $i, array('regex' => '/^#[0-9A-Fa-f]{6}$/'));
            }
        }
        // Fuentes: validar que no estén vacías si se envían
        $v->addRules('fuente_base', 'Fuente Base', array('maxLength' => 50));
        $v->addRules('fuente_titulo', 'Fuente Título', array('maxLength' => 50));
        // Tamaño base: número entre 0.5 y 3
        $v->addRules('tamano_base', 'Tamaño Base', array('required' => true, 'min' => 0.5, 'max' => 3, 'decimal' => true));
        // Escala de espaciado: número entre 0.5 y 2
        $v->addRules('escala_espaciado', 'Escala de Espaciado', array('required' => true, 'min' => 0.5, 'max' => 2, 'decimal' => true));
        // Dark mode: 0 o 1
        $v->addRules('dark_mode', 'Modo Oscuro', array('required' => true, 'integer' => true, 'min' => 0, 'max' => 1));

        $result = $v->validate();

        if ($result['messages'] == "") {
            return true;
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $result['messages'];
            $r['bad_fields'] = $result['bad_fields'];
            $r['errors'] = $result['errors'];
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            exit(0);
        }
    }

    function obtener()
    {
        // Obtener el tema activo (asumimos solo uno)
        $sql = "SELECT * FROM diseno_tema WHERE activo = 1 ORDER BY id DESC LIMIT 1";
        $rw = $this->db->select_row($sql);
        if (!$rw) {
            // Si no hay ningún tema activo, devolver fila vacía o valores por defecto?
            // Devolvemos un array vacío para que el frontend sepa que no hay configuración.
            $rw = array();
        }
        $r = array();
        $r['error'] = false;
        $r['msg'] = "OK";
        $r['data'] = $rw;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    function guardar()
    {
        if ($this->validar() == false) {
            exit(0);
        }

        // Preparar datos para insertar/actualizar
        // Como solo queremos una fila activa, podemos hacer un upsert: actualizar la fila activa o insertar si no existe.
        // Pero para simplicidad, vamos a insertar una nueva fila y marcar las anteriores como inactivas.
        // Alternativamente, podemos tener solo una fila y hacer update.
        // Vamos a usar: actualizar la fila con el id más reciente activo, o insertar si no hay.

        // Primero, desactivar cualquier tema activo anterior (opcional, pero dejamos solo uno activo)
        $this->db->query("UPDATE diseno_tema SET activo = 0 WHERE activo = 1");

        // Ahora insertar el nuevo tema
        $datos = array();
        $datos['nombre'] = $_POST['nombre'] ?? 'Tema Personalizado';
        $datos['primaria'] = $_POST['primaria'];
        $datos['secundaria'] = $_POST['secundaria'];
        $datos['exito'] = $_POST['exito'];
        $datos['info'] = $_POST['info'];
        $datos['advertencia'] = $_POST['advertencia'];
        $datos['peligro'] = $_POST['peligro'];
        $datos['claro'] = $_POST['claro'];
        $datos['oscuro'] = $_POST['oscuro'];
        // Grises
        for ($i = 100; $i <= 900; $i += 100) {
            $campo = 'gris' . $i;
            if (isset($_POST[$campo]) && $_POST[$campo] != '') {
                $datos[$campo] = $_POST[$campo];
            } else {
                $datos[$campo] = null;
            }
        }
        $datos['fuente_base'] = $_POST['fuente_base'] ?? '';
        $datos['fuente_titulo'] = $_POST['fuente_titulo'] ?? '';
        $datos['tamano_base'] = $_POST['tamano_base'] ?? 1;
        $datos['escala_espaciado'] = $_POST['escala_espaciado'] ?? 1;
        $datos['dark_mode'] = $_POST['dark_mode'] ?? 0;
        $datos['fecha_actualiz'] = date('Y-m-d H:i:s');

        $this->db->insert('diseno_tema', $datos);
        $nuevo_id = $this->db->last_insert_id();

        if ($this->db->error()) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            die;
        }

        // Opcional: registrar en historial
        // $this->registrarHistorial($nuevo_id, $_POST);

        $r = array();
        $r['error'] = false;
        $r['msg'] = "Tema guardado con éxito";
        // Devolver el tema guardado para que el frontend lo aplique inmediatamente
        $r['data'] = $datos;
        $r['data']['id'] = $nuevo_id;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    function resetear()
    {
        // Valores por defecto: podemos leer de un array o de la configuración original de Velzon.
        // Aquí definimos algunos valores por defecto (similar a los de Bootstrap/Velzon)
        $this->db->query("UPDATE diseno_tema SET activo = 0 WHERE activo = 1");

        $defecto = array();
        $defecto['nombre'] = 'Tema por Defecto';
        $defecto['primaria'] = '#405189';   // Azul oscuro de Velzon
        $defecto['secundaria'] = '#6c757d'; // Gris
        $defecto['exito'] = '#198754';      // Verde
        $defecto['info'] = '#0dcaf0';       // Azul claro
        $defecto['advertencia'] = '#ffc107'; // Amarillo
        $defecto['peligro'] = '#dc3545';    // Rojo
        $defecto['claro'] = '#f8f9fa';      // Gris claro
        $defecto['oscuro'] = '#212529';     // Casi negro
        // Grises: Bootstrap 5 tiene una escala de grises, pero nosotros dejamos algunos
        $defecto['gris100'] = '#f8f9fa';
        $defecto['gris200'] = '#e9ecef';
        $defecto['gris300'] = '#dee2e6';
        $defecto['gris400'] = '#ced4da';
        $defecto['gris500'] = '#adb5bd';
        $defecto['gris600'] = '#6c757d';
        $defecto['gris700'] = '#495057';
        $defecto['gris800'] = '#343a40';
        $defecto['gris900'] = '#212529';
        $defecto['fuente_base'] = 'System UI,Helvetica Neue,Arial,sans-serif';
        $defecto['fuente_titulo'] = 'System UI,Helvetica Neue,Arial,sans-serif';
        $defecto['tamano_base'] = 1;
        $defecto['escala_espaciado'] = 1;
        $defecto['dark_mode'] = 0;
        $defecto['fecha_actualiz'] = date('Y-m-d H:i:s');

        $this->db->insert('diseno_tema', $defecto);
        if ($this->db->error()) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = $this->db->error();
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            die;
        }

        $r = array();
        $r['error'] = false;
        $r['msg'] = "Tema restablecido a valores por defecto";
        $r['data'] = $defecto;
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }

    function generar_css()
    {
        // Este método se llama con tipo_accion = 'html' (o json si preferimos devolver el CSS como texto JSON)
        // Pero según el estándar, para devolver CSS plano podemos usar tipo_accion = 'html' y luego en el frontend hacer un fetch y insertar un <style>.
        // Vamos a devolver el CSS como texto plano.

        // Primero, obtener el tema activo
        $sql = "SELECT * FROM diseno_tema WHERE activo = 1 ORDER BY id DESC LIMIT 1";
        $rw = $this->db->select_row($sql);
        if (!$rw) {
            // Si no hay tema, devolver CSS vacío o por defecto? Vamos a devolver un comentario.
            header("Content-Type: text/css");
            echo "/* No hay tema activo */";
            exit;
        }

        // Construir el CSS con variables :root
        $css = ":root {\n";
        // Primario y secundario
        $css .= "  --bs-primary: " . $rw['primaria'] . ";\n";
        $css .= "  --bs-secondary: " . $rw['secundaria'] . ";\n";
        $css .= "  --bs-success: " . $rw['exito'] . ";\n";
        $css .= "  --bs-info: " . $rw['info'] . ";\n";
        $css .= "  --bs-warning: " . $rw['advertencia'] . ";\n";
        $css .= "  --bs-danger: " . $rw['peligro'] . ";\n";
        $css .= "  --bs-light: " . $rw['claro'] . ";\n";
        $css .= "  --bs-dark: " . $rw['oscuro'] . ";\n";
        // Grises (opcional)
        for ($i = 100; $i <= 900; $i += 100) {
            $campo = 'gris' . $i;
            if (!empty($rw[$campo])) {
                $css .= "  --bs-gray-" . $i . ": " . $rw[$campo] . ";\n";
            }
        }
        // Fuentes y tamaños
        // Nota: Bootstrap 5 usa --bs-font-sans-serif, etc.
        if (!empty($rw['fuente_base'])) {
            $css .= "  --bs-font-sans-serif: \"" . $rw['fuente_base'] . "\";\n";
        }
        if (!empty($rw['fuente_titulo'])) {
            $css .= "  --bs-font-serif: \"" . $rw['fuente_titulo'] . "\";\n"; // Los títulos a menudo usan serif, pero podemos usarlo para heading
            // También podemos definir una variable para heading si el tema la usa.
        }
        $css .= "  --bs-font-base-size: " . ($rw['tamano_base'] ?? 1) . "rem;\n";
        $css .= "  --bs-spacing-unit: " . ($rw['escala_espaciado'] ?? 1) . "rem;\n";
        // Modo oscuro: si está activado, podemos agregar una clase .dark en el body y definir variables alternativas.
        // Pero aquí solo dejamos las variables principales; el modo oscuro se puede manejar mediante una clase en body que sobrescriba.
        // Para simplificar, dejamos que el frontend agregue la clase .dark si dark_mode=1.
        $css .= "}\n";

        // Si dark_mode está activo, podemos definir variables alternativas dentro de [data-theme="dark"] o .dark
        if (!empty($rw['dark_mode']) && $rw['dark_mode'] == 1) {
            $css .= ".dark {\n";
            // Aquí podríamos invertir colores, pero para dejarlo simple, solo invertimos claro y oscuro y maybe primario?
            // En lugar de definir un conjunto completo, dejamos que el diseñador defina colores alternativos en la tabla? 
            // Por ahora, no generamos nada oscuro automáticamente.
            $css .= "  /* Modo oscuro: definir variables alternativas si se desea */\n";
            $css .= "}\n";
        }

        header("Content-Type: text/css");
        echo $css;
        exit;
    }
}

// Si se envían datos de plantas (como en otros módulos), los convertimos a string
if (isset($_POST['plantas'])) {
    $_POST['plantas'] = implode(",", $_POST['plantas']);
    $_POST['plantas'] = trim($_POST['plantas'], ',');
}

// Decodificar id si viene en formato urlsafe
if (isset($_POST['id'])) {
    $_POST['id'] = urlsafe_b64decode($_POST['id']);
}
if (isset($_GET['id'])) {
    $_GET['id'] = urlsafe_b64decode($_GET['id']);
}

$accion = ACCION;
$f = new TemaDiseno("diseno_tema", "id", true);
$f->$accion();
?>