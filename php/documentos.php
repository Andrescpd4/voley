<?php

/**
 * CLASE COMPARTIDA PARA GESTIÓN DE DOCUMENTOS
 * Funciones reutilizables para todos los módulos de documentos
 * @author Sistema de Documentos
 * @date 2026-04-27
 */

class Documentos
{

    private $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * Buscar usuarios por nombre o documento
     * @param string $termino Término de búsqueda
     * @return array Lista de usuarios encontrados
     */
    public static function buscarUsuarios($termino)
    {
        global $db;

        $termino = trim($termino);
        if (strlen($termino) < 3) {
            return array('error' => true, 'msg' => 'Ingrese al menos 3 caracteres para buscar');
        }

        $sql = "SELECT p.id, 
                       CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) as nombre_completo,
                       p.identifica, 
                       p.tipoide,
                       p.telefono,
                       p.correo,
                       s.nombre as sexo,
                       td.nombre as tipo_documento
                FROM persona p
                LEFT JOIN tipo_documento td ON p.tipoide = td.id
                LEFT JOIN sexo s ON p.sexo_id = s.id
                WHERE (p.nombre1 LIKE '%$termino%' 
                   OR p.nombre2 LIKE '%$termino%'
                   OR p.apellido1 LIKE '%$termino%'
                   OR p.apellido2 LIKE '%$termino%'
                   OR p.identifica LIKE '%$termino%'
                   OR p.telefono LIKE '%$termino%'
                   OR p.correo LIKE '%$termino%'
                   OR p.sexo_id LIKE '%$termino%'
                   OR s.nombre LIKE '%$termino%')
                   AND p.estado = 1
                ORDER BY p.apellido1, p.nombre1
                LIMIT 20";

        $resultados = $db->select_all($sql);

        if (empty($resultados)) {
            return array('error' => true, 'msg' => 'No se encontraron usuarios');
        }

        return array('error' => false, 'data' => $resultados);
    }

    /**
     * Obtener todas las categorías de documentos
     * @return array Lista de categorías
     */
    public static function getCategorias()
    {
        global $db;

        $sql = "SELECT id, nombre, descripcion, carpeta 
                FROM documentos_categorias 
                WHERE estado = 1 
                ORDER BY nombre";

        $result = $db->select_all($sql);
        return $result ? $result : array();
    }

    /**
     * Obtener categorías permitidas para empleados (Fijas + Asignadas por Rol)
     * @param int $rol_id (Opcional) ID del rol del usuario
     * @return array Lista de categorías permitidas
     */
    public static function getCategoriasEmpleado($rol_id = null)
    {
        global $db;

        // Si no se proporciona rol, intentamos obtenerlo de la sesión
        if ($rol_id === null) {
            $rol_id = $_SESSION['usuario_rol'] ?? null;
        }

        // Administradores (ID 1) y Super Admins (ID 4) tienen acceso total
        $roles_admin = array(1, 4);
        if (in_array($rol_id, $roles_admin)) {
            $sql = "SELECT * FROM documentos_categorias WHERE estado = 1 ORDER BY nombre";
        } else {
            // Combinar categorías fijas heredadas con las nuevas asignaciones por rol
            $sql = "SELECT DISTINCT c.* FROM documentos_categorias c
                    INNER JOIN documentos_categorias_roles cr ON c.id = cr.categoria_id AND cr.rol_id = '$rol_id'
                    WHERE c.estado = 1 
                    ORDER BY c.nombre";
        }

        $result = $db->select_all($sql);
        return $result ? $result : array();
    }

    /**
     * Obtener categorías permitidas para el módulo de GESTIÓN DOCUMENTOS
     * Usa la tabla documentos_categorias_roles_gestion específicamente para gestión
     * @param int $rol_id (Opcional) ID del rol del usuario
     * @return array Lista de categorías permitidas
     */
    public static function getCategoriasEmpleadoGestion($rol_id = null)
    {
        global $db;

        // Si no se proporciona rol, intentamos obtenerlo de la sesión
        if ($rol_id === null) {
            $rol_id = $_SESSION['usuario_rol'] ?? null;
        }

        // Administradores (ID 1) y Super Admins (ID 4) tienen acceso total
        $roles_admin = array(1, 4);
        if (in_array($rol_id, $roles_admin)) {
            $sql = "SELECT * FROM documentos_categorias WHERE estado = 1 ORDER BY nombre";
        } else {
            // Usar tabla específica para el módulo de gestión
            $sql = "SELECT DISTINCT c.* FROM documentos_categorias c
                    INNER JOIN documentos_categorias_roles_gestion crg ON c.id = crg.categoria_id AND crg.rol_id = '$rol_id'
                    WHERE c.estado = 1
                    ORDER BY c.nombre";
        }

        $result = $db->select_all($sql);
        return $result ? $result : array();
    }

    /**
     * Subir documento
     * @param array $datos Datos del documento
     * @return array Resultado de la operación
     */
    public static function subirDocumento($datos)
    {
        global $db;

        // Validaciones básicas
        if (empty($datos['usuario_id']) || empty($datos['categoria_id']) || empty($datos['archivo'])) {
            return array('error' => true, 'msg' => 'Faltan datos requeridos');
        }

        // Verificar que el usuario exista
        $usuario = $db->select_row("SELECT id FROM persona WHERE id = '{$datos['usuario_id']}' AND estado = 1");
        if (!$usuario) {
            return array('error' => true, 'msg' => 'El usuario no existe o está inactivo');
        }

        // Obtener información de la categoría
        $categoria = $db->select_row("SELECT carpeta FROM documentos_categorias WHERE id = '{$datos['categoria_id']}' AND estado = 1");
        if (!$categoria) {
            return array('error' => true, 'msg' => 'La categoría no existe o está inactiva');
        }

        // Procesar archivo
        $resultado_archivo = self::procesarArchivo($_FILES['archivo_documento'], $categoria['carpeta'], $datos['usuario_id']);
        if ($resultado_archivo['error']) {
            return $resultado_archivo;
        }

        // Insertar en base de datos
        $insert = array(
            'usuario_id' => $datos['usuario_id'],
            'categoria_id' => $datos['categoria_id'],
            'nombre_archivo' => $resultado_archivo['nombre_archivo'],
            'archivo_original' => $resultado_archivo['archivo_original'],
            'ruta_archivo' => $resultado_archivo['ruta_completa'],
            'tamano' => $resultado_archivo['tamano'],
            'tipo_mime' => $resultado_archivo['tipo_mime'],
            'descripcion' => $datos['descripcion'] ?? '',
            'subido_por' => $_SESSION['persona_id'] ?? null
        );

        $db->insert('documentos_usuarios', $insert);

        if ($db->error()) {
            // Eliminar archivo si falla la inserción
            unlink($resultado_archivo['ruta_completa']);
            return array('error' => true, 'msg' => 'Error al guardar en base de datos: ' . $db->error());
        }

        // =====================================================================
        // NOTIFICACIÓN: Si es un desprendible de pago, notificar al usuario
        // =====================================================================
        $es_desprendible = ($categoria['carpeta'] === 'desprendibles_pago' || $datos['categoria_id'] == 14);
        if ($es_desprendible) {
            // Crear notificación para el usuario dueño del desprendible
            // menú: 'mis_documentos' (campana), tipo: 'desprendible'
            crear_notificacion(
                $datos['usuario_id'],
                'mis_documentos',
                'desprendible',
                'Tienes un nuevo desprendible de pago disponible',
                'mis_documentos'
            );
        }

        return array('error' => false, 'msg' => 'Documento subido exitosamente');
    }

    /**
     * Listar documentos de un usuario
     * @param int $usuario_id ID del usuario
     * @param int $categoria_id (opcional) ID de categoría para filtrar
     * @return array Lista de documentos
     */
    public static function listarDocumentos($usuario_id, $categoria_id = null)
    {
        global $db;

        $sql = "SELECT d.*, c.nombre as categoria_nombre, c.carpeta as categoria_carpeta,
                       CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) as usuario_nombre
                FROM documentos_usuarios d
                LEFT JOIN documentos_categorias c ON d.categoria_id = c.id
                LEFT JOIN persona p ON d.usuario_id = p.id
                WHERE d.usuario_id = '$usuario_id' AND d.estado = 1";

        if ($categoria_id) {
            $sql .= " AND d.categoria_id = '$categoria_id'";
        }

        $sql .= " ORDER BY d.fecha_subida DESC";

        return $db->select_all($sql);
    }

    /**
     * Eliminar documento
     * @param int $documento_id ID del documento
     * @param int $usuario_actual ID del usuario que elimina (para validación de permisos)
     * @return array Resultado de la operación
     */
    public static function eliminarDocumento($documento_id, $usuario_actual = null)
    {
        global $db;

        // Obtener información del documento
        $documento = $db->select_row("SELECT * FROM documentos_usuarios WHERE id = '$documento_id' AND estado = 1");
        if (!$documento) {
            return array('error' => true, 'msg' => 'El documento no existe');
        }

        // Validar permisos (solo admin puede eliminar de otros, usuarios solo los suyos)
        if ($usuario_actual && $documento['usuario_id'] != $usuario_actual) {
            // Verificar si es administrador
            $rol = $_SESSION['rol'] ?? '';
            if (!in_array($rol, ['administrador', 'admin', 'Administrador'])) {
                return array('error' => true, 'msg' => 'No tiene permisos para eliminar este documento');
            }
        }

        // Eliminar archivo físico
        if (file_exists($documento['ruta_archivo'])) {
            unlink($documento['ruta_archivo']);
        }

        // Eliminar registro de la base de datos (hard delete)
        $db->query("DELETE FROM documentos_usuarios WHERE id = '$documento_id'");

        if ($db->error()) {
            return array('error' => true, 'msg' => 'Error al eliminar el documento de la base de datos');
        }

        return array('error' => false, 'msg' => 'Documento eliminado exitosamente');
    }

    /**
     * Procesar archivo subido
     * @param array $archivo Datos del archivo $_FILES
     * @param string $carpeta_categoria Carpeta de la categoría
     * @param int $usuario_id ID del usuario
     * @return array Resultado del procesamiento
     */
    private static function procesarArchivo($archivo, $carpeta_categoria, $usuario_id)
    {
        // Validaciones básicas
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return array('error' => true, 'msg' => 'Error al subir el archivo');
        }

        // Validar tamaño (5MB máximo)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $max_size) {
            return array('error' => true, 'msg' => 'El archivo es demasiado grande (máximo 5MB)');
        }

        // Validar tipo de archivo
        $tipos_permitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $tipos_permitidos)) {
            return array('error' => true, 'msg' => 'Tipo de archivo no permitido');
        }

        // Crear directorio si no existe
        $root_path = PATH_ROOT . '/';
        $directorio_base = $root_path . 'storage/documentos/' . $carpeta_categoria . '/' . $usuario_id;
        if (!is_dir($directorio_base)) {
            mkdir($directorio_base, 0777, true);
        }

        // Generar nombre único
        $nombre_unico = time() . '_' . uniqid() . '.' . $extension;
        $ruta_completa = $directorio_base . '/' . $nombre_unico;

        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            return array('error' => true, 'msg' => 'Error al guardar el archivo en: ' . $ruta_completa);
        }

        return array(
            'error' => false,
            'nombre_archivo' => $nombre_unico,
            'archivo_original' => $archivo['name'],
            'ruta_completa' => $ruta_completa,
            'tamano' => $archivo['size'],
            'tipo_mime' => $archivo['type']
        );
    }

    /**
     * Obtener ruta de descarga para un documento
     * @param int $documento_id ID del documento
     * @return string URL de descarga
     */
    public static function getRutaDescarga($documento_id)
    {
        global $db;

        $documento = $db->select_row("SELECT ruta_archivo, archivo_original FROM documentos_usuarios WHERE id = '$documento_id' AND estado = 1");

        if (!$documento || !file_exists($documento['ruta_archivo'])) {
            return null;
        }

        return $documento['ruta_archivo'];
    }

    /**
     * Obtener los IDs de roles que tienen acceso a una categoría específica
     * @param int $categoria_id
     * @return array Lista de IDs de roles
     */
    public static function getRolesCategoria($categoria_id)
    {
        global $db;
        $sql = "SELECT rol_id FROM documentos_categorias_roles WHERE categoria_id = '$categoria_id'";
        $result = $db->select_all($sql);

        $roles = array();
        if ($result) {
            foreach ($result as $row) {
                $roles[] = $row['rol_id'];
            }
        }
        return $roles;
    }

    /**
     * Asignar roles a una categoría
     * @param int $categoria_id
     * @param array $roles_ids
     * @return array Resultado
     */
    public static function asignarRolesACategoria($categoria_id, $roles_ids)
    {
        global $db;

        // Eliminar asignaciones previas para evitar duplicados
        $db->query("DELETE FROM documentos_categorias_roles WHERE categoria_id = '$categoria_id'");

        // Insertar los nuevos accesos
        if (!empty($roles_ids) && is_array($roles_ids)) {
            foreach ($roles_ids as $rol_id) {
                $insert = array(
                    'categoria_id' => $categoria_id,
                    'rol_id' => $rol_id
                );
                $db->insert('documentos_categorias_roles', $insert);
            }
        }

        return array('error' => false, 'msg' => 'Accesos actualizados exitosamente');
    }

    /**
     * Obtener todas las categorías con bandera si el rol tiene acceso
     * @param int $rol_id ID del rol
     * @return array Lista de categorías con campo 'tiene_acceso' (bool)
     */
    public static function getCategoriasPorRol($rol_id)
    {
        global $db;
        $sql = "SELECT c.id, c.nombre, c.descripcion,
                CASE WHEN r.rol_id IS NULL THEN 0 ELSE 1 END as tiene_acceso
                FROM documentos_categorias c
                LEFT JOIN documentos_categorias_roles r
                  ON c.id = r.categoria_id AND r.rol_id = '$rol_id'
                WHERE c.estado = 1
                ORDER BY c.nombre";
        $result = $db->select_all($sql);
        return $result ? $result : [];
    }

    /**
     * Asignar categorías a un rol (reemplaza asignaciones existentes)
     * @param int $rol_id ID del rol
     * @param array $categorias_ids IDs de categorías
     * @return array Resultado
     */
    public static function asignarCategoriasARol($rol_id, $categorias_ids)
    {
        global $db;
        // Eliminar asignaciones previas del rol
        $db->query("DELETE FROM documentos_categorias_roles WHERE rol_id = '$rol_id'");
        // Insertar nuevos accesos
        if (!empty($categorias_ids) && is_array($categorias_ids)) {
            foreach ($categorias_ids as $cat_id) {
                $insert = array(
                    'categoria_id' => $cat_id,
                    'rol_id' => $rol_id
                );
                $db->insert('documentos_categorias_roles', $insert);
            }
        }
        return array('error' => false, 'msg' => 'Accesos actualizados exitosamente');
    }

    // =====================================================================
    // Funciones específicas para el módulo GESTIÓN DOCUMENTOS
    // Usan la tabla documentos_categorias_roles_gestion
    // =====================================================================

    /**
     * Obtener los IDs de roles que tienen acceso a una categoría específica (gestión)
     * @param int $categoria_id
     * @return array Lista de IDs de roles
     */
    public static function getRolesCategoriaGestion($categoria_id)
    {
        global $db;
        $sql = "SELECT rol_id FROM documentos_categorias_roles_gestion WHERE categoria_id = '$categoria_id'";
        $result = $db->select_all($sql);

        $roles = array();
        if ($result) {
            foreach ($result as $row) {
                $roles[] = $row['rol_id'];
            }
        }
        return $roles;
    }

    /**
     * Asignar roles a una categoría (gestión)
     * @param int $categoria_id
     * @param array $roles_ids
     * @return array Resultado
     */
    public static function asignarRolesACategoriaGestion($categoria_id, $roles_ids)
    {
        global $db;

        $db->query("DELETE FROM documentos_categorias_roles_gestion WHERE categoria_id = '$categoria_id'");

        if (!empty($roles_ids) && is_array($roles_ids)) {
            foreach ($roles_ids as $rol_id) {
                $insert = array(
                    'categoria_id' => $categoria_id,
                    'rol_id' => $rol_id
                );
                $db->insert('documentos_categorias_roles_gestion', $insert);
            }
        }

        return array('error' => false, 'msg' => 'Accesos actualizados exitosamente');
    }

    /**
     * Obtener todas las categorías con bandera si el rol tiene acceso (gestión)
     * @param int $rol_id ID del rol
     * @return array Lista de categorías con campo 'tiene_acceso' (bool)
     */
    public static function getCategoriasPorRolGestion($rol_id)
    {
        global $db;
        $sql = "SELECT c.id, c.nombre, c.descripcion,
                CASE WHEN r.rol_id IS NULL THEN 0 ELSE 1 END as tiene_acceso
                FROM documentos_categorias c
                LEFT JOIN documentos_categorias_roles_gestion r
                  ON c.id = r.categoria_id AND r.rol_id = '$rol_id'
                WHERE c.estado = 1
                ORDER BY c.nombre";
        $result = $db->select_all($sql);
        return $result ? $result : [];
    }

    /**
     * Asignar categorías a un rol (gestión, reemplaza asignaciones existentes)
     * @param int $rol_id ID del rol
     * @param array $categorias_ids IDs de categorías
     * @return array Resultado
     */
    public static function asignarCategoriasARolGestion($rol_id, $categorias_ids)
    {
        global $db;
        $db->query("DELETE FROM documentos_categorias_roles_gestion WHERE rol_id = '$rol_id'");
        if (!empty($categorias_ids) && is_array($categorias_ids)) {
            foreach ($categorias_ids as $cat_id) {
                $insert = array(
                    'categoria_id' => $cat_id,
                    'rol_id' => $rol_id
                );
                $db->insert('documentos_categorias_roles_gestion', $insert);
            }
        }
        return array('error' => false, 'msg' => 'Accesos actualizados exitosamente');
    }
}
