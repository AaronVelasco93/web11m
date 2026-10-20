CREATE DATABASE crud_pdo;
USE crud_pdo;

DROP TABLE IF EXISTS `alumnos`;
CREATE TABLE `alumnos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `alumnos` WRITE;
INSERT INTO `alumnos` VALUES (6,'Kevin','kevin@gmail.com','2026-09-28 20:14:54'),(9,'Kevinas','kevin@gmsail.com','2026-09-28 20:53:05'),(11,'aaaron Velascoooooo','asdasd@gmasasasail.com','2026-09-30 21:12:09');
UNLOCK TABLES;
