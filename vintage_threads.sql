-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 02:29 PM
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
-- Database: `vintage_threads`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-09-10 22:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `content`, `excerpt`, `image_url`, `published`, `created_at`, `updated_at`) VALUES
(1, 'The Art of Vintage Fashion', 'the-art-of-vintage-fashion', '<p>Vintage fashion is more than just clothing - it\'s a window into the past, a statement of style, and a commitment to sustainable fashion...</p>', 'Discover the timeless appeal of vintage fashion and why it continues to captivate fashion enthusiasts worldwide.', NULL, 1, '2025-09-10 22:51:52', '2025-09-10 22:51:52'),
(2, 'How to Style Vintage Denim', 'how-to-style-vintage-denim', '<p>Vintage denim is a cornerstone of any well-curated wardrobe. From high-waisted jeans to classic denim jackets...</p>', 'Learn the secrets to incorporating vintage denim pieces into your modern wardrobe with style and confidence.', NULL, 1, '2025-09-10 22:51:52', '2025-09-10 22:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'T-Shirts', 't-shirts', 'Vintage and retro t-shirts', '2025-09-10 22:51:52'),
(2, 'Jeans', 'jeans', 'Classic denim and vintage jeans', '2025-09-10 22:51:52'),
(3, 'Hoodies', 'hoodies', 'Vintage hoodies and sweatshirts', '2025-09-10 22:51:52'),
(4, 'Jackets', 'jackets', 'Vintage jackets and outerwear', '2025-09-10 22:51:52'),
(5, 'Accessories', 'accessories', 'Vintage accessories and collectibles', '2025-09-10 22:51:52'),
(6, 'Shoes', 'shoes', 'Vintage and retro footwear', '2025-09-10 22:51:52'),
(7, 'New Arrivals', 'new-arrivals', 'Recently added vintage items', '2025-09-10 22:51:52'),
(8, 'Outlet', 'outlet', 'Discounted vintage clothing', '2025-09-10 22:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_number` varchar(20) DEFAULT NULL,
  `customer_address` text NOT NULL,
  `customer_city` varchar(100) NOT NULL,
  `customer_postal_code` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `order_token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `customer_number`, `customer_address`, `customer_city`, `customer_postal_code`, `total_amount`, `status`, `order_token`, `created_at`) VALUES
(10, 'Spasoja Stojkovski', 'spasojamk@gmail.com', '078998492', 'celopek', 'Tetovo', '1227', 45.00, 'pending', 'c5e6fe58fc9d745e54558fda36a941dc', '2025-09-11 15:16:33'),
(11, 'pero peroski', 'pero@gmail.com', '123456789', 'celopek', 'skopje', '1543', 370.00, 'cancelled', 'fb0f08b3c357206be851f5f91e39f7dd', '2025-09-11 15:26:20'),
(12, 'Spasoja Stojkovski', 'spasojamk@gmail.com', '075500000', 'celopek', 'Tetovo', '1227', 90.00, 'pending', '120b7a2f49d05250b0832c36d8df5b41', '2025-09-11 15:54:56'),
(13, 'Spasoja Stojkovski', 'pero@gmail.com', '075500000', '`123', 'Tetovo', '1543', 90.00, 'pending', 'b544b22cb89c1949d38641f4bd5c9bcf', '2025-09-11 15:58:18'),
(14, 'Spasoja Stojkovski', 'spasojamk@gmail.com', '075500000', 'asd', 'Tetovo', '1227', 45.00, 'pending', '87359fb512fff108525ae712d51e162c', '2025-09-11 16:00:13'),
(15, 'Spasoja Stojkovski', 'pero@gmail.com', '075500000', 'sad', 'Tetovo', '1227', 180.00, 'pending', 'c8db37b9e1b7af3a6b3223ca93f13536', '2025-09-11 16:36:58'),
(16, 'Spasoja Stojkovski', 'spasojamk@gmail.com', '075500000', 'sda', 'Tetovo', '1227', 8245.00, 'pending', '1d4ac8edd069cd1b8c7d0e228956343a', '2025-09-11 16:39:50');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 11, 1, 3, 45.00),
(2, 11, 2, 1, 85.00),
(3, 11, 3, 1, 150.00),
(4, 12, 2, 1, 85.00),
(5, 13, 2, 1, 85.00),
(6, 14, 1, 1, 45.00),
(7, 15, 1, 4, 45.00),
(8, 16, 2, 97, 85.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `sizes` varchar(255) DEFAULT NULL,
  `condition_notes` text DEFAULT NULL,
  `measurements` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock_S` int(10) UNSIGNED DEFAULT 0,
  `stock_M` int(10) UNSIGNED DEFAULT 0,
  `stock_L` int(10) UNSIGNED DEFAULT 0,
  `stock_XL` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `price`, `stock_quantity`, `category_id`, `image_url`, `sizes`, `condition_notes`, `measurements`, `is_featured`, `created_at`, `updated_at`, `stock_S`, `stock_M`, `stock_L`, `stock_XL`) VALUES
(1, 'Vintage Band T-Shirt', 'vintage-band-t-shirt', 'Authentic 80s band tour t-shirt in excellent condition', 45.00, 0, 1, '', 'S, M, L', 'Excellent vintage condition with minimal wear', '', 1, '2025-09-10 22:51:52', '2025-09-11 17:53:49', 0, 1, 1, 3),
(2, 'Classic 501 Jeans', 'classic-501-jeans', 'Vintage Levi&amp;#039;s 501 jeans with perfect fading', 85.00, 0, 2, '', '30, 32, 34', 'Great vintage condition with authentic wear patterns', '', 1, '2025-09-10 22:51:52', '2025-09-11 16:39:50', 0, 0, 0, 0),
(3, 'Retro Leather Jacket', 'retro-leather-jacket', 'Genuine leather jacket from the 70s', 150.00, 99, 4, '', 'M, L', 'Very good condition with beautiful patina', '', 1, '2025-09-10 22:51:52', '2025-09-11 15:26:20', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `shipping_rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`, `currency`, `shipping_rate`) VALUES
(1, 'Macedonia', 'MKD', 150.00),
(2, 'Europe', 'EUR', 10.00),
(3, 'USA', 'USD', 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `created_at`, `role`) VALUES
(1, 'Admin', 'admin.mk@admin.com', '$2y$10$OHdm/dH63uRK4R.Pe.5Q5ORDh.7jD5i7BPJMpAhF113cob3G1.wPy', '2025-09-11 14:31:35', 'admin'),
(2, 'Spasoja Stojkovski', 'spasojamk@gmail.com', '$2y$10$fC2DcHfrN8Zg47ekSRfhWe.DwVntdDX2hesLxHqbQQDA.sF6wHelu', '2025-09-11 14:36:31', 'user'),
(3, 'Nenad', 'nenad@gmail.com', '$2y$10$pbjvcdtJ3aMH.pOy7FcDdu1IOyz/V4fcM5eBUIrl89Uk8.ZbFwhkq', '2025-09-11 15:15:20', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_published` (`published`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
