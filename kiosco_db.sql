-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-01-2026 a las 19:32:43
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
-- Base de datos: `kiosco_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Bebidas', 'Refrescos, jugos, agua'),
(2, 'Snacks', 'Chips, golosinas, chocolates'),
(3, 'Lácteos', 'Leche, yogur, quesos'),
(4, 'Panadería', 'Pan, facturas, medialunas'),
(5, 'nueva CAT', 'categoria de PRUEBA'),
(7, 'salchipapa', 'xD'),
(8, 'Corazon chico', 'asdjsajd');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `saldo_cuenta` decimal(10,2) DEFAULT 0.00 COMMENT 'Positivo=a favor, Negativo=deuda',
  `limite_credito` decimal(10,2) DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `direccion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `email`, `telefono`, `saldo_cuenta`, `limite_credito`, `notas`, `activo`, `created_at`, `updated_at`, `direccion`) VALUES
(1, 'pepe toño', 'asddd@gmail.com', '12312321', 0.00, 0.00, NULL, 1, '2025-12-15 21:57:05', '2025-12-17 00:16:29', 'calle falsa 123'),
(2, 'jorge', 'jorge@gmailc.om', '1111', 50.00, 0.00, NULL, 1, '2025-12-16 00:38:47', '2025-12-16 02:25:32', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_pagos`
--

CREATE TABLE `cliente_pagos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente_pagos`
--

INSERT INTO `cliente_pagos` (`id`, `cliente_id`, `monto`, `fecha`, `usuario_id`, `turno_id`, `descripcion`) VALUES
(1, 2, 50.00, '2025-12-15 22:12:52', 6, 34, 'Test Payment CLI 22:12:52'),
(2, 2, 100.00, '2025-12-15 22:39:54', 6, 35, ''),
(3, 2, 100.00, '2025-12-15 22:45:04', 6, 35, ''),
(4, 2, 100.00, '2025-12-15 22:45:10', 6, 35, ''),
(5, 2, 100.00, '2025-12-15 22:48:40', 6, 35, ''),
(6, 2, 1.00, '2025-12-15 22:49:12', 6, 35, ''),
(7, 2, 30000.00, '2025-12-15 23:07:37', 6, 35, ''),
(8, 2, 2098.00, '2025-12-15 23:13:15', 6, 35, ''),
(9, 2, 350.00, '2025-12-15 23:25:32', 6, 35, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `updated_at`) VALUES
(1, 'negocio_nombre', 'Mi Kiosco', 'Nombre del negocio', '2025-12-15 21:27:25'),
(2, 'negocio_direccion', 'Calle Principal 123', 'Dirección del negocio', '2025-12-15 21:27:25'),
(3, 'negocio_telefono', '123-456-7890', 'Teléfono del negocio', '2025-12-15 21:27:25'),
(4, 'negocio_email', 'contacto@mikiosco.com', 'Email del negocio', '2025-12-15 21:27:25'),
(5, 'ticket_mensaje', '¡Gracias por su compra!', 'Mensaje en el ticket', '2025-12-15 21:27:25'),
(6, 'ticket_auto_print', '1', 'Imprimir ticket automáticamente (1=sí, 0=no)', '2025-12-15 21:27:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `success` tinyint(1) DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `user_agent`, `success`, `attempted_at`) VALUES
(1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:01:12'),
(2, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:04:57'),
(3, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:05:02'),
(4, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:05:41'),
(5, 'admin2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:06:24'),
(6, 'admin2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:06:39'),
(7, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 00:06:57'),
(8, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 00:30:10'),
(9, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 00:49:37'),
(10, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 00:49:42'),
(11, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:03:09'),
(12, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:05:13'),
(13, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:06:13'),
(14, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:07:50'),
(15, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:12:25'),
(16, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:15:18'),
(17, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:18:18'),
(18, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:23:40'),
(19, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:25:31'),
(20, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:30:50'),
(21, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:32:13'),
(22, 'caja', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:32:58'),
(23, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:33:49'),
(24, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:37:18'),
(25, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:38:24'),
(26, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:49:22'),
(27, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 01:59:04'),
(28, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:04:21'),
(29, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:05:44'),
(30, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:07:24'),
(31, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 02:12:12'),
(32, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:12:17'),
(33, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:18:15'),
(34, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:19:46'),
(35, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:20:00'),
(36, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:24:58'),
(37, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:27:38'),
(38, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:28:52'),
(39, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-12 02:30:44'),
(40, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:30:49'),
(41, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:32:13'),
(42, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:32:39'),
(43, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:35:00'),
(44, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 02:36:14'),
(45, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 20:50:48'),
(46, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 20:53:50'),
(47, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 20:54:32'),
(48, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-12 23:56:43'),
(49, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 00:09:04'),
(50, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 00:12:07'),
(51, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 00:17:12'),
(52, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 01:11:19'),
(53, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 01:13:47'),
(54, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 02:57:34'),
(55, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 03:18:12'),
(56, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 03:22:27'),
(57, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:17:37'),
(58, 'naza', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:27:39'),
(59, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:45:25'),
(60, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-13 22:56:58'),
(61, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:57:02'),
(62, 'naza', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:57:26'),
(63, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:57:43'),
(64, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-13 22:57:59'),
(65, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:58:03'),
(66, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:58:11'),
(67, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:58:20'),
(68, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-13 22:58:36'),
(69, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:03:46'),
(70, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:16:41'),
(71, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:18:48'),
(72, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:20:28'),
(73, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-14 03:20:49'),
(74, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:20:55'),
(75, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:21:30'),
(76, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:21:57'),
(77, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:23:35'),
(78, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:25:37'),
(79, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:34:52'),
(80, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:35:40'),
(81, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:41:50'),
(82, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:48:04'),
(83, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:48:20'),
(84, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:51:21'),
(85, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:52:58'),
(86, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 03:59:30'),
(87, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 04:00:30'),
(88, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 04:00:58'),
(89, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 04:07:25'),
(90, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:00:48'),
(91, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:01:47'),
(92, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:02:01'),
(93, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:08:42'),
(94, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:08:55'),
(95, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:09:19'),
(96, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:17:21'),
(97, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:17:57'),
(98, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:44:52'),
(99, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-14 22:45:01'),
(100, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-14 22:45:06'),
(101, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 19:33:13'),
(102, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 19:39:45'),
(103, 'juanjo', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 19:44:51'),
(104, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 19:45:23'),
(105, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-15 19:48:11'),
(106, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 19:48:15'),
(107, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 21:50:05'),
(108, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 0, '2025-12-15 21:50:27'),
(109, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 21:50:36'),
(110, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 22:04:45'),
(111, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 22:13:08'),
(112, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 0, '2025-12-15 22:14:58'),
(113, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 1, '2025-12-15 22:15:05'),
(114, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 22:17:24'),
(115, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 22:18:15'),
(116, 'nuevouser', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 22:48:00'),
(117, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-15 23:08:15'),
(118, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-16 00:38:06'),
(119, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-16 01:31:18'),
(120, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 1, '2025-12-16 01:31:41'),
(121, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-16 01:44:48'),
(122, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-16 02:20:43'),
(123, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-16 23:56:07'),
(124, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-17 00:41:16'),
(125, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-17 00:45:38'),
(126, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-17 00:48:11'),
(127, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-17 00:51:52'),
(128, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-17 02:31:06'),
(129, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2025-12-17 18:34:52'),
(130, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2026-01-04 20:37:19'),
(131, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, '2026-01-05 17:30:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `requiere_referencia` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `nombre`, `requiere_referencia`, `activo`, `created_at`) VALUES
(1, 'Efectivo', 0, 1, '2025-12-15 21:27:25'),
(2, 'Tarjeta Débito', 1, 1, '2025-12-15 21:27:25'),
(3, 'Tarjeta Crédito', 1, 1, '2025-12-15 21:27:25'),
(4, 'Transferencia', 1, 1, '2025-12-15 21:27:25'),
(5, 'Cuenta Corriente', 0, 1, '2025-12-15 21:27:25'),
(6, 'Efectivo', 0, 1, '2025-12-15 21:39:33'),
(7, 'Tarjeta Débito', 1, 1, '2025-12-15 21:39:33'),
(8, 'Tarjeta Crédito', 1, 1, '2025-12-15 21:39:33'),
(9, 'Transferencia', 1, 1, '2025-12-15 21:39:33'),
(10, 'Cuenta Corriente', 0, 1, '2025-12-15 21:39:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `tipo` enum('ingreso','egreso','venta','inicial') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_caja`
--

INSERT INTO `movimientos_caja` (`id`, `turno_id`, `tipo`, `monto`, `descripcion`, `venta_id`, `fecha`, `usuario_id`, `created_at`) VALUES
(1, 1, 'inicial', 100000.00, 'Apertura de turno', NULL, '2025-12-11 22:20:14', 6, '2025-12-12 01:20:14'),
(2, 5, 'inicial', 10000.00, 'Apertura de turno', NULL, '0000-00-00 00:00:00', 9, '2025-12-12 02:23:07'),
(3, 5, 'venta', 600.00, 'Venta #10', 10, '0000-00-00 00:00:00', 9, '2025-12-12 02:23:44'),
(4, 6, 'inicial', 30000.00, 'Apertura de turno', NULL, '0000-00-00 00:00:00', 9, '2025-12-12 02:32:51'),
(5, 6, 'venta', 1110.00, 'Venta #11', 11, '0000-00-00 00:00:00', 9, '2025-12-12 02:33:24'),
(6, 6, 'ingreso', 10.00, 'proveedor', NULL, '0000-00-00 00:00:00', 9, '2025-12-12 02:34:11'),
(7, 6, 'egreso', 20.00, 'proveedores', NULL, '0000-00-00 00:00:00', 9, '2025-12-12 02:34:30'),
(12, 19, 'inicial', 11.00, 'Apertura de turno', NULL, '2025-12-12 21:07:12', 6, '2025-12-13 00:07:12'),
(13, 19, 'egreso', 10.00, 'porque si', NULL, '2025-12-12 21:08:43', 6, '2025-12-13 00:08:43'),
(14, 20, 'inicial', 1000.00, 'Apertura de turno', NULL, '2025-12-12 21:09:13', 9, '2025-12-13 00:09:13'),
(15, 20, 'venta', 1050.00, 'Venta #14', 14, '2025-12-12 21:11:45', 9, '2025-12-13 00:11:45'),
(16, 21, 'inicial', 100.00, 'Apertura de turno', NULL, '2025-12-12 21:14:14', 6, '2025-12-13 00:14:14'),
(17, 21, 'venta', 120.00, 'Venta #16', 16, '2025-12-12 21:14:28', 6, '2025-12-13 00:14:28'),
(18, 21, 'venta', 120.00, 'Venta #17', 17, '2025-12-12 21:14:46', 6, '2025-12-13 00:14:46'),
(19, 21, 'venta', 100.00, 'Venta #18', 18, '2025-12-12 21:14:55', 6, '2025-12-13 00:14:55'),
(20, 21, 'venta', 100.00, 'Venta #19', 19, '2025-12-12 21:16:15', 6, '2025-12-13 00:16:15'),
(21, 22, 'inicial', 5000.00, 'Apertura de turno', NULL, '2025-12-12 21:24:30', 6, '2025-12-13 00:24:30'),
(22, 23, 'inicial', 1000.00, 'Apertura de turno', NULL, '2025-12-12 22:09:57', 6, '2025-12-13 01:09:57'),
(23, 23, 'venta', 220.00, 'Venta #20', 20, '2025-12-12 22:10:09', 6, '2025-12-13 01:10:09'),
(24, 24, 'inicial', 40.00, 'Apertura de turno', NULL, '2025-12-12 22:26:08', 9, '2025-12-13 01:26:08'),
(25, 25, 'inicial', 100.00, 'Apertura de turno', NULL, '2025-12-13 00:18:48', 9, '2025-12-13 03:18:48'),
(26, 26, 'inicial', 100.00, 'Apertura de turno', NULL, '2025-12-13 19:21:45', 6, '2025-12-13 22:21:45'),
(27, 27, 'inicial', 1.00, 'Apertura de turno', NULL, '2025-12-13 19:34:11', 12, '2025-12-13 22:34:11'),
(28, 28, 'inicial', 100.00, 'Apertura de turno', NULL, '2025-12-14 19:17:27', 9, '2025-12-14 22:17:27'),
(29, 28, 'venta', 2000.00, 'Venta #21', 21, '2025-12-14 19:17:41', 9, '2025-12-14 22:17:41'),
(30, 29, 'inicial', 1.00, 'Apertura de turno', NULL, '2025-12-14 19:18:09', 6, '2025-12-14 22:18:09'),
(31, 29, 'venta', 120.00, 'Venta #22', 22, '2025-12-14 19:18:26', 6, '2025-12-14 22:18:26'),
(32, 30, 'inicial', 1.00, 'Apertura de turno', NULL, '2025-12-14 19:27:02', 6, '2025-12-14 22:27:02'),
(33, 31, 'inicial', 100.00, 'Apertura de turno', NULL, '2025-12-15 16:40:34', 9, '2025-12-15 19:40:34'),
(34, 32, 'inicial', 100.00, 'Apertura de turno', NULL, '2025-12-15 16:41:04', 9, '2025-12-15 19:41:04'),
(35, 32, 'venta', 120.00, 'Venta #23', 23, '2025-12-15 16:41:29', 9, '2025-12-15 19:41:29'),
(36, 33, 'inicial', 11111.00, 'Apertura de turno', NULL, '2025-12-15 16:52:39', 9, '2025-12-15 19:52:39'),
(37, 34, 'inicial', 1.00, 'Apertura de turno', NULL, '2025-12-15 18:57:17', 6, '2025-12-15 21:57:17'),
(38, 34, 'venta', 120.00, 'Venta #24', 24, '2025-12-15 19:30:28', 6, '2025-12-15 22:30:28'),
(39, 34, 'venta', 120.00, 'Venta #25', 25, '2025-12-15 19:30:41', 6, '2025-12-15 22:30:41'),
(40, 33, 'venta', 100.00, 'Venta #26', 26, '2025-12-15 20:08:00', 9, '2025-12-15 23:08:00'),
(41, 34, 'venta', 10001.00, 'Venta #27', 27, '2025-12-15 20:23:52', 6, '2025-12-15 23:23:52'),
(42, 34, 'venta', 0.00, 'Venta Cta. Cte. #30 (Total: $1,800.00)', 30, '2025-12-15 21:23:03', 6, '2025-12-16 00:23:03'),
(43, 34, 'venta', 0.00, 'Venta Cta. Cte. #31 (Total: $1,800.00)', 31, '2025-12-15 21:31:13', 6, '2025-12-16 00:31:13'),
(44, 34, 'venta', 0.00, 'Venta Cta. Cte. #32 (Total: $1,800.00)', 32, '2025-12-15 21:37:14', 6, '2025-12-16 00:37:14'),
(45, 34, 'venta', 0.00, 'Venta Cta. Cte. #33 (Total: $3,125.00)', 33, '2025-12-15 21:39:42', 6, '2025-12-16 00:39:42'),
(46, 34, 'venta', 440.00, 'Venta #34', 34, '2025-12-15 21:48:52', 6, '2025-12-16 00:48:52'),
(47, 34, 'venta', 0.00, 'Venta Cta. Cte. #35 (Total: $100.00)', 35, '2025-12-15 21:49:26', 6, '2025-12-16 00:49:26'),
(48, 34, 'ingreso', 100.00, 'c d', NULL, '2025-12-15 21:50:36', 6, '2025-12-16 00:50:36'),
(49, 34, 'venta', 0.00, 'Venta Cta. Cte. #36 (Total: $11,801.00)', 36, '2025-12-15 22:09:22', 6, '2025-12-16 01:09:22'),
(50, 34, 'venta', 0.00, 'Venta Cta. Cte. #37 (Total: $1,800.00)', 37, '2025-12-15 22:10:11', 6, '2025-12-16 01:10:11'),
(51, 34, 'venta', 0.00, 'Venta Cta. Cte. #38 (Total: $1,800.00)', 38, '2025-12-15 22:26:57', 6, '2025-12-16 01:26:57'),
(52, 34, 'venta', 0.00, 'Venta Cta. Cte. #39 (Total: $1,800.00)', 39, '2025-12-15 22:32:09', 6, '2025-12-16 01:32:09'),
(53, 35, 'inicial', 600.00, 'Apertura de turno', NULL, '2025-12-15 22:33:57', 6, '2025-12-16 01:33:57'),
(54, 35, 'venta', 0.00, 'Venta Cta. Cte. #40 (Total: $3,600.00)', 40, '2025-12-15 22:34:10', 6, '2025-12-16 01:34:10'),
(55, 35, 'venta', 0.00, 'Venta Cta. Cte. #41 (Total: $1,800.00)', 41, '2025-12-15 22:39:41', 6, '2025-12-16 01:39:41'),
(56, 35, 'ingreso', 100.00, 'Pago Cta. Cte. #2 - ', NULL, '2025-12-15 22:39:54', 6, '2025-12-16 01:39:54'),
(57, 35, 'ingreso', 100.00, 'Pago Cta. Cte. #2 - ', NULL, '2025-12-15 22:45:04', 6, '2025-12-16 01:45:04'),
(58, 35, 'ingreso', 100.00, 'Pago Cta. Cte. #2 - ', NULL, '2025-12-15 22:45:10', 6, '2025-12-16 01:45:10'),
(59, 35, 'ingreso', 100.00, 'Pago Cta. Cte. #2 jorge', NULL, '2025-12-15 22:48:40', 6, '2025-12-16 01:48:40'),
(60, 35, 'ingreso', 1.00, 'Pago Cta. Cte. #2 jorge', NULL, '2025-12-15 22:49:12', 6, '2025-12-16 01:49:12'),
(61, 35, 'venta', 900.00, 'Venta Cta. Cte. #42 (Total: $1,900.00) - Entrega: $900.00', 42, '2025-12-15 23:00:02', 6, '2025-12-16 02:00:02'),
(62, 35, 'ingreso', 30000.00, 'Pago Cta. Cte. #2 jorge', NULL, '2025-12-15 23:07:37', 6, '2025-12-16 02:07:37'),
(63, 35, 'venta', 1.00, 'Venta Cta. Cte. #43 (Total: $10,001.00) - Entrega: $1.00', 43, '2025-12-15 23:10:31', 6, '2025-12-16 02:10:31'),
(64, 35, 'venta', 1.00, 'Venta Cta. Cte. #44 (Total: $10,001.00) - Entrega: $1.00', 44, '2025-12-15 23:11:40', 6, '2025-12-16 02:11:40'),
(65, 35, 'venta', 1.00, 'Venta Cta. Cte. #45 (Total: $10,001.00) - Entrega: $1.00', 45, '2025-12-15 23:12:51', 6, '2025-12-16 02:12:51'),
(66, 35, 'ingreso', 2098.00, 'Pago Cta. Cte. #2 jorge', NULL, '2025-12-15 23:13:15', 6, '2025-12-16 02:13:15'),
(67, 35, 'venta', 0.00, 'Venta Cta. Cte. #46 (Total: $300.00)', 46, '2025-12-15 23:23:49', 6, '2025-12-16 02:23:49'),
(68, 35, 'ingreso', 350.00, 'Pago Cta. Cte. #2 jorge', NULL, '2025-12-15 23:25:32', 6, '2025-12-16 02:25:32'),
(69, 35, 'venta', 750.00, 'Venta #47', 47, '2025-12-15 23:37:00', 6, '2025-12-16 02:37:00'),
(70, 35, 'venta', 150.00, 'Venta #48', 48, '2025-12-16 20:58:41', 6, '2025-12-16 23:58:41'),
(71, 35, 'venta', 450.00, 'Venta #49', 49, '2025-12-16 21:13:21', 6, '2025-12-17 00:13:21'),
(72, 35, 'venta', 600.00, 'Venta #50', 50, '2025-12-16 21:16:49', 6, '2025-12-17 00:16:49'),
(73, 35, 'venta', 600.00, 'Venta #51', 51, '2025-12-16 21:18:17', 6, '2025-12-17 00:18:17'),
(74, 35, 'venta', 300.00, 'Venta #52', 52, '2025-12-16 21:20:27', 6, '2025-12-17 00:20:27'),
(75, 35, 'venta', 120.00, 'Venta #53', 53, '2025-12-16 21:52:05', 6, '2025-12-17 00:52:05'),
(76, 35, 'venta', 150.00, 'Venta #54', 54, '2025-12-16 23:31:23', 6, '2025-12-17 02:31:23'),
(77, 35, 'venta', 10000.00, 'Venta #56', 56, '2025-12-17 15:44:54', 6, '2025-12-17 18:44:54'),
(78, 35, 'venta', 150.00, 'Venta #57', 57, '2025-12-17 15:51:12', 6, '2025-12-17 18:51:12'),
(79, 35, 'venta', 0.00, 'Transferencia ($300.00) - Ref: juan perez', 58, '2026-01-04 18:09:26', 6, '2026-01-04 21:09:26'),
(80, 35, 'venta', 0.00, 'Transferencia ($300.00) - Ref: ppepe (Tel: 394729842)', 59, '2026-01-05 14:33:51', 6, '2026-01-05 17:33:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `codigo_barra` varchar(50) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `codigo_barra`, `categoria_id`, `imagen`, `created_at`, `deleted_at`) VALUES
(1, 'Coca Cola 500ml', 'Refresco de cola', 150.00, 2, '123456789012', 1, NULL, '2025-09-25 18:19:08', NULL),
(2, 'Papas Lays', 'Chips de papa', 100.00, 13, '111111', 2, NULL, '2025-09-25 18:19:08', NULL),
(3, 'Leche La Serenísima', 'Leche entera 1L', 120.00, 19, '123123', 3, NULL, '2025-09-25 18:19:08', NULL),
(4, 'Pan Lactal', 'Pan blanco 500g', 80.00, 10, '7613035068391', 4, NULL, '2025-09-25 18:19:08', NULL),
(5, 'Coca Cola Ligth 500ml', 'Refresco de cola ligth :D', 300.00, 3, NULL, 1, NULL, '2025-09-26 19:10:51', '2025-12-14 04:10:48'),
(6, 'PRODUCTO', 'UN PRODUCTO', 2000.00, 11, '321321', 5, NULL, '2025-09-27 00:40:23', NULL),
(7, 'vaso de agua', 'un vaso de agua', 1205.00, 14, NULL, 1, NULL, '2025-12-12 01:48:27', NULL),
(8, 'nuevo productoes', 'una cosa raradsd', 10001.00, 296, '123123123', 5, NULL, '2025-12-14 23:30:37', '2025-12-14 23:31:26'),
(9, 'velas', 'son velas man', 10000.00, 30, '4732649832', 8, NULL, '2025-12-15 19:49:33', NULL),
(10, 'coca cola nueva', '', 5000.00, 46, '5449000000996', 1, NULL, '2025-12-17 18:42:36', NULL),
(11, 'off', '', 4000.00, 123, '7798047032537', 5, NULL, '2025-12-17 18:58:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('descuento_porcentaje','descuento_fijo','nxm','precio_especial','combo') NOT NULL,
  `valor` decimal(10,2) NOT NULL COMMENT 'Porcentaje, monto fijo, o precio especial',
  `valor_extra` varchar(10) DEFAULT NULL COMMENT 'Para NxM: ej 2x1, 3x2',
  `categoria_id` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `dias_semana` varchar(20) DEFAULT NULL COMMENT 'JSON array de días: [1,2,3,4,5,6,7]',
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`id`, `nombre`, `descripcion`, `tipo`, `valor`, `valor_extra`, `categoria_id`, `fecha_inicio`, `fecha_fin`, `dias_semana`, `hora_inicio`, `hora_fin`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'promo salchipapa', NULL, 'descuento_porcentaje', 10.00, '', NULL, '2025-12-15', '2025-12-16', NULL, NULL, NULL, 0, '2025-12-15 21:53:06', '2025-12-16 02:12:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocion_productos`
--

CREATE TABLE `promocion_productos` (
  `id` int(11) NOT NULL,
  `promocion_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promocion_productos`
--

INSERT INTO `promocion_productos` (`id`, `promocion_id`, `producto_id`) VALUES
(1, 1, 1),
(2, 1, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idProveedor` int(11) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `nombre_fantasia` varchar(255) DEFAULT NULL,
  `cuit` char(11) NOT NULL,
  `condicion_iva` enum('Responsable Inscripto','Monotributista','Exento','No Responsable','Consumidor Final') NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `localidad` varchar(150) DEFAULT NULL,
  `provincia` varchar(150) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `sitio_web` varchar(150) DEFAULT NULL,
  `contacto_nombre` varchar(150) DEFAULT NULL,
  `contacto_telefono` varchar(50) DEFAULT NULL,
  `banco` varchar(150) DEFAULT NULL,
  `cbu` char(22) DEFAULT NULL,
  `alias_cbu` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_alta` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`idProveedor`, `razon_social`, `nombre_fantasia`, `cuit`, `condicion_iva`, `direccion`, `localidad`, `provincia`, `codigo_postal`, `telefono`, `email`, `sitio_web`, `contacto_nombre`, `contacto_telefono`, `banco`, `cbu`, `alias_cbu`, `observaciones`, `estado`, `fecha_alta`) VALUES
(1, 'Distribuidora Sur S.A.S', 'Sur Distribucion', '30712345678', 'Responsable Inscripto', 'Av. San Martín 2451', 'Rosario', 'Santa Fe', '2000', '0341-4567891', 'contacto@surdist.com.ar', 'www.surdist.com.ar', 'María González', '0341-155678901', 'Banco Nación', '0110599520000001234567', 'SUR.DIST.RO', 'Proveedor habitual de insumos', 'Inactivo', '2026-01-05'),
(2, 'Servicios Técnicos López', NULL, '20345678901', 'Monotributista', 'Mitre 850', 'Santa Fe', 'Santa Fe', '3000', '0342-4234567', 'tecnico.lopez@gmail.com', NULL, 'Juan López', '0342-154123456', 'Banco Macro', '2850590940090412345678', 'LOPEZ.TECNICO', 'Mantenimiento y reparaciones', 'Activo', '2026-01-05'),
(3, 'Papelera del Litoral SRL', 'Litoral Papel', '30765432109', 'Responsable Inscripto', 'Ruta 11 Km 482', 'Recreo', 'Santa Fe', '3018', '0342-4901122', 'ventas@litoralpapel.com.ar', 'www.litoralpapel.com.ar', 'Carlos Medina', '0342-156789012', 'Banco Santander', '0720590920000009876543', 'LITORAL.PAPEL', 'Entrega semanal', 'Activo', '2026-01-05'),
(4, 'Estudio Contable Fernández', NULL, '27234567890', 'Exento', '9 de Julio 1123', 'Rafaela', 'Santa Fe', '2300', '03492-432100', 'estudiofernandez@gmail.com', NULL, 'Laura Fernández', '03492-154567890', 'Banco Galicia', '0070590920000004567890', 'ESTUDIO.FERNANDEZ', 'Honorarios mensuales', 'Activo', '2026-01-05'),
(5, 'Logística Norte S.A.', 'Norte Logística', '30987654321', 'Responsable Inscripto', 'Av. Circunvalación 5400', 'Córdoba', 'Córdoba', '5000', '0351-4987654', 'info@nortelogistica.com.ar', 'www.nortelogistica.com.ar', 'Pablo Ruiz', '0351-156543210', 'Banco BBVA', '0170590920000001122334', 'NORTE.LOGISTICA', 'Transporte de mercadería', 'Activo', '2026-01-05'),
(6, 'Rtoo', 'tu nno mete cabra', '2034403', 'Responsable Inscripto', 'asdasd', NULL, NULL, NULL, '3121286800', 'test@example.us', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', '2026-01-05'),
(7, 'Juanjo S.A', 'Fantasia', '202323992', 'Responsable Inscripto', 'nashe', NULL, NULL, NULL, '3408-402912', 'juanjo@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Activo', '2026-01-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel` int(11) NOT NULL COMMENT '1=Admin, 2=Kiosquero, 3=Cajero',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `nivel`, `created_at`) VALUES
(1, 'Admin', 'Acceso total al sistema', 1, '2025-12-12 00:05:13'),
(2, 'Kiosquero', 'Solo puede realizar ventas', 2, '2025-12-12 00:05:13'),
(3, 'Cajero', 'Solo puede gestionar caja', 3, '2025-12-12 00:05:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos_caja`
--

CREATE TABLE `turnos_caja` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `usuario_nombre` varchar(100) DEFAULT 'Admin',
  `usuario_id` int(11) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `monto_final` decimal(10,2) DEFAULT NULL,
  `monto_esperado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('abierto','cerrado') DEFAULT 'abierto',
  `notas_apertura` text DEFAULT NULL,
  `notas_cierre` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `turnos_caja`
--

INSERT INTO `turnos_caja` (`id`, `user_id`, `usuario_nombre`, `usuario_id`, `fecha_apertura`, `fecha_cierre`, `monto_inicial`, `monto_final`, `monto_esperado`, `diferencia`, `estado`, `notas_apertura`, `notas_cierre`, `created_at`) VALUES
(1, 1, 'Admin', 6, '2025-12-11 22:20:14', NULL, 100000.00, NULL, NULL, NULL, 'abierto', 'nuevo turno', NULL, '2025-12-12 01:20:14'),
(5, 9, 'juanjo villafañe', 9, '2025-12-11 23:23:07', '2025-12-11 23:31:43', 10000.00, 9000.00, 10600.00, -1600.00, 'cerrado', 'sadas', 'nuevo cierre', '2025-12-12 02:23:07'),
(6, 9, 'juanjo villafañe', 9, '2025-12-11 23:32:51', '2025-12-11 23:34:51', 30000.00, 31000.00, 31100.00, -100.00, 'cerrado', 'ddd', 'cierre hoy', '2025-12-12 02:32:51'),
(19, 6, 'admin', 6, '2025-12-12 21:07:12', '2025-12-12 21:08:49', 11.00, 2.00, 1.00, 1.00, 'cerrado', 'ddd', 'sdsd', '2025-12-13 00:07:12'),
(20, 9, 'juanjo villafañe', 9, '2025-12-12 21:09:13', '2025-12-12 21:11:58', 1000.00, 2000.00, 2050.00, -50.00, 'cerrado', 'dsd', '', '2025-12-13 00:09:13'),
(21, 6, 'admin', 6, '2025-12-12 21:14:14', '2025-12-12 21:16:46', 100.00, 5400.00, 540.00, 4860.00, 'cerrado', 'da', 'xD', '2025-12-13 00:14:14'),
(22, 6, 'admin', 6, '2025-12-12 21:24:30', '2025-12-12 21:48:36', 5000.00, 5000.00, 5000.00, 0.00, 'cerrado', 'khjkh', '', '2025-12-13 00:24:30'),
(23, 6, 'admin', 6, '2025-12-12 22:09:57', '2025-12-12 22:11:00', 1000.00, 1220.00, 1220.00, 0.00, 'cerrado', 'nueva caja', 'cierre caja', '2025-12-13 01:09:57'),
(24, 9, 'juanjo villafañe', 9, '2025-12-12 22:26:08', '2025-12-13 00:18:24', 40.00, 40.00, 40.00, 0.00, 'cerrado', '', '', '2025-12-13 01:26:08'),
(25, 9, 'juanjo villafañe', 9, '2025-12-13 00:18:47', '2025-12-14 00:21:14', 100.00, 100.00, 100.00, 0.00, 'cerrado', '', '', '2025-12-13 03:18:47'),
(26, 6, 'admin', 6, '2025-12-13 19:21:45', '2025-12-14 00:43:10', 100.00, 100.00, 100.00, 0.00, 'cerrado', '', '', '2025-12-13 22:21:45'),
(27, 12, 'nazareno fabian madero', 12, '2025-12-13 19:34:11', NULL, 1.00, NULL, NULL, NULL, 'abierto', '', NULL, '2025-12-13 22:34:11'),
(28, 9, 'juanjo villafañe', 9, '2025-12-14 19:17:26', '2025-12-14 19:17:52', 100.00, 2100.00, 2100.00, 0.00, 'cerrado', '', '', '2025-12-14 22:17:26'),
(29, 6, 'admin', 6, '2025-12-14 19:18:09', '2025-12-14 19:18:35', 1.00, 121.00, 121.00, 0.00, 'cerrado', '', '', '2025-12-14 22:18:09'),
(30, 6, 'admin', 6, '2025-12-14 19:27:02', '2025-12-14 20:07:48', 1.00, 1.00, 1.00, 0.00, 'cerrado', '', '', '2025-12-14 22:27:02'),
(31, 9, 'juanjo villafañe', 9, '2025-12-15 16:40:34', '2025-12-15 16:40:58', 100.00, 100.00, 100.00, 0.00, 'cerrado', 'sadhas', '', '2025-12-15 19:40:34'),
(32, 9, 'juanjo villafañe', 9, '2025-12-15 16:41:04', '2025-12-15 16:43:04', 100.00, 200.00, 220.00, -20.00, 'cerrado', '', 'nose porque', '2025-12-15 19:41:04'),
(33, 9, 'juanjo villafañe', 9, '2025-12-15 16:52:39', NULL, 11111.00, NULL, NULL, NULL, 'abierto', '', NULL, '2025-12-15 19:52:39'),
(34, 6, 'admin', 6, '2025-12-15 18:57:17', '2025-12-15 22:33:30', 1.00, 6000.00, 10782.00, -4782.00, 'cerrado', '', '', '2025-12-15 21:57:17'),
(35, 6, 'admin', 6, '2025-12-15 22:33:57', NULL, 600.00, NULL, NULL, NULL, 'abierto', '', NULL, '2025-12-16 01:33:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','empleado') DEFAULT 'empleado',
  `role_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `role`, `role_id`, `nombre`, `email`, `telefono`, `is_active`, `last_login`, `failed_attempts`, `locked_until`, `created_at`, `updated_at`) VALUES
(6, 'admin', '$argon2id$v=19$m=65536,t=4,p=3$UlZnSS9hY3VHY0xyUVpwdA$HE1zA4aUtavbmD9DiDFhanyRB4wRrIP5euwwNDCxjv8', 'admin', 1, NULL, NULL, NULL, 1, '2026-01-05 17:30:14', 0, NULL, '2025-09-26 19:38:53', '2026-01-05 17:30:14'),
(9, 'nuevouser', '$argon2id$v=19$m=65536,t=4,p=3$dlNvNzVPNFo0QmU0Zjh6Zg$3SzVcKZ6rh4dG1632Ib6bQX8pcfya/cws6cRr8zweac', 'empleado', 2, 'juanjo villafañe', 'jjj@gmail.com', NULL, 1, '2025-12-15 22:48:00', 0, NULL, '2025-12-12 01:23:31', '2025-12-15 22:48:00'),
(12, 'naza', '$argon2id$v=19$m=65536,t=4,p=3$bWVlMjdrbG9ZcDJwR1IxWQ$fSgCyWeftsp2jCwDhSdyjxOjGOnVVUy/BUBvZUxfOTA', 'empleado', 2, 'nazareno fabian madero', 'ebarile129@gmail.com', NULL, 1, '2025-12-13 22:57:26', 0, NULL, '2025-12-13 22:27:29', '2025-12-13 22:57:26'),
(13, 'juanjo', '$argon2id$v=19$m=65536,t=4,p=3$bExsWi9LNktqa2tEM01QZQ$UcCbVbaDgr3decyft06Ma73W4zykdGRo2NKcZgI4/eI', 'empleado', 3, 'juanjo', 'juanjo@gmail.com', NULL, 1, '2025-12-15 19:44:51', 0, NULL, '2025-12-14 03:11:57', '2025-12-15 19:44:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `descuento_total` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `monto_pagado` decimal(10,2) DEFAULT 0.00,
  `cambio` decimal(10,2) DEFAULT 0.00,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `usuario_id`, `cliente_id`, `total`, `descuento_total`, `subtotal`, `monto_pagado`, `cambio`, `fecha`) VALUES
(9, 9, NULL, 3300.00, 0.00, 0.00, 3500.00, 200.00, '2025-12-12 02:21:32'),
(10, 9, NULL, 600.00, 0.00, 0.00, 10000.00, 9400.00, '2025-12-12 02:23:44'),
(11, 9, NULL, 1110.00, 0.00, 0.00, 2000.00, 890.00, '2025-12-12 02:33:23'),
(12, 6, NULL, 250.00, 0.00, 0.00, 10000.00, 9750.00, '2025-12-12 20:52:02'),
(13, 9, NULL, 1050.00, 0.00, 0.00, 2000.00, 950.00, '2025-12-13 00:09:50'),
(14, 9, NULL, 1050.00, 0.00, 0.00, 2000.00, 950.00, '2025-12-13 00:11:44'),
(15, 6, NULL, 2000.00, 0.00, 0.00, 2000.00, 0.00, '2025-12-13 00:12:26'),
(16, 6, NULL, 120.00, 0.00, 0.00, 150.00, 30.00, '2025-12-13 00:14:28'),
(17, 6, NULL, 120.00, 0.00, 0.00, 150.00, 30.00, '2025-12-13 00:14:46'),
(18, 6, NULL, 100.00, 0.00, 0.00, 200.00, 100.00, '2025-12-13 00:14:55'),
(19, 6, NULL, 100.00, 0.00, 0.00, 130.00, 30.00, '2025-12-13 00:16:15'),
(20, 6, NULL, 220.00, 0.00, 0.00, 2000.00, 1780.00, '2025-12-13 01:10:09'),
(21, 9, NULL, 2000.00, 0.00, 0.00, 2000.00, 0.00, '2025-12-14 22:17:41'),
(22, 6, NULL, 120.00, 0.00, 0.00, 120.00, 0.00, '2025-12-14 22:18:26'),
(23, 9, NULL, 120.00, 0.00, 0.00, 200.00, 80.00, '2025-12-15 19:41:29'),
(24, 6, NULL, 120.00, 0.00, 120.00, 120.00, 0.00, '2025-12-15 22:30:28'),
(25, 6, NULL, 120.00, 0.00, 120.00, 120.00, 0.00, '2025-12-15 22:30:41'),
(26, 9, NULL, 100.00, 0.00, 100.00, 100.00, 0.00, '2025-12-15 23:08:00'),
(27, 6, NULL, 10001.00, 0.00, 10001.00, 11000.00, 999.00, '2025-12-15 23:23:52'),
(28, 6, 1, 120.00, 0.00, 120.00, 0.00, 0.00, '2025-12-15 23:32:43'),
(29, 6, 1, 120.00, 0.00, 120.00, 0.00, 0.00, '2025-12-16 00:16:15'),
(30, 6, 1, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 00:23:02'),
(31, 6, 1, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 00:31:13'),
(32, 6, 1, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 00:37:13'),
(33, 6, 2, 3125.00, 200.00, 3325.00, 0.00, 0.00, '2025-12-16 00:39:42'),
(34, 6, NULL, 440.00, 0.00, 440.00, 20000.00, 19560.00, '2025-12-16 00:48:52'),
(35, 6, 2, 100.00, 0.00, 100.00, 0.00, 0.00, '2025-12-16 00:49:25'),
(36, 6, 1, 11801.00, 200.00, 12001.00, 0.00, 0.00, '2025-12-16 01:09:22'),
(37, 6, 2, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 01:10:10'),
(38, 6, 2, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 01:26:56'),
(39, 6, 2, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 01:32:09'),
(40, 6, 1, 3600.00, 400.00, 4000.00, 0.00, 0.00, '2025-12-16 01:34:10'),
(41, 6, 2, 1800.00, 200.00, 2000.00, 0.00, 0.00, '2025-12-16 01:39:41'),
(42, 6, 2, 1900.00, 200.00, 2100.00, 900.00, 0.00, '2025-12-16 02:00:01'),
(43, 6, 2, 10001.00, 0.00, 10001.00, 1.00, 0.00, '2025-12-16 02:10:31'),
(44, 6, 2, 10001.00, 0.00, 10001.00, 1.00, 0.00, '2025-12-16 02:11:39'),
(45, 6, 2, 10001.00, 0.00, 10001.00, 1.00, 0.00, '2025-12-16 02:12:50'),
(46, 6, 2, 300.00, 0.00, 300.00, 0.00, 0.00, '2025-12-16 02:23:48'),
(47, 6, NULL, 750.00, 0.00, 750.00, 1000.00, 250.00, '2025-12-16 02:37:00'),
(48, 6, NULL, 150.00, 0.00, 150.00, 150.00, 0.00, '2025-12-16 23:58:41'),
(49, 6, NULL, 450.00, 0.00, 450.00, 2000.00, 1550.00, '2025-12-17 00:13:20'),
(50, 6, NULL, 600.00, 0.00, 600.00, 1000.00, 400.00, '2025-12-17 00:16:48'),
(51, 6, NULL, 600.00, 0.00, 600.00, 1000.00, 400.00, '2025-12-17 00:18:17'),
(52, 6, NULL, 300.00, 0.00, 300.00, 600.00, 300.00, '2025-12-17 00:20:27'),
(53, 6, NULL, 120.00, 0.00, 120.00, 200.00, 80.00, '2025-12-17 00:52:05'),
(54, 6, NULL, 150.00, 0.00, 150.00, 200.00, 50.00, '2025-12-17 02:31:22'),
(55, 6, NULL, 10000.00, 0.00, 10000.00, 30000.00, 20000.00, '2025-12-17 18:44:35'),
(56, 6, NULL, 10000.00, 0.00, 10000.00, 20000.00, 10000.00, '2025-12-17 18:44:54'),
(57, 6, NULL, 150.00, 0.00, 150.00, 2000.00, 1850.00, '2025-12-17 18:51:12'),
(58, 6, NULL, 300.00, 0.00, 300.00, 300.00, 0.00, '2026-01-04 21:09:25'),
(59, 6, NULL, 300.00, 0.00, 300.00, 300.00, 0.00, '2026-01-05 17:33:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_descuentos`
--

CREATE TABLE `venta_descuentos` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `promocion_id` int(11) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `monto_descuento` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_descuentos`
--

INSERT INTO `venta_descuentos` (`id`, `venta_id`, `promocion_id`, `tipo`, `descripcion`, `monto_descuento`, `created_at`) VALUES
(1, 30, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 00:23:02'),
(2, 31, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 00:31:13'),
(3, 32, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 00:37:13'),
(4, 33, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 00:39:42'),
(5, 36, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 01:09:22'),
(6, 37, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 01:10:10'),
(7, 38, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 01:26:56'),
(8, 39, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 01:32:09'),
(9, 40, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 400.00, '2025-12-16 01:34:10'),
(10, 41, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 01:39:41'),
(11, 42, 1, 'descuento_porcentaje', 'promo salchipapa (10.00% OFF)', 200.00, '2025-12-16 02:00:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta_detalles`
--

INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio`, `subtotal`) VALUES
(1, 1, 1, 10, 150.00, 1500.00),
(2, 2, 1, 10, 150.00, 1500.00),
(3, 3, 5, 10, 300.00, 3000.00),
(4, 4, 5, 10, 300.00, 3000.00),
(5, 5, 5, 8, 300.00, 2400.00),
(6, 6, 5, 3, 300.00, 900.00),
(7, 7, 4, 10, 80.00, 800.00),
(8, 8, 4, 5, 80.00, 400.00),
(9, 9, 1, 22, 150.00, 3300.00),
(10, 10, 1, 4, 150.00, 600.00),
(11, 11, 3, 8, 120.00, 960.00),
(12, 11, 1, 1, 150.00, 150.00),
(13, 12, 1, 1, 150.00, 150.00),
(14, 12, 2, 1, 100.00, 100.00),
(15, 13, 1, 1, 150.00, 150.00),
(16, 13, 2, 9, 100.00, 900.00),
(17, 14, 1, 1, 150.00, 150.00),
(18, 14, 2, 9, 100.00, 900.00),
(19, 15, 6, 1, 2000.00, 2000.00),
(20, 16, 3, 1, 120.00, 120.00),
(21, 17, 3, 1, 120.00, 120.00),
(22, 18, 2, 1, 100.00, 100.00),
(23, 19, 2, 1, 100.00, 100.00),
(24, 20, 2, 1, 100.00, 100.00),
(25, 20, 3, 1, 120.00, 120.00),
(26, 21, 6, 1, 2000.00, 2000.00),
(27, 22, 3, 1, 120.00, 120.00),
(28, 23, 3, 1, 120.00, 120.00),
(29, 24, 3, 1, 120.00, 120.00),
(30, 25, 3, 1, 120.00, 120.00),
(31, 26, 2, 1, 100.00, 100.00),
(32, 27, 8, 1, 10001.00, 10001.00),
(33, 28, 3, 1, 120.00, 120.00),
(34, 29, 3, 1, 120.00, 120.00),
(35, 30, 6, 1, 2000.00, 2000.00),
(36, 31, 6, 1, 2000.00, 2000.00),
(37, 32, 6, 1, 2000.00, 2000.00),
(38, 33, 6, 1, 2000.00, 2000.00),
(39, 33, 3, 1, 120.00, 120.00),
(40, 33, 7, 1, 1205.00, 1205.00),
(41, 34, 3, 2, 120.00, 240.00),
(42, 34, 2, 2, 100.00, 200.00),
(43, 35, 2, 1, 100.00, 100.00),
(44, 36, 6, 1, 2000.00, 2000.00),
(45, 36, 8, 1, 10001.00, 10001.00),
(46, 37, 6, 1, 2000.00, 2000.00),
(47, 38, 6, 1, 2000.00, 2000.00),
(48, 39, 6, 1, 2000.00, 2000.00),
(49, 40, 6, 2, 2000.00, 4000.00),
(50, 41, 6, 1, 2000.00, 2000.00),
(51, 42, 6, 1, 2000.00, 2000.00),
(52, 42, 2, 1, 100.00, 100.00),
(53, 43, 8, 1, 10001.00, 10001.00),
(54, 44, 8, 1, 10001.00, 10001.00),
(55, 45, 8, 1, 10001.00, 10001.00),
(56, 46, 1, 2, 150.00, 300.00),
(57, 47, 1, 5, 150.00, 750.00),
(58, 48, 1, 1, 150.00, 150.00),
(59, 49, 1, 1, 150.00, 150.00),
(60, 49, 5, 1, 300.00, 300.00),
(61, 50, 5, 2, 300.00, 600.00),
(62, 51, 5, 2, 300.00, 600.00),
(63, 52, 1, 2, 150.00, 300.00),
(64, 53, 3, 1, 120.00, 120.00),
(65, 54, 1, 1, 150.00, 150.00),
(66, 55, 10, 2, 5000.00, 10000.00),
(67, 56, 10, 2, 5000.00, 10000.00),
(68, 57, 1, 1, 150.00, 150.00),
(69, 58, 5, 1, 300.00, 300.00),
(70, 59, 1, 2, 150.00, 300.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_pagos`
--

CREATE TABLE `venta_pagos` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `metodo_pago_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_pagos`
--

INSERT INTO `venta_pagos` (`id`, `venta_id`, `metodo_pago_id`, `monto`, `referencia`, `telefono`, `created_at`) VALUES
(1, 24, 1, 120.00, NULL, NULL, '2025-12-15 22:30:28'),
(2, 25, 1, 120.00, NULL, NULL, '2025-12-15 22:30:41'),
(3, 26, 1, 100.00, NULL, NULL, '2025-12-15 23:08:00'),
(4, 27, 1, 11000.00, NULL, NULL, '2025-12-15 23:23:52'),
(5, 28, 5, 120.00, NULL, NULL, '2025-12-15 23:32:44'),
(6, 29, 5, 120.00, NULL, NULL, '2025-12-16 00:16:15'),
(7, 30, 5, 1800.00, NULL, NULL, '2025-12-16 00:23:03'),
(8, 31, 5, 1800.00, NULL, NULL, '2025-12-16 00:31:13'),
(9, 32, 5, 1800.00, NULL, NULL, '2025-12-16 00:37:14'),
(10, 33, 5, 3125.00, NULL, NULL, '2025-12-16 00:39:42'),
(11, 34, 1, 20000.00, NULL, NULL, '2025-12-16 00:48:52'),
(12, 35, 5, 100.00, NULL, NULL, '2025-12-16 00:49:25'),
(13, 36, 5, 11801.00, NULL, NULL, '2025-12-16 01:09:22'),
(14, 37, 5, 1800.00, NULL, NULL, '2025-12-16 01:10:11'),
(15, 38, 5, 1800.00, NULL, NULL, '2025-12-16 01:26:56'),
(16, 39, 5, 1800.00, NULL, NULL, '2025-12-16 01:32:09'),
(17, 40, 5, 3600.00, NULL, NULL, '2025-12-16 01:34:10'),
(18, 41, 5, 1800.00, NULL, NULL, '2025-12-16 01:39:41'),
(19, 42, 5, 1000.00, NULL, NULL, '2025-12-16 02:00:01'),
(20, 42, 1, 900.00, NULL, NULL, '2025-12-16 02:00:01'),
(21, 43, 5, 10000.00, NULL, NULL, '2025-12-16 02:10:31'),
(22, 43, 1, 1.00, NULL, NULL, '2025-12-16 02:10:31'),
(23, 44, 5, 10000.00, NULL, NULL, '2025-12-16 02:11:40'),
(24, 44, 1, 1.00, NULL, NULL, '2025-12-16 02:11:40'),
(25, 45, 5, 10000.00, NULL, NULL, '2025-12-16 02:12:51'),
(26, 45, 1, 1.00, NULL, NULL, '2025-12-16 02:12:51'),
(27, 46, 5, 300.00, NULL, NULL, '2025-12-16 02:23:49'),
(28, 47, 1, 1000.00, NULL, NULL, '2025-12-16 02:37:00'),
(29, 48, 1, 150.00, NULL, NULL, '2025-12-16 23:58:41'),
(30, 49, 1, 2000.00, NULL, NULL, '2025-12-17 00:13:20'),
(31, 50, 1, 1000.00, NULL, NULL, '2025-12-17 00:16:49'),
(32, 51, 1, 1000.00, NULL, NULL, '2025-12-17 00:18:17'),
(33, 52, 1, 600.00, NULL, NULL, '2025-12-17 00:20:27'),
(34, 53, 1, 200.00, NULL, NULL, '2025-12-17 00:52:05'),
(35, 54, 1, 200.00, NULL, NULL, '2025-12-17 02:31:23'),
(36, 56, 1, 20000.00, NULL, NULL, '2025-12-17 18:44:54'),
(37, 57, 1, 2000.00, NULL, NULL, '2025-12-17 18:51:12'),
(38, 58, 4, 300.00, 'juan perez', NULL, '2026-01-04 21:09:26'),
(39, 59, 4, 300.00, 'ppepe', '394729842', '2026-01-05 17:33:51');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_telefono` (`telefono`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `cliente_pagos`
--
ALTER TABLE `cliente_pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `turno_id` (`turno_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username_time` (`username`,`attempted_at`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempted_at`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_turno` (`turno_id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_codigo_barra` (`codigo_barra`),
  ADD KEY `idx_categoria` (`categoria_id`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_fechas` (`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `promocion_productos`
--
ALTER TABLE `promocion_productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_promo_producto` (`promocion_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`idProveedor`),
  ADD UNIQUE KEY `cuit` (`cuit`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_apertura` (`fecha_apertura`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indices de la tabla `venta_descuentos`
--
ALTER TABLE `venta_descuentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `promocion_id` (`promocion_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `venta_pagos`
--
ALTER TABLE `venta_pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodo_pago_id` (`metodo_pago_id`),
  ADD KEY `idx_venta` (`venta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `cliente_pagos`
--
ALTER TABLE `cliente_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `promocion_productos`
--
ALTER TABLE `promocion_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `venta_descuentos`
--
ALTER TABLE `venta_descuentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `venta_pagos`
--
ALTER TABLE `venta_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente_pagos`
--
ALTER TABLE `cliente_pagos`
  ADD CONSTRAINT `cliente_pagos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`turno_id`) REFERENCES `turnos_caja` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_caja_ibfk_2` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `movimientos_caja_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD CONSTRAINT `promociones_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `promocion_productos`
--
ALTER TABLE `promocion_productos`
  ADD CONSTRAINT `promocion_productos_ibfk_1` FOREIGN KEY (`promocion_id`) REFERENCES `promociones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promocion_productos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD CONSTRAINT `turnos_caja_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `venta_descuentos`
--
ALTER TABLE `venta_descuentos`
  ADD CONSTRAINT `venta_descuentos_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_descuentos_ibfk_2` FOREIGN KEY (`promocion_id`) REFERENCES `promociones` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `venta_pagos`
--
ALTER TABLE `venta_pagos`
  ADD CONSTRAINT `venta_pagos_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_pagos_ibfk_2` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
