-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: IT490DB
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `userid` int NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (10,'dc35646faaf1dffa741f7c301a99c1ee','2026-02-24 04:20:55');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
	`gameId` int NOT NULL,
	`title` varchar(255) NOT NULL,
	`summary` text  NULL,
	`cover_url` varchar(255) NULL,
       	`rating` decimal(5,2) NULL,
	`release_date` date NULL,
	PRIMARY KEY (`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



DROP TABLE IF EXISTS `platforms`;
CREATE TABLE `platforms` (
	`platformId` int NOT NULL,
	`name` varchar(100) NOT NULL,
	PRIMARY KEY (`platformId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
	`genreId` int NOT NULL,
	`name` varchar(100) NOT NULL,
	PRIMARY KEY (`genreId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `game_platforms`;
CREATE TABLE `game_platforms` (
	`game_id` int NOT NULL,
	`platform_id` int NOT NULL, 
	PRIMARY KEY (`game_id`, `platform_id`),
	FOREIGN KEY (`game_id`) REFERENCES `games`(`gameId`) ON DELETE CASCADE,
	FOREIGN KEY (`platform_id`) REFERENCES `platforms`(`platformId`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `game_genres`;
CREATE TABLE `game_genres` (
	`game_id` int NOT NULL,
	`genre_id` int NOT NULL,
	PRIMARY KEY (`game_id`, `genre_id`),
	FOREIGN KEY (`game_id`) REFERENCES `games`(`gameId`) ON DELETE CASCADE,
	FOREIGN KEY (`genre_id`) REFERENCES `genres`(`genreId`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `user_platforms`;
CREATE TABLE `user_platforms` (
	`user_id` int NOT NULL,
	`platform_id` int NOT NULL,
	PRIMARY KEY (`user_id`, `platform_id`),
	FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`platform_id`) REFERENCES `platforms`(`platformId`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

DROP TABLE IF EXISTS `user_genres`;
CREATE TABLE `user_genres` (
        `user_id` int NOT NULL,
        `genre_id` int NOT NULL,
        PRIMARY KEY (`user_id`, `genre_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`genre_id`) REFERENCES `genres`(`genreId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `user_library`;
CREATE TABLE `user_library` (
	`id` int NOT NULL AUTO_INCREMENT,
	`user_id` int NOT NULL,
	`game_id` int NOT NULL,
	`status` enum('watchlist', 'playing', 'completed', 'dropped') DEFAULT 'watchlist',
	PRIMARY KEY (`id`),
	FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`game_id`) REFERENCES `games`(`gameId`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
	`id` int NOT NULL AUTO_INCREMENT,
	`user_id` int NOT NULL,
	`game_id` int NOT NULL,
	`rating` int CHECK (rating BETWEEN 1 AND 10),
	`comment` text,
	`created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`game_id`) REFERENCES `games`(`gameId`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT IGNORE INTO platforms (platformId, name) VALUES
(3, 'Linux'), (6, 'PC (Microsoft Windows)'), (7, 'PlayStation'),
(8, 'PlayStation 2'), (9, 'PlayStation 3'), (11, 'Xbox'),
(12, 'Xbox 360'), (13, 'PC (DOS)'), (14, 'Mac'),
(18, 'Nintendo Entertainment System (NES)'), (19, 'Super Nintendo Entertainment System (SNES)'),
(34, 'Android'), (39, 'iOS'), (48, 'PlayStation 4'),
(49, 'Xbox One'), (130, 'Nintendo Switch'), (167, 'PlayStation 5'),
(169, 'Xbox Series X|S');


INSERT IGNORE INTO genres (genreId, name) VALUES
(2, 'Point-and-click'), (4, 'Fighting'), (5, 'Shooter'),
(7, 'Music'), (8, 'Platform'), (9, 'Puzzle'),
(10, 'Racing'), (11, 'Real Time Strategy (RTS)'), (12, 'Role-playing (RPG)'),
(13, 'Simulator'), (14, 'Sport'), (15, 'Strategy'),
(16, 'Turn-based strategy (TBS)'), (24, 'Tactical'), (25, 'Hack and slash/Beat em up'),
(26, 'Quiz/Trivia'), (31, 'Adventure'), (32, 'Indie'),
(33, 'Arcade'), (34, 'Visual Novel'), (35, 'Card & Board Game'), (36, 'MOBA');
--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (10,'test','test@gmail.com','$2y$10$MIUOWDd24abc1atnAQ3Xm.b0Jeljc10fXakGvsdgODpkLGgcPzTou','test','test','2026-02-24 20:24:18'),(11,'Arghavan123','ak123@gmail.com','$2y$10$SalDwrBjTs2qO86lfLrTUerNnfhCtQvoRmKv4O.IwE0yUMNNskrHi','Arghavn','Katebi','2026-02-24 20:24:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-24 16:43:59
