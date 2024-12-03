-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-12-2024 a las 20:11:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_zapatos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contraseña`
--

CREATE TABLE `contraseña` (
  `id` int(11) NOT NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contraseña`
--

INSERT INTO `contraseña` (`id`, `clave`) VALUES
(1, '12345'),
(2, '12345');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_credito`
--

CREATE TABLE `detalles_credito` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `numero_tarjeta` varchar(20) DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_credito`
--

INSERT INTO `detalles_credito` (`id`, `id_venta`, `numero_tarjeta`, `fecha_caducidad`, `cvv`) VALUES
(1, 4, '78898967452312', '0000-00-00', '123'),
(2, 5, '78898967452312', '0000-00-00', '123'),
(3, 6, '43434343344343', '0000-00-00', '123'),
(4, 7, '8976564332123456', '0000-00-00', '123'),
(5, 8, '8976564332123456', '0000-00-00', '123'),
(6, 9, '7867564523123456', '0000-00-00', '123'),
(7, 10, '3434343434343434343', '0000-00-00', '345'),
(8, 11, '222222222222222222', '0000-00-00', '345'),
(9, 12, '44444444444444444', '0000-00-00', '353'),
(10, 13, '2985483282384832', '0000-00-00', '234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `contacto` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `contacto`, `direccion`, `correo`, `rfc`) VALUES
(6, 'zapato feliz', '4921710593', 'Fidel Velazques 44', 'klausdepaepe@gmail.com', 'OICS010516000'),
(8, 'sketchers', '4981292482', 'colonia Roma, 56, CDMX', 'zapatofeliz@gmail.com', 'SK456YTY67'),
(12, 'zapato triste', '4921714575', 'colonia Roma, 56, CDMX', 'zapatotriste@outlook.com', 'SK456YTY67');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `tipo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `password_hash`, `password`, `username`, `tipo`) VALUES
(22, 'sergio', NULL, 'dHmjzSHjhFQEHUsfnVimlw==', 'dHmjzSHjhFQEHUsfnVimlw==', 'administrador'),
(24, 'Juan', NULL, 'lbkZIjs/lK5q0gjNZtRWlA==', 'lbkZIjs/lK5q0gjNZtRWlA==', 'vendedor'),
(25, 'A', NULL, 'h4pypEqXOjZLl8UHSCZmRw==', 'h4pypEqXOjZLl8UHSCZmRw==', 'vendedor'),
(26, 'E', NULL, 'TQmCES097zW3kuBigHS7fA==', 'TQmCES097zW3kuBigHS7fA==', 'vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `cliente_nombre` varchar(255) DEFAULT NULL,
  `cliente_email` varchar(255) DEFAULT NULL,
  `tipo_pago` enum('contado','credito') DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `codigo_zapato` varchar(255) NOT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `fecha_venta` date NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `cliente_nombre`, `cliente_email`, `tipo_pago`, `total`, `id_usuario`, `codigo_zapato`, `nombre_cliente`, `fecha_venta`, `precio`, `cantidad`) VALUES
(1, '2024-11-07', 'Sergio Castañeda', 'veeronicazoe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-07', 1200.00, 1),
(2, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-07', 1200.00, 1),
(3, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-07', 1200.00, 1),
(4, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, NULL, 'ZP001', '', '0000-00-00', 0.00, 1),
(5, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, NULL, 'ZP001', '', '0000-00-00', 0.00, 1),
(6, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, NULL, 'ZP001', '', '0000-00-00', 0.00, 1),
(7, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-07', 1200.00, 1),
(8, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-07', 1200.00, 1),
(9, '2024-11-07', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-07', 1200.00, 1),
(10, '2024-11-08', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP009', 'Sergio Castañeda', '2024-11-08', 1200.00, 1),
(11, '2024-11-10', 'dddddddddddddd', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'dddddddddddddd', '2024-11-10', 1200.00, 1),
(12, '2024-11-10', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, 'ZP001', 'Sergio Castañeda', '2024-11-10', 1200.00, 1),
(13, '2024-11-21', 'Juan Rosales', 'usuario123@gmail.com', 'credito', 950.00, 1, 'ZP003', 'Juan Rosales', '2024-11-21', 950.00, 1),
(17, '2024-11-25', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 2400.00, 1, '0', 'Sergio Castañeda', '2024-11-25', 1200.00, 1),
(18, '2024-11-25', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 2400.00, 1, '0', 'Sergio Castañeda', '2024-11-25', 1200.00, 1),
(19, '2024-11-25', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'Sergio Castañeda', '2024-11-25', 1200.00, 1),
(20, '2024-11-25', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'Sergio Castañeda', '2024-11-25', 1200.00, 1),
(25, '2024-11-27', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 3050.00, 22, '', '', '0000-00-00', 0.00, 1),
(26, '2024-11-27', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 4250.00, 1, '0', 'Sergio Castañeda', '2024-11-27', 950.00, 1),
(27, '2024-11-27', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 4250.00, 1, '0', 'Sergio Castañeda', '2024-11-27', 1150.00, 1),
(28, '2024-11-27', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 4250.00, 1, '0', 'Sergio Castañeda', '2024-11-27', 950.00, 1),
(29, '2024-11-27', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 4250.00, 1, '0', 'Sergio Castañeda', '2024-11-27', 1200.00, 1),
(30, '2024-11-27', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'Sergio Castañeda', '2024-11-27', 1200.00, 1),
(31, '2024-11-28', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 950.00, 1, '0', 'Sergio Castañeda', '2024-11-28', 950.00, 1),
(32, '2024-11-28', 'Manuel', 'klausdepaepe@gmail.com', 'contado', 2250.00, 1, '0', 'Manuel', '2024-11-28', 1200.00, 1),
(33, '2024-11-28', 'Manuel', 'klausdepaepe@gmail.com', 'contado', 2250.00, 1, '0', 'Manuel', '2024-11-28', 1050.00, 1),
(34, '2024-11-28', 'Segio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'Segio Castañeda', '2024-11-28', 1200.00, 1),
(35, '2024-11-28', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'Sergio Castañeda', '2024-11-28', 1200.00, 1),
(36, '2024-11-28', 'Sergio Castañeda', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'Sergio Castañeda', '2024-11-28', 1200.00, 1),
(37, '2024-11-28', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'ZapaTecNM', '2024-11-28', 1200.00, 1),
(38, '2024-11-28', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'credito', 1200.00, 1, '0', 'ZapaTecNM', '2024-11-28', 1200.00, 1),
(39, '2024-11-28', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'contado', 950.00, 1, '0', 'ZapaTecNM', '2024-11-28', 950.00, 1),
(40, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'credito', 12500.00, 1, '0', 'ZapaTecNM', '2024-12-03', 11300.00, 1),
(41, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'credito', 12500.00, 1, '0', 'ZapaTecNM', '2024-12-03', 1200.00, 1),
(42, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'contado', 13550.00, 1, '0', 'ZapaTecNM', '2024-12-03', 11300.00, 1),
(43, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'contado', 13550.00, 1, '0', 'ZapaTecNM', '2024-12-03', 1200.00, 1),
(44, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'contado', 13550.00, 1, '0', 'ZapaTecNM', '2024-12-03', 1050.00, 1),
(45, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', 'contado', 1200.00, 1, '0', 'ZapaTecNM', '2024-12-03', 1200.00, 1),
(46, '2024-12-03', 'ZapaTecNM', 'klausdepaepe@gmail.com', '', 1200.00, 1, 'ZP001', '', '0000-00-00', 1200.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zapatos`
--

CREATE TABLE `zapatos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zapatos`
--

INSERT INTO `zapatos` (`id`, `codigo`, `marca`, `color`, `modelo`, `precio`) VALUES
(1, 'ZP001', 'Nike', 'Negro', 'Air Max', 1200.00),
(2, 'ZP002', 'Adidas', 'Blanco', 'Ultra Boost', 1400.00),
(3, 'ZP003', 'Puma', 'Rojo', 'Suede', 950.00),
(4, 'ZP004', 'Reebok', 'Azul', 'Classic', 1100.00),
(5, 'ZP005', 'Under Armour', 'Negro', 'HOVR', 1050.00),
(6, 'ZP006', 'Vans', 'Gris', 'Old Skool', 900.00),
(7, 'ZP007', 'New Balance', 'Blanco', '574', 1150.00),
(8, 'ZP008', 'Converse', 'Negro', 'All Star', 750.00),
(9, 'ZP009', 'Asics', 'Azul', 'Gel', 1200.00),
(10, 'ZP010', 'Fila', 'Rojo', 'Disruptor', 950.00),
(11, 'ZP011', 'GUCCI', 'Blanco', 'Bee', 11300.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zapatos_proveedores`
--

CREATE TABLE `zapatos_proveedores` (
  `id_zapato` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contraseña`
--
ALTER TABLE `contraseña`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalles_credito`
--
ALTER TABLE `detalles_credito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_ibfk_1` (`id_usuario`);

--
-- Indices de la tabla `zapatos`
--
ALTER TABLE `zapatos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `zapatos_proveedores`
--
ALTER TABLE `zapatos_proveedores`
  ADD PRIMARY KEY (`id_zapato`,`id_proveedor`),
  ADD KEY `id_proveedor` (`id_proveedor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contraseña`
--
ALTER TABLE `contraseña`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `detalles_credito`
--
ALTER TABLE `detalles_credito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `zapatos`
--
ALTER TABLE `zapatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_credito`
--
ALTER TABLE `detalles_credito`
  ADD CONSTRAINT `detalles_credito_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`);

--
-- Filtros para la tabla `zapatos_proveedores`
--
ALTER TABLE `zapatos_proveedores`
  ADD CONSTRAINT `zapatos_proveedores_ibfk_1` FOREIGN KEY (`id_zapato`) REFERENCES `zapatos` (`id`),
  ADD CONSTRAINT `zapatos_proveedores_ibfk_2` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
