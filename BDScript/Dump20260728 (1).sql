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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'Banco Venezuela','0102','activo'),(2,'Banco Provincial','0108','activo'),(3,'Mercantil','0105','activo'),(4,'Banesco','0134','activo'),(5,'Banco Nacional de Crédito BNC','0191','activo'),(6,'Bancamiga','0172','activo'),(7,'Banco del Tesoro','0163','activo'),(8,'Banco Bicentenario','0175','activo'),(9,'Bancaribe','0114','activo'),(10,'Banco Exterior','0115','activo'),(11,'Banplus','0174','activo'),(12,'Banco Plaza','0138','activo'),(13,'Banco Fondo Común BFC','0151','activo'),(14,'Banco Activo','0171','inactivo'),(15,'Bancrecer','0168','activo'),(16,'100% Banco','0156','activo'),(17,'DelSur Banco Universal','0157','activo'),(18,'Banco Caroní','0128','activo'),(19,'Venezolano de Crédito','0104','activo'),(20,'Mi Banco','0169','activo'),(21,'BANFANB','0177','activo'),(22,'Banco Agrícola de Venezuela','0166','activo');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `choferes`
--

LOCK TABLES `choferes` WRITE;
/*!40000 ALTER TABLE `choferes` DISABLE KEYS */;
INSERT INTO `choferes` VALUES (1,3,17,'01571012202016021206',6771.56),(2,7,1,'01024524245245242452',10090.13),(3,8,21,'01774535434534535434',0.00),(4,9,21,'01772646456465646541',0.00),(5,6,1,'01025156106510650165',0.00);
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
INSERT INTO `clientes` VALUES (1,2,3074.02),(2,6,0.00),(3,10,1912.15),(4,11,2050.18);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactos_emergencia`
--

LOCK TABLES `contactos_emergencia` WRITE;
/*!40000 ALTER TABLE `contactos_emergencia` DISABLE KEYS */;
INSERT INTO `contactos_emergencia` VALUES (1,1,'Manuel','04146454151','Familiar'),(2,1,'Simon','04245153313','Amigo/Amiga'),(3,2,'luis','04245424242','Amigo/Amiga'),(4,2,'sam garcia','04124554354','Familiar'),(5,3,'delsi','04145435435','Padre/Madre'),(6,3,'carlos','04164534535','Familiar'),(7,4,'miguel bolivar','04245464521','Familiar'),(8,4,'samuel silva','04165242242','Amigo/Amiga'),(9,5,'juan','04145234135','Hijo/Hija'),(10,5,'sam','04245435435','Hijo/Hija');
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
INSERT INTO `cuentas_empresa` VALUES (1,2,'8416852001403520','J-123485678-9','Decarrerita C.A.',NULL,'activo'),(2,3,'5214685215357582','J-123456768-9','Decarrerita C.A.',NULL,'inactivo'),(3,7,'4564868464564564','J-454654654-3','Decarrerita C.A','04169856456','activo'),(4,10,'5654213654687684','J-256834164-4','Decarrerita C.A','04149566285','activo'),(5,19,'1231321313123123','J-123123123-1','Decarrerita C.A',NULL,'inactivo');
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
  `fecha_creacion` datetime NOT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobado','reprobado') NOT NULL,
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `fk_evaluaciones_choferes1_idx` (`id_chofer`),
  KEY `fk_evaluaciones_usuarios1_idx` (`id_personal`),
  CONSTRAINT `fk_evaluaciones_choferes1` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_evaluaciones_usuarios1` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_choferes`
--

LOCK TABLES `evaluaciones_choferes` WRITE;
/*!40000 ALTER TABLE `evaluaciones_choferes` DISABLE KEYS */;
INSERT INTO `evaluaciones_choferes` VALUES (1,4,1,100.00,'2026-07-12 11:06:22','2026-07-13','aprobado',''),(2,5,2,90.00,'2026-07-28 11:17:00','2026-07-28','aprobado','\"Apto técnicamente para conducir, pero se sugiere capacitación corta en atención al cliente y resolución de conflictos.'),(3,4,3,40.00,'2026-07-28 11:27:25','2026-07-28','reprobado','Poco control de la tolerancia a la frustración y baja capacidad de manejo de estrés bajo presión. No apto para atención al público.'),(4,5,4,80.00,'2026-07-28 11:34:59','2026-07-28','aprobado','Muestra buen apego a las normas, aunque se detecta cierta fatiga acumulada. Se recomienda monitorear su carga horaria semanal.'),(5,NULL,5,NULL,'2026-07-28 12:01:34',NULL,'pendiente',NULL);
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
  `fecha_creacion` datetime NOT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `estado` enum('pendiente','apto','no_apto') NOT NULL,
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `fk_evaluaciones_vehiculos1_idx` (`id_vehiculo`),
  KEY `fk_evaluaciones_usuarios1_idx` (`id_personal`),
  CONSTRAINT `fk_evaluaciones_usuarios10` FOREIGN KEY (`id_personal`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_evaluaciones_vehiculos10` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones_vehiculos`
--

LOCK TABLES `evaluaciones_vehiculos` WRITE;
/*!40000 ALTER TABLE `evaluaciones_vehiculos` DISABLE KEYS */;
INSERT INTO `evaluaciones_vehiculos` VALUES (1,4,1,100.00,'2026-07-12 11:07:29','2026-07-14','apto',''),(2,4,2,100.00,'2026-07-28 11:18:46','2026-07-28','apto','Vehículo en óptimas condiciones mecánicas, estéticas y de seguridad. Aprobado para operación inmediata.'),(3,NULL,3,NULL,'2026-07-28 11:19:01',NULL,'pendiente',NULL),(4,NULL,4,NULL,'2026-07-28 11:19:23',NULL,'pendiente',NULL),(5,4,5,30.00,'2026-07-28 11:28:25','2026-07-28','no_apto','\"Desgaste severo en el sistema de frenos y fugas visibles de aceite en el motor. No apto para prestar servicio.'),(6,5,6,80.00,'2026-07-28 11:35:27','2026-07-28','apto','\"El vehículo cumple con el estándar mecánico pero presenta abolladuras menores en latonería y falla ligera en el aire acondicionado.'),(7,NULL,7,NULL,'2026-07-28 11:35:44',NULL,'pendiente',NULL);
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
INSERT INTO `pago_chofer` VALUES (1,4,4,21,'01772646456465646541',12855.26,'445345','2026-07-28 12:05:16',NULL),(2,2,4,1,'01024524245245242452',6657.62,'412542','2026-07-28 12:05:21',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recargas`
--

LOCK TABLES `recargas` WRITE;
/*!40000 ALTER TABLE `recargas` DISABLE KEYS */;
INSERT INTO `recargas` VALUES (1,1,1,6000.00,'455435','2026-07-15','2026-07-28 11:03:23'),(2,1,4,10000.00,'543654','2026-07-20','2026-07-28 11:03:54'),(3,1,4,8000.00,'442424','2026-07-25','2026-07-28 11:04:20'),(4,3,1,20000.00,'453543','2026-07-22','2026-07-28 12:04:37'),(5,4,1,5000.00,'543453','2026-07-22','2026-07-28 12:06:39'),(6,4,1,10000.00,'554354','2026-07-26','2026-07-28 12:06:51');
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
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp(),
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
INSERT INTO `roles_asignados` VALUES (1,1,'admin','2026-07-02 10:56:43'),(2,2,'cliente','2026-07-08 11:01:58'),(3,3,'chofer','2026-07-09 11:06:22'),(4,4,'personal','2026-07-10 11:08:44'),(5,5,'personal','2026-07-10 11:10:15'),(6,6,'cliente','2026-07-28 11:15:17'),(7,7,'chofer','2026-07-28 11:17:00'),(8,8,'chofer','2026-07-28 11:27:25'),(9,9,'chofer','2026-07-28 11:34:59'),(10,10,'cliente','2026-07-28 11:50:06'),(11,11,'cliente','2026-07-28 11:59:37'),(12,6,'chofer','2026-07-28 12:01:34');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traslados`
--

LOCK TABLES `traslados` WRITE;
/*!40000 ALTER TABLE `traslados` DISABLE KEYS */;
INSERT INTO `traslados` VALUES (1,1,4,6,26,21,5659.75,'2026-07-15 11:43:59','finalizado',1),(2,1,2,2,22,6,9510.89,'2026-07-20 11:44:09','finalizado',2),(3,1,4,6,8,1,5755.34,'2026-07-28 11:44:18','finalizado',1),(4,3,4,6,23,15,6949.56,'2026-07-28 12:04:44','finalizado',1),(5,3,2,2,26,21,5659.75,'2026-07-28 12:05:39','finalizado',NULL),(6,3,2,2,6,9,3373.15,'2026-07-28 12:05:59','finalizado',NULL),(7,3,2,2,18,10,2105.39,'2026-07-28 12:06:06','finalizado',NULL),(8,4,1,1,14,27,9673.65,'2026-07-28 12:06:57','finalizado',NULL),(9,4,2,2,13,9,3276.17,'2026-07-28 12:07:06','finalizado',NULL);
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
  UNIQUE KEY `cedula_UNIQUE` (`cedula`),
  UNIQUE KEY `telefono_UNIQUE` (`telefono`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin@decarrerita.com','$2y$10$VXU5mk9lxJ7iwTlFJ22HCeyqz.Bm4OrM.Pw/MooZoN0fHBuYzqL9W','lucas mateo','perez silva','V-22645986','04148542602','activo'),(2,'aaromarmando3@gmail.com','$2y$10$pKN.2WsjYnuh0PdDmMMjee4VhZ2L1I335I9z6QVVx8wRiqFSU36O2','aarom armando','luces bolivar','V-28162993','04242282256','activo'),(3,'miguel@gmail.com','$2y$10$A2bd6HMPPl9QowdkcRsm6uV.D3YI3PeNFJpSnZBsf0jcXS98zuIGy','miguel','lopez','V-12213123','04148541313','activo'),(4,'lauraperez@gmail.com','$2y$10$5v57r.GeVQe2432qEqssauQeUQqZTYI3ft.gveq3osWEUXWU8rWxq','Laura','Perez','V-26545852','04142616512','activo'),(5,'gabrieladasilva@gmail.com','$2y$10$.5P7j1FNSxxKiz1j20DY3.JyUMQ8AwnRCLIhl.9Zp8p8LtJGeA32.','gabriela','dasilva','V-26822852','04145345354','activo'),(6,'samuelgonzales@gmail.com','$2y$10$d9FoCy04KzVS7Xhg2QsoAuHrgnNGac.iIFcwfh5CQ6dzrdc5ApdJe','samuel','gonzales','V-26985258','04142235481','activo'),(7,'lucasrodri@gmail.com','$2y$10$GE/TRrBh/WxRabCXpks57.l2WirJXzkoNbjNfk4YFyFAjj1WZkKZq','lucas','rodriguez','V-29885859','04121564152','activo'),(8,'juan@gmail.com','$2y$10$7Rky15JLMEZ/rPiPbFAzrublGBzQaK5qD/4GnNNSHbdJXRUtvFthi','juan','hernandez','V-25635435','04144354345','activo'),(9,'giovanni@gmail.com','$2y$10$uj14a0xlGkIeqKPv8RxldO/z8nPkQx1rXMWiLUWDEgnh5C/JYPGeS','giovanni','bolivar','V-16896632','04141561651','activo'),(10,'lisbelisyemes@gmail.com','$2y$10$jIkhrB5fGD.9OA6t8upHSepJHE9645LKPYWLg7SCjteGDtSs0Akk.','Lisbelis','yemes','V-30437441','04249500568','activo'),(11,'angieurrieta@gmail.com','$2y$10$ZZAnT0EfzxEGNJgIS1.c8..aowQNGKS9SjQ/Y80Di/bAXKVcwtagm','angie','urrieta','V-31538385','04148786143','activo');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,'A1SD22V','TOYOTA','YARIS',2006,1,1),(2,'A2S2ASD','CHEVROLET','AVEO',2006,2,1),(3,'1SAD61S','FORD','FIESTA',2008,2,0),(4,'21ASD6A','HYUNDAI','ACCENT',2000,2,0),(5,'21SA2D2','KIA','RIO',2015,3,0),(6,'1SDA5SA','KIA','SORENTO',2006,4,1),(7,'5ASD561','TOYOTA','HILUX',2011,4,0);
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

-- Dump completed on 2026-07-28 12:15:03
