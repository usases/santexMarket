-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 08 2025 г., 21:09
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

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total`, `total_quantity`, `status`, `shipping_address`, `payment_method`, `products`, `created_at`) VALUES
(9, 8, '363900.00', 48, 'completed', '421', 'cash', '[{\"product_id\":\"12\",\"quantity\":\"3\",\"price\":\"11800.00\",\"name\":\"\\u0423\\u043d\\u0438\\u0442\\u0430\\u0437 \\u0441 \\u043c\\u0438\\u043a\\u0440\\u043e\\u043b\\u0438\\u0444\\u0442\\u043e\\u043c Roca Gap\",\"image\":\"https:\\/\\/i.dushevoi.ru\\/i\\/Roca\\/Gap-CHasha-7342477000-25122\\/217283.jpg\"},{\"product_id\":\"11\",\"quantity\":45,\"price\":\"7300.00\",\"name\":\"\\u0423\\u043d\\u0438\\u0442\\u0430\\u0437-\\u043a\\u043e\\u043c\\u043f\\u0430\\u043a\\u0442 Jika Lyra\",\"image\":\"https:\\/\\/santehnika1.ru\\/upload\\/resize_cache\\/iblock\\/b39\\/328_328_1\\/2.jpeg\"}]', '2025-04-28 12:55:09'),
(10, 8, '73290.00', 8, 'completed', '21', 'cash', '[{\"product_id\":\"8\",\"quantity\":2,\"price\":\"12500.00\",\"name\":\"\\u0421\\u043c\\u0435\\u0441\\u0438\\u0442\\u0435\\u043b\\u044c \\u0434\\u043b\\u044f \\u0432\\u0430\\u043d\\u043d\\u044b Hansgrohe\",\"image\":\"https:\\/\\/shop.hansgrohe.ru\\/media\\/catalog\\/product\\/cache\\/37\\/small_image\\/1172x\\/0dc2d03fe217f8c83829496872af24a0\\/7\\/7\\/77f72623a8a46b19e394f36265d4db4f.webp\"},{\"product_id\":\"11\",\"quantity\":2,\"price\":\"7300.00\",\"name\":\"\\u0423\\u043d\\u0438\\u0442\\u0430\\u0437-\\u043a\\u043e\\u043c\\u043f\\u0430\\u043a\\u0442 Jika Lyra\",\"image\":\"https:\\/\\/santehnika1.ru\\/upload\\/resize_cache\\/iblock\\/b39\\/328_328_1\\/2.jpeg\"},{\"product_id\":\"12\",\"quantity\":1,\"price\":\"11800.00\",\"name\":\"\\u0423\\u043d\\u0438\\u0442\\u0430\\u0437 \\u0441 \\u043c\\u0438\\u043a\\u0440\\u043e\\u043b\\u0438\\u0444\\u0442\\u043e\\u043c Roca Gap\",\"image\":\"https:\\/\\/i.dushevoi.ru\\/i\\/Roca\\/Gap-CHasha-7342477000-25122\\/217283.jpg\"},{\"product_id\":\"10\",\"quantity\":1,\"price\":\"9500.00\",\"name\":\"\\u0423\\u043d\\u0438\\u0442\\u0430\\u0437 \\u043f\\u043e\\u0434\\u0432\\u0435\\u0441\\u043d\\u043e\\u0439 Cersanit City\",\"image\":\"https:\\/\\/i.dushevoi.ru\\/i\\/Cersanit\\/City-S-MZ-CITY-COn-w-228049\\/334960.jpg\"},{\"product_id\":\"9\",\"quantity\":1,\"price\":\"5400.00\",\"name\":\"\\u0421\\u043c\\u0435\\u0441\\u0438\\u0442\\u0435\\u043b\\u044c \\u043a\\u0443\\u0445\\u043e\\u043d\\u043d\\u044b\\u0439 Vidima Retro\",\"image\":\"https:\\/\\/variant-a.ru\\/upload\\/iblock\\/catalog\\/smesiteli\\/smesiteli_dlya_rakoviny\\/4717\\/detail_smesitel_dlya_umyvalnika_vidima_retro_v9702aa_ba119aa_kod_4717.jpg\"},{\"product_id\":\"7\",\"quantity\":1,\"price\":\"6990.00\",\"name\":\"\\u0421\\u043c\\u0435\\u0441\\u0438\\u0442\\u0435\\u043b\\u044c \\u0434\\u043b\\u044f \\u0440\\u0430\\u043a\\u043e\\u0432\\u0438\\u043d\\u044b Grohe\",\"image\":\"https:\\/\\/grohe-russia.shop\\/media\\/catalog\\/product\\/cache\\/56\\/small_image\\/1310x874\\/0dc2d03fe217f8c83829496872af24a0\\/1\\/9\\/196d0885-4297-11ef-b775-005056043025.webp\"}]', '2025-04-29 19:38:25');

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
(12, 'Унитаз с микролифтом Roca Gap', 'Безободковый унитаз, сиденье с плавным опусканием', '11800.00', 'https://i.dushevoi.ru/i/Roca/Gap-CHasha-7342477000-25122/217283.jpg', 5, '2025-04-26 16:31:53', '2025-04-26 16:31:53', 10),
(13, 'Душевая кабина RGW Classic CL-05', 'Кабина с раздвижными дверями, антикальциевая защита стекла, усиленный поддон с антискользящим покрытием.\r\n\r\nРазмер: 90×90×195 см\r\nФорма: четверть круга\r\nСтекло: закалённое, прозрачное, 6 мм\r\nПрофиль: алюминиевый, хром', '27990.00', 'https://i.dushevoi.ru/i/RGW/Classic-CL-48-160kh100-378693/533449.jpg', 6, '2025-05-08 13:48:53', '2025-05-08 14:05:26', 10),
(14, 'Душевая кабина Triton Standard L', 'Просторная кабина с распашными дверцами, удобным поддоном и встроенным сиденьем.\r\n\r\nРазмер: 120×80×215 см\r\nФорма: прямоугольная\r\nСтекло: матовое, 5 мм\r\nПрофиль: белый', '32500.00', 'https://aquagorod.ru/image/cache/catalog/Triton/dushevyekabiny/2dktritonstavndart90h90polosy-01-700x700.png', 6, '2025-05-08 14:04:24', '2025-05-08 14:05:04', 5),
(15, 'Душевая кабина Erlit ER3512TP-C4', 'Модель с крышей, гидромассажем, подсветкой и зеркалом. Идеальный выбор для современной ванной.\r\n\r\nРазмер: 120×120×215 см\r\nФорма: полукруглая\r\nСтекло: тонированное, 4 мм\r\nПрофиль: сатин', '45900.00', 'https://mosplitka.ru/upload/resize_cache/iblock/b0d/700_370_1/b0dda516e0779c25973a8af1131c1dce.jpg', 6, '2025-05-08 14:06:58', '2025-05-08 14:06:58', 5),
(16, 'Раковина Cersanit Como 50', 'Компактная и универсальная раковина, подходит для небольших ванных комнат. Отверстие под смеситель и перелив.\r\n\r\nРазмер: 50×42 см\r\nУстановка: на тумбу / подвесная\r\nМатериал: санфаянс\r\nЦвет: белый', '3490.00', 'https://www.aquanet.ru/upload/resize_cache/iblock/e67/3m8rjmdny97ll80cyukk67ttd7fhw5vi/1024_650_14178da36a12ff37578e12d8135c8da14/RAKOVINA_CERSANIT_COMO_50_U_UM_COM50_1_1.jpg', 7, '2025-05-08 14:09:01', '2025-05-08 14:09:01', 5),
(17, 'Раковина Roca Inspira Round 42', 'Круглая дизайнерская раковина без отверстия под смеситель, устанавливается на столешницу. Современный минималистичный стиль.\r\n\r\nДиаметр: 42 см\r\nУстановка: накладная\r\nМатериал: фарфор\r\nЦвет: глянцевый белый', '14990.00', 'https://santehpremium.ru/content/product/big/img_75529.jpg', 7, '2025-05-08 14:10:27', '2025-05-08 14:10:27', 5),
(18, 'Раковина Акватон Бетта 60', 'Прямоугольная раковина с глубоким чашей и глянцевым покрытием. Устойчива к царапинам и перепадам температуры.\r\n\r\nРазмер: 60×46 см\r\nУстановка: на тумбу\r\nМатериал: литьевой мрамор\r\nЦвет: белый', '6790.00', 'https://santehnika1.ru/upload/resize_cache/iblock/daf/hgjo40ijcltb7i00ss3q7eoy0eo2ncmc/328_328_1/3.jpg', 7, '2025-05-08 14:11:42', '2025-05-08 14:11:42', 5),
(19, 'Полотенцесушитель TermoSmart Квадро', 'Современный дизайн, высокая теплоотдача, подходит для центрального водоснабжения.\r\n\r\nТип: водяной\r\nМатериал: нержавеющая сталь\r\nФорма: лесенка\r\nРазмер: 500×800 мм\r\nПодключение: нижнее', '7990.00', 'https://avatars.mds.yandex.net/get-mpic/5213758/2a00000195f66823d22b64ede761489b28d3/orig', 8, '2025-05-08 14:13:35', '2025-05-08 14:23:29', 10),
(20, 'Полотенцесушитель Energy U Ultra 500×530', 'Быстрый нагрев, низкое энергопотребление, встроенный терморегулятор. Удобен для установки в помещениях без центрального отопления.\r\n\r\nТип: электрический\r\nМатериал: сталь с хромированным покрытием\r\nФорма: П-образный\r\nРазмер: 500×530 мм\r\nМощность: 100 Вт', '5450.00', 'https://energyrus.ru/images/katalog/polotencesushiteli/electricheskie/u/polotencesushitel-elektricheskiy-energy-u-g3k-48-vt.jpg', 8, '2025-05-08 14:15:51', '2025-05-08 14:15:51', 10),
(21, 'Полотенцесушитель Двин Терминус ', 'Универсальное решение с возможностью работы вне отопительного сезона. Подходит для городской квартиры и загородного дома.\r\n\r\nТип: комбинированный (водяной + электрический ТЭН)\r\nМатериал: полированная нержавейка\r\nФорма: лесенка, 7 перекладин\r\nРазмер: 600×800 мм', '11200.00', 'https://1000vann.com/uploads/product/4500/4599/1.webp', 8, '2025-05-08 14:20:57', '2025-05-08 14:20:57', 10),
(22, ' Ванна акриловая Triton Стандарт', 'Классическая модель с анатомической спинкой и усиленным дном. Лёгкая, прочная, быстро нагревается и долго сохраняет тепло.\r\n\r\nРазмер: 150×70 см\r\nМатериал: акрил\r\nФорма: прямоугольная\r\nОбъём: 170 л', '11990.00', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQclaJbU_VDquOl3fxHJSNkYtzh5jEPVaap0w&s', 9, '2025-05-08 14:22:28', '2025-05-08 14:23:03', 5),
(23, 'Ванна стальная Kaldewei Eurowa', 'Немецкое качество, устойчивость к ударам и царапинам. Подходит для установки на ножках или встраивания.\r\n\r\nРазмер: 170×75 см\r\nМатериал: эмалированная сталь 2,5 мм\r\nФорма: прямоугольная\r\nОбъём: 190 л', '18500.00', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFDmTuhXnP1fLbsOmaBa1RkS4fuO-HgQ_N5A&s', 9, '2025-05-08 14:24:23', '2025-05-08 14:24:23', 10),
(24, 'Ванна чугунная Roca Malibu', 'Тихая, устойчивая и долговечная ванна с антискользящим покрытием. Идеальна для длительного пользования.\r\n\r\nРазмер: 160×70 см\r\nМатериал: чугун с эмалированным покрытием\r\nФорма: прямоугольная\r\nОбъём: 180 л', '29900.00', 'https://st21.stblizko.ru/images/product/073/054/882_big.png', 9, '2025-05-08 14:25:20', '2025-05-08 17:54:26', 6),
(25, 'Сифон для раковины McAlpine M11PVN', 'Надёжный сифон с удобной системой прочистки и простым монтажом. Совместим с большинством раковин.\r\n\r\nТип: бутылочный\r\nДиаметр подключения: 32 мм\r\nМатериал: полипропилен\r\nОсобенности: с выходом под стиральную машину, регулируемая высота', '690.00', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3d8ybVtNoWdfO13fdSJFRiKazPZG6AfhI3w&s', 10, '2025-05-08 14:26:35', '2025-05-08 14:26:35', 20),
(26, 'Гибкая подводка Rehau Flex 1/2\" – 50 см', 'Устойчива к высоким температурам и давлению. Срок службы до 10 лет.\r\n\r\nНазначение: подключение смесителей и сантехники\r\nДлина: 50 см\r\nДиаметр: 1/2\"\r\nМатериал: нержавеющая сталь, внутренняя оплётка – EPDM', '210.00', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSxRrLuQiwRT6ndLG0TpIMxRwQJ03bypRRddw&s', 10, '2025-05-08 14:27:34', '2025-05-08 14:27:34', 20),
(27, 'Инсталляция Grohe Rapid SL 3-в-1', 'Регулируемая высота, антивандальная клавиша, совместимость с большинством подвесных чаш\r\n\r\nНазначение: скрытая установка унитаза\r\nВысота: 113 см\r\nКомплектация: металлическая рама, сливной бачок, крепёж, клавиша смыва', '18900.00', 'https://grohe-russia.shop/media/catalog/product/cache/56/small_image/1310x874/0dc2d03fe217f8c83829496872af24a0/f/9/f9bb0190-4299-11ef-b775-005056043025.jpg', 11, '2025-05-08 14:28:31', '2025-05-08 14:30:27', 4),
(28, 'Инсталляция Grohe Rapid SL 38772001', 'Регулируемая высота, тихий бачок, немецкое качество\r\n\r\nРазмер: 113×50×14 см\r\nНазначение: для подвесного унитаза\r\nМатериал рамы: сталь с антикоррозийным покрытием\r\nМеханизм смыва: двойной, пневматическая клавиша (в комплекте)', '19990.00', 'https://www.ar59.ru/image/cache/catalog/image/cache/catalog/MaLVowDGMWs-1200x800.webp', 11, '2025-05-08 14:30:22', '2025-05-08 14:30:22', 4),
(29, 'Инсталляция Vitra Blue Life 742-5800-01', 'Компактная, подходит для гипсокартонных и кирпичных стен, тихий набор воды\r\n\r\nРазмер: 112×50×14 см\r\nНазначение: для подвесного унитаза\r\nМатериал: оцинкованная сталь\r\nМеханизм смыва: двойной, клавиша приобретается отдельно', '10990.00', 'https://mosplitka.ru/upload/iblock/05a/05ab80c8908759bf38bde4705ca56dc2.jpg', 11, '2025-05-08 14:31:26', '2025-05-08 14:31:26', 4),
(30, 'Универсальный слив-перелив AlcaPlast A55K', 'Современный механизм управления сливом одной поворотной ручкой. Подходит для большинства ванн. Прост в установке и обслуживании.\r\n\r\nТип: автоматический слив-перелив\r\nНазначение: для акриловых и стальных ванн\r\nДиаметр слива: 40 мм\r\nДлина троса: 570 мм\r\nМатериал: латунь, пластик\r\n\r\n', '1890.00', 'https://www.blumart.ru/img/77177_sifon_dlya_vanni_sliv_pereliv_alcaplast_a55k_ru_01_pereliv_57_sm_hrom_poluavtomat-154138.webp', 10, '2025-05-08 14:33:03', '2025-05-08 14:33:03', 10);

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
(8, 'Жма', '123@mail.ru', '$2y$10$zUgdeL4Jo2uw6w8Ve.00geHCJptgO0G07Jf8bMIWOLQKvUXiavOMi', 'admin', '2025-04-26 21:00:50');

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
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
