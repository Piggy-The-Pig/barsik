-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июл 01 2024 г., 08:44
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Barsik`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Basket`
--

CREATE TABLE `Basket` (
  `Id_basket` int NOT NULL,
  `User_id` int NOT NULL,
  `id_product` int NOT NULL,
  `count` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Category`
--

CREATE TABLE `Category` (
  `Category_id` int NOT NULL,
  `Name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Category`
--

INSERT INTO `Category` (`Category_id`, `Name`) VALUES
(1, 'Сок'),
(2, 'Кофе'),
(3, 'Газированные напитки'),
(4, 'Молочные напитки'),
(5, 'Вода'),
(6, 'Детские напитки');

-- --------------------------------------------------------

--
-- Структура таблицы `Orders`
--

CREATE TABLE `Orders` (
  `Id_order` int NOT NULL,
  `User_id` int NOT NULL,
  `Date_of_order` datetime NOT NULL,
  `Status` enum('Обработка','Доставляется','Отменен') NOT NULL,
  `Total_price` decimal(25,0) NOT NULL,
  `Used_bonuses` decimal(10,0) DEFAULT NULL,
  `Accrued_bonuses` decimal(10,0) DEFAULT NULL,
  `Delivery_address` varchar(255) DEFAULT NULL,
  `Delivery_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Orders`
--

INSERT INTO `Orders` (`Id_order`, `User_id`, `Date_of_order`, `Status`, `Total_price`, `Used_bonuses`, `Accrued_bonuses`, `Delivery_address`, `Delivery_method`) VALUES
(4, 9, '2024-04-18 22:25:03', 'Обработка', '567', '193', '28', 'Уфа, Уксивт, Пункт выдачи заказов.', 'pickup'),
(7, 9, '2024-04-21 15:09:09', 'Обработка', '1454', '331', '73', 'Уфа, Уксивт, Пункт выдачи заказов.', 'pickup'),
(8, 9, '2024-04-21 15:20:46', 'Обработка', '151', '65', '8', 'Уфа, Уксивт, Пункт выдачи заказов.', 'pickup'),
(9, 13, '2024-07-01 08:32:41', 'Обработка', '214', '1', '11', 'Уфа, Уксивт, Пункт выдачи заказов.', 'pickup');

-- --------------------------------------------------------

--
-- Структура таблицы `Order_Product`
--

CREATE TABLE `Order_Product` (
  `id` int NOT NULL,
  `Id_order` int NOT NULL,
  `Id_product` int NOT NULL,
  `count` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Order_Product`
--

INSERT INTO `Order_Product` (`id`, `Id_order`, `Id_product`, `count`) VALUES
(7, 4, 10, 4),
(11, 7, 9, 1),
(12, 7, 4, 2),
(13, 7, 10, 1),
(14, 8, 12, 1),
(15, 9, 12, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Product`
--

CREATE TABLE `Product` (
  `Id_product` int NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `Category_id` int NOT NULL,
  `Price` decimal(10,0) NOT NULL,
  `Image` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Product`
--

INSERT INTO `Product` (`Id_product`, `Name`, `Description`, `Category_id`, `Price`, `Image`) VALUES
(4, 'Лимонад Шихан', 'Освежающий лимонад с натуральным лимонным соком\r\n', 3, '50', 'http://avatars.mds.yandex.net/get-mpic/5234219/img_id6162136631165449300.jpeg/orig'),
(9, 'Набор \"Водяной\"', 'В набор входят:\r\nNestea Ice Tea 0.5л | Экстракт: Персик\r\nFanta 0.33мл | Экстракт: Апельсин\r\nFanta 1л | Экстракт: Апельсин\r\nCoca Cola 1л | Экстракт: Кока\r\nSprite 1л | Экстракт: Лайм\r\nSprite 0.33мл | Экстракт: Лайм\r\nNestea 0.5л | Экстракт: Цитрус', 3, '1495', 'https://tatdeno.ru/wp-content/uploads/2023/05/1665878457_1-podacha-blud-com-p-vrednie-napitki-krasivie-foto-1.png'),
(10, 'Coca Cola', 'Classic Coca Cola.\r\nОбъем 0.5\r\nЭкспорт из Турции', 3, '190', 'https://jamierubin.net/wp-content/uploads/2021/12/pexels-photo-2668310.jpeg'),
(12, 'Красный ключ', 'Водная вода', 5, '215', 'https://reafond.ru/wp-content/uploads/2019/07/WhatsApp-Image-2019-07-08-at-15.37.49.jpeg');

-- --------------------------------------------------------

--
-- Структура таблицы `Reviews`
--

CREATE TABLE `Reviews` (
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `id_product` int NOT NULL,
  `review_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `rating` int NOT NULL,
  `review_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `User_id` int NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password_hash` varchar(255) NOT NULL,
  `Bonus_points` decimal(10,0) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `contact_info` varchar(255) NOT NULL DEFAULT 'Данных нет.',
  `name` varchar(33) NOT NULL DEFAULT 'Данных нет.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`User_id`, `Email`, `Password_hash`, `Bonus_points`, `role`, `contact_info`, `name`) VALUES
(13, 'aa@mail.ru', '$2y$10$KmBUI2.GxiqcLhP.8dXQUeMMNOugw9eLoM/6VxgovHBLdzbUO6dyq', '11', 'user', 'уксивт', 'Piggy');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Basket`
--
ALTER TABLE `Basket`
  ADD PRIMARY KEY (`Id_basket`),
  ADD KEY `User_id` (`User_id`);

--
-- Индексы таблицы `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`Category_id`);

--
-- Индексы таблицы `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`Id_order`),
  ADD KEY `User_id` (`User_id`);

--
-- Индексы таблицы `Order_Product`
--
ALTER TABLE `Order_Product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Id_order` (`Id_order`),
  ADD KEY `Id_product` (`Id_product`);

--
-- Индексы таблицы `Product`
--
ALTER TABLE `Product`
  ADD PRIMARY KEY (`Id_product`),
  ADD KEY `Price` (`Price`),
  ADD KEY `Category_id` (`Category_id`);

--
-- Индексы таблицы `Reviews`
--
ALTER TABLE `Reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `User_id` (`user_id`),
  ADD KEY `Product_id` (`id_product`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_id`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Basket`
--
ALTER TABLE `Basket`
  MODIFY `Id_basket` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `Category`
--
ALTER TABLE `Category`
  MODIFY `Category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `Orders`
--
ALTER TABLE `Orders`
  MODIFY `Id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `Order_Product`
--
ALTER TABLE `Order_Product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `Product`
--
ALTER TABLE `Product`
  MODIFY `Id_product` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `Reviews`
--
ALTER TABLE `Reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `User_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Basket`
--
ALTER TABLE `Basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Order_Product`
--
ALTER TABLE `Order_Product`
  ADD CONSTRAINT `order_product_ibfk_1` FOREIGN KEY (`Id_order`) REFERENCES `Orders` (`Id_order`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_product_ibfk_2` FOREIGN KEY (`Id_product`) REFERENCES `Product` (`Id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Product`
--
ALTER TABLE `Product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Category_id`) REFERENCES `Category` (`Category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Reviews`
--
ALTER TABLE `Reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`User_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `Product` (`Id_product`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
