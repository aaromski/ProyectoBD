CREATE DATABASE  IF NOT EXISTS `carreritabd` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `carreritabd`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: carreritabd
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_banco` varchar(50) NOT NULL,
  `prefijo` varchar(4) NOT NULL,
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'Sistema','0000'),(2,'Banco Venezuela','0102'),(3,'Banco Provincial','0108'),(4,'Mercantil','0105'),(5,'Banesco','0134'),(6,'Banco Nacional de Crédito BNC','0191'),(7,'Bancamiga','0172'),(8,'Banco del Tesoro','0163'),(9,'Banco Bicentenario','0175'),(10,'Bancaribe','0114'),(11,'Banco Exterior','0115'),(12,'Banplus','0174'),(13,'Banco Plaza','0138'),(14,'Banco Fondo Común BFC','0151'),(15,'Banco Activo','0171'),(16,'Bancrecer','0168'),(17,'100% Banco','0156'),(18,'DelSur Banco Universal','0157'),(19,'Banco Caroní','0128'),(20,'Venezolano de Crédito','0104'),(21,'Mi Banco','0169'),(22,'BANFANB','0177'),(23,'Banco Agrícola de Venezuela','0166');
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `choferes`
--

DROP TABLE IF EXISTS `choferes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `choferes` (
  `id_chofer` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_banco` int(11) DEFAULT NULL,
  `nro_cuenta` varchar(20) NOT NULL,
  `nombre_contacto1` varchar(45) DEFAULT NULL,
  `contacto1` varchar(15) DEFAULT NULL,
  `nombre_contacto2` varchar(45) DEFAULT NULL,
  `contacto2` varchar(15) DEFAULT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_chofer`),
  KEY `fk_choferes_usuarios1_idx` (`id_usuario`),
  KEY `fk_choferes_bancos1` (`id_banco`),
  CONSTRAINT `fk_choferes_bancos1` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_choferes_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `choferes`
--

LOCK TABLES `choferes` WRITE;
/*!40000 ALTER TABLE `choferes` DISABLE KEYS */;
INSERT INTO `choferes` VALUES (1,3,4,'23254122558866332254','AAROM','04242282256','SAM','04142142424',440.31),(2,6,2,'01024456465465464465','luis','04144113131','miguel','04148489456',0.00),(6,11,9,'01754684646546546546','sam','04141313213','sa','04143213216',0.00),(7,13,23,'01664125432455345345','maria','04144534534','nerida','04142131231',0.00),(8,12,12,'01745431430450453045','sami','04144242138','samantha','04147864564',0.00);
/*!40000 ALTER TABLE `choferes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_cliente`),
  KEY `fk_clientes_usuarios1_idx` (`id_usuario`),
  CONSTRAINT `fk_clientes_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,1,21469.48),(2,3,0.00),(3,4,0.00),(4,12,0.00);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_empresa`
--

DROP TABLE IF EXISTS `cuentas_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuentas_empresa` (
  `id_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `id_banco` int(11) NOT NULL,
  `numero_cuenta` varchar(16) NOT NULL,
  `identificacion_titular` varchar(15) NOT NULL DEFAULT 'J-12345678-9',
  `nombre_titular` varchar(100) NOT NULL DEFAULT 'Decarrerita C.A.',
  `telefono` varchar(15) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id_cuenta`),
  KEY `id_banco` (`id_banco`),
  CONSTRAINT `cuentas_empresa_ibfk_1` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_empresa`
--

LOCK TABLES `cuentas_empresa` WRITE;
/*!40000 ALTER TABLE `cuentas_empresa` DISABLE KEYS */;
INSERT INTO `cuentas_empresa` VALUES (1,1,'0000000000000000','J-12345678-9','Decarrerita C.A.',NULL,'activo'),(2,2,'8416852001403520','J-12345678-9','Decarrerita C.A.',NULL,'activo'),(3,3,'5214685215357582','J-12345678-9','Decarrerita C.A.',NULL,'activo'),(4,7,'4564868464564564','J-4546546543','Decarrerita C.A','04169856456','activo'),(5,10,'5654213654687684','J-256834164','Decarrerita C.A','041695662854','inactivo');
/*!40000 ALTER TABLE `cuentas_empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluaciones_choferes`
--

DROP TABLE IF EXISTS `evaluaciones_choferes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluaciones_choferes` (
  `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) DEFAULT NULL,
  `id_chofer` int(11) NOT NULL,
  `nota_psicologica` decimal(5,2) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','aprobado','reprobado') NOT NULL,
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `fk_evaluaciones_choferes1_idx` (`id_chofer`),
  KEY `fk_evaluaciones_usuarios1_idx` (`id_personal`),
  CONSTRAINT `fk_evaluaciones_choferes1` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_evaluaciones_usuarios1` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_choferes`
--

LOCK TABLES `evaluaciones_choferes` WRITE;
/*!40000 ALTER TABLE `evaluaciones_choferes` DISABLE KEYS */;
INSERT INTO `evaluaciones_choferes` VALUES (1,5,1,80.00,'2026-07-15 19:11:07','aprobado',NULL),(2,5,2,10.00,'2026-07-16 17:25:36','reprobado',NULL),(3,5,2,50.00,'2026-07-16 17:27:11','reprobado',NULL),(4,NULL,6,NULL,'2026-07-16 19:01:53','pendiente',NULL),(5,NULL,7,NULL,'2026-07-16 19:08:22','pendiente',NULL),(6,NULL,8,NULL,'2026-07-16 19:09:25','pendiente',NULL);
/*!40000 ALTER TABLE `evaluaciones_choferes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluaciones_vehiculos`
--

DROP TABLE IF EXISTS `evaluaciones_vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluaciones_vehiculos` (
  `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) DEFAULT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `nota_tecnica` decimal(5,2) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','apto','no_apto') NOT NULL,
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `fk_evaluaciones_vehiculos1_idx` (`id_vehiculo`),
  KEY `fk_evaluaciones_usuarios1_idx` (`id_personal`),
  CONSTRAINT `fk_evaluaciones_usuarios10` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_evaluaciones_vehiculos10` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_vehiculos`
--

LOCK TABLES `evaluaciones_vehiculos` WRITE;
/*!40000 ALTER TABLE `evaluaciones_vehiculos` DISABLE KEYS */;
INSERT INTO `evaluaciones_vehiculos` VALUES (1,5,1,80.00,'2026-07-15 19:11:55','apto','bien'),(2,5,2,50.00,'2026-07-15 19:12:07','no_apto','regular');
/*!40000 ALTER TABLE `evaluaciones_vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_asignados`
--

DROP TABLE IF EXISTS `roles_asignados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_asignados` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tipo_rol` enum('admin','cliente','chofer','personal') NOT NULL,
  PRIMARY KEY (`id_rol`),
  KEY `fk_roles_asignados_usuarios1_idx` (`id_usuario`),
  CONSTRAINT `fk_roles_asignados_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_asignados`
--

LOCK TABLES `roles_asignados` WRITE;
/*!40000 ALTER TABLE `roles_asignados` DISABLE KEYS */;
INSERT INTO `roles_asignados` VALUES (1,1,'cliente'),(2,2,'admin'),(3,3,'cliente'),(4,4,'cliente'),(5,3,'chofer'),(6,5,'personal'),(7,6,'chofer'),(8,11,'chofer'),(9,12,'cliente'),(10,13,'chofer'),(11,12,'chofer'),(12,13,'cliente');
/*!40000 ALTER TABLE `roles_asignados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transacciones`
--

DROP TABLE IF EXISTS `transacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transacciones` (
  `id_transaccion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `tipo` enum('recarga','pago_viaje','pago_chofer','retiro') NOT NULL,
  `id_banco` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nro_ref` varchar(45) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','finalizado') NOT NULL,
  `detalles` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_transaccion`),
  UNIQUE KEY `nro_ref_UNIQUE` (`nro_ref`),
  KEY `fk_transacciones_usuarios1_idx` (`id_usuario`),
  KEY `fk_transaccion_banco` (`id_banco`),
  CONSTRAINT `fk_transaccion_banco` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`),
  CONSTRAINT `fk_transacciones_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacciones`
--

LOCK TABLES `transacciones` WRITE;
/*!40000 ALTER TABLE `transacciones` DISABLE KEYS */;
INSERT INTO `transacciones` VALUES (1,1,'recarga',3,15000.00,'REC-125425','2026-07-15 03:06:04','finalizado',NULL),(2,1,'pago_viaje',1,7215.19,'VIAJE-496598','2026-07-15 19:20:23','finalizado',NULL),(3,3,'pago_chofer',1,5050.63,'CHOFER-496598','2026-07-15 19:20:23','finalizado',NULL),(4,1,'pago_viaje',1,3699.54,'VIAJE-498863','2026-07-15 19:29:17','finalizado',NULL),(5,3,'pago_chofer',1,2589.68,'CHOFER-498863','2026-07-15 19:29:17','finalizado','Traslado de Alta Vista a Castillito'),(6,3,'retiro',1,1500.00,'RETIRO-814646','2026-07-15 19:30:39','finalizado','Transferencia a Banesco'),(7,3,'retiro',1,5200.00,'RETIRO-268040','2026-07-15 19:50:19','finalizado','Transferencia a Banesco'),(8,3,'retiro',1,500.00,'RETIRO-584366','2026-07-15 20:01:00','finalizado','Transferencia a Mercantil'),(9,1,'recarga',1,15000.00,'REC-545435','2026-07-16 02:33:10','finalizado',NULL),(10,1,'recarga',1,1000.00,'REC-254534','2026-07-16 02:39:46','finalizado',NULL),(11,1,'recarga',1,1000.00,'REC-453543','2026-07-16 02:40:37','finalizado','Recarga a Sistema'),(12,1,'recarga',1,1000.00,'REC-635478','2026-07-16 02:41:41','finalizado','Recarga de saldo'),(13,1,'recarga',1,1000.00,'REC-565435','2026-07-16 02:43:51','finalizado','Recarga a Sistema'),(14,1,'recarga',3,1000.00,'REC-415635','2026-07-16 02:47:38','finalizado','Recarga a Banco Provincial');
/*!40000 ALTER TABLE `transacciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traslados`
--

DROP TABLE IF EXISTS `traslados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `traslados` (
  `id_traslado` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_chofer` int(11) NOT NULL,
  `id_vehiculo` int(11) DEFAULT NULL,
  `id_zona_origen` int(11) NOT NULL,
  `id_zona_destino` int(11) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` enum('pendiente','en_curso','finalizado','cancelado') NOT NULL,
  PRIMARY KEY (`id_traslado`),
  KEY `fk_traslados_choferes1_idx` (`id_chofer`),
  KEY `fk_traslados_clientes1_idx` (`id_cliente`),
  KEY `fk_traslados_zonas1_idx` (`id_zona_origen`),
  KEY `fk_traslados_zonas2_idx` (`id_zona_destino`),
  KEY `fk_traslados_vehiculos1_idx` (`id_vehiculo`),
  CONSTRAINT `fk_traslados_choferes1` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_vehiculos1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_zonas1` FOREIGN KEY (`id_zona_origen`) REFERENCES `zonas` (`id_zona`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_zonas2` FOREIGN KEY (`id_zona_destino`) REFERENCES `zonas` (`id_zona`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traslados`
--

LOCK TABLES `traslados` WRITE;
/*!40000 ALTER TABLE `traslados` DISABLE KEYS */;
INSERT INTO `traslados` VALUES (1,1,1,1,14,26,7215.19,'2026-07-15 19:16:02','finalizado'),(2,1,1,1,14,21,3699.54,'2026-07-15 19:29:02','finalizado'),(3,1,1,NULL,15,21,2615.79,'2026-07-15 20:05:23','pendiente');
/*!40000 ALTER TABLE `traslados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `correo` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombres` varchar(45) NOT NULL,
  `apellidos` varchar(45) NOT NULL,
  `cedula` varchar(15) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo_UNIQUE` (`correo`),
  UNIQUE KEY `cedula_UNIQUE` (`cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'aaromarmando3@gmail.com','$2y$10$5YuDYzVGyY.GZ9bfRObRu.zutUBG.fIo7i7KFWLXP5eDU/4k4KTF6','aarom armando','luces bolivar','28162993','04242282256'),(2,'admin@decarrerita.com','$2y$10$5YuDYzVGyY.GZ9bfRObRu.zutUBG.fIo7i7KFWLXP5eDU/4k4KTF6','Luis Miguel','Perez Lopez','25684235',''),(3,'juancarlos@gmail.com','$2y$10$m4kOEzgiAWfezs4/0D1Br.22EPOBMQnQG/zRdt/KZIK8hHj54KYx.','juan carlos','perez garcia','25683215','04125656516'),(4,'miguelsamuel@gmail.com','$2y$10$6KP4H6uwsAeUhiZ0S6cmge7Xafuggp/4aNmyWiM.EBF2fgJ4kao6G','miguel samuel','lugo hernandez','25836952','04144127566'),(5,'personalLaura@decarrerita.com','$2y$10$VXU5mk9lxJ7iwTlFJ22HCeyqz.Bm4OrM.Pw/MooZoN0fHBuYzqL9W','Laura Gabriela','Perez Angelica','8845629','04162586256'),(6,'libelisyemes@gmail.com','$2y$10$5RebXPcyuBeIJNNZGdAiIO292n3K4GLAZWDs20fI4xW3JxEI3J2T6','Lisbelis','yemes','28653154','04141532696'),(11,'manue@gmail.com','$2y$10$LlIXZa5WxahSRkETAjtSmua9x4zGtd9uhhlBxiSNe3ZF2yEpwodAK','manuekl','rivas','51651651','04141653232'),(12,'prueba@gmail.com','$2y$10$107o7sHCngUHUou78HIcsuLOZKWAwP3US/PkueR0Y59/Lly7agDq.','prueba','pasajero','132156351','04141354561'),(13,'choferprueba@gmail.com','$2y$10$n1wWb4ca35RGHP9DOSKd4upcJ4Q1wVVdzvlaFxI/5fRYnGCWvsCZi','chofer','chofer','8523161','04146168416');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT,
  `placa` varchar(10) NOT NULL,
  `marca` varchar(45) NOT NULL,
  `modelo` varchar(45) NOT NULL,
  `anio` int(11) NOT NULL,
  `id_chofer` int(11) NOT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `placa_UNIQUE` (`placa`),
  KEY `fk_carros_choferes_idx` (`id_chofer`),
  CONSTRAINT `fk_carros_choferes` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,'aa111bbb','toyota','yaris',2006,1),(2,'aa12vc','honda','ranger',2005,1);
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zonas`
--

DROP TABLE IF EXISTS `zonas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zonas` (
  `id_zona` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_zona` varchar(100) NOT NULL,
  `coord_x` decimal(10,2) NOT NULL,
  `coord_y` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zonas`
--

LOCK TABLES `zonas` WRITE;
/*!40000 ALTER TABLE `zonas` DISABLE KEYS */;
INSERT INTO `zonas` VALUES (1,'Las Amazonas',8.22,-62.83),(2,'Las Teodokildas',8.23,-62.83),(3,'Gran Sabana',8.24,-62.82),(4,'Villa Betania',8.25,-62.81),(5,'Villa Bahía',8.24,-62.79),(6,'Curagua',8.27,-62.78),(7,'Santa Rosa',8.25,-62.77),(8,'Lomas del Caroní',8.26,-62.77),(9,'Río Yocoima',8.28,-62.75),(10,'Unare 2',8.28,-62.76),(11,'Mini Fincas',8.26,-62.73),(12,'Villa Africana',8.28,-62.72),(13,'Los Olivos',8.28,-62.72),(14,'Alta Vista',8.29,-62.74),(15,'Chilemex',8.30,-62.72),(16,'Guaiparo',8.34,-62.69),(17,'La Llovizna',8.33,-62.67),(18,'Unare 1',8.28,-62.75),(19,'Terrazas del Caroní',8.26,-62.74),(20,'Río Aro',8.27,-62.75),(21,'Castillito',8.32,-62.71),(22,'El Roble',8.35,-62.67),(23,'Centro de San Félix',8.37,-62.66),(24,'Las Batallas',8.37,-62.65),(25,'Vista Al Sol',8.35,-62.62),(26,'Barrio Brisas del Sur',8.33,-62.64),(27,'11 de Abril',8.36,-62.62),(28,'Inés Romero',8.37,-62.61),(29,'Chirica Vieja',8.32,-62.63),(30,'Francisca Duarte',8.31,-62.63),(31,'Primero de Mayo',8.36,-62.64);
/*!40000 ALTER TABLE `zonas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-16 20:26:24
