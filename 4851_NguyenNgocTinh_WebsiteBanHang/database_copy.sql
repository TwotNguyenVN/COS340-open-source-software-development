-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: my_store
-- ------------------------------------------------------
-- Server version 8.4.3


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
-- Table structure for table `account`
--


DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `account`
--


LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (1,'admin',NULL,'Administrator','$2y$12$vZ.q7WSFUkvqv1lBOr/7NeV7/CTxOGy1jPzPE4IIge86jeCuvV9N.','admin','2026-05-19 16:33:07'),(2,'user',NULL,'Nguyễn Văn A','$2y$12$jMLsAnJiXTZtBKNsg8bWdujvCBlRQEJoP7AsvcaevWbebvw8BkP8.','user','2026-05-19 16:33:08'),(3,'testuser_1780672121','testuser_1780672121@example.com','Test User','$2y$12$E1dvPtkTj/5dP7U2Sf65FuLii9rGltsoLNKRMxjAWLjh0onbJRY8W','user','2026-06-05 15:08:41'),(4,'testuser_1780672222','testuser_1780672222@example.com','Test User','$2y$12$zXYrsjLDBI7cbSbMjQaF4uCALHBt59LkMyhaT/V3rOrByvX9DRDu.','user','2026-06-05 15:10:22'),(5,'test','test@gmail.com','tui tinh test','$2y$12$Fa.F/cS1YHsdSWgCz7Kxdum7JIHJ51rMg4zraHp5Q0sKFI7RfcU1O','user','2026-06-05 15:13:15');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `category`
--


DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `category`
--


LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Điện thoại','Các dòng điện thoại thông minh iOS và Android chính hãng mới nhất'),(2,'Laptop','Máy tính xách tay văn phòng, gaming, đồ họa cao cấp'),(3,'Phụ kiện','Cáp sạc, tai nghe, chuột, bàn phím và thiết bị phụ trợ'),(4,'Máy tính bảng','iPad và các mẫu tablet cấu hình cao, màn hình rộng');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `order_details`
--


DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `order_details`
--


LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (1,1,4,1,15490000.00),(2,2,1,2,32990000.00),(3,3,1,1,32990000.00),(4,4,2,1,24990000.00),(5,4,1,1,32990000.00),(6,5,2,3,24990000.00),(7,5,1,4,32990000.00),(8,5,3,1,5490000.00),(9,5,4,1,14290000.00),(10,6,3,11,5490000.00);
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `orders`
--


DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Chờ xác nhận',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_orders_account` (`account_id`),
  CONSTRAINT `fk_orders_account` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `orders`
--


LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'Nguyen Van A','0912345678','123 Duong Apple, TP. HCM',NULL,0.00,0.00,'Chờ xác nhận','2026-05-17 13:19:54'),(2,2,'Nguyen Ngoc Tinh','0987654321','123 Duong ABC, Quan 1, TP. HCM','NTECH10',6598000.00,59382000.00,'Chờ xác nhận','2026-05-19 17:02:48'),(3,2,'Nguyen Ngoc Tinh','0912345678','234234',NULL,0.00,32990000.00,'Đang giao hàng','2026-05-19 17:03:31'),(4,2,'Nguyen Ngoc Tinh','0912345678','123',NULL,0.00,57980000.00,'Chờ xác nhận','2026-05-19 17:15:34'),(5,2,'TÌNH 4851_NGUYỄN NGỌC','0369861439','lê đức thọ\r\ngò vấp',NULL,0.00,226710000.00,'Chờ xác nhận','2026-05-27 06:11:45'),(6,2,'TÌNH 4851_NGUYỄN NGỌC','0369861439','lê đức thọ\r\ngò vấp',NULL,0.00,60390000.00,'Chờ xác nhận','2026-05-27 06:38:43');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `password_resets`
--


DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` int NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `password_resets`
--


LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (1,'testuser_1780672121@example.com',978064,'2026-06-05 15:23:41','2026-06-05 15:08:41');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `product`
--


DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) DEFAULT NULL,
  `sale_price` decimal(15,2) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `brand` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `product`
--


LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'iPhone 15 Pro Max 256GB Titanium','iphone-15-pro-max-256gb-titanium','Thiết kế khung viền titan siêu bền nhẹ, chip A17 Pro đột phá hiệu năng, camera zoom quang học 5x cực đỉnh.',34990000.00,32990000.00,7,'public/uploads/iphone_15_pro_max.png',1,1,'Apple'),(2,'Macbook Air M2 13.6 inch 8GB/256GB','macbook-air-m2-13-6-inch-8gb-256gb','Mỏng nhẹ tinh tế sang trọng, chip Apple M2 mạnh mẽ, màn hình Liquid Retina sống động, pin tối đa 18 tiếng liên tục.',26490000.00,24990000.00,6,'public/uploads/macbook_air_m2.png',2,1,'Apple'),(3,'Tai nghe AirPods Pro Gen 2 USB-C','tai-nghe-airpods-pro-gen-2-usb-c','Chức năng chống ồn chủ động (ANC) thế hệ mới, âm thanh vòm cá nhân hóa, kháng nước IP54.',5890000.00,5490000.00,38,'public/uploads/airpods_pro_2.png',3,0,'Apple'),(4,'iPad Air 5 M1 Wifi 64GB','ipad-air-5-m1-wifi-64gb','Trang bị chip Apple M1 tối tân, hỗ trợ Apple Pencil 2 và Magic Keyboard, màn hình 10.9 inch hiển thị rực rỡ.',15490000.00,14290000.00,24,'public/uploads/ipad_air_5.png',4,0,'Apple');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- Dump completed on 2026-06-06 12:53:23