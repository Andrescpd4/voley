-- ============================================================
-- SISTEMA VOLEY+ — ESQUEMA COMPLETO DE BASE DE DATOS
-- Base de datos: voley_plus
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS `voley_plus` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `voley_plus`;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. TABLAS DEL SISTEMA DE ADMINISTRACIÓN Y CONTROL DE ACCESO
-- ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_tipo_accion`;
CREATE TABLE `admin_tipo_accion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `nombre` VARCHAR(50) NOT NULL,
    `archivo` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_tipo_accion` (`id`, `codigo`, `nombre`, `archivo`) VALUES
(1, 'pagina', 'Página Web', 'pagina.php'),
(2, 'json', 'Petición JSON (AJAX)', 'descarga.php'),
(3, 'html', 'Fragmento HTML', 'descarga.php');

DROP TABLE IF EXISTS `admin_rol`;
CREATE TABLE `admin_rol` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `nivel` INT DEFAULT 1,
    `visible` CHAR(1) DEFAULT 'S',
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_rol` (`id`, `nombre`, `slug`, `nivel`, `visible`) VALUES
(1, 'Administrador Voley+', 'admin', 80, 'S'),
(2, 'Entrenador', 'entrenador', 50, 'S'),
(3, 'Acudiente / Padre', 'acudiente', 10, 'S'),
(4, 'Super Administrador', 'superadmin', 100, 'S');

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `menu` VARCHAR(50) NOT NULL UNIQUE,
    `padre` VARCHAR(50) DEFAULT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `ruta` VARCHAR(200) NOT NULL DEFAULT '#',
    `accion` VARCHAR(50) NOT NULL DEFAULT 'ver',
    `orden` INT DEFAULT 0,
    `visible` CHAR(1) DEFAULT 'S',
    `acceso` CHAR(1) DEFAULT '7',
    `icono` VARCHAR(50) DEFAULT 'ri-circle-line',
    `_self` VARCHAR(10) DEFAULT '_self'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_menu` (`id`, `menu`, `padre`, `nombre`, `ruta`, `accion`, `orden`, `visible`, `acceso`, `icono`) VALUES
(1, 'inicio', NULL, 'Dashboard', 'modulos/inicio', 'ver', 10, 'S', '3', 'ri-dashboard-line'),
(2, 'iniciar-sesion', NULL, 'Iniciar Sesión', 'modulos/sesion', 'ver', 999, 'N', '1', ''),
(3, 'cerrar-sesion', NULL, 'Cerrar Sesión', 'modulos/sesion', 'cerrarSesion', 999, 'N', '3', ''),
(4, 'perfil', NULL, 'Mi Perfil', 'modulos/admin/usuarios', 'perfil', 998, 'N', '3', 'ri-user-line'),
(10, 'administracion', NULL, 'Administración', '#', 'ver', 20, 'S', '7', 'ri-settings-3-line'),
(11, 'usuarios', 'administracion', 'Usuarios', 'modulos/admin/usuarios', 'ver', 21, 'S', '7', 'ri-user-settings-line'),
(12, 'roles', 'administracion', 'Roles', 'modulos/admin/roles', 'ver', 22, 'S', '7', 'ri-shield-user-line'),
(13, 'permisos-por-rol', 'administracion', 'Permisos por Rol', 'modulos/admin/permisos-rol', 'ver', 23, 'S', '7', 'ri-lock-password-line'),
(20, 'escuela', NULL, 'Escuela Voley', '#', 'ver', 30, 'S', '7', 'ri-medal-line'),
(21, 'afiliacion', 'escuela', 'Afiliaciones', 'modulos/afiliacion', 'ver', 31, 'S', '7', 'ri-user-add-line'),
(22, 'deportistas', 'escuela', 'Ficha Deportistas', 'modulos/escuela/deportistas', 'ver', 32, 'S', '7', 'ri-team-line'),
(23, 'asistencia', 'escuela', 'Control Asistencia', 'modulos/asistencia', 'ver', 33, 'S', '7', 'ri-calendar-check-line'),
(24, 'eventos', 'escuela', 'Eventos y Torneos', 'modulos/eventos', 'ver', 34, 'S', '7', 'ri-trophy-line'),
(25, 'comunicados', 'escuela', 'Comunicados', 'modulos/comunicados', 'ver', 35, 'S', '7', 'ri-broadcast-line');

DROP TABLE IF EXISTS `admin_accion`;
CREATE TABLE `admin_accion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `menu` VARCHAR(50) NOT NULL,
    `accion` VARCHAR(50) NOT NULL,
    `tipo_accion` VARCHAR(20) NOT NULL,
    `archivo` VARCHAR(100) NOT NULL,
    `requiere_permiso` CHAR(1) DEFAULT 'N',
    `orden` INT DEFAULT 0,
    `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `orden`) VALUES
-- Inicio / Dashboard
(1, 'inicio', 'ver', 'pagina', 'formulario.php', 'N', 1),
(2, 'inicio', 'set_token', 'json', 'acciones.php', 'N', 2),
(3, 'inicio', 'dashboard', 'json', 'acciones.php', 'N', 3),

-- Sesion
(4, 'iniciar-sesion', 'ver', 'pagina', 'iniciar_sesion.php', 'N', 1),
(5, 'iniciar-sesion', 'iniciar', 'json', 'acciones.php', 'N', 2),
(6, 'iniciar-sesion', 'set_login', 'json', 'acciones.php', 'N', 3),
(7, 'cerrar-sesion', 'cerrarSesion', 'pagina', 'cerrar_sesion.php', 'N', 1),

-- Admin Usuarios
(10, 'usuarios', 'ver', 'pagina', 'formulario.php', 'N', 1),
(11, 'usuarios', 'listar', 'json', 'acciones.php', 'N', 2),
(12, 'usuarios', 'agregar', 'json', 'acciones.php', 'S', 3),
(13, 'usuarios', 'modificar', 'json', 'acciones.php', 'S', 4),
(14, 'usuarios', 'eliminar', 'json', 'acciones.php', 'S', 5),
(15, 'usuarios', 'asignar', 'json', 'acciones.php', 'N', 6),
(16, 'usuarios', 'listarPersonasSinUsuario', 'json', 'acciones.php', 'N', 7),
(17, 'usuarios', 'listarRoles', 'json', 'acciones.php', 'N', 8),

-- Admin Roles
(20, 'roles', 'ver', 'pagina', 'formulario.php', 'N', 1),
(21, 'roles', 'listar', 'json', 'acciones.php', 'N', 2),
(22, 'roles', 'agregar', 'json', 'acciones.php', 'S', 3),
(23, 'roles', 'modificar', 'json', 'acciones.php', 'S', 4),
(24, 'roles', 'eliminar', 'json', 'acciones.php', 'S', 5),
(25, 'roles', 'asignar', 'json', 'acciones.php', 'N', 6),

-- Admin Permisos por Rol
(30, 'permisos-por-rol', 'ver', 'pagina', 'formulario.php', 'N', 1),
(31, 'permisos-por-rol', 'cargar', 'json', 'acciones.php', 'N', 2),
(32, 'permisos-por-rol', 'listar', 'json', 'acciones.php', 'N', 3),
(33, 'permisos-por-rol', 'guardar', 'json', 'acciones.php', 'S', 4);

DROP TABLE IF EXISTS `admin_permiso_menu`;
CREATE TABLE `admin_permiso_menu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rol` INT NOT NULL,
    `menu` VARCHAR(50) NOT NULL,
    UNIQUE KEY `uk_rol_menu` (`rol`, `menu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos de menú para roles 1 (Admin) y 4 (SuperAdmin)
INSERT INTO `admin_permiso_menu` (`rol`, `menu`) VALUES
(1, 'administracion'), (1, 'usuarios'), (1, 'roles'), (1, 'permisos-por-rol'),
(1, 'escuela'), (1, 'afiliacion'), (1, 'deportistas'), (1, 'asistencia'), (1, 'eventos'), (1, 'comunicados'),
(4, 'administracion'), (4, 'usuarios'), (4, 'roles'), (4, 'permisos-por-rol'),
(4, 'escuela'), (4, 'afiliacion'), (4, 'deportistas'), (4, 'asistencia'), (4, 'eventos'), (4, 'comunicados'),
-- Entrenador
(2, 'escuela'), (2, 'deportistas'), (2, 'asistencia'), (2, 'eventos'), (2, 'comunicados'),
-- Acudiente
(3, 'escuela'), (3, 'afiliacion'), (3, 'comunicados');

DROP TABLE IF EXISTS `admin_permiso_accion`;
CREATE TABLE `admin_permiso_accion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rol` INT NOT NULL,
    `accion` INT NOT NULL,
    UNIQUE KEY `uk_rol_accion` (`rol`, `accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos de acción para rol 1 y 4
INSERT INTO `admin_permiso_accion` (`rol`, `accion`) VALUES
(1, 12), (1, 13), (1, 14), (1, 22), (1, 23), (1, 24), (1, 32), (1, 33),
(4, 12), (4, 13), (4, 14), (4, 22), (4, 23), (4, 24), (4, 32), (4, 33);

DROP TABLE IF EXISTS `admin_token`;
CREATE TABLE `admin_token` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `token` TEXT NOT NULL,
    `caduca` DATE NOT NULL,
    `id_user` INT DEFAULT 0,
    `estado` TINYINT DEFAULT 1,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_sesion`;
CREATE TABLE `admin_sesion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario` VARCHAR(50) NOT NULL,
    `session_id` VARCHAR(128) NOT NULL,
    `user_agent` TEXT,
    `refer` TEXT,
    `ip` VARCHAR(45),
    `inicio` DATETIME NOT NULL,
    `fin` DATETIME NOT NULL,
    `salida` CHAR(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_sesion_denegada`;
CREATE TABLE `admin_sesion_denegada` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(128),
    `user_agent` TEXT,
    `refer` TEXT,
    `ip` VARCHAR(45),
    `fecha` DATETIME NOT NULL,
    `usuario` VARCHAR(50),
    `tipo` INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_persona` INT DEFAULT 0,
    `archivo` VARCHAR(100),
    `tipo` INT DEFAULT 1,
    `mensaje` TEXT,
    `menu` VARCHAR(50),
    `accion` VARCHAR(50),
    `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_bitacoras_sistema`;
CREATE TABLE `admin_bitacoras_sistema` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_tipo_bitacora` INT DEFAULT 1,
    `datos_anteriores` LONGTEXT,
    `datos_insertados` LONGTEXT,
    `observacion` TEXT,
    `menu` VARCHAR(50),
    `id_usuario` INT DEFAULT 0,
    `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_favoritos`;
CREATE TABLE `admin_favoritos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_menu` INT NOT NULL,
    `id_persona` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. TABLAS DE PERSONAS, USUARIOS Y AUTENTICACIÓN
-- ------------------------------------------------------------

DROP TABLE IF EXISTS `persona`;
CREATE TABLE `persona` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo_documento` ENUM('CC','TI','RC','CE','PASAPORTE','OTRO') DEFAULT 'CC',
    `identificacion` VARCHAR(20) NOT NULL UNIQUE,
    `user` VARCHAR(50) DEFAULT NULL,
    `nombre1` VARCHAR(50) NOT NULL,
    `nombre2` VARCHAR(50) DEFAULT '',
    `apellido1` VARCHAR(50) NOT NULL,
    `apellido2` VARCHAR(50) DEFAULT '',
    `fecha_nacimiento` DATE DEFAULT NULL,
    `genero` ENUM('M','F','OTRO') DEFAULT 'M',
    `celular` VARCHAR(20) DEFAULT '',
    `correo` VARCHAR(100) DEFAULT '',
    `direccion` TEXT,
    `foto` VARCHAR(255) DEFAULT 'img/user.png',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `persona_id` INT NOT NULL UNIQUE,
    `rol_id` INT NOT NULL DEFAULT 3,
    `login` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `ultimo_acceso` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuario_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `admin_rol`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_usuario`;
CREATE TABLE `admin_usuario` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `persona_id` INT NOT NULL UNIQUE,
    `rol_id` INT NOT NULL DEFAULT 3,
    `_usuario` VARCHAR(50) DEFAULT '',
    `_fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. TABLAS DE LA ESCUELA DE VOLEIBOL
-- ------------------------------------------------------------

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` TEXT,
    `edad_minima` INT DEFAULT 0,
    `edad_maxima` INT DEFAULT 99,
    `genero` ENUM('M','F','MIXTO') DEFAULT 'MIXTO',
    `activo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categoria` (`nombre`, `slug`, `descripcion`, `edad_minima`, `edad_maxima`, `genero`) VALUES
('Benjamín', 'benjamin', 'Iniciación deportiva (6 a 9 años)', 6, 9, 'MIXTO'),
('Infantil', 'infantil', 'Desarrollo motriz y táctico (10 a 13 años)', 10, 13, 'MIXTO'),
('Junior / Menores', 'junior', 'Formación competitiva (14 a 17 años)', 14, 17, 'MIXTO'),
('Juvenil / Mayores', 'juvenil', 'Alto rendimiento y mayores (18+ años)', 18, 99, 'MIXTO');

DROP TABLE IF EXISTS `deportista`;
CREATE TABLE `deportista` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `persona_id` INT NOT NULL UNIQUE,
    `categoria_id` INT DEFAULT NULL,
    `eps` VARCHAR(100) DEFAULT '',
    `rh` VARCHAR(5) DEFAULT '',
    `alergias` TEXT,
    `contacto_emergencia_nombre` VARCHAR(100) DEFAULT '',
    `contacto_emergencia_telefono` VARCHAR(20) DEFAULT '',
    `estado` ENUM('borrador','pendiente_revision','requiere_info','aprobado','no_aprobado','activo','inactivo') DEFAULT 'borrador',
    `fecha_afiliacion` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_deportista_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_deportista_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `deportista_acudiente`;
CREATE TABLE `deportista_acudiente` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `deportista_id` INT NOT NULL,
    `acudiente_id` INT NOT NULL,
    `parentesco` ENUM('padre','madre','tio','abuelo','tutor','otro') DEFAULT 'padre',
    `es_principal` TINYINT(1) DEFAULT 1,
    UNIQUE KEY `uk_dep_acud` (`deportista_id`, `acudiente_id`),
    CONSTRAINT `fk_da_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_da_acudiente` FOREIGN KEY (`acudiente_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tipo_documento`;
CREATE TABLE `tipo_documento` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `obligatorio` TINYINT(1) DEFAULT 0,
    `activo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_documento` (`nombre`, `slug`, `obligatorio`) VALUES
('Documento de Identidad (TI / CC)', 'documento_identidad', 1),
('Certificado de Afiliación EPS / SISBEN', 'certificado_eps', 1),
('Certificado Médico de Aptitud Física', 'certificado_medico', 1),
('Foto Tipo Documento (Fondo Blanco)', 'foto_documento', 1),
('Documento de Identidad del Acudiente', 'documento_acudiente', 0);

DROP TABLE IF EXISTS `documento`;
CREATE TABLE `documento` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `deportista_id` INT NOT NULL,
    `tipo_documento_id` INT NOT NULL,
    `archivo` VARCHAR(255) NOT NULL,
    `archivo_original` VARCHAR(255) NOT NULL,
    `estado` ENUM('pendiente','en_revision','aprobado','rechazado') DEFAULT 'pendiente',
    `observaciones` TEXT,
    `subido_por` INT NOT NULL,
    `fecha_subida` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `revisado_por` INT DEFAULT NULL,
    `fecha_revision` DATETIME DEFAULT NULL,
    CONSTRAINT `fk_doc_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_doc_tipo` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tipo_autorizacion`;
CREATE TABLE `tipo_autorizacion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `contenido` LONGTEXT NOT NULL,
    `version` VARCHAR(20) DEFAULT '1.0',
    `activo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tipo_autorizacion` (`nombre`, `slug`, `contenido`, `version`) VALUES
('Consentimiento Informado y Exoneración', 'consentimiento_informado', 'Por medio del presente documento, yo como padre/madre/tutor legal autorizo la participación del menor en las actividades deportivas de Voley+...', '1.0'),
('Autorización de Tratamiento de Datos y Uso de Imagen', 'tratamiento_datos_imagen', 'Autorizo a Voley+ para el uso de fotografías y videos en eventos deportivos con fines informativos e institucionales...', '1.0');

DROP TABLE IF EXISTS `autorizacion_firmada`;
CREATE TABLE `autorizacion_firmada` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `deportista_id` INT NOT NULL,
    `acudiente_id` INT NOT NULL,
    `tipo_autorizacion_id` INT NOT NULL,
    `fecha_firma` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `firma_electronica` LONGTEXT,
    `aceptada` TINYINT(1) DEFAULT 1,
    `version_firmada` VARCHAR(20) DEFAULT '1.0',
    CONSTRAINT `fk_af_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_af_acudiente` FOREIGN KEY (`acudiente_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_af_tipo` FOREIGN KEY (`tipo_autorizacion_id`) REFERENCES `tipo_autorizacion`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `clase`;
CREATE TABLE `clase` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `categoria_id` INT NOT NULL,
    `entrenador_id` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `hora_inicio` TIME NOT NULL,
    `hora_fin` TIME NOT NULL,
    `lugar` VARCHAR(150) DEFAULT 'Cancha Principal',
    CONSTRAINT `fk_clase_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria`(`id`),
    CONSTRAINT `fk_clase_entrenador` FOREIGN KEY (`entrenador_id`) REFERENCES `persona`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `asistencia`;
CREATE TABLE `asistencia` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `clase_id` INT NOT NULL,
    `deportista_id` INT NOT NULL,
    `estado` ENUM('asistio','no_asistio','excusa','tarde') NOT NULL DEFAULT 'asistio',
    `observaciones` TEXT,
    `fecha_marcacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_clase_dep` (`clase_id`, `deportista_id`),
    CONSTRAINT `fk_asist_clase` FOREIGN KEY (`clase_id`) REFERENCES `clase`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_asist_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `evento`;
CREATE TABLE `evento` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(200) NOT NULL,
    `tipo` ENUM('torneo','entrenamiento','salida','reunion','otro') DEFAULT 'torneo',
    `fecha` DATE NOT NULL,
    `lugar` VARCHAR(200) NOT NULL,
    `horario` TEXT,
    `recomendaciones` TEXT,
    `requiere_autorizacion` TINYINT(1) DEFAULT 0,
    `creado_por` INT NOT NULL,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `evento_deportista`;
CREATE TABLE `evento_deportista` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `evento_id` INT NOT NULL,
    `deportista_id` INT NOT NULL,
    `convocado` TINYINT(1) DEFAULT 1,
    UNIQUE KEY `uk_ev_dep` (`evento_id`, `deportista_id`),
    CONSTRAINT `fk_ed_evento` FOREIGN KEY (`evento_id`) REFERENCES `evento`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ed_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `autorizacion_evento`;
CREATE TABLE `autorizacion_evento` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `evento_id` INT NOT NULL,
    `deportista_id` INT NOT NULL,
    `acudiente_id` INT NOT NULL,
    `autoriza` TINYINT(1) NOT NULL DEFAULT 1,
    `fecha_respuesta` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `firma_electronica` LONGTEXT,
    UNIQUE KEY `uk_ae` (`evento_id`, `deportista_id`, `acudiente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `comunicado`;
CREATE TABLE `comunicado` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(200) NOT NULL,
    `contenido` LONGTEXT NOT NULL,
    `destinatario_tipo` ENUM('todos','categoria','grupo','individual') DEFAULT 'todos',
    `destinatario_id` INT DEFAULT 0,
    `creado_por` INT NOT NULL,
    `fecha_publicacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `confirmacion_lectura` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `comunicado_lectura`;
CREATE TABLE `comunicado_lectura` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `comunicado_id` INT NOT NULL,
    `persona_id` INT NOT NULL,
    `fecha_lectura` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_com_per` (`comunicado_id`, `persona_id`),
    CONSTRAINT `fk_cl_comunicado` FOREIGN KEY (`comunicado_id`) REFERENCES `comunicado`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cl_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. USUARIO ADMINISTRADOR INICIAL (Password: admin123)
-- ------------------------------------------------------------

INSERT INTO `persona` (`id`, `tipo_documento`, `identificacion`, `user`, `nombre1`, `nombre2`, `apellido1`, `apellido2`, `correo`, `celular`) VALUES
(1, 'CC', '10000001', 'ADMIN', 'Super', '', 'Admin', 'Voley+', 'admin@voleyplus.com', '3001234567');

-- Hash bcrypt real de 'admin123'
INSERT INTO `usuario` (`id`, `persona_id`, `rol_id`, `login`, `password_hash`, `activo`) VALUES
(1, 1, 4, 'ADMIN', '$2y$10$xg5jO5IJyemCOMEYcay3ReB8iS/NqpXD6LBoFOBe8SJfN97IaKYBS', 1);

INSERT INTO `admin_usuario` (`id`, `persona_id`, `rol_id`, `_usuario`) VALUES
(1, 1, 4, '10000001');

SET FOREIGN_KEY_CHECKS = 1;
