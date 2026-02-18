-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-02-2026 a las 17:24:48
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
-- Base de datos: `ferreteria_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-tiagoraminelli@gmail.com|127.0.0.1', 'i:2;', 1771195176),
('laravel-cache-tiagoraminelli@gmail.com|127.0.0.1:timer', 'i:1771195176;', 1771195176);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(50) DEFAULT 'fa-box',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`, `activo`, `created_at`) VALUES
(1, 'Herramientas Manuales', 'Martillos, destornilladores, llaves, alicates', 'fa-wrench', 1, '2026-02-12 02:49:58'),
(2, 'Herramientas Eléctricas', 'Taladros, amoladoras, sierras eléctricas', 'fa-power-off', 1, '2026-02-12 02:49:58'),
(3, 'Electricidad', 'Cables, enchufes, llaves térmicas, cajas', 'fa-bolt', 1, '2026-02-12 02:49:58'),
(4, 'Plomería', 'Caños, conexiones, grifería, accesorios', 'fa-tint', 1, '2026-02-12 02:49:58'),
(5, 'Pinturería', 'Pinturas, pinceles, rodillos, diluyentes', 'fa-paint-brush', 1, '2026-02-12 02:49:58'),
(6, 'Construcción', 'Cemento, arena, ladrillos, bloques', 'fa-building', 1, '2026-02-12 02:49:58'),
(7, 'Tornillería y Bulonería', 'Tornillos, tuercas, arandelas, bulones', 'fa-cog', 1, '2026-02-12 02:49:58'),
(8, 'Ferretería General', 'Candados, bisagras, cerraduras, herrajes', 'fa-key', 1, '2026-02-12 02:49:58'),
(9, 'Jardín y Exterior', 'Mangueras, aspersores, herramientas de jardín', 'fa-leaf', 1, '2026-02-12 02:49:58'),
(10, 'Seguridad', 'Candados, alarmas, cámaras, elementos de protección', 'fa-shield-alt', 1, '2026-02-12 02:49:58'),
(11, 'Adhesivos y Selladores', 'Pegamentos, siliconas, cintas, selladores', 'fa-tape', 1, '2026-02-12 02:49:58'),
(12, 'Maderas y Tableros', 'Madera, MDF, melamina, terciados', 'fa-tree', 1, '2026-02-12 02:49:58'),
(13, 'Abrasivos', 'Lijas, discos de corte, piedras de amolar', 'fa-circle', 1, '2026-02-12 02:49:58'),
(14, 'Iluminación', 'Lámparas, tubos, focos, LED', 'fa-lightbulb', 1, '2026-02-12 02:49:58'),
(15, 'Climatización', 'Ventiladores, estufas, aires acondicionados', 'fa-fan', 1, '2026-02-12 02:49:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `tipo_documento` enum('DNI','CUIT','CUIL','Pasaporte') DEFAULT 'DNI',
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `saldo_cuenta_corriente` decimal(10,2) DEFAULT 0.00,
  `limite_credito` decimal(10,2) DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `documento`, `tipo_documento`, `telefono`, `email`, `direccion`, `ciudad`, `provincia`, `codigo_postal`, `saldo_cuenta_corriente`, `limite_credito`, `notas`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'juanjo', '3234563434343', 'DNI', '34533434562', 'admin@gmail.com', 'calle falsa 123', NULL, NULL, NULL, 2080.00, 0.00, NULL, 1, '2026-02-12 23:29:20', '2026-02-13 18:53:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_corriente_movimientos`
--

CREATE TABLE `cuenta_corriente_movimientos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `tipo` enum('venta','pago','ajuste_debito','ajuste_credito') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `saldo_historico` decimal(10,2) NOT NULL DEFAULT 0.00,
  `referencia_id` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuenta_corriente_movimientos`
--

INSERT INTO `cuenta_corriente_movimientos` (`id`, `cliente_id`, `tipo`, `monto`, `saldo_historico`, `referencia_id`, `descripcion`, `usuario_id`, `fecha`) VALUES
(1, 1, 'venta', 1040.00, 1040.00, 3, 'Venta #3', 1, '2026-02-12 23:35:50'),
(2, 1, 'venta', 1040.00, 2080.00, 4, 'Venta #4', 1, '2026-02-13 18:53:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-11 23:55:45'),
(2, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-11 23:56:45'),
(3, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-11 23:58:37'),
(4, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:01:56'),
(5, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 1, '2026-02-12 00:02:32'),
(6, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 1, '2026-02-12 00:02:45'),
(7, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:04:04'),
(8, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:04:22'),
(9, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 1, '2026-02-12 00:05:35'),
(10, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:13:38'),
(11, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:13:51'),
(12, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:14:23'),
(13, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 00:16:41'),
(14, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 21:06:54'),
(15, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 21:12:20'),
(16, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-02-12 21:36:15'),
(17, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 1, '2026-02-12 23:31:25'),
(18, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, '2026-02-13 18:50:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `pais_origen` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `nombre`, `pais_origen`, `descripcion`, `activo`, `created_at`) VALUES
(1, 'Bahco', 'Suecia', 'Herramientas manuales profesionales', 1, '2026-02-12 02:49:58'),
(2, 'Stanley', 'Estados Unidos', 'Herramientas y accesorios', 1, '2026-02-12 02:49:58'),
(3, 'Bosch', 'Alemania', 'Herramientas eléctricas y accesorios', 1, '2026-02-12 02:49:58'),
(4, 'DeWalt', 'Estados Unidos', 'Herramientas eléctricas profesionales', 1, '2026-02-12 02:49:58'),
(5, 'Makita', 'Japón', 'Herramientas eléctricas de alta calidad', 1, '2026-02-12 02:49:58'),
(6, 'Black+Decker', 'Estados Unidos', 'Herramientas para hogar y jardín', 1, '2026-02-12 02:49:58'),
(7, 'Einhell', 'Alemania', 'Herramientas y equipos de bricolaje', 1, '2026-02-12 02:49:58'),
(8, 'Tramontina', 'Brasil', 'Herramientas y productos para el hogar', 1, '2026-02-12 02:49:58'),
(9, 'Philips', 'Países Bajos', 'Iluminación y productos electrónicos', 1, '2026-02-12 02:49:58'),
(10, 'Osram', 'Alemania', 'Iluminación profesional', 1, '2026-02-12 02:49:58'),
(11, '3M', 'Estados Unidos', 'Adhesivos, abrasivos y protección', 1, '2026-02-12 02:49:58'),
(12, 'Sika', 'Suiza', 'Selladores y productos químicos para construcción', 1, '2026-02-12 02:49:58'),
(13, 'Fischer', 'Alemania', 'Anclajes y fijaciones', 1, '2026-02-12 02:49:58'),
(14, 'Karcher', 'Alemania', 'Limpieza y equipos de alta presión', 1, '2026-02-12 02:49:58'),
(15, 'Total Tools', 'China', 'Herramientas accesibles para múltiples usos', 1, '2026-02-12 02:49:58'),
(16, 'Pretul', 'México', 'Herramientas económicas', 1, '2026-02-12 02:49:58'),
(17, 'Ema', 'Argentina', 'Accesorios y ferretería general', 1, '2026-02-12 02:49:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `tipo` enum('venta','ingreso','egreso','apertura','cierre','inicial') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_caja`
--

INSERT INTO `movimientos_caja` (`id`, `turno_id`, `tipo`, `monto`, `descripcion`, `venta_id`, `usuario_id`, `fecha`, `created_at`) VALUES
(1, 3, 'inicial', 10.00, 'Apertura de turno', NULL, 1, '2026-02-12 00:16:56', '2026-02-12 00:16:56'),
(2, 3, 'venta', 1040.00, 'Venta #2', 2, 1, '2026-02-12 00:31:08', '2026-02-12 00:31:08'),
(3, 4, 'venta', 520.00, 'Venta #5', 5, 1, '2026-02-13 18:58:46', '2026-02-13 18:58:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `codigo_barra` varchar(50) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `unidad_medida_id` int(11) DEFAULT 1,
  `proveedor_id` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_costo` decimal(10,2) DEFAULT 0.00,
  `margen_ganancia` decimal(5,2) DEFAULT 30.00,
  `stock` decimal(10,3) NOT NULL DEFAULT 0.000,
  `unidad_medida` varchar(20) DEFAULT 'unid',
  `stock_minimo` decimal(10,3) NOT NULL DEFAULT 0.000,
  `ubicacion_deposito` varchar(50) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `codigo_barra`, `modelo`, `categoria_id`, `marca_id`, `unidad_medida_id`, `proveedor_id`, `precio`, `precio_costo`, `margen_ganancia`, `stock`, `unidad_medida`, `stock_minimo`, `ubicacion_deposito`, `imagen`, `activo`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'pedazo de chapa', '', '123123123', '2sd3das', 6, NULL, 2, NULL, 520.00, 400.00, 30.00, 3.000, 'unid', 5.000, NULL, NULL, 1, '2026-02-13 19:36:03', '2026-02-12 00:18:09', '2026-02-15 00:23:53'),
(2, 'Cable 2.5mm', NULL, 'TEST-1770855977', NULL, NULL, 1, 1, NULL, 150.50, 0.00, 30.00, 98.500, 'mts', 0.000, NULL, NULL, 1, NULL, '2026-02-12 00:26:17', '2026-02-15 00:23:54'),
(3, 'prueba', 'articulo de prueba', '8473432', NULL, NULL, NULL, 2, NULL, 130.00, 100.00, 30.00, 20.300, 'unid', 20.100, NULL, NULL, 1, '2026-02-13 19:36:18', '2026-02-12 21:31:32', '2026-02-13 19:36:18'),
(4, 'ACOPLE RÁPIDO', '1/2', 'P0001', NULL, 4, 8, 1, NULL, 6100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(5, 'ACOPLE RÁPIDO', '3/3', 'P0002', NULL, 4, 8, 1, NULL, 7000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(6, 'ACOPLE RÁPIDO', '1\"', 'P0003', NULL, 4, 8, 1, NULL, 7700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(7, 'AEROSOL BLANCO', 'BRILLANTE - DOBLE AA X 250G', 'P0004', NULL, 5, 11, 1, NULL, 5030.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(8, 'AEROSOL BLANCO', 'SATINADO - DOBLE AA X 250G', 'P0005', NULL, 5, 11, 1, NULL, 5030.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(9, 'AEROSOL BLANCO', 'MATE - DOBLE AA X 250G ', 'P0006', NULL, 5, 11, 1, NULL, 5030.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(10, 'AEROSOL NEGRO', 'BRILLANTE X250G', 'P0007', NULL, 5, 11, 1, NULL, 5030.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(11, 'AEROSOL NEGRO', 'SATINADO X 250G', 'P0008', NULL, 5, 11, 1, NULL, 5030.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(12, 'AEROSOL NEGRO', 'MATE X 250G', 'P0009', NULL, 5, 11, 1, NULL, 5030.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(13, 'ALICATE CORTE DIAG', 'CROSS 6½\"', 'P0010', NULL, 1, 1, 1, NULL, 13500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(14, 'ARGOLLA NIQUELADAS P/TOLDO', ' Nº 30', 'P0011', NULL, 4, NULL, 1, NULL, 26466.48, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(15, 'BARRAL', '22 MM X 2,00 MTS. CEDRO S/ARG.', 'P0012', NULL, 6, NULL, 1, NULL, 11096.14, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(16, 'BARRAL ', '22 MM X 1,40 MTS. CEDRO S/ARG.', 'P0013', NULL, 6, NULL, 1, NULL, 9481.08, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(17, 'BARRAL ', '22 MM X 1,80 MTS. CEDRO S/ARG.', 'P0014', NULL, 6, NULL, 1, NULL, 10570.08, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(18, 'BARRAL ', '22 MM X 2,20 MTS . CEDRO S/ARG.', 'P0015', NULL, 6, NULL, 1, NULL, 11644.13, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(19, 'BASE PARA 1 MODULO', 'JELUZ EXTERIOR', 'P0016', NULL, 3, 17, 1, NULL, 428.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(20, 'BASE PARA 2 MODULOS', 'JELUZ EXTERIOR', 'P0017', NULL, 3, 17, 1, NULL, 718.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(21, 'BASE PARA 3 MODULOS', 'JELUZ EXTERIOR', 'P0018', NULL, 3, 17, 1, NULL, 1430.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(22, 'BASE PARA 4 MODULOS', 'JELUZ EXTERIOR', 'P0019', NULL, 3, 17, 1, NULL, 1911.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(23, 'BASE PARA 5 MODULOS', 'JELUZ EXTERIOR', 'P0020', NULL, 3, 17, 1, NULL, 2300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(24, 'BOMBA CENTRIFUGA BTA', '-', 'P0021', NULL, 2, 3, 1, NULL, 52000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(25, 'BOYA PILETA SATELITE CHICA', '- ', 'P0022', NULL, 4, 8, 1, NULL, 3000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(26, 'BUJE M-H RED. ', '¾\" X ½\"', 'P0023', NULL, 4, 8, 1, NULL, 221.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(27, 'BUJE M-H RED. ', '1\" X ¾\"', 'P0024', NULL, 4, 8, 1, NULL, 410.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(28, 'BULON ZINCADO 1/4', '1/4 X 1/2', 'P0025', NULL, 7, 13, 1, NULL, 62.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(29, 'BULON ZINCADO 1/4', '1/4 X 5/8', 'P0026', NULL, 7, 13, 1, NULL, 68.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(30, 'BULON ZINCADO 1/4', '1/4 X 3/4', 'P0027', NULL, 7, 13, 1, NULL, 72.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(31, 'BULON ZINCADO 1/4', '1/4 X 7/8', 'P0028', NULL, 7, 13, 1, NULL, 75.46, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(32, 'BULON ZINCADO 1/4', '1/4 X 1', 'P0029', NULL, 7, 13, 1, NULL, 76.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(33, 'BULON ZINCADO 1/4', '1/4 X 1 1/4', 'P0030', NULL, 7, 13, 1, NULL, 99.30, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(34, 'BULON ZINCADO 1/4', '1/4 X 1 1/2', 'P0031', NULL, 7, 13, 1, NULL, 112.11, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(35, 'BULON ZINCADO 1/4', '1/4 X 1 3/4', 'P0032', NULL, 7, 13, 1, NULL, 133.64, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(36, 'BULON ZINCADO 1/4', '1/4 X 2', 'P0033', NULL, 7, 13, 1, NULL, 150.30, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(37, 'BULON ZINCADO 1/4', '1/4 X 2 1/4', 'P0034', NULL, 7, 13, 1, NULL, 160.26, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(38, 'BULON ZINCADO 1/4', '1/4 X 2 1/2', 'P0035', NULL, 7, 13, 1, NULL, 178.60, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(39, 'BULON ZINCADO 1/4', '1/4 X 2 3/4', 'P0036', NULL, 7, 13, 1, NULL, 194.09, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(40, 'BULON ZINCADO 1/4', '1/4 X 3', 'P0037', NULL, 7, 13, 1, NULL, 211.43, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(41, 'BULON ZINCADO 1/4', '1/4 X 3 1/4', 'P0038', NULL, 7, 13, 1, NULL, 234.26, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(42, 'BULON ZINCADO 1/4', '1/4 X 3 1/2', 'P0039', NULL, 7, 13, 1, NULL, 251.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(43, 'BULON ZINCADO 1/4', '1/4 X 4', 'P0040', NULL, 7, 13, 1, NULL, 283.75, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(44, 'BULON ZINCADO 1/4', '1/4 X 4 1/2', 'P0041', NULL, 7, 13, 1, NULL, 317.71, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(45, 'BULON ZINCADO 1/4', '1/4 X 5', 'P0042', NULL, 7, 13, 1, NULL, 343.34, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(46, 'BULON ZINCADO 1/4', '1/4 X 5 1/2', 'P0043', NULL, 7, 13, 1, NULL, 460.74, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(47, 'BULON ZINCADO 1/4', '1/4 X 6', 'P0044', NULL, 7, 13, 1, NULL, 500.77, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(48, 'BULON ZINCADO 3/8', '3/8 X 1/2', 'P0045', NULL, 7, 13, 1, NULL, 155.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(49, 'BULON ZINCADO 3/8', '3/8 X 5/8', 'P0046', NULL, 7, 13, 1, NULL, 146.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(50, 'BULON ZINCADO 3/8', '3/8 X 3/4', 'P0047', NULL, 7, 13, 1, NULL, 162.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(51, 'BULON ZINCADO 3/8', '3/8 X 7/8', 'P0048', NULL, 7, 13, 1, NULL, 172.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(52, 'BULON ZINCADO 3/8', '3/8 X 1', 'P0049', NULL, 7, 13, 1, NULL, 183.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(53, 'BULON ZINCADO 3/8', '3/8 X 1 1/4', 'P0050', NULL, 7, 13, 1, NULL, 220.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(54, 'BULON ZINCADO 3/8', '3/8 X 1 1/2', 'P0051', NULL, 7, 13, 1, NULL, 240.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(55, 'BULON ZINCADO 3/8', '3/8 X 1 3/4', 'P0052', NULL, 7, 13, 1, NULL, 294.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(56, 'BULON ZINCADO 3/8', '3/8 X 2', 'P0053', NULL, 7, 13, 1, NULL, 309.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(57, 'BULON ZINCADO 3/8', '3/8 X 2 1/4', 'P0054', NULL, 7, 13, 1, NULL, 363.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(58, 'BULON ZINCADO 3/8', '3/8 X 2 1/2', 'P0055', NULL, 7, 13, 1, NULL, 400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(59, 'BULON ZINCADO 3/8', '3/8 X 2 3/4', 'P0056', NULL, 7, 13, 1, NULL, 452.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(60, 'BULON ZINCADO 3/8', '3/8 X 3', 'P0057', NULL, 7, 13, 1, NULL, 485.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(61, 'BULON ZINCADO 3/8', '3/8 X 3 1/4', 'P0058', NULL, 7, 13, 1, NULL, 532.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(62, 'BULON ZINCADO 3/8', '3/8 X 3 1/2', 'P0059', NULL, 7, 13, 1, NULL, 572.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(63, 'BULON ZINCADO 3/8', '3/8 X 4', 'P0060', NULL, 7, 13, 1, NULL, 622.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(64, 'BULON ZINCADO 5/16', '5/16 X 1/2', 'P0061', NULL, 7, 13, 1, NULL, 97.24, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(65, 'BULON ZINCADO 5/16', '5/16 X 5/8', 'P0062', NULL, 7, 13, 1, NULL, 102.33, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(66, 'BULON ZINCADO 5/16', '5/16 X 3/4', 'P0063', NULL, 7, 13, 1, NULL, 108.44, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(67, 'BULON ZINCADO 5/16', '5/16 X 7/8', 'P0064', NULL, 7, 13, 1, NULL, 116.26, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(68, 'BULON ZINCADO 5/16', '5/16  X 1', 'P0065', NULL, 7, 13, 1, NULL, 126.52, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(69, 'BULON ZINCADO 5/16', '5/16 X 1 1/4', 'P0066', NULL, 7, 13, 1, NULL, 145.77, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(70, 'BULON ZINCADO 5/16', '5/16 X 1 1/2', 'P0067', NULL, 7, 13, 1, NULL, 172.62, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(71, 'BULON ZINCADO 5/16', '5/16 X 1 3/4 ', 'P0068', NULL, 7, 13, 1, NULL, 195.51, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(72, 'BULON ZINCADO 5/16', '5/16 X 2', 'P0069', NULL, 7, 13, 1, NULL, 223.31, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(73, 'BULON ZINCADO 5/16', '5/16 X 2 1/4', 'P0070', NULL, 7, 13, 1, NULL, 246.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(74, 'BULON ZINCADO 5/16', '5/16 X 2 1/2', 'P0071', NULL, 7, 13, 1, NULL, 269.49, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(75, 'BULON ZINCADO 5/16', '5/16 X 2 3/4', 'P0072', NULL, 7, 13, 1, NULL, 311.52, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(76, 'BULON ZINCADO 5/16', '5/16 X 3', 'P0073', NULL, 7, 13, 1, NULL, 331.61, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(77, 'BULON ZINCADO 5/16', '5/16 X 3 1/4', 'P0074', NULL, 7, 13, 1, NULL, 364.71, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(78, 'BULON ZINCADO 5/16', '5/16 X 3 1/2', 'P0075', NULL, 7, 13, 1, NULL, 389.09, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(79, 'BULON ZINCADO 5/16', '5/16 X 4', 'P0076', NULL, 7, 13, 1, NULL, 419.28, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(80, 'BULON ZINCADO 5/16', '5/16 X 4 1/2', 'P0077', NULL, 7, 13, 1, NULL, 484.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(81, 'BULON ZINCADO 5/16', '5/16 X 5', 'P0078', NULL, 7, 13, 1, NULL, 524.51, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(82, 'BULON ZINCADO 5/16', '5/16 X 5 1/2', 'P0079', NULL, 7, 13, 1, NULL, 643.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(83, 'BULON ZINCADO 5/16', '5/16 X 6 ', 'P0080', NULL, 7, 13, 1, NULL, 747.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(84, 'BULON ZINCADO 5/16', '5/16 X 7', 'P0081', NULL, 7, 13, 1, NULL, 860.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(85, 'BULON ZINCADO 5/16', '5/16 X 8', 'P0082', NULL, 7, 13, 1, NULL, 980.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(86, 'BUSCAPOLO CHICO ', '3 X 140 SICA', 'P0083', NULL, 2, 3, 1, NULL, 2500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(87, 'CABLE CANAL', '20 X 10 C/ADH X 2MTS.', 'P0084', NULL, 3, NULL, 1, NULL, 7400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(88, 'CABLE CANAL', '20 X 10 S/ADH X 2 MTS.', 'P0085', NULL, 3, NULL, 1, NULL, 5400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(89, 'CABLE CANAL ACCESORIOS', '20 X 10', 'P0086', NULL, 3, NULL, 1, NULL, 940.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(90, 'CABLE COAXIL ', 'REG X MT', 'P0087', NULL, 3, NULL, 1, NULL, 650.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(91, 'CABLE COAXIL ', 'RG59 X MT', 'P0088', NULL, 3, NULL, 1, NULL, 1100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(92, 'CABLE ENV. REDONDO', '2 X1.5 KALOP', 'P0090', NULL, 3, 15, 1, NULL, 1900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(93, 'CABLE ENV. REDONDO', '2 X 2.5', 'P0091', NULL, 3, 15, 1, NULL, 2600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(94, 'CABLE ENV. REDONDO', '2 X 4', 'P0092', NULL, 3, 15, 1, NULL, 3887.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(95, 'CABLE ENV. REDONDO', '3 X 1', 'P0093', NULL, 3, 15, 1, NULL, 2500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(96, 'CABLE ENV. REDONDO', '3 X 1,5', 'P0094', NULL, 3, 15, 1, NULL, 3430.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(97, 'CABLE ENV. REDONDO', '3 X 2,5', 'P0095', NULL, 3, 15, 1, NULL, 3670.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(98, 'CABLE ENV. REDONDO', '4 X 1', 'P0096', NULL, 3, 15, 1, NULL, 2310.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(99, 'CABLE ENV. REDONDO', '4 X 1,5', 'P0097', NULL, 3, 15, 1, NULL, 3020.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(100, 'CABLE ENV. REDONDO', '4 X 2,5', 'P0098', NULL, 3, 15, 1, NULL, 4590.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(101, 'CABLE IMSA', '1 X 1', 'P0099', NULL, 3, 15, 1, NULL, 407.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(102, 'CABLE IMSA', '1 X 1.5', 'P0100', NULL, 3, 15, 1, NULL, 588.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(103, 'CABLE IMSA', '1 X 2.5', 'P0101', NULL, 3, 15, 1, NULL, 935.20, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(104, 'CABLE IMSA', '1 X 4', 'P0102', NULL, 3, 15, 1, NULL, 1385.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(105, 'CABLE IMSA', '1 X 6', 'P0103', NULL, 3, 15, 1, NULL, 2220.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(106, 'CABLE SUPERASTIC', '1 X 1', 'P0104', NULL, 3, 15, 1, NULL, 595.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(107, 'CABLE SUPERASTIC', '1 X 1.5', 'P0105', NULL, 3, 15, 1, NULL, 820.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(108, 'CABLE SUPERASTIC', '1 X 2.5', 'P0106', NULL, 3, 15, 1, NULL, 1308.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(109, 'CABLE SUPERASTIC', '1 X 4', 'P0107', NULL, 3, 15, 1, NULL, 2100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(110, 'CABLE SUPERASTIC', '1 X 6', 'P0108', NULL, 3, 15, 1, NULL, 3100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(111, 'CABOS MARTILLO CARPINTERO', '12\"', 'P0109', NULL, 1, 2, 1, NULL, 1050.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(112, 'CABOS MARTILLO MACETA', '14\"', 'P0110', NULL, 1, 2, 1, NULL, 1300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(113, 'CABOS MARTILLO MACETA', '16\"', 'P0111', NULL, 1, 2, 1, NULL, 1400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(114, 'CAJA CAPSULADA MIG.', '-', 'P0112', NULL, 3, 17, 1, NULL, 4000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(115, 'CAJA HIERRO CUADRADA', '10X10', 'P0113', NULL, 3, 17, 1, NULL, 1600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(116, 'CAJA HIERRO OCTAGONAL ', 'CHICA', 'P0114', NULL, 3, 17, 1, NULL, 530.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(117, 'CAJA HIERRO OCTAGONAL ', 'GRANDE', 'P0115', NULL, 3, 17, 1, NULL, 1112.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(118, 'CAJA HIERRO RECTANGULAR', '5X10', 'P0116', NULL, 3, 17, 1, NULL, 530.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(119, 'CAJA PVC OCTAGONAL', 'CHICA', 'P0117', NULL, 3, 17, 1, NULL, 500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(120, 'CAJA PVC RECTANGULAR', '5X10', 'P0118', NULL, 3, 17, 1, NULL, 500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(121, 'CALEFON ELECTRICO', 'ACERO INOX 20LTS', 'P0119', NULL, 4, 12, 1, NULL, 72000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(122, 'CALEFON ELECTRICO', 'ACERO INOX 10LTS', 'P0120', NULL, 4, 12, 1, NULL, 65000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(123, 'CALEFON RESISTENCIA \"FOCO\"', '-', 'P0122', NULL, 4, 12, 1, NULL, 9100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(124, 'CALEFON RESISTENCIA \"PULMÓN\"', '-', 'P0123', NULL, 4, 12, 1, NULL, 12500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(125, 'CANILLA METÁLICA ESF', '1\"', 'P0126', NULL, 4, 12, 1, NULL, 17500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(126, 'CAÑERIA ROSCA CODO 1/2\"', '1/2\" X 45° H-H', 'P0127', NULL, 4, NULL, 1, NULL, 813.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(127, 'CAÑERIA ROSCA CODO 1/2\"', 'H-H C/INS.', 'P0128', NULL, 4, NULL, 1, NULL, 3500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(128, 'CAÑERIA ROSCA CODO 3', '3/4\" X 45° H-H', 'P0129', NULL, 4, NULL, 1, NULL, 1070.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(129, 'CAÑERIA ROSCA CODO 3/4\"', '1\" X 45° H-H', 'P0130', NULL, 4, NULL, 1, NULL, 1940.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(130, 'CAÑERIA ROSCA CODO 3/4\"', 'H-H C/INS.', 'P0131', NULL, 4, NULL, 1, NULL, 5200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(131, 'CAÑERIA ROSCA CODO H-H', '1/2\"', 'P0132', NULL, 4, NULL, 1, NULL, 375.30, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(132, 'CAÑERIA ROSCA CODO H-H', '3/4\"', 'P0133', NULL, 4, NULL, 1, NULL, 598.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(133, 'CAÑERIA ROSCA CODO H-H', '1\"', 'P0134', NULL, 4, NULL, 1, NULL, 1066.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(134, 'CAÑERIA ROSCA CODO M-H', '1/2\"', 'P0135', NULL, 4, NULL, 1, NULL, 394.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(135, 'CAÑERIA ROSCA CODO M-H', '3/4\"', 'P0136', NULL, 4, NULL, 1, NULL, 625.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(136, 'CAÑERIA ROSCA CODO M-H', '1\"', 'P0137', NULL, 4, NULL, 1, NULL, 900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(137, 'CAÑERIA ROSCA CODO RED.', '3/4\" X 1/2\"', 'P0138', NULL, 4, NULL, 1, NULL, 1062.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(138, 'CAÑERIA ROSCA CODO RED.', '1\" X 3/4\"', 'P0139', NULL, 4, NULL, 1, NULL, 1410.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(139, 'CAÑERIA ROSCA CODO RED.', '1 X 1/2\"', 'P0140', NULL, 4, NULL, 1, NULL, 1711.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(140, 'CAÑERIA ROSCA CURVA H-H', '1/2\"', 'P0141', NULL, 4, NULL, 1, NULL, 877.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(141, 'CAÑERIA ROSCA CURVA H-H', '3/4\"', 'P0142', NULL, 4, NULL, 1, NULL, 1125.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(142, 'CAÑERIA ROSCA CURVA H-H', '1\"', 'P0143', NULL, 4, NULL, 1, NULL, 2500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(143, 'CAÑERIA ROSCA CURVA M-H', '1/2\"', 'P0144', NULL, 4, NULL, 1, NULL, 1026.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(144, 'CAÑERIA ROSCA CURVA M-H', '3/4\"', 'P0145', NULL, 4, NULL, 1, NULL, 1300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(145, 'CAÑERIA ROSCA CURVA M-H', '1\"', 'P0146', NULL, 4, NULL, 1, NULL, 2600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(146, 'CAÑERIA ROSCA TE H-H', '1/2\"', 'P0147', NULL, 4, NULL, 1, NULL, 515.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(147, 'CAÑERIA ROSCA TE H-H', '3/4\"', 'P0148', NULL, 4, NULL, 1, NULL, 835.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(148, 'CAÑERIA ROSCA TE H-H', '1\"', 'P0149', NULL, 4, NULL, 1, NULL, 1640.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(149, 'CAÑERIA ROSCA TE RED.', '3/4 X 1/2\" H-H', 'P0150', NULL, 4, NULL, 1, NULL, 1132.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(150, 'CAÑERIA ROSCA TE RED.', '1\" X 1/2 H-H', 'P0151', NULL, 4, NULL, 1, NULL, 1890.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(151, 'CAÑERIA ROSCA TE RED.', '1\" X 3/4 H-H', 'P0152', NULL, 4, NULL, 1, NULL, 1260.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(152, 'CAÑO CORRUGADO', 'NARANJA 1\"X25MTS.', 'P0153', NULL, 3, 3, 1, NULL, 11700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(153, 'CAÑO CORRUGADO', 'IGNIFUGO BLANCO 3/4X25MTS', 'P0154', NULL, 3, 3, 1, NULL, 12700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(154, 'CAÑO CORRUGADO', 'IGNIFUGO BLANCO 7/8X25MTS', 'P0155', NULL, 3, 3, 1, NULL, 15307.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(155, 'CAÑO CORRUGADO', 'IGNIFUGO BLANCO 1\"X 25MTS.', 'P0156', NULL, 3, 3, 1, NULL, 18900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(156, 'CAÑO CORRUGADO', 'IGNIFUGO BLANCO 1\"1/4X25MTS.', 'P0157', NULL, 3, 3, 1, NULL, 24830.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(157, 'CAÑO CORRUGADO', 'IGNIFUGO BLANCO 1\"1/2X25MTS', 'P0158', NULL, 3, 3, 1, NULL, 21200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(158, 'CAÑO CORRUGADO', 'PESADO GRIS 1\"', 'P0159', NULL, 3, 3, 1, NULL, 37250.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(159, 'CAÑO CORRUGADO', 'PESADO GRIS 7/8\"', 'P0160', NULL, 3, 3, 1, NULL, 38000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(160, 'CAÑO IGNIFUGO BLANCO', '3MTS X 20MM', 'P0161', NULL, 3, 3, 1, NULL, 4050.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(161, 'CAÑO IGNIFUGO BLANCO', '3MTS X 22MM', 'P0162', NULL, 3, 3, 1, NULL, 5444.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(162, 'CAÑO RIGIDO', '3 MTS X 5/8', 'P0163', NULL, 3, NULL, 1, NULL, 2400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(163, 'CAÑO RIGIDO', '3/4 X 3MTS', 'P0164', NULL, 3, NULL, 1, NULL, 2980.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(164, 'CAÑO RIGIDO', '7/8 X 3MTS', 'P0165', NULL, 3, NULL, 1, NULL, 4360.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(165, 'CAÑO RIGIDO', '1\" X 3MTS', 'P0166', NULL, 3, NULL, 1, NULL, 5200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(166, 'CAPACITOR', '4 UF / 400V C/TERMINAL', 'P0167', NULL, 2, NULL, 1, NULL, 4404.44, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(167, 'CAPACITOR', '12,5 UF / 400V C/TERMINAL', 'P0168', NULL, 2, NULL, 1, NULL, 7479.24, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(168, 'CAPACITOR', '14 UF / 400V C/TERMINAL', 'P0169', NULL, 2, NULL, 1, NULL, 8227.17, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(169, 'CAPACITOR', '16 UF / 400V C/TERMINAL', 'P0170', NULL, 2, NULL, 1, NULL, 10110.83, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(170, 'CAPACITOR', '1,5 UF / 400V C/CABLE TIPO CARAMELO', 'P0171', NULL, 2, NULL, 1, NULL, 3490.31, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(171, 'CAPACITOR', '2,5 UF / 400V C/CABLE TIPO CARAMELO', 'P0172', NULL, 2, NULL, 1, NULL, 3850.42, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(172, 'CAPACITOR', '4 UF / 400V C/CABLE TIPO CARAMELO', 'P0173', NULL, 2, NULL, 1, NULL, 6343.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(173, 'CAPACITOR', '30 UF', 'P0174', NULL, 2, NULL, 1, NULL, 7600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(174, 'CAPACITOR', '35 UF', 'P0175', NULL, 2, NULL, 1, NULL, 8000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(175, 'CAPACITOR', '40 UF', 'P0176', NULL, 2, NULL, 1, NULL, 9200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(176, 'CAPACITOR', '45 UF', 'P0177', NULL, 2, NULL, 1, NULL, 9800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(177, 'CAPACITOR', '50 UF', 'P0178', NULL, 2, NULL, 1, NULL, 10416.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(178, 'CAPACITOR', '60 UF', 'P0179', NULL, 2, NULL, 1, NULL, 11700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(179, 'CAPACITOR ELECTROLITICO', '140-170 220VCA', 'P0180', NULL, 2, NULL, 1, NULL, 11740.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(180, 'CAPACITOR ELECTROLITICO', '190-210 220VCA', 'P0181', NULL, 2, NULL, 1, NULL, 13561.48, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(181, 'CAPACITOR ELECTROLITICO', '240-270 220VCA', 'P0182', NULL, 2, NULL, 1, NULL, 15365.03, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(182, 'CAPACITOR ELECTROLITICO', '270- 310  220VCA', 'P0183', NULL, 2, NULL, 1, NULL, 16100.40, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(183, 'CAPACITOR ELECTROLITICO', '320-360 220VCA', 'P0184', NULL, 2, NULL, 1, NULL, 18746.93, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(184, 'CAPACITOR ELECTROLITICO', '350-400 220VCA', 'P0185', NULL, 2, NULL, 1, NULL, 19522.15, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(185, 'CARRETEL BORDEADORA', 'ABRA-SOL', 'P0186', NULL, 13, NULL, 1, NULL, 4500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(186, 'CERRADURA', 'PRIVE 101', 'P0187', NULL, 8, 2, 1, NULL, 7000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(187, 'CERRADURA', 'PARA PUERTA PLACA', 'P0188', NULL, 8, 2, 1, NULL, 5000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(188, 'CINTA AISLADORA', '15 PLUS TACSA', 'P0189', NULL, 11, 3, 1, NULL, 870.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(189, 'CINTA ENMASCARAR', '24 X 40', 'P0190', NULL, 11, 11, 1, NULL, 5300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(190, 'CINTA MÉTRICA IMP.', '5MS', 'P0191', NULL, 11, NULL, 1, NULL, 3250.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(191, 'CINTA MINISTERIO (VERDE)XMT', '-', 'P0192', NULL, 11, NULL, 1, NULL, 1000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(192, 'CINTA PANAMÁ (AMERICANA) XMT', '-', 'P0193', NULL, 11, NULL, 1, NULL, 750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(193, 'CINTA SILLÓN X MT', '-', 'P0194', NULL, 11, NULL, 1, NULL, 535.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:54'),
(194, 'CLAVOS PUNTA PARIS  1.\"', 'KG', 'P0195', NULL, 6, NULL, 1, NULL, 10385.34, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(195, 'CLAVOS PUNTA PARIS  1.1/2\"', 'KG', 'P0196', NULL, 6, NULL, 1, NULL, 10085.10, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(196, 'CLAVOS PUNTA PARIS  2.\"', 'KG', 'P0197', NULL, 6, NULL, 1, NULL, 9162.24, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(197, 'CLAVOS PUNTA PARIS  2.1/2\"', 'KG', 'P0198', NULL, 6, NULL, 1, NULL, 8726.08, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(198, 'CLAVOS PUNTA PARIS  3.\"', 'KG', 'P0199', NULL, 6, NULL, 1, NULL, 8726.08, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(199, 'CLAVOS PUNTA PARIS  3.1/2\"', 'KG', 'P0200', NULL, 6, NULL, 1, NULL, 8726.08, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(200, 'CLAVOS PUNTA PARIS  4.\"', 'KG', 'P0201', NULL, 6, NULL, 1, NULL, 8726.08, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(201, 'CODO ESPIGA', '1/2\"', 'P0202', NULL, 4, 8, 1, NULL, 243.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(202, 'CODO ESPIGA', '3/4\"', 'P0203', NULL, 4, 8, 1, NULL, 330.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(203, 'CODO ESPIGA', '1\"', 'P0204', NULL, 4, 8, 1, NULL, 516.28, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:12', '2026-02-15 00:23:53'),
(204, 'CODO ESPIGA ROSCA HEMBRA', '1/2\"', 'P0205', NULL, 4, 8, 1, NULL, 359.60, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(205, 'CODO ESPIGA ROSCA HEMBRA', '3/4\"', 'P0206', NULL, 4, 8, 1, NULL, 487.71, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(206, 'CODO ESPIGA ROSCA HEMBRA', '1\"', 'P0207', NULL, 4, 8, 1, NULL, 688.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(207, 'COMANDO VENTILADOR ', ' ABON GARDEN', 'P0208', NULL, 15, NULL, 1, NULL, 12000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(208, 'COMANDO VENTILADOR EVEREST', 'CHICO', 'P0209', NULL, 15, NULL, 1, NULL, 12100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(209, 'COMANDO VENTILADOR EVEREST', 'GRANDE', 'P0210', NULL, 15, NULL, 1, NULL, 16050.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(210, 'CONECTOR CAÑO', '20MM', 'P0211', NULL, 3, 3, 1, NULL, 390.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(211, 'CONECTOR CAÑO', '22MM', 'P0212', NULL, 3, 3, 1, NULL, 535.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(212, 'CONEXIÓN TANQUE', '½\"', 'P0213', NULL, 4, 8, 1, NULL, 3700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(213, 'CONEXIÓN TANQUE', '¾\"', 'P0214', NULL, 4, 8, 1, NULL, 4200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(214, 'CONEXIÓN TANQUE', '1\"', 'P0215', NULL, 4, 8, 1, NULL, 4750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(215, 'CORTA HIERRO BIASSONI HEX.', '350 MM', 'P0216', NULL, 1, 1, 1, NULL, 12625.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(216, 'CORTA HIERRO BIASSONI PLANO', '350 X 35MM', 'P0217', NULL, 1, 1, 1, NULL, 11300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(217, 'CORTINA BAÑO ', 'ALUMINIO BRONCEADO X MTS Ø½ ', 'P0218', NULL, 8, 2, 1, NULL, 2474.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(218, 'CORTINA BAÑO ', 'HIERRO X MT. Ø ½', 'P0219', NULL, 8, 2, 1, NULL, 2000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(219, 'CUPLA', 'Ø40', 'P0220', NULL, NULL, NULL, 1, NULL, 475.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(220, 'CUPLA', 'Ø50', 'P0221', NULL, NULL, NULL, 1, NULL, 506.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(221, 'CUPLA', 'Ø60', 'P0222', NULL, NULL, NULL, 1, NULL, 640.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(222, 'CUPLA', 'Ø100', 'P0223', NULL, NULL, NULL, 1, NULL, 1500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(223, 'CUPLA', 'Ø110', 'P0224', NULL, NULL, NULL, 1, NULL, 1525.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(224, 'CUPLA H - H', '½\"', 'P0225', NULL, 4, 8, 1, NULL, 303.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(225, 'CUPLA H - H', '¾\"', 'P0226', NULL, 4, 8, 1, NULL, 440.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(226, 'CUPLA H - H', '1\"', 'P0227', NULL, 4, 8, 1, NULL, 645.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(227, 'CUPLA H-H C/INSERTO', '½\"', 'P0228', NULL, 4, 8, 1, NULL, 3500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(228, 'CUPLA H-H C/INSERTO', '¾\"', 'P0229', NULL, 4, 8, 1, NULL, 4675.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(229, 'CUPLA RED.', '¾\" X ½\"', 'P0230', NULL, 4, 8, 1, NULL, 456.30, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(230, 'CUPLA RED.', '1\" X ¾\"', 'P0231', NULL, 4, 8, 1, NULL, 815.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(231, 'CUPLA RED. ', '1\" X ½\"', 'P0232', NULL, 4, 8, 1, NULL, 950.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(232, 'CUPLA REDUCCIÓN', 'Ø100 A Ø60', 'P0233', NULL, 6, 8, 1, NULL, 1250.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(233, 'CURVA PVC BLANCO', 'Ø60°-45°', 'P0234', NULL, 6, 8, 1, NULL, 1361.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(234, 'CURVA PVC BLANCO', 'Ø100°-45°', 'P0235', NULL, 6, 8, 1, NULL, 2200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(235, 'CURVA RIGIDA', '5/8', 'P0236', NULL, 3, 3, 1, NULL, 260.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(236, 'CURVA RIGIDA', '3/4', 'P0237', NULL, 3, 3, 1, NULL, 280.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(237, 'CURVA RIGIDA', '7/8', 'P0238', NULL, 3, 3, 1, NULL, 413.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(238, 'CURVA RIGIDA', '1\"', 'P0239', NULL, 3, 3, 1, NULL, 465.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(239, 'CURVAS PARA CAÑO', '20MM', 'P0240', NULL, 3, 3, 1, NULL, 670.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(240, 'CURVAS PARA CAÑO', '22MM', 'P0241', NULL, 3, 3, 1, NULL, 787.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(241, 'DESTORNILLADOR PHILLIPS', 'CR.VA 5 X 125 CROSS.', 'P0242', NULL, 1, 2, 1, NULL, 3100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(242, 'DESTORNILLADOR PLANO', 'CR.VA 5 X 125 CROSS.', 'P0243', NULL, 1, 2, 1, NULL, 300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(243, 'DISCO CORTE ', '115 X 1,0 PLANO', 'P0244', NULL, 13, NULL, 1, NULL, 595.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(244, 'DISCO CORTE ', '180 X 1,2 PLANO', 'P0245', NULL, 13, NULL, 1, NULL, 1617.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(245, 'DISCO FLAP', 'G° 40', 'P0246', NULL, 13, NULL, 1, NULL, 2400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(246, 'DISCO FLAP', 'G°60', 'P0247', NULL, 13, NULL, 1, NULL, 2400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(247, 'ELECTRODO', '2.0MM', 'P0249', NULL, NULL, NULL, 1, NULL, 16400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(248, 'ELECTRODO', '2.5MM', 'P0250', NULL, NULL, NULL, 1, NULL, 12310.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(249, 'ENTREROSCA', '½\"', 'P0251', NULL, 4, 8, 1, NULL, 226.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(250, 'ENTREROSCA', '¾\"', 'P0252', NULL, 4, 8, 1, NULL, 325.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(251, 'ENTREROSCA', '1\"', 'P0253', NULL, 4, 8, 1, NULL, 520.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(252, 'ESCALERA FAMILIAR ', '8 ESCALONES', 'P0254', NULL, NULL, NULL, 1, NULL, 75900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(253, 'ESPIGA 1/2\" ROSCA H RED. ', '3/4\"', 'P0255', NULL, 4, 8, 1, NULL, 514.32, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(254, 'ESPIGA 1/2\" ROSCA M RED.', '3/4\"', 'P0257', NULL, 4, 8, 1, NULL, 273.90, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(255, 'ESPIGA 3/4\" ROSCA H RED.', '1\"', 'P0258', NULL, 4, 8, 1, NULL, 667.03, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(256, 'ESPIGA 3/4\" ROSCA M RED.', '1\"', 'P0259', NULL, 4, 8, 1, NULL, 355.69, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(257, 'ESPIGA 3/4\" ROSCA M RED.', '1/2\"', 'P0260', NULL, 4, 8, 1, NULL, 273.90, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(258, 'ESPIGA DOBLE', '1/2\"', 'P0261', NULL, 4, 8, 1, NULL, 171.44, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(259, 'ESPIGA DOBLE', '3/4\"', 'P0262', NULL, 4, 8, 1, NULL, 225.63, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(260, 'ESPIGA DOBLE', '1\"', 'P0263', NULL, 4, 8, 1, NULL, 335.98, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(261, 'ESPIGA DOBLE RED.', '3/4\" X 1/2\"', 'P0264', NULL, 4, 8, 1, NULL, 291.65, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(262, 'ESPIGA DOBLE RED.', '1\" X 3/4\"', 'P0265', NULL, 4, 8, 1, NULL, 356.67, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(263, 'ESPIGA DOBLE RED.', '1\" X 1/2\"', 'P0266', NULL, 4, 8, 1, NULL, 258.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(264, 'ESPIGA ROSCA HEMBRA', '1/2\"', 'P0267', NULL, 4, 8, 1, NULL, 213.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(265, 'ESPIGA ROSCA HEMBRA', '3/4\"', 'P0268', NULL, 4, 8, 1, NULL, 243.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(266, 'ESPIGA ROSCA HEMBRA', '1\"', 'P0269', NULL, 4, 8, 1, NULL, 360.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(267, 'ESPIGA ROSCA MACHO', '1/2\"', 'P0270', NULL, 4, 8, 1, NULL, 171.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(268, 'ESPIGA ROSCA MACHO', '3/4\"', 'P0271', NULL, 4, 8, 1, NULL, 225.60, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(269, 'ESPIGA ROSCA MACHO', '1\"', 'P0272', NULL, 4, 8, 1, NULL, 335.90, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(270, 'ESPUMA POLIURETANO', '300ML CROSS', 'P0273', NULL, 11, 11, 1, NULL, 6730.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(271, 'FICHA MACHO ', 'ALTO CONSUMO PERNO CHICO', 'P0274', NULL, 3, NULL, 1, NULL, 2800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(272, 'FICHA MACHO/HEMBRA', 'POLARIZADA C/U', 'P0275', NULL, 3, NULL, 1, NULL, 1600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(273, 'GABINETE P/BOMBA FIBRA', NULL, 'P0276', NULL, 6, NULL, 1, NULL, 34000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(274, 'GRAMPA OMEGA PPN', '½\"', 'P0277', NULL, NULL, NULL, 1, NULL, 115.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13');
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `codigo_barra`, `modelo`, `categoria_id`, `marca_id`, `unidad_medida_id`, `proveedor_id`, `precio`, `precio_costo`, `margen_ganancia`, `stock`, `unidad_medida`, `stock_minimo`, `ubicacion_deposito`, `imagen`, `activo`, `deleted_at`, `created_at`, `updated_at`) VALUES
(275, 'GRAMPA OMEGA PPN', '¾\"', 'P0278', NULL, NULL, NULL, 1, NULL, 140.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(276, 'GRAMPA OMEGA PPN', '1\"', 'P0279', NULL, NULL, NULL, 1, NULL, 171.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(277, 'GRAMPA OMEGA PPN', '1 1/4\"', 'P0280', NULL, NULL, NULL, 1, NULL, 568.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(278, 'GRAMPA OMEGA PPN', '1 1/2\"', 'P0281', NULL, NULL, NULL, 1, NULL, 700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(279, 'GRAMPA OMEGA PPN', '2\"', 'P0282', NULL, NULL, NULL, 1, NULL, 770.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(280, 'GRAMPA PVC', 'Ø40', 'P0283', NULL, NULL, NULL, 1, NULL, 780.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(281, 'GRAMPA PVC', 'Ø50', 'P0284', NULL, NULL, NULL, 1, NULL, 1164.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(282, 'GRAMPA PVC', 'Ø60', 'P0285', NULL, NULL, NULL, 1, NULL, 1300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(283, 'GRAMPA PVC', 'Ø100 / 110', 'P0286', NULL, NULL, NULL, 1, NULL, 2062.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(284, 'GRAMPAS CAÑO', 'Ø20MM', 'P0287', NULL, 3, 3, 1, NULL, 275.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(285, 'GRAMPAS CAÑO', 'Ø22MM', 'P0288', NULL, 3, 3, 1, NULL, 350.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(286, 'GRAMPAS OMEGA CHAPA', '5/8', 'P0289', NULL, 3, NULL, 1, NULL, 82.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(287, 'GRAMPAS OMEGA CHAPA', '3/4', 'P0290', NULL, 3, NULL, 1, NULL, 97.80, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(288, 'GRAMPAS OMEGA CHAPA', '7/8', 'P0291', NULL, 3, NULL, 1, NULL, 152.80, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(289, 'GRAMPAS OMEGA CHAPA', '1 \"', 'P0292', NULL, 3, NULL, 1, NULL, 185.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(290, 'GRAMPAS OMEGA CHAPA', '1 1/4 \"', 'P0293', NULL, 3, NULL, 1, NULL, 305.60, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(291, 'GUANTES ALGODÓN PALMA', 'MDT.BCO.REF', 'P0294', NULL, 10, NULL, 1, NULL, 940.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(292, 'GUANTE DESCARNE PUÑO CORTO', 'PAR ', 'P0295', NULL, 10, NULL, 1, NULL, 8000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(293, 'HOJA SIERRA', '18 DIENTES', 'P0297', NULL, 1, 2, 1, NULL, 3700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(294, 'HOJA SIERRA', '24 DIENTES', 'P0298', NULL, 1, 2, 1, NULL, 3700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(295, 'HOJA SIERRA', '36 DIENTES', 'P0299', NULL, 1, 2, 1, NULL, 3400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(296, 'JUEGO 9 LLAVES HEXAGONALES', '1.5 A 10MM LARGAS', 'P0300', NULL, 1, 2, 1, NULL, 13900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(297, 'JUEGO 9 LLAVES HEXAGONALES', '1/16\"  A 3/8\" LARGAS', 'P0301', NULL, 1, 2, 1, NULL, 13900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(298, 'LAMPARA INFRARROJA', '150W', 'P0302', NULL, 14, 9, 1, NULL, 24000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(299, 'LAMPARA LED', '9W-E27', 'P0303', NULL, 14, 9, 1, NULL, 900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(300, 'LAMPARA LED', '12W-E27', 'P0304', NULL, 14, 9, 1, NULL, 1100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(301, 'LAMPARA LED', '18W-E27', 'P0305', NULL, 14, 9, 1, NULL, 2500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(302, 'LAMPARA LED', '20W -E27', 'P0306', NULL, 14, 9, 1, NULL, 2850.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(303, 'LAMPARA LED', 'DICROICA GU1O-5W', 'P0307', NULL, 14, 9, 1, NULL, 1300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(304, 'LAMPARA LED ', '100W-E40', 'P0308', NULL, 14, 9, 1, NULL, 36900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(305, 'LAMPARA LED CON FOTOCELULA', '10W LUZ ', 'P0309', NULL, 14, 9, 1, NULL, 15600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(306, 'LAMPARA LED AUTÓNOMA', '12W', 'P0310', NULL, 14, 9, 1, NULL, 13700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(307, 'LAMPARA LED TUBULAR', '9W', 'P0311', NULL, 14, 9, 1, NULL, 5400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(308, 'LAMPARA LED GOTA FILAMENTO', NULL, 'P0312', NULL, 14, 9, 1, NULL, 3500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(309, 'LAMPARA LED GOTA', '4W', 'P0313', NULL, 14, 9, 1, NULL, 900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(310, 'LAMPARA FILAMENTO ALIC', '20W', 'P0314', NULL, 14, 9, 1, NULL, 7950.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(311, 'LAMPARA LED TUBULAR', '15W', 'P0315', NULL, 14, 9, 1, NULL, 6800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(312, 'LIJA AL AGUA', '-', 'P0316', NULL, 13, 11, 1, NULL, 540.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(313, 'LIJA ANTIEMPASTE', '-', 'P0317', NULL, 13, 11, 1, NULL, 800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(314, 'LIJA ESMERIL', '-', 'P0318', NULL, 13, 11, 1, NULL, 400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(315, 'LIMPIA PILETA S/C', '-', 'P0319', NULL, 9, 12, 1, NULL, 8400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(316, 'LLAVE AJUSTABLE', 'CROSS 8\"- 203MM', 'P0320', NULL, 1, 2, 1, NULL, 18700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(317, 'LLAVE COMBINADA GEDORE', '8MM', 'P0321', NULL, NULL, 5, 1, NULL, 3000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(318, 'LLAVE COMBINADA GEDORE', '10MM', 'P0322', NULL, NULL, 5, 1, NULL, 3450.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(319, 'LLAVE COMBINADA GEDORE', '11MM', 'P0323', NULL, NULL, 5, 1, NULL, 3900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(320, 'LLAVE COMBINADA GEDORE', '12MM', 'P0324', NULL, NULL, 5, 1, NULL, 4100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(321, 'LLAVE COMBINADA GEDORE', '14MM', 'P0325', NULL, NULL, 5, 1, NULL, 4900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(322, 'LLAVE COMBINADA GEDORE', '15MM', 'P0326', NULL, NULL, 5, 1, NULL, 5500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(323, 'LLAVE COMBINADA RHEIN', '8 MM', 'P0327', NULL, NULL, 16, 1, NULL, 3678.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(324, 'LLAVE COMBINADA RHEIN', '9MM', 'P0328', NULL, NULL, 16, 1, NULL, 3800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(325, 'LLAVE COMBINADA RHEIN', '10MM ', 'P0329', NULL, NULL, 16, 1, NULL, 3850.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(326, 'LLAVE COMBINADA RHEIN', '11MM', 'P0330', NULL, NULL, 16, 1, NULL, 4200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(327, 'LLAVE COMBINADA RHEIN', '12MM', 'P0331', NULL, NULL, 16, 1, NULL, 4320.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(328, 'LLAVE COMBINADA RHEIN', '13MM', 'P0332', NULL, NULL, 16, 1, NULL, 4480.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(329, 'LLAVE COMBINADA RHEIN', '14MM', 'P0333', NULL, NULL, 16, 1, NULL, 5200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(330, 'LLAVE DE PASO METÁLICA', '1\"', 'P0336', NULL, 4, 12, 1, NULL, 11200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(331, 'LLAVE DOBLE BOCA GEDORE', '6-7', 'P0337', NULL, NULL, 5, 1, NULL, 5445.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(332, 'LLAVE DOBLE BOCA GEDORE', '8-9', 'P0338', NULL, NULL, 5, 1, NULL, 6400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(333, 'LLAVE DOBLE BOCA GEDORE', '8-10', 'P0339', NULL, NULL, 5, 1, NULL, 6600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(334, 'LLAVE DOBLE BOCA GEDORE', '12-13', 'P0340', NULL, NULL, 5, 1, NULL, 9200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(335, 'LLAVE DOBLE BOCA GEDORE', '13-15', 'P0341', NULL, NULL, 5, 1, NULL, 10100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(336, 'LLAVE DOBLE BOCA GEDORE', '14-15', 'P0342', NULL, NULL, 5, 1, NULL, 11220.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(337, 'LLAVE DOBLE BOCA GEDORE', '16-17', 'P0343', NULL, NULL, 5, 1, NULL, 11800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(338, 'LLAVE TÉRMICA SICA', '3 X 20', 'P0344', NULL, 3, 16, 1, NULL, 18500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(339, 'LLAVE TÉRMICA SICA', '3 X 25', 'P0345', NULL, 3, 16, 1, NULL, 18500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(340, 'LLAVE TÉRMICA SICA', '3 X 63', 'P0346', NULL, 3, 16, 1, NULL, 30200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(341, 'LLAVE TÉRMICA SICA', '4 X 40', 'P0347', NULL, 3, 16, 1, NULL, 31600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(342, 'LLAVE TÉRMICA SICA', '4 X 50', 'P0348', NULL, 3, 16, 1, NULL, 42400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(343, 'LLAVE TÉRMICA SICA', '4 X 63', 'P0349', NULL, 3, 16, 1, NULL, 42400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(344, 'LLAVE TÉRMICA SICA', '4 X 80', 'P0350', NULL, 3, 16, 1, NULL, 140000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(345, 'LLAVE TÉRRICA SICA 1', '1X5', 'P0351', NULL, 3, 16, 1, NULL, 4370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(346, 'LLAVE TÉRRICA SICA 1', '1X10', 'P0352', NULL, 3, 16, 1, NULL, 4370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(347, 'LLAVE TÉRRICA SICA 1', '1X15', 'P0353', NULL, 3, 16, 1, NULL, 4370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(348, 'LLAVE TÉRRICA SICA 1', '1X20', 'P0354', NULL, 3, 16, 1, NULL, 4370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(349, 'LLAVE TÉRRICA SICA 1', '1X25', 'P0355', NULL, 3, 16, 1, NULL, 4370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(350, 'LLAVE TÉRRICA SICA 1', '1X32', 'P0356', NULL, 3, 16, 1, NULL, 4370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(351, 'LLAVE TÉRRICA SICA 1', '1X40', 'P0357', NULL, 3, 16, 1, NULL, 6350.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(352, 'LLAVE TÉRRICA SICA 1', '1X50', 'P0358', NULL, 3, 16, 1, NULL, 10100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(353, 'LLAVE TÉRRICA SICA 1', '1X63', 'P0359', NULL, 3, 16, 1, NULL, 10100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(354, 'LLAVE TÉRRICA SICA 1', '1X80', 'P0360', NULL, 3, 16, 1, NULL, 28500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(355, 'LLAVE TÉRRICA SICA 1', '1X100', 'P0361', NULL, 3, 16, 1, NULL, 28500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(356, 'LLAVE TÉRRICA SICA 2', '2X10', 'P0362', NULL, 3, 16, 1, NULL, 9600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(357, 'LLAVE TÉRRICA SICA 2', '2X20', 'P0363', NULL, 3, 16, 1, NULL, 9600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(358, 'LLAVE TÉRRICA SICA 2', '2X25', 'P0364', NULL, 3, 16, 1, NULL, 9600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(359, 'LLAVE TÉRRICA SICA 2', '2X32', 'P0365', NULL, 3, 16, 1, NULL, 9600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(360, 'LLAVE TÉRRICA SICA 2', '2X40', 'P0366', NULL, 3, 16, 1, NULL, 13700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(361, 'LLAVE TÉRRICA SICA 2', '2X50', 'P0367', NULL, 3, 16, 1, NULL, 19900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(362, 'LLAVE TÉRRICA SICA 2', '2X63', 'P0368', NULL, 3, 16, 1, NULL, 19900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(363, 'LLAVE TÉRRICA SICA 2', '2X80', 'P0369', NULL, 3, 16, 1, NULL, 70100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(364, 'LLAVE TÉRRICA SICA 2', '2X100', 'P0370', NULL, 3, 16, 1, NULL, 70100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(365, 'LLAVE TÉRRICA SICA 3', '3X10', 'P0371', NULL, 3, 16, 1, NULL, 18500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(366, 'LLAVE TÉRRICA SICA 3', '3X15', 'P0372', NULL, 3, 16, 1, NULL, 18500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:53'),
(367, 'MANGUERA REF. P/PILETA', '1 1/4 X MT.', 'P0373', NULL, 9, 14, 1, NULL, 1950.80, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(368, 'MANGUERA REF. P/PILETA', '1 1/2 X MT.', 'P0374', NULL, 9, 14, 1, NULL, 2545.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(369, 'MANGUERA RIEGO', '3/4 TRANSPARENTE X MT', 'P0375', NULL, 9, 14, 1, NULL, 1890.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(370, 'MANGUERA TRICOLOR', '1\" X MT', 'P0376', NULL, 9, 14, 1, NULL, 2000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(371, 'MANIJA BAUL ZINCADA', '80MM', 'P0377', NULL, 8, 17, 1, NULL, 2900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(372, 'MANIJA BAUL ZINCADA', '91MM', 'P0378', NULL, 8, 17, 1, NULL, 3600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(373, 'MANIJA BAUL ZINCADA', '103MM', 'P0379', NULL, 8, 17, 1, NULL, 3900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(374, 'MANIJA BISELADA', 'BRONCE PLATIL', 'P0380', NULL, 8, 17, 1, NULL, 14700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(375, 'MANIJA FALLEBA', 'EMB/EXT', 'P0381', NULL, 8, 17, 1, NULL, 4800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(376, 'MANIJA PULIDA', 'BRONCE MINISTERIO', 'P0382', NULL, 8, 17, 1, NULL, 13400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(377, 'MANIJA SANATORIO', 'BRONCE PULIDO', 'P0383', NULL, 8, 17, 1, NULL, 22000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(378, 'MANIJA SANATORIO', 'PESADA', 'P0384', NULL, 8, 17, 1, NULL, 15000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-15 00:23:54'),
(379, 'MECHA ACERO RÁPIDO', 'Ø1,50', 'P0385', NULL, NULL, NULL, 1, NULL, 1480.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(380, 'MECHA ACERO RÁPIDO', 'Ø1,75', 'P0386', NULL, NULL, NULL, 1, NULL, 1300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(381, 'MECHA ACERO RÁPIDO', 'Ø2', 'P0387', NULL, NULL, NULL, 1, NULL, 1300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(382, 'MECHA ACERO RÁPIDO', 'Ø4,75', 'P0398', NULL, NULL, NULL, 1, NULL, 2600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(383, 'MECHA ACERO RÁPIDO', 'Ø5', 'P0399', NULL, NULL, NULL, 1, NULL, 2750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(384, 'MECHA ACERO RÁPIDO', 'Ø5,25', 'P0400', NULL, NULL, NULL, 1, NULL, 2910.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(385, 'MECHA ACERO RÁPIDO', 'Ø5,50', 'P0401', NULL, NULL, NULL, 1, NULL, 3200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(386, 'MECHA ACERO RÁPIDO', 'Ø5,75', 'P0402', NULL, NULL, NULL, 1, NULL, 3260.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(387, 'MECHA ACERO RÁPIDO', 'Ø6', 'P0403', NULL, NULL, NULL, 1, NULL, 3350.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(388, 'MECHA ACERO RÁPIDO', 'Ø6,25', 'P0404', NULL, NULL, NULL, 1, NULL, 3800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(389, 'MECHA ACERO RÁPIDO', 'Ø6,50', 'P0405', NULL, NULL, NULL, 1, NULL, 3900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(390, 'MECHA ACERO RÁPIDO', 'Ø7,25', 'P0408', NULL, NULL, NULL, 1, NULL, 4980.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(391, 'MECHA ACERO RÁPIDO', 'Ø7,50', 'P0409', NULL, NULL, NULL, 1, NULL, 5200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(392, 'MECHA ACERO RÁPIDO', 'Ø7,75', 'P0410', NULL, NULL, NULL, 1, NULL, 5770.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(393, 'MECHA ACERO RÁPIDO', 'Ø8,25', 'P0412', NULL, NULL, NULL, 1, NULL, 6600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(394, 'MECHA ACERO RÁPIDO', 'Ø8,50', 'P0413', NULL, NULL, NULL, 1, NULL, 6700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(395, 'MECHA ACERO RÁPIDO', 'Ø8,75', 'P0414', NULL, NULL, NULL, 1, NULL, 7200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(396, 'MECHA ACERO RÁPIDO', 'Ø9', 'P0415', NULL, NULL, NULL, 1, NULL, 7600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(397, 'MECHA ACERO RÁPIDO', 'Ø9,25', 'P0416', NULL, NULL, NULL, 1, NULL, 8100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(398, 'MECHA ACERO RÁPIDO', 'Ø9,50', 'P0417', NULL, NULL, NULL, 1, NULL, 8450.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(399, 'MECHA ACERO RÁPIDO', 'Ø9,75', 'P0418', NULL, NULL, NULL, 1, NULL, 9200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(400, 'MECHA ACERO RÁPIDO', 'Ø10', 'P0419', NULL, NULL, NULL, 1, NULL, 9600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(401, 'MECHA ACERO RÁPIDO', 'Ø10,25', 'P0420', NULL, NULL, NULL, 1, NULL, 11400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(402, 'MECHA ACERO RÁPIDO', 'Ø10,50', 'P0421', NULL, NULL, NULL, 1, NULL, 11810.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(403, 'MECHA ACERO RÁPIDO', 'Ø10,75', 'P0422', NULL, NULL, NULL, 1, NULL, 12870.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:13', '2026-02-13 19:35:13'),
(404, 'MECHA ACERO RÁPIDO', 'Ø11', 'P0423', NULL, NULL, NULL, 1, NULL, 13260.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(405, 'MECHA ACERO RÁPIDO', 'Ø11,25', 'P0424', NULL, NULL, NULL, 1, NULL, 14260.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(406, 'MECHA ACERO RÁPIDO', 'Ø11,50', 'P0425', NULL, NULL, NULL, 1, NULL, 14560.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(407, 'MECHA ACERO RÁPIDO', 'Ø11,75', 'P0426', NULL, NULL, NULL, 1, NULL, 15530.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(408, 'MECHA ACERO RÁPIDO', 'Ø12', 'P0427', NULL, NULL, NULL, 1, NULL, 16470.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(409, 'MECHA ACERO RÁPIDO', 'Ø12,25', 'P0428', NULL, NULL, NULL, 1, NULL, 17800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(410, 'MECHA ACERO RÁPIDO', 'Ø12,50', 'P0429', NULL, NULL, NULL, 1, NULL, 18500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(411, 'MECHA ACERO RÁPIDO', 'Ø12,75', 'P0430', NULL, NULL, NULL, 1, NULL, 20300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(412, 'MECHA ACERO RÁPIDO', 'Ø13', 'P0431', NULL, NULL, NULL, 1, NULL, 20900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(413, 'MECHA ACERO RÁPIDO ', 'Ø1', 'P0432', NULL, NULL, NULL, 1, NULL, 1480.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(414, 'MECHA ACERO RÁPIDO ', 'Ø1,25', 'P0433', NULL, NULL, NULL, 1, NULL, 1480.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(415, 'MECHA WIDIA', 'Ø10', 'P0436', NULL, NULL, NULL, 1, NULL, 3750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(416, 'MECHA WIDIA LARGA', 'Ø6 X 200', 'P0437', NULL, NULL, NULL, 1, NULL, 8100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(417, 'MECHA WIDIA LARGA', 'Ø6 X 400', 'P0438', NULL, NULL, NULL, 1, NULL, 12000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(418, 'MECHA WIDIA LARGA', 'Ø14 X 400', 'P0439', NULL, NULL, NULL, 1, NULL, 32850.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(419, 'MEDIA SOMBRA VERDE', '2MTS ANCHO X 1MT', 'P0440', NULL, 9, NULL, 1, NULL, 8750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(420, 'MODULO BIPOLAR', 'JELUZ VERONA', 'P0441', NULL, 3, 17, 1, NULL, 3300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(421, 'MODULO C/U', NULL, 'P0442', NULL, 3, 17, 1, NULL, 670.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(422, 'MODULO CIEGO', 'JELUZ VERONA', 'P0443', NULL, 3, 17, 1, NULL, 140.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(423, 'MODULO CIEGO', 'PLASNAVI', 'P0444', NULL, 3, 17, 1, NULL, 300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(424, 'MODULO COMBINACIÓN', 'JELUZ VERONA', 'P0445', NULL, 3, 17, 1, NULL, 1370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(425, 'MODULO COMBINACIÓN', 'PLASNAVI', 'P0446', NULL, 3, 17, 1, NULL, 2200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(426, 'MODULO PULSADOR', 'JELUZ VERONA', 'P0447', NULL, 3, 17, 1, NULL, 1200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(427, 'MODULO PUNTO', 'JELUZ VERONA', 'P0448', NULL, 3, 17, 1, NULL, 1120.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(428, 'MODULO PUNTO', '2 1/2 - JELUZ VERONA', 'P0449', NULL, 3, 17, 1, NULL, 2370.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(429, 'MODULO PUNTO', 'PLASNAVI', 'P0450', NULL, 3, 17, 1, NULL, 1800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(430, 'MODULO TELEFONO', 'PLASNAVI', 'P0451', NULL, 3, 17, 1, NULL, 5600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(431, 'MODULO TODA TELEFONO', 'JELUZ VERONA', 'P0452', NULL, 3, 17, 1, NULL, 4450.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(432, 'MODULO TOMACORRIENTE', '10 AMP - JELUZ VERONA', 'P0453', NULL, 3, 17, 1, NULL, 1200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(433, 'MODULO TOMACORRIENTE', '20 AMP - JELUZ VERONA', 'P0454', NULL, 3, 17, 1, NULL, 2300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(434, 'MODULO TOMACORRIENTE', 'PLASNAVI', 'P0455', NULL, 3, 17, 1, NULL, 2000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(435, 'MODULO TOMACORRIENTE', '20 AMP - PLASNAVI', 'P0456', NULL, 3, 17, 1, NULL, 3200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(436, 'MODULO VARIADOR VENTILADOR', 'JELUZ VERONA', 'P0457', NULL, 15, 17, 1, NULL, 10100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(437, 'MODULO VENTILADOR VELOC.', 'PLASNAVI', 'P0458', NULL, 15, 17, 1, NULL, 10100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(438, 'NIPLE ', ' ½\" X 6CM', 'P0459', NULL, 4, 8, 1, NULL, 300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(439, 'NIPLE ', '¾\" X 6CM', 'P0460', NULL, 4, 8, 1, NULL, 402.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(440, 'NIPLE ', '¾\" X 10CM', 'P0461', NULL, 4, 8, 1, NULL, 600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(441, 'NIPLE ', '1\" X 6M', 'P0462', NULL, 4, 8, 1, NULL, 615.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(442, 'PALA ANCHA BIASSONI', 'MOD. 992100', 'P0463', NULL, 1, 1, 1, NULL, 65000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(443, 'PALA CORAZÓN BIASSONI', 'MOD.992110', 'P0464', NULL, 1, 1, 1, NULL, 55000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(444, 'PALA PUNTA BIASSONI', 'MOD.992130', 'P0465', NULL, 1, 1, 1, NULL, 65000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(445, 'PINCEL', '1\"', 'P0466', NULL, 5, 16, 1, NULL, 1400.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(446, 'PINCEL', '1 ½\"', 'P0467', NULL, 5, 16, 1, NULL, 2000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(447, 'PINCEL', '2 ½\"', 'P0468', NULL, 5, 16, 1, NULL, 3000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(448, 'PINZA PICO LORO', 'CROSSMASTER 6\"', 'P0469', NULL, 1, 2, 1, NULL, 14500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(449, 'PINZA PUNTA MEDIA CAÑA', 'CROSSMASTER 6½\"', 'P0470', NULL, 1, 2, 1, NULL, 11900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(450, 'PINZA UNIV.', 'CROSSMASTER 7\"', 'P0471', NULL, 1, 2, 1, NULL, 15500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(451, 'PINZA UNIV.', 'CROSSMASTER 8\"', 'P0472', NULL, 1, 2, 1, NULL, 18600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(452, 'PLAFON S17- LED 18W', 'REDONDO', 'P0473', NULL, 14, 9, 1, NULL, 7600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(453, 'PLAFON S17- LED 18W', 'CUADRADO', 'P0474', NULL, 14, 9, 1, NULL, 8050.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(454, 'PORTA CUCHILLAS ', '18 MM ECONO. CROSS', 'P0475', NULL, 8, 2, 1, NULL, 3255.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(455, 'PORTALAMPARA', 'PORCELANA C/GRAMPA \"L\"', 'P0476', NULL, 3, NULL, 1, NULL, 2000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(456, 'PORTALAMPARA', 'PORCELANA C/GRAMPA 3/8', 'P0477', NULL, 3, NULL, 1, NULL, 1966.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(457, 'PORTALAMPARA', 'PVC', 'P0478', NULL, 3, NULL, 1, NULL, 1500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(458, 'PROYECTOR HALOGENO', '150 W', 'P0479', NULL, 14, 9, 1, NULL, 12465.40, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(459, 'PROYECTOR HALOGENO', '500W CON LAMPARA', 'P0480', NULL, 14, 9, 1, NULL, 16537.43, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(460, 'PROYECTOR LED ', '10W LUZ FRÍA', 'P0481', NULL, 14, 9, 1, NULL, 6100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(461, 'PROYECTOR LED ', '20W - SICA', 'P0483', NULL, 14, 9, 1, NULL, 10800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(462, 'PROYECTOR LED ', '100W LUZ FRÍA', 'P0484', NULL, 14, 9, 1, NULL, 27100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(463, 'PROYECTOR LED ', '30W LUZ FRÍA', 'P0485', NULL, 14, 9, 1, NULL, 7600.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(464, 'PROYECTOR LED ', '20W - CAPOBIANCO', 'P0486', NULL, 14, 9, 1, NULL, 12000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(465, 'RAMAL T', 'Ø40', 'P0487', NULL, 6, 8, 1, NULL, 960.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(466, 'RAMAL Y', 'Ø60°-45°', 'P0491', NULL, 6, 8, 1, NULL, 2048.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(467, 'RAMAL Y', 'Ø110°-45°', 'P0492', NULL, 6, 8, 1, NULL, 3700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(468, 'RECEPTACULO', 'CURVO PVC', 'P0493', NULL, 4, NULL, 1, NULL, 3000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(469, 'RECEPTACULO', 'RECTO PVC', 'P0494', NULL, 4, NULL, 1, NULL, 4200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(470, 'RECEPTACULO', 'CURVO PORCELANA', 'P0495', NULL, 4, NULL, 1, NULL, 3540.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(471, 'REGATONES GOMA', 'Ø16MM', 'P0496', NULL, NULL, NULL, 1, NULL, 333.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(472, 'REGATONES GOMA', 'Ø19MM', 'P0497', NULL, NULL, NULL, 1, NULL, 335.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(473, 'REGATONES GOMA', 'Ø22MM', 'P0498', NULL, NULL, NULL, 1, NULL, 348.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(474, 'REGATONES GOMA', 'Ø25MM', 'P0499', NULL, NULL, NULL, 1, NULL, 355.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(475, 'REMACHES RÁPIDOS POP', '3,5 X 10 C/U', 'P0500', NULL, 7, 13, 1, NULL, 18.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(476, 'REMACHES RÁPIDOS POP', '3,5 X 12 C/U', 'P0501', NULL, 7, 13, 1, NULL, 19.80, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(477, 'REMACHES RÁPIDOS POP', '3,5 X 14 C/U', 'P0502', NULL, 7, 13, 1, NULL, 20.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(478, 'REMACHES RÁPIDOS POP', '3,5 X 16 C/U', 'P0503', NULL, 7, 13, 1, NULL, 22.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(479, 'REMACHES RÁPIDOS POP', '4 X 12 C/U', 'P0504', NULL, 7, 13, 1, NULL, 24.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(480, 'REMACHES RÁPIDOS POP', '4 X 16 C/U', 'P0505', NULL, 7, 13, 1, NULL, 26.80, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(481, 'REMACHES RÁPIDOS POP', '4 X 19 C/U', 'P0506', NULL, 7, 13, 1, NULL, 29.30, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(482, 'REMACHES RÁPIDOS POP', '4X 25 C/U', 'P0507', NULL, 7, 13, 1, NULL, 40.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(483, 'REMACHES RÁPIDOS POP', '4,8 X 10  C/U', 'P0508', NULL, 7, 13, 1, NULL, 41.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(484, 'REMACHES RÁPIDOS POP', '4,8 X 12 C/U ', 'P0509', NULL, 7, 13, 1, NULL, 42.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(485, 'REMACHES RÁPIDOS POP', '4,8 X 14 C/U', 'P0510', NULL, 7, 13, 1, NULL, 44.03, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(486, 'REMACHES RÁPIDOS POP', '4,8 X 16 C/U', 'P0511', NULL, 7, 13, 1, NULL, 44.75, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(487, 'REMACHES RÁPIDOS POP', '4,8 X 20 C/U', 'P0512', NULL, 7, 13, 1, NULL, 45.60, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(488, 'REMACHES RÁPIDOS POP', '4,8 X 25 C/U ', 'P0513', NULL, 7, 13, 1, NULL, 46.30, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(489, 'REMACHES RÁPIDOS POP', '5 X 12 C/U ', 'P0514', NULL, 7, 13, 1, NULL, 42.20, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(490, 'REMACHES RÁPIDOS POP', '5 X 14 C/U', 'P0515', NULL, 7, 13, 1, NULL, 60.15, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(491, 'REMACHES RÁPIDOS POP', '5 X 16 C/U', 'P0516', NULL, 7, 13, 1, NULL, 46.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(492, 'RIEL  SOPORTE RAPI-STAND', 'X PAR', 'P0517', NULL, 8, 8, 1, NULL, 22900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(493, 'RODAMIENTO', 'SKF 6201', 'P0518', NULL, NULL, 4, 1, NULL, 5000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(494, 'RODAMIENTO', 'SKF 6203', 'P0519', NULL, NULL, 4, 1, NULL, 6500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(495, 'RODAMIENTO', 'SKF 2Z 6204', 'P0520', NULL, NULL, 4, 1, NULL, 5500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(496, 'RODAMIENTO', 'SKF 6204 2RSH/C3 (BLINDADO)', 'P0521', NULL, NULL, 4, 1, NULL, 8000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(497, 'RODAMIENTO', 'SKF 6205 2RSH', 'P0522', NULL, NULL, 4, 1, NULL, 8500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(498, 'RODAMIENTO', 'SKF 6205 2RS1/C3', 'P0523', NULL, NULL, 4, 1, NULL, 9300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(499, 'RODAMIENTO', 'SKF 6200 2RSH/C3', 'P0524', NULL, NULL, 4, 1, NULL, 12000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(500, 'RODAMIENTO', '609', 'P0525', NULL, NULL, 4, 1, NULL, 7000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(501, 'RODAMIENTO', 'NTN 6200 Z NR', 'P0526', NULL, NULL, 4, 1, NULL, 15000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(502, 'RODAMIENTO', 'NTN 6202 LLUC3/2AS', 'P0527', NULL, NULL, 4, 1, NULL, 9000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(503, 'RODAMIENTO', 'NSK 6200', 'P0528', NULL, NULL, 4, 1, NULL, 4300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(504, 'RODAMIENTO', 'NSK 6201', 'P0529', NULL, NULL, 4, 1, NULL, 4500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(505, 'RODAMIENTO', 'GBC 6204', 'P0530', NULL, NULL, 4, 1, NULL, 6000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(506, 'RODAMIENTO', 'KBS 6002', 'P0531', NULL, NULL, 4, 1, NULL, 3500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(507, 'RODAMIENTO', 'KOYO 629', 'P0532', NULL, NULL, 4, 1, NULL, 5200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(508, 'SELLADOR GRIETA POMO', NULL, 'P0533', NULL, 11, 12, 1, NULL, 9100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(509, 'SELLADOR SILICONA', '280ML', 'P0534', NULL, 11, 12, 1, NULL, 6500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(510, 'SOGA TRENZADA PP', '6MM X MT', 'P0536', NULL, NULL, NULL, 1, NULL, 318.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(511, 'SOGA TRENZADA PP', '8MM X MT', 'P0537', NULL, NULL, NULL, 1, NULL, 590.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(512, 'SOGA TRENZADA PP', '10MM X MT', 'P0538', NULL, NULL, NULL, 1, NULL, 920.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(513, 'SOGA TRENZADA PP', '12MM X MT', 'P0539', NULL, NULL, NULL, 1, NULL, 1360.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(514, 'SOPORTE CORTINA', 'ALUMINIO½ ABIERTO', 'P0540', NULL, 8, 2, 1, NULL, 1160.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(515, 'SOPORTE CORTINA', 'ALUMINIO½ CERRADO', 'P0541', NULL, 8, 2, 1, NULL, 1160.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(516, 'SOPORTE CORTINA', 'HIERRO½ ABIERTO', 'P0542', NULL, 8, 2, 1, NULL, 850.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(517, 'SOPORTE CORTINA', 'HIERRO½ CERRADO', 'P0543', NULL, 8, 2, 1, NULL, 850.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(518, 'SOPORTE CORTINA', '5/8 ABIERTO Y CERRADO', 'P0544', NULL, 8, 2, 1, NULL, 900.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(519, 'SOPORTE ESTANTERÍA', '100 X 125', 'P0545', NULL, 8, 2, 1, NULL, 750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(520, 'SOPORTE ESTANTERÍA', '250 X 300', 'P0546', NULL, 8, 2, 1, NULL, 2315.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(521, 'SOPORTE RAPI-STAND', '17CM X PAR', 'P0547', NULL, 8, 8, 1, NULL, 6800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(522, 'SOPORTE RAPI-STAND', '27 CM X PAR', 'P0548', NULL, 8, 8, 1, NULL, 14000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(523, 'SOPORTE RAPI-STAND', '37 X PAR', 'P0549', NULL, 8, 8, 1, NULL, 17000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(524, 'TAPA BASTIDOR', 'JELUZ VERONA', 'P0550', NULL, 8, 17, 1, NULL, 930.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(525, 'TAPA BASTIDOR', 'PLASNAVI', 'P0551', NULL, 8, 17, 1, NULL, 1000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(526, 'TAPA INODORO', NULL, 'P0552', NULL, 8, NULL, 1, NULL, 16100.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(527, 'TAPA PORTA MODULO EXT.', 'JELUZ EXTERIOR', 'P0553', NULL, 3, 17, 1, NULL, 545.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(528, 'TAPON ROSCA HEMBRA', '½\"', 'P0554', NULL, 4, 8, 1, NULL, 211.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(529, 'TAPON ROSCA HEMBRA', '¾\"', 'P0555', NULL, 4, 8, 1, NULL, 295.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(530, 'TAPON ROSCA HEMBRA', '1\"', 'P0556', NULL, 4, 8, 1, NULL, 550.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(531, 'TAPON ROSCA MACHO', '½\"', 'P0557', NULL, 4, 8, 1, NULL, 242.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(532, 'TAPON ROSCA MACHO', '¾\"', 'P0558', NULL, 4, 8, 1, NULL, 263.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(533, 'TAPON ROSCA MACHO', '1\"', 'P0559', NULL, 4, 8, 1, NULL, 296.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(534, 'TARUGO', 'Ø6', 'P0560', NULL, NULL, NULL, 1, NULL, 14.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(535, 'TARUGO', 'Ø8', 'P0561', NULL, NULL, NULL, 1, NULL, 30.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(536, 'TARUGO', 'Ø10', 'P0562', NULL, NULL, NULL, 1, NULL, 55.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(537, 'TARUGO', 'Ø12', 'P0563', NULL, NULL, NULL, 1, NULL, 127.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(538, 'TARUGO LADO HUECO ', 'Ø6', 'P0564', NULL, NULL, NULL, 1, NULL, 42.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(539, 'TARUGO LADO HUECO ', 'Ø8', 'P0565', NULL, NULL, NULL, 1, NULL, 64.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(540, 'TARUGO LADO HUECO ', 'Ø10', 'P0566', NULL, NULL, NULL, 1, NULL, 115.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-13 19:35:14'),
(541, 'TEE ESPIGA', '1/2\"', 'P0567', NULL, 4, 8, 1, NULL, 635.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(542, 'TEE ESPIGA', '3/4\"', 'P0568', NULL, 4, 8, 1, NULL, 685.76, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(543, 'TEE ESPIGA', '1\"', 'P0569', NULL, 4, 8, 1, NULL, 956.70, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(544, 'TENAZA CARPINTERO', 'PROF.CROSS. 7\"', 'P0570', NULL, 1, 2, 1, NULL, 16630.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:53'),
(545, 'TENDEDERO CALESITA', 'BASE DE CEMENTO', 'P0571', NULL, 9, 2, 1, NULL, 115000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(546, 'TENDEDERO EXTENSIBLE', '7 VARILLAS', 'P0572', NULL, 9, 2, 1, NULL, 37000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(547, 'TORNILLOS CABEZA FRESADA 1/8', '1/8 X 1\"', 'P0574', NULL, 7, 13, 1, NULL, 22.73, 6.10, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(548, 'TORNILLOS CABEZA FRESADA 1/8', '1/8 X 2\"', 'P0575', NULL, 7, 13, 1, NULL, 40.07, 6.10, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(549, 'TORNILLOS CABEZA FRESADA 1/8', '1/8 X 1/2\"', 'P0576', NULL, 7, 13, 1, NULL, 13.82, 6.10, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54');
INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `codigo_barra`, `modelo`, `categoria_id`, `marca_id`, `unidad_medida_id`, `proveedor_id`, `precio`, `precio_costo`, `margen_ganancia`, `stock`, `unidad_medida`, `stock_minimo`, `ubicacion_deposito`, `imagen`, `activo`, `deleted_at`, `created_at`, `updated_at`) VALUES
(550, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 1/4', 'P0577', NULL, 7, 13, 1, NULL, 16.86, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(551, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 1/2', 'P0578', NULL, 7, 13, 1, NULL, 24.31, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(552, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 3/4', 'P0579', NULL, 7, 13, 1, NULL, 32.20, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(553, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 1', 'P0580', NULL, 7, 13, 1, NULL, 39.93, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(554, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 1 1/2', 'P0581', NULL, 7, 13, 1, NULL, 54.16, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(555, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 2', 'P0582', NULL, 7, 13, 1, NULL, 67.68, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(556, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 2 1/2', 'P0583', NULL, 7, 13, 1, NULL, 81.93, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(557, 'TORNILLOS CABEZA FRESADA 5/32', '5/32 X 3', 'P0584', NULL, 7, 13, 1, NULL, 95.00, 7.30, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(558, 'TORNILLOS CABEZA FRESADA 3/16', '3/16 X 1\"', 'P0585', NULL, 7, 13, 1, NULL, 48.23, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(559, 'TORNILLOS CABEZA FRESADA 3/16', '3/16 X 1 1/2', 'P0586', NULL, 7, 13, 1, NULL, 65.61, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(560, 'TORNILLOS CABEZA FRESADA 3/16', '3/16 X 2\"', 'P0587', NULL, 7, 13, 1, NULL, 83.95, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(561, 'TORNILLOS CABEZA FRESADA 3/16', '3/16 X 3\"', 'P0588', NULL, 7, 13, 1, NULL, 117.44, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(562, 'TORNILLOS CABEZA FRESADA 1/4', '1/4 X 1/2\"', 'P0589', NULL, 7, 13, 1, NULL, 54.07, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(563, 'TORNILLOS CABEZA FRESADA 1/4', '1/4 X 3/4', 'P0590', NULL, 7, 13, 1, NULL, 69.90, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(564, 'TORNILLOS CABEZA FRESADA 1/4', '1/4 X 1\"', 'P0591', NULL, 7, 13, 1, NULL, 76.98, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(565, 'TORNILLOS CABEZA FRESADA 1/4', '1/4 X 1 1/2\"', 'P0592', NULL, 7, 13, 1, NULL, 102.35, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(566, 'TORNILLOS CABEZA FRESADA 5/6', '5/6 X 1/2', 'P0593', NULL, 7, 13, 1, NULL, 97.20, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(567, 'TORNILLOS CABEZA FRESADA 5/6', '5/6 X 3/4', 'P0594', NULL, 7, 13, 1, NULL, 108.74, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(568, 'TORNILLOS CABEZA FRESADA 5/6', '5/6 X 1', 'P0595', NULL, 7, 13, 1, NULL, 94.60, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(569, 'TORNILLOS CABEZA REDONDA 1/4', '1/4 X 1/2', 'P0596', NULL, 7, 13, 1, NULL, 54.07, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(570, 'TORNILLOS CABEZA REDONDA 1/4', '1/4 X 3/4', 'P0597', NULL, 7, 13, 1, NULL, 69.60, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(571, 'TORNILLOS CABEZA REDONDA 1/4', '1/4 X 1', 'P0598', NULL, 7, 13, 1, NULL, 76.98, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(572, 'TORNILLOS CABEZA REDONDA 1/4', '1/4 X 1 1/2', 'P0599', NULL, 7, 13, 1, NULL, 102.35, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(573, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 1/2', 'P0600', NULL, 7, 13, 1, NULL, 97.20, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(574, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 3/4', 'P0601', NULL, 7, 13, 1, NULL, 108.74, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(575, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 1', 'P0602', NULL, 7, 13, 1, NULL, 94.60, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(576, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 1 1/4', 'P0603', NULL, 7, 13, 1, NULL, 146.00, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(577, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 1 1/2', 'P0604', NULL, 7, 13, 1, NULL, 170.19, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(578, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 1 3/4 ', 'P0605', NULL, 7, 13, 1, NULL, 187.95, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(579, 'TORNILLOS CABEZA REDONDA 5/16', '5/16 X 2', 'P0606', NULL, 7, 13, 1, NULL, 206.50, 12.80, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(580, 'TORNILLOS CABEZA TANQUE 3/16', ' 3/16 X 1/2\"', 'P0607', NULL, 7, 13, 1, NULL, 29.71, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(581, 'TORNILLOS CABEZA TANQUE 3/16', '3/16 X 1\"', 'P0608', NULL, 7, 13, 1, NULL, 48.23, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(582, 'TORNILLOS CABEZA TANQUE 3/16', '3/16 X 1 1/2\"', 'P0609', NULL, 7, 13, 1, NULL, 65.61, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(583, 'TORNILLOS CABEZA TANQUE 3/16', '3/16 X 2 1/2\"', 'P0610', NULL, 7, 13, 1, NULL, 95.88, 8.60, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(584, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 1/4', 'P0611', NULL, 7, 13, 1, NULL, 36.77, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(585, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 1/2', 'P0612', NULL, 7, 13, 1, NULL, 54.07, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(586, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 3/4', 'P0613', NULL, 7, 13, 1, NULL, 69.60, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(587, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 1', 'P0614', NULL, 7, 13, 1, NULL, 76.98, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(588, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 1 1/4 ', 'P0615', NULL, 7, 13, 1, NULL, 91.20, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(589, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 1 1/2', 'P0616', NULL, 7, 13, 1, NULL, 102.35, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(590, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 2 ', 'P0617', NULL, 7, 13, 1, NULL, 130.91, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(591, 'TORNILLOS CABEZA TANQUE 1/4', '1/4 X 2 1/2\"', 'P0618', NULL, 7, 13, 1, NULL, 161.19, 10.20, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(592, 'TORNILLOS FIX DORADO', '5 X 40 ', 'P0619', NULL, 7, 13, 1, NULL, 31.75, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(593, 'TORNILLOS FIX DORADO', '5 X 50', 'P0620', NULL, 7, 13, 1, NULL, 36.90, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(594, 'TORNILLOS FIX DORADO', '6 X 40', 'P0621', NULL, 7, 13, 1, NULL, 51.76, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(595, 'TORNILLOS FIX DORADO', '6 X 50', 'P0622', NULL, 7, 13, 1, NULL, 58.48, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(596, 'TORNILLOS FIX NEGRO (3,5)', '6X3/4\" MADERA X500-PRF', 'P0623', NULL, 7, 13, 1, NULL, 25.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(597, 'TORNILLOS FIX NEGRO (3,5)', '6X1\" MADERA X500-PRF', 'P0624', NULL, 7, 13, 1, NULL, 29.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(598, 'TORNILLOS FIX NEGRO (3,5)', '6X5/8\" MADERA X500-PRF', 'P0625', NULL, 7, 13, 1, NULL, 30.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(599, 'TORNILLOS FIX NEGRO (3,5)', '6X1\"1/4 MADERA X500-PRF', 'P0626', NULL, 7, 13, 1, NULL, 33.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(600, 'TORNILLOS FIX NEGRO (3,5)', '6X1\"1/2 MADERA X500-PRF', 'P0627', NULL, 7, 13, 1, NULL, 38.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(601, 'TORNILLOS FIX NEGRO (3,5)', '6X2\" MADERA X300-PRF', 'P0629', NULL, 7, 13, 1, NULL, 43.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(602, 'TORNILLOS FIX NEGRO (4,5)', '8X3\" MADERA X300-PRF', 'P0636', NULL, 7, 13, 1, NULL, 50.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(603, 'TORNILLOS INODORO', '22 X 70 MM C/U', 'P0637', NULL, 7, 13, 1, NULL, 1050.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:14', '2026-02-15 00:23:54'),
(604, 'TORNILLOS PARKER 10', '10 X 1/2', 'P0638', NULL, 7, 13, 1, NULL, 30.19, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(605, 'TORNILLOS PARKER 10', '10 X 3/4', 'P0639', NULL, 7, 13, 1, NULL, 37.04, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(606, 'TORNILLOS PARKER 10', '10 X 1', 'P0640', NULL, 7, 13, 1, NULL, 42.34, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(607, 'TORNILLOS PARKER 10', '10 X 1 1/4', 'P0641', NULL, 7, 13, 1, NULL, 55.71, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(608, 'TORNILLOS PARKER 10', '10 X 2 ', 'P0642', NULL, 7, 13, 1, NULL, 77.73, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(609, 'TORNILLOS PARKER 12', '12 X 1/2', 'P0643', NULL, 7, 13, 1, NULL, 38.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(610, 'TORNILLOS PARKER 12', '12 X 3/4', 'P0644', NULL, 7, 13, 1, NULL, 49.97, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(611, 'TORNILLOS PARKER 12', '12 X 1', 'P0645', NULL, 7, 13, 1, NULL, 61.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(612, 'TORNILLOS PARKER 14', '14 X 1/2 ', 'P0646', NULL, 7, 13, 1, NULL, 51.40, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(613, 'TORNILLOS PARKER 14', '14 X 3/4', 'P0647', NULL, 7, 13, 1, NULL, 66.34, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(614, 'TORNILLOS PARKER 14', '14 X 1', 'P0648', NULL, 7, 13, 1, NULL, 78.65, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(615, 'TORNILLOS PARKER 14', '14 X 1 1/2', 'P0649', NULL, 7, 13, 1, NULL, 109.75, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(616, 'TORNILLOS PARKER 4', '4 X 1/4', 'P0650', NULL, 7, 13, 1, NULL, 9.55, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(617, 'TORNILLOS PARKER 4', '4 X 1/2', 'P0651', NULL, 7, 13, 1, NULL, 12.23, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(618, 'TORNILLOS PARKER 4', '4 X 3/4', 'P0652', NULL, 7, 13, 1, NULL, 17.95, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(619, 'TORNILLOS PARKER 4', '4 X  1', 'P0653', NULL, 7, 13, 1, NULL, 22.33, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(620, 'TORNILLOS PARKER 6', '6 X 1/4', 'P0654', NULL, 7, 13, 1, NULL, 13.01, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(621, 'TORNILLOS PARKER 6', '6 X 3/8', 'P0655', NULL, 7, 13, 1, NULL, 16.52, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(622, 'TORNILLOS PARKER 6', '6 X 1/2', 'P0656', NULL, 7, 13, 1, NULL, 16.16, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(623, 'TORNILLOS PARKER 6', '6 X 3/4', 'P0657', NULL, 7, 13, 1, NULL, 21.18, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(624, 'TORNILLOS PARKER 6', '6 X 1 ', 'P0658', NULL, 7, 13, 1, NULL, 26.69, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(625, 'TORNILLOS PARKER 6', '6 X 1 1/2', 'P0659', NULL, 7, 13, 1, NULL, 39.49, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(626, 'TORNILLOS PARKER 7', '7 X 1/2', 'P0660', NULL, 7, 13, 1, NULL, 20.73, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(627, 'TORNILLOS PARKER 7', '7 X 1', 'P0661', NULL, 7, 13, 1, NULL, 31.51, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(628, 'TORNILLOS PARKER 7', '7 X 1 1/4', 'P0662', NULL, 7, 13, 1, NULL, 37.83, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(629, 'TORNILLOS PARKER 7', '7 X 1 1/2', 'P0663', NULL, 7, 13, 1, NULL, 46.50, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(630, 'TORNILLOS PARKER 7', '7 X 3/4', 'P0664', NULL, 7, 13, 1, NULL, 25.81, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(631, 'TORNILLOS PARKER 8', '8 X 1/2', 'P0665', NULL, 7, 13, 1, NULL, 23.21, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(632, 'TORNILLOS PARKER 8', '8 X 3/4', 'P0666', NULL, 7, 13, 1, NULL, 29.10, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(633, 'TORNILLOS PARKER 8', '8 X 1', 'P0667', NULL, 7, 13, 1, NULL, 35.40, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(634, 'TORNILLOS PARKER 8', '8 X 1 1/2', 'P0668', NULL, 7, 13, 1, NULL, 53.62, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(635, 'TORNILLOS PARKER 8', '8 X 2 ', 'P0669', NULL, 7, 13, 1, NULL, 69.47, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(636, 'TUBO 18W LED ', '288 LED 120CR', 'P0670', NULL, 14, 9, 1, NULL, 3870.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(637, 'UNION DOBLE', '½\"', 'P0671', NULL, 4, 8, 1, NULL, 1223.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(638, 'UNION DOBLE', '¾\"', 'P0672', NULL, 4, 8, 1, NULL, 1575.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(639, 'UNION DOBLE', '1\"', 'P0673', NULL, 4, 8, 1, NULL, 3200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(640, 'UNION PARA CAÑO', '20MM', 'P0674', NULL, 4, 14, 1, NULL, 287.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(641, 'UNION PARA CAÑO', '22MM', 'P0675', NULL, 4, 14, 1, NULL, 390.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(642, 'VALVULA DE RETENCIÓN PVC', 'C/CANASTA 3/4', 'P0676', NULL, 4, NULL, 1, NULL, 6000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(643, 'VALVULA ESFÉRICA PVC', '1\"', 'P0679', NULL, 4, NULL, 1, NULL, 6000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(644, 'VALVULA EXCLUSA BRONCE', '1\"', 'P0680', NULL, 4, NULL, 1, NULL, 19800.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(645, 'VALVULA RETENCIÓN BRONCE', '3/4', 'P0681', NULL, 4, NULL, 1, NULL, 16500.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(646, 'VALVULA RETENCIÓN BRONCE', '1\"', 'P0682', NULL, 4, NULL, 1, NULL, 25200.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(647, 'VENTILADOR DE TECHO', 'BENJAMIN- 4P', 'P0683', NULL, 15, NULL, 1, NULL, 90550.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(648, 'VENTILADOR DE TECHO', 'CODINI- 4P', 'P0684', NULL, 15, NULL, 1, NULL, 95870.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(649, 'VENTILADOR DE TECHO', 'EVEREST- 3P', 'P0685', NULL, 15, NULL, 1, NULL, 85300.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(650, 'VENTILADOR DE TECHO', 'SELENE- 4P MADERA C/APLIQUE', 'P0686', NULL, 15, NULL, 1, NULL, 106570.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(651, 'ZAPATILLA CON CABLE', '1.5 MT', 'P0687', NULL, 3, NULL, 1, NULL, 20150.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:53'),
(652, 'ZOCALOS PUERTA ALUMINIO', '60CM', 'P0688', NULL, 8, 2, 1, NULL, 2700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(653, 'ZOCALOS PUERTA ALUMINIO', '70CM', 'P0689', NULL, 8, 2, 1, NULL, 2700.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(654, 'ZOCALOS PUERTA ALUMINIO', '80CM', 'P0690', NULL, 8, 2, 1, NULL, 2860.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(655, 'ZOCALOS PUERTA ALUMINIO', '90CM', 'P0691', NULL, 8, 2, 1, NULL, 3240.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(656, 'ZOCALOS PUERTA ALUMINIO', '100CM', 'P0692', NULL, 8, 2, 1, NULL, 3560.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(657, 'w 40', '155 g', 'P0693', NULL, 8, 16, 1, NULL, 6750.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(658, 'Aceitodo', '90 g', 'P0694', NULL, 11, 16, 1, NULL, 5000.00, 0.00, 30.00, 0.000, 'unid', 0.000, NULL, NULL, 1, NULL, '2026-02-13 19:35:15', '2026-02-15 00:23:54'),
(659, 'prueba de la logica', 'A ver si funca', '2139290', NULL, 5, NULL, 12, NULL, 13000.00, 10000.00, 30.00, 0.000, 'unid', 5.000, NULL, NULL, 1, '2026-02-15 00:30:23', '2026-02-15 00:30:14', '2026-02-15 00:30:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `cuit` varchar(20) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `permisos` text DEFAULT NULL COMMENT 'JSON con permisos del rol',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `permisos`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrador del sistema', '{\"all\": true}', 1, '2026-02-11 23:53:19', '2026-02-11 23:53:19'),
(2, 'vendedor', 'Vendedor - Acceso a ventas y productos', '{\"sales\": true, \"products\": true, \"customers\": true}', 1, '2026-02-11 23:53:19', '2026-02-11 23:53:19'),
(3, 'cajero', 'Cajero - Acceso a caja y reportes', '{\"cash\": true, \"reports\": true, \"sales\": true}', 1, '2026-02-11 23:53:19', '2026-02-11 23:53:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('vfv7mppYsIoIAGGuxXtVnFp7xqzGxuBdLszlBydv', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTEFhaTg3NEhHYVZSRWYyalYxVFZNR0hiZ3ZSQ05JN01WZ3M1d01qaCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Byb2R1Y3RvcyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcHJvZHVjdG9zL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoxNjoicHJvZHVjdG9zLmNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1771202750);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos_caja`
--

CREATE TABLE `turnos_caja` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(100) DEFAULT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_final` decimal(10,2) DEFAULT NULL,
  `monto_esperado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('abierto','cerrado') DEFAULT 'abierto',
  `fecha_apertura` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `notas_apertura` text DEFAULT NULL,
  `notas_cierre` text DEFAULT NULL,
  `cerrado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `turnos_caja`
--

INSERT INTO `turnos_caja` (`id`, `user_id`, `usuario_id`, `usuario_nombre`, `monto_inicial`, `monto_final`, `monto_esperado`, `diferencia`, `estado`, `fecha_apertura`, `fecha_cierre`, `notas_apertura`, `notas_cierre`, `cerrado_por`) VALUES
(3, 1, 1, 'Administrador', 10.00, 11.00, 1275.75, -1264.75, 'cerrado', '2026-02-12 00:16:56', '2026-02-12 21:14:02', '', NULL, NULL),
(4, 1, 1, 'admin', 0.00, NULL, NULL, NULL, 'abierto', '2026-02-12 23:18:31', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `tipo` enum('unidad','longitud','peso','volumen','area','otro') DEFAULT 'unidad',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`id`, `nombre`, `abreviatura`, `tipo`, `activo`) VALUES
(1, 'Unidad', 'ud', 'unidad', 1),
(2, 'Metro', 'm', 'longitud', 1),
(3, 'Centímetro', 'cm', 'longitud', 1),
(4, 'Kilogramo', 'kg', 'peso', 1),
(5, 'Gramo', 'g', 'peso', 1),
(6, 'Litro', 'L', 'volumen', 1),
(7, 'Mililitro', 'ml', 'volumen', 1),
(8, 'Metro cuadrado', 'm²', 'area', 1),
(9, 'Caja', 'caja', 'unidad', 1),
(10, 'Paquete', 'paq', 'unidad', 1),
(11, 'Bolsa', 'bolsa', 'unidad', 1),
(12, 'Rollo', 'rollo', 'unidad', 1),
(13, 'Par', 'par', 'unidad', 1),
(14, 'Juego', 'juego', 'unidad', 1),
(15, 'Set', 'set', 'unidad', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Tiago Raminelli', 'tiagoraminelli@gmail.com', NULL, '$2y$12$HJrQLna8l6fbd0N//psp8O1pvfDf9PSBbLZBhV8egWEpiD/V4FBvK', NULL, '2026-02-16 01:39:21', '2026-02-16 01:39:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rol` enum('admin','vendedor','cajero') NOT NULL DEFAULT 'vendedor',
  `role_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre`, `email`, `rol`, `role_id`, `activo`, `is_active`, `last_login`, `failed_attempts`, `last_failed_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$argon2id$v=19$m=65536,t=4,p=3$OVFDWTJZN1poQWVFSWUzdA$hop7fr/UVRC9kKkzw1zIWlaafOChzNrJQghG9vPiGik', 'Administrador', 'admin@ferreteria.com', 'admin', 1, 1, 1, '2026-02-13 18:50:52', 0, NULL, '2026-02-11 23:49:57', '2026-02-13 18:50:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `descuento_porcentaje` decimal(5,2) DEFAULT 0.00,
  `descuento_monto` decimal(10,2) DEFAULT 0.00,
  `monto_pagado` decimal(10,2) DEFAULT 0.00,
  `cambio` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` enum('efectivo','tarjeta_debito','tarjeta_credito','transferencia','cuenta_corriente','otro') DEFAULT 'efectivo',
  `metodo_pago_secundario` varchar(50) DEFAULT NULL,
  `monto_pago_secundario` decimal(10,2) DEFAULT 0.00,
  `estado` enum('completada','pendiente','cancelada') DEFAULT 'completada',
  `notas` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `usuario_id`, `cliente_id`, `total`, `subtotal`, `descuento_porcentaje`, `descuento_monto`, `monto_pagado`, `cambio`, `metodo_pago`, `metodo_pago_secundario`, `monto_pago_secundario`, `estado`, `notas`, `fecha`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 225.75, 0.00, 0.00, 0.00, 225.75, 0.00, 'efectivo', NULL, 0.00, 'completada', NULL, '2026-02-12 00:26:17', '2026-02-12 00:26:17', '2026-02-12 00:26:17'),
(2, 1, NULL, 1040.00, 0.00, 0.00, 0.00, 1040.00, 0.00, 'efectivo', NULL, 0.00, 'completada', NULL, '2026-02-12 00:31:08', '2026-02-12 00:31:08', '2026-02-12 00:31:08'),
(3, 1, 1, 1040.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'cuenta_corriente', '', 0.00, 'completada', NULL, '2026-02-12 23:35:50', '2026-02-12 23:35:50', '2026-02-12 23:35:50'),
(4, 1, 1, 1040.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'cuenta_corriente', '', 0.00, 'completada', NULL, '2026-02-13 18:53:26', '2026-02-13 18:53:26', '2026-02-13 18:53:26'),
(5, 1, NULL, 520.00, 0.00, 0.00, 0.00, 520.00, 0.00, 'transferencia', 'Nombre: pepe - Tel: 3248324324', 0.00, 'completada', NULL, '2026-02-13 18:58:46', '2026-02-13 18:58:46', '2026-02-13 18:58:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_pendientes`
--

CREATE TABLE `ventas_pendientes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `items` longtext NOT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_costo` decimal(10,2) DEFAULT 0.00,
  `descuento_porcentaje` decimal(5,2) DEFAULT 0.00,
  `descuento_monto` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `subtotal_sin_descuento` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_detalles`
--

INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio`, `precio_costo`, `descuento_porcentaje`, `descuento_monto`, `subtotal`, `subtotal_sin_descuento`) VALUES
(1, 1, 2, 1.500, 150.50, 0.00, 0.00, 0.00, 225.75, 0.00),
(2, 2, 1, 2.000, 520.00, 0.00, 0.00, 0.00, 1040.00, 0.00),
(3, 3, 1, 2.000, 520.00, 0.00, 0.00, 0.00, 1040.00, 0.00),
(4, 4, 1, 2.000, 520.00, 0.00, 0.00, 0.00, 1040.00, 0.00),
(5, 5, 1, 1.000, 520.00, 0.00, 0.00, 0.00, 520.00, 0.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_documento` (`documento`);

--
-- Indices de la tabla `cuenta_corriente_movimientos`
--
ALTER TABLE `cuenta_corriente_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `fk_cc_usuario` (`usuario_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_attempted_at` (`attempted_at`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_turno` (`turno_id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `fk_movimiento_usuario` (`usuario_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_codigo_barra` (`codigo_barra`),
  ADD KEY `idx_categoria` (`categoria_id`),
  ADD KEY `idx_marca` (`marca_id`),
  ADD KEY `idx_proveedor` (`proveedor_id`),
  ADD KEY `idx_stock` (`stock`),
  ADD KEY `idx_deleted` (`deleted_at`),
  ADD KEY `fk_producto_unidad` (`unidad_medida_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_cuit` (`cuit`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_apertura` (`fecha_apertura`),
  ADD KEY `fk_turno_cerrado_por` (`cerrado_por`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `fk_usuario_role` (`role_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_metodo_pago` (`metodo_pago`);

--
-- Indices de la tabla `ventas_pendientes`
--
ALTER TABLE `ventas_pendientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_cliente` (`cliente_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venta` (`venta_id`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cuenta_corriente_movimientos`
--
ALTER TABLE `cuenta_corriente_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=660;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas_pendientes`
--
ALTER TABLE `ventas_pendientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cuenta_corriente_movimientos`
--
ALTER TABLE `cuenta_corriente_movimientos`
  ADD CONSTRAINT `fk_cc_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `fk_movimiento_turno` FOREIGN KEY (`turno_id`) REFERENCES `turnos_caja` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_movimiento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_movimiento_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_producto_marca` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_producto_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_producto_unidad` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD CONSTRAINT `fk_turno_cerrado_por` FOREIGN KEY (`cerrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_turno_usuario` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_venta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas_pendientes`
--
ALTER TABLE `ventas_pendientes`
  ADD CONSTRAINT `fk_pendiente_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pendiente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
