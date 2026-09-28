-- ============================================================
-- NUEVAS ACCIONES PARA MODULO AFILIACION (Tabs)
-- Ejecutar en la base de datos voley_plus
-- Columnas correctas: id, menu, accion, tipo_accion, archivo, requiere_permiso, descripcion, orden, fecha
-- ============================================================

-- 1. Insertar nuevas acciones en admin_accion
-- IDs 55-62 (continuacion despues del 54 actual)

INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`) VALUES
(55, 'afiliacion', 'listar_gestion', 'json', 'acciones.php', 'S', NULL, 10, NOW()),
(56, 'afiliacion', 'agregar_gestion', 'json', 'acciones.php', 'S', NULL, 20, NOW()),
(57, 'afiliacion', 'modificar_gestion', 'json', 'acciones.php', 'S', NULL, 30, NOW()),
(58, 'afiliacion', 'eliminar_gestion', 'json', 'acciones.php', 'S', NULL, 40, NOW()),
(59, 'afiliacion', 'asignar_gestion', 'json', 'acciones.php', 'S', NULL, 50, NOW()),
(60, 'afiliacion', 'listar_mis_afiliaciones', 'json', 'acciones.php', 'S', NULL, 60, NOW()),
(61, 'afiliacion', 'subir_pdf_mis_afiliaciones', 'json', 'acciones.php', 'S', NULL, 70, NOW()),
(62, 'afiliacion', 'obtener_mis_afiliaciones', 'json', 'acciones.php', 'S', NULL, 80, NOW());

-- 2. Permisos para Rol 1 (Admin) - Todas las acciones nuevas
INSERT INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(1, 55), (1, 56), (1, 57), (1, 58), (1, 59), (1, 60), (1, 61), (1, 62);

-- 3. Permisos para Rol 4 (SuperAdmin) - Todas las acciones nuevas
INSERT INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(4, 55), (4, 56), (4, 57), (4, 58), (4, 59), (4, 60), (4, 61), (4, 62);

-- 4. Permisos para Rol 3 (Acudiente) - Solo acciones de "Mis Afiliaciones"
INSERT INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(3, 60), (3, 61), (3, 62);

-- 5. Verificar que el menu 'afiliacion' tenga acceso para rol 3 (ya existe en admin_permiso_menu linea 927)
-- INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES (3, 'afiliacion');

-- ============================================================
-- NOTAS:
-- - Acciones 55-59: Para Tab "Gestion" (Admin - roles 1, 4)
-- - Acciones 60-62: Para Tab "Mis Afiliaciones" (Acudiente - rol 3)
-- - listarAcudientes (53) y listarDeportistas (54) son compartidos, ya tienen permisos para roles 1,4
--   Si rol 3 necesita crear solicitudes desde su tab, agregar permisos para 53, 54
-- ============================================================