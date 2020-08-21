-- MySQL dump 10.13  Distrib 8.0.21, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: efw_general
-- ------------------------------------------------------
-- Server version	8.0.21-0ubuntu0.20.04.4

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
-- Current Database: `efw_general`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `efw_general` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `efw_general`;

--
-- Table structure for table `efw_onesignal`
--

DROP TABLE IF EXISTS `efw_onesignal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_onesignal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userID` int DEFAULT NULL,
  `playerID` varchar(300) NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `efw_onesignal_userID_playerID_index` (`userID`,`playerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_onesignal`
--

LOCK TABLES `efw_onesignal` WRITE;
/*!40000 ALTER TABLE `efw_onesignal` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_onesignal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_roles`
--

DROP TABLE IF EXISTS `efw_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) DEFAULT NULL,
  `permissions` json DEFAULT NULL,
  `style` varchar(600) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asosal_roles_id_index` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_roles`
--

LOCK TABLES `efw_roles` WRITE;
/*!40000 ALTER TABLE `efw_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_users`
--

DROP TABLE IF EXISTS `efw_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `profileImage` varchar(400) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email_validated` int DEFAULT NULL,
  `regdate` datetime(6) DEFAULT CURRENT_TIMESTAMP(6),
  `lastlogin` datetime(6) DEFAULT NULL,
  `lastseen` datetime(6) DEFAULT NULL,
  `role` int DEFAULT '2',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `aramalar` (`name`,`surname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_users`
--

LOCK TABLES `efw_users` WRITE;
/*!40000 ALTER TABLE `efw_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_users_discord`
--

DROP TABLE IF EXISTS `efw_users_discord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_users_discord` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userID` int DEFAULT NULL,
  `discordID` int DEFAULT NULL,
  `username` varchar(600) DEFAULT NULL,
  `discriminator` int DEFAULT NULL,
  `locale` varchar(6) DEFAULT NULL,
  `avatar` varchar(150) DEFAULT NULL,
  `public_flags` varchar(60) DEFAULT NULL,
  `token` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `efw_users_discord_userID_index` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_users_discord`
--

LOCK TABLES `efw_users_discord` WRITE;
/*!40000 ALTER TABLE `efw_users_discord` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_users_discord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_users_profileData`
--

DROP TABLE IF EXISTS `efw_users_profileData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_users_profileData` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` int DEFAULT NULL,
  `about` varchar(5000) DEFAULT NULL,
  `coverPicture` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_users_profileData`
--

LOCK TABLES `efw_users_profileData` WRITE;
/*!40000 ALTER TABLE `efw_users_profileData` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_users_profileData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_users_theme`
--

DROP TABLE IF EXISTS `efw_users_theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_users_theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userID` int DEFAULT NULL,
  `layout` varchar(30) DEFAULT NULL,
  `footer` varchar(30) DEFAULT NULL,
  `navbar` varchar(30) DEFAULT NULL,
  `logoColors` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `efw_users_theme_userID_uindex` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_users_theme`
--

LOCK TABLES `efw_users_theme` WRITE;
/*!40000 ALTER TABLE `efw_users_theme` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_users_theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `efw_massdata`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `efw_massdata` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `efw_massdata`;

--
-- Table structure for table `efw_chats`
--

DROP TABLE IF EXISTS `efw_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_chats` (
  `id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `participants` longtext,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_chats`
--

LOCK TABLES `efw_chats` WRITE;
/*!40000 ALTER TABLE `efw_chats` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_hashtags`
--

DROP TABLE IF EXISTS `efw_hashtags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_hashtags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tag` varchar(60) DEFAULT NULL,
  `postid` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_hashtags`
--

LOCK TABLES `efw_hashtags` WRITE;
/*!40000 ALTER TABLE `efw_hashtags` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_hashtags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_notifications`
--

DROP TABLE IF EXISTS `efw_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `header` varchar(50) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `readState` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `efw_notifications_user_index` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_notifications`
--

LOCK TABLES `efw_notifications` WRITE;
/*!40000 ALTER TABLE `efw_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_notifications_donelist`
--

DROP TABLE IF EXISTS `efw_notifications_donelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_notifications_donelist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `itemID` int DEFAULT NULL,
  `context` varchar(50) DEFAULT NULL,
  `affector` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_notifications_donelist`
--

LOCK TABLES `efw_notifications_donelist` WRITE;
/*!40000 ALTER TABLE `efw_notifications_donelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_notifications_donelist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_notifications_meta`
--

DROP TABLE IF EXISTS `efw_notifications_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_notifications_meta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notifID` int DEFAULT NULL,
  `itemID` int DEFAULT NULL,
  `userID` int DEFAULT NULL,
  `context` varchar(50) DEFAULT NULL,
  `affector` int DEFAULT NULL,
  `hint` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `efw_notifications_meta_efw_notifications_id_fk` (`notifID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_notifications_meta`
--

LOCK TABLES `efw_notifications_meta` WRITE;
/*!40000 ALTER TABLE `efw_notifications_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_notifications_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_posts`
--

DROP TABLE IF EXISTS `efw_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender` int DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `text` text,
  `data` varchar(255) DEFAULT NULL,
  `postdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `hint` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `efw_posts_type_id_index` (`type`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_posts`
--

LOCK TABLES `efw_posts` WRITE;
/*!40000 ALTER TABLE `efw_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efw_posts_meta`
--

DROP TABLE IF EXISTS `efw_posts_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_posts_meta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `postid` int DEFAULT NULL,
  `owner` int DEFAULT NULL,
  `data` text,
  `time` datetime(4) DEFAULT CURRENT_TIMESTAMP(4),
  PRIMARY KEY (`id`),
  KEY `efw_posts_meta_postid_index` (`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_posts_meta`
--

LOCK TABLES `efw_posts_meta` WRITE;
/*!40000 ALTER TABLE `efw_posts_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_posts_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `efw_system`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `efw_system` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `efw_system`;

--
-- Table structure for table `efw_meta`
--

DROP TABLE IF EXISTS `efw_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `efw_meta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meta` varchar(255) DEFAULT NULL,
  `data` varchar(2500) DEFAULT NULL,
  `owner` int NOT NULL,
  `time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efw_meta`
--

LOCK TABLES `efw_meta` WRITE;
/*!40000 ALTER TABLE `efw_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `efw_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessionData`
--

DROP TABLE IF EXISTS `sessionData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessionData` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sesskey` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `val` varchar(255) DEFAULT NULL,
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sessionData_sesskey_index` (`sesskey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessionData`
--

LOCK TABLES `sessionData` WRITE;
/*!40000 ALTER TABLE `sessionData` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessionData` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-21 10:10:37
