SET FOREIGN_KEY_CHECKS=0;  
-- MySQL dump 
-- Database: my_store
-- Gộp cấu trúc từ database.sql (tính năng hoàn trả) và database_copy.sql (tính năng đặt lại mật khẩu)
-- ------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;

--
-- Table structure for table `account`
--
DROP TABLE IF EXISTS `account`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account`
-- (Gộp tất cả các account từ nhánh copy)
--
INSERT INTO `account` (`id`, `username`, `email`, `fullname`, `password`, `role`, `created_at`) VALUES
(1, 'admin', NULL, 'Administrator', '$2y$12$vZ.q7WSFUkvqv1lBOr/7NeV7/CTxOGy1jPzPE4IIge86jeCuvV9N.', 'admin', '2026-05-19 16:33:07'),
(2, 'user', NULL, 'Nguyễn Văn A', '$2y$12$jMLsAnJiXTZtBKNsg8bWdujvCBlRQEJoP7AsvcaevWbebvw8BkP8.', 'user', '2026-05-19 16:33:08'),
(3, 'testuser_1780672121', 'testuser_1780672121@example.com', 'Test User', '$2y$12$E1dvPtkTj/5dP7U2Sf65FuLii9rGltsoLNKRMxjAWLjh0onbJRY8W', 'user', '2026-06-05 15:08:41'),
(4, 'testuser_1780672222', 'testuser_1780672222@example.com', 'Test User', '$2y$12$zXYrsjLDBI7cbSbMjQaF4uCALHBt59LkMyhaT/V3rOrByvX9DRDu.', 'user', '2026-06-05 15:10:22'),
(5, 'test', 'test@gmail.com', 'tui tinh test', '$2y$12$Fa.F/cS1YHsdSWgCz7Kxdum7JIHJ51rMg4zraHp5Q0sKFI7RfcU1O', 'user', '2026-06-05 15:13:15');

--
-- Table structure for table `category`
--
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--
INSERT INTO `category` (`id`, `name`, `description`) VALUES
(1, 'Điện thoại', 'Các dòng điện thoại thông minh iOS và Android chính hãng mới nhất'),
(2, 'Laptop', 'Máy tính xách tay văn phòng, gaming, đồ họa cao cấp'),
(3, 'Phụ kiện', 'Cáp sạc, tai nghe, chuột, bàn phím và thiết bị phụ trợ'),
(4, 'Máy tính bảng', 'iPad và các mẫu tablet cấu hình cao, màn hình rộng');

--
-- Table structure for table `product`
--
DROP TABLE IF EXISTS `product`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--
INSERT INTO `product` (`id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `image`, `category_id`, `is_featured`, `brand`) VALUES
(1, 'iPhone 15 Pro Max 256GB Titanium', 'iphone-15-pro-max-256gb-titanium', 'Thiết kế khung viền titan siêu bền nhẹ, chip A17 Pro đột phá hiệu năng, camera zoom quang học 5x cực đỉnh.', 34990000.00, 32990000.00, 5, 'public/uploads/iphone_15_pro_max.png', 1, 1, 'Apple'),
(2, 'Macbook Air M2 13.6 inch 8GB/256GB', 'macbook-air-m2-13-6-inch-8gb-256gb', 'Mỏng nhẹ tinh tế sang trọng, chip Apple M2 mạnh mẽ, màn hình Liquid Retina sống động, pin tối đa 18 tiếng liên tục.', 26490000.00, 24990000.00, 0, 'public/uploads/macbook_air_m2.png', 2, 1, 'Apple'),
(3, 'Tai nghe AirPods Pro Gen 2 USB-C', 'tai-nghe-airpods-pro-gen-2-usb-c', 'Chức năng chống ồn chủ động (ANC) thế hệ mới, âm thanh vòm cá nhân hóa, kháng nước IP54.', 5890000.00, 5490000.00, 30, 'public/uploads/airpods_pro_2.png', 3, 0, 'Apple'),
(4, 'iPad Air 5 M1 Wifi 64GB', 'ipad-air-5-m1-wifi-64gb', 'Trang bị chip Apple M1 tối tân, hỗ trợ Apple Pencil 2 và Magic Keyboard, màn hình 10.9 inch hiển thị rực rỡ.', 15490000.00, 14290000.00, 23, 'public/uploads/ipad_air_5.png', 4, 0, 'Apple');

--
-- Table structure for table `orders`
--
DROP TABLE IF EXISTS `orders`;
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
  `return_reason` text COLLATE utf8mb4_unicode_ci,
  `return_products` text COLLATE utf8mb4_unicode_ci,
  `return_admin_reply` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_orders_account` (`account_id`),
  CONSTRAINT `fk_orders_account` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
-- (Lấy dữ liệu test hoàn trả từ nhánh database.sql)
--
INSERT INTO `orders` (`id`, `account_id`, `name`, `phone`, `address`, `coupon_code`, `discount_amount`, `total_amount`, `status`, `created_at`, `return_reason`, `return_products`, `return_admin_reply`) VALUES
(1, 2, 'TÌNH NGỌC', '0369861439', '123 lê đức thọ', NULL, 0.00, 14390000.00, 'Hoàn thành', '2026-06-06 01:31:33', NULL, NULL, NULL),
(2, 2, 'TÌNH NGUYỄN NGỌC', '0369861439', '123 lê đức thọ', NULL, 0.00, 5590000.00, 'Đã thu hồi', '2026-06-06 01:42:11', 'hàng bị lỗi', '[\"3\"]', 'cảm ơn bạn yêu cầu đã được tiếp nhận và chúng tôi sẽ đên thu hồi vào ngày sớm nhất'),
(3, 2, 'TÌNH NGUYỄN NGỌC', '0369861439', '123 lê đức thọ', NULL, 0.00, 38530000.00, 'Đã giao hàng', '2026-06-06 02:30:05', NULL, NULL, NULL),
(4, 2, 'TÌNH NGUYỄN NGỌC', '0369861439', '123 lê đức thọ', NULL, 0.00, 33090000.00, 'Đã duyệt hoàn trả', '2026-06-06 03:14:28', 'lỗi hàng', '[\"1\"]', 'okii'),
(5, 2, 'TÌNH NGUYỄN NGỌC', '0369861439', '123 lê đức thọ', NULL, 0.00, 33090000.00, 'Đã hủy', '2026-06-06 03:15:54', NULL, NULL, NULL);

--
-- Table structure for table `order_details`
--
DROP TABLE IF EXISTS `order_details`;
CREATE TABLE `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
-- (Khớp với danh sách 5 orders ở trên)
--
INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 4, 1, 14290000.00),
(2, 2, 3, 1, 5490000.00),
(3, 3, 3, 7, 5490000.00),
(4, 4, 1, 1, 32990000.00),
(5, 5, 1, 1, 32990000.00);

--
-- Table structure for table `password_resets`
-- (Bảng từ nhánh database_copy.sql)
--
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` int NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--
INSERT INTO `password_resets` (`id`, `email`, `otp_code`, `expires_at`, `created_at`) VALUES
(1, 'testuser_1780672121@example.com', 978064, '2026-06-05 15:23:41', '2026-06-05 15:08:41');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
SET FOREIGN_KEY_CHECKS=1;  
