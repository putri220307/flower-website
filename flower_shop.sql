-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Jul 2025 pada 03.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `activity_log`
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
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-14 04:33:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
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
-- Dumping data untuk tabel `comments`
--

INSERT INTO `comments` (`id`, `product_id`, `user_id`, `comment`, `rating`, `created_at`, `updated_at`, `username`) VALUES
(39, 1, 48, 'pppp', NULL, '2025-07-16 12:19:17', '2025-07-16 12:19:17', ''),
(40, 16, 49, 'yahuuu', NULL, '2025-07-17 11:31:24', '2025-07-17 11:31:24', ''),
(41, 16, 47, 'wwdwdwdw', NULL, '2025-07-18 01:43:21', '2025-07-18 01:43:21', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `flowers`
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
-- Struktur dari tabel `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(3, 48, 1, '2025-07-17 12:14:12'),
(4, 48, 2, '2025-07-17 12:14:13'),
(5, 48, 16, '2025-07-17 12:14:14'),
(6, 47, 16, '2025-07-18 01:43:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
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
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `color`, `price`, `description`, `image`, `created_at`, `updated_at`, `category`, `image_path`) VALUES
(1, 'Sedap malam', 'white', 0.00, 'Bunga Sedap Malam (nama ilmiah: Polianthes tuberosa) adalah bunga berwarna putih yang dikenal karena aromanya yang harum dan kuat, terutama saat malam hari. Karena itu, bunga ini sering dikaitkan dengan suasana yang tenang, romantis, bahkan mistis dalam beberapa budaya.', 'sedap malam.jpg', '2025-05-20 14:26:46', '2025-05-22 02:10:02', 'bunga', '/assets/images/products/682c9126904dc.jpg'),
(2, 'Melati', 'white', 0.00, 'Cocok untuk acara pernikahan atau pemakaman atau nelayat ke kuburan', '', '2025-05-22 00:05:29', '2025-05-22 02:11:50', 'bunga', '/uploads/admin/flowers/682e6ace399fd.jpg'),
(16, 'Mawar', 'red', 0.00, '– Simbol Cinta dan Keindahan ❤️???? Mawar merah adalah bunga klasik yang melambangkan cinta, gairah, dan keanggunan. Dengan kelopaknya yang lembut dan warna merah yang menawan, bunga ini sempurna untuk mengungkapkan perasaan mendalam kepada orang terkasih. Cocok untuk hadiah romantis, perayaan spesial, atau sekadar memperindah ruangan dengan keharumannya yang khas', '', '2025-05-22 01:43:22', '2025-05-22 02:11:50', 'bunga', 'assets/images/products/682e813a069bc.jpeg'),
(17, 'Lavender', 'purple', 0.00, '– Keindahan dan Ketenangan dalam Setiap Tangkainya Lavender dengan warna ungu memikat dan aroma menenangkan, sempurna untuk dekorasi rumah atau hadiah. Memberikan sentuhan elegan dan suasana damai di setiap ruang. Keunggulan: ???? Aroma menenangkan dan menyegarkan ???? Cocok untuk dekorasi dan pengharum alami ???? Mudah dirawat di iklim hangat dan kering', '', '2025-05-22 02:01:38', '2025-05-22 02:11:50', 'bunga', 'assets/images/products/682e858222e92.jpeg'),
(18, 'Liliy', 'white', 0.00, '– Keanggunan dalam Setiap Kelopak Bunga Lily melambangkan kemurnian, keanggunan, dan cinta yang tulus. Dengan kelopaknya yang lembut dan aroma yang memikat, Lily menjadi pilihan sempurna untuk berbagai momen spesial, seperti perayaan, ungkapan kasih sayang, atau sekadar mempercantik ruangan.', '', '2025-05-22 02:02:15', '2025-05-22 02:11:50', 'bunga', 'assets/images/products/682e85a73386d.jpeg'),
(19, 'Tulip', 'pink', 0.00, '– Simbol Keindahan & Cinta ???? Tulip adalah bunga yang elegan dengan kelopak lembut dan warna-warna memikat. Melambangkan cinta, kebahagiaan, dan harapan, bunga ini cocok untuk berbagai momen istimewa. Dari merah yang melambangkan cinta sejati hingga kuning yang membawa keceriaan, setiap warna memiliki maknanya sendiri. Jadikan tulip sebagai hadiah yang sempurna untuk orang tersayang atau hiasan indah di ruangan Anda. Segar, menawan, dan penuh makna—tulip selalu menghadirkan kehangatan di setiap kesempatan. ????✨', '', '2025-05-22 02:02:45', '2025-05-22 02:02:45', 'bunga', 'assets/images/products/682e85c5c2a9a.jpeg'),
(20, 'Angrek', 'purple', 0.00, '– Keindahan Elegan yang Abadi Bunga anggrek dikenal karena keindahan dan keanggunannya. Dengan berbagai warna dan bentuk yang unik, anggrek menjadi pilihan favorit untuk dekorasi rumah dan hadiah istimewa. Tanaman ini memerlukan perawatan khusus, seperti pencahayaan yang cukup, kelembapan yang stabil, serta penyiraman yang tidak berlebihan. ✨ Keunggulan: ✔️ Tampilan elegan dan eksotis ✔️ Daya tahan bunga cukup lama ✔️ Melambangkan keindahan dan kesempurnaan', '', '2025-05-22 02:03:13', '2025-05-22 02:11:50', 'bunga', 'assets/images/products/682e85e141ff1.jpeg'),
(21, 'Daisy', 'white', 0.00, '– Simbol Keceriaan & Kemurnian Bunga Daisy dikenal dengan kelopaknya yang indah dan pusatnya yang cerah, melambangkan kepolosan, keceriaan, dan awal yang baru. Cocok untuk hadiah spesial atau sebagai dekorasi yang menyegarkan ruangan. Dengan daya tahan yang baik, Daisy memberikan sentuhan alami yang manis dan menawan di setiap kesempatan', '', '2025-05-22 02:03:51', '2025-05-22 02:11:50', 'bunga', 'assets/images/products/682e8607bc36b.jpeg'),
(22, 'Baby breath', 'white', 0.00, '– Simbol Keindahan dan Ketulusan Baby Breath dikenal dengan bunga kecilnya yang lembut dan anggun, melambangkan cinta yang tulus, kesederhanaan, dan ketulusan hati. Bunga ini sering digunakan dalam rangkaian buket pernikahan, hadiah spesial, atau dekorasi elegan yang memberikan sentuhan klasik dan menenangkan. Dengan warna putih yang menawan dan daya tahan yang lama, Baby Breath adalah pilihan sempurna untuk mengungkapkan perasaan yang murni dan abadi. ????✨', '', '2025-05-22 02:04:16', '2025-05-22 02:11:50', 'bunga', 'assets/images/products/682e8620aef0f.jpeg'),
(23, 'Peony', 'pink', 0.00, '– Keindahan & Kemewahan dalam Setiap Kelopak Peony adalah simbol kemewahan, cinta, dan keberuntungan. Dengan kelopak yang lembut dan berlapis, bunga ini memancarkan keanggunan yang sempurna untuk setiap momen spesial. Baik sebagai hadiah romantis, dekorasi pernikahan, atau pemanis ruangan, Peony selalu menghadirkan kehangatan dan keindahan.', '', '2025-05-22 02:04:42', '2025-05-22 02:04:42', 'bunga', 'assets/images/products/682e863a1d9bc.jpeg'),
(24, 'Matahari', 'kuning', 0.00, 'Bunga matahari adalah tumbuhan berbunga dari famili Asteraceae yang dikenal karena bentuknya yang menyerupai matahari. Bunga ini memiliki mahkota besar berwarna kuning cerah dengan bagian tengah berwarna cokelat atau kehitaman yang berisi biji.\r\n\r\nCiri khas utama bunga matahari adalah perilaku heliotropisme, yaitu kemampuannya mengikuti arah matahari dari timur ke barat saat masih muda. Seiring pertumbuhan, bunga dewasa akan menghadap ke arah timur secara permanen.', '', '2025-05-22 02:39:43', '2025-05-22 02:39:43', 'bunga', 'assets/images/products/682e8e6f188b3.jpg'),
(25, 'bunga sepatu', 'merah', 0.00, 'Bunga Sepatu adalah tanaman hias berbunga besar dan mencolok, biasanya berwarna merah, tetapi juga bisa kuning, putih, atau ungu. Nama ilmiahnya Hibiscus rosa-sinensis. Bunga ini memiliki lima kelopak dan benang sari panjang yang menonjol. Selain indah, bunga sepatu juga bermanfaat untuk kesehatan dan sering digunakan dalam pelajaran biologi', '', '2025-05-23 09:24:30', '2025-05-23 09:49:21', 'bunga', 'assets/images/products/68303eceeb91c.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `saved_products`
--

CREATE TABLE `saved_products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `saved_products`
--

INSERT INTO `saved_products` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 48, 16, '2025-07-17 12:24:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sliders`
--

INSERT INTO `sliders` (`id`, `image_path`, `created_at`, `updated_at`) VALUES
(3, 'assets/images/sliders/slider_1747872243_70f2f1d4.jpeg', '2025-05-22 00:04:03', '2025-05-22 00:04:03'),
(4, 'assets/images/sliders/slider_1747872257_61c644e3.jpeg', '2025-05-22 00:04:17', '2025-05-22 00:04:17'),
(5, 'assets/images/sliders/slider_1747877292_97f42972.jpg', '2025-05-22 01:28:12', '2025-05-22 01:28:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `profile_picture`, `created_at`, `updated_at`, `role`, `is_verified`) VALUES
(12, 'admin', 'admin123', 'admin@example.com', NULL, NULL, '2025-05-14 05:21:29', '2025-05-17 03:55:13', 'admin', 1),
(47, 'ZAKKI', 'zakki', 'zakki@gmail.com', NULL, '6879a68be8aa0_RobloxScreenShot20250716_135116864.png', '2025-07-16 12:15:58', '2025-07-18 01:42:35', 'user', 1),
(48, 'user1', 'user123', 'user@gmail.com', NULL, '687856224d771_RobloxScreenShot20250715_112025974.png', '2025-07-16 12:17:02', '2025-07-17 01:47:14', 'user', 1),
(49, 'iputt', 'iput123', 'iputt@gmail.com', NULL, '6878df01584d5_RobloxScreenShot20250715_112025974.png', '2025-07-17 11:30:30', '2025-07-17 11:31:13', 'user', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `flowers`
--
ALTER TABLE `flowers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_user_product_like` (`user_id`,`product_id`),
  ADD KEY `fk_product_like` (`product_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `saved_products`
--
ALTER TABLE `saved_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_user_product_save` (`user_id`,`product_id`),
  ADD KEY `fk_product_save` (`product_id`);

--
-- Indeks untuk tabel `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `flowers`
--
ALTER TABLE `flowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `saved_products`
--
ALTER TABLE `saved_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_product_like` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_like` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `saved_products`
--
ALTER TABLE `saved_products`
  ADD CONSTRAINT `fk_product_save` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_save` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
