-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2026 a las 04:25:11
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
-- Base de datos: `carreritabd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bancos`
--

CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL,
  `nombre_banco` varchar(50) NOT NULL,
  `prefijo` varchar(4) NOT NULL,
  `estado` varchar(20) DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `bancos`
--

INSERT INTO `bancos` (`id_banco`, `nombre_banco`, `prefijo`, `estado`) VALUES
(1, 'Sistema', '0000', 'inactivo'),
(2, 'Banco Venezuela', '0102', 'activo'),
(3, 'Banco Provincial', '0108', 'inactivo'),
(4, 'Mercantil', '0105', 'inactivo'),
(5, 'Banesco', '0134', 'inactivo'),
(6, 'Banco Nacional de Crédito BNC', '0191', 'inactivo'),
(7, 'Bancamiga', '0172', 'inactivo'),
(8, 'Banco del Tesoro', '0163', 'inactivo'),
(9, 'Banco Bicentenario', '0175', 'inactivo'),
(10, 'Bancaribe', '0114', 'inactivo'),
(11, 'Banco Exterior', '0115', 'inactivo'),
(12, 'Banplus', '0174', 'inactivo'),
(13, 'Banco Plaza', '0138', 'inactivo'),
(14, 'Banco Fondo Común BFC', '0151', 'inactivo'),
(15, 'Banco Activo', '0171', 'inactivo'),
(16, 'Bancrecer', '0168', 'inactivo'),
(17, '100% Banco', '0156', 'inactivo'),
(18, 'DelSur Banco Universal', '0157', 'inactivo'),
(19, 'Banco Caroní', '0128', 'inactivo'),
(20, 'Venezolano de Crédito', '0104', 'inactivo'),
(21, 'Mi Banco', '0169', 'inactivo'),
(22, 'BANFANB', '0177', 'inactivo'),
(23, 'Banco Agrícola de Venezuela', '0166', 'inactivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `choferes`
--

CREATE TABLE `choferes` (
  `id_chofer` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_banco` int(11) DEFAULT NULL,
  `nro_cuenta` varchar(20) NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `choferes`
--

INSERT INTO `choferes` (`id_chofer`, `id_usuario`, `id_banco`, `nro_cuenta`, `saldo`) VALUES
(1, 3, 4, '23254122558866332254', 0.00),
(2, 6, 2, '01024456465465464465', 0.00),
(6, 11, 9, '01754684646546546546', 0.00),
(7, 13, 23, '01664125432455345345', 0.00),
(11, 17, 5, '01344534534534534354', 0.00),
(12, 4, 6, '01915343543345345354', 0.00),
(13, 18, 6, '01918867687678678678', 0.00),
(14, 22, 2, '01020504298787987854', 2854.64);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `id_usuario`, `saldo`) VALUES
(1, 1, 12142.70),
(2, 3, 0.00),
(3, 4, 0.00),
(5, 20, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos_emergencia`
--

CREATE TABLE `contactos_emergencia` (
  `id_contactos` int(11) NOT NULL,
  `id_chofer` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `relacion` enum('Cónyuge/Pareja','Padre/Madre','Hijo/Hija','Familiar','Amigo/Amiga','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `contactos_emergencia`
--

INSERT INTO `contactos_emergencia` (`id_contactos`, `id_chofer`, `nombre`, `telefono`, `relacion`) VALUES
(1, 1, 'AAROM', '04242282256', 'Hijo/Hija'),
(2, 2, 'luis', '04144113131', 'Amigo/Amiga'),
(3, 6, 'sam', '04141313213', 'Familiar'),
(4, 7, 'maria', '04144534534', 'Cónyuge/Pareja'),
(8, 1, 'SAM', '04142142424', 'Amigo/Amiga'),
(9, 2, 'miguel', '04148489456', 'Padre/Madre'),
(10, 6, 'sa', '04143213216', 'Otro'),
(11, 7, 'nerida', '04142131231', 'Familiar'),
(16, 11, 'luis', '04148568564', 'Familiar'),
(17, 11, 'debora', '04145465465', 'Cónyuge/Pareja'),
(18, 12, 'anderson', '04146456456', 'Familiar'),
(19, 12, 'pedro', '04145464564', 'Hijo/Hija'),
(20, 13, 'maria', '04147867867', 'Cónyuge/Pareja'),
(21, 13, 'jose', '04147867867', 'Padre/Madre'),
(22, 14, 'Aurimar Perez', '04129876543', 'Amigo/Amiga'),
(23, 14, 'Oriana Alvarez', '04147899432', 'Hijo/Hija');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_empresa`
--

CREATE TABLE `cuentas_empresa` (
  `id_cuenta` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `numero_cuenta` varchar(16) NOT NULL,
  `identificacion_titular` varchar(15) NOT NULL DEFAULT 'J-12345678-9',
  `nombre_titular` varchar(100) NOT NULL DEFAULT 'Decarrerita C.A.',
  `telefono` varchar(15) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `cuentas_empresa`
--

INSERT INTO `cuentas_empresa` (`id_cuenta`, `id_banco`, `numero_cuenta`, `identificacion_titular`, `nombre_titular`, `telefono`, `estado`) VALUES
(1, 1, '0000000000000000', 'J-12345678-9', 'Decarrerita C.A.', NULL, 'activo'),
(2, 2, '8416852001403520', 'J-12345678-9', 'Decarrerita C.A.', NULL, 'activo'),
(3, 3, '5214685215357582', 'J-12345678-9', 'Decarrerita C.A.', NULL, 'inactivo'),
(4, 7, '4564868464564564', 'J-4546546543', 'Decarrerita C.A', '04169856456', 'activo'),
(5, 10, '5654213654687684', 'J-256834164', 'Decarrerita C.A', '041695662854', 'activo'),
(6, 6, '7484132975287459', 'V-758426849', 'Decarrerita', '04148759852', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_choferes`
--

CREATE TABLE `evaluaciones_choferes` (
  `id_evaluacion` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `id_chofer` int(11) NOT NULL,
  `nota_psicologica` decimal(5,2) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','aprobado','reprobado') NOT NULL,
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones_choferes`
--

INSERT INTO `evaluaciones_choferes` (`id_evaluacion`, `id_personal`, `id_chofer`, `nota_psicologica`, `fecha`, `estado`, `observacion`) VALUES
(1, 5, 1, 80.00, '2026-07-15 19:11:07', 'aprobado', NULL),
(2, 5, 2, 10.00, '2026-07-16 17:25:36', 'reprobado', NULL),
(3, 5, 2, 50.00, '2026-07-16 17:27:11', 'reprobado', NULL),
(4, 5, 6, 80.00, '2026-07-16 19:01:53', 'aprobado', NULL),
(5, 5, 7, 60.00, '2026-07-16 19:08:22', 'reprobado', NULL),
(7, 5, 11, 70.00, '2026-07-17 11:37:19', 'aprobado', NULL),
(8, 5, 12, 60.00, '2026-07-17 11:40:00', 'reprobado', NULL),
(9, 5, 13, 85.00, '2026-07-17 11:55:22', 'aprobado', NULL),
(10, 5, 2, 100.00, '2026-07-18 20:09:37', 'aprobado', NULL),
(11, 5, 7, 20.00, '2026-07-22 12:07:05', 'reprobado', NULL),
(12, 5, 14, 65.00, '2026-07-24 02:07:41', 'reprobado', NULL),
(13, 5, 14, 73.00, '2026-07-24 02:13:43', 'aprobado', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_vehiculos`
--

CREATE TABLE `evaluaciones_vehiculos` (
  `id_evaluacion` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `nota_tecnica` decimal(5,2) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','apto','no_apto') NOT NULL,
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones_vehiculos`
--

INSERT INTO `evaluaciones_vehiculos` (`id_evaluacion`, `id_personal`, `id_vehiculo`, `nota_tecnica`, `fecha`, `estado`, `observacion`) VALUES
(1, 5, 1, 80.00, '2026-07-15 19:11:55', 'apto', 'bien'),
(2, 5, 2, 50.00, '2026-07-15 19:12:07', 'no_apto', 'regular'),
(3, 5, 3, 100.00, '2026-07-17 14:10:06', 'apto', ''),
(4, 5, 4, 90.00, '2026-07-22 11:27:36', 'apto', 'Sin opinion'),
(5, 5, 5, 20.00, '2026-07-22 12:20:29', 'no_apto', 'Datos no coinciden e incorrectos'),
(6, 5, 6, 73.00, '2026-07-24 02:08:49', 'apto', 'Bien'),
(7, 5, 7, 64.00, '2026-07-24 02:14:27', 'no_apto', 'Detalles');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_chofer`
--

CREATE TABLE `pago_chofer` (
  `id_pago` int(11) NOT NULL,
  `id_chofer` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `id_banco` int(11) NOT NULL,
  `numero_cuenta` varchar(30) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nro_ref` varchar(100) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','finalizado') DEFAULT 'pendiente',
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `pago_chofer`
--

INSERT INTO `pago_chofer` (`id_pago`, `id_chofer`, `id_personal`, `id_banco`, `numero_cuenta`, `monto`, `nro_ref`, `fecha`, `estado`, `detalles`) VALUES
(1, 1, 5, 4, '23254122558866332254', 2000.00, '265256', '2026-07-22 11:20:13', 'finalizado', 'Transferencia a Mercantil (Cta: 23254122558866332254)'),
(3, 2, 5, 2, '01024456465465464465', 2500.00, '865651', '2026-07-22 11:42:34', 'finalizado', 'Transferencia a Banco Venezuela (Cta: 01024456465465464465)'),
(4, 2, 5, 2, '01024456465465464465', 100.00, '654654', '2026-07-22 11:42:42', 'finalizado', 'Nada'),
(5, 2, 5, 2, '01024456465465464465', 2500.00, '646546', '2026-07-22 11:45:44', 'finalizado', 'Transferencia a Banco Venezuela (Cta: 01024456465465464465)'),
(6, 2, 5, 2, '01024456465465464465', 200.00, '852147', '2026-07-24 00:00:00', 'finalizado', 'Liquidación procesada. Ref: 852147'),
(7, 2, 5, 2, '01024456465465464465', 100.00, '753285', '2026-07-24 00:00:00', 'finalizado', 'Liquidación procesada. Ref: 753285'),
(8, 2, 5, 2, '01024456465465464465', 9930.44, '986528', '2026-07-24 00:00:00', 'finalizado', 'Liquidación procesada. Ref: 986528'),
(9, 1, 5, 4, '23254122558866332254', 7385.71, '552876', '2026-07-24 00:00:00', 'finalizado', 'Liquidación procesada. Ref: 552876');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recargas`
--

CREATE TABLE `recargas` (
  `id_recarga` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nro_ref` varchar(100) NOT NULL,
  `fecha_pago` date NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `recargas`
--

INSERT INTO `recargas` (`id_recarga`, `id_cliente`, `id_banco`, `monto`, `nro_ref`, `fecha_pago`, `fecha_registro`) VALUES
(1, 1, 3, 15000.00, 'REC-125425', '2026-07-15', '2026-07-22 00:00:00'),
(2, 1, 1, 15000.00, 'REC-545435', '2026-07-16', '2026-07-22 00:00:00'),
(3, 1, 1, 1000.00, 'REC-254534', '2026-07-16', '2026-07-22 00:00:00'),
(4, 1, 1, 1000.00, 'REC-453543', '2026-07-16', '2026-07-22 00:00:00'),
(5, 1, 1, 1000.00, 'REC-635478', '2026-07-16', '2026-07-22 00:00:00'),
(6, 1, 1, 1000.00, 'REC-565435', '2026-07-16', '2026-07-22 00:00:00'),
(7, 1, 3, 1000.00, 'REC-415635', '2026-07-16', '2026-07-22 00:00:00'),
(8, 1, 2, 3000.00, 'REC-868767', '2026-07-17', '2026-07-22 00:00:00'),
(16, 1, 4, 5000.00, 'REC-463546', '2026-07-22', '2026-07-22 00:00:00'),
(22, 1, 10, 1000.00, 'REC-598592', '2026-07-22', '2026-07-22 00:00:00'),
(23, 1, 7, 3000.00, 'REC-156165', '2026-07-22', '2026-07-22 00:00:00'),
(24, 1, 7, 1000.00, 'REC-416512', '2026-07-22', '2026-07-22 00:00:00'),
(25, 1, 2, 1500.00, 'REC-586484', '2026-07-22', '2026-07-22 00:00:00'),
(26, 1, 10, 5000.00, 'REC-545435', '2026-07-22', '2026-07-22 00:00:00'),
(27, 1, 7, 1000.00, 'REC-435434', '2026-07-22', '2026-07-22 13:08:59'),
(28, 1, 6, 10000.00, 'REC-000000', '2026-07-24', '2026-07-24 03:49:27'),
(29, 1, 10, 5000.00, 'REC-852369', '2026-07-24', '2026-07-24 09:21:40'),
(30, 1, 2, 1000.00, 'REC-741852', '2026-07-24', '2026-07-24 09:22:05'),
(31, 1, 6, 1000.00, 'REC-358219', '2026-07-24', '2026-07-24 09:26:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_asignados`
--

CREATE TABLE `roles_asignados` (
  `id_rol` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_rol` enum('admin','cliente','chofer','personal') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `roles_asignados`
--

INSERT INTO `roles_asignados` (`id_rol`, `id_usuario`, `tipo_rol`) VALUES
(1, 1, 'cliente'),
(2, 2, 'admin'),
(3, 3, 'cliente'),
(4, 4, 'cliente'),
(5, 3, 'chofer'),
(6, 5, 'personal'),
(7, 6, 'chofer'),
(8, 11, 'chofer'),
(10, 13, 'chofer'),
(12, 13, 'cliente'),
(13, 17, 'chofer'),
(14, 4, 'chofer'),
(15, 18, 'chofer'),
(17, 20, 'cliente'),
(18, 21, 'admin'),
(19, 22, 'chofer');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traslados`
--

CREATE TABLE `traslados` (
  `id_traslado` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_chofer` int(11) NOT NULL,
  `id_vehiculo` int(11) DEFAULT NULL,
  `id_zona_origen` int(11) NOT NULL,
  `id_zona_destino` int(11) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','en_curso','finalizado','cancelado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `traslados`
--

INSERT INTO `traslados` (`id_traslado`, `id_cliente`, `id_chofer`, `id_vehiculo`, `id_zona_origen`, `id_zona_destino`, `costo`, `fecha`, `estado`) VALUES
(1, 1, 2, 4, 23, 22, 2795.12, '2026-07-24 03:20:54', ''),
(2, 1, 1, 1, 14, 18, 2315.09, '2026-07-24 09:27:18', ''),
(3, 1, 14, 6, 14, 6, 4078.06, '2026-07-24 19:03:37', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `correo` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombres` varchar(45) NOT NULL,
  `apellidos` varchar(45) NOT NULL,
  `cedula` varchar(15) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `estado` enum('activo','bloqueado') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `correo`, `password`, `nombres`, `apellidos`, `cedula`, `telefono`, `estado`) VALUES
(1, 'aaromarmando232@gmail.com', '$2y$10$5YuDYzVGyY.GZ9bfRObRu.zutUBG.fIo7i7KFWLXP5eDU/4k4KTF6', 'aarom armando', 'luces bolivar', '28162993', '04242282256', 'activo'),
(2, 'admin@gmail.com', '$2y$10$5YuDYzVGyY.GZ9bfRObRu.zutUBG.fIo7i7KFWLXP5eDU/4k4KTF6', 'Luis Miguel', 'Perez Lopez', '25684235', '', 'activo'),
(3, 'juancarlos1235@gmail.com', '$2y$10$m4kOEzgiAWfezs4/0D1Br.22EPOBMQnQG/zRdt/KZIK8hHj54KYx.', 'juan carlos', 'perez garcia', '25683215', '04125656516', 'activo'),
(4, 'miguelsamuel@gmail.com', '$2y$10$6KP4H6uwsAeUhiZ0S6cmge7Xafuggp/4aNmyWiM.EBF2fgJ4kao6G', 'miguel samuel', 'lugo hernandez', '25836952', '04144127566', 'activo'),
(5, 'personallaura@gmail.com', '$2y$10$VXU5mk9lxJ7iwTlFJ22HCeyqz.Bm4OrM.Pw/MooZoN0fHBuYzqL9W', 'Laura Gabriela', 'Perez Angelica', '8845629', '04162586256', 'activo'),
(6, 'libelisyemes@gmail.com', '$2y$10$5RebXPcyuBeIJNNZGdAiIO292n3K4GLAZWDs20fI4xW3JxEI3J2T6', 'Lisbelis', 'yemes', '28653154', '04141532696', 'activo'),
(11, 'manue@gmail.com', '$2y$10$LlIXZa5WxahSRkETAjtSmua9x4zGtd9uhhlBxiSNe3ZF2yEpwodAK', 'manuekl', 'rivas', '51651651', '04141653232', 'activo'),
(13, 'choferprueba1@gmail.com', '$2y$10$n1wWb4ca35RGHP9DOSKd4upcJ4Q1wVVdzvlaFxI/5fRYnGCWvsCZi', 'chofer', 'chofer', '8523161', '04146168416', 'activo'),
(17, 'romero@gmail.com', '$2y$10$iCjfSY3vf/3o1ec9SSDtj.1Fs9W/miDQVo6PlkQLc5JhdZU4.FXsy', 'jose', 'romero', 'V-22665165', '04146185165', 'activo'),
(18, 'yani@gmail.com', '$2y$10$a7VqSR4ItkC6gy9HKAi8A.HlzDAcNBvK.YqxKnUH1IWM9ZEgKaOJ6', 'yani', 'lopez', 'V-65455145', '04143121323', 'activo'),
(20, 'anagarcia123@gmail.com', '$2y$10$VCa.oYTdeWDEwkubSAzUQ.MXBmguHvnVfTxYvjq3MVue6a9Mo.VhO', 'Ana', 'Garcia', 'V-9951984', '04147268456', 'activo'),
(21, 'anabelopez@gmail.com', '$2y$10$ySm4MShQX8ECvB7owp4VCOC44Msjv/Fn/UkJaUFzSVFoILw4CH6FS', 'Anabel', 'Lopez', '20586951', '0424857412', 'activo'),
(22, 'angieurrieta@gmail.com', '$2y$10$jZnamzf9w4EDKlQhwBsGfu0csJbAvNec5Uyk4B1nmoRuKkN0RsRyG', 'Angie', 'Urrieta', 'V-31538385', '04149582568', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `marca` varchar(45) NOT NULL,
  `modelo` varchar(45) NOT NULL,
  `anio` int(11) NOT NULL,
  `id_chofer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `placa`, `marca`, `modelo`, `anio`, `id_chofer`) VALUES
(1, 'aa111bbb', 'toyota', 'yaris', 2006, 1),
(2, 'aa12vc', 'honda', 'ranger', 2005, 1),
(3, 'DD52bc', 'hyundai', 'sonic', 2006, 2),
(4, '11552ab', 'Toyota ', 'honda', 2006, 2),
(5, 'BCD523ee', 'toyota', 'honda', 2006, 7),
(6, 'AHA2424', 'Toyota', 'Hilux', 2015, 14),
(7, 'A43523', 'Chevrolet', 'Spark', 2008, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas`
--

CREATE TABLE `zonas` (
  `id_zona` int(11) NOT NULL,
  `nombre_zona` varchar(100) NOT NULL,
  `coord_x` decimal(10,2) NOT NULL,
  `coord_y` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `zonas`
--

INSERT INTO `zonas` (`id_zona`, `nombre_zona`, `coord_x`, `coord_y`) VALUES
(1, 'Las Amazonas', 8.22, -62.83),
(2, 'Las Teodokildas', 8.23, -62.83),
(3, 'Gran Sabana', 8.24, -62.82),
(4, 'Villa Betania', 8.25, -62.81),
(5, 'Villa Bahía', 8.24, -62.79),
(6, 'Curagua', 8.27, -62.78),
(7, 'Santa Rosa', 8.25, -62.77),
(8, 'Lomas del Caroní', 8.26, -62.77),
(9, 'Río Yocoima', 8.28, -62.75),
(10, 'Unare 2', 8.28, -62.76),
(11, 'Mini Fincas', 8.26, -62.73),
(12, 'Villa Africana', 8.28, -62.72),
(13, 'Los Olivos', 8.28, -62.72),
(14, 'Alta Vista', 8.29, -62.74),
(15, 'Chilemex', 8.30, -62.72),
(16, 'Guaiparo', 8.34, -62.69),
(17, 'La Llovizna', 8.33, -62.67),
(18, 'Unare 1', 8.28, -62.75),
(19, 'Terrazas del Caroní', 8.26, -62.74),
(20, 'Río Aro', 8.27, -62.75),
(21, 'Castillito', 8.32, -62.71),
(22, 'El Roble', 8.35, -62.67),
(23, 'Centro de San Félix', 8.37, -62.66),
(24, 'Las Batallas', 8.37, -62.65),
(25, 'Vista Al Sol', 8.35, -62.62),
(26, 'Barrio Brisas del Sur', 8.33, -62.64),
(27, '11 de Abril', 8.36, -62.62),
(28, 'Inés Romero', 8.37, -62.61),
(29, 'Chirica Vieja', 8.32, -62.63),
(30, 'Francisca Duarte', 8.31, -62.63),
(31, 'Primero de Mayo', 8.36, -62.64);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bancos`
--
ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id_banco`);

--
-- Indices de la tabla `choferes`
--
ALTER TABLE `choferes`
  ADD PRIMARY KEY (`id_chofer`),
  ADD KEY `fk_choferes_usuarios1_idx` (`id_usuario`),
  ADD KEY `fk_choferes_bancos1` (`id_banco`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `fk_clientes_usuarios1_idx` (`id_usuario`);

--
-- Indices de la tabla `contactos_emergencia`
--
ALTER TABLE `contactos_emergencia`
  ADD PRIMARY KEY (`id_contactos`),
  ADD KEY `fk_choferes_contactos` (`id_chofer`);

--
-- Indices de la tabla `cuentas_empresa`
--
ALTER TABLE `cuentas_empresa`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD KEY `id_banco` (`id_banco`);

--
-- Indices de la tabla `evaluaciones_choferes`
--
ALTER TABLE `evaluaciones_choferes`
  ADD PRIMARY KEY (`id_evaluacion`),
  ADD KEY `fk_evaluaciones_choferes1_idx` (`id_chofer`),
  ADD KEY `fk_evaluaciones_usuarios1_idx` (`id_personal`);

--
-- Indices de la tabla `evaluaciones_vehiculos`
--
ALTER TABLE `evaluaciones_vehiculos`
  ADD PRIMARY KEY (`id_evaluacion`),
  ADD KEY `fk_evaluaciones_vehiculos1_idx` (`id_vehiculo`),
  ADD KEY `fk_evaluaciones_usuarios1_idx` (`id_personal`);

--
-- Indices de la tabla `pago_chofer`
--
ALTER TABLE `pago_chofer`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `fk_pago_chofer` (`id_chofer`),
  ADD KEY `fk_pago_personal` (`id_personal`),
  ADD KEY `fk_pago_banco` (`id_banco`);

--
-- Indices de la tabla `recargas`
--
ALTER TABLE `recargas`
  ADD PRIMARY KEY (`id_recarga`),
  ADD KEY `fk_recarga_cliente` (`id_cliente`),
  ADD KEY `fk_recarga_banco` (`id_banco`);

--
-- Indices de la tabla `roles_asignados`
--
ALTER TABLE `roles_asignados`
  ADD PRIMARY KEY (`id_rol`),
  ADD KEY `fk_roles_asignados_usuarios1_idx` (`id_usuario`);

--
-- Indices de la tabla `traslados`
--
ALTER TABLE `traslados`
  ADD PRIMARY KEY (`id_traslado`),
  ADD KEY `fk_traslados_choferes1_idx` (`id_chofer`),
  ADD KEY `fk_traslados_clientes1_idx` (`id_cliente`),
  ADD KEY `fk_traslados_zonas1_idx` (`id_zona_origen`),
  ADD KEY `fk_traslados_zonas2_idx` (`id_zona_destino`),
  ADD KEY `fk_traslados_vehiculos1_idx` (`id_vehiculo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo_UNIQUE` (`correo`),
  ADD UNIQUE KEY `cedula_UNIQUE` (`cedula`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id_vehiculo`),
  ADD UNIQUE KEY `placa_UNIQUE` (`placa`),
  ADD KEY `fk_carros_choferes_idx` (`id_chofer`);

--
-- Indices de la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD PRIMARY KEY (`id_zona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bancos`
--
ALTER TABLE `bancos`
  MODIFY `id_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `choferes`
--
ALTER TABLE `choferes`
  MODIFY `id_chofer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `contactos_emergencia`
--
ALTER TABLE `contactos_emergencia`
  MODIFY `id_contactos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `cuentas_empresa`
--
ALTER TABLE `cuentas_empresa`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `evaluaciones_choferes`
--
ALTER TABLE `evaluaciones_choferes`
  MODIFY `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `evaluaciones_vehiculos`
--
ALTER TABLE `evaluaciones_vehiculos`
  MODIFY `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pago_chofer`
--
ALTER TABLE `pago_chofer`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `recargas`
--
ALTER TABLE `recargas`
  MODIFY `id_recarga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `roles_asignados`
--
ALTER TABLE `roles_asignados`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `traslados`
--
ALTER TABLE `traslados`
  MODIFY `id_traslado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `zonas`
--
ALTER TABLE `zonas`
  MODIFY `id_zona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `choferes`
--
ALTER TABLE `choferes`
  ADD CONSTRAINT `fk_choferes_bancos1` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_choferes_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `contactos_emergencia`
--
ALTER TABLE `contactos_emergencia`
  ADD CONSTRAINT `fk_choferes_contactos` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`);

--
-- Filtros para la tabla `cuentas_empresa`
--
ALTER TABLE `cuentas_empresa`
  ADD CONSTRAINT `cuentas_empresa_ibfk_1` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `evaluaciones_choferes`
--
ALTER TABLE `evaluaciones_choferes`
  ADD CONSTRAINT `fk_evaluaciones_choferes1` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_evaluaciones_usuarios1` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `evaluaciones_vehiculos`
--
ALTER TABLE `evaluaciones_vehiculos`
  ADD CONSTRAINT `fk_evaluaciones_usuarios10` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_evaluaciones_vehiculos10` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pago_chofer`
--
ALTER TABLE `pago_chofer`
  ADD CONSTRAINT `fk_pago_banco` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pago_chofer` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pago_personal` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recargas`
--
ALTER TABLE `recargas`
  ADD CONSTRAINT `fk_recarga_banco` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recarga_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `roles_asignados`
--
ALTER TABLE `roles_asignados`
  ADD CONSTRAINT `fk_roles_asignados_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `traslados`
--
ALTER TABLE `traslados`
  ADD CONSTRAINT `fk_traslados_choferes1` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_traslados_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_traslados_vehiculos1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_traslados_zonas1` FOREIGN KEY (`id_zona_origen`) REFERENCES `zonas` (`id_zona`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_traslados_zonas2` FOREIGN KEY (`id_zona_destino`) REFERENCES `zonas` (`id_zona`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `fk_carros_choferes` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
