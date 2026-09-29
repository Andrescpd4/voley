<?php
// ============================================================
// INICIO — Backend del modulo de inicio y muro social de Voley+
//
// Responsabilidad: Procesar peticiones y responder solo JSON.
// Reglas AGENTS.md:
//   - Cero HTML en el backend
//   - if/else en lugar de ?? y ternarios
//   - Comprobacion segura de select_one() con is_string()
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
        $total_deportistas = $this->db->select_one("SELECT COUNT(*) FROM deportista WHERE estado != 'inactivo'");
        if (!is_string($total_deportistas)) {
            $total_deportistas = 0;
        }

        // 2. Nuevas solicitudes pendientes
        $nuevas_solicitudes = $this->db->select_one("SELECT COUNT(*) FROM deportista WHERE estado = 'pendiente_revision'");
        if (!is_string($nuevas_solicitudes)) {
            $nuevas_solicitudes = 0;
        }

        // 3. Documentos pendientes de revision
        $documentacion_pendiente = $this->db->select_one("SELECT COUNT(*) FROM documento WHERE estado = 'pendiente'");
        if (!is_string($documentacion_pendiente)) {
            $documentacion_pendiente = 0;
        }

        // 4. Proximos eventos
        $proximos_total = $this->db->select_one("SELECT COUNT(*) FROM evento WHERE fecha >= CURDATE()");
        if (!is_string($proximos_total)) {
            $proximos_total = 0;
        }

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
            if (is_string($existe) && intval($existe) > 0) {
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

            $total_likes = $this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$pub_id'");
            if (!is_string($total_likes)) {
                $total_likes = 0;
            }

            $mi_like = $this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$pub_id' AND persona_id = '$persona_id'");
            if (is_string($mi_like) && intval($mi_like) > 0) {
                $me_gusta = true;
            } else {
                $me_gusta = false;
            }

            $total_comentarios = $this->db->select_one("SELECT COUNT(*) FROM comunicado_comentario WHERE comunicado_id = '$pub_id' AND visible = 1");
            if (!is_string($total_comentarios)) {
                $total_comentarios = 0;
            }

            $leido = $this->db->select_one("SELECT COUNT(*) FROM comunicado_lectura WHERE comunicado_id = '$pub_id' AND persona_id = '$persona_id'");
            if (is_string($leido) && intval($leido) > 0) {
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
        $existe = $this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$comunicado_id' AND persona_id = '$persona_id'");
        if (is_string($existe) && intval($existe) > 0) {
            $this->db->query("DELETE FROM comunicado_like WHERE comunicado_id = '$comunicado_id' AND persona_id = '$persona_id'");
            $me_gusta = false;
        } else {
            $this->db->insert('comunicado_like', array('comunicado_id' => $comunicado_id, 'persona_id' => $persona_id));
            $me_gusta = true;
        }

        // 2. Contar likes actualizados
        $total = $this->db->select_one("SELECT COUNT(*) FROM comunicado_like WHERE comunicado_id = '$comunicado_id'");
        if (!is_string($total)) {
            $total = 0;
        }

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

        $total = $this->db->select_one("SELECT COUNT(*) FROM comunicado_comentario WHERE comunicado_id = '$comunicado_id' AND visible = 1");
        if (!is_string($total)) {
            $total = 0;
        }

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
        $pide = $this->db->select_one("SELECT confirmacion_lectura FROM comunicado WHERE id = '$comunicado_id'");
        if (!is_string($pide) || intval($pide) !== 1) {
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
            $existe_cat = $this->db->select_one("SELECT COUNT(*) FROM categoria WHERE id = '$dest_id'");
            if (!is_string($existe_cat) || intval($existe_cat) <= 0) {
                $this->_error_muro('La categoria elegida no existe');
                return;
            }
        } elseif ($tipo === 'individual' && $dest_id > 0) {
            $existe_per = $this->db->select_one("SELECT COUNT(*) FROM persona WHERE id = '$dest_id'");
            if (!is_string($existe_per) || intval($existe_per) <= 0) {
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

        $existe = $this->db->select_one("SELECT COUNT(*) FROM comunicado WHERE id = '$comunicado_id'");
        if (!is_string($existe) || intval($existe) <= 0) {
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
