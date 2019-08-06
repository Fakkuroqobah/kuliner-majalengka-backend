-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2019 at 02:49 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kuliner_majalengka`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_category` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_user` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_category`, `category_name`, `category_image`, `category_user`, `created_at`, `updated_at`) VALUES
(1, 'Masakan Inggris', 'http://localhost:8000/categories/y1vWBtfTKCpuS664EgGADS5qeNtjvN.png', 1, '2019-08-05 19:31:58', '2019-08-05 19:31:58'),
(2, 'Masakan Jepang', 'http://localhost:8000/categories/cP2nJIQYDleXoqudjumq7zbJ68jPE2.png', 1, '2019-08-05 19:32:09', '2019-08-05 19:32:09'),
(3, 'Masakan Italia', 'http://localhost:8000/categories/UzwWhHij8HALoHODoNDeeHVyxCQiQq.png', 2, '2019-08-05 19:32:27', '2019-08-05 19:32:27'),
(4, 'Masakan Indonesia', 'http://localhost:8000/categories/48s33jQpsNokHMXgLF9PufBMGZWcIU.png', 2, '2019-08-05 19:32:45', '2019-08-05 19:32:45');

-- --------------------------------------------------------

--
-- Table structure for table `category_restaurant`
--

CREATE TABLE `category_restaurant` (
  `id_category_restaurant` int(10) UNSIGNED NOT NULL,
  `id_category` int(10) UNSIGNED NOT NULL,
  `id_restaurant` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id_gallery` int(10) UNSIGNED NOT NULL,
  `gallery_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gallery_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gallery_copyright` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gallery_restaurant` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id_gallery`, `gallery_image`, `gallery_info`, `gallery_copyright`, `gallery_restaurant`, `created_at`, `updated_at`) VALUES
(1, 'http://localhost:8000/galleries/4S8YUOhw2vY27t6X5JLnRVY8wOYdZn.jpg', 'Masakan Inggris', '@restocode', 1, '2019-08-05 19:38:42', '2019-08-05 19:38:42'),
(2, 'http://localhost:8000/galleries/OTFMq1Ct0QL7Jg610WjhbTZUjaaqCe.jpg', 'Masakan Indonesia', '@wonderfulIndonesia', 4, '2019-08-05 19:39:11', '2019-08-05 19:39:11');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id_like` int(10) UNSIGNED NOT NULL,
  `id_restaurant` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id_like`, `id_restaurant`, `id_user`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2019-08-05 20:04:59', '2019-08-05 20:04:59'),
(2, 4, 2, '2019-08-05 20:05:11', '2019-08-05 20:05:11'),
(3, 3, 2, '2019-08-05 23:05:07', '2019-08-05 23:05:07');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id_menu` int(10) UNSIGNED NOT NULL,
  `menu_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_price` int(11) NOT NULL,
  `menu_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_info` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_favorite` tinyint(1) NOT NULL DEFAULT '0',
  `menu_restaurant` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id_menu`, `menu_name`, `menu_slug`, `menu_price`, `menu_image`, `menu_info`, `menu_favorite`, `menu_restaurant`, `created_at`, `updated_at`) VALUES
(1, 'Chicken fry', 'chicken-fry', 100000, 'http://localhost:8000/menus/ZO9YPqe7v8kwJuB62qbO2r5jUnOe6I.jpg', '100% Halal', 1, 1, '2019-08-05 19:06:09', '2019-08-05 19:06:09'),
(2, 'Kopi arabika', 'kopi-arabika', 100000, 'http://localhost:8000/menus/tqBui8eyerbXNxuGG8nWDdaq5xOtdW.jpg', '100% Halal', 1, 1, '2019-08-05 19:17:23', '2019-08-05 19:17:23'),
(3, 'Kopi cappuchino', 'kopi-cappuchino', 100000, 'http://localhost:8000/menus/QMfpQz6XCAQH90VXDnc5flbb6gRtmY.jpg', '100% Halal', 1, 3, '2019-08-05 19:18:54', '2019-08-05 19:18:54'),
(4, 'Kopi Toraja', 'kopi-toraja', 100000, 'http://localhost:8000/menus/h87H2yn95jVZQjVUad7hcwyKuLzfrJ.jpg', '100% Halal', 1, 4, '2019-08-05 19:19:21', '2019-08-05 19:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(2, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(3, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(4, '2016_06_01_000004_create_oauth_clients_table', 1),
(5, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(6, '2019_07_31_192929_create_users_table', 1),
(7, '2019_07_31_193140_create_categories_table', 1),
(8, '2019_07_31_194426_create_restaurants_table', 1),
(9, '2019_07_31_200819_create_likes_table', 1),
(10, '2019_07_31_201715_create_ratings_table', 1),
(11, '2019_07_31_203059_create_galleries_table', 1),
(12, '2019_07_31_203531_create_menus_table', 1),
(13, '2019_08_02_125733_create_category_restaurant_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('2094447b7c16482574a4a7270aba3aa0beecb29de6480588c2f88e2f3186ead7ea1dcd32978bd076', 1, 1, 'nApp', '[]', 0, '2019-08-05 18:21:14', '2019-08-05 18:21:14', '2020-08-05 18:21:14'),
('746059a863639dad7bcd76e1669881df6a50af1fe2478f512af238dfd658e5b5e2b611f60751d27f', 2, 1, 'nApp', '[]', 0, '2019-08-05 18:22:40', '2019-08-05 18:22:40', '2020-08-05 18:22:40'),
('e51f26ac6ba18d63ea29c7d22757cc10f993066e2f1316c6665e9df96b863255fb650613f10b0cb8', 1, 1, 'nApp', '[]', 0, '2019-08-05 18:23:35', '2019-08-05 18:23:35', '2020-08-05 18:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, ' Personal Access Client', '2vUVn97eibImFfSXK3wHnZdjGSQQsiqRljzUsItI', 'http://localhost', 1, 0, 0, '2019-08-05 13:45:42', '2019-08-05 13:45:42'),
(2, NULL, ' Password Grant Client', 'E6kdZ6DK1Vc3aueBrKW99V4Eh2o9Q7GX9ZqVwcXe', 'http://localhost', 0, 1, 0, '2019-08-05 13:45:42', '2019-08-05 13:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2019-08-05 13:45:42', '2019-08-05 13:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id_rating` int(10) UNSIGNED NOT NULL,
  `rating_value` tinyint(4) NOT NULL DEFAULT '0',
  `rating_comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating_restaurant` int(10) UNSIGNED NOT NULL,
  `rating_user` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id_rating`, `rating_value`, `rating_comment`, `rating_restaurant`, `rating_user`, `created_at`, `updated_at`) VALUES
(4, 10, 'Enak', 1, 1, '2019-08-06 09:38:57', '2019-08-06 09:38:57'),
(5, 8, 'lezat', 1, 2, '2019-08-06 09:44:07', '2019-08-06 09:44:07'),
(6, 8, 'Keren', 3, 2, '2019-08-06 09:44:46', '2019-08-06 09:44:46');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id_restaurant` int(10) UNSIGNED NOT NULL,
  `restaurant_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_owner` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_latitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_longitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_user` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id_restaurant`, `restaurant_name`, `restaurant_slug`, `restaurant_owner`, `restaurant_address`, `restaurant_image`, `restaurant_latitude`, `restaurant_longitude`, `restaurant_description`, `restaurant_user`, `created_at`, `updated_at`) VALUES
(1, 'Resto Code', 'resto-code', 'Agung Maulana', 'Yogyakarta', 'http://localhost:8000/resto/m05c3ovCTuVDzUpe8eNHv1jeB3tz5e.jpg', '19210', '29219', 'Tasty and Healty', 1, '2019-08-05 18:25:02', '2019-08-05 18:25:02'),
(2, 'PHP Resto', 'php-resto', 'Agung Maulana', 'Bandung', 'http://localhost:8000/resto/Uoya2Wnwu6IyYdoPKYX0u1Sqoi1Woj.png', '19210', '29219', 'Lezat dan nikmat', 1, '2019-08-05 18:41:48', '2019-08-05 18:59:46'),
(3, 'Cafetaria', 'cafetaria', 'Novariza', 'Bandung', 'http://localhost:8000/resto/sKo0T3q5u8pO0bhNsMGim5lNNfwY6H.jpg', '19210', '29219', 'kopi', 2, '2019-08-05 18:46:05', '2019-08-05 18:46:05'),
(4, 'Cat resto', 'cat-resto', 'Novariza', 'Bandung', 'http://localhost:8000/resto/EkicrbrmPWSFCv2pcVqYB5lv0KVYRt.jpg', '19210', '29219', 'Kucing', 2, '2019-08-05 18:46:49', '2019-08-05 18:46:49'),
(5, 'Restaurant Burger', 'restaurant-burger', 'Agung Maulana', 'Bandung', 'http://localhost:8000/resto/AC95kprLUkJen2PPiv2ik26hm09rRr.jpg', '-127212666128', '612729127112', 'Restaurant burger enak', 2, '2019-08-05 21:44:35', '2019-08-05 21:44:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `user_name`, `user_email`, `user_password`, `user_level`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Agung Maulana', 'agung@mail.com', '$2y$10$nRqp0DuWAQ5iyykmZP/dS.EvBtxjcqgn4nxhth5j52hztcGmRgQDW', 'admin', NULL, '2019-08-05 18:21:13', '2019-08-05 18:21:13'),
(2, 'Novariza', 'nova@mail.com', '$2y$10$Nk0sgSf9O/5iIARDInvplO2cQy7Gsah50vkxtV6iin2MH8qb8mEjK', 'admin', NULL, '2019-08-05 18:22:40', '2019-08-05 18:22:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`),
  ADD KEY `categories_category_user_foreign` (`category_user`);

--
-- Indexes for table `category_restaurant`
--
ALTER TABLE `category_restaurant`
  ADD PRIMARY KEY (`id_category_restaurant`),
  ADD KEY `category_restaurant_id_category_foreign` (`id_category`),
  ADD KEY `category_restaurant_id_restaurant_foreign` (`id_restaurant`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id_gallery`),
  ADD KEY `galleries_gallery_restaurant_foreign` (`gallery_restaurant`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id_like`),
  ADD KEY `likes_id_restaurant_foreign` (`id_restaurant`),
  ADD KEY `likes_id_user_foreign` (`id_user`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `menus_menu_restaurant_foreign` (`menu_restaurant`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `ratings_rating_restaurant_foreign` (`rating_restaurant`),
  ADD KEY `ratings_rating_user_foreign` (`rating_user`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id_restaurant`),
  ADD KEY `restaurants_restaurant_user_foreign` (`restaurant_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `category_restaurant`
--
ALTER TABLE `category_restaurant`
  MODIFY `id_category_restaurant` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id_gallery` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id_like` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id_menu` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id_rating` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id_restaurant` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_category_user_foreign` FOREIGN KEY (`category_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `category_restaurant`
--
ALTER TABLE `category_restaurant`
  ADD CONSTRAINT `category_restaurant_id_category_foreign` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `category_restaurant_id_restaurant_foreign` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurants` (`id_restaurant`);

--
-- Constraints for table `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_gallery_restaurant_foreign` FOREIGN KEY (`gallery_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_id_restaurant_foreign` FOREIGN KEY (`id_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_menu_restaurant_foreign` FOREIGN KEY (`menu_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_rating_restaurant_foreign` FOREIGN KEY (`rating_restaurant`) REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_rating_user_foreign` FOREIGN KEY (`rating_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_restaurant_user_foreign` FOREIGN KEY (`restaurant_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
