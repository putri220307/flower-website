-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 01:02 AM
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
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-14 04:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 1, 2, 'Rekomendasi yang bagus', 4, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(3, 1, 3, 'Bagus', 5, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(5, 2, 2, 'Saya tidak terlalu suka wanginya', 3, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(6, 3, 3, 'Iyaaaa', 4, '2025-05-05 11:35:30', '2025-05-05 11:35:30', ''),
(15, 3, 10, 'as', NULL, '2025-05-10 11:39:41', '2025-05-10 11:39:41', ''),
(16, 1, 10, 'aaas', NULL, '2025-05-11 06:52:06', '2025-05-11 06:52:06', ''),
(17, 1, 10, 'hh', NULL, '2025-05-11 06:55:32', '2025-05-11 06:55:32', ''),
(19, 5, 10, 'p', NULL, '2025-05-11 17:03:20', '2025-05-11 17:03:20', ''),
(20, 3, 10, 'i', NULL, '2025-05-14 06:05:40', '2025-05-14 06:05:40', ''),
(21, 3, 10, 'ppp', NULL, '2025-05-14 06:49:32', '2025-05-14 06:49:32', ''),
(23, 2, 2, 'iyaaaaaaaaaa', NULL, '2025-05-15 01:06:07', '2025-05-15 01:06:07', ''),
(24, 1, 10, 'pppp', NULL, '2025-05-15 01:30:06', '2025-05-15 01:30:06', ''),
(25, 2, 26, 'bbbbbb', NULL, '2025-05-17 08:49:27', '2025-05-17 08:49:27', ''),
(26, 3, 10, 'pppp', NULL, '2025-05-18 14:43:22', '2025-05-18 14:43:22', ''),
(27, 5, 38, 'iyaaa', NULL, '2025-05-18 14:44:07', '2025-05-18 14:44:07', ''),
(28, 11, 10, 'yayayayay', NULL, '2025-05-19 02:15:56', '2025-05-19 02:15:56', '');

-- --------------------------------------------------------

--
-- Table structure for table `flowers`
--

CREATE TABLE `flowers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(50) NOT NULL DEFAULT 'produk',
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `color`, `price`, `description`, `image`, `created_at`, `updated_at`, `category`, `image_path`) VALUES
(1, 'Mawar', 'Red', 125000.00, '– Simbol Cinta dan Keindahan ❤️🌹 Mawar merah adalah bunga klasik yang melambangkan cinta, gairah, dan keanggunan. Dengan kelopaknya yang lembut dan warna merah yang menawan, bunga ini sempurna untuk mengungkapkan perasaan mendalam kepada orang terkasih. Cocok untuk hadiah romantis, perayaan spesial, atau sekadar memperindah ruangan dengan keharumannya yang khas', 'red-roses.jpg', '2025-05-05 11:35:00', '2025-05-20 10:22:25', 'produk', NULL),
(2, 'Purple Lavender', 'Purple', 95000.00, '– Keindahan dan Ketenangan dalam Setiap Tangkainya Lavender dengan warna ungu memikat dan aroma menenangkan, sempurna untuk dekorasi rumah atau hadiah. Memberikan sentuhan elegan dan suasana damai di setiap ruang. Keunggulan: 🌿 Aroma menenangkan dan menyegarkan 💜 Cocok untuk dekorasi dan pengharum alami 🌱 Mudah dirawat di iklim hangat dan kering', 'lavender.jpg', '2025-05-05 11:35:00', '2025-05-11 16:09:07', 'produk', NULL),
(3, 'Liliy', 'White', 110000.00, '– Keanggunan dalam Setiap Kelopak Bunga Lily melambangkan kemurnian, keanggunan, dan cinta yang tulus. Dengan kelopaknya yang lembut dan aroma yang memikat, Lily menjadi pilihan sempurna untuk berbagai momen spesial, seperti perayaan, ungkapan kasih sayang, atau sekadar mempercantik ruangan.', 'white-lilies.jpg', '2025-05-05 11:35:00', '2025-05-11 16:10:00', 'produk', NULL),
(5, 'Tulip', 'Pink', 120000.00, '– Simbol Keindahan & Cinta 🌷 Tulip adalah bunga yang elegan dengan kelopak lembut dan warna-warna memikat. Melambangkan cinta, kebahagiaan, dan harapan, bunga ini cocok untuk berbagai momen istimewa. Dari merah yang melambangkan cinta sejati hingga kuning yang membawa keceriaan, setiap warna memiliki maknanya sendiri. Jadikan tulip sebagai hadiah yang sempurna untuk orang tersayang atau hiasan indah di ruangan Anda. Segar, menawan, dan penuh makna—tulip selalu menghadirkan kehangatan di setiap kesempatan. 💐✨', 'tulip.jpg', '2025-05-05 11:35:00', '2025-05-11 16:14:39', 'produk', NULL),
(8, 'Angrek', 'purple', 1000.00, '– Keindahan Elegan yang Abadi Bunga anggrek dikenal karena keindahan dan keanggunannya. Dengan berbagai warna dan bentuk yang unik, anggrek menjadi pilihan favorit untuk dekorasi rumah dan hadiah istimewa. Tanaman ini memerlukan perawatan khusus, seperti pencahayaan yang cukup, kelembapan yang stabil, serta penyiraman yang tidak berlebihan. ✨ Keunggulan: ✔️ Tampilan elegan dan eksotis ✔️ Daya tahan bunga cukup lama ✔️ Melambangkan keindahan dan kesempurnaan', 'anggrek.jpg', '2025-05-08 03:31:13', '2025-05-08 03:31:13', 'produk', NULL),
(9, 'Daisy', 'White', 100000.00, '– Simbol Keceriaan & Kemurnian Bunga Daisy dikenal dengan kelopaknya yang indah dan pusatnya yang cerah, melambangkan kepolosan, keceriaan, dan awal yang baru. Cocok untuk hadiah spesial atau sebagai dekorasi yang menyegarkan ruangan. Dengan daya tahan yang baik, Daisy memberikan sentuhan alami yang manis dan menawan di setiap kesempatan', 'daisy.jpg', '2025-05-08 03:31:13', '2025-05-08 03:31:13', 'produk', NULL),
(10, 'Baby breath', 'White', 100000.00, '– Simbol Keindahan dan Ketulusan Baby Breath dikenal dengan bunga kecilnya yang lembut dan anggun, melambangkan cinta yang tulus, kesederhanaan, dan ketulusan hati. Bunga ini sering digunakan dalam rangkaian buket pernikahan, hadiah spesial, atau dekorasi elegan yang memberikan sentuhan klasik dan menenangkan. Dengan warna putih yang menawan dan daya tahan yang lama, Baby Breath adalah pilihan sempurna untuk mengungkapkan perasaan yang murni dan abadi. 🌿✨', 'baby breath.jpg', '2025-05-08 03:35:31', '2025-05-11 16:13:36', 'produk', NULL),
(11, 'Peony', 'Pink', 10000.00, '– Keindahan & Kemewahan dalam Setiap Kelopak Peony adalah simbol kemewahan, cinta, dan keberuntungan. Dengan kelopak yang lembut dan berlapis, bunga ini memancarkan keanggunan yang sempurna untuk setiap momen spesial. Baik sebagai hadiah romantis, dekorasi pernikahan, atau pemanis ruangan, Peony selalu menghadirkan kehangatan dan keindahan.', 'peony.jpg', '2025-05-11 16:11:56', '2025-05-11 16:11:56', 'produk', NULL),
(13, 'Dahlia', 'ungu', 0.00, 'Bunga Dahlia adalah bunga yang memukau dengan bentuknya yang unik dan warna-warna cerah yang beragam. Berasal dari Meksiko dan Amerika Tengah, bunga ini termasuk dalam keluarga Asteraceae dan memiliki nama ilmiah Dahlia pinnata. Dahlia dikenal karena kelopaknya yang tersusun rapat dan simetris, menyerupai pom-pom atau mahkota bunga yang anggun.\r\n\r\nBunga dahlia hadir dalam berbagai warna seperti merah, ungu, kuning, oranye, putih, dan bahkan kombinasi warna, menjadikannya favorit dalam dunia pertamanan dan dekorasi. Selain itu, ukuran bunganya juga bervariasi, dari yang kecil berdiameter beberapa sentimeter hingga yang besar seperti piring makan.', 'dahlia.jpg\r\n', '2025-05-20 13:59:15', '2025-05-20 14:56:17', 'bunga', 'uploads/flowers/682c8ab394651.jpg'),
(14, 'Sedap malam', 'putih', 0.00, 'Bunga Sedap Malam (nama ilmiah: Polianthes tuberosa) adalah bunga berwarna putih yang dikenal karena aromanya yang harum dan kuat, terutama saat malam hari. Karena itu, bunga ini sering dikaitkan dengan suasana yang tenang, romantis, bahkan mistis dalam beberapa budaya.', 'sedap malam.jpg', '2025-05-20 14:26:46', '2025-05-20 15:16:19', 'bunga', '/assets/images/products/682c9126904dc.jpg');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin') DEFAULT 'user',
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `profile_picture`, `created_at`, `updated_at`, `role`, `is_verified`) VALUES
(2, 'putri', 'user123', 'putri@example.com', 'puput', '68243e145dfb9_profile.jpg', '2025-05-05 11:35:14', '2025-05-17 03:55:24', 'user', 1),
(3, 'refi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igc', 'refi@example.com', 'Refi Ahmad', NULL, '2025-05-05 11:35:14', '2025-05-17 03:55:25', 'user', 1),
(10, 'user1', 'user123', 'user@example', 'user', '682c96f4aed8f_dahlia.jpg', '2025-05-10 11:15:52', '2025-05-20 14:51:32', 'user', 1),
(12, 'admin', 'admin123', 'admin@example.com', NULL, NULL, '2025-05-14 05:21:29', '2025-05-17 03:55:13', 'admin', 1),
(14, 'zaki', 'user123', 'zakki@gmail.com', 'zakki bilqis', NULL, '2025-05-17 04:20:30', '2025-05-17 04:36:51', 'user', 1),
(26, 'angga', 'user123', 'angga@gmail.com', 'angga p', NULL, '2025-05-17 08:45:40', '2025-05-17 08:48:22', 'user', 1),
(33, 'imunisasi', 'imun2007', 'zakki.bilqia77@gmail.com', NULL, NULL, '2025-05-18 10:53:11', '2025-05-18 10:56:38', 'user', 1),
(34, 'firman', 'user123', 'firman@gmail.com', NULL, NULL, '2025-05-18 11:00:05', '2025-05-18 11:00:44', 'user', 1),
(35, 'lita', 'user123', 'lita@gmail.com', NULL, NULL, '2025-05-18 11:11:08', '2025-05-18 11:27:00', 'user', 1),
(36, 'iputtaja', 'user123', 'iput@gmail.com', NULL, NULL, '2025-05-18 12:10:38', '2025-05-18 12:10:53', 'user', 1),
(37, 'ahnaf', 'user123', 'ahnaf@gmail.com', NULL, NULL, '2025-05-18 12:28:24', '2025-05-18 12:30:32', 'user', 1),
(38, 'zubaidah', 'putri220307', 'zubaidah@gmail.com', NULL, NULL, '2025-05-18 13:46:09', '2025-05-18 13:47:14', 'user', 1),
(39, 'bilqis', 'bilqis2007', 'bilqis@gmail.com', NULL, NULL, '2025-05-18 14:50:12', '2025-05-18 14:51:22', 'user', 1),
(40, 'audrey', 'audrey123', 'audrey@gmail.com', NULL, '682a72f24f100_logo-coffee-574x630.jpg', '2025-05-18 23:50:28', '2025-05-18 23:53:22', 'user', 1),
(41, 'galih', 'galih123', 'galih@gmail.com', NULL, NULL, '2025-05-19 06:37:51', '2025-05-19 06:38:02', 'user', 1),
(42, 'maulana', 'maulana123', 'maulana@gmail.com', NULL, NULL, '2025-05-20 06:47:07', '2025-05-20 06:47:29', 'user', 1),
(43, 'agung', 'agung123', 'agung@gmail.com', NULL, NULL, '2025-05-20 12:25:42', '2025-05-20 12:25:59', 'user', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `flowers`
--
ALTER TABLE `flowers`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `flowers`
--
ALTER TABLE `flowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
