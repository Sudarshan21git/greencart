-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2025 at 03:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enursery`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `userId` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `productId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`userId`, `id`, `quantity`, `productId`) VALUES
(12, 293, 2, 0),
(19, 302, 3, 0),
(28, 314, 31, 0),
(30, 317, 6, 0),
(27, 352, 1, 106);

-- --------------------------------------------------------

--
-- Table structure for table `customeruser`
--

CREATE TABLE `customeruser` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `role` int(11) NOT NULL,
  `address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customeruser`
--

INSERT INTO `customeruser` (`id`, `username`, `phone`, `Email`, `Password`, `role`, `address`) VALUES
(12, 'sudarshan', '9847749680', 'sudarshansharma123@gmail.com', 'Sudarshan123', 1, 'Pokhara'),
(13, 'sulav', '9825254584', 'sulav@gmail.com', 'Sulav123', 1, 'Kathmandu'),
(27, 'samikshya', '9816191446', 'samikshya12@gmail.com', 'Kafle123', 0, 'butwal'),
(28, 'sujan', '9847643527', 'sujan123@gmail.com', 'Sujan123', 0, 'Kathmandu'),
(29, 'ritu', '9816191448', 'ritu123@gmail.com', 'Ritu123', 0, 'Kathmandu');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `phno` bigint(20) NOT NULL,
  `location` varchar(40) NOT NULL,
  `email` varchar(40) NOT NULL,
  `method` varchar(40) NOT NULL,
  `total_products` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `u_id`, `name`, `phno`, `location`, `email`, `method`, `total_products`, `total_price`, `status`) VALUES
(248, 26, 'roshan', 9847643523, 'Kathmandu', 'roshan145@gmail.com', 'cash on delivery', 0, 3150, 3),
(250, 26, 'roshan', 9847643523, 'Kathmandu', 'roshan145@gmail.com', 'cash on delivery', 0, 1500, 2),
(251, 26, 'roshan', 9847643523, 'Kathmandu', 'roshan145@gmail.com', 'cash on delivery', 0, 400, 2),
(252, 29, 'ritu', 9816191448, 'Kathmandu', 'ritu123@gmail.com', 'cash on delivery', 0, 1950, 2),
(253, 27, 'samikshya', 9816191446, 'Kathmandu', 'samikshya12@gmail.com', 'cash on delivery', 0, 1000, 2),
(254, 27, 'samikshya', 9816191446, 'Kathmandu', 'samikshya12@gmail.com', 'cash on delivery', 0, 500, 2),
(255, 27, 'samikshya', 9816191446, 'Kathmandu', 'samikshya12@gmail.com', 'cash on delivery', 0, 2500, 2),
(256, 29, 'ritu', 9816191448, 'Kathmandu', 'ritu123@gmail.com', 'cash on delivery', 0, 3250, 2),
(257, 29, 'ritu', 9816191448, 'Kathmandu', 'ritu123@gmail.com', 'cash on delivery', 0, 450, 1),
(258, 29, 'ritu', 9816191448, 'Kathmandu', 'ritu123@gmail.com', 'cash on delivery', 0, 500, 1),
(259, 29, 'ritu', 9816191448, 'Kathmandu', 'ritu123@gmail.com', 'cash on delivery', 0, 5000, 1),
(260, 27, 'samikshya', 9816191446, 'Kathmandu', 'samikshya12@gmail.com', 'cash on delivery', 0, 2200, 1),
(261, 27, 'samikshya', 9816191446, 'Kathmandu', 'samikshya12@gmail.com', 'cash on delivery', 0, 800, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 251, 93, 1, 400),
(2, 252, 105, 3, 650),
(3, 253, 104, 1, 1000),
(4, 254, 103, 1, 500),
(5, 255, 103, 5, 500),
(6, 256, 103, 2, 500),
(7, 256, 106, 5, 450),
(8, 257, 106, 1, 450),
(9, 258, 103, 1, 500),
(10, 259, 104, 5, 1000),
(11, 260, 106, 4, 450),
(12, 260, 93, 1, 400),
(13, 261, 93, 2, 400);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `desc` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `stock`, `desc`, `created_at`) VALUES
(93, 'Black Rose', 400, 'Black rose.jpg', 0, 'Black roses, with their mysterious allure and deep, velvety petals, symbolize rebirth and new beginn', '2025-02-07 00:46:08'),
(95, 'Jacaranda', 1000, 'Jacaranda.jpg', 0, 'Jacaranda trees, with their vibrant purple flowers, add stunning beauty to any landscape', '2025-02-07 00:46:08'),
(103, 'Red Rose', 500, 'Rose.jpg', 83, 'A Red Rose typically symbolizes love, passion, and romance. Its a classic symbol of affection and is', '2025-02-07 00:46:08'),
(104, 'Sumire', 1000, 'Sumire.jpg', 54, 'Sumire, a delicate flower native to Japan, symbolizes beauty, grace, and humility.', '2025-02-07 00:46:08'),
(106, 'Sakura', 450, 'sakura.jpg', 31, 'Sakura: A fleeting beauty, embodying natures delicate grace and ephemeral charm.', '2025-02-07 00:46:08'),
(108, 'Rosessssssssssssss', 400000, '1024.png', 40, 'ssss', '2025-02-07 19:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `product_id`, `order_id`, `rating`, `created_at`) VALUES
(1, 27, 103, 254, 2, '2025-02-08 06:30:14'),
(2, 27, 103, 254, 2, '2025-02-08 06:32:13'),
(3, 27, 103, 254, 2, '2025-02-08 06:48:26'),
(4, 27, 104, 253, 2, '2025-02-08 06:48:49'),
(5, 27, 104, 253, 1, '2025-02-08 06:49:51'),
(6, 27, 104, 253, 4, '2025-02-08 06:50:40'),
(7, 27, 104, 253, 3, '2025-02-08 06:56:25'),
(8, 27, 104, 253, 3, '2025-02-08 06:56:43'),
(9, 27, 104, 253, 2, '2025-02-08 06:58:00'),
(10, 27, 104, 253, 2, '2025-02-08 06:58:23'),
(11, 27, 103, 254, 2, '2025-02-08 07:06:06'),
(12, 27, 104, 253, 2, '2025-02-08 07:07:09'),
(13, 29, 106, 256, 3, '2025-02-08 07:07:27'),
(14, 29, 106, 256, 3, '2025-02-08 07:12:35'),
(15, 29, 103, 256, 2, '2025-02-08 07:14:09'),
(16, 29, 106, 256, 3, '2025-02-08 07:17:42'),
(17, 29, 106, 256, 3, '2025-02-08 07:17:42'),
(18, 29, 106, 256, 3, '2025-02-08 07:18:52'),
(19, 29, 106, 256, 5, '2025-02-10 05:31:46'),
(20, 29, 106, 256, 3, '2025-02-10 05:33:02'),
(21, 27, 103, 254, 5, '2025-02-13 05:15:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customeruser`
--
ALTER TABLE `customeruser`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT for table `customeruser`
--
ALTER TABLE `customeruser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
