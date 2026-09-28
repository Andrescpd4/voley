-- ============================================================
-- ACCIONES Y PERMISOS PARA MODULO AFILIACION (3 TABS)
-- Ejecutar en la base de datos voley_plus
-- ============================================================

-- 1. LIMPIAR ACCIONES ANTIGUAS Y DUPLICADOS DE AFILIACION (excepto la acción 47 'ver')
DELETE FROM `admin_permiso_accion` WHERE `accion` IN (SELECT `id` FROM `admin_accion` WHERE `menu` = 'afiliacion' AND `id` != 47);
DELETE FROM `admin_accion` WHERE `menu` = 'afiliacion' AND `id` != 47;

-- 2. INSERTAR NUEVAS ACCIONES (IDs 55-68)
-- Tab 3: Administracion (Admin - roles 1, 4)
INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`) VALUES
(55, 'afiliacion', 'listar_gestion', 'json', 'acciones.php', 'S', 'Listar solicitudes para DataTable admin', 10, NOW()),
(56, 'afiliacion', 'asignar_gestion', 'json', 'acciones.php', 'S', 'Obtener detalle solicitud para modal Ver', 20, NOW()),
(57, 'afiliacion', 'modificar_gestion', 'json', 'acciones.php', 'S', 'Cambiar estado + observaciones admin', 30, NOW()),
(58, 'afiliacion', 'eliminar_gestion', 'json', 'acciones.php', 'S', 'Eliminar solicitud (soft delete)', 40, NOW()),

-- Tab 1: Registro (Acudiente - rol 3)
(59, 'afiliacion', 'crear_registro_acudiente', 'json', 'acciones.php', 'S', 'Crear persona + deportista + acudiente + docs', 50, NOW()),
(60, 'afiliacion', 'actualizar_registro_acudiente', 'json', 'acciones.php', 'S', 'Editar borrador existente', 60, NOW()),
(61, 'afiliacion', 'subir_documento_acudiente', 'json', 'acciones.php', 'S', 'Subir documento por tipo_documento', 70, NOW()),
(62, 'afiliacion', 'eliminar_documento_acudiente', 'json', 'acciones.php', 'S', 'Eliminar documento propio', 80, NOW()),

-- Tab 2: Mis Solicitudes (Acudiente - rol 3)
(63, 'afiliacion', 'listar_mis_solicitudes', 'json', 'acciones.php', 'S', 'Listar solicitudes del acudiente', 90, NOW()),
(64, 'afiliacion', 'obtener_mis_solicitud', 'json', 'acciones.php', 'S', 'Detalle solicitud para modal Ver', 100, NOW()),

-- Compartidas
(65, 'afiliacion', 'listar_categorias', 'json', 'acciones.php', 'S', 'Select categorias activas', 110, NOW()),
(66, 'afiliacion', 'listar_tipos_documento', 'json', 'acciones.php', 'S', 'Select tipos documento activos', 120, NOW()),
(67, 'afiliacion', 'listar_acudientes_admin', 'json', 'acciones.php', 'S', 'Select acudientes para admin', 130, NOW()),
(68, 'afiliacion', 'listar_deportistas_admin', 'json', 'acciones.php', 'S', 'Select deportistas para admin', 140, NOW());

-- 3. PERMISOS ROL 1 (Admin) - TODAS las acciones
INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(1, 55), (1, 56), (1, 57), (1, 58),
(1, 59), (1, 60), (1, 61), (1, 62),
(1, 63), (1, 64),
(1, 65), (1, 66), (1, 67), (1, 68);

-- 4. PERMISOS ROL 4 (SuperAdmin) - TODAS las acciones
INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(4, 55), (4, 56), (4, 57), (4, 58),
(4, 59), (4, 60), (4, 61), (4, 62),
(4, 63), (4, 64),
(4, 65), (4, 66), (4, 67), (4, 68);

-- 5. PERMISOS ROL 3 (Acudiente) - Sus tabs + compartidas
INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(3, 59), (3, 60), (3, 61), (3, 62),  -- Tab 1: Registro
(3, 63), (3, 64),                     -- Tab 2: Mis Solicitudes
(3, 65), (3, 66);                     -- Compartidas: categorias, tipos_doc

-- 6. PERMISO DE MENU 'afiliacion'
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (1, 'afiliacion');
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (4, 'afiliacion');
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (3, 'afiliacion');
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (1, 'escuela');
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (4, 'escuela');
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (3, 'escuela');
