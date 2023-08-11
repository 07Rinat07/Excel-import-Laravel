-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Авг 11 2023 г., 13:38
-- Версия сервера: 8.0.30
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `excel`
--

-- --------------------------------------------------------

--
-- Структура таблицы `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `failed_rows`
--

CREATE TABLE `failed_rows` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `row` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `failed_rows`
--

INSERT INTO `failed_rows` (`id`, `key`, `message`, `task_id`, `created_at`, `updated_at`, `row`) VALUES
(1, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 2),
(2, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 3),
(3, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 4),
(4, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 5),
(5, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 6),
(6, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 7),
(7, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 8),
(8, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 9),
(9, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 10),
(10, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 11),
(11, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 12),
(12, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 13),
(13, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 14),
(14, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 15),
(15, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 16),
(16, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 17),
(17, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 18),
(18, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 19),
(19, 'Дата создания', 'The data_sozdaniia must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 20),
(20, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 2),
(21, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 3),
(22, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 4),
(23, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 5),
(24, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 6),
(25, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 7),
(26, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 8),
(27, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 9),
(28, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 10),
(29, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 11),
(30, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 12),
(31, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 13),
(32, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 14),
(33, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 15),
(34, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 16),
(35, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 17),
(36, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 18),
(37, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 19),
(38, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 1, '2023-08-11 08:28:26', '2023-08-11 08:28:26', 20),
(39, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 2),
(40, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 3),
(41, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 4),
(42, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 5),
(43, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 6),
(44, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 7),
(45, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 8),
(46, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 9),
(47, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 10),
(48, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 11),
(49, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 12),
(50, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 13),
(51, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 14),
(52, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 15),
(53, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 16),
(54, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 17),
(55, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 18),
(56, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 19),
(57, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 20),
(58, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 2),
(59, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 3),
(60, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 4),
(61, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 5),
(62, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 6),
(63, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 7),
(64, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 8),
(65, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 9),
(66, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 10),
(67, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 11),
(68, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 12),
(69, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 13),
(70, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 14),
(71, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 15),
(72, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 16),
(73, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 17),
(74, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 18),
(75, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 19),
(76, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-11 08:28:46', '2023-08-11 08:28:46', 20),
(77, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 2),
(78, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 3),
(79, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 4),
(80, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 5),
(81, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 6),
(82, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 7),
(83, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 8),
(84, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 9),
(85, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 10),
(86, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 11),
(87, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 12),
(88, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 13),
(89, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 14),
(90, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 15),
(91, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 16),
(92, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 17),
(93, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 18),
(94, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 19),
(95, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 20),
(96, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 2),
(97, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 3),
(98, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 4),
(99, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 5),
(100, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 6),
(101, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 7),
(102, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 8),
(103, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 9),
(104, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 10),
(105, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 11),
(106, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 12),
(107, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 13),
(108, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 14),
(109, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 15),
(110, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 16),
(111, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 17),
(112, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 18),
(113, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 19),
(114, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 3, '2023-08-11 08:28:55', '2023-08-11 08:28:55', 20),
(115, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 2),
(116, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 3),
(117, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 4),
(118, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 5),
(119, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 6),
(120, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 7),
(121, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 8),
(122, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 9),
(123, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 10),
(124, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 11),
(125, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 12),
(126, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 13),
(127, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 14),
(128, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 15),
(129, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 16),
(130, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 17),
(131, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 18),
(132, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 19),
(133, 'Дата создания', 'The data_sozdaniia must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 20),
(134, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 2),
(135, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 3),
(136, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 4),
(137, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 5),
(138, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 6),
(139, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 7),
(140, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 8),
(141, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 9),
(142, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 10),
(143, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 11),
(144, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 12),
(145, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 13),
(146, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 14),
(147, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 15),
(148, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 16),
(149, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 17),
(150, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 18),
(151, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 19),
(152, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 4, '2023-08-11 08:29:03', '2023-08-11 08:29:03', 20),
(153, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 2),
(154, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 3),
(155, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 4),
(156, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 5),
(157, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 6),
(158, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 7),
(159, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 8),
(160, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 9),
(161, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 10),
(162, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 11),
(163, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 12),
(164, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 13),
(165, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 14),
(166, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 15),
(167, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 16),
(168, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 17),
(169, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 18),
(170, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 19),
(171, 'Дата создания', 'The data_sozdaniia must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 20),
(172, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 2),
(173, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 3),
(174, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 4),
(175, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 5),
(176, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 6),
(177, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 7),
(178, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 8),
(179, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 9),
(180, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 10),
(181, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 11),
(182, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 12),
(183, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 13),
(184, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 14),
(185, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 15),
(186, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 16),
(187, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 17),
(188, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 18),
(189, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 19),
(190, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 5, '2023-08-11 08:29:09', '2023-08-11 08:29:09', 20),
(191, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 2),
(192, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 3),
(193, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 4),
(194, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 5),
(195, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 6),
(196, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 7),
(197, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 8),
(198, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 9),
(199, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 10),
(200, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 11),
(201, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 12),
(202, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 13),
(203, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 14),
(204, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 15),
(205, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 16),
(206, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 17),
(207, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 18),
(208, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 19),
(209, 'Дата создания', 'The data_sozdaniia must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 20),
(210, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 2),
(211, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 3),
(212, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 4),
(213, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 5),
(214, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 6),
(215, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 7),
(216, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 8),
(217, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 9),
(218, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 10),
(219, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 11),
(220, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 12),
(221, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 13),
(222, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 14),
(223, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 15),
(224, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 16),
(225, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 17),
(226, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 18),
(227, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 19),
(228, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 6, '2023-08-11 08:29:17', '2023-08-11 08:29:17', 20),
(229, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 2),
(230, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 3),
(231, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 4),
(232, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 5),
(233, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 6),
(234, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 7),
(235, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 8),
(236, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 9),
(237, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 10),
(238, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 11),
(239, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 12),
(240, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 13),
(241, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 14),
(242, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 15),
(243, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 16),
(244, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 17),
(245, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 18),
(246, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 19),
(247, 'Дата создания', 'The data_sozdaniia must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 20),
(248, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 2),
(249, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 3),
(250, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 4),
(251, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 5),
(252, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 6),
(253, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 7),
(254, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 8),
(255, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 9),
(256, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 10),
(257, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 11),
(258, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 12),
(259, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 13),
(260, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 14),
(261, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 15),
(262, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 16),
(263, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 17),
(264, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 18),
(265, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 19),
(266, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 7, '2023-08-11 08:29:25', '2023-08-11 08:29:25', 20),
(267, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 2),
(268, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 3),
(269, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 4),
(270, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 5),
(271, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 6),
(272, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 7),
(273, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 8),
(274, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 9),
(275, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 10),
(276, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 11),
(277, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 12),
(278, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 13),
(279, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 14),
(280, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 15),
(281, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 16),
(282, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 17),
(283, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 18),
(284, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 19),
(285, 'Дата создания', 'The data_sozdaniia must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 20),
(286, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 2),
(287, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 3),
(288, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 4),
(289, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 5),
(290, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 6),
(291, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 7),
(292, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 8),
(293, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 9),
(294, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 10),
(295, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 11),
(296, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 12),
(297, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 13),
(298, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 14),
(299, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 15),
(300, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 16),
(301, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 17),
(302, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 18),
(303, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 19),
(304, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 8, '2023-08-11 08:29:35', '2023-08-11 08:29:35', 20),
(305, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 2),
(306, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 3),
(307, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 4),
(308, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 5),
(309, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 6),
(310, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 7),
(311, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 8),
(312, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 9),
(313, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 10),
(314, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 11),
(315, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 12),
(316, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 13),
(317, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 14),
(318, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 15),
(319, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 16),
(320, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 17),
(321, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 18),
(322, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 19),
(323, 'Дата создания', 'The data_sozdaniia must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 20),
(324, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 2),
(325, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 3),
(326, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 4),
(327, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 5),
(328, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 6),
(329, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 7),
(330, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 8),
(331, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 9),
(332, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 10),
(333, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 11),
(334, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 12),
(335, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 13),
(336, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 14),
(337, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 15),
(338, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 16),
(339, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 17),
(340, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 18),
(341, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 19),
(342, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 9, '2023-08-11 08:29:43', '2023-08-11 08:29:43', 20),
(343, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 2),
(344, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 3),
(345, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 4),
(346, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 5),
(347, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 6),
(348, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 7),
(349, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 8),
(350, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 9),
(351, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 10),
(352, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 11),
(353, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 12),
(354, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 13),
(355, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 14),
(356, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 15),
(357, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 16),
(358, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 17),
(359, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 18),
(360, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 19),
(361, 'Дата создания', 'The data_sozdaniia must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 20),
(362, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 2),
(363, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 3),
(364, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 4),
(365, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 5),
(366, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 6),
(367, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 7),
(368, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 8),
(369, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 9),
(370, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 10),
(371, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 11),
(372, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 12),
(373, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 13),
(374, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 14),
(375, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 15),
(376, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 16),
(377, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 17),
(378, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 18),
(379, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 19),
(380, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 10, '2023-08-11 08:29:50', '2023-08-11 08:29:50', 20),
(381, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 2),
(382, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 3),
(383, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 4),
(384, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 5),
(385, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 6),
(386, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 7),
(387, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 8),
(388, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 9),
(389, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 10),
(390, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 11),
(391, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 12),
(392, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 13),
(393, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 14),
(394, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 15),
(395, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 16),
(396, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 17),
(397, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 18),
(398, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 19),
(399, 'Дата создания', 'The data_sozdaniia must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 20),
(400, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 2),
(401, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 3),
(402, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 4),
(403, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 5),
(404, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 6),
(405, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 7),
(406, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 8),
(407, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 9),
(408, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 10),
(409, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 11),
(410, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 12),
(411, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 13),
(412, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 14),
(413, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 15),
(414, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 16),
(415, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 17);
INSERT INTO `failed_rows` (`id`, `key`, `message`, `task_id`, `created_at`, `updated_at`, `row`) VALUES
(416, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 18),
(417, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 19),
(418, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 11, '2023-08-11 08:29:59', '2023-08-11 08:29:59', 20),
(419, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 2),
(420, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 3),
(421, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 4),
(422, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 5),
(423, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 6),
(424, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 7),
(425, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 8),
(426, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 9),
(427, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 10),
(428, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 11),
(429, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 12),
(430, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 13),
(431, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 14),
(432, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 15),
(433, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 16),
(434, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 17),
(435, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 18),
(436, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 19),
(437, 'Дата создания', 'The data_sozdaniia must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 20),
(438, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 2),
(439, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 3),
(440, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 4),
(441, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 5),
(442, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 6),
(443, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 7),
(444, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 8),
(445, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 9),
(446, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 10),
(447, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 11),
(448, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 12),
(449, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 13),
(450, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 14),
(451, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 15),
(452, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 16),
(453, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 17),
(454, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 18),
(455, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 19),
(456, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 12, '2023-08-11 08:30:28', '2023-08-11 08:30:28', 20);

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `files`
--

INSERT INTO `files` (`id`, `path`, `mime_type`, `title`, `created_at`, `updated_at`) VALUES
(1, 'files//UUp9k8lD36UefmfNXj4jmjYykUhPgVPHoPxL6NrY.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:28:22', '2023-08-11 08:28:22'),
(2, 'files//jPaA0lFN1WXtXXoJXplnZzQ9INxHkQWV4087Xvwl.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:28:44', '2023-08-11 08:28:44'),
(3, 'files//7UgFDTv06tBr5mQMpNwzjK2vb1XqgAylVn54iL2G.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:28:52', '2023-08-11 08:28:52'),
(4, 'files//pRCprzvke5IiV27vPYZpmGAiDlC2uquok0X6zCTQ.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:29:00', '2023-08-11 08:29:00'),
(5, 'files//mdQwzBm1toSB9wYzCgI3gg7i3E18BFG2SbNSspax.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:29:07', '2023-08-11 08:29:07'),
(6, 'files//yLrULdO5ducaScANDH8cOeg2wC6LaWFGGOhRXKAp.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:29:15', '2023-08-11 08:29:15'),
(7, 'files//hEVYO1bkSvub0a2VjHoc9JBRICTNcOKn3Fn8Damm.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:29:22', '2023-08-11 08:29:22'),
(8, 'files//OXuvXJokVITJ89hb91neDOBveAHKvmf5ZFN4GYKV.bin', 'xlsx', 'projects2.xlsx', '2023-08-11 08:29:33', '2023-08-11 08:29:33'),
(9, 'files//qJVTO7Sk3NBqJawKpQHbjhADCoGQS1kLYiGbke7c.bin', 'xlsx', 'projects2.xlsx', '2023-08-11 08:29:41', '2023-08-11 08:29:41'),
(10, 'files//v0cCL7tYixgWfINEZw4h4gDJuqoqvI1wwH5791xU.bin', 'xlsx', 'projects2.xlsx', '2023-08-11 08:29:48', '2023-08-11 08:29:48'),
(11, 'files//kBiYd20q2qGBVHIxCBkvcOzrARmAKiWNZ7LavPoc.bin', 'xlsx', 'projects2.xlsx', '2023-08-11 08:29:56', '2023-08-11 08:29:56'),
(12, 'files//CGUnDaKBBMonokQVYvPY144RzF6bXulh0T1XygV8.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:30:26', '2023-08-11 08:30:26'),
(13, 'files//jLC8N4nq8zMDM3MdZhZCdgAjjhRtmvLw4duc5UFt.bin', 'xlsx', 'projects.xlsx', '2023-08-11 08:30:53', '2023-08-11 08:30:53');

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_08_08_054035_create_files_table', 1),
(6, '2023_08_09_053907_create_types_table', 1),
(7, '2023_08_09_053944_create_projects_table', 1),
(8, '2023_08_09_054007_create_tasks_table', 1),
(9, '2023_08_09_054107_create_failed_rows_table', 1),
(10, '2023_08_10_191253_add_column_row_to_failed_rows_table', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `projects`
--

CREATE TABLE `projects` (
  `id` bigint UNSIGNED NOT NULL,
  `type_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at_time` date NOT NULL,
  `contracted_at` date NOT NULL,
  `dead_line` date DEFAULT NULL,
  `is_chain` tinyint(1) DEFAULT NULL,
  `is_on_time` tinyint(1) DEFAULT NULL,
  `has_outsource` tinyint(1) DEFAULT NULL,
  `has_investors` tinyint(1) DEFAULT NULL,
  `worker_count` int DEFAULT NULL,
  `service_count` int DEFAULT NULL,
  `payment_first_step` int DEFAULT NULL,
  `payment_second_step` int DEFAULT NULL,
  `payment_third_step` int DEFAULT NULL,
  `payment_forth_step` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `effective_value` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `projects`
--

INSERT INTO `projects` (`id`, `type_id`, `title`, `created_at_time`, `contracted_at`, `dead_line`, `is_chain`, `is_on_time`, `has_outsource`, `has_investors`, `worker_count`, `service_count`, `payment_first_step`, `payment_second_step`, `payment_third_step`, `payment_forth_step`, `comment`, `effective_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'Лепесток', '2018-10-12', '2018-10-04', '2019-02-12', 1, 1, 1, 1, 10, 3, 600000, 435000, 600200, 320000, 'Laravel – фреймворк веб-приложения с выразительным, элегантным синтаксисом. Веб-фреймворк предлагает структуру и отправную точку для создания вашего приложения, позволяя вам сосредоточиться на создании чего-то удивительного, но пока мы не будем вдаваться в детали.', 20.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(2, 2, 'Кожаночка', '2017-05-08', '2017-05-01', '2017-08-08', 0, 0, 0, 0, 12, 5, 900000, 685000, 600200, 685000, 'Laravel стремится обеспечить потрясающий опыт разработчика, предоставляя при этом мощный функционал: тщательное внедрение зависимостей, выразительный уровень абстракции базы данных, очереди и запланированные задачи, модульное и интеграционное тестирование и многое другое.', 14.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(3, 3, 'Блин в мундире', '2020-06-02', '2020-06-01', '2020-10-02', 0, 1, 1, 1, 10, 9, 1100000, 658000, 606200, 874000, 'Независимо от того, новичок ли вы в PHP, веб-фреймворках или имеете многолетний опыт, Laravel – это фреймворк, который может расти вместе с вами. Мы поможем вам сделать первые шаги в качестве веб-разработчика или подскажем, как вы поднимите свой опыт на новый уровень. Нам не терпится увидеть, что вы построите.', 13.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(4, 4, 'Две ноздри', '2019-07-24', '2019-07-20', '2019-10-24', 0, 1, 0, 0, 13, 2, 607000, 623000, 408000, 527000, 'При создании веб-приложения вам доступны различные инструменты и фреймворки. Однако мы считаем, что Laravel – лучший выбор для создания современных полнофункциональных веб-приложений.', 14.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(5, 5, 'Первая свежесть', '2021-09-18', '2021-09-17', '2021-11-18', 0, 0, 1, 1, 14, 4, 605000, 98600, 400500, 405000, 'Нам нравится называть Laravel «прогрессивным» фреймворком. Под этим мы подразумеваем, что Laravel растет вместе с вами. Если вы только делаете первые шаги в веб-разработке, обширная библиотека документации, руководств и видеоуроков Laravel поможет вам изучить основы, не перегружая себя.', 11.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(6, 6, 'Ностальгия', '2015-04-05', '2015-04-02', '2015-06-05', 0, 1, 1, 0, 10, 5, 684000, 368000, 454000, 685000, 'Если вы старший разработчик, Laravel предлагает вам надежные инструменты для внедрения зависимостей, модульного тестирования, создания очередей, событий в реальном времени и многое другое. Laravel оптимизирован для создания профессиональных веб-приложений и готов обрабатывать корпоративные рабочие нагрузки.', 23.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(7, 7, 'Ягодицепс', '2016-09-07', '2016-09-04', '2016-12-07', 0, 1, 0, 1, 12, 2, 650400, 162400, 350000, 460800, 'Laravel невероятно масштабируем. Благодаря удобному для масштабирования характеру PHP и встроенной поддержке быстрых распределенных систем кеширования, таких как Redis, горизонтальное масштабирование с Laravel очень просто. Фактически, приложения Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.', 27.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(8, 8, 'Чил', '2020-11-18', '2020-11-09', '2021-02-18', 1, 1, 0, 0, 20, 4, 435000, 365000, 658000, 98500, 'Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать приложение Laravel в практически неограниченном масштабе с использованием новейшей бессерверной технологии AWS.', 13.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(9, 9, 'Накручу', '2017-02-12', '2017-02-11', '2017-04-12', 0, 1, 0, 0, 8, 3, 320000, 685000, 98500, 650400, 'Laravel объединяет лучшие пакеты в экосистеме PHP, чтобы предложить наиболее надежный и удобный для разработчиков фреймворк. Кроме того, тысячи талантливых разработчиков со всего мира внесли свой вклад в фреймворк. Кто знает, возможно, вы даже станете соучастником Laravel.', 10.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(10, 10, 'От души в душу', '2022-10-15', '2022-10-14', NULL, 1, NULL, 0, 0, 18, 2, 600200, 658000, 325000, 365000, 'Мы хотим, чтобы начать работу с Laravel было как можно проще. Существует множество вариантов разработки и запуска проекта Laravel на вашем собственном компьютере. Хотя вы, возможно, захотите изучить эти варианты позже, но Laravel предлагает Sail – встроенное решение для запуска вашего проекта Laravel с помощью Docker.', 14.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(11, 11, 'В движении', '2016-03-04', '2016-03-02', '2016-07-04', 1, 1, 1, 1, 10, 3, 400500, 301000, 368000, 680500, 'Docker – это инструмент для запуска приложений и служб в небольших, легких «контейнерах», которые не мешают установленному на вашем локальном компьютере программному обеспечению или его конфигурации. Это означает, что вам не нужно беспокоиться о конфигурировании или настройке сложных инструментов разработки, таких как веб-серверы и базы данных на вашем персональном компьютере. Для начала вам нужно всего лишь установить Docker Desktop.', 13.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(12, 12, 'Банки-кабанки', '2015-10-12', '2015-10-11', '2016-02-12', 0, 1, 0, 0, 11, 5, 325000, 125000, 455000, 165000, 'Laravel Sail – это легкий интерфейс командной строки для взаимодействия с конфигурацией Docker по умолчанию в Laravel. Sail обеспечивает отличную отправную точку для создания приложения Laravel с использованием PHP, MySQL и Redis без предварительного опыта работы с Docker.', 27.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(13, 13, 'Фитнесго', '2020-07-24', '2020-07-20', '2020-10-24', 1, 0, 1, 0, 16, 2, 98500, 160000, 230500, 190500, 'Если вы разрабатываете на Mac и Docker Desktop уже установлен, то вы можете использовать простую команду терминала для создания нового проекта Laravel. Например, чтобы создать новое приложение Laravel в каталоге с именем example-app, вы можете запустить следующую команду в своем терминале:', 24.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(14, 14, 'Аятомат', '2018-11-24', '2018-11-21', '2018-12-24', 0, 1, 0, 0, 18, 7, 658000, 98600, 1420000, 365000, 'После создания проекта вы можете перейти в каталог приложения и запустить Laravel Sail. Laravel Sail предлагает простой интерфейс командной строки для взаимодействия с конфигурацией Docker по умолчанию в Laravel:', 21.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(15, 15, 'Краски жизни', '2019-05-26', '2019-05-24', '2019-08-26', 0, 1, 1, 0, 12, 9, 1360000, 800000, 364000, 1120000, 'Если вы разрабатываете в Linux и Docker Desktop уже установлен, то вы можете использовать простую команду терминала для создания нового проекта Laravel. Например, чтобы создать новое приложение Laravel в каталоге с именем example-app, вы можете запустить следующую команду в своем терминале:', 18.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(16, 16, 'Тоньше некуда', '2017-06-09', '2017-06-07', '2017-09-09', 1, 0, 0, 1, 10, 4, 685000, 168000, 147000, 600200, 'Пакет для создания приложений в стиле администрирования на фреймворке Laravel. Позволяет абстрагировать общие шаблоны бизнес-приложений, чтобы разработчикам было легко реализовывать красивые и элегантные интерфейсы без особых усилий.', 16.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(17, 17, 'Три толстяка', '2021-12-06', '2021-12-05', '2022-02-06', 0, 1, 1, 1, 14, 4, 98600, 480600, 302600, 153600, 'Расширение для платформы IDEA(PhpStorm), экономящее время при разработке решений на основе Laravel. Прекрасное автозаполнение магии Laravel, навигация по коду, генераторы кода, автокомплит валидаторов и роутов, и многое другое.', 18.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(18, 18, 'Кочка', '2022-08-22', '2022-08-20', NULL, 0, NULL, 1, 0, 10, 5, 365000, 325000, 410000, 368000, 'SleepingOwl Admin — это гибкая административная панель для Laravel. За свою более чем пятилетнюю историю — эта система зарекомендовала себя как мощное решение для разработки интерфейса любого уровня.', 34.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(19, 19, 'Веер', '2018-02-07', '2018-02-04', '2018-04-07', 1, 1, 0, 1, 8, 3, 874000, 845000, 771000, 904000, 'Мы верим, что процесс разработки только тогда наиболее продуктивен, когда работа с фреймворком приносит радость и удовольствие. Счастливые разработчики пишут лучший код.', 32.00, '2023-08-11 08:30:56', '2023-08-11 08:30:56');

-- --------------------------------------------------------

--
-- Структура таблицы `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `file_id` bigint UNSIGNED NOT NULL,
  `status` smallint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `file_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, '2023-08-11 08:28:22', '2023-08-11 08:28:26'),
(2, 1, 2, 3, '2023-08-11 08:28:44', '2023-08-11 08:28:46'),
(3, 1, 3, 3, '2023-08-11 08:28:52', '2023-08-11 08:28:55'),
(4, 1, 4, 3, '2023-08-11 08:29:00', '2023-08-11 08:29:03'),
(5, 1, 5, 3, '2023-08-11 08:29:07', '2023-08-11 08:29:09'),
(6, 1, 6, 3, '2023-08-11 08:29:15', '2023-08-11 08:29:17'),
(7, 1, 7, 3, '2023-08-11 08:29:22', '2023-08-11 08:29:25'),
(8, 1, 8, 3, '2023-08-11 08:29:33', '2023-08-11 08:29:35'),
(9, 1, 9, 3, '2023-08-11 08:29:41', '2023-08-11 08:29:43'),
(10, 1, 10, 3, '2023-08-11 08:29:48', '2023-08-11 08:29:50'),
(11, 1, 11, 3, '2023-08-11 08:29:56', '2023-08-11 08:29:59'),
(12, 1, 12, 3, '2023-08-11 08:30:26', '2023-08-11 08:30:28'),
(13, 1, 13, 2, '2023-08-11 08:30:53', '2023-08-11 08:30:53');

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE `types` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `types`
--

INSERT INTO `types` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1, 'Цветочный магазин', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(2, 'Обувной магазин', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(3, 'Закусочная', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(4, 'Кинотеатр для двоих', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(5, 'Лавка овощей и фруктов', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(6, 'Игровой клуб', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(7, 'Фитнес клуб', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(8, 'Игровой клуб', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(9, 'Парикмахерская', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(10, 'Спа салон', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(11, 'Магазин спортивной одежды', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(12, 'Магазин спортивного питания', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(13, 'Фитнес клуб', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(14, 'Лавка овощей и фруктов', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(15, 'Цветочный магазин', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(16, 'Магазин женского белья', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(17, 'Кафе', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(18, 'Фитнес клуб', '2023-08-11 08:30:56', '2023-08-11 08:30:56'),
(19, 'Парикмахерская', '2023-08-11 08:30:56', '2023-08-11 08:30:56');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'user', 'user@mail.ru', NULL, '$2y$10$hqXf8K0G9NsaFKbtmp/0uOLPk4Vp4ap3KuolSmRzejcjnIqfgNn1.', NULL, '2023-08-11 08:24:36', '2023-08-11 08:24:36');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Индексы таблицы `failed_rows`
--
ALTER TABLE `failed_rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `failed_rows_task_id_index` (`task_id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Индексы таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Индексы таблицы `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_type_id_index` (`type_id`);

--
-- Индексы таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_user_id_index` (`user_id`),
  ADD KEY `tasks_file_id_index` (`file_id`);

--
-- Индексы таблицы `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `failed_rows`
--
ALTER TABLE `failed_rows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `types`
--
ALTER TABLE `types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `failed_rows`
--
ALTER TABLE `failed_rows`
  ADD CONSTRAINT `failed_rows_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Ограничения внешнего ключа таблицы `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`);

--
-- Ограничения внешнего ключа таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`),
  ADD CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
