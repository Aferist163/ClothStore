  -- phpMyAdmin SQL Dump
  -- version 5.2.1
  -- https://www.phpmyadmin.net/
  --
  -- Host: 127.0.0.1
  -- Generation Time: Paź 28, 2025 at 11:24 PM
  -- Wersja serwera: 10.4.32-MariaDB
  -- Wersja PHP: 8.2.12

  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  START TRANSACTION;
  SET time_zone = "+00:00";


  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
  /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
  /*!40101 SET NAMES utf8mb4 */;

  --
  -- Database: `clothstore`
  --

  -- --------------------------------------------------------

  --
  -- Struktura tabeli dla tabeli `cart`
  --

  CREATE TABLE `cart` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Struktura tabeli dla tabeli `categories`
  --

  CREATE TABLE `categories` (
    `id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `categories`
  --

  INSERT INTO `categories` (`id`, `name`) VALUES
  (1, 't-shirt');

  -- --------------------------------------------------------

  --
  -- Struktura tabeli dla tabeli `orders`
  --

  CREATE TABLE `orders` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `total_amount` decimal(10,2) NOT NULL,
    `status` enum('pending','processing','shipped','cancelled') NOT NULL DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Struktura tabeli dla tabeli `order_items`
  --

  CREATE TABLE `order_items` (
    `id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Struktura tabeli dla tabeli `products`
  --

  CREATE TABLE `products` (
    `id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `price` decimal(10,2) NOT NULL,
    `image_url` varchar(255) DEFAULT NULL,
    `category_id` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `products`
  --

  INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category_id`, `created_at`) VALUES
  (3, 'Riot', '100% cotton, size M, color Black', 50.00, 'img/riot_tshirt.webp\r\n', 1, '2025-10-28 20:34:42'),
  (4, 'FA', '100% cotton, size M, color Blue', 60.00, 'img/fa.webp', 1, '2025-10-28 21:57:14'),
  (5, 'Rutine', '100% cotton, size M, color Red', 55.00, 'img/rutine.webp', 1, '2025-10-28 21:59:43'),
  (6, 'GHOSTS PRPL', '100% cotton, size M, color Purple', 50.00, 'img/mayot.webp', 1, '2025-10-28 22:02:58'),
  (7, 'Paradise', '100% cotton, size M, color White', 50.00, 'img/paradise.webp', 1, '2025-10-28 22:05:40');

  -- --------------------------------------------------------

  --
  -- Struktura tabeli dla tabeli `users`
  --

  CREATE TABLE `users` (
    `id` int(11) NOT NULL,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    `role` enum('user','admin') NOT NULL DEFAULT 'user',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Indeksy dla zrzutów tabel
  --

  --
  -- Indeksy dla tabeli `cart`
  --
  ALTER TABLE `cart`
    ADD PRIMARY KEY (`id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `product_id` (`product_id`);

  --
  -- Indeksy dla tabeli `categories`
  --
  ALTER TABLE `categories`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `name` (`name`);

  --
  -- Indeksy dla tabeli `orders`
  --
  ALTER TABLE `orders`
    ADD PRIMARY KEY (`id`),
    ADD KEY `user_id` (`user_id`);

  --
  -- Indeksy dla tabeli `order_items`
  --
  ALTER TABLE `order_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `order_id` (`order_id`),
    ADD KEY `product_id` (`product_id`);

  --
  -- Indeksy dla tabeli `products`
  --
  ALTER TABLE `products`
    ADD PRIMARY KEY (`id`),
    ADD KEY `category_id` (`category_id`);

  --
  -- Indeksy dla tabeli `users`
  --
  ALTER TABLE `users`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `email` (`email`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `cart`
  --
  ALTER TABLE `cart`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT for table `categories`
  --
  ALTER TABLE `categories`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

  --
  -- AUTO_INCREMENT for table `orders`
  --
  ALTER TABLE `orders`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT for table `order_items`
  --
  ALTER TABLE `order_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT for table `products`
  --
  ALTER TABLE `products`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

  --
  -- AUTO_INCREMENT for table `users`
  --
  ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `cart`
  --
  ALTER TABLE `cart`
    ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

  --
  -- Constraints for table `orders`
  --
  ALTER TABLE `orders`
    ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

  --
  -- Constraints for table `order_items`
  --
  ALTER TABLE `order_items`
    ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

  --
  -- Constraints for table `products`
  --
  ALTER TABLE `products`
    ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
  COMMIT;

  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
