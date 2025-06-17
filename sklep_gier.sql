-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 17, 2025 at 04:30 PM
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
-- Database: `sklep_gier`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `game_id`, `created_at`) VALUES
(33, 2, 5, '2025-06-16 11:03:12');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `description`, `image`, `price`) VALUES
(1, 'Wiedźmin 1', 'Pierwsza część wyśmienitej serii gier Polskiego studia CD Project Red!', '6836cbec9d497W1.jpg', 29.99),
(2, 'Wiedźmin 2: Zabójcy Królów', 'Krótka natomiast niezwykle treściwa fabularnie!', '6836cc20cb55fW2.jpg', 39.99),
(3, 'Wiedźmin 3: Dziki Gon', 'Zdobywca Gry Roku 2015!', '6836cc94b46ecW3.jpg', 99.99),
(4, 'Gothic 1', 'Zdecydowanie ponadczasowa!', '6836cd1dc99e7G1.jpg', 91.99),
(5, 'Gothic II: Złota edycja', 'Druga część kultowej gry!', '6836cd7293112G2.jpg', 91.99),
(6, 'Gothic® 3', 'Najmniej udana z całej serii lecz warta uwagi!', '6836cda33d1e1G3.jpg', 91.99),
(7, 'EA SPORTS™ FIFA 23', 'W większości pozytywne recenzowana!', '6836cdfa33b10FIFA23.jpg', 89.90),
(8, 'EA SPORTS FC™ 24', 'Lepsza mechanicznie w porównaniu do poprzednich części!', '6836ce1fa2c04FIFA24.jpeg', 319.90),
(9, 'EA SPORTS FC™ 25', 'Mieszane recenzje natomiast można spędzić wiele godzin!', '6836ce5a61ca4FIFA25.jpeg', 319.90),
(10, 'Battlefield™ 1', 'Arcydzieło graficzne! ', '6836cea76e226BF1.jpg', 179.90),
(11, 'Battlefield 3™', 'Recenzje niezwykle skrajne, także trzeba ocenić samemu!', '6836cf45de319BF3.jpg', 49.99),
(12, 'Battlefield™ V', 'Zdecydowanie warta uwagi!', '6836cf767414bBFV.jpg', 219.90),
(13, 'LEGO Gwiezdne wojny: Saga Skywalkerów', 'Zbiór wszystkich części filmów z serii Gwiezdnych Wojen!', '6836d015b3205LEGO1.jpg', 229.00),
(14, 'LEGO Piraci z Karaibów', 'Świetna zabawa dla całej rodziny!', '6836d0d0c9a1fLEGO2.jpg', 91.99),
(15, 'LEGO Jurassic World', 'Przenieś się w świat dinozaurów!', '6836d14f5e6b6LEGO3.jpg', 94.00),
(16, 'LEGO City Undercover', 'Zostań bohaterem LEGO City!', '6836d191d6239LEGO4.jpg', 139.00),
(18, 'Kangurek Kao', 'Odświeżona wersja kultowej trójwymiarowej platformówki!', '6849ea9a6a9aeKAO.jpg', 129.90),
(19, 'UNCHARTED™: Kolekcja Dziedzictwo Złodziei', 'Pełne akcji, filmowe przygody Nathana Drake’a i Chloe Frazer!', '6849f02cf209dU.jpg', 219.00),
(20, 'PC Building Simulator', 'Naucz się składania komputera stacjonarnego za pomocą tego symulatora!', '684d4164b3913PC.jpg', 91.99),
(21, 'Wrap House Simulator', 'Wspólnie ze znajomymi spędzicie kilka długich wieczorów!', '684d42aa3bc77WRAP.jpg', 39.99),
(22, 'Outlast', 'Jedna z najstraszniejszych dostępnych na rynku gier z gatunku horror!', '684d51ebe05a9O.jpg', 67.99),
(23, 'Test', 'Testowa gra', '684ffa2ee7143PlanZajęć.png', 100.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `game_codes`
--

CREATE TABLE `game_codes` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `code` varchar(15) NOT NULL,
  `is_assigned` tinyint(1) DEFAULT 0,
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_codes`
--

INSERT INTO `game_codes` (`id`, `game_id`, `code`, `is_assigned`, `assigned_to`) VALUES
(116, 1, 'UBH-1OP-CGS-F8W', 0, 1),
(117, 1, '0KW-NMQ-OX5-P97', 0, NULL),
(118, 2, 'N1V-2LR-5UK-GIO', 0, NULL),
(119, 2, '27V-1D6-H9Z-4WI', 0, NULL),
(120, 3, 'AQ6-RES-C5O-W7L', 0, NULL),
(121, 3, 'SK8-M1B-P7Q-ZIJ', 0, NULL),
(122, 3, 'SKB-Q2W-TIR-NU4', 0, NULL),
(123, 19, 'DQM-CE8-I6S-W5V', 0, 1),
(124, 19, 'W2X-DV3-PKF-NC5', 0, NULL),
(125, 19, 'VFM-84T-A9G-QWB', 0, NULL),
(126, 18, '1XW-2EC-KJ4-LYV', 0, 2),
(127, 18, 'XCY-5BK-E1V-QIT', 0, NULL),
(128, 18, 'JRK-08B-A4C-1ME', 0, NULL),
(129, 5, '9IC-BD8-MUY-AXS', 0, NULL),
(130, 5, '6XT-D9A-0CR-3KM', 0, NULL),
(131, 5, 'H9Q-2GW-D0L-C6K', 0, NULL),
(132, 4, 'PUT-WG5-HSL-9AJ', 0, NULL),
(133, 4, 'HSD-P6U-2VR-B3G', 0, NULL),
(134, 10, '7CS-1L4-MXY-26I', 0, NULL),
(135, 11, 'F5H-E17-S2A-K60', 0, 2),
(136, 11, 'RZW-EUI-2OT-K6A', 0, 2),
(137, 12, 'KLS-O2U-IME-FY3', 0, NULL),
(138, 12, 'FX1-K4N-VL5-TUP', 0, NULL),
(139, 16, 'MOK-XS4-I07-39A', 0, 2),
(140, 16, 'JAX-K20-RWS-ETM', 0, NULL),
(141, 15, 'IGE-YZQ-H5S-2KT', 0, NULL),
(142, 15, '9RO-AFG-QUT-8HZ', 0, NULL),
(143, 14, 'NHU-W83-FS4-LRE', 0, NULL),
(144, 14, 'AB0-4DG-OIU-7XR', 0, NULL),
(145, 13, 'VBC-7P2-GD4-NHO', 0, NULL),
(146, 13, 'M1C-8UW-DEB-7JY', 0, NULL),
(147, 21, 'VJC-MSA-72Y-5BH', 0, NULL),
(148, 21, '35S-74Y-RHA-06T', 0, NULL),
(149, 20, '3YU-587-TQ0-24Z', 0, NULL),
(150, 20, 'SEG-4ZT-2VJ-ALO', 0, NULL),
(151, 8, 'JNY-MHW-P6C-9T0', 0, NULL),
(152, 8, '38Z-TYE-0C6-JH7', 0, NULL),
(153, 9, 'QFI-YJC-87B-V56', 0, NULL),
(154, 9, 'F0H-LQK-17Z-ER6', 0, NULL),
(155, 22, 'U6P-2GQ-DEZ-B0S', 0, NULL),
(156, 22, '6G2-AKN-T04-9WF', 0, NULL),
(157, 23, 'KPR-AT1-ZSX-V6B', 0, NULL),
(158, 23, 'KZY-4C8-V27-S5J', 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `game_tags`
--

CREATE TABLE `game_tags` (
  `game_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `game_tags`
--

INSERT INTO `game_tags` (`game_id`, `tag_id`) VALUES
(1, 1),
(1, 7),
(2, 1),
(2, 7),
(3, 1),
(3, 5),
(4, 1),
(4, 28),
(4, 29),
(5, 1),
(5, 28),
(6, 1),
(6, 5),
(7, 34),
(7, 35),
(8, 34),
(8, 35),
(9, 34),
(9, 35),
(10, 40),
(10, 41),
(11, 43),
(11, 46),
(12, 40),
(12, 43),
(13, 10),
(13, 11),
(13, 63),
(14, 10),
(14, 11),
(14, 20),
(15, 10),
(15, 11),
(15, 26),
(16, 10),
(16, 11),
(18, 46),
(18, 60),
(19, 7),
(19, 63),
(20, 68),
(21, 68),
(22, 70),
(22, 71),
(23, 72);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `game_id` int(11) DEFAULT NULL,
  `code_id` int(11) DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `game_id`, `code_id`, `purchase_date`, `total_price`) VALUES
(27, 1, 1, 116, '2025-06-12 09:12:20', 0.00),
(28, 2, 11, 135, '2025-06-16 11:24:31', 0.00),
(29, 2, 11, 136, '2025-06-16 11:24:49', 0.00),
(30, 1, 19, 123, '2025-06-16 12:25:11', 0.00),
(31, 2, 18, 126, '2025-06-16 13:02:31', 0.00),
(32, 2, 16, 139, '2025-06-16 13:02:31', 0.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `game_id`, `rating`, `content`, `created_at`) VALUES
(9, 2, 11, 5, 'Polecam do wspólnej gry z jakąś zajebistą ekipą!', '2025-06-16 09:25:26'),
(10, 1, 19, 5, 'Na prawdę dojrzała fabularnie, daję 5 gwiazdek i zalecam każdemu zagrać!', '2025-06-16 10:23:16'),
(11, 1, 23, 5, 'Świetny test', '2025-06-16 20:41:05');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(46, 'Akcja'),
(7, 'Bogata fabuła'),
(26, 'Dinozaury'),
(40, 'FPS'),
(70, 'Horror'),
(28, 'Klasyczne'),
(29, 'Klimatyczne'),
(11, 'Kooperacja'),
(10, 'LEGO'),
(5, 'Otwarty świat'),
(35, 'Piłka nożna'),
(20, 'Piraci'),
(60, 'Platformowe 3D'),
(63, 'Przygodowe'),
(1, 'RPG'),
(34, 'Sportowe'),
(43, 'Strzelanka'),
(71, 'Survival horror'),
(68, 'Symulatory'),
(72, 'Test'),
(41, 'Wojna');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar`, `role`) VALUES
(1, 'admin', 'jaglinskiadam@gmail.com', '$2y$10$IMLmNcm7tHO/vM8bklprW.0w0UNi.514EvNKTflFHhv1SD03v3OaK', NULL, 'admin'),
(2, 'Adam', 'xad4m3k.69@gmail.com', '$2y$10$BnbW2TWiLAsn8gVjY9BxgOl7IpAYQnnW0UhdI5Vmf23IlVD2/Rq0q', NULL, 'user'),
(3, 'Kurczak', 'sadam.cali1@gmail.com', '$2y$10$MZe7RPabN6PRDPAam3wWQ.cdgsdQGu1PJgsPCKnN53hczCY/1tveu', NULL, 'user');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_carts`
--

CREATE TABLE `user_carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indeksy dla tabeli `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `game_codes`
--
ALTER TABLE `game_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_game_codes_game_id` (`game_id`);

--
-- Indeksy dla tabeli `game_tags`
--
ALTER TABLE `game_tags`
  ADD PRIMARY KEY (`game_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `code_id` (`code_id`);

--
-- Indeksy dla tabeli `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indeksy dla tabeli `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `user_carts`
--
ALTER TABLE `user_carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `game_codes`
--
ALTER TABLE `game_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_carts`
--
ALTER TABLE `user_carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_codes`
--
ALTER TABLE `game_codes`
  ADD CONSTRAINT `fk_game_codes_game_id` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_tags`
--
ALTER TABLE `game_tags`
  ADD CONSTRAINT `game_tags_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`code_id`) REFERENCES `game_codes` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

--
-- Constraints for table `user_carts`
--
ALTER TABLE `user_carts`
  ADD CONSTRAINT `user_carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_carts_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
