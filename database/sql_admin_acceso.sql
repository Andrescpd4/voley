-- ============================================================
-- VOLEY+ — Recrear control de accesos igual que logistics
-- Ejecutar UNA vez en cada base (local voley_plus y servidor)
-- 1. Catalogo admin_acceso (solo lo usan los selects del
--    constructor de menus + el CRUD Niveles de acceso)
-- 2. FK admin_menu.acceso -> admin_acceso.codigo (igual que logistics)
-- 3. Menu admin-acceso + 6 acciones + permisos roles 1 y 4
-- ============================================================

-- 1. Tabla catalogo (charset igual que admin_menu para que el FK funcione)
CREATE TABLE IF NOT EXISTS `admin_acceso` (
    `codigo` CHAR(1) NOT NULL PRIMARY KEY,
    `descripcion` VARCHAR(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Seeds identicos a logistics
INSERT IGNORE INTO `admin_acceso` (`codigo`, `descripcion`) VALUES
('1', 'Publico - Todos (Sin loguear y logueados)'),
('2', 'Solo usuarios sin loguear'),
('3', 'Solo usuarios logueados'),
('4', 'Estudiantes'),
('5', 'Docentes'),
('6', 'Administrativos'),
('7', 'Asignacion por roles'),
('8', 'Prohibido');

-- 3. FK igual que logistics (admin_menu_fk2). Solo si no existe:
--    Verificar antes: SELECT DISTINCT acceso FROM admin_menu;
--    Todos los valores deben estar entre '1' y '8'.
-- ALTER TABLE `admin_menu` ADD CONSTRAINT `admin_menu_fk_acceso`
-- FOREIGN KEY (`acceso`) REFERENCES `admin_acceso` (`codigo`);

-- 4. Menu del CRUD (id automatico, slug unico)
INSERT IGNORE INTO `admin_menu`
    (`menu`, `padre`, `nombre`, `ruta`, `accion`, `orden`, `visible`, `acceso`, `icono`, `descripcion`)
VALUES
    ('admin-acceso', 'administracion', 'Niveles de acceso',
     'modulos/administracion/admin_acceso', 'ver', 24, 'S', '7',
     'ri-key-line', 'Catalogo de niveles de acceso del sistema');

-- 5. Acciones del CRUD (ver/listar/asignar libres, CUD con permiso)
INSERT IGNORE INTO `admin_accion`
    (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `orden`)
VALUES
    ('admin-acceso', 'ver', 'pagina', 'formulario.php', 'N', 1),
    ('admin-acceso', 'listar', 'json', 'acciones.php', 'N', 2),
    ('admin-acceso', 'asignar', 'json', 'acciones.php', 'N', 3),
    ('admin-acceso', 'agregar', 'json', 'acciones.php', 'S', 4),
    ('admin-acceso', 'modificar', 'json', 'acciones.php', 'S', 5),
    ('admin-acceso', 'eliminar', 'json', 'acciones.php', 'S', 6);

-- 6. Permisos base roles 1 (Admin) y 4 (SuperAdmin)
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`)
VALUES (1, 'admin-acceso'), (4, 'admin-acceso');

INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 1, `id` FROM `admin_accion`
WHERE `menu` = 'admin-acceso' AND `accion` IN ('agregar', 'modificar', 'eliminar');

INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 4, `id` FROM `admin_accion`
WHERE `menu` = 'admin-acceso' AND `accion` IN ('agregar', 'modificar', 'eliminar');

-- 7. Archivos PHP a subir al servidor (copia exacta de logistics):
--    modulos/administracion/admin_acceso/acciones.php
--    modulos/administracion/admin_acceso/formulario.php
