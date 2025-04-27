-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 27 2025 г., 14:36
-- Версия сервера: 10.3.22-MariaDB
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `santexmarket`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(4, 'Смесители'),
(5, 'Унитазы'),
(6, 'Душевые кабины'),
(7, 'Раковины'),
(8, 'Полотенцесушители'),
(9, 'Ванны'),
(10, 'Комплектующие'),
(11, 'Инсталляции');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `status` enum('processing','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'processing',
  `shipping_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` enum('cash','card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `created_at`, `updated_at`, `stock`) VALUES
(7, 'Смеситель для раковины Grohe', 'Хромированный смеситель с одним рычагом.', '6990.00', 'https://grohe-russia.shop/media/catalog/product/cache/56/small_image/1310x874/0dc2d03fe217f8c83829496872af24a0/1/9/196d0885-4297-11ef-b775-005056043025.webp', 4, '2025-04-26 16:09:08', '2025-04-26 16:26:41', 25),
(8, 'Смеситель для ванны Hansgrohe', 'Настенный смеситель с душевым гарнитуром', '12500.00', 'https://shop.hansgrohe.ru/media/catalog/product/cache/37/small_image/1172x/0dc2d03fe217f8c83829496872af24a0/7/7/77f72623a8a46b19e394f36265d4db4f.webp', 4, '2025-04-26 16:16:02', '2025-04-26 16:16:02', 18),
(9, 'Смеситель кухонный Vidima Retro', 'Классический кухонный смеситель с высоким изливом	', '5400.00', 'https://variant-a.ru/upload/iblock/catalog/smesiteli/smesiteli_dlya_rakoviny/4717/detail_smesitel_dlya_umyvalnika_vidima_retro_v9702aa_ba119aa_kod_4717.jpg', 4, '2025-04-26 16:26:19', '2025-04-26 16:26:19', 20),
(10, 'Унитаз подвесной Cersanit City', 'Компактный подвесной унитаз с системой антивсплеск', '9500.00', 'https://i.dushevoi.ru/i/Cersanit/City-S-MZ-CITY-COn-w-228049/334960.jpg', 5, '2025-04-26 16:28:26', '2025-04-26 16:28:26', 12),
(11, 'Унитаз-компакт Jika Lyra', 'Классический унитаз с бачком двойного смыва', '7300.00', 'https://santehnika1.ru/upload/resize_cache/iblock/b39/328_328_1/2.jpeg', 5, '2025-04-26 16:30:12', '2025-04-26 16:30:12', 14),
(12, 'Унитаз с микролифтом Roca Gap', 'Безободковый унитаз, сиденье с плавным опусканием', '11800.00', 'https://i.dushevoi.ru/i/Roca/Gap-CHasha-7342477000-25122/217283.jpg', 5, '2025-04-26 16:31:53', '2025-04-26 16:31:53', 10);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(8, 'Жма', '123@mail.ru', '$2y$10$zUgdeL4Jo2uw6w8Ve.00geHCJptgO0G07Jf8bMIWOLQKvUXiavOMi', 'user', '2025-04-26 21:00:50');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
