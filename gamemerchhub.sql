-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 03:39 AM
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
-- Database: `gamemerchhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `delivery_status` enum('pending','shipped','delivered','cancelled') DEFAULT 'pending',
  `delivery_address` text DEFAULT NULL,
  `redeem_key` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'Credit_Card'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_status`, `delivery_status`, `delivery_address`, `redeem_key`, `created_at`, `discount_amount`, `payment_method`) VALUES
(1, 3, 515.94, 'completed', 'shipped', '31,Seattle,Washington', '', '2025-11-25 06:38:07', 0.00, 'PayPal');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_key` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `product_key`, `quantity`, `price`) VALUES
(1, 1, 'FaZe Clan Jersey', NULL, 6, 84.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `product_type` enum('digital','physical') DEFAULT 'physical',
  `created_at` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock_quantity`, `image_path`, `category`, `product_type`, `created_at`, `description`) VALUES
(1, 'God of War Ragnarök', 69.99, 50, './image/games/God_of_War_Ragnarök_cover.jpg', 'PS5 Games', 'physical', '2025-11-25 06:13:30', 'Kratos and Atreus continue their epic journey through the Nine Realms.'),
(2, 'Spider-Man 2', 69.99, 50, './image/games/spi2.jpg', 'PS5 Games', 'physical', '2025-11-25 06:13:30', 'Swing through New York as Spider-Man in this thrilling sequel.'),
(3, 'Horizon Forbidden West', 59.99, 50, './image/games/hfw.jpeg', 'PS5 Games', 'physical', '2025-11-25 06:13:30', 'Explore a post-apocalyptic world filled with robotic creatures.'),
(4, 'Halo Infinite', 59.99, 50, './image/games/heloin.jpeg', 'Xbox Games', 'physical', '2025-11-25 06:13:30', 'Master Chief returns in an epic sci-fi adventure.'),
(5, 'Forza Horizon 5', 59.99, 50, './image/games/fh5.jpg', 'Xbox Games', 'physical', '2025-11-25 06:13:30', 'Drive and race through the stunning landscapes of Mexico.'),
(6, 'Call of Duty: Black Ops 7', 59.99, 50, './image/games/codbo7.jpg', 'Xbox Games', 'physical', '2025-11-25 06:13:30', 'Intense multiplayer and gripping campaign missions await.'),
(7, 'Steam Wallet $20', 20.00, 100, './image/steam wallet/Steam-Gift-Card-20-us.png', 'Steam Wallet', 'digital', '2025-11-25 06:13:30', 'Add $20 to your Steam account for games or items.'),
(8, 'Steam Wallet $50', 50.00, 100, './image/steam wallet/Steam-Gift-Card-50-USA.png', 'Steam Wallet', 'digital', '2025-11-25 06:13:30', 'Add $50 to your Steam account for gaming content.'),
(9, 'Steam Wallet $95', 95.00, 100, './image/steam wallet/Steam gift card usd 95.png', 'Steam Wallet', 'digital', '2025-11-25 06:13:30', 'Add $95 to your Steam wallet for your favorite games.'),
(10, 'Team Liquid Jersey 2024', 79.99, 25, './image/Jersey/Liquid.jpg', 'Esports Jersey', 'physical', '2025-11-25 06:13:30', 'Official Team Liquid esports jersey for 2024.'),
(11, 'FaZe Clan Jersey', 84.99, 20, './image/Jersey/Faze.jpg', 'Esports Jersey', 'physical', '2025-11-25 06:13:30', 'Support FaZe Clan with this authentic jersey.'),
(12, 'Cloud9 Jersey', 79.99, 25, './image/Jersey/Cloud 9.jpg', 'Esports Jersey', 'physical', '2025-11-25 06:13:30', 'Official Cloud9 jersey for fans and players.'),
(13, 'Mario Figure Limited Edition', 149.99, 10, './image/figure/super-mario-bros-figure-nintendo-official-store-tokyo.png', 'Collectibles', 'physical', '2025-11-25 06:13:30', 'Limited edition Super Mario collectible figure.'),
(14, 'Pokemon Cards Rare Collection', 199.99, 10, './image/figure/rare-pokemon-cards-1.jpg', 'Collectibles', 'physical', '2025-11-25 06:13:30', 'Collection of rare Pokémon trading cards.'),
(15, 'Doom Slayer Action figure', 129.99, 10, './image/figure/3663641-doom slayer mini figure.jpeg', 'Collectibles', 'physical', '2025-11-25 06:13:30', 'Action figure of Doom Slayer from Doom franchise.'),
(16, 'Custom Gaming T-Shirt', 24.99, 20, './image/custom/start_konfi_esport.png', 'Custom Print', 'physical', '2025-11-25 06:13:30', 'Design your own gaming T-shirt with custom prints.'),
(17, 'Custom Mousepad', 19.99, 20, './image/custom/Custom_mouse_pad.jpg', 'Custom Print', 'physical', '2025-11-25 06:13:30', 'Personalize your mousepad for gaming or work.'),
(18, 'Custom Poster', 14.99, 20, './image/custom/poster.jpg', 'Custom Print', 'physical', '2025-11-25 06:13:30', 'Create a unique poster with your custom design.');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('open','resolved') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `member_since` date DEFAULT curdate(),
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `phone`, `address`, `member_since`, `role`, `status`) VALUES
(1, 'johndoakes', '$2y$10$W.Dzg/Bm5JlYQ9R80qbUH.OvQCF6TXL3kGEbA2D6SJux3NhvI5x5y', 'Jonathan Doakes', 'doakes313@gmail.com', '555-385-9999', '31,Seattle,Washington', '2025-11-25', 'user', 'active'),
(3, 'johndoakes55', '$2y$10$2vCDU4.FcYphLlM7OWAaKOfhQJAl7U66wFIXxRwhxltY2PNRmddQi', 'Jonathan Doakes', 'doakes33@gmail.com', '555-385-9999', '31,Seattle,Washington', '2025-11-25', 'user', 'active'),
(7, 'admin1', '$2y$10$c8Y1cKQBQPgxmLbn.QWMGu/2OcVoVI/lFxAyjFWF9EHpEunO93QB6', 'Admin One', 'admin1@gamemerchhub.com', NULL, NULL, '2025-11-25', 'admin', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
