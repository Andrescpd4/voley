-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2026 a las 18:30:15
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
  `orden` int(11) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_accion`
--

INSERT INTO `admin_accion` (`id`, `menu`, `accion`, `tipo_accion`, `archivo`, `requiere_permiso`, `orden`, `fecha`) VALUES
(1, 'inicio', 'ver', 'pagina', 'formulario.php', 'N', 1, '2026-09-16 15:14:39'),
(2, 'inicio', 'set_token', 'json', 'acciones.php', 'N', 2, '2026-09-16 15:14:39'),
(3, 'inicio', 'dashboard', 'json', 'acciones.php', 'N', 3, '2026-09-16 15:14:39'),
(4, 'iniciar-sesion', 'ver', 'pagina', 'iniciar_sesion.php', 'N', 1, '2026-09-16 15:14:39'),
(5, 'iniciar-sesion', 'iniciar', 'json', 'acciones.php', 'N', 2, '2026-09-16 15:14:39'),
(6, 'iniciar-sesion', 'set_login', 'json', 'acciones.php', 'N', 3, '2026-09-16 15:14:39'),
(7, 'cerrar-sesion', 'cerrarSesion', 'pagina', 'cerrar_sesion.php', 'N', 1, '2026-09-16 15:14:39'),
(10, 'usuarios', 'ver', 'pagina', 'formulario.php', 'N', 1, '2026-09-16 15:14:39'),
(11, 'usuarios', 'listar', 'json', 'acciones.php', 'N', 2, '2026-09-16 15:14:39'),
(12, 'usuarios', 'agregar', 'json', 'acciones.php', 'S', 3, '2026-09-16 15:14:39'),
(13, 'usuarios', 'modificar', 'json', 'acciones.php', 'S', 4, '2026-09-16 15:14:39'),
(14, 'usuarios', 'eliminar', 'json', 'acciones.php', 'S', 5, '2026-09-16 15:14:39'),
(15, 'usuarios', 'asignar', 'json', 'acciones.php', 'N', 6, '2026-09-16 15:14:39'),
(16, 'usuarios', 'listarPersonasSinUsuario', 'json', 'acciones.php', 'N', 7, '2026-09-16 15:14:39'),
(17, 'usuarios', 'listarRoles', 'json', 'acciones.php', 'N', 8, '2026-09-16 15:14:39'),
(20, 'roles', 'ver', 'pagina', 'formulario.php', 'N', 1, '2026-09-16 15:14:39'),
(21, 'roles', 'listar', 'json', 'acciones.php', 'N', 2, '2026-09-16 15:14:39'),
(22, 'roles', 'agregar', 'json', 'acciones.php', 'S', 3, '2026-09-16 15:14:39'),
(23, 'roles', 'modificar', 'json', 'acciones.php', 'S', 4, '2026-09-16 15:14:39'),
(24, 'roles', 'eliminar', 'json', 'acciones.php', 'S', 5, '2026-09-16 15:14:39'),
(25, 'roles', 'asignar', 'json', 'acciones.php', 'N', 6, '2026-09-16 15:14:39'),
(30, 'permisos-por-rol', 'ver', 'pagina', 'formulario.php', 'N', 1, '2026-09-16 15:14:39'),
(31, 'permisos-por-rol', 'cargar', 'json', 'acciones.php', 'N', 2, '2026-09-16 15:14:39'),
(32, 'permisos-por-rol', 'listar', 'json', 'acciones.php', 'N', 3, '2026-09-16 15:14:39'),
(33, 'permisos-por-rol', 'guardar', 'json', 'acciones.php', 'S', 4, '2026-09-16 15:14:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_bitacoras_sistema`
--

CREATE TABLE `admin_bitacoras_sistema` (
  `id` int(11) NOT NULL,
  `id_tipo_bitacora` int(11) DEFAULT 1,
  `datos_anteriores` longtext DEFAULT NULL,
  `datos_insertados` longtext DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `menu` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_favoritos`
--

CREATE TABLE `admin_favoritos` (
  `id` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_log`
--

CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL,
  `id_persona` int(11) DEFAULT 0,
  `archivo` varchar(100) DEFAULT NULL,
  `tipo` int(11) DEFAULT 1,
  `mensaje` text DEFAULT NULL,
  `menu` varchar(50) DEFAULT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_log`
--

INSERT INTO `admin_log` (`id`, `id_persona`, `archivo`, `tipo`, `mensaje`, `menu`, `accion`, `fecha`) VALUES
(1, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 15:42:54'),
(2, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 15:42:55'),
(3, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 15:42:55'),
(4, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:09:33'),
(5, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:09:38'),
(6, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:09:39'),
(7, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:09:45'),
(8, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:09:48'),
(9, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:09:48'),
(10, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:09:57'),
(11, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:10:00'),
(12, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:10:00'),
(13, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:10:45'),
(14, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:10:47'),
(15, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:10:47'),
(16, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:11:01'),
(17, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:11:06'),
(18, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:11:06'),
(19, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:11:24'),
(20, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:11:25'),
(21, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:11:25'),
(22, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:12:37'),
(23, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:12:38'),
(24, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:12:39'),
(25, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:12:44'),
(26, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:12:47'),
(27, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:12:47'),
(28, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:13:10'),
(29, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:13:12'),
(30, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:13:12'),
(31, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:13:20'),
(32, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:13:22'),
(33, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:13:22'),
(34, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:14:01'),
(35, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:14:03'),
(36, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:14:03'),
(37, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:17:01'),
(38, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:17:56'),
(39, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:36:38'),
(40, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:36:40'),
(41, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:36:40'),
(42, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:38:46'),
(43, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:38:48'),
(44, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:38:48'),
(45, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:43:03'),
(46, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:43:07'),
(47, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:43:07'),
(48, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:43:15'),
(49, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:43:21'),
(50, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:43:21'),
(51, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:55:00'),
(52, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:55:03'),
(53, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:55:03'),
(54, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 16:55:19'),
(55, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 16:55:27'),
(56, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 16:55:27'),
(57, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:12:55'),
(58, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:13:03'),
(59, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:13:03'),
(60, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:13:05'),
(61, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:13:07'),
(62, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:13:08'),
(63, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:14:17'),
(64, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:15:29'),
(65, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:15:32'),
(66, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:15:32'),
(67, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:15:34'),
(68, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-16 18:15:36'),
(69, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarRoles', '2026-09-16 18:15:36'),
(70, 1, 'pagina.php', 1, 'EXITO', 'roles', 'ver', '2026-09-16 18:17:13'),
(71, 1, 'descarga.php', 1, 'EXITO', 'roles', 'listar', '2026-09-16 18:17:15'),
(72, 1, 'descarga.php', 1, 'EXITO', 'roles', 'asignar', '2026-09-16 18:19:38'),
(73, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:19:44'),
(74, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-16 18:19:46'),
(75, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarRoles', '2026-09-16 18:19:46'),
(76, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarPersonasSinUsuario', '2026-09-16 18:21:15'),
(77, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:21:32'),
(78, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:21:33'),
(79, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:21:33'),
(80, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:21:38'),
(81, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:21:38'),
(82, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:21:38'),
(83, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:21:43'),
(84, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:21:43'),
(85, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:21:43'),
(86, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 18:26:07'),
(87, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 18:26:09'),
(88, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:28:05'),
(89, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 18:28:06'),
(90, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarRoles', '2026-09-16 18:28:06'),
(91, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-16 18:28:06'),
(92, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 18:28:07'),
(93, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 18:33:41'),
(94, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 18:33:43'),
(95, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-16 18:33:53'),
(96, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 18:33:53'),
(97, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 18:33:56'),
(98, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 18:33:57'),
(99, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-16 18:34:08'),
(100, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 18:34:09'),
(101, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-16 18:34:18'),
(102, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 18:34:23'),
(103, 1, '404.php', 2, 'Acceso denegado al menu', 'permisos-por-rol', 'ver', '2026-09-16 18:34:23'),
(104, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:34:31'),
(105, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:34:31'),
(106, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 18:37:35'),
(107, 1, '404.php', 2, 'Acceso denegado al menu', 'permisos-por-rol', 'ver', '2026-09-16 18:37:35'),
(108, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:37:36'),
(109, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:37:37'),
(110, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:37:37'),
(111, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:37:39'),
(112, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:37:40'),
(113, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:37:40'),
(114, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:37:41'),
(115, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:37:41'),
(116, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:37:42'),
(117, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:37:43'),
(118, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:37:43'),
(119, 1, 'pagina.php', 1, 'EXITO', 'roles', 'ver', '2026-09-16 18:37:45'),
(120, 1, '404.php', 2, 'Acceso denegado al menu', 'roles', 'ver', '2026-09-16 18:37:45'),
(121, 1, 'pagina.php', 1, 'EXITO', 'roles', 'ver', '2026-09-16 18:37:50'),
(122, 1, '404.php', 2, 'Acceso denegado al menu', 'roles', 'ver', '2026-09-16 18:37:50'),
(123, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:38:10'),
(124, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:38:10'),
(125, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:40:14'),
(126, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:40:14'),
(127, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:56:11'),
(128, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:56:11'),
(129, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:56:16'),
(130, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:56:16'),
(131, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:56:17'),
(132, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:56:18'),
(133, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 18:56:19'),
(134, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 18:56:22'),
(135, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 18:56:23'),
(136, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:56:29'),
(137, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:56:29'),
(138, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:56:34'),
(139, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:56:34'),
(140, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 18:56:44'),
(141, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 18:56:44'),
(142, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 19:00:47'),
(143, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 19:00:49'),
(144, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 19:00:49'),
(145, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 19:00:52'),
(146, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 19:00:52'),
(147, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-16 19:30:35'),
(148, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-16 19:30:36'),
(149, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-16 19:30:36'),
(150, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 19:30:45'),
(151, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 19:30:45'),
(152, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 19:30:51'),
(153, 1, '404.php', 2, 'Acceso denegado al menu', 'usuarios', 'ver', '2026-09-16 19:30:51'),
(154, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 19:49:35'),
(155, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarRoles', '2026-09-16 19:49:37'),
(156, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-16 19:49:37'),
(157, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-16 20:30:27'),
(158, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-16 20:30:31'),
(159, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarRoles', '2026-09-16 20:30:31'),
(160, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-16 20:30:41'),
(161, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-16 20:30:42'),
(162, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-16 20:31:45'),
(163, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-16 20:31:53'),
(164, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 12:40:11'),
(165, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 12:40:16'),
(166, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:02:35'),
(167, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 14:02:37'),
(168, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:03:11'),
(169, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:03:12'),
(170, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:03:12'),
(171, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:03:13'),
(172, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 14:03:14'),
(173, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:03:18'),
(174, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 14:03:19'),
(175, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:47:48'),
(176, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 14:47:50'),
(177, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:53:36'),
(178, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 14:53:37'),
(179, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 14:53:42'),
(180, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-17 15:15:08'),
(181, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-17 15:15:09'),
(182, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-17 15:15:09'),
(183, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 15:17:00'),
(184, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 15:17:01'),
(185, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 15:18:59'),
(186, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 15:18:59'),
(187, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 15:57:17'),
(188, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 15:57:18'),
(189, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 15:57:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL,
  `menu` varchar(50) NOT NULL,
  `padre` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `ruta` varchar(200) NOT NULL DEFAULT '#',
  `accion` varchar(50) NOT NULL DEFAULT 'ver',
  `orden` int(11) DEFAULT 0,
  `visible` char(1) DEFAULT 'S',
  `acceso` char(1) DEFAULT '7',
  `icono` varchar(50) DEFAULT 'ri-circle-line',
  `_self` varchar(10) DEFAULT '_self'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `menu`, `padre`, `nombre`, `ruta`, `accion`, `orden`, `visible`, `acceso`, `icono`, `_self`) VALUES
(1, 'inicio', NULL, 'Dashboard', 'modulos/inicio', 'ver', 10, 'S', '3', 'ri-dashboard-line', '_self'),
(2, 'iniciar-sesion', NULL, 'Iniciar Sesión', 'modulos/sesion', 'ver', 999, 'N', '1', '', '_self'),
(3, 'cerrar-sesion', NULL, 'Cerrar Sesión', 'modulos/sesion', 'cerrarSesion', 999, 'N', '3', '', '_self'),
(4, 'perfil', NULL, 'Mi Perfil', 'modulos/admin/usuarios', 'perfil', 998, 'N', '3', 'ri-user-line', '_self'),
(10, 'administracion', NULL, 'Administración', '#', 'ver', 20, 'S', '7', 'ri-settings-3-line', '_self'),
(11, 'usuarios', 'administracion', 'Usuarios', 'modulos/admin/usuarios', 'ver', 21, 'S', '7', 'ri-user-settings-line', '_self'),
(12, 'roles', 'administracion', 'Roles', 'modulos/admin/roles', 'ver', 22, 'S', '7', 'ri-shield-user-line', '_self'),
(13, 'permisos-por-rol', 'administracion', 'Permisos por Rol', 'modulos/admin/permisos-rol', 'ver', 23, 'S', '7', 'ri-lock-password-line', '_self'),
(20, 'escuela', NULL, 'Escuela Voley', '#', 'ver', 30, 'S', '7', 'ri-medal-line', '_self'),
(21, 'afiliacion', 'escuela', 'Afiliaciones', 'modulos/afiliacion', 'ver', 31, 'S', '7', 'ri-user-add-line', '_self'),
(22, 'deportistas', 'escuela', 'Ficha Deportistas', 'modulos/escuela/deportistas', 'ver', 32, 'S', '7', 'ri-team-line', '_self'),
(23, 'asistencia', 'escuela', 'Control Asistencia', 'modulos/asistencia', 'ver', 33, 'S', '7', 'ri-calendar-check-line', '_self'),
(24, 'eventos', 'escuela', 'Eventos y Torneos', 'modulos/eventos', 'ver', 34, 'S', '7', 'ri-trophy-line', '_self'),
(25, 'comunicados', 'escuela', 'Comunicados', 'modulos/comunicados', 'ver', 35, 'S', '7', 'ri-broadcast-line', '_self');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_permiso_accion`
--

CREATE TABLE `admin_permiso_accion` (
  `id` int(11) NOT NULL,
  `rol` int(11) NOT NULL,
  `accion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_permiso_accion`
--

INSERT INTO `admin_permiso_accion` (`id`, `rol`, `accion`) VALUES
(1, 1, 12),
(2, 1, 13),
(3, 1, 14),
(4, 1, 22),
(5, 1, 23),
(6, 1, 24),
(7, 1, 32),
(8, 1, 33),
(28, 4, 12),
(30, 4, 13),
(29, 4, 14),
(25, 4, 22),
(27, 4, 23),
(26, 4, 24);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_permiso_menu`
--

CREATE TABLE `admin_permiso_menu` (
  `id` int(11) NOT NULL,
  `rol` int(11) NOT NULL,
  `menu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_permiso_menu`
--

INSERT INTO `admin_permiso_menu` (`id`, `rol`, `menu`) VALUES
(1, 1, 'administracion'),
(6, 1, 'afiliacion'),
(8, 1, 'asistencia'),
(10, 1, 'comunicados'),
(7, 1, 'deportistas'),
(5, 1, 'escuela'),
(9, 1, 'eventos'),
(4, 1, 'permisos-por-rol'),
(3, 1, 'roles'),
(2, 1, 'usuarios'),
(23, 2, 'asistencia'),
(25, 2, 'comunicados'),
(22, 2, 'deportistas'),
(21, 2, 'escuela'),
(24, 2, 'eventos'),
(27, 3, 'afiliacion'),
(28, 3, 'comunicados'),
(26, 3, 'escuela'),
(44, 4, 'administracion'),
(39, 4, 'afiliacion'),
(41, 4, 'asistencia'),
(43, 4, 'comunicados'),
(40, 4, 'deportistas'),
(42, 4, 'eventos'),
(38, 4, 'inicio'),
(47, 4, 'permisos-por-rol'),
(46, 4, 'roles'),
(45, 4, 'usuarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_rol`
--

CREATE TABLE `admin_rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `nivel` int(11) DEFAULT 1,
  `visible` char(1) DEFAULT 'S',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_rol`
--

INSERT INTO `admin_rol` (`id`, `nombre`, `slug`, `nivel`, `visible`, `fecha_creacion`) VALUES
(1, 'Administrador Voley+', 'admin', 80, 'S', '2026-09-16 15:14:39'),
(2, 'Entrenador', 'entrenador', 50, 'S', '2026-09-16 15:14:39'),
(3, 'Acudiente / Padre', 'acudiente', 10, 'S', '2026-09-16 15:14:39'),
(4, 'Super Administrador', 'superadmin', 100, 'S', '2026-09-16 15:14:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_sesion`
--

CREATE TABLE `admin_sesion` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `refer` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `salida` char(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_sesion`
--

INSERT INTO `admin_sesion` (`id`, `usuario`, `session_id`, `user_agent`, `refer`, `ip`, `inicio`, `fin`, `salida`) VALUES
(1, '10000001', 'jvnruni8p8p9jgf4nplenj07fn', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 10:42:53', '2026-09-16 10:42:53', 'N'),
(2, '10000001', '3l4rkg0c4p3fhk8uvlgd0vp4qh', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 11:10:43', '2026-09-16 11:10:43', 'N'),
(3, '10000001', '08ov3259l9i1do9of93qdfqv2t', 'curl/8.21.0', '', '::1', '2026-09-16 13:14:17', '2026-09-16 13:14:17', 'N'),
(4, '10000001', '83n80u543hdqeqvio5iogeo5a2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/permisos-por-rol', '::1', '2026-09-17 10:15:07', '2026-09-17 10:15:07', 'N');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_sesion_denegada`
--

CREATE TABLE `admin_sesion_denegada` (
  `id` int(11) NOT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `refer` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `tipo` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_sesion_denegada`
--

INSERT INTO `admin_sesion_denegada` (`id`, `session_id`, `user_agent`, `refer`, `ip`, `fecha`, `usuario`, `tipo`) VALUES
(1, 'be5u6hkqfu488dtul38c7gd9mt', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 10:15:10', 'admin', 1),
(2, '88e9v56dgp8003f2i1o23drna9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 10:15:24', 'ADMIN', 1),
(3, 'vebu04oj7t78oi50jpst31hrq7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 10:15:56', 'ADMIN', 1),
(4, 'pga8ruq6ho41cecr44d1sung00', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 10:16:30', 'admin', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_tipo_accion`
--

CREATE TABLE `admin_tipo_accion` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `archivo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_tipo_accion`
--

INSERT INTO `admin_tipo_accion` (`id`, `codigo`, `nombre`, `archivo`) VALUES
(1, 'pagina', 'Página Web', 'pagina.php'),
(2, 'json', 'Petición JSON (AJAX)', 'descarga.php'),
(3, 'html', 'Fragmento HTML', 'descarga.php');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_token`
--

CREATE TABLE `admin_token` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `caduca` date NOT NULL,
  `id_user` int(11) DEFAULT 0,
  `estado` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_token`
--

INSERT INTO `admin_token` (`id`, `token`, `caduca`, `id_user`, `estado`, `fecha_creacion`) VALUES
(8, 'x1Cvf1CnLIUPkHb5FT3c7HQTJnxjBH15aRUPx4D17VVX5Djmtmsz7qteAKFjBoHWuaxHnp1z5eJ8G6GlYgrIQEy+svzrG2LRmibC/e4y3Qkuu5tgfxM9cuEe6H9v2Y5J', '2026-09-16', 1, 1, '2026-09-16 16:11:06'),
(21, 'x1Cvf1CnLIUPkHb5FT3c7HQTJnxjBH15aRUPx4D17VVX5Djmtmsz7qteAKFjBoHWasuzi4G6G9aA4thXIScYyU9NZXg/NuRkbP6CqQDVkz7JBZUpIHfg+AP/2qDrfK7V', '2026-09-16', 1, 1, '2026-09-16 18:13:05'),
(23, 'gLPz71e168jMzKj1Htz1+TngN7/C2bHfc9WivR+Z4VUfZRRwNIx51LUJHAOdSk6ZVumGmpPgPG3iIN3g603Wxw==', '2026-09-16', 1, 1, '2026-09-16 18:14:17'),
(34, 'x1Cvf1CnLIUPkHb5FT3c7HQTJnxjBH15aRUPx4D17VVX5Djmtmsz7qteAKFjBoHWrL1KfBFWyS8JGnOhC10we+JssNxxQ/aBjHm4jK1B9uxK6jGWEg8+vTZwHrw8FaNb', '2026-09-16', 1, 1, '2026-09-16 19:30:36'),
(35, 'gLPz71e168jMzKj1Htz1+UetA/cD9zmtNfVTVBV6pWDyoCapNMSdpBxditkyWr7dMVkjpc8qKjReEh55L+TMUg==', '2026-09-17', 1, 2, '2026-09-17 15:15:07'),
(36, 'x1Cvf1CnLIUPkHb5FT3c7K83JZsvTZ9abIRkTHtZv5QjMQn/xd0VHDAApzTO3Amj0kBwzBekm10lpTK/1bhpFBw5vQ7xwmAWX72Yft0HTvvYrOV532io/porvLET27/k', '2026-09-17', 1, 1, '2026-09-17 15:15:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_usuario`
--

CREATE TABLE `admin_usuario` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL DEFAULT 3,
  `_usuario` varchar(50) DEFAULT '',
  `_fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_usuario`
--

INSERT INTO `admin_usuario` (`id`, `persona_id`, `rol_id`, `_usuario`, `_fecha`) VALUES
(1, 1, 4, '10000001', '2026-09-16 15:14:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `clase_id` int(11) NOT NULL,
  `deportista_id` int(11) NOT NULL,
  `estado` enum('asistio','no_asistio','excusa','tarde') NOT NULL DEFAULT 'asistio',
  `observaciones` text DEFAULT NULL,
  `fecha_marcacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autorizacion_evento`
--

CREATE TABLE `autorizacion_evento` (
  `id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `deportista_id` int(11) NOT NULL,
  `acudiente_id` int(11) NOT NULL,
  `autoriza` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_respuesta` datetime DEFAULT current_timestamp(),
  `firma_electronica` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autorizacion_firmada`
--

CREATE TABLE `autorizacion_firmada` (
  `id` int(11) NOT NULL,
  `deportista_id` int(11) NOT NULL,
  `acudiente_id` int(11) NOT NULL,
  `tipo_autorizacion_id` int(11) NOT NULL,
  `fecha_firma` datetime DEFAULT current_timestamp(),
  `firma_electronica` longtext DEFAULT NULL,
  `aceptada` tinyint(1) DEFAULT 1,
  `version_firmada` varchar(20) DEFAULT '1.0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `edad_minima` int(11) DEFAULT 0,
  `edad_maxima` int(11) DEFAULT 99,
  `genero` enum('M','F','MIXTO') DEFAULT 'MIXTO',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `slug`, `descripcion`, `edad_minima`, `edad_maxima`, `genero`, `activo`) VALUES
(1, 'Benjamín', 'benjamin', 'Iniciación deportiva (6 a 9 años)', 6, 9, 'MIXTO', 1),
(2, 'Infantil', 'infantil', 'Desarrollo motriz y táctico (10 a 13 años)', 10, 13, 'MIXTO', 1),
(3, 'Junior / Menores', 'junior', 'Formación competitiva (14 a 17 años)', 14, 17, 'MIXTO', 1),
(4, 'Juvenil / Mayores', 'juvenil', 'Alto rendimiento y mayores (18+ años)', 18, 99, 'MIXTO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `entrenador_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `lugar` varchar(150) DEFAULT 'Cancha Principal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicado`
--

CREATE TABLE `comunicado` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `contenido` longtext NOT NULL,
  `destinatario_tipo` enum('todos','categoria','grupo','individual') DEFAULT 'todos',
  `destinatario_id` int(11) DEFAULT 0,
  `creado_por` int(11) NOT NULL,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `confirmacion_lectura` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicado_lectura`
--

CREATE TABLE `comunicado_lectura` (
  `id` int(11) NOT NULL,
  `comunicado_id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `fecha_lectura` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deportista`
--

CREATE TABLE `deportista` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `eps` varchar(100) DEFAULT '',
  `rh` varchar(5) DEFAULT '',
  `alergias` text DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(100) DEFAULT '',
  `contacto_emergencia_telefono` varchar(20) DEFAULT '',
  `estado` enum('borrador','pendiente_revision','requiere_info','aprobado','no_aprobado','activo','inactivo') DEFAULT 'borrador',
  `fecha_afiliacion` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deportista_acudiente`
--

CREATE TABLE `deportista_acudiente` (
  `id` int(11) NOT NULL,
  `deportista_id` int(11) NOT NULL,
  `acudiente_id` int(11) NOT NULL,
  `parentesco` enum('padre','madre','tio','abuelo','tutor','otro') DEFAULT 'padre',
  `es_principal` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `deportista_id` int(11) NOT NULL,
  `tipo_documento_id` int(11) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `archivo_original` varchar(255) NOT NULL,
  `estado` enum('pendiente','en_revision','aprobado','rechazado') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `subido_por` int(11) NOT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp(),
  `revisado_por` int(11) DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE `evento` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `tipo` enum('torneo','entrenamiento','salida','reunion','otro') DEFAULT 'torneo',
  `fecha` date NOT NULL,
  `lugar` varchar(200) NOT NULL,
  `horario` text DEFAULT NULL,
  `recomendaciones` text DEFAULT NULL,
  `requiere_autorizacion` tinyint(1) DEFAULT 0,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_deportista`
--

CREATE TABLE `evento_deportista` (
  `id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `deportista_id` int(11) NOT NULL,
  `convocado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id` int(11) NOT NULL,
  `tipo_documento` enum('CC','TI','RC','CE','PASAPORTE','OTRO') DEFAULT 'CC',
  `identificacion` varchar(20) NOT NULL,
  `user` varchar(50) DEFAULT NULL,
  `nombre1` varchar(50) NOT NULL,
  `nombre2` varchar(50) DEFAULT '',
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) DEFAULT '',
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','OTRO') DEFAULT 'M',
  `celular` varchar(20) DEFAULT '',
  `correo` varchar(100) DEFAULT '',
  `direccion` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'img/user.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id`, `tipo_documento`, `identificacion`, `user`, `nombre1`, `nombre2`, `apellido1`, `apellido2`, `fecha_nacimiento`, `genero`, `celular`, `correo`, `direccion`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'CC', '10000001', 'ADMIN', 'Super', '', 'Admin', 'Voley+', NULL, 'M', '3001234567', 'admin@voleyplus.com', NULL, 'img/user.png', '2026-09-16 15:14:39', '2026-09-16 15:14:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_autorizacion`
--

CREATE TABLE `tipo_autorizacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `contenido` longtext NOT NULL,
  `version` varchar(20) DEFAULT '1.0',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_autorizacion`
--

INSERT INTO `tipo_autorizacion` (`id`, `nombre`, `slug`, `contenido`, `version`, `activo`) VALUES
(1, 'Consentimiento Informado y Exoneración', 'consentimiento_informado', 'Por medio del presente documento, yo como padre/madre/tutor legal autorizo la participación del menor en las actividades deportivas de Voley+...', '1.0', 1),
(2, 'Autorización de Tratamiento de Datos y Uso de Imagen', 'tratamiento_datos_imagen', 'Autorizo a Voley+ para el uso de fotografías y videos en eventos deportivos con fines informativos e institucionales...', '1.0', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `obligatorio` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`id`, `nombre`, `slug`, `obligatorio`, `activo`) VALUES
(1, 'Documento de Identidad (TI / CC)', 'documento_identidad', 1, 1),
(2, 'Certificado de Afiliación EPS / SISBEN', 'certificado_eps', 1, 1),
(3, 'Certificado Médico de Aptitud Física', 'certificado_medico', 1, 1),
(4, 'Foto Tipo Documento (Fondo Blanco)', 'foto_documento', 1, 1),
(5, 'Documento de Identidad del Acudiente', 'documento_acudiente', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL DEFAULT 3,
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `persona_id`, `rol_id`, `login`, `password_hash`, `activo`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'ADMIN', '$2y$10$xg5jO5IJyemCOMEYcay3ReB8iS/NqpXD6LBoFOBe8SJfN97IaKYBS', 1, '2026-09-17 15:15:07', '2026-09-16 15:14:39', '2026-09-17 15:15:07');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_accion`
--
ALTER TABLE `admin_accion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_bitacoras_sistema`
--
ALTER TABLE `admin_bitacoras_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_favoritos`
--
ALTER TABLE `admin_favoritos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_log`
--
ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu` (`menu`);

--
-- Indices de la tabla `admin_permiso_accion`
--
ALTER TABLE `admin_permiso_accion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_rol_accion` (`rol`,`accion`);

--
-- Indices de la tabla `admin_permiso_menu`
--
ALTER TABLE `admin_permiso_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_rol_menu` (`rol`,`menu`);

--
-- Indices de la tabla `admin_rol`
--
ALTER TABLE `admin_rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `admin_sesion`
--
ALTER TABLE `admin_sesion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_sesion_denegada`
--
ALTER TABLE `admin_sesion_denegada`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_tipo_accion`
--
ALTER TABLE `admin_tipo_accion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `admin_token`
--
ALTER TABLE `admin_token`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_usuario`
--
ALTER TABLE `admin_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persona_id` (`persona_id`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_clase_dep` (`clase_id`,`deportista_id`),
  ADD KEY `fk_asist_deportista` (`deportista_id`);

--
-- Indices de la tabla `autorizacion_evento`
--
ALTER TABLE `autorizacion_evento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ae` (`evento_id`,`deportista_id`,`acudiente_id`);

--
-- Indices de la tabla `autorizacion_firmada`
--
ALTER TABLE `autorizacion_firmada`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_af_deportista` (`deportista_id`),
  ADD KEY `fk_af_acudiente` (`acudiente_id`),
  ADD KEY `fk_af_tipo` (`tipo_autorizacion_id`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_clase_categoria` (`categoria_id`),
  ADD KEY `fk_clase_entrenador` (`entrenador_id`);

--
-- Indices de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comunicado_lectura`
--
ALTER TABLE `comunicado_lectura`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_com_per` (`comunicado_id`,`persona_id`),
  ADD KEY `fk_cl_persona` (`persona_id`);

--
-- Indices de la tabla `deportista`
--
ALTER TABLE `deportista`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persona_id` (`persona_id`),
  ADD KEY `fk_deportista_categoria` (`categoria_id`);

--
-- Indices de la tabla `deportista_acudiente`
--
ALTER TABLE `deportista_acudiente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_dep_acud` (`deportista_id`,`acudiente_id`),
  ADD KEY `fk_da_acudiente` (`acudiente_id`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doc_deportista` (`deportista_id`),
  ADD KEY `fk_doc_tipo` (`tipo_documento_id`);

--
-- Indices de la tabla `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evento_deportista`
--
ALTER TABLE `evento_deportista`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ev_dep` (`evento_id`,`deportista_id`),
  ADD KEY `fk_ed_deportista` (`deportista_id`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion` (`identificacion`);

--
-- Indices de la tabla `tipo_autorizacion`
--
ALTER TABLE `tipo_autorizacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persona_id` (`persona_id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `fk_usuario_rol` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_accion`
--
ALTER TABLE `admin_accion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `admin_bitacoras_sistema`
--
ALTER TABLE `admin_bitacoras_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `admin_favoritos`
--
ALTER TABLE `admin_favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `admin_log`
--
ALTER TABLE `admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT de la tabla `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `admin_permiso_accion`
--
ALTER TABLE `admin_permiso_accion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `admin_permiso_menu`
--
ALTER TABLE `admin_permiso_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `admin_rol`
--
ALTER TABLE `admin_rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `admin_sesion`
--
ALTER TABLE `admin_sesion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `admin_sesion_denegada`
--
ALTER TABLE `admin_sesion_denegada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `admin_tipo_accion`
--
ALTER TABLE `admin_tipo_accion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `admin_token`
--
ALTER TABLE `admin_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `admin_usuario`
--
ALTER TABLE `admin_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `autorizacion_evento`
--
ALTER TABLE `autorizacion_evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `autorizacion_firmada`
--
ALTER TABLE `autorizacion_firmada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comunicado`
--
ALTER TABLE `comunicado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comunicado_lectura`
--
ALTER TABLE `comunicado_lectura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `deportista`
--
ALTER TABLE `deportista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `deportista_acudiente`
--
ALTER TABLE `deportista_acudiente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evento`
--
ALTER TABLE `evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evento_deportista`
--
ALTER TABLE `evento_deportista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_autorizacion`
--
ALTER TABLE `tipo_autorizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `fk_asist_clase` FOREIGN KEY (`clase_id`) REFERENCES `clase` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asist_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `autorizacion_firmada`
--
ALTER TABLE `autorizacion_firmada`
  ADD CONSTRAINT `fk_af_acudiente` FOREIGN KEY (`acudiente_id`) REFERENCES `persona` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_af_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_af_tipo` FOREIGN KEY (`tipo_autorizacion_id`) REFERENCES `tipo_autorizacion` (`id`);

--
-- Filtros para la tabla `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `fk_clase_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `fk_clase_entrenador` FOREIGN KEY (`entrenador_id`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `comunicado_lectura`
--
ALTER TABLE `comunicado_lectura`
  ADD CONSTRAINT `fk_cl_comunicado` FOREIGN KEY (`comunicado_id`) REFERENCES `comunicado` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cl_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `deportista`
--
ALTER TABLE `deportista`
  ADD CONSTRAINT `fk_deportista_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_deportista_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `deportista_acudiente`
--
ALTER TABLE `deportista_acudiente`
  ADD CONSTRAINT `fk_da_acudiente` FOREIGN KEY (`acudiente_id`) REFERENCES `persona` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_da_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `fk_doc_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_doc_tipo` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento` (`id`);

--
-- Filtros para la tabla `evento_deportista`
--
ALTER TABLE `evento_deportista`
  ADD CONSTRAINT `fk_ed_deportista` FOREIGN KEY (`deportista_id`) REFERENCES `deportista` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ed_evento` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_persona` FOREIGN KEY (`persona_id`) REFERENCES `persona` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `admin_rol` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
