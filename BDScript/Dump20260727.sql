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
  `estado` varchar(20) DEFAULT 'activo',
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'Sistema','0000','activo'),(2,'Banco Venezuela','0102','activo'),(3,'Banco Provincial','0108','activo'),(4,'Mercantil','0105','activo'),(5,'Banesco','0134','activo'),(6,'Banco Nacional de Crédito BNC','0191','activo'),(7,'Bancamiga','0172','activo'),(8,'Banco del Tesoro','0163','activo'),(9,'Banco Bicentenario','0175','activo'),(10,'Bancaribe','0114','activo'),(11,'Banco Exterior','0115','activo'),(12,'Banplus','0174','activo'),(13,'Banco Plaza','0138','activo'),(14,'Banco Fondo Común BFC','0151','activo'),(15,'Banco Activo','0171','inactivo'),(16,'Bancrecer','0168','activo'),(17,'100% Banco','0156','activo'),(18,'DelSur Banco Universal','0157','activo'),(19,'Banco Caroní','0128','activo'),(20,'Venezolano de Crédito','0104','activo'),(21,'Mi Banco','0169','activo'),(22,'BANFANB','0177','activo'),(23,'Banco Agrícola de Venezuela','0166','activo');
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
INSERT INTO `choferes` VALUES (1,5,12,'01745263153543453455',1982.29),(2,6,16,'01686516565561656515',10706.66),(3,7,18,'01575613232132132132',0.00),(4,8,14,'01515464564564564564',0.00),(6,3,18,'01573221321321312312',0.00),(7,10,5,'01345446456456456546',0.00),(8,11,18,'01574564564564564564',0.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,3,10312.73),(2,4,0.00),(3,10,0.00);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contactos_emergencia`
--

DROP TABLE IF EXISTS `contactos_emergencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contactos_emergencia` (
  `id_contactos` int(11) NOT NULL AUTO_INCREMENT,
  `id_chofer` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `relacion` enum('Cónyuge/Pareja','Padre/Madre','Hijo/Hija','Familiar','Amigo/Amiga','Otro') NOT NULL,
  PRIMARY KEY (`id_contactos`),
  KEY `fk_choferes_contactos` (`id_chofer`),
  CONSTRAINT `fk_choferes_contactos` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactos_emergencia`
--

LOCK TABLES `contactos_emergencia` WRITE;
/*!40000 ALTER TABLE `contactos_emergencia` DISABLE KEYS */;
INSERT INTO `contactos_emergencia` VALUES (1,1,'luis','04144525245','Padre/Madre'),(2,1,'miguel','04145345354','Hijo/Hija'),(3,2,'sam','04149846545','Hijo/Hija'),(4,3,'luis','04145311531','Cónyuge/Pareja'),(5,3,'debora','04128565613','Padre/Madre'),(6,4,'luis','04145465463','Padre/Madre'),(7,4,'kjl','04143123123','Familiar'),(10,6,'sam','04246546456','Padre/Madre'),(11,6,'anderson','04145464564','Familiar'),(12,7,'miguel','04125464564','Padre/Madre'),(13,7,'samuel','04124565464','Hijo/Hija'),(14,8,'luisa','04245464564','Cónyuge/Pareja'),(15,8,'manuel','04144564565','Hijo/Hija');
/*!40000 ALTER TABLE `contactos_emergencia` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_empresa`
--

LOCK TABLES `cuentas_empresa` WRITE;
/*!40000 ALTER TABLE `cuentas_empresa` DISABLE KEYS */;
INSERT INTO `cuentas_empresa` VALUES (1,1,'0000000000000000','J-12345678-9','Decarrerita C.A.',NULL,'activo'),(2,2,'8416852001403520','J-12345678-9','Decarrerita C.A.',NULL,'activo'),(3,3,'5214685215357582','J-12345678-9','Decarrerita C.A.',NULL,'inactivo'),(4,7,'4564868464564564','J-454654654-3','Decarrerita C.A','04169856456','activo'),(5,10,'5654213654687684','J-256834164-4','Decarrerita C.A','04149566285','activo'),(8,19,'1231321313123123','J-123123123-1','Decarrerita C.A',NULL,'inactivo');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_choferes`
--

LOCK TABLES `evaluaciones_choferes` WRITE;
/*!40000 ALTER TABLE `evaluaciones_choferes` DISABLE KEYS */;
INSERT INTO `evaluaciones_choferes` VALUES (1,2,1,80.00,'2026-07-27 11:53:54','aprobado',NULL),(2,2,2,80.00,'2026-07-27 11:58:45','aprobado','bien'),(3,NULL,3,NULL,'2026-07-27 13:27:01','pendiente',NULL),(4,NULL,4,NULL,'2026-07-27 13:29:43','pendiente',NULL),(6,NULL,6,NULL,'2026-07-27 13:38:44','pendiente',NULL),(7,NULL,7,NULL,'2026-07-27 13:49:10','pendiente',NULL),(8,NULL,8,NULL,'2026-07-27 13:50:14','pendiente',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_vehiculos`
--

LOCK TABLES `evaluaciones_vehiculos` WRITE;
/*!40000 ALTER TABLE `evaluaciones_vehiculos` DISABLE KEYS */;
INSERT INTO `evaluaciones_vehiculos` VALUES (1,2,1,100.00,'2026-07-27 11:54:48','apto','Todo correcto'),(2,2,2,100.00,'2026-07-27 11:59:04','apto','bien'),(3,2,3,80.00,'2026-07-27 11:59:21','apto',''),(4,2,4,50.00,'2026-07-27 11:59:47','no_apto','Modelo del carro incorrecto');
/*!40000 ALTER TABLE `evaluaciones_vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pago_chofer`
--

DROP TABLE IF EXISTS `pago_chofer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pago_chofer` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_chofer` int(11) NOT NULL,
  `id_personal` int(11) DEFAULT NULL,
  `id_banco` int(11) NOT NULL,
  `numero_cuenta` varchar(30) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nro_ref` varchar(100) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `detalles` text DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `fk_pago_chofer` (`id_chofer`),
  KEY `fk_pago_personal` (`id_personal`),
  KEY `fk_pago_banco` (`id_banco`),
  CONSTRAINT `fk_pago_banco` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`) ON DELETE CASCADE,
  CONSTRAINT `fk_pago_chofer` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE CASCADE,
  CONSTRAINT `fk_pago_personal` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_chofer`
--

LOCK TABLES `pago_chofer` WRITE;
/*!40000 ALTER TABLE `pago_chofer` DISABLE KEYS */;
INSERT INTO `pago_chofer` VALUES (1,2,2,16,'01686516565561656515',9303.11,'231231','2026-07-27 12:08:54',NULL),(2,1,2,12,'01745263153543453455',4389.03,'546464','2026-07-27 12:09:03',NULL);
/*!40000 ALTER TABLE `pago_chofer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recargas`
--

DROP TABLE IF EXISTS `recargas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recargas` (
  `id_recarga` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_cuenta` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `nro_ref` varchar(100) NOT NULL,
  `fecha_pago` date NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_recarga`),
  KEY `fk_recarga_cliente` (`id_cliente`),
  KEY `fk_recargas_cuenta` (`id_cuenta`),
  CONSTRAINT `fk_recarga_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `fk_recargas_cuenta` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas_empresa` (`id_cuenta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recargas`
--

LOCK TABLES `recargas` WRITE;
/*!40000 ALTER TABLE `recargas` DISABLE KEYS */;
INSERT INTO `recargas` VALUES (1,1,4,3000.00,'221313','2026-07-27','2026-07-27 12:43:27'),(2,1,2,25000.00,'321313','2026-07-27','2026-07-27 13:06:31');
/*!40000 ALTER TABLE `recargas` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_asignados`
--

LOCK TABLES `roles_asignados` WRITE;
/*!40000 ALTER TABLE `roles_asignados` DISABLE KEYS */;
INSERT INTO `roles_asignados` VALUES (1,1,'admin'),(2,2,'personal'),(3,3,'cliente'),(4,4,'cliente'),(5,5,'chofer'),(6,6,'chofer'),(7,7,'chofer'),(8,8,'chofer'),(10,3,'chofer'),(11,10,'cliente'),(12,10,'chofer'),(13,11,'chofer'),(14,11,'cliente');
/*!40000 ALTER TABLE `roles_asignados` ENABLE KEYS */;
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
  `estado` enum('en_curso','finalizado','cancelado') NOT NULL,
  `id_pago_chofer` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_traslado`),
  KEY `fk_traslados_choferes1_idx` (`id_chofer`),
  KEY `fk_traslados_clientes1_idx` (`id_cliente`),
  KEY `fk_traslados_zonas1_idx` (`id_zona_origen`),
  KEY `fk_traslados_zonas2_idx` (`id_zona_destino`),
  KEY `fk_traslados_vehiculos1_idx` (`id_vehiculo`),
  KEY `fk_traslados_pago_chofer` (`id_pago_chofer`),
  CONSTRAINT `fk_traslados_choferes1` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_pago_chofer` FOREIGN KEY (`id_pago_chofer`) REFERENCES `pago_chofer` (`id_pago`) ON DELETE SET NULL,
  CONSTRAINT `fk_traslados_vehiculos1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_zonas1` FOREIGN KEY (`id_zona_origen`) REFERENCES `zonas` (`id_zona`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_traslados_zonas2` FOREIGN KEY (`id_zona_destino`) REFERENCES `zonas` (`id_zona`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traslados`
--

LOCK TABLES `traslados` WRITE;
/*!40000 ALTER TABLE `traslados` DISABLE KEYS */;
INSERT INTO `traslados` VALUES (1,1,2,3,14,21,3858.09,'2026-07-27 12:06:10','finalizado',1),(2,1,2,3,23,26,3995.73,'2026-07-27 12:06:22','finalizado',1),(3,1,2,3,21,26,5436.34,'2026-07-14 12:06:48','finalizado',1),(4,1,1,1,15,26,6270.04,'2026-07-27 12:06:56','finalizado',2),(5,1,2,3,1,24,15295.23,'2026-07-27 13:06:41','finalizado',NULL),(6,1,1,1,5,4,2831.84,'2026-07-27 13:06:48','finalizado',NULL);
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
  `estado` enum('activo','bloqueado') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo_UNIQUE` (`correo`),
  UNIQUE KEY `cedula_UNIQUE` (`cedula`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin@decarrerita.com','$2y$10$VXU5mk9lxJ7iwTlFJ22HCeyqz.Bm4OrM.Pw/MooZoN0fHBuYzqL9W','Luis Miguel','Perez Lopez','25684235','04142545542','activo'),(2,'personallaura@decarrerita.com','$2y$10$VXU5mk9lxJ7iwTlFJ22HCeyqz.Bm4OrM.Pw/MooZoN0fHBuYzqL9W','Laura Gabriela','Perez Angelica','V-8845629','04162586256','activo'),(3,'aaromarmando3@gmail.com','$2y$10$O59sJPaGiA45M45tRS6JaOt1tr53qRPkpz4UXJQgM.2FR.eR5yAfW','aarom armando','luces bolivar','V-28162993','04242282256','activo'),(4,'pedro@gmail.com','$2y$10$xwbLiPJeRWae6fK/BW8WDOXQ0UBzryskKoWR.A6un2RjNGvwOMxJi','pedro','rojas','V-15263385','04122313132','activo'),(5,'juan@gmail.com','$2y$10$Siagz2Wv82r4h2qQxGfPpeJNQVe3CigHGXCf5sXWntp5JlZIh68/e','juan','garcia','V-26513223','04163165123','activo'),(6,'carlos@gmail.com','$2y$10$C2lK7l2cPsOB8FEc4vsySOeQGzVV.nhqg7r5Q6QjLn2ytOU0R8AtC','carlos','perez','V-16515531','04148411315','activo'),(7,'manuel@gmail.com','$2y$10$PWiMAjyVb1s1zDcGJF2qzerLEbA/q2Xz3tTEy4dtFwuVHbSFQksyu','manuel','rivas','V-11313321','04145155212','activo'),(8,'sam@gmail.com','$2y$10$aoytT63/koUnr.HQOux9jeWvMmdqm1V60Ko6SzuieRDsLy.d.zvK2','sam','bolivar','V-21511321','04263165135','activo'),(10,'armando@gmail.com','$2y$10$vJA2c15Lj86Hb4r28NQnE.fnBKqRMFUCEZMz0ZForZbxJDga07mru','armando','perez','V-52312321','04241321232','activo'),(11,'samuel@gmail.com','$2y$10$JT9cvmNBRwiE3VpFMhq77ugt0ETxsaAouMqChjtTlwxes0CJFb6jW','samuel','lopez','V-11111111','04141651655','activo');
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
  `activo` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `placa_UNIQUE` (`placa`),
  KEY `fk_carros_choferes_idx` (`id_chofer`),
  CONSTRAINT `fk_carros_choferes` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,'AA12VAS','TOYOTA','HILUX',2006,1,1),(2,'DAS1D6A','HYUNDAI','ELANTRA',2006,2,0),(3,'1SD1AS3','MITSUBISHI','SIGNO',2001,2,1),(4,'1S3A1D3','TOYOTA','HOONDA',2006,2,0);
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
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zonas`
--

LOCK TABLES `zonas` WRITE;
/*!40000 ALTER TABLE `zonas` DISABLE KEYS */;
INSERT INTO `zonas` VALUES (1,'Las Amazonas',8.22,-62.83,1),(2,'Las Teodokildas',8.23,-62.83,0),(3,'Gran Sabana',8.24,-62.82,1),(4,'Villa Betania',8.25,-62.81,1),(5,'Villa Bahía',8.24,-62.79,1),(6,'Curagua',8.27,-62.78,1),(7,'Santa Rosa',8.25,-62.77,1),(8,'Lomas del Caroní',8.26,-62.77,1),(9,'Río Yocoima',8.28,-62.75,1),(10,'Unare 2',8.28,-62.76,1),(11,'Mini Fincas',8.26,-62.73,1),(12,'Villa Africana',8.28,-62.72,1),(13,'Los Olivos',8.28,-62.72,1),(14,'Alta Vista',8.29,-62.74,1),(15,'Chilemex',8.30,-62.72,1),(16,'Guaiparo',8.34,-62.69,1),(17,'La Llovizna',8.33,-62.67,1),(18,'Unare 1',8.28,-62.75,1),(19,'Terrazas del Caroní',8.26,-62.74,1),(20,'Río Aro',8.27,-62.75,1),(21,'Castillito',8.32,-62.71,1),(22,'El Roble',8.35,-62.67,1),(23,'Centro de San Félix',8.37,-62.66,1),(24,'Las Batallas',8.37,-62.65,1),(25,'Vista Al Sol',8.35,-62.62,1),(26,'Barrio Brisas del Sur',8.33,-62.64,1),(27,'11 de Abril',8.36,-62.62,1),(28,'Inés Romero',8.37,-62.61,1),(29,'Chirica Vieja',8.32,-62.63,1),(30,'Francisca Duarte',8.31,-62.63,1),(31,'Primero de Mayo',8.36,-62.64,1);
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

-- Dump completed on 2026-07-27 13:51:45
