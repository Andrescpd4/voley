-- ============================================================
-- ACTUALIZAR ACCIONES - VOLEY PLUS
-- ============================================================
-- 1. Agregar acciones faltantes para roles (agregar, modificar, eliminar, asignar)
-- 2. Agregar acciones para permisos-rol (cargar, guardar)
-- 3. Crear menús para el asistente (asistente, asistente-crud, asistente-menu)
-- 4. Agregar acciones para los menús del asistente
-- 5. Registrar permisos del rol admin (id=1) a todos los nuevos menús y acciones
-- ============================================================

SET NAMES utf8;

-- ============================================================
-- 1. ACCIONES FALTANTES PARA ROLES
-- ============================================================

-- Agregar accion 'agregar' a roles (si no existe)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'roles', 'agregar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'roles' AND accion = 'agregar');

-- Agregar accion 'modificar' a roles (si no existe)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'roles', 'modificar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'roles' AND accion = 'modificar');

-- Agregar accion 'eliminar' a roles (si no existe)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'roles', 'eliminar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'roles' AND accion = 'eliminar');

-- Agregar accion 'asignar' a roles (si no existe)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'roles', 'asignar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'roles' AND accion = 'asignar');

-- ============================================================
-- 2. ACCIONES FALTANTES PARA PERMISOS-ROL
-- ============================================================

-- Agregar accion 'cargar' a permisos-rol (si no existe)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'permisos-rol', 'cargar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'permisos-rol' AND accion = 'cargar');

-- Agregar accion 'guardar' a permisos-rol (si no existe)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'permisos-rol', 'guardar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'permisos-rol' AND accion = 'guardar');

-- ============================================================
-- 3. CREAR MENÚS PARA EL ASISTENTE
-- ============================================================

-- Menú padre: asistente (si no existe)
INSERT IGNORE INTO admin_menu (menu, padre, nombre, ruta, accion, orden, visible, acceso, icono)
VALUES ('asistente', NULL, 'Asistente', '#', 'ver', 14, 'S', '7', 'ri-robot-line');

-- Sub-menú: asistente-crud (si no existe)
INSERT IGNORE INTO admin_menu (menu, padre, nombre, ruta, accion, orden, visible, acceso, icono)
VALUES ('asistente-crud', 'asistente', 'CRUD Asistente', 'modulos/admin/asistente-crud', 'ver', 15, 'S', '7', 'ri-database-2-line');

-- Sub-menú: asistente-menu (si no existe)
INSERT IGNORE INTO admin_menu (menu, padre, nombre, ruta, accion, orden, visible, acceso, icono)
VALUES ('asistente-menu', 'asistente', 'Menú Asistente', 'modulos/admin/asistente-menu', 'ver', 16, 'S', '7', 'ri-menu-2-line');

-- ============================================================
-- 4. AGREGAR ACCIONES PARA LOS MENÚS DEL ASISTENTE
-- ============================================================

-- Acciones para asistente (menú principal - solo ver)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente', 'ver', 'pagina', 'formulario.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente' AND accion = 'ver');

-- Acciones para asistente-crud (CRUD completo)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-crud', 'ver', 'pagina', 'formulario.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-crud' AND accion = 'ver');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-crud', 'listar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-crud' AND accion = 'listar');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-crud', 'agregar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-crud' AND accion = 'agregar');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-crud', 'modificar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-crud' AND accion = 'modificar');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-crud', 'eliminar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-crud' AND accion = 'eliminar');

-- Acciones para asistente-menu (gestión de menús)
INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-menu', 'ver', 'pagina', 'formulario.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-menu' AND accion = 'ver');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-menu', 'listar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-menu' AND accion = 'listar');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-menu', 'agregar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-menu' AND accion = 'agregar');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-menu', 'modificar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-menu' AND accion = 'modificar');

INSERT IGNORE INTO admin_accion (menu, accion, tipo_accion, archivo, requiere_permiso)
SELECT 'asistente-menu', 'eliminar', 'json', 'acciones.php', 'S'
WHERE NOT EXISTS (SELECT 1 FROM admin_accion WHERE menu = 'asistente-menu' AND accion = 'eliminar');

-- ============================================================
-- 5. REGISTRAR PERMISOS DEL ROL ADMIN (id=1) A TODOS LOS MENÚS Y ACCIONES
-- ============================================================

-- Permisos de menú para el rol admin (id=1)
INSERT IGNORE INTO admin_permiso_menu (rol, menu) VALUES (1, 'asistente');
INSERT IGNORE INTO admin_permiso_menu (rol, menu) VALUES (1, 'asistente-crud');
INSERT IGNORE INTO admin_permiso_menu (rol, menu) VALUES (1, 'asistente-menu');

-- Permisos de acciones para roles (rol=1)
INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE menu = 'roles' AND accion IN ('ver', 'listar', 'agregar', 'modificar', 'eliminar', 'asignar');

-- Permisos de acciones para permisos-rol (rol=1)
INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE menu = 'permisos-rol' AND accion IN ('ver', 'listar', 'cargar', 'guardar');

-- Permisos de acciones para asistente (rol=1)
INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE menu = 'asistente' AND accion = 'ver';

-- Permisos de acciones para asistente-crud (rol=1)
INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE menu = 'asistente-crud' AND accion IN ('ver', 'listar', 'agregar', 'modificar', 'eliminar');

-- Permisos de acciones para asistente-menu (rol=1)
INSERT IGNORE INTO admin_permiso_accion (rol, accion)
SELECT 1, id FROM admin_accion WHERE menu = 'asistente-menu' AND accion IN ('ver', 'listar', 'agregar', 'modificar', 'eliminar');

-- ============================================================
-- VERIFICACIÓN
-- ============================================================
SELECT '--- VERIFICACIÓN: Acciones por menú ---' AS '';
SELECT menu, accion, archivo FROM admin_accion WHERE menu IN ('roles','permisos-rol','asistente','asistente-crud','asistente-menu') ORDER BY menu, accion;
