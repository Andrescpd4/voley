-- ============================================================
-- VOLEY+ Fix muro social (comunicados en inicio)
-- Fecha: 2026-10-15
-- Uso: ejecutar en phpMyAdmin del servidor (es idempotente,
-- se puede correr varias veces sin duplicar nada).
--
-- Corrige: likes, comentarios, lectura, publicar y eliminar
-- no funcionaban por permisos faltantes (roles 2, 3, 4).
-- El codigo tambien se corrigio en modulos/inicio/acciones.php
-- (conteos COUNT con _a_numero en vez de is_string).
-- ============================================================

-- 1. Tablas del muro (solo se crean si faltan)
CREATE TABLE IF NOT EXISTS `comunicado_like` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `comunicado_id` INT NOT NULL,
    `persona_id` INT NOT NULL,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_com_per` (`comunicado_id`, `persona_id`),
    CONSTRAINT `fk_like_comunicado` FOREIGN KEY (`comunicado_id`) REFERENCES `comunicado`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_like_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comunicado_comentario` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `comunicado_id` INT NOT NULL,
    `persona_id` INT NOT NULL,
    `comentario` TEXT NOT NULL,
    `visible` TINYINT(1) DEFAULT 1,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_com_comunicado` FOREIGN KEY (`comunicado_id`) REFERENCES `comunicado`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_com_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Acciones del muro (solo se insertan si faltan, sin IDs fijos)
INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'feed', 'json', 'acciones.php', 'S', 'Muro: listar publicaciones visibles', 10, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'feed');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'toggle_like', 'json', 'acciones.php', 'S', 'Muro: dar o quitar Me gusta', 20, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'toggle_like');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'comentar', 'json', 'acciones.php', 'S', 'Muro: agregar comentario', 30, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'comentar');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'marcar_leido', 'json', 'acciones.php', 'S', 'Muro: confirmar lectura', 40, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'marcar_leido');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'publicar', 'json', 'acciones.php', 'S', 'Muro: publicar comunicado (admin)', 50, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'publicar');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'eliminar_publicacion', 'json', 'acciones.php', 'S', 'Muro: eliminar publicacion (admin)', 60, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'eliminar_publicacion');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'categorias', 'json', 'acciones.php', 'S', 'Muro: select de categorias activas', 70, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'categorias');

INSERT INTO `admin_accion` (`menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`)
SELECT 'inicio', 'contexto', 'json', 'acciones.php', 'S', 'Muro: contexto y accesos del usuario', 80, NOW()
WHERE NOT EXISTS (SELECT 1 FROM `admin_accion` WHERE `menu` = 'inicio' AND `accion` = 'contexto');

-- 3. Permisos de lectura e interaccion (roles 1, 2, 3, 4)
INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 1, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'inicio' AND a.`accion` IN ('feed', 'toggle_like', 'comentar', 'marcar_leido', 'categorias', 'contexto')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 1 AND p.`accion` = a.`id`);

INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 2, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'inicio' AND a.`accion` IN ('feed', 'toggle_like', 'comentar', 'marcar_leido', 'categorias', 'contexto')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 2 AND p.`accion` = a.`id`);

INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 3, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'inicio' AND a.`accion` IN ('feed', 'toggle_like', 'comentar', 'marcar_leido', 'categorias', 'contexto')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 3 AND p.`accion` = a.`id`);

INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 4, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'inicio' AND a.`accion` IN ('feed', 'toggle_like', 'comentar', 'marcar_leido', 'categorias', 'contexto')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 4 AND p.`accion` = a.`id`);

-- 4. Permisos de publicar y eliminar (solo administradores 1 y 4)
INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 1, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'inicio' AND a.`accion` IN ('publicar', 'eliminar_publicacion')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 1 AND p.`accion` = a.`id`);

INSERT INTO `admin_permiso_accion` (`rol`, `accion`)
SELECT 4, a.`id` FROM `admin_accion` a
WHERE a.`menu` = 'inicio' AND a.`accion` IN ('publicar', 'eliminar_publicacion')
AND NOT EXISTS (SELECT 1 FROM `admin_permiso_accion` p WHERE p.`rol` = 4 AND p.`accion` = a.`id`);
