-- ============================================================
-- MURO SOCIAL DEL CLUB (inicio como red social)
-- Ejecutar en la base de datos voley_plus
-- Crea tablas de likes/comentarios + acciones JSON del menu inicio
-- Las acciones 1-3 (ver, set_token, dashboard) quedan intactas en 'N'
-- ============================================================

-- 1. Tabla de Me gusta por publicacion y persona (un like por persona)
CREATE TABLE IF NOT EXISTS `comunicado_like` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `comunicado_id` INT NOT NULL,
    `persona_id` INT NOT NULL,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_com_per` (`comunicado_id`, `persona_id`),
    CONSTRAINT `fk_like_comunicado` FOREIGN KEY (`comunicado_id`) REFERENCES `comunicado`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_like_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de comentarios por publicacion
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

-- 3. Nuevas acciones JSON del menu inicio (IDs 69-74, el MAX actual es 68)
INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`) VALUES
(69, 'inicio', 'feed', 'json', 'acciones.php', 'S', 'Muro: listar publicaciones visibles', 10, NOW()),
(70, 'inicio', 'toggle_like', 'json', 'acciones.php', 'S', 'Muro: dar o quitar Me gusta', 20, NOW()),
(71, 'inicio', 'comentar', 'json', 'acciones.php', 'S', 'Muro: agregar comentario', 30, NOW()),
(72, 'inicio', 'marcar_leido', 'json', 'acciones.php', 'S', 'Muro: confirmar lectura', 40, NOW()),
(73, 'inicio', 'publicar', 'json', 'acciones.php', 'S', 'Muro: publicar comunicado (admin)', 50, NOW()),
(74, 'inicio', 'eliminar_publicacion', 'json', 'acciones.php', 'S', 'Muro: eliminar publicacion (admin)', 60, NOW());

-- 4. Permisos de lectura e interaccion para todos los roles (1, 2, 3, 4)
INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(1, 69), (1, 70), (1, 71), (1, 72),
(2, 69), (2, 70), (2, 71), (2, 72),
(3, 69), (3, 70), (3, 71), (3, 72),
(4, 69), (4, 70), (4, 71), (4, 72);

-- 5. Permisos de publicacion solo para administradores (1, 4)
INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(1, 73), (1, 74),
(4, 73), (4, 74);

-- 6. Lista de categorias para el composer (todos los roles del muro)
INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`) VALUES
(75, 'inicio', 'categorias', 'json', 'acciones.php', 'S', 'Muro: select de categorias activas', 70, NOW());
INSERT IGNORE INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(1, 75), (2, 75), (3, 75), (4, 75);
