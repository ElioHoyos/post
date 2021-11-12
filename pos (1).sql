-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-11-2021 a las 02:09:57
-- Versión del servidor: 10.1.34-MariaDB
-- Versión de PHP: 7.0.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `categoria` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `categoria`, `fecha`) VALUES
(1, 'CAMARAS DE SEGURIDAD', '2021-11-10 14:30:29'),
(2, 'ACCESORIOS CIRCUITOS CERRADOS', '2021-11-10 14:32:53'),
(3, 'KIT ARRASTRE', '2021-11-10 14:34:09'),
(4, 'ACCESORIOS DE COMPUTO', '2021-11-10 14:35:16'),
(5, 'MODEMS', '2021-11-10 14:41:00'),
(6, 'ROUTERS', '2021-11-10 14:41:16'),
(7, 'SWITCHS', '2021-11-10 14:43:12'),
(8, 'DVRs', '2021-11-10 14:47:06'),
(9, 'UTILES DE LIMPIEZA', '2021-11-10 19:56:58'),
(10, 'ARTICULOS DE SALUD', '2021-11-10 22:17:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `documento` text COLLATE utf8_spanish_ci NOT NULL,
  `email` text COLLATE utf8_spanish_ci NOT NULL,
  `telefono` text COLLATE utf8_spanish_ci NOT NULL,
  `direccion` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `compras` int(11) NOT NULL,
  `ultima_compra` datetime NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `codigo` text COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `imagen` text COLLATE utf8_spanish_ci NOT NULL,
  `stock` int(11) NOT NULL,
  `precioMayor` float NOT NULL,
  `precio_compra` float NOT NULL,
  `precio_venta` float NOT NULL,
  `ventas` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `id_categoria`, `codigo`, `descripcion`, `imagen`, `stock`, `precioMayor`, `precio_compra`, `precio_venta`, `ventas`, `estado`, `fecha`) VALUES
(1, 1, '101', 'Camara de seguridad THD DS2CE76H0TITMF 5MP', 'vistas/img/productos/101/545.jpg', 3, 0, 90, 150, 3, 0, '2021-11-08 22:45:43'),
(4, 1, '102', 'HikVision DS 2CE56D0T   IRPF 1080P', 'vistas/img/productos/default/anonymous.png', 1, 0, 67.9, 95.06, 0, 0, '2021-11-10 15:13:16'),
(5, 1, '103', 'HikVision DS 2CE16D0T  IRPF  1080P', 'vistas/img/productos/default/anonymous.png', 3, 0, 75, 105, 0, 0, '2021-11-10 15:11:41'),
(6, 1, '104', 'HikVision H 265 PLUS DS 2CD1053G0  I HD 5MP', 'vistas/img/productos/default/anonymous.png', 1, 0, 100, 140, 0, 0, '2021-11-10 15:24:07'),
(7, 6, '601', 'TP LINK TL WR940N 450Mbps', 'vistas/img/productos/default/anonymous.png', 3, 0, 89.3, 125.02, 0, 0, '2021-11-10 15:27:51'),
(8, 2, '201', 'FUENTE AC00 1 2010 12V 1A', 'vistas/img/productos/default/anonymous.png', 6, 0, 8.6, 12.04, 0, 0, '2021-11-10 16:16:16'),
(9, 2, '202', 'FUENTE AC00 2 2106 12V 2A', 'vistas/img/productos/default/anonymous.png', 2, 0, 8.6, 12.04, 0, 0, '2021-11-10 16:23:31'),
(10, 7, '701', 'TP LINK TL SG1008D ', 'vistas/img/productos/default/anonymous.png', 1, 0, 60, 84, 0, 0, '2021-11-10 16:48:00'),
(11, 7, '702', 'TP LINK TL SF1008D  8 PUERTOS', 'vistas/img/productos/default/anonymous.png', 1, 0, 40, 56, 0, 0, '2021-11-10 16:50:47'),
(12, 4, '401', 'CAMARA WEB TEROS TE 9070', 'vistas/img/productos/default/anonymous.png', 3, 0, 71.5, 100.1, 0, 0, '2021-11-10 16:54:16'),
(13, 4, '402', 'TP LINK BLUETOOTH  NANO USB ADAPTER 4 0', 'vistas/img/productos/default/anonymous.png', 1, 0, 46.45, 65.03, 0, 0, '2021-11-10 17:04:56'),
(14, 2, '203', 'SPLIT JOIN HD VIDEO BALUN 4K', 'vistas/img/productos/default/anonymous.png', 3, 0, 15, 21, 0, 0, '2021-11-10 17:20:31'),
(15, 2, '204', 'SPLIT JOIN HD VIDEO BALUN AHD', 'vistas/img/productos/default/anonymous.png', 10, 0, 10, 14, 0, 0, '2021-11-10 17:23:41'),
(16, 4, '403', 'SSD 240 GB KINGSTON', 'vistas/img/productos/default/anonymous.png', 1, 0, 140, 196, 0, 0, '2021-11-10 17:32:34'),
(17, 4, '404', 'SSD 480 GB KINGSTON', 'vistas/img/productos/default/anonymous.png', 1, 0, 221.45, 310.03, 0, 0, '2021-11-10 17:39:38'),
(18, 4, '405', 'SSD 256 GB TEAMGROUP', 'vistas/img/productos/default/anonymous.png', 1, 0, 150, 210, 0, 0, '2021-11-10 17:43:30'),
(19, 4, '406', 'SSD 512 GB TEAM GROUP', 'vistas/img/productos/default/anonymous.png', 3, 0, 225, 315, 0, 0, '2021-11-10 17:46:08'),
(20, 1, '105', 'TP LINK TAPO C 100  WI FI CAMARA', 'vistas/img/productos/default/anonymous.png', 1, 0, 105, 147, 0, 0, '2021-11-10 17:52:15'),
(23, 9, '901', 'PAPEL HIGIENICO 40 MTRS (1PAQUETE 4 UND)', 'vistas/img/productos/default/anonymous.png', 1, 0, 0, 0, 0, 0, '2021-11-10 21:09:45'),
(24, 9, '902', 'JABON LIQUIDO FAMILY DOCTOR', 'vistas/img/productos/default/anonymous.png', 63, 0, 0, 0, 0, 0, '2021-11-10 21:49:54'),
(25, 9, '903', 'LEJIA 4LT SAPOLIO (GALON)', 'vistas/img/productos/default/anonymous.png', 1, 0, 0, 0, 0, 0, '2021-11-10 21:51:44'),
(26, 9, '904', 'JABON LIQUIDO 1LT NEXT', 'vistas/img/productos/default/anonymous.png', 1, 0, 0, 0, 0, 0, '2021-11-10 21:52:49'),
(27, 9, '905', 'LAVAVAJILLA LIQUIDO 1250ml (SAPOLIO)', 'vistas/img/productos/default/anonymous.png', 6, 0, 0, 0, 0, 0, '2021-11-10 21:55:54'),
(28, 9, '906', 'FIBRA VERDE (LAVA PLATOS)', 'vistas/img/productos/default/anonymous.png', 35, 0, 0, 0, 0, 0, '2021-11-10 21:57:26'),
(29, 9, '907', 'ESPONJA MULTIUSO 2 EN 1', 'vistas/img/productos/default/anonymous.png', 6, 0, 0, 0, 0, 0, '2021-11-10 21:58:24'),
(30, 9, '908', 'PAÑO ABSORVENTE AZUL', 'vistas/img/productos/default/anonymous.png', 11, 0, 0, 0, 0, 0, '2021-11-10 21:59:27'),
(31, 9, '909', 'PAÑO ABSORVENTE AMARILLO', 'vistas/img/productos/default/anonymous.png', 13, 0, 0, 0, 0, 0, '2021-11-10 22:00:07'),
(32, 9, '910', 'PAÑO ABSORVENTE ROSADO', 'vistas/img/productos/default/anonymous.png', 4, 0, 0, 0, 0, 0, '2021-11-10 22:01:43'),
(33, 9, '911', 'PAÑO ABSORVENTE SCOTCH BRITE (PAQUETE 8UND)', 'vistas/img/productos/default/anonymous.png', 2, 0, 0, 0, 0, 0, '2021-11-10 22:03:09'),
(34, 9, '912', 'PAÑO ABSORVENTE KLEINE (PAQUETE 8UND)', 'vistas/img/productos/default/anonymous.png', 2, 0, 0, 0, 0, 0, '2021-11-10 22:04:30'),
(35, 9, '913', 'PAÑO ABSORVENTE BOREAL (PAQUETE 6UND)', 'vistas/img/productos/default/anonymous.png', 8, 0, 0, 0, 0, 0, '2021-11-10 22:05:45'),
(36, 9, '914', 'GUANTE VIRUTEX TALLA (M)', 'vistas/img/productos/default/anonymous.png', 4, 0, 0, 0, 0, 0, '2021-11-10 22:07:19'),
(37, 9, '915', 'ATOMIZADOR DE 1LT AZUL', 'vistas/img/productos/default/anonymous.png', 2, 0, 0, 0, 0, 0, '2021-11-10 22:09:27'),
(38, 9, '916', 'ATOMIZADOR DE 1LT AMARILLO', 'vistas/img/productos/default/anonymous.png', 3, 0, 0, 0, 0, 0, '2021-11-10 22:10:06'),
(39, 9, '917', 'ATOMIZADOR DE 1LT ROJO', 'vistas/img/productos/default/anonymous.png', 2, 0, 0, 0, 0, 0, '2021-11-10 22:10:34'),
(40, 9, '918', 'ATOMIZADOR DE 1LT BLANCO', 'vistas/img/productos/default/anonymous.png', 3, 0, 0, 0, 0, 0, '2021-11-10 22:11:11'),
(41, 9, '919', 'ATOMIZADOR DE 500ml AZUL', 'vistas/img/productos/default/anonymous.png', 0, 0, 0, 0, 0, 0, '2021-11-10 22:13:19'),
(42, 9, '920', 'ATOMIZADOR DE 500ml AMARILLO', 'vistas/img/productos/default/anonymous.png', 0, 0, 0, 0, 0, 0, '2021-11-10 22:13:45'),
(43, 9, '921', 'ATOMIZADOR DE 500ml ROJO', 'vistas/img/productos/default/anonymous.png', 0, 0, 0, 0, 0, 0, '2021-11-10 22:15:04'),
(44, 9, '922', 'ATOMIZADOR (CHISQUETE)', 'vistas/img/productos/default/anonymous.png', 23, 0, 0, 0, 0, 0, '2021-11-10 22:15:52'),
(45, 10, '1001', 'MASCARILLAS QUIRURJICAS PARA NIÑOS (PAQUETE 50UND)', 'vistas/img/productos/default/anonymous.png', 59, 0, 7, 9.8, 0, 0, '2021-11-10 22:27:38'),
(46, 10, '1002', 'MASCARILLAS QUIRURJICAS PARA ADULTOS', 'vistas/img/productos/default/anonymous.png', 19, 0, 0, 0, 0, 0, '2021-11-10 22:31:13'),
(47, 10, '1003', 'ALCOHOL EN GEL 1TR BEAUTY', 'vistas/img/productos/default/anonymous.png', 1, 0, 0, 0, 0, 0, '2021-11-10 22:32:59'),
(48, 10, '1004', 'ALCOHOL MEDICINAL 70º 1LT SCIENTIFIC', 'vistas/img/productos/default/anonymous.png', 5, 0, 0, 0, 0, 0, '2021-11-10 22:34:23'),
(49, 10, '1005', 'ALCOHOL MEDICINAL 500ml MATFARMA', 'vistas/img/productos/default/anonymous.png', 6, 0, 0, 0, 0, 0, '2021-11-10 22:35:49'),
(50, 9, '923', 'PAPEL TOALLA NOVA', 'vistas/img/productos/default/anonymous.png', 29, 0, 0, 0, 0, 0, '2021-11-10 22:38:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` text COLLATE utf8_spanish_ci NOT NULL,
  `usuario` text COLLATE utf8_spanish_ci NOT NULL,
  `password` text COLLATE utf8_spanish_ci NOT NULL,
  `perfil` text COLLATE utf8_spanish_ci NOT NULL,
  `foto` text COLLATE utf8_spanish_ci NOT NULL,
  `estado` int(11) NOT NULL,
  `ultimo_login` datetime NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `perfil`, `foto`, `estado`, `ultimo_login`, `fecha`) VALUES
(66, 'Elio', '76439016', '$2a$07$asxx54ahjppf45sd87a5auY/vF3TQR/zjk4LJlEb2mL1fkG6nhecS', 'Administrador', 'vistas/img/usuarios/76439016/286.jpg', 1, '2021-11-11 18:17:31', '2021-11-11 23:17:31'),
(67, 'JACK OJIDE MASIADO', '08666123', '$2a$07$asxx54ahjppf45sd87a5aumUskocpQucMnvwsUt.aC6WLWGcLNcY6', '', '', 1, '2021-11-11 14:24:02', '2021-11-11 22:28:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `productos` text COLLATE utf8_spanish_ci NOT NULL,
  `impuesto` float NOT NULL,
  `neto` float NOT NULL,
  `total` float NOT NULL,
  `metodo_pago` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
