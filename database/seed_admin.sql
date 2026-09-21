-- ============================================================
-- DATOS INICIALES / SEED — VOLEY+
-- ============================================================
USE `voley_plus`;

-- 1. Asegurar roles
INSERT IGNORE INTO `admin_rol` (`id`, `nombre`, `slug`, `nivel`, `visible`) VALUES
(1, 'Administrador Voley+', 'admin', 80, 'S'),
(2, 'Entrenador', 'entrenador', 50, 'S'),
(3, 'Acudiente / Padre', 'acudiente', 10, 'S'),
(4, 'Super Administrador', 'superadmin', 100, 'S');

-- 2. Asegurar tipos de acción
INSERT IGNORE INTO `admin_tipo_accion` (`id`, `codigo`, `nombre`, `archivo`) VALUES
(1, 'pagina', 'Página Web', 'pagina.php'),
(2, 'json', 'Petición JSON (AJAX)', 'descarga.php'),
(3, 'html', 'Fragmento HTML', 'descarga.php');

-- 3. Persona y Usuario Admin (Password: admin123)
INSERT IGNORE INTO `persona` (`id`, `tipo_documento`, `identificacion`, `user`, `nombre1`, `apellido1`, `correo`) VALUES
(1, 'CC', '10000001', 'ADMIN', 'Super', 'Admin', 'admin@voleyplus.com');

INSERT INTO `usuario` (`id`, `persona_id`, `rol_id`, `login`, `password_hash`, `activo`) VALUES
(1, 1, 4, 'ADMIN', '$2y$10$eE2lVlF83p/9JgZJq04sK.c2y1fVjHqUfC6eR4U3zPjDq1f3o7SgG', 1)
ON DUPLICATE KEY UPDATE `password_hash` = '$2y$10$eE2lVlF83p/9JgZJq04sK.c2y1fVjHqUfC6eR4U3zPjDq1f3o7SgG', `activo` = 1;

INSERT IGNORE INTO `admin_usuario` (`id`, `persona_id`, `rol_id`, `_usuario`) VALUES
(1, 1, 4, '10000001');
