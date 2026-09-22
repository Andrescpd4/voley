<?php
// ============================================================
// MENU.PHP — Genera el menu lateral del sistema
//
// Lee de admin_menu y admin_permiso_menu para mostrar solo
// los menus que el rol actual puede ver.
//
// generarMenu("")    -> menus padres (sin padre)
// generarMenu("admin") -> hijos de admin, etc.
// ============================================================

$menu_items = NULL;
$menu_padre_hijos = array();

$acceso = "'" . implode("','", $_SESSION['acceso_menu'] ?? array(1, 2)) . "'";

$sql = "SELECT
            m.*,
            (SELECT COUNT(*) FROM admin_menu WHERE padre = m.menu) as hijos,
            (SELECT 'S' FROM admin_permiso_menu p, admin_usuario u
             WHERE (u.rol = p.rol AND p.menu = m.menu AND u.persona_id = '" . ($_SESSION['persona_id'] ?? 0) . "')
                OR (p.rol = '" . ($_SESSION['usuario_rol'] ?? 0) . "' AND p.menu = m.menu) LIMIT 1) as disponible
        FROM admin_menu m
        WHERE m.visible = 'S' AND m.acceso IN ($acceso)
        ORDER BY m.orden, m.nombre";

$menu_items = $db->select_all($sql);

function generarMenu($padre)
{
    global $menu_items;

    if ($padre != "") {
        echo ' <div class="collapse menu-dropdown" id="' . $padre . '"><ul class="nav nav-sm flex-column">';
    }

    foreach ($menu_items as $rw) {
        $padre_rw = $rw['padre'] ?? "";
        if ($padre_rw != $padre) {
            continue;
        }

        if ($rw['hijos'] == 0) {
            $href = WEB_ROOT . $rw['menu'];
        } else {
            $href = "#" . $rw['menu'];
        }

        // Verificar si es menu con acceso por rol (acceso 7 requiere permiso explícito)
        if ($rw['acceso'] == "7" && ($rw['disponible'] ?? '') != "S") {
            continue;
        }

        if ($rw['hijos'] > 0) {
            echo "<li class='nav-item'>";
            echo '<a class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="' . $rw["menu"] . '" href="' . $href . '">';
            echo '<i class="' . ($rw["icono"] ?? '') . '"></i> <span data-key="t-' . $rw['menu'] . '">' . $rw["nombre"] . '</span>';
            echo '</a>';
            generarMenu($rw['menu']);
            echo "</li>";
        } else {
            echo "<li class='nav-item'>";
            echo "<a href='$href' target='" . ($rw['_self'] ?? '_self') . "' class='nav-link menu-link'>";
            echo '<i class="' . ($rw['icono'] ?? '') . '"></i> <span data-key="t-' . $rw['menu'] . '">' . $rw['nombre'] . '</span>';
            echo "</a>";
            echo "</li>";
        }
    }

    if ($padre != "") {
        echo "</ul></div>";
    }
}
