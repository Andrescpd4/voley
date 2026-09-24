-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-09-2026 a las 22:10:12
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
(7, 'cerrar-sesion', 'cerrarSesion', 'json', 'cerrar_sesion.php', 'N', NULL, 1, '2026-09-16 15:14:39'),
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
(47, 'afiliacion', 'ver', 'pagina', 'formulario.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(48, 'afiliacion', 'listar', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(49, 'afiliacion', 'asignar', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(50, 'afiliacion', 'agregar', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(51, 'afiliacion', 'modificar', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(52, 'afiliacion', 'eliminar', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(53, 'afiliacion', 'listarAcudientes', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53'),
(54, 'afiliacion', 'listarDeportistas', 'json', 'acciones.php', 'S', NULL, 1, '2026-09-23 21:10:53');

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

--
-- Volcado de datos para la tabla `admin_bitacoras_sistema`
--

INSERT INTO `admin_bitacoras_sistema` (`id`, `id_tipo_bitacora`, `datos_anteriores`, `datos_insertados`, `observacion`, `menu`, `id_usuario`, `fecha`) VALUES
(1, 1, 'b:0;', 'a:17:{s:4:\"user\";s:12:\"nuevousuario\";s:10:\"identifica\";s:8:\"11223344\";s:7:\"nombre1\";s:5:\"Nuevo\";s:7:\"nombre2\";s:0:\"\";s:9:\"apellido1\";s:7:\"Usuario\";s:9:\"apellido2\";s:0:\"\";s:8:\"telefono\";s:10:\"3005555555\";s:6:\"correo\";s:14:\"nuevo@test.com\";s:7:\"sexo_id\";s:1:\"1\";s:7:\"tipoide\";s:2:\"CC\";s:3:\"rol\";s:1:\"3\";s:13:\"fecha_ingreso\";s:0:\"\";s:7:\"salario\";s:0:\"\";s:20:\"auxilio_alimentacion\";s:0:\"\";s:13:\"tipo_contrato\";s:0:\"\";s:7:\"plantas\";s:0:\"\";s:5:\"clave\";s:5:\"12345\";}', 'Registro agregado con exito', 'usuarios', 1, '2026-09-21 20:34:00');

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
-- Estructura de tabla para la tabla `admin_front`
--

CREATE TABLE `admin_front` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `class` varchar(250) DEFAULT NULL,
  `active` int(11) DEFAULT 2,
  `color_primario` varchar(50) DEFAULT NULL,
  `color_secundario` varchar(50) DEFAULT NULL,
  `visible` int(11) DEFAULT 1,
  `img` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin_front`
--

INSERT INTO `admin_front` (`id`, `nombre`, `class`, `active`, `color_primario`, `color_secundario`, `visible`, `img`) VALUES
(1, 'Men├║ lateral', 'compact-wrapper', 1, '#7366ff', '#7366ff', 1, 'img/menu_lateral.png'),
(2, 'Men├║ Horizontal', 'horizontal-wrapper enterprice-type advance-layout', 2, '#497eb2', '#7366ff', 1, 'img/menu_horizontal.png'),
(3, 'Men├║ lateral (Iconos peque├▒os)', 'compact-sidebar compact-small material-icon', 2, '#5058a1', '#7366ff', 1, 'img/menu_lateral_iconos_minis.png'),
(4, 'Men├║ lateral (Flotante)', 'compact-wrapper modern-type', 2, '#7366ff', '#7366ff', 1, 'img/menu_lateral_flotante.png'),
(5, 'Sistema centrado', 'compact-wrapper box-layout', 2, '#7366ff', '#7366ff', 2, 'img/menu_centrado.png'),
(6, 'Men├║ lateral (Iconos grandes)', 'compact-sidebar', 2, '#7366ff', '#7366ff', 2, 'img/menu_lateral_iconos_grandes.png');

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
(189, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 15:57:18'),
(190, 1, 'descarga.php', 1, 'EXITO', 'iniciar-sesion', 'iniciar', '2026-09-17 18:26:18'),
(191, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-17 19:03:38'),
(192, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-17 19:03:39'),
(193, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-17 19:03:39'),
(194, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 19:03:51'),
(195, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 19:03:52'),
(196, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-17 19:04:00'),
(197, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-17 19:04:01'),
(198, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarRoles', '2026-09-17 19:04:01'),
(199, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listarPersonasSinUsuario', '2026-09-17 19:04:27'),
(200, 1, 'pagina.php', 1, 'EXITO', 'roles', 'ver', '2026-09-17 19:04:41'),
(201, 1, 'descarga.php', 1, 'EXITO', 'roles', 'listar', '2026-09-17 19:04:42'),
(202, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 19:04:46'),
(203, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 19:04:47'),
(204, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 19:05:59'),
(205, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 19:05:59'),
(206, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:48:59'),
(207, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:49:01'),
(208, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:50:31'),
(209, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:54:24'),
(210, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:54:26'),
(211, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:03'),
(212, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:55:04'),
(213, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:18'),
(214, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:55:19'),
(215, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:21'),
(216, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:21'),
(217, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:22'),
(218, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:22'),
(219, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:55:23'),
(220, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-17 20:55:29'),
(221, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-17 20:55:29'),
(222, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-17 20:55:29'),
(223, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:55:35'),
(224, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:55:35'),
(225, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-17 20:56:57'),
(226, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-17 20:56:58'),
(227, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-17 20:56:58'),
(228, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:57:03'),
(229, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:57:04'),
(230, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-17 20:57:36'),
(231, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-17 20:57:37'),
(232, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-17 20:57:38'),
(233, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 20:58:19'),
(234, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 20:58:20'),
(235, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 21:45:02'),
(236, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 21:45:22'),
(237, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-17 21:45:24'),
(238, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 21:47:50'),
(239, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-17 21:50:05'),
(240, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-18 20:58:57'),
(241, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-18 21:12:45'),
(242, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-18 21:13:08'),
(243, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-18 21:13:29'),
(244, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-18 21:15:38'),
(245, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-18 21:16:20'),
(246, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-18 21:16:21'),
(247, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-18 21:16:21'),
(248, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-18 21:16:25'),
(249, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-18 21:16:33'),
(250, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 13:31:47'),
(251, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 13:32:32'),
(252, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 13:32:34'),
(253, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 13:32:35'),
(254, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 13:32:35'),
(255, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 13:33:33'),
(256, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 13:33:34'),
(257, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 13:33:34'),
(258, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 13:33:58'),
(259, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 14:56:35'),
(260, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 14:56:43'),
(261, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 14:59:51'),
(262, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 14:59:58'),
(263, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 15:00:03'),
(264, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 15:01:17'),
(265, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 15:02:08'),
(266, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 15:05:40'),
(267, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-21 15:21:46'),
(268, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 15:22:35'),
(269, 1, 'pagina.php', 1, 'EXITO', 'clave_usuarios', 'ver', '2026-09-21 15:25:13'),
(270, 1, 'pagina.php', 1, 'EXITO', 'restaurar_clave', 'ver', '2026-09-21 15:25:59'),
(271, 1, 'descarga.php', 1, 'EXITO', 'clave_usuarios', 'buscar', '2026-09-21 15:33:44'),
(272, 1, 'descarga.php', 1, 'EXITO', 'restaurar_clave', 'buscar', '2026-09-21 15:34:31'),
(273, 1, 'descarga.php', 1, 'EXITO', 'clave_usuarios', 'cambiar', '2026-09-21 15:35:20'),
(274, 1, 'descarga.php', 1, 'EXITO', 'clave_usuarios', 'cambiar', '2026-09-21 15:38:12'),
(275, 1, 'descarga.php', 1, 'EXITO', 'restaurar_clave', 'restaurar', '2026-09-21 15:38:56'),
(276, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:39:42'),
(277, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:39:45'),
(278, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:39:47'),
(279, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:39:49'),
(280, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 15:40:07'),
(281, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:48:34'),
(282, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:57:55'),
(283, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:57:58'),
(284, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:58:01'),
(285, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 15:58:08'),
(286, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:58:20'),
(287, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 15:58:25'),
(288, 6237, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 15:58:43'),
(289, 6237, 'pagina.php', 1, 'EXITO', 'roles', 'ver', '2026-09-21 15:59:37'),
(290, 6237, 'descarga.php', 1, 'EXITO', 'roles', 'listar', '2026-09-21 15:59:37'),
(291, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 15:59:42'),
(292, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:00:12'),
(293, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 16:20:29'),
(294, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 16:21:51'),
(295, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:29:50'),
(296, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:29:53'),
(297, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:29:54'),
(298, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:29:54'),
(299, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:29:59'),
(300, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:30:00'),
(301, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:30:00'),
(302, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:30:03'),
(303, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:30:03'),
(304, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:30:03'),
(305, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:30:07'),
(306, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:30:14'),
(307, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:30:14'),
(308, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:30:14'),
(309, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:30:23'),
(310, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:30:23'),
(311, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:30:23'),
(312, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:30:26'),
(313, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:30:29'),
(314, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 16:30:33'),
(315, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 16:42:08'),
(316, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:42:10'),
(317, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:42:15'),
(318, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:42:16'),
(319, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:42:16'),
(320, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:42:22'),
(321, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:42:23'),
(322, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:42:23'),
(323, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:42:27'),
(324, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:42:28'),
(325, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:42:28'),
(326, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:42:31'),
(327, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 16:42:33'),
(328, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 16:42:38'),
(329, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 16:43:00'),
(330, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 16:43:01'),
(331, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 16:43:01'),
(332, 6237, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 16:43:08'),
(333, 6237, 'pagina.php', 1, 'EXITO', 'restaurar_clave', 'ver', '2026-09-21 16:46:07'),
(334, 6237, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 16:46:12'),
(335, 6237, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 16:52:49'),
(336, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 16:52:54'),
(337, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-21 18:43:34'),
(338, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-21 18:43:54'),
(339, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 18:44:12'),
(340, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:44:36'),
(341, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 18:44:53'),
(342, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 18:45:45'),
(343, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 18:46:14'),
(344, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:53:52'),
(345, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:53:56'),
(346, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-21 18:53:56'),
(347, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 18:55:24'),
(348, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-21 18:55:25'),
(349, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:55:33'),
(350, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:55:37'),
(351, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-21 18:55:37'),
(352, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:55:53'),
(353, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:55:54'),
(354, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:55:54'),
(355, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:55:54'),
(356, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 18:55:55'),
(357, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'listar', '2026-09-21 18:55:58'),
(358, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:55:58'),
(359, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 18:56:07'),
(360, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:09'),
(361, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:09'),
(362, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 18:56:09'),
(363, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 18:56:14'),
(364, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:15'),
(365, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:15'),
(366, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 18:56:16'),
(367, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 18:56:20'),
(368, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:22'),
(369, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:22'),
(370, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 18:56:22'),
(371, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 18:56:28'),
(372, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:29'),
(373, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 18:56:29'),
(374, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 18:56:29'),
(375, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 19:02:49'),
(376, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 19:17:40'),
(377, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 19:17:43'),
(378, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 19:20:00'),
(379, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 19:20:10'),
(380, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-21 20:16:47'),
(381, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 20:30:01'),
(382, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 20:30:03'),
(383, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-21 20:32:15'),
(384, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'agregar', '2026-09-21 20:33:59'),
(385, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 20:39:14'),
(386, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 20:39:15'),
(387, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 20:39:39'),
(388, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 20:39:40'),
(389, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 20:39:40'),
(390, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 20:39:40'),
(391, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 20:39:48'),
(392, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 20:39:49'),
(393, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 20:39:52'),
(394, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 20:40:06'),
(395, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 20:40:08'),
(396, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 20:40:09'),
(397, 1, 'pagina.php', 1, 'EXITO', 'usuarios', 'ver', '2026-09-21 21:20:03'),
(398, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:20:05'),
(399, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-21 21:20:05'),
(400, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-21 21:20:08'),
(401, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:32:12'),
(402, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:32:14'),
(403, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 21:32:38'),
(404, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:32:39'),
(405, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 21:32:39'),
(406, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:32:39'),
(407, 6237, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-21 21:32:45'),
(408, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:32:45'),
(409, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:32:46'),
(410, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-21 21:32:46'),
(411, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:32:48'),
(412, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:32:48'),
(413, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:50:26'),
(414, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:50:26'),
(415, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:50:27'),
(416, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:50:28'),
(417, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:50:31'),
(418, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:50:32'),
(419, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 21:50:34'),
(420, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 21:50:39'),
(421, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:50:41'),
(422, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:50:42'),
(423, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-21 21:50:44'),
(424, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-21 21:50:53'),
(425, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-21 21:50:56'),
(426, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-21 21:50:56'),
(427, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 13:06:28'),
(428, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 13:08:33'),
(429, 6237, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-22 13:42:55'),
(430, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:42:56'),
(431, 6237, 'pagina.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-22 13:42:59'),
(432, 1, 'pagina.php', 1, 'EXITO', 'inicio', 'ver', '2026-09-22 13:43:16'),
(433, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:43:17'),
(434, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:43:17'),
(435, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 13:43:17'),
(436, 1, 'pagina.php', 1, 'EXITO', 'diseno', 'ver', '2026-09-22 13:43:38'),
(437, 1, '404.php', 2, 'Archivo no encontrado', 'diseno', 'ver', '2026-09-22 13:43:38'),
(438, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:43:38'),
(439, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-22 13:43:45'),
(440, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:43:45'),
(441, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-22 13:43:48'),
(442, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-22 13:43:54'),
(443, 1, 'pagina.php', 1, 'EXITO', 'permisos-por-rol', 'ver', '2026-09-22 13:53:25'),
(444, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:53:26'),
(445, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-22 13:53:34'),
(446, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-22 13:53:46'),
(447, 1, 'pagina.php', 1, 'EXITO', 'diseno', 'ver', '2026-09-22 13:53:50'),
(448, 1, '404.php', 2, 'Archivo no encontrado', 'diseno', 'ver', '2026-09-22 13:53:50'),
(449, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'set_token', '2026-09-22 13:53:50'),
(450, 1, 'pagina.php', 1, 'EXITO', 'diseno', 'ver', '2026-09-22 14:25:58'),
(451, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 14:39:55'),
(452, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 15:18:45'),
(453, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 15:25:11'),
(454, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 15:25:19'),
(455, 1, 'pagina.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-22 15:25:58'),
(456, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 16:07:30'),
(457, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 16:54:02'),
(458, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 16:54:07'),
(459, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 16:57:59'),
(460, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 19:04:13'),
(461, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 19:05:51'),
(462, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 19:07:00'),
(463, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 19:07:10'),
(464, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 19:07:15'),
(465, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 19:08:37'),
(466, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-22 19:08:53'),
(467, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-22 19:08:55'),
(468, 1, 'descarga.php', 1, 'EXITO', 'roles', 'listar', '2026-09-22 19:08:58'),
(469, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 19:09:06'),
(470, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 19:59:47'),
(471, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 20:30:41'),
(472, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 20:51:23'),
(473, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 21:02:40'),
(474, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 21:02:44'),
(475, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 21:53:57'),
(476, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-22 21:54:05'),
(477, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-22 21:54:08'),
(478, 1, 'descarga.php', 1, 'EXITO', 'usuarios', 'listar', '2026-09-22 21:54:11'),
(479, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 21:54:13'),
(480, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-22 21:54:24'),
(481, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 21:54:26'),
(482, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 21:56:11'),
(483, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-22 21:56:30'),
(484, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-23 12:48:23'),
(485, 1, 'pagina.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-23 12:48:27'),
(486, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 12:48:37'),
(487, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 15:03:25'),
(488, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 15:03:42'),
(489, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-23 15:05:23'),
(490, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-23 15:07:20'),
(491, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-23 15:07:21'),
(492, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 15:07:24'),
(493, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-23 15:07:25'),
(494, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-23 15:23:10'),
(495, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-23 15:23:42'),
(496, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 15:24:44'),
(497, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 15:24:48'),
(498, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 16:04:36'),
(499, 6237, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-23 16:39:14'),
(500, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:22:05'),
(501, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:22:18'),
(502, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:22:26'),
(503, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:22:35'),
(504, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:40:20'),
(505, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:40:23'),
(506, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:45:48'),
(507, 6237, 'pagina.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-23 18:45:50'),
(508, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:47:08'),
(509, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:47:30'),
(510, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:51:02'),
(511, 6237, 'pagina.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-23 18:54:42'),
(512, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 18:54:56'),
(513, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 19:19:07'),
(514, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 19:19:10'),
(515, 1, 'descarga.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-23 19:19:13'),
(516, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 19:19:47'),
(517, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 19:44:51'),
(518, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-23 19:45:32'),
(519, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 19:45:35'),
(520, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 20:01:32'),
(521, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-23 20:35:27'),
(522, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-23 20:35:32'),
(523, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'cargar', '2026-09-23 21:11:53'),
(524, 1, 'descarga.php', 1, 'EXITO', 'permisos-por-rol', 'guardar', '2026-09-23 21:12:07'),
(525, 1, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listar', '2026-09-23 21:12:11'),
(526, 1, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listarAcudientes', '2026-09-23 21:15:36'),
(527, 1, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listarDeportistas', '2026-09-23 21:15:36'),
(528, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-23 21:43:14'),
(529, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-23 22:01:43'),
(530, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-23 22:01:59'),
(531, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-23 22:04:17'),
(532, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 12:43:42'),
(533, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 13:11:58'),
(534, 1, 'descarga.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-24 13:12:01'),
(535, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 13:12:19'),
(536, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 13:12:28'),
(537, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 13:14:12'),
(538, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 13:18:37'),
(539, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 14:56:18'),
(540, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 14:56:34'),
(541, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 14:56:54'),
(542, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 14:57:27'),
(543, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:08:12'),
(544, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 15:08:35'),
(545, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:09:05'),
(546, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:10:00'),
(547, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:10:06'),
(548, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 15:10:15'),
(549, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:10:21'),
(550, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 15:11:12'),
(551, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:11:25'),
(552, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:32:07'),
(553, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 15:32:36'),
(554, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:32:41'),
(555, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 15:33:32'),
(556, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:33:51'),
(557, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:34:01'),
(558, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 15:41:39'),
(559, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 15:41:41'),
(560, 1, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listar', '2026-09-24 15:53:18'),
(561, 6237, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listar', '2026-09-24 16:04:10'),
(562, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:04:16'),
(563, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 16:04:25'),
(564, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:06:17'),
(565, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'guardar', '2026-09-24 16:07:00'),
(566, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:07:36'),
(567, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:07:40'),
(568, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:23:43'),
(569, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:25:16'),
(570, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:26:07'),
(571, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:26:31'),
(572, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:26:54'),
(573, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:28:29'),
(574, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:28:49'),
(575, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:29:11'),
(576, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:29:20'),
(577, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:31:30'),
(578, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:31:46'),
(579, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:32:01'),
(580, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:34:57'),
(581, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:36:21'),
(582, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:38:16'),
(583, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:40:30'),
(584, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:41:18'),
(585, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 16:42:42'),
(586, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:43:40'),
(587, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:47:13'),
(588, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 16:58:25'),
(589, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 18:38:47'),
(590, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 18:38:56'),
(591, 1, 'descarga.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-24 18:39:04'),
(592, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 18:39:24'),
(593, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 18:40:44'),
(594, 1, 'descarga.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-24 18:40:53'),
(595, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 18:53:10'),
(596, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 18:53:46'),
(597, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 18:54:34'),
(598, 1, 'descarga.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-24 19:15:34'),
(599, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 19:16:19'),
(600, 1, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 19:16:34'),
(601, 1, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listar', '2026-09-24 19:16:51');
INSERT INTO `admin_log` (`id`, `id_persona`, `archivo`, `tipo`, `mensaje`, `menu`, `accion`, `fecha`) VALUES
(602, 1, 'descarga.php', 1, 'EXITO', 'cerrar-sesion', 'cerrarSesion', '2026-09-24 19:16:58'),
(603, 1, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 19:17:18'),
(604, 6237, 'descarga.php', 1, 'EXITO', 'diseno', 'obtener', '2026-09-24 19:22:20'),
(605, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 19:28:18'),
(606, 6237, 'descarga.php', 1, 'EXITO', 'inicio', 'dashboard', '2026-09-24 19:28:22'),
(607, 6237, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listar', '2026-09-24 19:40:04'),
(608, 6237, 'descarga.php', 1, 'EXITO', 'afiliacion', 'listar', '2026-09-24 19:40:14');

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
  `descripcion` varchar(300) DEFAULT NULL,
  `_self` varchar(10) DEFAULT '_self'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `menu`, `padre`, `nombre`, `ruta`, `accion`, `orden`, `visible`, `acceso`, `icono`, `descripcion`, `_self`) VALUES
(1, 'inicio', NULL, 'Dashboard', 'modulos/inicio', 'ver', 10, 'S', '3', 'ri-dashboard-line', NULL, '_self'),
(2, 'iniciar-sesion', NULL, 'Iniciar Sesión', 'modulos/sesion', 'ver', 999, 'N', '1', '', NULL, '_self'),
(3, 'cerrar-sesion', NULL, 'Cerrar Sesión', 'modulos/sesion', 'cerrarSesion', 999, 'N', '3', '', NULL, '_self'),
(4, 'perfil', NULL, 'Mi Perfil', 'modulos/admin/usuarios', 'perfil', 998, 'N', '3', 'ri-user-line', NULL, '_self'),
(10, 'administracion', NULL, 'Administración', '#', 'ver', 20, 'S', '7', 'ri-settings-3-line', NULL, '_self'),
(11, 'usuarios', 'administracion', 'Usuarios', 'modulos/gestion_usuarios/listado_usuarios', 'ver', 21, 'S', '7', 'ri-user-settings-line', NULL, '_self'),
(12, 'roles', 'administracion', 'Roles', 'modulos/admin/roles', 'ver', 22, 'S', '7', 'ri-shield-user-line', NULL, '_self'),
(13, 'permisos-por-rol', 'administracion', 'Permisos por Rol', 'modulos/admin/permisos-rol', 'ver', 23, 'S', '7', 'ri-lock-password-line', NULL, '_self'),
(20, 'escuela', NULL, 'Escuela Voley', '#', 'ver', 30, 'S', '7', 'ri-medal-line', NULL, '_self'),
(21, 'afiliacion', 'escuela', 'Afiliaciones', 'modulos/afiliacion', 'ver', 31, 'S', '7', 'ri-user-add-line', NULL, '_self'),
(22, 'deportistas', 'escuela', 'Ficha Deportistas', 'modulos/escuela/deportistas', 'ver', 32, 'S', '7', 'ri-team-line', NULL, '_self'),
(23, 'asistencia', 'escuela', 'Control Asistencia', 'modulos/asistencia', 'ver', 33, 'S', '7', 'ri-calendar-check-line', NULL, '_self'),
(24, 'eventos', 'escuela', 'Eventos y Torneos', 'modulos/eventos', 'ver', 34, 'S', '7', 'ri-trophy-line', NULL, '_self'),
(25, 'comunicados', 'escuela', 'Comunicados', 'modulos/comunicados', 'ver', 35, 'S', '7', 'ri-broadcast-line', NULL, '_self'),
(27, 'clave_usuarios', 'administracion', 'Cambiar Clave Usuario', 'modulos/gestion_usuarios/clave_usuarios', 'ver', 22, 'S', '7', 'ri-lock-line', NULL, '_self'),
(28, 'restaurar_clave', 'administracion', 'Restaurar Clave Usuario', 'modulos/gestion_usuarios/restaurar_clave', 'ver', 23, 'S', '7', 'ri-restart-line', NULL, '_self');

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
(88, 1, 10),
(89, 1, 11),
(90, 1, 12),
(91, 1, 13),
(92, 1, 14),
(93, 1, 20),
(94, 1, 21),
(95, 1, 22),
(96, 1, 23),
(97, 1, 24),
(98, 1, 30),
(99, 1, 31),
(100, 1, 32),
(101, 1, 33),
(102, 1, 36),
(103, 1, 37),
(104, 1, 38),
(105, 1, 39),
(106, 1, 40),
(107, 1, 41),
(54, 2, 12),
(55, 2, 13),
(56, 2, 14),
(57, 2, 22),
(58, 2, 23),
(59, 2, 24),
(60, 2, 33),
(178, 4, 12),
(180, 4, 13),
(179, 4, 14),
(181, 4, 22),
(183, 4, 23),
(182, 4, 24),
(185, 4, 33),
(184, 4, 38),
(186, 4, 41),
(189, 4, 43),
(188, 4, 44),
(190, 4, 45),
(187, 4, 46),
(198, 4, 47),
(194, 4, 48),
(192, 4, 49),
(191, 4, 50),
(197, 4, 51),
(193, 4, 52),
(195, 4, 53),
(196, 4, 54);

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
(91, 1, 'administracion'),
(98, 1, 'afiliacion'),
(100, 1, 'asistencia'),
(95, 1, 'clave_usuarios'),
(102, 1, 'comunicados'),
(99, 1, 'deportistas'),
(97, 1, 'escuela'),
(101, 1, 'eventos'),
(94, 1, 'permisos-por-rol'),
(96, 1, 'restaurar_clave'),
(93, 1, 'roles'),
(92, 1, 'usuarios'),
(64, 2, 'administracion'),
(63, 2, 'inicio'),
(65, 2, 'permisos-por-rol'),
(67, 2, 'roles'),
(66, 2, 'usuarios'),
(27, 3, 'afiliacion'),
(28, 3, 'comunicados'),
(26, 3, 'escuela'),
(167, 4, 'administracion'),
(175, 4, 'afiliacion'),
(177, 4, 'asistencia'),
(170, 4, 'clave_usuarios'),
(179, 4, 'comunicados'),
(176, 4, 'deportistas'),
(174, 4, 'escuela'),
(178, 4, 'eventos'),
(171, 4, 'permisos-por-rol'),
(172, 4, 'restaurar_clave'),
(169, 4, 'roles'),
(168, 4, 'usuarios');

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
(4, '10000001', '83n80u543hdqeqvio5iogeo5a2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/permisos-por-rol', '::1', '2026-09-17 10:15:07', '2026-09-17 10:15:07', 'N'),
(5, '10000001', '58o0h16ck0q0ca3skdjh3qnfng', 'curl/8.21.0', '', '::1', '2026-09-17 13:25:52', '2026-09-17 13:25:52', 'N'),
(6, '10000001', 'ueqftlhcr7mk6953kfkttb2far', 'curl/8.21.0', '', '::1', '2026-09-17 13:26:23', '2026-09-17 13:26:23', 'N'),
(7, '10000001', '87k0p50mlqa4j5lmakplk6j97f', 'curl/8.21.0', '', '::1', '2026-09-17 13:29:59', '2026-09-17 13:29:59', 'N'),
(8, '10000001', '4i2mg6do8r6j3nunot5iorh55b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/permisos-por-rol', '::1', '2026-09-17 14:03:37', '2026-09-17 14:03:37', 'N'),
(9, '10000001', 'kdhhdal50n257k983l1oari9b6', '', '', NULL, '2026-09-17 15:50:47', '2026-09-17 15:50:47', 'N'),
(10, '10000001', 'a6ici8i1jtl6trhisi20b3is60', 'curl/8.21.0', '', '::1', '2026-09-17 16:08:38', '2026-09-17 16:08:38', 'N'),
(11, '10000001', 'kud0d6pq6e2kci7s0061ror2l1', 'curl/8.21.0', '', '::1', '2026-09-18 16:12:41', '2026-09-18 16:12:41', 'N'),
(12, '10000001', 'dmn71n3bds5j1lbbcthe3k6jfj', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/permisos-por-rol', '::1', '2026-09-18 16:16:19', '2026-09-18 16:16:19', 'N'),
(13, '10000001', 'mo095k860ol5f267r757p8tfoa', 'curl/8.21.0', '', '::1', '2026-09-21 09:55:53', '2026-09-21 09:55:53', 'N'),
(14, '10000001', 'qeovvtsp9nkop5kbk6pnh29c7v', 'curl/8.21.0', '', '::1', '2026-09-21 10:47:26', '2026-09-21 10:47:26', 'N'),
(15, '10000001', 'u7aj8sslcvfd7b5l8uhsd3qpq0', 'curl/8.21.0', '', '::1', '2026-09-21 13:43:07', '2026-09-21 13:43:07', 'N'),
(16, '10000001', 'clpn503amcfn63c6o56t4865lg', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-21 13:56:06', '2026-09-21 13:56:06', 'N'),
(17, '10000001', '9r2hdebo0g463h1j4t0vkgasfg', 'curl/8.21.0', '', '::1', '2026-09-21 15:15:25', '2026-09-21 15:15:25', 'N'),
(18, '10000001', '1m6k6f9br4vcrv6bk2vbfkil8h', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-21 15:39:37', '2026-09-21 15:39:37', 'N'),
(19, '10000001', 'ei3vmv8d33lmhh20dp852q7u6a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-22 08:43:15', '2026-09-22 08:43:15', 'N'),
(20, '10000001', 'ald1t5op6qstjrf6f16h5s4ghh', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', 'http://localhost/voley/', '::1', '2026-09-22 11:07:27', '2026-09-22 11:07:27', 'N'),
(21, '10000001', '0duer6jf6tjk7fjaf4vbpkf59p', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-22 14:04:09', '2026-09-22 14:04:09', 'N'),
(22, '10000001', '8cohvc32d8q7pd3idk7h0ev9i1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-22 16:53:55', '2026-09-22 16:53:55', 'N'),
(23, '10000001', 'pan3dsuu3p6pudh8bh6pc9v0v1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-23 07:48:35', '2026-09-23 07:48:35', 'N'),
(24, '10000001', 'g1kuq8270hbd8vrn47lbhtt78k', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-23 13:47:06', '2026-09-23 13:47:06', 'N'),
(25, '10000001', 'eq91hiofqmbaauiph7cgmfd4i6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-23 13:54:54', '2026-09-23 13:54:54', 'N'),
(26, '10000001', 'r2nm7rnaj4p15cjabd2fvilpfa', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-23 14:19:45', '2026-09-23 14:19:45', 'N'),
(27, '10000001', '0s936bq9hte21g92dlpjg99p5m', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-24 08:12:17', '2026-09-24 08:12:17', 'N'),
(28, '10000001', 'jdssn5fghl8luvh0pn1ukfo64l', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-24 10:11:09', '2026-09-24 10:11:09', 'N'),
(29, '10000001', 'n49k01q3i387pfou24f4icjeu8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-24 13:39:23', '2026-09-24 13:39:23', 'N'),
(30, '10000001', 'kir92k392d45jcamth8mer52pf', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-24 13:53:06', '2026-09-24 13:53:06', 'N'),
(31, '10000001', 'ms58vom3q6fc5tne2mepf6bocu', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-24 14:16:17', '2026-09-24 14:16:17', 'N'),
(32, '10000001', 'gojmhu7luqrjkm89vs81taptkq', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/afiliacion', '::1', '2026-09-24 14:17:16', '2026-09-24 14:17:16', 'N');

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
(4, 'pga8ruq6ho41cecr44d1sung00', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-16 10:16:30', 'admin', 1),
(5, '58o0h16ck0q0ca3skdjh3qnfng', 'curl/8.21.0', '', '::1', '2026-09-17 13:26:18', 'ADMIN', 3),
(6, 'hpd1j43fdhmrub61kkvv1bca8q', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'http://localhost/voley/permisos-por-rol', '::1', '2026-09-18 16:16:13', 'admin', 1),
(7, 'b73aotpl2tdc1r365mo42q4ef0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/', '::1', '2026-09-24 13:39:14', 'ADMIN', 1),
(8, 't7j1bs9hfi7ol31iuet3vetj48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'http://localhost/voley/afiliacion', '::1', '2026-09-24 14:17:11', 'admin', 1);

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
(36, 'x1Cvf1CnLIUPkHb5FT3c7K83JZsvTZ9abIRkTHtZv5QjMQn/xd0VHDAApzTO3Amj0kBwzBekm10lpTK/1bhpFBw5vQ7xwmAWX72Yft0HTvvYrOV532io/porvLET27/k', '2026-09-17', 1, 1, '2026-09-17 15:15:09'),
(37, 'gLPz71e168jMzKj1Htz1+Y65O0fppBmVXR/XHpDcPE7UVrpvx9cLi/8fC8w7rIvo0O9dC04mu0MqO694/dF6SQ==', '2026-09-17', 1, 1, '2026-09-17 18:25:52'),
(38, 'gLPz71e168jMzKj1Htz1+QnZrN1K9LNj6u8q/k/uiEjvV90BjVF5x2Md0lvbcvXzh6lmdH6Gxw0GdxLpSxmL1g==', '2026-09-17', 1, 1, '2026-09-17 18:26:23'),
(39, 'gLPz71e168jMzKj1Htz1+cuEZvOG6bAT8lipGb+jLvJksNqVJU6FtD1JyTMvfATdfZBTRr0shBMzMrxevZmwng==', '2026-09-17', 1, 1, '2026-09-17 18:29:59'),
(42, 'gLPz71e168jMzKj1Htz1+WyizlGQfWzeqEahi6ffbHl43BECLhO7P8q3idB4dAXIwgOvAxN+BoXcg+l3lMhOSA==', '2026-09-17', 1, 1, '2026-09-17 20:50:47'),
(43, 'x1Cvf1CnLIUPkHb5FT3c7K83JZsvTZ9abIRkTHtZv5QjMQn/xd0VHDAApzTO3AmjkGX6V/IAE7o9Cb4kKfdFxxwLWK8WipS3lA9jjtgzfVJ6CQAih0xRJW+42uqzTRL5', '2026-09-17', 1, 1, '2026-09-17 20:55:29'),
(45, 'x1Cvf1CnLIUPkHb5FT3c7K83JZsvTZ9abIRkTHtZv5QjMQn/xd0VHDAApzTO3Amj6eA8sUJg9xdJ1paOqiupR3rhNs8ro3pDK2iXyG6nhStoTSip42eBfSnsL7oYI11K', '2026-09-17', 1, 1, '2026-09-17 20:57:37'),
(46, 'gLPz71e168jMzKj1Htz1+W6cbym3lqZDx/SdbFK0TPsVO92vBdmj2rCAijwAR801kjywD9DDNgilS+3Sv80/gA==', '2026-09-17', 1, 1, '2026-09-17 21:08:38'),
(47, 'gLPz71e168jMzKj1Htz1+f1fwi1DgqI2ABCsRfLQ+vt3bYCJt37xccfS9Biw+wpWIPrtBNRrnMF6KQtLEVb77A==', '2026-09-18', 1, 1, '2026-09-18 21:12:41'),
(49, 'x1Cvf1CnLIUPkHb5FT3c7P118pP3EIwWUQ//uWQ5e0uacuR/iPgP5BiRJwnzIFlxaB6WpO0zzvc9vNLhFXbIUOeO8Tma47DvsqnjpIOw2JUVXYoHHhkE6XjCx+OcGd5i', '2026-09-18', 1, 1, '2026-09-18 21:16:21'),
(51, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHumnLoY/zjXj5UaGGFirobekjusNk+RwydVLDnzpfHY7hD+V19QVJRFx8ICgUVO6KKtZcuHaxo2iduVbbnkhnBCD', '2026-09-21', 1, 1, '2026-09-21 13:33:34'),
(52, 'gLPz71e168jMzKj1Htz1+QNfmrO9Z9Qhawin1H+W4klDtJRvyCfWrpQcM1cADwG0UBdAk5JYcPV1Mh5J1OHFgw==', '2026-09-21', 1, 1, '2026-09-21 14:55:53'),
(53, 'gLPz71e168jMzKj1Htz1+QzPNhEx1yo/TqloHVdoSoXMxTnkNRWfg8Xlg+ZrP2yZ4P8XQFc5Kx1dpGz4eVy1bg==', '2026-09-21', 1, 1, '2026-09-21 15:47:26'),
(63, 'gLPz71e168jMzKj1Htz1+VA7sNH48IzXpJm5GGt6kXazv7tFUIgvK29SzlmkrxCNhoqEbOZwbAntpUURlNYMwQ==', '2026-09-21', 1, 1, '2026-09-21 18:43:07'),
(66, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHuk45IIBWDCt8S1NsFfnolpDUrpfFXkYlaQO0LYiQfyNH9pkv2NdLJFjP+kSh98lFED0WtPHAznJZDi3uNwrN7Ic', '2026-09-21', 6237, 1, '2026-09-21 18:55:58'),
(74, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHumnLoY/zjXj5UaGGFirobekgvzc/m9Oxhj4SNNr3ekHpQf94tjgxQOT0UGBD9Vrg8IpEdJFtGbvH9xGSseyVBFx', '2026-09-21', 1, 1, '2026-09-21 18:56:29'),
(75, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHumnLoY/zjXj5UaGGFirobekvItbZERdWY8jjjItDTv98JyTPlIQAhOx619gu6KuTcNvt8BVlhVc7hZOh+NvyA38', '2026-09-21', 1, 1, '2026-09-21 18:56:30'),
(76, 'gLPz71e168jMzKj1Htz1+XPyKKCMk2sOrv4Q1y/J7CjRzR7pevpSStQOZO3q8xe7Sxue5xS+VsuKyexe5VTcSw==', '2026-09-21', 1, 1, '2026-09-21 20:15:25'),
(80, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHumnLoY/zjXj5UaGGFirobeksZQkAgdIC1H/CWlW3Mkl4lVkzoSKlyoyrMWszGMioX3PJU9LfNPmb5HtFTQgocpQ', '2026-09-21', 1, 1, '2026-09-21 20:39:41'),
(83, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHumnLoY/zjXj5UaGGFirobekhL9TCLjuuYq6ljeJ+UDnxSnjP/CgAlbR9nfq311EKLkwDr02YvnDXPGcb5umNA6J', '2026-09-21', 1, 1, '2026-09-21 21:20:05'),
(87, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHuk45IIBWDCt8S1NsFfnolpDlLGnzp+dkC8TF9/bf+YIdGxvkKHFBqy8tNPiGKKCPtrLod8WvXNSHsHYk0om5TTq', '2026-09-21', 6237, 1, '2026-09-21 21:32:45'),
(89, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHuk45IIBWDCt8S1NsFfnolpDdw4AU+8M/7sFyTsMOUVnsvHr/U43iIZcNaC43HDHLvy4njlDv2rYkOQMkfOGxzSy', '2026-09-21', 6237, 1, '2026-09-21 21:32:48'),
(94, 'x1Cvf1CnLIUPkHb5FT3c7Cq5szvCVuW1xU01EptwHuk45IIBWDCt8S1NsFfnolpDJZACfMm/M03SC7doqFNay4Q6fEOazBJ+dGE1Dy4l85EAPFli1jbpSBtPRpOGOzSf', '2026-09-21', 6237, 1, '2026-09-21 21:50:56'),
(97, 'x1Cvf1CnLIUPkHb5FT3c7PT1rg8l+y4yDKSNjUClzXHQPecyVPzhInE1K+wPqHVcWXgjAL8FTT7p62C2zO4lPT59woyOr1dmvXt2gws/1PFcm4xlCcPA2WPDotFPwDhs', '2026-09-22', 1, 1, '2026-09-22 13:43:17'),
(101, 'x1Cvf1CnLIUPkHb5FT3c7PT1rg8l+y4yDKSNjUClzXHQPecyVPzhInE1K+wPqHVcZABGg4vQEctE/RwqigGmC0yxXmR9wqI2VyUjHL04XEfHHeSY8g6AzWBpQRiLMUsn', '2026-09-22', 1, 1, '2026-09-22 13:53:50'),
(108, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHQL2WyrcXVT+3xdXhNHROO3w8igmoe2qVSt2OAhbmcxXOuAVaPlAOcwtqbcNmG8Dj', '2026-09-22', 1, 1, '2026-09-22 15:25:20'),
(113, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHlUKIqukGnQG7W11W1Z+5BhDlZKJUPsb/HHRp/mqqfX4U/UuqdpWyQYljX7mkSgAW', '2026-09-22', 1, 1, '2026-09-22 15:25:53'),
(120, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHtusuw+BCIicnU9iynaoZgfRqT521dq/aBJhoVO5m9WlppOpge/RJkc5cdXQ7wAlz', '2026-09-22', 1, 1, '2026-09-22 16:15:14'),
(126, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHeh4M7+cVHF8U5etsDKzf9t7uklIPOPIWOP/jkBitz/BXMY1w4FQuzDNCXmLykEaG', '2026-09-22', 1, 1, '2026-09-22 16:57:59'),
(129, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCH+SO5eY84FbS6WKeTUZC0adDCdsXPkOctzs+554RFJUMHFsj9e0kkJ8jGz1KkAj52', '2026-09-22', 1, 1, '2026-09-22 16:59:30'),
(134, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHLjGqWhDtuRh0D+p8wXZWXWVVj0/f+kzDoJN7cPEq/R5GMVgsWKYKclM3RjWQ04Am', '2026-09-22', 1, 1, '2026-09-22 19:04:13'),
(137, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHkVV8P5boKJfKaIUeHm7fpeIX1y9U8of2lpf/salX2acs6qnq5FU07B+7Q9WMcQ/A', '2026-09-22', 1, 1, '2026-09-22 19:05:51'),
(138, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHN6XP1HM0Y08jw8ilXc6OOoztIkkw9HLSVNNCL7pZYGablalhT/1fBMKBQD7HhpBz', '2026-09-22', 1, 1, '2026-09-22 19:07:00'),
(148, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHqbB3TR6sEsqNGC89Qmy8LIquvuXyGNdv/qhXothOvjAChUoFSFpYeq+SXVV485w0', '2026-09-22', 1, 1, '2026-09-22 19:59:47'),
(153, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHabgDrWKYKb5aot0yxgVYxvOHcREDaPxp2gs1T3R4QeEv4cXkI0mYCVtbj+1M868E', '2026-09-22', 1, 1, '2026-09-22 21:02:44'),
(154, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCHabgDrWKYKb5aot0yxgVYxvOHcREDaPxp2gs1T3R4QeEv4cXkI0mYCVtbj+1M868E', '2026-09-22', 1, 1, '2026-09-22 21:02:44'),
(159, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCH+Tg2xWrGgvK9aJCtC/iEm47ee3t/FlV6RffehP7+Qr1AwXPg1PG0sjL56CJOozKO', '2026-09-22', 1, 1, '2026-09-22 21:54:05'),
(164, 'dohHrvq1GrOfI9ixtQb3qW2vHceJsZYTYjjBBmBJnkbuglZAsDCT+Ro4w/hc7BCH9Mrza+lNFApYGfbF2e7dGOQMfMCfkTflqqyXaX7b44EWgD9aLJzGRxSSRT3GLTOt', '2026-09-22', 1, 1, '2026-09-22 21:56:30'),
(171, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76v5TKEqmx4JGLdltCgqBlSoXAKFkqwDMq1s6IVdt+3Tr5BOBdcGGvoYDAdmNLM2sF', '2026-09-23', 1, 1, '2026-09-23 15:03:42'),
(174, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76OYbTygC28RuEt7gRqp1n07MW/lXuefTUL/gFnu/Zcsp53Wavn6G/N2oe26zgLUGr', '2026-09-23', 1, 1, '2026-09-23 15:07:24'),
(180, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76AwQO2KDuKQp8PDeY87n+xJypgTYF4eJmvo2aPRB8qcQ7wcl2mHVYNzAE734eI+ub', '2026-09-23', 1, 1, '2026-09-23 15:24:48'),
(181, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76AwQO2KDuKQp8PDeY87n+xJypgTYF4eJmvo2aPRB8qcQ7wcl2mHVYNzAE734eI+ub', '2026-09-23', 1, 1, '2026-09-23 15:24:48'),
(183, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+Tfknovgb8naKJAcZbNY3mqKwK6bStTV/BvNah4/QnorL1aF86yqCWZ279tbRGbpFb7oqB8', '2026-09-23', 6237, 1, '2026-09-23 16:04:37'),
(189, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+Tfknovog75RjNOKQehPBntHh2KMOIJkIqPb4NMnTcg/IdTHNz12ZBA07nNKjN+fR9mBP9/', '2026-09-23', 6237, 1, '2026-09-23 18:22:18'),
(195, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+TfknovlfgSNYJUYb+iSYiSjfEfNjFsQgHthPXvhDl2EKSyzmL3eBAif7tmhI2WEaUli9Gu', '2026-09-23', 6237, 1, '2026-09-23 18:40:19'),
(199, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+TfknovBEW+P2bMyGkQYU9sJNTGg17w1oZrFqyFApQyMvLtVocPrHBOGX6bpmAGQ/Nd6PFq', '2026-09-23', 6237, 1, '2026-09-23 18:45:49'),
(200, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+TfknovBEW+P2bMyGkQYU9sJNTGg17w1oZrFqyFApQyMvLtVocPrHBOGX6bpmAGQ/Nd6PFq', '2026-09-23', 6237, 1, '2026-09-23 18:45:49'),
(204, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76z/J0SKbVEn6DpQ+ILeOL3TjP6fTxUXjDevHHzfZuULoKLa39Go7zGvI5R/j7Xtjk', '2026-09-23', 1, 1, '2026-09-23 18:47:30'),
(205, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76z/J0SKbVEn6DpQ+ILeOL3TjP6fTxUXjDevHHzfZuULoKLa39Go7zGvI5R/j7Xtjk', '2026-09-23', 1, 1, '2026-09-23 18:47:30'),
(206, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+Tfknovxxic7Y5Z56WZ2b0zuCPZ/C6zUS7hDsqA7VNjOi7YqaJVoAUAcEKJocmyOeMXKmQL', '2026-09-23', 6237, 1, '2026-09-23 18:51:02'),
(207, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhpogOvYhRuh54Rr+Tfknovxxic7Y5Z56WZ2b0zuCPZ/C6zUS7hDsqA7VNjOi7YqaJVoAUAcEKJocmyOeMXKmQL', '2026-09-23', 6237, 1, '2026-09-23 18:51:02'),
(213, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76Scc1/5XVs+eSvsEjZucD5eZNQGxYtO06Wkqj84zsAamO6YoyMmZ8h1fIS/6G5t4j', '2026-09-23', 1, 1, '2026-09-23 19:19:09'),
(214, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76UYEGt397dQNueq/PAk5YvURTWfz9bIcLTJ50XGs3HT+c/l41vLFiNMRqyRi0Lq/R', '2026-09-23', 1, 1, '2026-09-23 19:19:10'),
(219, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l769Jj0bTuZFrfHupEmBASrh26CDOyu2JX6MM+6d/z+v//jIE9u3gytnw1H0z7aal5c', '2026-09-23', 1, 1, '2026-09-23 19:44:51'),
(226, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76SuKb2o0BdGVFL1rgI6aA0b2J9kOV/B8TmUvFwexGv7U5aNngPcnMur9QaZV/HoU7', '2026-09-23', 1, 1, '2026-09-23 20:35:27'),
(232, 'dohHrvq1GrOfI9ixtQb3qYLdVHKEEETXR37XacRMgdhWhG1/MCfhLmnE1BGs9l76uRqTu3ca5WIISNuMx289Aw5e25pQWzdSU7+IaeQDVcPv/dRQDE3tFk2G5BY1JCeS', '2026-09-23', 1, 1, '2026-09-23 22:01:42'),
(235, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcx6B2Ey5zXKt9mHEPrh142NlHGTxJO3XaVWKf/YYjHE+XtTifxRJgsuEzrpJlsUPw', '2026-09-24', 1, 1, '2026-09-24 13:12:19'),
(236, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAc+HcgpgHRXFRcaFOTIP1VpAcNrUwyAPSJMQqPH71Ck5E33x2A5IlF1mUzxHLL4apQ', '2026-09-24', 1, 1, '2026-09-24 13:12:28'),
(244, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcGlsJ2JXnBiFm7/143ECAkLGMLv9f8AZBw6T1Ym1wwK+WyufP66SofjA2mjc2oBtt', '2026-09-24', 1, 1, '2026-09-24 15:10:15'),
(248, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcGuqU/bd5nHi4VZ8xEb3RfmE3qrYHSQfakH3h2R8Ne6sG+BtLIAaLe8FVrH0iZb3L', '2026-09-24', 1, 1, '2026-09-24 15:11:11'),
(255, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcMoY9NdieMR56pT9yGQJbfXHWY9Y9yWDz2STpmP4SjukF6JV8ODT8gJSJaar8K9Wr', '2026-09-24', 1, 1, '2026-09-24 15:53:17'),
(271, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAccDk8p3vwjys/0S5h6ERtbNS9ALEBQEWuveze0CZ6uXVVg5onB5Ps8Q5VTkLpeN5x', '2026-09-24', 1, 1, '2026-09-24 16:28:29'),
(287, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcv8NYnZXjd0a5ZqObcB7pIJfiHHV0E4/MPb/28QfD2kHQ532ZAcaBcbaRx/nsZR4v', '2026-09-24', 1, 1, '2026-09-24 16:36:21'),
(289, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcoONuemhbyTctDy9OICPOKZ1vMTtrUYG7GEMmr2QQWNjIzCtMwNGwD31w49/9dZSv', '2026-09-24', 1, 1, '2026-09-24 16:38:15'),
(291, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAccDk8p3vwjys/0S5h6ERtbNS9ALEBQEWuveze0CZ6uXVVg5onB5Ps8Q5VTkLpeN5x', '2026-09-24', 1, 1, '2026-09-24 16:40:29'),
(296, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAc4tCEeyag/EtvDE7tTjzld4foFbN0Qj7sgx+YYGwjazG8WEGmHapebQnoDFQMFQbA', '2026-09-24', 1, 1, '2026-09-24 16:42:42'),
(301, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcu5UU3F8ma3XD/z8Gt6ddB9NwDINa5EPi048qcKWnXYskaxB2VpyE9FT81LeMeHlR', '2026-09-24', 1, 1, '2026-09-24 18:38:56'),
(302, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcu5UU3F8ma3XD/z8Gt6ddB9NwDINa5EPi048qcKWnXYskaxB2VpyE9FT81LeMeHlR', '2026-09-24', 1, 1, '2026-09-24 18:38:56'),
(304, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcEzetGb7m3oz1oVpwtRRjmTIFnvHAThL2z1mFCKUiOHdnoxjm8xfpAnVIpXXSA2w1', '2026-09-24', 1, 1, '2026-09-24 18:39:24'),
(306, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcmjRn+lAf0egcFOKNaGYJ9jbZX7uU/DFovFN72wt9Gq9vyH9YEiz2bKtAeLi67gtw', '2026-09-24', 1, 1, '2026-09-24 18:40:44'),
(307, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcmjRn+lAf0egcFOKNaGYJ9jbZX7uU/DFovFN72wt9Gq9vyH9YEiz2bKtAeLi67gtw', '2026-09-24', 1, 1, '2026-09-24 18:40:44'),
(311, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcq8TkEl9vC2kzerXIs6qT2duOCG5yqFbs8+pP65fnMdHdQFpuRP5kJvSLkGtnpg66', '2026-09-24', 1, 1, '2026-09-24 18:53:46'),
(313, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcYco/TdERLNnrGPpS6RJYDxE8WUHnelJqM5ygzSIiYE1qLxW4WTlBPuNQSftyr6ra', '2026-09-24', 1, 1, '2026-09-24 18:54:34'),
(315, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAc9LrCOUB6Z84k1WPyn95cEVqaBAU4bcjJj3Avi1YFj4/uBlaToEt1ud7G+JTH0/5u', '2026-09-24', 1, 1, '2026-09-24 19:16:19'),
(318, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAcyZtgc1BSnLN31ND4+11LfACrl3rHk5O9HNoEUuFWn8r8hvqpDpNAkJZVkHsl1b5i', '2026-09-24', 1, 1, '2026-09-24 19:16:51'),
(320, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAckODhFWvhTwX7x7IBOwc5gCw225D/W+4LBCMevfXG/tuehe+3mD9fgVnF4mDQ79z1', '2026-09-24', 1, 1, '2026-09-24 19:17:18'),
(321, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTMnbQb0EcYG+rnafVJ8tUAckODhFWvhTwX7x7IBOwc5gCw225D/W+4LBCMevfXG/tuehe+3mD9fgVnF4mDQ79z1', '2026-09-24', 1, 1, '2026-09-24 19:17:18'),
(323, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTOlLXPGU7sHnpHkIF1tNilXdUCZYQUlrYM6dGL4iOK5eGS+F1rKwA7dNoT99EVT8wLDsawHZ8tGd75MiloL1pt6', '2026-09-24', 6237, 1, '2026-09-24 19:28:17'),
(325, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTOlLXPGU7sHnpHkIF1tNilXUTnwqmkoG1sAh4maWlsdhESZhg/6lc1Gtdy5Lb6ty+Cx6OM28Pg6JIgMUCJ0CTUZ', '2026-09-24', 6237, 1, '2026-09-24 19:28:22'),
(327, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTOlLXPGU7sHnpHkIF1tNilXGnDTGtkDnHYcrXArJ3b1Grs8E1HmwtDus18y3P5ZhnbaxncMkWeP+jK1POXp569/', '2026-09-24', 6237, 2, '2026-09-24 19:40:04'),
(328, 'dohHrvq1GrOfI9ixtQb3qZYG6PzttWrlfRpqbDwRNTOlLXPGU7sHnpHkIF1tNilXlh+S4Gavttx9MKDGhYKIzLxyLLMj4/4qdCVmq7+X0ePC7YobBrZNyuypnM8J0wrs', '2026-09-24', 6237, 1, '2026-09-24 19:40:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_usuario`
--

CREATE TABLE `admin_usuario` (
  `persona_id` int(11) NOT NULL,
  `rol` int(11) NOT NULL,
  `_usuario` varchar(50) DEFAULT '',
  `_fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_usuario`
--

INSERT INTO `admin_usuario` (`persona_id`, `rol`, `_usuario`, `_fecha`) VALUES
(1, 4, '10000001', '2026-09-16 15:14:39'),
(2, 3, '10000001', '2026-09-21 20:33:59');

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
-- Estructura de tabla para la tabla `datos_laborales`
--

CREATE TABLE `datos_laborales` (
  `id` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `cargo` varchar(100) DEFAULT '',
  `nombre_completo` varchar(200) DEFAULT '',
  `fecha_ingreso` date DEFAULT NULL,
  `salario` decimal(12,2) DEFAULT 0.00,
  `auxilio_alimentacion` decimal(12,2) DEFAULT 0.00,
  `tipo_contrato` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_laborales`
--

INSERT INTO `datos_laborales` (`id`, `persona_id`, `cargo`, `nombre_completo`, `fecha_ingreso`, `salario`, `auxilio_alimentacion`, `tipo_contrato`) VALUES
(1, 2, 'Acudiente / Padre', 'Nuevo  Usuario', NULL, 0.00, 0.00, '');

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
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id`, `nombre`, `slug`) VALUES
(1, 'Activo', 'activo'),
(2, 'Inactivo', 'inactivo'),
(3, 'Pendiente', 'pendiente');

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
(1, 'CC', '10000001', 'ADMIN', 'Super', '', 'Admin', 'Voley+', NULL, 'M', '3001234567', 'admin@voleyplus.com', NULL, 'img/user.png', '2026-09-16 15:14:39', '2026-09-16 15:14:39'),
(2, 'CC', '11223344', 'nuevousuario', 'Nuevo', '', 'Usuario', '', NULL, 'M', '3005555555', 'nuevo@test.com', NULL, 'img/user.png', '2026-09-21 20:33:59', '2026-09-21 20:33:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantas`
--

CREATE TABLE `plantas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `visible` char(1) DEFAULT 'S'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plantas`
--

INSERT INTO `plantas` (`id`, `nombre`, `slug`, `visible`) VALUES
(1, 'Planta Principal', 'planta-principal', 'S'),
(2, 'Planta Norte', 'planta-norte', 'S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sexo`
--

CREATE TABLE `sexo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sexo`
--

INSERT INTO `sexo` (`id`, `nombre`, `slug`) VALUES
(1, 'Masculino', 'masculino'),
(2, 'Femenino', 'femenino'),
(3, 'Otro', 'otro');

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
-- Estructura de tabla para la tabla `tipo_contrato`
--

CREATE TABLE `tipo_contrato` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `visible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_contrato`
--

INSERT INTO `tipo_contrato` (`id`, `nombre`, `visible`) VALUES
(1, 'Tiempo Completo', 1),
(2, 'Medio Tiempo', 1),
(3, 'Contrato Fijo', 1),
(4, 'Practicas', 1);

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
(1, 1, 4, 'ADMIN', '$2y$10$EaspXXDFKIuN3Noj6N.fKOd5WaAxkY5pTNHp1N/siGSMortH1MYky', 1, '2026-09-24 19:17:16', '2026-09-16 15:14:39', '2026-09-24 19:17:16'),
(2, 2, 3, 'nuevousuario', '$2y$10$3w29UGCL4a3KB1oDp05Vie6zLyD/YWBO8CV.o904z6zLJeuckwFWi', 1, NULL, '2026-09-21 20:33:59', '2026-09-21 20:33:59');

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
-- Indices de la tabla `admin_front`
--
ALTER TABLE `admin_front`
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
  ADD PRIMARY KEY (`persona_id`),
  ADD KEY `rol` (`rol`);

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
-- Indices de la tabla `datos_laborales`
--
ALTER TABLE `datos_laborales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persona_id` (`persona_id`);

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
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

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
-- Indices de la tabla `plantas`
--
ALTER TABLE `plantas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `sexo`
--
ALTER TABLE `sexo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `tipo_autorizacion`
--
ALTER TABLE `tipo_autorizacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `tipo_contrato`
--
ALTER TABLE `tipo_contrato`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `admin_bitacoras_sistema`
--
ALTER TABLE `admin_bitacoras_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `admin_favoritos`
--
ALTER TABLE `admin_favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `admin_front`
--
ALTER TABLE `admin_front`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `admin_log`
--
ALTER TABLE `admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=609;

--
-- AUTO_INCREMENT de la tabla `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `admin_permiso_accion`
--
ALTER TABLE `admin_permiso_accion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT de la tabla `admin_permiso_menu`
--
ALTER TABLE `admin_permiso_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT de la tabla `admin_rol`
--
ALTER TABLE `admin_rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `admin_sesion`
--
ALTER TABLE `admin_sesion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `admin_sesion_denegada`
--
ALTER TABLE `admin_sesion_denegada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `admin_tipo_accion`
--
ALTER TABLE `admin_tipo_accion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `admin_token`
--
ALTER TABLE `admin_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;

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
-- AUTO_INCREMENT de la tabla `datos_laborales`
--
ALTER TABLE `datos_laborales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `plantas`
--
ALTER TABLE `plantas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sexo`
--
ALTER TABLE `sexo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_autorizacion`
--
ALTER TABLE `tipo_autorizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_contrato`
--
ALTER TABLE `tipo_contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Filtros para la tabla `datos_laborales`
--
ALTER TABLE `datos_laborales`
  ADD CONSTRAINT `datos_laborales_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `persona` (`id`) ON DELETE CASCADE;

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
