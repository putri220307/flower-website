-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 02:24 PM
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
-- Database: `flower_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `product_id`, `user_id`, `comment`, `rating`, `created_at`, `updated_at`, `username`) VALUES
(1, 1, 1, 'Iy a, bunga ny a sangat cocok untuk dijadikan buket', 5, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(2, 1, 2, 'Rekomendasi yang bagus', 4, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(3, 1, 3, 'Bagus', 5, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(4, 2, 1, 'Wanginya bikin rileks', 5, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(5, 2, 2, 'Saya tidak terlalu suka wanginya', 3, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(6, 3, 3, 'Iyaaaa', 4, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(15, 3, 10, 'as', NULL, '2025-05-10 11:39:41', '2025-05-10 11:39:41', ''),
(16, 1, 10, 'aaas', NULL, '2025-05-11 06:52:06', '2025-05-11 06:52:06', ''),
(17, 1, 10, 'hh', NULL, '2025-05-11 06:55:32', '2025-05-11 06:55:32', ''),
(18, 10, 4, 'yayay', NULL, '2025-05-11 07:21:07', '2025-05-11 07:21:07', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `color`, `price`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Red Roses Bouquet', 'Red', 125000.00, 'Beautiful red roses perfect for romantic occasions', 'red-roses.jpg', '2025-05-05 11:35:00', '2025-05-05 11:35:00'),
(2, 'Purple Lavender', 'Purple', 95000.00, 'Fragrant lavender with calming aroma', 'lavender.jpg', '2025-05-05 11:35:00', '2025-05-05 11:35:00'),
(3, 'Liliy', 'White', 110000.00, 'Elegant white lilies for special events', 'white-lilies.jpg', '2025-05-05 11:35:00', '2025-05-08 03:26:54'),
(5, 'Tulip', 'Pink', 120000.00, 'Delicate pink tulips for spring arrangements', 'tulip.jpg', '2025-05-05 11:35:00', '2025-05-08 03:33:11'),
(7, 'Dandelions', 'Yellow', 100000.00, 'Dandelions are bright yellow flowers known for their resilience and unique life cycle. Belonging to the Taraxacum genus, these cheerful blooms often appear in spring and can grow almost anywhere—from meadows to sidewalks. Each dandelion head is made up of numerous tiny florets, creating a vivid display that later transforms into a delicate, white puffball of seeds. ', 'dandelions.jpg', '2025-05-08 00:24:53', '2025-05-08 00:24:53'),
(8, 'Angrek', 'purple', 1000.00, '– Keindahan Elegan yang Abadi Bunga anggrek dikenal karena keindahan dan keanggunannya. Dengan berbagai warna dan bentuk yang unik, anggrek menjadi pilihan favorit untuk dekorasi rumah dan hadiah istimewa. Tanaman ini memerlukan perawatan khusus, seperti pencahayaan yang cukup, kelembapan yang stabil, serta penyiraman yang tidak berlebihan. ✨ Keunggulan: ✔️ Tampilan elegan dan eksotis ✔️ Daya tahan bunga cukup lama ✔️ Melambangkan keindahan dan kesempurnaan', 'anggrek.jpg', '2025-05-08 03:31:13', '2025-05-08 03:31:13'),
(9, 'Daisy', 'White', 100000.00, '– Simbol Keceriaan & Kemurnian Bunga Daisy dikenal dengan kelopaknya yang indah dan pusatnya yang cerah, melambangkan kepolosan, keceriaan, dan awal yang baru. Cocok untuk hadiah spesial atau sebagai dekorasi yang menyegarkan ruangan. Dengan daya tahan yang baik, Daisy memberikan sentuhan alami yang manis dan menawan di setiap kesempatan', 'daisy.jpg', '2025-05-08 03:31:13', '2025-05-08 03:31:13'),
(10, 'Baby breath', 'White', 100000.00, 'Baby’s Breath adalah bunga kecil dan halus yang sering digunakan sebagai pelengkap dalam rangkaian bunga, terutama buket pernikahan. Meskipun mungil, kehadirannya memberi kesan lembut, manis, dan romantis.', 'baby breath.jpg', '2025-05-08 03:35:31', '2025-05-08 03:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `profile_picture`, `created_at`, `updated_at`) VALUES
(1, 'zakki', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igo', 'zakki@example.com', 'Zakki Pratama', NULL, '2025-05-05 11:35:14', '2025-05-10 10:58:03'),
(2, 'putri', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igp\r\n', 'putri@example.com', 'Putri Anggraeni', NULL, '2025-05-05 11:35:14', '2025-05-10 10:58:12'),
(3, 'refi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igc', 'refi@example.com', 'Refi Ahmad', NULL, '2025-05-05 11:35:14', '2025-05-10 10:58:20'),
(4, 'imun', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igz', 'imun@example', 'imunisasi', '68205c9ae3d85_tulip.jpeg', '2025-05-08 05:49:31', '2025-05-11 08:15:22'),
(5, 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igaa', '', NULL, NULL, '2025-05-08 12:42:33', '2025-05-10 10:58:36'),
(10, 'user1', 'user123', 'user@example', 'user', 'user.png', '2025-05-10 11:15:52', '2025-05-10 11:15:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
