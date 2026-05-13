-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: salespilot
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `features` json DEFAULT NULL,
  `max_managers` int NOT NULL DEFAULT '1',
  `max_staff` int DEFAULT NULL,
  `max_branches` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `trial_days` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
INSERT INTO `subscription_plans` VALUES (1,'free',0.00,'Free 7-day trial to test all features','[\"basic_inventory\", \"basic_reports\", \"basic_user_roles\"]',1,1,1,1,0,7,'2026-04-29 19:44:41','2026-04-29 21:42:49'),(2,'basic',5000.00,'Perfect for small businesses','[\"basic_dashboard\", \"advanced_inventory\", \"basic_reports\", \"email_support\", \"basic_user_roles\", \"customer_management\"]',1,2,0,1,1,0,'2026-04-29 19:44:41','2026-04-29 21:42:46'),(3,'standard',10000.00,'Ideal for growing businesses','[\"advanced_dashboard\", \"advanced_inventory\", \"multi_branch\", \"advanced_reports\", \"priority_support\", \"advanced_user_roles\", \"pos_system\", \"customer_management\", \"invoicing\"]',2,4,2,1,1,0,'2026-04-29 19:44:41','2026-04-29 19:44:41'),(4,'premium',20000.00,'Complete solution for large businesses','[\"advanced_dashboard\", \"advanced_inventory\", \"multi_branch\", \"advanced_reports\", \"priority_support\", \"advanced_user_roles\", \"pos_system\", \"customer_management\", \"invoicing\", \"api_access\", \"stock_transfer\", \"supplier_management\", \"realtime_analytics\", \"export_data\", \"profit_loss_reports\", \"activity_logs\", \"phone_support\", \"dedicated_account_manager\"]',3,NULL,NULL,1,0,0,'2026-04-29 19:44:41','2026-04-29 19:44:41');
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30  9:14:39
