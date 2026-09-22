-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-09-2026 a las 18:43:03
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
(0, 4, '10000001', '2026-09-16 15:14:39'),
(0, 3, '10000001', '2026-09-21 20:33:59');

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

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_usuario`
--
ALTER TABLE `admin_usuario`
  ADD KEY `rol` (`rol`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion` (`identificacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
