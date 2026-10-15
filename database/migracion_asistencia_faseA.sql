-- ============================================================
-- VOLEY+ Fase A — Asistencia con escaneo (entrada/salida)
-- Fecha: 2026-10-15
-- Uso: ejecutar UNA vez en phpMyAdmin (local y servidor).
-- Tablas nuevas: ninguna. Solo columnas y acciones/permisos.
-- ============================================================

-- 1. Clase: estado de la sesion y cierre + borrado suave
ALTER TABLE `clase`
  ADD COLUMN IF NOT EXISTS `estado` ENUM('abierta','cerrada') NOT NULL DEFAULT 'abierta',
  ADD COLUMN IF NOT EXISTS `fecha_cierre` DATETIME NULL,
  ADD COLUMN IF NOT EXISTS `activo` TINYINT(1) NOT NULL DEFAULT 1;

-- 2. Asistencia: horas de entrada/salida y origen de la marca
ALTER TABLE `asistencia`
  ADD COLUMN IF NOT EXISTS `hora_entrada` DATETIME NULL,
  ADD COLUMN IF NOT EXISTS `hora_salida` DATETIME NULL,
  ADD COLUMN IF NOT EXISTS `minutos` INT NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `origen` ENUM('qr','manual','cierre') NOT NULL DEFAULT 'qr';

-- 3. Acciones del menu asistencia (pagina + json)
INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`) VALUES
('asistencia', 'ver', 'pagina', 'formulario.php', 'S', 'Ver el modulo de asistencia', 1),
('asistencia', 'listar_clases', 'json', 'acciones.php', 'N', 'Listar clases o entrenamientos', 2),
('asistencia', 'guardar_clase', 'json', 'acciones.php', 'S', 'Crear o editar una clase', 3),
('asistencia', 'eliminar_clase', 'json', 'acciones.php', 'S', 'Eliminar una clase sin registros', 4),
('asistencia', 'listar_categorias', 'json', 'acciones.php', 'N', 'Listar categorias activas', 5),
('asistencia', 'sesion_en_vivo', 'json', 'acciones.php', 'N', 'Ver presentes y pendientes de una clase', 6),
('asistencia', 'marcar', 'json', 'acciones.php', 'S', 'Marcar entrada o salida por documento', 7),
('asistencia', 'marcar_excusa', 'json', 'acciones.php', 'S', 'Marcar excusa a una deportista', 8),
('asistencia', 'finalizar', 'json', 'acciones.php', 'S', 'Cerrar la clase y marcar inasistencias', 9),
('asistencia', 'consolidado', 'json', 'acciones.php', 'N', 'Consolidado de asistencia por periodo', 10),
('asistencia', 'resumen_hijos', 'json', 'acciones.php', 'N', 'Resumen de asistencia para el acudiente', 11),
('asistencia', 'iniciar_jornada', 'json', 'acciones.php', 'S', 'Iniciar la jornada de hoy con tema y categoria', 12);

-- 4. El menu lo ven admin, superadmin, entrenador y acudiente
INSERT IGNORE INTO `admin_permiso_menu` (`rol`, `menu`) VALUES
(1, 'asistencia'),
(4, 'asistencia'),
(2, 'asistencia'),
(3, 'asistencia');

-- 5. Permisos de acciones para admin (1) y superadmin (4): todo
INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 1, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'asistencia'
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 1 AND p.`accion` = a.`id`);

INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 4, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'asistencia'
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 4 AND p.`accion` = a.`id`);

-- 6. Permisos para entrenador (2): todo el operativo
INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 2, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'asistencia'
AND a.`accion` IN ('ver', 'listar_clases', 'guardar_clase', 'eliminar_clase', 'listar_categorias', 'sesion_en_vivo', 'marcar', 'marcar_excusa', 'finalizar', 'consolidado', 'iniciar_jornada')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 2 AND p.`accion` = a.`id`);

-- 7. Permisos para acudiente (3): solo abrir el modulo y ver a sus hijos
INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 3, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'asistencia'
AND a.`accion` IN ('ver', 'resumen_hijos')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 3 AND p.`accion` = a.`id`);
