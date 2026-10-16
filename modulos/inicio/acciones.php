<?php
// ============================================================
// INICIO — Backend del modulo de inicio y muro social de Voley+
//
// Responsabilidad: Procesar peticiones y responder solo JSON.
// Reglas AGENTS.md:
//   - Cero HTML en el backend
//   - if/else en lugar de ?? y ternarios
//   - Conteos de select_one() con _a_numero() (COUNT trae int, no string)
//   - Consultas sanitizadas
// ============================================================

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once("php/clase_base.php");

class Formulario extends clase_base
{
    // ============================================================
    // GESTION DE SESION Y JWT (sistema base)
    // ============================================================

    // 1. Renovar el token JWT en cada cambio de pagina
    function set_token()
    {
        if (isset($_SESSION['nombre_usuario'])) {
            // Limpiar tokens consumidos
            $this->db->query("DELETE FROM admin_token WHERE estado = 2");

            // Leer secret de entorno
            if (isset($_ENV['JWT_SECRET'])) {
                $jwt_secret = $_ENV['JWT_SECRET'];
            } else {
                $jwt_secret = 'clave_secreta';
            }

            // Leer persona_id de sesion
            if (isset($_SESSION['persona_id'])) {
                $persona_id = intval($_SESSION['persona_id']);
            } else {
                $persona_id = 0;
            }

            // Validar JWT actual recibido en el header
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $auth_header = str_replace(" ", "", strval($_SERVER['HTTP_AUTHORIZATION']));
            } else {
                $auth_header = '';
            }

            $token_valido = false;
            try {
                $decoded = $this->db->object_to_array(JWT::decode($auth_header, new Key($jwt_secret, 'HS256')));

                if (is_array($decoded) && isset($decoded['next_token'])) {
                    $token_anterior = $decoded['next_token'];
                    $token_seguro = $this->db->escape_string($token_anterior);
                    $rw = $this->db->select_row("SELECT * FROM admin_token WHERE token = '$token_seguro' AND estado = 1 AND id_user = '$persona_id'");

                    if (!empty($rw)) {
                        // Verificar expiracion por fecha
                        if (isset($rw['caduca']) && $rw['caduca'] < date('Y-m-d')) {
                            $r = array();
                            $r['error'] = true;
                            $r['msg'] = "Su sesion ha expirado por inactividad";
                            echo json_encode($r);
                            return;
                        }

                        // Consumir token anterior
                        $update = array();
                        $update['estado'] = 2;
                        $this->db->update("admin_token", $update, array('id' => $rw['id']));
                        $token_valido = true;
                    }
                }
            } catch (Exception $e) {
                // Si el JWT es corrupto o expiro, se emite uno nuevo
            }

            // Emitir token fresco
            $this->_emitir_token();
        } else {
            $r = array();
            $r['error'] = true;
            $r['msg'] = "Usuario sin sesion activa";
            echo json_encode($r);
        }
    }

    // 2. Emitir nuevo JWT para el usuario logueado
    private function _emitir_token()
    {
        if (isset($_SESSION['persona_id'])) {
            $persona_id = intval($_SESSION['persona_id']);
        } else {
            $persona_id = 0;
        }

        if (isset($_ENV['JWT_SECRET'])) {
            $jwt_secret = $_ENV['JWT_SECRET'];
        } else {
            $jwt_secret = 'clave_secreta';
        }

        $hash = strtotime(date('Y-m-d H:i:s')) * 27;
        $token_hash = md5(strval($hash));

        $cifrado_datos = array(
            'caduca' => date('Y-m-d'),
            'id_user' => $persona_id,
            'token_hash' => $token_hash
        );
        $token = cifrar_texto(json_encode($cifrado_datos, JSON_UNESCAPED_UNICODE));

        $insert = array();
        $insert['token'] = $token;
        $insert['caduca'] = date('Y-m-d');
        $insert['id_user'] = $persona_id;
        $this->db->insert('admin_token', $insert);

        $payload = array(
            'caduca' => date('Y-m-d'),
            'id_user' => $persona_id,
            'token_hash' => $token_hash,
            'next_token' => $token
        );
        $jwt = JWT::encode($payload, $jwt_secret, 'HS256');

        $r = array();
        $r['error'] = false;
        $r['data'] = $jwt;
        $r['msg'] = "ok";
        echo json_encode($r);
    }

    // ============================================================
    // ESTADISTICAS DEL DASHBOARD (modulo dashboard)
    // ============================================================

    // 3. Conteos generales para el panel operativo
    function dashboard()
    {
        // 1. Total deportistas activos
        $total_deportistas = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM deportista WHERE estado != 'inactivo'"));

        // 2. Nuevas solicitudes pendientes
        $nuevas_solicitudes = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM deportista WHERE estado = 'pendiente_revision'"));

        // 3. Documentos pendientes de revision
        $documentacion_pendiente = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM documento WHERE estado = 'pendiente'"));

        // 4. Proximos eventos
        $proximos_total = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM evento WHERE fecha >= CURDATE()"));

        $proximos_eventos = $this->db->select_all("SELECT nombre, fecha, lugar FROM evento WHERE fecha >= CURDATE() ORDER BY fecha ASC LIMIT 3");
        if (!is_array($proximos_eventos)) {
            $proximos_eventos = array();
        }

        $datos = array(
            'total_deportistas' => intval($total_deportistas),
            'nuevas_solicitudes' => intval($nuevas_solicitudes),
            'documentacion_pendiente' => intval($documentacion_pendiente),
            'autorizaciones_pendientes' => 0,
            'proximos_total' => intval($proximos_total),
            'proximos_eventos' => $proximos_eventos
        );

        echo json_encode(array('error' => false, 'data' => $datos));
    }

    // ============================================================
    // HELPERS INTERNOS DEL MURO SOCIAL
    // ============================================================

    // 4. Obtener rol numerico de la sesion
    private function _rol_muro()
    {
        if (isset($_SESSION['usuario_rol'])) {
            return intval($_SESSION['usuario_rol']);
        }
        return 0;
    }

    // 5. Obtener persona_id de la sesion
    private function _persona_muro()
    {
        if (isset($_SESSION['persona_id'])) {
            return intval($_SESSION['persona_id']);
        }
        return 0;
    }

    // 6. Validar si el usuario actual es administrador (rol 1 o 4)
    private function _es_admin_muro()
    {
        $rol = $this->_rol_muro();
        if ($rol === 1 || $rol === 4) {
            return true;
        }
        return false;
    }

    // 7. Validar presencia del token en la peticion
    private function _token_muro()
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            echo json_encode(array('error' => true, 'msg' => 'Error en TOKEN'));
            return false;
        }
        return true;
    }

    // 8. Responder error estandar
    private function _error_muro($msg)
    {
        echo json_encode(array('error' => true, 'msg' => $msg));
    }

    // 9. Categorias asociadas a la persona segun su rol
    private function _categorias_persona($persona_id, $rol)
    {
        $categorias = array();
        if ($persona_id <= 0) {
            return $categorias;
        }

        // Acudiente: categorias de sus deportistas vinculados
        if ($rol === 3) {
            $filas = $this->db->select_all("SELECT DISTINCT d.categoria_id FROM deportista d INNER JOIN deportista_acudiente da ON da.deportista_id = d.id WHERE da.acudiente_id = '$persona_id' AND d.categoria_id IS NOT NULL");
            if (is_array($filas)) {
                for ($i = 0; $i < count($filas); $i++) {
                    if (isset($filas[$i]['categoria_id'])) {
                        $categorias[] = intval($filas[$i]['categoria_id']);
                    }
                }
            }
        }

        // Entrenador: categorias de sus clases asignadas
        if ($rol === 2) {
            $filas = $this->db->select_all("SELECT DISTINCT categoria_id FROM clase WHERE entrenador_id = '$persona_id' AND categoria_id IS NOT NULL");
            if (is_array($filas)) {
                for ($i = 0; $i < count($filas); $i++) {
                    if (isset($filas[$i]['categoria_id'])) {
                        $categorias[] = intval($filas[$i]['categoria_id']);
                    }
                }
            }
        }

        return $categorias;
    }

    // 10. Validar si una publicacion es visible para el usuario actual
    private function _visible_para($comunicado_id, $persona_id, $rol)
    {
        $comunicado_id = intval($comunicado_id);
        if ($comunicado_id <= 0 || $persona_id <= 0) {
            return false;
        }

        // Los administradores ven todas las publicaciones
        if ($rol === 1 || $rol === 4) {
            $existe = $this->db->select_one("SELECT COUNT(*) FROM comunicado WHERE id = '$comunicado_id'");
            if ($this->_a_numero($existe) > 0) {
                return true;
            }
            return false;
        }

        $rw = $this->db->select_row("SELECT destinatario_tipo, destinatario_id FROM comunicado WHERE id = '$comunicado_id'");
        if (empty($rw)) {
            return false;
        }

        if ($rw['destinatario_tipo'] === 'todos') {
            return true;
        }

        if ($rw['destinatario_tipo'] === 'individual' && intval($rw['destinatario_id']) === $persona_id) {
            return true;
        }

        if ($rw['destinatario_tipo'] === 'categoria') {
            $categorias = $this->_categorias_persona($persona_id, $rol);
            for ($i = 0; $i < count($categorias); $i++) {
                if ($categorias[$i] === intval($rw['destinatario_id'])) {
                    return true;
                }
            }
        }

        return false;
    }

    // 11. Nombre para mostrar del autor de un comunicado
    private function _nombre_autor($creado_por)
    {
        $creado_por = intval($creado_por);
        if ($creado_por <= 0) {
            return 'Club Voley+';
        }
        $rw = $this->db->select_row("SELECT nombre1, apellido1 FROM persona WHERE id = '$creado_por'");
        if (empty($rw)) {
            return 'Club Voley+';
        }
        if (isset($rw['nombre1'])) {
            $nombre1 = trim($rw['nombre1']);
        } else {
            $nombre1 = '';
        }
        if (isset($rw['apellido1'])) {
            $apellido1 = trim($rw['apellido1']);
        } else {
            $apellido1 = '';
        }
        $nombre = trim($nombre1 . ' ' . $apellido1);
        if ($nombre === '') {
            return 'Club Voley+';
        }
        return $nombre;
    }

    // ============================================================
    // ACCIONES PUBLICAS DEL MURO SOCIAL
    // ============================================================

    // 12. Contexto del usuario y accesos permitidos para la vista
    function contexto()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        $rol = $this->_rol_muro();
        $es_admin = $this->_es_admin_muro();

        // 1. Saludo
        if (isset($_SESSION['nombre_usuario']) && trim($_SESSION['nombre_usuario']) !== '') {
            $nombre_saludo = trim($_SESSION['nombre_usuario']);
        } else {
            $nombre_saludo = 'Bienvenido';
        }

        // 2. Modulos accesibles segun permisos de BD
        $accesos = array();
        $permiso_afiliacion = $this->db->select_one("SELECT menu FROM admin_permiso_menu WHERE rol = '$rol' AND menu = 'afiliacion'");
        if (is_string($permiso_afiliacion) && $permiso_afiliacion !== '') {
            $accesos[] = array(
                'slug' => 'afiliacion',
                'url' => 'afiliacion',
                'titulo' => 'Afiliacion',
                'descripcion' => 'Registro de deportistas, documentos y autorizaciones.',
                'icono' => 'ri-user-add-line'
            );
        }

        $permiso_dashboard = $this->db->select_one("SELECT menu FROM admin_permiso_menu WHERE rol = '$rol' AND menu = 'dashboard'");
        if (is_string($permiso_dashboard) && $permiso_dashboard !== '') {
            $accesos[] = array(
                'slug' => 'dashboard',
                'url' => 'dashboard',
                'titulo' => 'Dashboard',
                'descripcion' => 'Cifras, graficos y resumenes del club.',
                'icono' => 'ri-dashboard-line'
            );
        }

        $datos = array(
            'es_admin' => $es_admin,
            'rol' => $rol,
            'persona_id' => $persona_id,
            'nombre_saludo' => $nombre_saludo,
            'accesos' => $accesos
        );

        echo json_encode(array('error' => false, 'data' => $datos));
    }

    // 13. Listar publicaciones visibles para el feed del muro
    function feed()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        $rol = $this->_rol_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        // 1. Armar filtro por destinatario (admin ve todo)
        $filtro = '';
        if ($rol !== 1 && $rol !== 4) {
            $categorias = $this->_categorias_persona($persona_id, $rol);
            $lista_categorias = '0';
            for ($i = 0; $i < count($categorias); $i++) {
                $lista_categorias .= ',' . intval($categorias[$i]);
            }
            $filtro = "WHERE (c.destinatario_tipo = 'todos' OR (c.destinatario_tipo = 'individual' AND c.destinatario_id = '$persona_id') OR (c.destinatario_tipo = 'categoria' AND c.destinatario_id IN ($lista_categorias)))";
        }

        // 2. Traer ultimas 20 publicaciones
        $sql = "SELECT c.id, c.titulo, c.contenido, c.destinatario_tipo, c.creado_por, c.fecha_publicacion, c.confirmacion_lectura FROM comunicado c $filtro ORDER BY c.fecha_publicacion DESC LIMIT 20";
        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }

        // 3. Enriquecer cada publicacion con likes, comentarios y estado de lectura
        $muro = array();
        for ($i = 0; $i < count($filas); $i++) {
            $pub = $filas[$i];
            $pub_id = intval($pub['id']);

            $total_likes = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$pub_id'"));

            $mi_like = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$pub_id' AND persona_id = '$persona_id'"));
            if ($mi_like > 0) {
                $me_gusta = true;
            } else {
                $me_gusta = false;
            }

            $total_comentarios = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_comentario WHERE comunicado_id = '$pub_id' AND visible = 1"));

            $leido = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_lectura WHERE comunicado_id = '$pub_id' AND persona_id = '$persona_id'"));
            if ($leido > 0) {
                $leido_por_mi = true;
            } else {
                $leido_por_mi = false;
            }

            $comentarios = $this->db->select_all("SELECT cc.id, cc.comentario, cc.fecha, CONCAT_WS(' ', p.nombre1, p.apellido1) AS autor FROM comunicado_comentario cc INNER JOIN persona p ON p.id = cc.persona_id WHERE cc.comunicado_id = '$pub_id' AND cc.visible = 1 ORDER BY cc.fecha DESC LIMIT 3");
            if (!is_array($comentarios)) {
                $comentarios = array();
            }

            $pub['autor'] = $this->_nombre_autor($pub['creado_por']);
            $pub['total_likes'] = intval($total_likes);
            $pub['me_gusta'] = $me_gusta;
            $pub['total_comentarios'] = intval($total_comentarios);
            $pub['leido_por_mi'] = $leido_por_mi;
            $pub['comentarios'] = $comentarios;
            $muro[] = $pub;
        }

        echo json_encode(array('error' => false, 'data' => $muro));
    }

    // 14. Dar o quitar Me gusta (toggle)
    function toggle_like()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        $rol = $this->_rol_muro();

        if (isset($_POST['comunicado_id'])) {
            $comunicado_id = intval($_POST['comunicado_id']);
        } else {
            $comunicado_id = 0;
        }

        if ($comunicado_id <= 0) {
            $this->_error_muro('Publicacion no valida');
            return;
        }

        if (!$this->_visible_para($comunicado_id, $persona_id, $rol)) {
            $this->_error_muro('No tienes acceso a esta publicacion');
            return;
        }

        // 1. Si ya existe el like se quita, si no se crea
        $existe = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$comunicado_id' AND persona_id = '$persona_id'"));
        if ($existe > 0) {
            $this->db->query("DELETE FROM comunicado_like WHERE comunicado_id = '$comunicado_id' AND persona_id = '$persona_id'");
            $me_gusta = false;
        } else {
            $this->db->insert('comunicado_like', array('comunicado_id' => $comunicado_id, 'persona_id' => $persona_id));
            $me_gusta = true;
        }

        // 2. Contar likes actualizados
        $total = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$comunicado_id'"));

        echo json_encode(array('error' => false, 'msg' => 'ok', 'data' => array('total_likes' => intval($total), 'me_gusta' => $me_gusta)));
    }

    // 15. Agregar un comentario (maximo 500 caracteres)
    function comentar()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        $rol = $this->_rol_muro();

        if (isset($_POST['comunicado_id'])) {
            $comunicado_id = intval($_POST['comunicado_id']);
        } else {
            $comunicado_id = 0;
        }
        if (isset($_POST['comentario'])) {
            $texto = trim($_POST['comentario']);
        } else {
            $texto = '';
        }

        if ($comunicado_id <= 0) {
            $this->_error_muro('Publicacion no valida');
            return;
        }
        if ($texto === '') {
            $this->_error_muro('Escribe un comentario primero');
            return;
        }
        if (mb_strlen($texto, 'UTF-8') > 500) {
            $this->_error_muro('El comentario no puede pasar de 500 caracteres');
            return;
        }
        if (!$this->_visible_para($comunicado_id, $persona_id, $rol)) {
            $this->_error_muro('No tienes acceso a esta publicacion');
            return;
        }

        $nuevo_id = $this->db->insert('comunicado_comentario', array('comunicado_id' => $comunicado_id, 'persona_id' => $persona_id, 'comentario' => $texto, 'visible' => 1));
        if ($nuevo_id <= 0) {
            $this->_error_muro('No se pudo guardar el comentario');
            return;
        }

        $total = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado_comentario WHERE comunicado_id = '$comunicado_id' AND visible = 1"));

        $autor = $this->_nombre_autor($persona_id);
        echo json_encode(array('error' => false, 'msg' => 'Comentario publicado', 'data' => array('id' => $nuevo_id, 'autor' => $autor, 'total_comentarios' => intval($total))));
    }

    // 16. Confirmar lectura de una publicacion
    function marcar_leido()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        $rol = $this->_rol_muro();

        if (isset($_POST['comunicado_id'])) {
            $comunicado_id = intval($_POST['comunicado_id']);
        } else {
            $comunicado_id = 0;
        }

        if ($comunicado_id <= 0) {
            $this->_error_muro('Publicacion no valida');
            return;
        }
        if (!$this->_visible_para($comunicado_id, $persona_id, $rol)) {
            $this->_error_muro('No tienes acceso a esta publicacion');
            return;
        }

        // 1. Solo aplica si la publicacion exige confirmacion
        $pide = $this->_a_numero($this->db->select_one("SELECT confirmacion_lectura FROM comunicado WHERE id = '$comunicado_id'"));
        if ($pide !== 1) {
            $this->_error_muro('Esta publicacion no requiere confirmacion');
            return;
        }

        // 2. Registrar lectura (ignora duplicados por llave unica)
        $this->db->query("INSERT IGNORE INTO comunicado_lectura (comunicado_id, persona_id) VALUES ('$comunicado_id', '$persona_id')");

        echo json_encode(array('error' => false, 'msg' => 'Lectura confirmada'));
    }

    // 17. Publicar un comunicado nuevo (solo administradores)
    function publicar()
    {
        if (!$this->_token_muro()) {
            return;
        }

        if (!$this->_es_admin_muro()) {
            $this->_error_muro('No tienes permisos para publicar');
            return;
        }

        if (isset($_POST['titulo'])) {
            $titulo = trim($_POST['titulo']);
        } else {
            $titulo = '';
        }
        if (isset($_POST['contenido'])) {
            $contenido = trim($_POST['contenido']);
        } else {
            $contenido = '';
        }
        if (isset($_POST['destinatario_tipo'])) {
            $tipo = trim($_POST['destinatario_tipo']);
        } else {
            $tipo = 'todos';
        }
        if (isset($_POST['destinatario_id'])) {
            $dest_id = intval($_POST['destinatario_id']);
        } else {
            $dest_id = 0;
        }
        if (isset($_POST['confirmacion_lectura']) && intval($_POST['confirmacion_lectura']) === 1) {
            $confirmar = 1;
        } else {
            $confirmar = 0;
        }

        // 1. Validar campos requeridos
        if ($titulo === '') {
            $this->_error_muro('El titulo es obligatorio');
            return;
        }
        if (mb_strlen($titulo, 'UTF-8') > 200) {
            $this->_error_muro('El titulo no puede pasar de 200 caracteres');
            return;
        }
        if ($contenido === '') {
            $this->_error_muro('El contenido es obligatorio');
            return;
        }

        // 2. Validar destinatario
        if ($tipo !== 'todos' && $tipo !== 'categoria' && $tipo !== 'individual') {
            $tipo = 'todos';
        }
        if ($tipo === 'categoria' && $dest_id > 0) {
            $existe_cat = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM categoria WHERE id = '$dest_id'"));
            if ($existe_cat <= 0) {
                $this->_error_muro('La categoria elegida no existe');
                return;
            }
        } elseif ($tipo === 'individual' && $dest_id > 0) {
            $existe_per = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM persona WHERE id = '$dest_id'"));
            if ($existe_per <= 0) {
                $this->_error_muro('La persona elegida no existe');
                return;
            }
        } else {
            $dest_id = 0;
        }

        // 3. Guardar en la base de datos
        $persona_id = $this->_persona_muro();
        $nuevo_id = $this->db->insert('comunicado', array(
            'titulo' => $titulo,
            'contenido' => $contenido,
            'destinatario_tipo' => $tipo,
            'destinatario_id' => $dest_id,
            'creado_por' => $persona_id,
            'fecha_publicacion' => date('Y-m-d H:i:s'),
            'confirmacion_lectura' => $confirmar
        ));

        if ($nuevo_id <= 0) {
            $this->_error_muro('No se pudo publicar');
            return;
        }

        if (function_exists('insertar_bitacora')) {
            insertar_bitacora(1, 'Publicar comunicado en muro', 'Comunicado: ' . $titulo);
        }

        echo json_encode(array('error' => false, 'msg' => 'Publicacion creada', 'data' => array('id' => $nuevo_id)));
    }

    // 18. Listar categorias activas para el selector de destinatario
    function categorias()
    {
        if (!$this->_token_muro()) {
            return;
        }
        $filas = $this->db->select_all("SELECT id, nombre FROM categoria WHERE activo = 1 ORDER BY edad_minima");
        if (!is_array($filas)) {
            $filas = array();
        }
        echo json_encode(array('error' => false, 'data' => $filas));
    }

    // ============================================================
    // AUTORIZACIONES DE EVENTOS DEL ACUDIENTE (Fase C)
    // El acudiente responde todo en inicio (Nada que expulse).
    //
    // Nota de compatibilidad: select_one() con COUNT(*) devuelve
    // entero (int) con este controlador PDO, no cadena. Por eso los
    // conteos se leen con _a_numero() que acepta int o string.
    // ============================================================

    // Apoyo: convertir un conteo de BD a numero (0 si no es valido)
    private function _a_numero($valor)
    {
        if (is_int($valor)) {
            return intval($valor);
        }
        if (is_string($valor)) {
            return intval($valor);
        }
        return 0;
    }

    // 20. Convocatorias pendientes de mis deportistas vinculados
    function mis_convocatorias()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        // 1. Convocadas activas de eventos activos con mis deportistas
        $sql = "SELECT ec.id AS convocada_id, e.id AS evento_id, e.nombre AS evento_nombre, e.fecha AS evento_fecha, e.horario AS evento_horario, e.lugar AS evento_lugar, e.recomendaciones AS evento_recomendaciones, e.requiere_autorizacion AS requiere_autorizacion, d.id AS deportista_id, CONCAT_WS(' ', pdep.nombre1, pdep.apellido1) AS deportista_nombre, ae.id AS respuesta_id FROM evento_convocada ec INNER JOIN evento e ON e.id = ec.evento_id INNER JOIN deportista d ON d.id = ec.deportista_id INNER JOIN persona pdep ON pdep.id = d.persona_id INNER JOIN deportista_acudiente da ON da.deportista_id = d.id LEFT JOIN autorizacion_evento ae ON ae.id = (SELECT MAX(x.id) FROM autorizacion_evento x WHERE x.evento_id = ec.evento_id AND x.deportista_id = ec.deportista_id) WHERE ec.activo = 1 AND e.activo = 1 AND da.acudiente_id = '$persona_id' ORDER BY e.fecha DESC, pdep.apellido1 ASC";
        $filas = $this->db->select_all($sql);
        if (!is_array($filas)) {
            $filas = array();
        }

        // 2. Quedarse solo con las que aun no tienen respuesta
        $pendientes = array();
        for ($i = 0; $i < count($filas); $i++) {
            $respondida = false;
            if (isset($filas[$i]['respuesta_id'])) {
                if (intval($filas[$i]['respuesta_id']) > 0) {
                    $respondida = true;
                }
            }
            if ($respondida) {
                continue;
            }
            $pendientes[] = $filas[$i];
        }

        echo json_encode(array('error' => false, 'data' => $pendientes));
    }

    // 21. Responder Autorizo / No autorizo con firma manuscrita
    function responder_autorizacion()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        // 1. Leer y asegurar los datos del POST
        if (isset($_POST['evento_id'])) {
            $id_evento = intval($_POST['evento_id']);
        } else {
            $id_evento = 0;
        }
        if (isset($_POST['deportista_id'])) {
            $id_deportista = intval($_POST['deportista_id']);
        } else {
            $id_deportista = 0;
        }
        if (isset($_POST['autoriza'])) {
            $autoriza = intval($_POST['autoriza']);
        } else {
            $autoriza = -1;
        }
        if (isset($_POST['firma_data'])) {
            $firma_data = trim($_POST['firma_data']);
        } else {
            $firma_data = '';
        }

        if ($id_evento <= 0 || $id_deportista <= 0) {
            $this->_error_muro('Convocatoria no valida');
            return;
        }
        if ($autoriza !== 0 && $autoriza !== 1) {
            $this->_error_muro('Debes elegir Autorizo o No autorizo');
            return;
        }

        // 2. El evento debe existir y estar activo
        $evento = $this->db->select_row("SELECT id, nombre, fecha, horario, lugar, recomendaciones, requiere_autorizacion FROM evento WHERE id = '$id_evento' AND activo = 1");
        if (empty($evento)) {
            $this->_error_muro('El evento no es valido');
            return;
        }
        $requiere = 0;
        if (isset($evento['requiere_autorizacion'])) {
            $requiere = intval($evento['requiere_autorizacion']);
        }
        $nombre_evento = '';
        if (isset($evento['nombre'])) {
            $nombre_evento = $evento['nombre'];
        }
        $fecha_evento = '';
        if (isset($evento['fecha'])) {
            $fecha_evento = $evento['fecha'];
        }
        $horario_evento = '';
        if (isset($evento['horario'])) {
            $horario_evento = $evento['horario'];
        }
        $lugar_evento = '';
        if (isset($evento['lugar'])) {
            $lugar_evento = $evento['lugar'];
        }
        $recomendaciones_evento = '';
        if (isset($evento['recomendaciones'])) {
            $recomendaciones_evento = $evento['recomendaciones'];
        }

        // 3. El deportista debe estar vinculado a este acudiente
        $vinculo = $this->db->select_one("SELECT COUNT(*) FROM deportista_acudiente WHERE deportista_id = '$id_deportista' AND acudiente_id = '$persona_id'");
        $total_vinculo = $this->_a_numero($vinculo);
        if ($total_vinculo <= 0) {
            $this->_error_muro('No tienes vinculo con este deportista');
            return;
        }

        // 4. El deportista debe estar convocado y activo en el evento
        $convocada = $this->db->select_one("SELECT COUNT(*) FROM evento_convocada WHERE evento_id = '$id_evento' AND deportista_id = '$id_deportista' AND activo = 1");
        $total_convocada = $this->_a_numero($convocada);
        if ($total_convocada <= 0) {
            $this->_error_muro('Tu deportista no esta convocada a este evento');
            return;
        }

        // 5. No permitir responder dos veces
        $duplicada = $this->db->select_one("SELECT COUNT(*) FROM autorizacion_evento WHERE evento_id = '$id_evento' AND deportista_id = '$id_deportista'");
        $total_duplicada = $this->_a_numero($duplicada);
        if ($total_duplicada > 0) {
            $this->_error_muro('Ya respondiste esta convocatoria');
            return;
        }

        // 6. Si el evento exige autorizacion, la firma manuscrita es obligatoria
        $ruta_firma = '';
        if ($requiere === 1) {
            $guardado = $this->_guardar_firma_evento($persona_id, $firma_data);
            if ($guardado['ok'] === false) {
                $this->_error_muro($guardado['msg']);
                return;
            }
            $ruta_firma = $guardado['ruta'];
        }

        // 7. Armar la evidencia electronica de auditoria
        if (function_exists('verIP')) {
            $ip_cliente = verIP();
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip_cliente = '';
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agente = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agente = '';
        }
        $fecha_respuesta = date('Y-m-d H:i:s');
        if ($autoriza === 1) {
            $estado_respuesta = 'AUTORIZADA';
        } else {
            $estado_respuesta = 'NO_AUTORIZADA';
        }
        $hash_evidencia = hash('sha256', $persona_id . '|' . $id_evento . '|' . $id_deportista . '|' . $fecha_respuesta . '|' . $ip_cliente . '|' . $estado_respuesta);
        $evidencia = array(
            'ip' => $ip_cliente,
            'user_agent' => $agente,
            'fecha_hora' => $fecha_respuesta,
            'persona_id' => $persona_id,
            'evento_id' => $id_evento,
            'evento_nombre' => $nombre_evento,
            'deportista_id' => $id_deportista,
            'estado' => $estado_respuesta,
            'hash_evidencia' => $hash_evidencia
        );
        $firma_json = json_encode($evidencia, JSON_UNESCAPED_UNICODE);

        // 8. Guardar la respuesta
        $nuevo_id = $this->db->insert('autorizacion_evento', array(
            'evento_id' => $id_evento,
            'deportista_id' => $id_deportista,
            'acudiente_id' => $persona_id,
            'autoriza' => $autoriza,
            'firma_electronica' => $firma_json,
            'firma_imagen' => $ruta_firma
        ));
        if ($nuevo_id <= 0) {
            $this->_error_muro('No se pudo guardar tu respuesta');
            return;
        }

        // 9. Apagar los avisos pendientes de este evento para este acudiente
        $this->db->query("UPDATE notificaciones SET leida = 1 WHERE persona_id = '$persona_id' AND evento_id = '$id_evento' AND leida = 0");

        if (function_exists('insertar_bitacora')) {
            insertar_bitacora(1, 'Responder autorizacion de evento', 'Evento: ' . $nombre_evento . ' Estado: ' . $estado_respuesta);
        }

        if ($autoriza === 1) {
            $mensaje_ok = 'Autorizacion registrada correctamente';
        } else {
            $mensaje_ok = 'Respuesta registrada: no autorizaste la participacion';
        }
        echo json_encode(array('error' => false, 'msg' => $mensaje_ok, 'data' => array('id' => $nuevo_id, 'autoriza' => $autoriza, 'fecha' => $fecha_respuesta, 'evento_nombre' => $nombre_evento, 'evento_fecha' => $fecha_evento, 'evento_horario' => $horario_evento, 'evento_lugar' => $lugar_evento, 'evento_recomendaciones' => $recomendaciones_evento)));
    }

    // 22. Guardar el PNG de la firma en storage/firmas/ (dato sensible sin URL directa)
    private function _guardar_firma_evento($persona_id, $data_url)
    {
        $id_acudiente = intval($persona_id);

        // 1. Validar prefijo del dataURL
        if (strpos($data_url, 'data:image/png;base64,') !== 0) {
            return array('ok' => false, 'msg' => 'Debes firmar en el recuadro antes de confirmar', 'ruta' => '');
        }

        // 2. Decodificar base64 en modo estricto
        $base64_limpio = substr($data_url, strlen('data:image/png;base64,'));
        $binario = base64_decode($base64_limpio, true);
        if ($binario === false) {
            return array('ok' => false, 'msg' => 'La firma recibida no se pudo decodificar', 'ruta' => '');
        }

        // 3. Limite de tamano: 500 KB
        if (strlen($binario) > 500 * 1024) {
            return array('ok' => false, 'msg' => 'La imagen de la firma excede el tamano permitido', 'ruta' => '');
        }

        // 4. Verificar magia PNG real (no confiar solo en el prefijo)
        $magia_png = "\x89PNG\r\n\x1a\n";
        if (substr($binario, 0, 8) !== $magia_png) {
            return array('ok' => false, 'msg' => 'La firma recibida no es una imagen PNG valida', 'ruta' => '');
        }

        // 5. Crear carpeta protegida si no existe
        $carpeta = 'storage/firmas/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // 6. Nombre unico construido en servidor (sin nada del POST)
        $nombre_archivo = 'firma_evt_' . $id_acudiente . '_' . date('YmdHis') . '.png';
        $ruta_relativa = $carpeta . $nombre_archivo;

        $escritos = file_put_contents($ruta_relativa, $binario);
        if ($escritos === false) {
            return array('ok' => false, 'msg' => 'No se pudo guardar la imagen de la firma', 'ruta' => '');
        }

        return array('ok' => true, 'msg' => 'Firma guardada', 'ruta' => $ruta_relativa);
    }

    // 23. Ultimas 10 notificaciones del usuario (para la campana)
    function listar_notificaciones()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        $filas = $this->db->select_all("SELECT id, titulo, mensaje, url, evento_id, tipo, leida, creada_en FROM notificaciones WHERE persona_id = '$persona_id' ORDER BY id DESC LIMIT 10");
        if (!is_array($filas)) {
            $filas = array();
        }

        echo json_encode(array('error' => false, 'data' => $filas));
    }

    // 24. Contar avisos sin leer (numero de la campana)
    function contar_notificaciones()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        $total_crudo = $this->db->select_one("SELECT COUNT(*) FROM notificaciones WHERE persona_id = '$persona_id' AND leida = 0");
        $total = $this->_a_numero($total_crudo);

        echo json_encode(array('error' => false, 'data' => array('total' => $total)));
    }

    // 25. Marcar avisos como leidos: uno (id), un evento (evento_id) o todos (id = 0)
    function marcar_notificacion()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        if (isset($_POST['id'])) {
            $id_aviso = intval($_POST['id']);
        } else {
            $id_aviso = 0;
        }
        if (isset($_POST['evento_id'])) {
            $id_evento = intval($_POST['evento_id']);
        } else {
            $id_evento = 0;
        }

        // 1. Elegir alcance: un evento, un aviso o todos los mios
        if ($id_evento > 0) {
            $this->db->query("UPDATE notificaciones SET leida = 1 WHERE persona_id = '$persona_id' AND evento_id = '$id_evento' AND leida = 0");
        } else if ($id_aviso > 0) {
            $this->db->query("UPDATE notificaciones SET leida = 1 WHERE id = '$id_aviso' AND persona_id = '$persona_id'");
        } else {
            $this->db->query("UPDATE notificaciones SET leida = 1 WHERE persona_id = '$persona_id' AND leida = 0");
        }

        echo json_encode(array('error' => false, 'msg' => 'ok'));
    }

    // ============================================================
    // AVISOS EN VIVO (Fase F: SSE + Push del navegador)
    // El SSE mantiene una conexion abierta y empuja avisos al instante,
    // sin preguntar cada 30-60 segundos (adiós al polling corto).
    // ============================================================

    // 26. Canal SSE de avisos en vivo (GET, usa cookie de sesion)
    function avisos_tiempo_real()
    {
        // 1. Solo el dueno de la sesion puede abrir su canal
        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            header('Content-Type: text/event-stream; charset=utf-8');
            echo "event: error\n";
            echo "data: {\"msg\":\"Sesion no valida\"}\n\n";
            exit;
        }

        // 2. Soltar la sesion para no bloquear las demas paginas del usuario
        if (function_exists('session_write_close')) {
            session_write_close();
        }

        // 3. Permitir que el script viva hasta que el hosting lo corte
        @set_time_limit(0);
        if (function_exists('ignore_user_abort')) {
            @ignore_user_abort(true);
        }

        // 4. Cabeceras del canal + anti-bufer (si no, el hosting retiene todo)
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        if (function_exists('ob_implicit_flush')) {
            @ob_implicit_flush(1);
        }
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }

        // 5. Retomar desde el ultimo aviso visto (reconexion sin perder nada)
        $ultimo_id = 0;
        if (isset($_GET['ultimo_id'])) {
            $ultimo_id = intval($_GET['ultimo_id']);
        } else if (isset($_SERVER['HTTP_LAST_EVENT_ID'])) {
            $ultimo_id = intval($_SERVER['HTTP_LAST_EVENT_ID']);
        }
        if ($ultimo_id < 0) {
            $ultimo_id = 0;
        }

        // 6. Bucle corto: tope de vueltas por si el hosting no corta solo
        $vuelta = 0;
        $tope_vueltas = 40;
        while (!connection_aborted()) {
            if ($vuelta >= $tope_vueltas) {
                break;
            }
            $vuelta = $vuelta + 1;

            // 7. Una sola consulta indexada: mis avisos nuevos sin leer
            $sql = "SELECT id, titulo, mensaje, url, evento_id, tipo, creada_en FROM notificaciones WHERE persona_id = '$persona_id' AND id > '$ultimo_id' AND leida = 0 ORDER BY id ASC LIMIT 20";
            $nuevos = $this->db->select_all($sql);
            if (!is_array($nuevos)) {
                $nuevos = array();
            }
            for ($i = 0; $i < count($nuevos); $i++) {
                $id_aviso = 0;
                if (isset($nuevos[$i]['id'])) {
                    $id_aviso = intval($nuevos[$i]['id']);
                }
                if ($id_aviso > $ultimo_id) {
                    $ultimo_id = $id_aviso;
                }
                echo "id: " . $id_aviso . "\n";
                echo "event: aviso\n";
                echo "data: " . json_encode($nuevos[$i], JSON_UNESCAPED_UNICODE) . "\n\n";
            }
            if (count($nuevos) > 0) {
                @flush();
            } else {
                // 8. Latido cada 16 segundos para que el proxy no corte por inactivo
                if (($vuelta % 2) === 0) {
                    echo ": latido\n\n";
                    @flush();
                }
            }

            // 9. Esperar 8 segundos antes de volver a preguntar a la BD
            sleep(8);
        }
        exit;
    }

    // 27. Guardar la suscripcion push del navegador del usuario logueado
    function guardar_suscripcion()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        // 1. Leer los datos que envia el navegador
        if (isset($_POST['endpoint'])) {
            $endpoint = trim($_POST['endpoint']);
        } else {
            $endpoint = '';
        }
        if (isset($_POST['p256dh'])) {
            $llave_p256 = trim($_POST['p256dh']);
        } else {
            $llave_p256 = '';
        }
        if (isset($_POST['auth'])) {
            $llave_auth = trim($_POST['auth']);
        } else {
            $llave_auth = '';
        }
        if (isset($_POST['user_agent'])) {
            $agente = substr(trim($_POST['user_agent']), 0, 250);
        } else {
            $agente = '';
        }

        // 2. Que no falte ningun dato
        if ($endpoint === '' || $llave_p256 === '' || $llave_auth === '') {
            $this->_error_muro('Faltan los datos de la suscripcion');
            return;
        }

        // 3. Si ya existia este endpoint, actualizarlo; si no, crearlo
        $existe = $this->db->select_row("SELECT id FROM push_suscripciones WHERE endpoint = '" . $this->db->escape_string($endpoint) . "'");
        if (is_array($existe)) {
            if (isset($existe['id'])) {
                $this->db->update('push_suscripciones', array(
                    'persona_id' => $persona_id,
                    'p256dh' => $llave_p256,
                    'auth' => $llave_auth,
                    'user_agent' => $agente
                ), array('id' => intval($existe['id'])));
                echo json_encode(array('error' => false, 'msg' => 'Suscripcion actualizada'));
                return;
            }
        }
        $this->db->insert('push_suscripciones', array(
            'persona_id' => $persona_id,
            'endpoint' => $endpoint,
            'p256dh' => $llave_p256,
            'auth' => $llave_auth,
            'user_agent' => $agente
        ));
        echo json_encode(array('error' => false, 'msg' => 'Suscripcion guardada'));
    }

    // 28. Quitar la suscripcion push del navegador del usuario logueado
    function eliminar_suscripcion()
    {
        if (!$this->_token_muro()) {
            return;
        }

        $persona_id = $this->_persona_muro();
        if ($persona_id <= 0) {
            $this->_error_muro('Sesion no valida');
            return;
        }

        // 1. Leer el endpoint que manda el navegador
        if (isset($_POST['endpoint'])) {
            $endpoint = trim($_POST['endpoint']);
        } else {
            $endpoint = '';
        }
        if ($endpoint === '') {
            $this->_error_muro('Falta el endpoint');
            return;
        }

        // 2. Borrar solo mis suscripciones (por seguridad)
        $this->db->query("DELETE FROM push_suscripciones WHERE endpoint = '" . $this->db->escape_string($endpoint) . "' AND persona_id = '$persona_id'");
        echo json_encode(array('error' => false, 'msg' => 'Suscripcion eliminada'));
    }

    // 19. Eliminar una publicacion (solo administradores)
    function eliminar_publicacion()
    {
        if (!$this->_token_muro()) {
            return;
        }

        if (!$this->_es_admin_muro()) {
            $this->_error_muro('No tienes permisos para eliminar');
            return;
        }

        if (isset($_POST['comunicado_id'])) {
            $comunicado_id = intval($_POST['comunicado_id']);
        } else {
            $comunicado_id = 0;
        }

        if ($comunicado_id <= 0) {
            $this->_error_muro('Publicacion no valida');
            return;
        }

        $existe = $this->_a_numero($this->db->select_one("SELECT COUNT(*) FROM comunicado WHERE id = '$comunicado_id'"));
        if ($existe <= 0) {
            $this->_error_muro('La publicacion no existe');
            return;
        }

        $this->db->query("DELETE FROM comunicado WHERE id = '$comunicado_id'");

        if (function_exists('insertar_bitacora')) {
            insertar_bitacora(2, 'Eliminar comunicado del muro', 'Comunicado id: ' . $comunicado_id);
        }

        echo json_encode(array('error' => false, 'msg' => 'Publicacion eliminada'));
    }
}

// Despachador principal
$accion = ACCION;
$f = new Formulario();
$f->$accion();
