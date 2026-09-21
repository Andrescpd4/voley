<?php
// ============================================================
// TRAIT au_usuarios — CRUD y asignación de roles del módulo
// Administración de Usuarios.
//
// Gestiona las tablas:
//   persona -> datos de la persona
//   usuario -> credenciales de acceso y rol
//   admin_usuario -> relación persona-rol (compatibilidad)
//   admin_rol -> catálogo de roles
// ============================================================

trait au_usuarios
{
    // --------------------------------------------------------
    // LISTAR — devuelve JSON con todos los usuarios
    // --------------------------------------------------------
    function listar()
    {
        $sql = "SELECT
                    u.persona_id,
                    p.identificacion,
                    p.user AS login,
                    CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS nombre,
                    p.correo,
                    u.rol_id AS rol_id,
                    r.nombre AS rol
                FROM admin_usuario u
                INNER JOIN persona p ON p.id = u.persona_id
                LEFT JOIN admin_rol r ON r.id = u.rol_id
                ORDER BY nombre ASC";

        $usuarios = $this->_consultar($sql);
        $this->_success('Listado de usuarios', $usuarios);
    }

    // --------------------------------------------------------
    // AGREGAR — crea un nuevo usuario con su rol y credenciales
    // --------------------------------------------------------
    function agregar()
    {
        // 1. Validar token de seguridad
        $this->validar_token_simple();

        // 2. Obtener campos enviados por POST
        $persona_id = isset($_POST['persona_id']) ? intval($_POST['persona_id']) : 0;
        $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;
        $clave = isset($_POST['clave']) && trim($_POST['clave']) !== '' ? trim($_POST['clave']) : '123456';

        // 3. Validaciones
        if ($persona_id <= 0) {
            $this->_error('Debe seleccionar una persona válida');
        }
        if ($rol <= 0) {
            $this->_error('Debe seleccionar un rol válido');
        }

        // 4. Verificar existencia
        $sql_verificar = "SELECT COUNT(*) FROM admin_usuario WHERE persona_id = ?";
        $existe = $this->_obtener_valor($sql_verificar, array($persona_id));
        if ($existe > 0) {
            $this->_error('Esta persona ya tiene un usuario asignado');
        }

        // 5. Obtener datos de la persona
        $persona = $this->_obtener_fila("SELECT * FROM persona WHERE id = ?", array($persona_id));
        if (empty($persona)) {
            $this->_error('Persona no encontrada');
        }

        $login = !empty($persona['user']) ? strtoupper($persona['user']) : strtoupper($persona['identificacion']);
        $password_hash = password_hash($clave, PASSWORD_BCRYPT, ['cost' => 10]);

        // 6. Insertar en admin_usuario
        $datos_admin = array(
            'persona_id' => $persona_id,
            'rol_id' => $rol,
            '_usuario' => $persona['identificacion']
        );
        $this->db->insert('admin_usuario', $datos_admin);

        // 7. Insertar / Actualizar en usuario
        $this->db->getConnection()->statement(
            "INSERT INTO usuario (persona_id, rol_id, login, password_hash, activo) 
             VALUES (?, ?, ?, ?, 1) 
             ON DUPLICATE KEY UPDATE rol_id = VALUES(rol_id), password_hash = VALUES(password_hash), activo = 1",
            array($persona_id, $rol, $login, $password_hash)
        );

        // 8. Responder éxito
        $this->_success('Usuario creado correctamente con clave inicial');
    }

    // --------------------------------------------------------
    // MODIFICAR — actualiza el rol o clave de un usuario existente
    // --------------------------------------------------------
    function modificar()
    {
        // 1. Validar token de seguridad
        $this->validar_token_simple();

        // 2. Obtener campos
        $persona_id = isset($_POST['persona_id']) ? intval($_POST['persona_id']) : 0;
        $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;
        $clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

        // 3. Validaciones
        if ($persona_id <= 0) {
            $this->_error('Debe seleccionar una persona válida');
        }
        if ($rol <= 0) {
            $this->_error('Debe seleccionar un rol válido');
        }

        // 4. Actualizar admin_usuario
        $this->db->getConnection()->update(
            "UPDATE admin_usuario SET rol_id = ? WHERE persona_id = ?",
            array($rol, $persona_id)
        );

        // 5. Actualizar usuario
        if ($clave !== '') {
            $password_hash = password_hash($clave, PASSWORD_BCRYPT, ['cost' => 10]);
            $this->db->getConnection()->update(
                "UPDATE usuario SET rol_id = ?, password_hash = ? WHERE persona_id = ?",
                array($rol, $password_hash, $persona_id)
            );
        } else {
            $this->db->getConnection()->update(
                "UPDATE usuario SET rol_id = ? WHERE persona_id = ?",
                array($rol, $persona_id)
            );
        }

        // 6. Responder éxito
        $this->_success('Usuario actualizado correctamente');
    }

    // --------------------------------------------------------
    // ELIMINAR — borra el usuario
    // --------------------------------------------------------
    function eliminar()
    {
        // 1. Validar token
        $this->validar_token_simple();

        // 2. Obtener ID
        $persona_id = isset($_POST['persona_id']) ? intval($_POST['persona_id']) : 0;

        if ($persona_id <= 0) {
            $this->_error('Identificador de usuario no válido');
        }

        // 3. No permitir eliminar al usuario ID 1 (admin principal)
        if ($persona_id === 1) {
            $this->_error('No se puede eliminar al usuario administrador principal');
        }

        // 4. Borrar registros
        $this->db->getConnection()->delete("DELETE FROM admin_usuario WHERE persona_id = ?", array($persona_id));
        $this->db->getConnection()->delete("DELETE FROM usuario WHERE persona_id = ?", array($persona_id));

        // 5. Responder éxito
        $this->_success('Usuario eliminado correctamente');
    }

    // --------------------------------------------------------
    // ASIGNAR — consulta un usuario por persona_id para edición
    // --------------------------------------------------------
    function asignar()
    {
        $this->validar_token_simple();

        $persona_id = isset($_POST['persona_id']) ? intval($_POST['persona_id']) : (isset($_GET['persona_id']) ? intval($_GET['persona_id']) : 0);

        if ($persona_id <= 0) {
            $this->_error('Identificador de usuario no válido');
        }

        $sql = "SELECT
                    u.persona_id,
                    u.rol_id AS rol,
                    p.identificacion,
                    p.user AS login,
                    CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) AS persona_nombre
                FROM admin_usuario u
                INNER JOIN persona p ON p.id = u.persona_id
                WHERE u.persona_id = ?";

        $usuario = $this->_obtener_fila($sql, array($persona_id));

        if (empty($usuario)) {
            $this->_error('Usuario no encontrado');
        }

        $this->_success('Datos del usuario', $usuario);
    }

    // --------------------------------------------------------
    // LISTAR_PERSONAS — personas que aún NO tienen usuario asignado
    // --------------------------------------------------------
    function listarPersonasSinUsuario()
    {
        $sql = "SELECT
                    p.id,
                    CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2, ' [', p.identificacion, ']') AS text
                FROM persona p
                WHERE p.id NOT IN (SELECT persona_id FROM admin_usuario)
                ORDER BY p.nombre1, p.apellido1";

        $personas = $this->_consultar($sql);
        $this->_success('Personas disponibles', $personas);
    }

    // --------------------------------------------------------
    // LISTAR_ROLES — lista todos los roles activos
    // --------------------------------------------------------
    function listarRoles()
    {
        $sql = "SELECT id, nombre FROM admin_rol WHERE visible = 'S' ORDER BY nivel DESC, nombre ASC";
        $roles = $this->_consultar($sql);
        $this->_success('Listado de roles', $roles);
    }
}
