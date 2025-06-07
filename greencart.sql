-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 07, 2025 at 12:53 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `greencart`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-05-07 17:12:53', '2025-05-07 17:12:53'),
(2, 1, '2025-05-09 08:16:46', '2025-05-09 08:16:46'),
(3, 1, '2025-05-09 08:17:49', '2025-05-09 08:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `cart_item_id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_item_id`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 12, 1, '2025-05-07 17:12:53', '2025-05-07 17:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Indoor Plant', 'Beautiful plants for your home interior', 'indoor.png', '2025-03-09 13:07:55', '2025-05-09 06:07:08'),
(2, 'Outdoor Plants', 'Hardy plants for your garden and farm', 'outdoor.jpg', '2025-03-09 13:07:55', '2025-05-09 06:08:06'),
(6, 'Tools', 'Tools for maintaining plants', 'tool.jpg', '2025-05-09 06:08:59', '2025-05-09 06:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `status` enum('pending','delivered','cancelled','approved','declined') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_number`, `status`, `subtotal`, `total`, `payment_method`, `shipping_address`, `created_at`, `updated_at`) VALUES
(5, 1, 'ORD-681b15758f817', 'declined', 400.00, 400.00, 'cod', 'swoyambhu, bhagwanpau', '2025-05-07 08:10:29', '2025-05-07 10:22:21'),
(6, 1, 'ORD-681b158b71d1a', 'delivered', 400.00, 400.00, 'cod', 'swoyambhu, bhagwanpau', '2025-05-07 08:10:51', '2025-05-07 10:04:20'),
(7, 1, 'ORD-681b3055d2237', 'declined', 15.00, 15.00, 'cod', 'swoyambhu, bhagwanpau', '2025-05-07 10:05:09', '2025-05-07 10:22:25'),
(8, 2, 'ORD-681b348853d6c', 'approved', 15.00, 15.00, 'cod', 'Baneswor', '2025-05-07 10:23:04', '2025-05-09 05:54:26'),
(9, 2, 'ORD-681b443b81345', 'delivered', 14.00, 14.00, 'esewa', 'Baneswor', '2025-05-07 11:30:03', '2025-05-07 11:30:27'),
(10, 2, 'ORD-681b8110914ea', 'pending', 208.00, 208.00, 'esewa', '232', '2025-05-07 15:49:36', '2025-05-07 15:49:36'),
(11, 1, 'ORD-681dba2d0064a', 'pending', 105.00, 105.00, 'cod', 'ktm', '2025-05-09 08:17:49', '2025-05-09 08:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(7, 7, 10, 1, 15.00, '2025-05-07 10:05:09'),
(8, 8, 10, 1, 15.00, '2025-05-07 10:23:04'),
(9, 9, 6, 1, 14.00, '2025-05-07 11:30:03'),
(11, 11, 10, 7, 15.00, '2025-05-09 08:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` int NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `rating` decimal(3,1) DEFAULT '0.0',
  `review_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image`, `is_featured`, `rating`, `review_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'Monstera Deliciosa', 'The Swiss Cheese Plant is famous for its quirky natural leaf holes.', 39, 5, 'christmas.jpg', 1, 4.8, 24, '2025-03-09 13:07:55', '2025-05-09 05:40:01'),
(2, 1, 'Peace Lily', 'Elegant white flowers and glossy leaves that purify the air.', 29, 3, 'peacelily.jpg', 1, 4.7, 18, '2025-03-09 13:07:55', '2025-05-09 05:41:42'),
(3, 1, 'Snake Plant', 'Nearly indestructible, perfect for beginners and busy people.', 24, 40, 'bush.jpeg', 1, 4.9, 32, '2025-03-09 13:07:55', '2025-05-09 05:41:27'),
(4, 2, 'Hydrangea', 'Stunning flowering shrub with large, showy blooms.', 34, 15, 'flower1.jpg', 0, 4.6, 14, '2025-03-09 13:07:55', '2025-05-09 05:41:54'),
(5, 2, 'Lavender', 'Fragrant purple flowers that attract butterflies and bees.', 19, 35, 'outdoorplant.jpg', 1, 4.5, 22, '2025-03-09 13:07:55', '2025-05-09 05:42:20'),
(6, 1, 'Echeveria', 'Rosette-forming succulent with beautiful colors.', 14, 49, 'shevanti.jpg', 1, 4.7, 19, '2025-03-09 13:07:55', '2025-05-09 05:42:41'),
(7, 1, 'Aloe Vera', 'Medicinal plant with soothing gel inside its leaves.', 22, 45, 'Aloe_Vera_-1052-1015.jpg', 0, 4.8, 27, '2025-03-09 13:07:55', '2025-05-09 05:44:04'),
(8, 1, 'Basil', 'Essential culinary herb with aromatic leaves.', 9, 60, 'IMG_9165.JPG', 0, 4.6, 16, '2025-03-09 13:07:55', '2025-05-09 05:44:36'),
(9, 2, 'Mint', 'Fast-growing herb perfect for teas and cocktails.', 8, 55, 'mint-605937415.jpg', 0, 4.5, 13, '2025-03-09 13:07:55', '2025-05-09 05:45:12'),
(10, 1, 'Organic Plant Food', 'All-purpose fertilizer for healthy, vibrant plants.', 15, 63, 'Sumire.jpg', 0, 4.7, 21, '2025-03-09 13:07:55', '2025-05-09 08:17:49'),
(13, 6, 'Steel Watering Can', 'A handy tool for watering plants easily. Holds 1.ltr of water. Made out of stainless steel', 400, 15, 'watering can.jpg', 0, 0.0, 0, '2025-05-09 06:10:52', '2025-05-09 06:10:52'),
(14, 6, 'Shovel', 'Small Handy Shovel perfect for plant lovers.', 500, 20, 'shovel.jpeg', 0, 0.0, 0, '2025-05-09 06:12:14', '2025-05-09 06:12:14');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `reviews_ibfk_2` (`product_id`),
  KEY `reviews_ibfk_1` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `message`, `created_at`) VALUES
(1, 1, 1, 5, 'This Monstera is thriving in my living room! The leaves are huge and healthy. but it requres a lot of water im flooding my room', '2025-03-09 13:07:55'),
(2, 1, 2, 4, 'Beautiful plant, arrived in great condition. Took a while to adjust but doing well now.', '2025-03-09 13:07:55'),
(3, 2, 1, 5, 'My Peace Lily bloomed within a week of arrival! So beautiful.', '2025-03-09 13:07:55'),
(4, 3, 1, 5, 'This Snake Plant is practically indestructible. Perfect for my office.', '2025-03-09 13:07:55'),
(5, 4, 2, 4, 'Gorgeous hydrangea with big blue flowers. Needed some extra care at first.', '2025-03-09 13:07:55'),
(6, 5, 1, 4, 'Lovely lavender plant, smells amazing and attracts lots of bees to my garden.', '2025-03-09 13:07:55'),
(7, 11, 1, 4, 'very beautiful plant', '2025-05-06 08:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `address`, `city`, `state`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'Roshan', 'Magar', 'rosn030214@gmail.com', 'The123', '9865662544', '', NULL, NULL, 0, '2025-03-09 15:24:00', '2025-05-09 05:47:28'),
(2, 'Sudarshan', 'Sharma', 'sudarshan@gmail.com', '123123', '9836251478', '', NULL, NULL, 0, '2025-04-08 13:54:38', '2025-05-07 10:24:51'),
(4, 'admin', 'admin', 'admin@gmail.com', 'admin', NULL, NULL, NULL, NULL, 1, '2025-03-22 11:22:46', '2025-03-22 11:25:19'),
(6, 'demo', 'demo', 'demo@gmail.com', 'demo', NULL, NULL, NULL, NULL, 0, '2025-05-07 16:06:30', '2025-05-07 16:06:30');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
