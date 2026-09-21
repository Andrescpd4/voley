<?php
// ============================================================
// PERMISOS POR ROL — Backend compatible con logistics/index.php
//
// Formato respuesta cargar(): array plano merge de:
//   {"menu": "slug"} para menus
//   {"accion": id} para acciones
//
// Metodos:
//   - cargar()  : Devuelve array merge (menu/accion) del rol
//   - listar()  : Alias de cargar()
//   - guardar(): Guarda menus[] y accion[] (value='S')
//
// Protecciones:
//   - Token HTTP_AUTHORIZATION
//   - intval() en IDs
//   - Super Admin (nivel >= 90) no puede perder 'permisos-por-rol'
//   - Usuario no puede quitarse su propio 'permisos-por-rol'
// ============================================================

require_once("php/clase_base.php");

class PermisosRol extends clase_base
{
    /**
     * Cargar permisos del rol - formato logistics (array plano merge)
     */
    function cargar()
    {
        // 1. Validar token de seguridad
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Error en TOKEN de seguridad';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        // 2. Validar que venga el rol
        if (!isset($_POST['rol']) || $_POST['rol'] == '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe seleccionar un rol';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $rol_id = intval($_POST['rol']);

        // 3. Obtener menus del rol
        $sql = "SELECT menu FROM admin_permiso_menu WHERE rol = " . $rol_id;
        $menus = $this->db->select_all($sql);

        // 4. Obtener acciones del rol
        $sql = "SELECT accion FROM admin_permiso_accion WHERE rol = " . $rol_id;
        $acciones = $this->db->select_all($sql);

        // 5. Merge para compatibilidad con logistics frontend (array plano)
        $result = array_merge($menus, $acciones);

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Alias de cargar()
     */
    function listar()
    {
        $this->cargar();
    }

    /**
     * Guardar permisos - recibe menu[slug]='S' y accion[id]='S'
     */
    function guardar()
    {
        // 1. Validar token
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Error en TOKEN de seguridad';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        // 2. Validar rol
        if (!isset($_POST['rol']) || $_POST['rol'] == '') {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'Debe seleccionar un rol';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        $rol_id = intval($_POST['rol']);

        // 3. Verificar rol existe y nivel
        $rol = $this->db->select_row("SELECT id, nombre, nivel FROM admin_rol WHERE id = " . $rol_id);
        if (!$rol) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'El rol seleccionado no existe';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        // 4. Menus seleccionados (array key=slug, value='S')
        $menus_sel = array();
        if (isset($_POST['menu']) && is_array($_POST['menu'])) {
            foreach ($_POST['menu'] as $slug => $val) {
                if ($val == 'S') {
                    $menus_sel[] = $this->db->escape_string($slug);
                }
            }
        }

        // 5. Acciones seleccionadas (array key=id, value='S')
        $acciones_sel = array();
        if (isset($_POST['accion']) && is_array($_POST['accion'])) {
            foreach ($_POST['accion'] as $id => $val) {
                if ($val == 'S') {
                    $acciones_sel[] = intval($id);
                }
            }
        }

        // 6. PROTECCION: Super Admin (nivel >= 90) debe tener permisos-por-rol
        $nivel_rol = intval($rol['nivel'] ?? 0);
        $tiene_permisos_menu = in_array('permisos-por-rol', $menus_sel);
        $usuario_rol = intval($_SESSION['usuario_rol'] ?? 0);

        if ($nivel_rol >= 90 && !$tiene_permisos_menu) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'El rol Super Administrador (nivel >= 90) siempre debe tener acceso a "Permisos por Rol" para evitar bloqueo del sistema.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        // 7. PROTECCION: Auto-lockout (usuario no puede quitarse su propio acceso)
        if ($rol_id == $usuario_rol && !$tiene_permisos_menu) {
            $r = array();
            $r['error'] = true;
            $r['msg'] = 'No puede quitarse el acceso a "Permisos por Rol" a su propio rol. Le bloquearia del sistema.';
            echo json_encode($r, JSON_UNESCAPED_UNICODE);
            return;
        }

        // 8. Eliminar anteriores
        $this->db->query("DELETE FROM admin_permiso_menu WHERE rol = " . $rol_id);
        $this->db->query("DELETE FROM admin_permiso_accion WHERE rol = " . $rol_id);

        // 9. Insertar menus
        for ($i = 0; $i < count($menus_sel); $i++) {
            $datos = array('rol' => $rol_id, 'menu' => $menus_sel[$i]);
            $this->db->insert('admin_permiso_menu', $datos);
        }

        // 10. Insertar acciones
        for ($i = 0; $i < count($acciones_sel); $i++) {
            $datos = array('rol' => $rol_id, 'accion' => $acciones_sel[$i]);
            $this->db->insert('admin_permiso_accion', $datos);
        }

        // 11. Respuesta éxito (formato logistics)
        $r = array();
        $r['error'] = false;
        $r['msg'] = 'Permisos guardados correctamente para el rol: ' . $rol['nombre'];
        echo json_encode($r, JSON_UNESCAPED_UNICODE);
    }
}

$accion = ACCION;
$f = new PermisosRol();
$f->$accion();
?>