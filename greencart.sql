-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2025 at 06:53 AM
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Indoor Plants', 'Beautiful plants for your home interior', 'category-indoor.jpg', '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(2, 'Outdoor Plants', 'Hardy plants for your garden and patio', 'category-outdoor.jpg', '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(3, 'Succulents', 'Low-maintenance plants for busy plant lovers', 'category-succulents.jpg', '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(4, 'Herbs', 'Aromatic and edible plants for your kitchen', 'category-herbs.jpg', '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(5, 'Plant Care', 'Everything you need to keep your plants healthy', 'category-care.jpg', '2025-03-09 13:07:55', '2025-03-09 13:07:55');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_address` text NOT NULL,
  `billing_address` text NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `rating` decimal(3,1) DEFAULT '0.0',
  `review_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image`, `is_featured`, `rating`, `review_count`, `created_at`, `updated_at`) VALUES
(1, 1, 'Monstera Deliciosa', 'The Swiss Cheese Plant is famous for its quirky natural leaf holes.', 39.99, 25, 'monstera.jpg', 1, 4.8, 24, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(2, 1, 'Peace Lily', 'Elegant white flowers and glossy leaves that purify the air.', 29.99, 30, 'peace-lily.jpg', 1, 4.7, 18, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(3, 1, 'Snake Plant', 'Nearly indestructible, perfect for beginners and busy people.', 24.99, 40, 'snake-plant.jpg', 1, 4.9, 32, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(4, 2, 'Hydrangea', 'Stunning flowering shrub with large, showy blooms.', 34.99, 15, 'hydrangea.jpg', 0, 4.6, 14, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(5, 2, 'Lavender', 'Fragrant purple flowers that attract butterflies and bees.', 19.99, 35, 'lavender.jpg', 1, 4.5, 22, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(6, 3, 'Echeveria', 'Rosette-forming succulent with beautiful colors.', 14.99, 50, 'echeveria.jpg', 1, 4.7, 19, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(7, 3, 'Aloe Vera', 'Medicinal plant with soothing gel inside its leaves.', 22.99, 45, 'aloe-vera.jpg', 0, 4.8, 27, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(8, 4, 'Basil', 'Essential culinary herb with aromatic leaves.', 9.99, 60, 'basil.jpg', 0, 4.6, 16, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(9, 4, 'Mint', 'Fast-growing herb perfect for teas and cocktails.', 8.99, 55, 'mint.jpg', 0, 4.5, 13, '2025-03-09 13:07:55', '2025-03-09 13:07:55'),
(10, 5, 'Organic Plant Food', 'All-purpose fertilizer for healthy, vibrant plants.', 15.99, 70, 'plant-food.jpg', 0, 4.7, 21, '2025-03-09 13:07:55', '2025-03-09 13:07:55');

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
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `message`, `created_at`) VALUES
(1, 1, 1, 5, 'This Monstera is thriving in my living room! The leaves are huge and healthy.', '2025-03-09 13:07:55'),
(2, 1, 2, 4, 'Beautiful plant, arrived in great condition. Took a while to adjust but doing well now.', '2025-03-09 13:07:55'),
(3, 2, 1, 5, 'My Peace Lily bloomed within a week of arrival! So beautiful.', '2025-03-09 13:07:55'),
(4, 3, 3, 5, 'This Snake Plant is practically indestructible. Perfect for my office.', '2025-03-09 13:07:55'),
(5, 4, 2, 4, 'Gorgeous hydrangea with big blue flowers. Needed some extra care at first.', '2025-03-09 13:07:55'),
(6, 5, 1, 4, 'Lovely lavender plant, smells amazing and attracts lots of bees to my garden.', '2025-03-09 13:07:55');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `address`, `city`, `state`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'Roshan', 'Jhendi', 'rosn030214@gmail.com', 'The123', NULL, NULL, NULL, NULL, 0, '2025-03-09 15:24:00', '2025-03-09 15:24:00'),
(3, 'Sudarshan', 'Sharma', 'sudarshan@gmail.com', 'Sudarshan123', NULL, NULL, NULL, NULL, 0, '2025-03-09 15:27:52', '2025-03-09 15:27:52');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
