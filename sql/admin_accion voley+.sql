-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-09-2026 a las 17:36:10
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `voley_plus`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_accion`
--

CREATE TABLE `admin_accion` (
  `id` int(11) NOT NULL,
  `menu` varchar(50) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `tipo_accion` varchar(20) NOT NULL,
  `archivo` varchar(100) NOT NULL,
  `requiere_permiso` char(1) DEFAULT 'N',
  `descripcion` text DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_accion`
--

INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `descripcion`, `orden`, `fecha`) VALUES
(1, 'inicio', 'ver', 'pagina', 'formulario.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
(2, 'inicio', 'set_token', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-16 15:14:39'),
(3, 'inicio', 'dashboard', 'json', 'acciones.php', 'N', NULL, 3, '2026-09-16 15:14:39'),
(4, 'iniciar-sesion', 'ver', 'pagina', 'iniciar_sesion.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
(5, 'iniciar-sesion', 'iniciar', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-16 15:14:39'),
(6, 'iniciar-sesion', 'set_login', 'json', 'acciones.php', 'N', NULL, 3, '2026-09-16 15:14:39'),
(7, 'cerrar-sesion', 'cerrarSesion', 'pagina', 'cerrar_sesion.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
(10, 'usuarios', 'ver', 'pagina', 'formulario.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
(11, 'usuarios', 'listar', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-16 15:14:39'),
(12, 'usuarios', 'agregar', 'json', 'acciones.php', 'S', NULL, 3, '2026-09-16 15:14:39'),
(13, 'usuarios', 'modificar', 'json', 'acciones.php', 'S', NULL, 4, '2026-09-16 15:14:39'),
(14, 'usuarios', 'eliminar', 'json', 'acciones.php', 'S', NULL, 5, '2026-09-16 15:14:39'),
(15, 'usuarios', 'asignar', 'json', 'acciones.php', 'N', NULL, 6, '2026-09-16 15:14:39'),
(16, 'usuarios', 'listarPersonasSinUsuario', 'json', 'acciones.php', 'N', NULL, 7, '2026-09-16 15:14:39'),
(17, 'usuarios', 'listarRoles', 'json', 'acciones.php', 'N', NULL, 8, '2026-09-16 15:14:39'),
(20, 'roles', 'ver', 'pagina', 'formulario.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
(21, 'roles', 'listar', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-16 15:14:39'),
(22, 'roles', 'agregar', 'json', 'acciones.php', 'S', NULL, 3, '2026-09-16 15:14:39'),
(23, 'roles', 'modificar', 'json', 'acciones.php', 'S', NULL, 4, '2026-09-16 15:14:39'),
(24, 'roles', 'eliminar', 'json', 'acciones.php', 'S', NULL, 5, '2026-09-16 15:14:39'),
(25, 'roles', 'asignar', 'json', 'acciones.php', 'N', NULL, 6, '2026-09-16 15:14:39'),
(30, 'permisos-por-rol', 'ver', 'pagina', 'index.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
(31, 'permisos-por-rol', 'cargar', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-16 15:14:39'),
(32, 'permisos-por-rol', 'listar', 'json', 'acciones.php', 'N', NULL, 3, '2026-09-16 15:14:39'),
(33, 'permisos-por-rol', 'guardar', 'json', 'acciones.php', 'S', NULL, 4, '2026-09-16 15:14:39'),
(36, 'clave_usuarios', 'ver', 'pagina', 'formulario.php', 'N', NULL, 1, '2026-09-21 15:23:57'),
(37, 'clave_usuarios', 'buscar', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-21 15:23:57'),
(38, 'clave_usuarios', 'cambiar', 'json', 'acciones.php', 'S', NULL, 3, '2026-09-21 15:23:57'),
(39, 'restaurar_clave', 'ver', 'pagina', 'formulario.php', 'N', NULL, 1, '2026-09-21 15:23:57'),
(40, 'restaurar_clave', 'buscar', 'json', 'acciones.php', 'N', NULL, 2, '2026-09-21 15:23:57'),
(41, 'restaurar_clave', 'restaurar', 'json', 'acciones.php', 'S', NULL, 3, '2026-09-21 15:23:57'),
(42, 'diseno', 'ver', 'pagina', 'formulario.php', 'N', 'Cargar el formulario de dise±o', 0, '2026-09-22 13:05:38'),
(43, 'diseno', 'obtener', 'json', 'acciones.php', 'S', 'Obtener la configuraci¾n actual del tema', 0, '2026-09-22 13:05:38'),
(44, 'diseno', 'guardar', 'json', 'acciones.php', 'S', 'Guardar la configuraci¾n del tema', 0, '2026-09-22 13:05:38'),
(45, 'diseno', 'resetear', 'json', 'acciones.php', 'S', 'Restablecer el tema a valores por defecto', 0, '2026-09-22 13:05:38'),
(46, 'diseno', 'generar_css', 'html', 'acciones.php', 'S', 'Generar CSS dinßmico basado en la configuraci¾n', 0, '2026-09-22 13:05:38'),
(47, 'sesion', 'iniciar', 'json', 'acciones.php', 'N', 'Iniciar sesi¾n', 100, '2026-09-22 18:40:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_accion`
--
ALTER TABLE `admin_accion`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_accion`
--
ALTER TABLE `admin_accion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
