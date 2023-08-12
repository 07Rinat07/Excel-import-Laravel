-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Авг 12 2023 г., 08:02
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
(1, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 2),
(2, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 3),
(3, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 4),
(4, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 5),
(5, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 6),
(6, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 7),
(7, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 8),
(8, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 9),
(9, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 10),
(10, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 11),
(11, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 12),
(12, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 13),
(13, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 14),
(14, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 15),
(15, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 16),
(16, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 17),
(17, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 18),
(18, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 19),
(19, 'Дата создания', 'The data_sozdaniia must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 20),
(20, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 2),
(21, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 3),
(22, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 4),
(23, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 5),
(24, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 6),
(25, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 7),
(26, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 8),
(27, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 9),
(28, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 10),
(29, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 11),
(30, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 12),
(31, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 13),
(32, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 14),
(33, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 15),
(34, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 16),
(35, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 17),
(36, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 18),
(37, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 19),
(38, 'Подписание договора', 'The podpisanie_dogovora must be a string.', 2, '2023-08-12 02:58:42', '2023-08-12 02:58:42', 20),
(39, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 2),
(40, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 3),
(41, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 4),
(42, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 5),
(43, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 6),
(44, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 7),
(45, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 8),
(46, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 9),
(47, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 10),
(48, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 11),
(49, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 12),
(50, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 13),
(51, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 14),
(52, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 15),
(53, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 16),
(54, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 17),
(55, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 18),
(56, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 19),
(57, 'Дата создания', 'The data_sozdaniia must be a string.', 3, '2023-08-12 02:59:45', '2023-08-12 02:59:45', 20);

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
(1, 'files//w6zvlm9PwOmPHrx7rvNOv7nJRSWGhRXfjhxvFYKK.bin', 'xlsx', 'projects.xlsx', '2023-08-12 01:10:07', '2023-08-12 01:10:07'),
(2, 'files//8jUIxpHcSrf7QFUyKDEbFhqc8UDgrlOCDRrvmLWc.bin', 'xlsx', 'projects.xlsx', '2023-08-12 02:58:40', '2023-08-12 02:58:40'),
(3, 'files//CdS3DZjlSZKj1UDhDp8txWzIgjnolpfrr892UPHR.bin', 'xlsx', 'projects2.xlsx', '2023-08-12 02:59:42', '2023-08-12 02:59:42'),
(4, 'files//C3JyyaWkIT55LPH9ug9IhXLCBi6jgxMiJmGyToo9.bin', 'xlsx', 'projects2.xlsx', '2023-08-12 03:00:03', '2023-08-12 03:00:03');

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
(21, '2014_10_12_000000_create_users_table', 1),
(22, '2014_10_12_100000_create_password_resets_table', 1),
(23, '2019_08_19_000000_create_failed_jobs_table', 1),
(24, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(25, '2023_08_08_054035_create_files_table', 1),
(26, '2023_08_09_053907_create_types_table', 1),
(27, '2023_08_09_053944_create_projects_table', 1),
(28, '2023_08_09_054007_create_tasks_table', 1),
(29, '2023_08_09_054107_create_failed_rows_table', 1),
(30, '2023_08_10_191253_add_column_row_to_failed_rows_table', 1);

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
  `deadline` date DEFAULT NULL,
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

INSERT INTO `projects` (`id`, `type_id`, `title`, `created_at_time`, `contracted_at`, `deadline`, `is_chain`, `is_on_time`, `has_outsource`, `has_investors`, `worker_count`, `service_count`, `payment_first_step`, `payment_second_step`, `payment_third_step`, `payment_forth_step`, `comment`, `effective_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'Лепесток', '2018-10-12', '2018-10-04', '2019-02-12', 1, 1, 1, 1, 10, 3, 600000, 435000, 600200, 320000, 'Laravel – фреймворк веб-приложения с выразительным, элегантным синтаксисом. Веб-фреймворк предлагает структуру и отправную точку для создания вашего приложения, позволяя вам сосредоточиться на создании чего-то удивительного, но пока мы не будем вдаваться в детали.', 20.00, '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(2, 2, 'Кожаночка', '2017-05-08', '2017-05-01', '2017-08-08', 0, 0, 0, 0, 12, 5, 900000, 685000, 600200, 685000, 'Laravel стремится обеспечить потрясающий опыт разработчика, предоставляя при этом мощный функционал: тщательное внедрение зависимостей, выразительный уровень абстракции базы данных, очереди и запланированные задачи, модульное и интеграционное тестирование и многое другое.', 14.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(3, 3, 'Блин в мундире', '2020-06-02', '2020-06-01', '2020-10-02', 0, 1, 1, 1, 10, 9, 1100000, 658000, 606200, 874000, 'Независимо от того, новичок ли вы в PHP, веб-фреймворках или имеете многолетний опыт, Laravel – это фреймворк, который может расти вместе с вами. Мы поможем вам сделать первые шаги в качестве веб-разработчика или подскажем, как вы поднимите свой опыт на новый уровень. Нам не терпится увидеть, что вы построите.', 13.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(4, 4, 'Две ноздри', '2019-07-24', '2019-07-20', '2019-10-24', 0, 1, 0, 0, 13, 2, 607000, 623000, 408000, 527000, 'При создании веб-приложения вам доступны различные инструменты и фреймворки. Однако мы считаем, что Laravel – лучший выбор для создания современных полнофункциональных веб-приложений.', 14.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(5, 5, 'Первая свежесть', '2021-09-18', '2021-09-17', '2021-11-18', 0, 0, 1, 1, 14, 4, 605000, 98600, 400500, 405000, 'Нам нравится называть Laravel «прогрессивным» фреймворком. Под этим мы подразумеваем, что Laravel растет вместе с вами. Если вы только делаете первые шаги в веб-разработке, обширная библиотека документации, руководств и видеоуроков Laravel поможет вам изучить основы, не перегружая себя.', 11.00, '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(6, 6, 'Ностальгия', '2015-04-05', '2015-04-02', '2015-06-05', 0, 1, 1, 0, 10, 5, 684000, 368000, 454000, 685000, 'Если вы старший разработчик, Laravel предлагает вам надежные инструменты для внедрения зависимостей, модульного тестирования, создания очередей, событий в реальном времени и многое другое. Laravel оптимизирован для создания профессиональных веб-приложений и готов обрабатывать корпоративные рабочие нагрузки.', 23.00, '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(7, 7, 'Ягодицепс', '2016-09-07', '2016-09-04', '2016-12-07', 0, 1, 0, 1, 12, 2, 650400, 162400, 350000, 460800, 'Laravel невероятно масштабируем. Благодаря удобному для масштабирования характеру PHP и встроенной поддержке быстрых распределенных систем кеширования, таких как Redis, горизонтальное масштабирование с Laravel очень просто. Фактически, приложения Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.', 27.00, '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(8, 8, 'Чил', '2020-11-18', '2020-11-09', '2021-02-18', 1, 1, 0, 0, 20, 4, 435000, 365000, 658000, 98500, 'Требуется экстремальное масштабирование? Такие платформы, как Laravel Vapor, позволяют запускать приложение Laravel в практически неограниченном масштабе с использованием новейшей бессерверной технологии AWS.', 13.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(9, 9, 'Накручу', '2017-02-12', '2017-02-11', '2017-04-12', 0, 1, 0, 0, 8, 3, 320000, 685000, 98500, 650400, 'Laravel объединяет лучшие пакеты в экосистеме PHP, чтобы предложить наиболее надежный и удобный для разработчиков фреймворк. Кроме того, тысячи талантливых разработчиков со всего мира внесли свой вклад в фреймворк. Кто знает, возможно, вы даже станете соучастником Laravel.', 10.00, '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(10, 10, 'От души в душу', '2022-10-15', '2022-10-14', NULL, 1, NULL, 0, 0, 18, 2, 600200, 658000, 325000, 365000, 'Мы хотим, чтобы начать работу с Laravel было как можно проще. Существует множество вариантов разработки и запуска проекта Laravel на вашем собственном компьютере. Хотя вы, возможно, захотите изучить эти варианты позже, но Laravel предлагает Sail – встроенное решение для запуска вашего проекта Laravel с помощью Docker.', 14.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(11, 11, 'В движении', '2016-03-04', '2016-03-02', '2016-07-04', 1, 1, 1, 1, 10, 3, 400500, 301000, 368000, 680500, 'Docker – это инструмент для запуска приложений и служб в небольших, легких «контейнерах», которые не мешают установленному на вашем локальном компьютере программному обеспечению или его конфигурации. Это означает, что вам не нужно беспокоиться о конфигурировании или настройке сложных инструментов разработки, таких как веб-серверы и базы данных на вашем персональном компьютере. Для начала вам нужно всего лишь установить Docker Desktop.', 13.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(12, 12, 'Банки-кабанки', '2015-10-12', '2015-10-11', '2016-02-12', 0, 1, 0, 0, 11, 5, 325000, 125000, 455000, 165000, 'Laravel Sail – это легкий интерфейс командной строки для взаимодействия с конфигурацией Docker по умолчанию в Laravel. Sail обеспечивает отличную отправную точку для создания приложения Laravel с использованием PHP, MySQL и Redis без предварительного опыта работы с Docker.', 27.00, '2023-08-12 01:10:10', '2023-08-12 03:00:05'),
(13, 13, 'Фитнесго', '2020-07-24', '2020-07-20', '2020-10-24', 1, 0, 1, 0, 16, 2, 98500, 160000, 230500, 190500, 'Если вы разрабатываете на Mac и Docker Desktop уже установлен, то вы можете использовать простую команду терминала для создания нового проекта Laravel. Например, чтобы создать новое приложение Laravel в каталоге с именем example-app, вы можете запустить следующую команду в своем терминале:', 24.00, '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(14, 14, 'Аятомат', '2018-11-24', '2018-11-21', '2018-12-24', 0, 1, 0, 0, 18, 7, 658000, 98600, 1420000, 365000, 'После создания проекта вы можете перейти в каталог приложения и запустить Laravel Sail. Laravel Sail предлагает простой интерфейс командной строки для взаимодействия с конфигурацией Docker по умолчанию в Laravel:', 21.00, '2023-08-12 01:10:10', '2023-08-12 03:00:06'),
(15, 15, 'Краски жизни', '2019-05-26', '2019-05-24', '2019-08-26', 0, 1, 1, 0, 12, 9, 1360000, 800000, 364000, 1120000, 'Если вы разрабатываете в Linux и Docker Desktop уже установлен, то вы можете использовать простую команду терминала для создания нового проекта Laravel. Например, чтобы создать новое приложение Laravel в каталоге с именем example-app, вы можете запустить следующую команду в своем терминале:', 18.00, '2023-08-12 01:10:10', '2023-08-12 03:00:06'),
(16, 16, 'Тоньше некуда', '2017-06-09', '2017-06-07', '2017-09-09', 1, 0, 0, 1, 10, 4, 685000, 168000, 147000, 600200, 'Пакет для создания приложений в стиле администрирования на фреймворке Laravel. Позволяет абстрагировать общие шаблоны бизнес-приложений, чтобы разработчикам было легко реализовывать красивые и элегантные интерфейсы без особых усилий.', 16.00, '2023-08-12 01:10:10', '2023-08-12 03:00:06'),
(17, 17, 'Три толстяка', '2021-12-06', '2021-12-05', '2022-02-06', 0, 1, 1, 1, 14, 4, 98600, 480600, 302600, 153600, 'Расширение для платформы IDEA(PhpStorm), экономящее время при разработке решений на основе Laravel. Прекрасное автозаполнение магии Laravel, навигация по коду, генераторы кода, автокомплит валидаторов и роутов, и многое другое.', 18.00, '2023-08-12 01:10:10', '2023-08-12 03:00:06'),
(18, 18, 'Кочка', '2022-08-22', '2022-08-20', NULL, 0, NULL, 1, 0, 10, 5, 365000, 325000, 410000, 368000, 'SleepingOwl Admin — это гибкая административная панель для Laravel. За свою более чем пятилетнюю историю — эта система зарекомендовала себя как мощное решение для разработки интерфейса любого уровня.', 34.00, '2023-08-12 01:10:10', '2023-08-12 03:00:06'),
(19, 19, 'Веер', '2018-02-07', '2018-02-04', '2018-04-07', 1, 1, 0, 1, 8, 3, 874000, 845000, 771000, 904000, 'Мы верим, что процесс разработки только тогда наиболее продуктивен, когда работа с фреймворком приносит радость и удовольствие. Счастливые разработчики пишут лучший код.', 32.00, '2023-08-12 01:10:10', '2023-08-12 03:00:06'),
(20, 15, 'Лепесток', '2018-10-12', '2018-10-04', '2019-02-12', 1, 1, 1, 1, 10, 3, 600000, 435000, 600200, 320000, 'Laravel – фреймворк веб-приложения с выразительным, элегантным синтаксисом. Веб-фреймворк предлагает структуру и отправную точку для создания вашего приложения, позволяя вам сосредоточиться на создании чего-то удивительного, но пока мы не будем вдаваться в детали.', 20.00, '2023-08-12 03:00:05', '2023-08-12 03:00:05'),
(21, 14, 'Первая свежесть', '2021-09-18', '2021-09-17', '2021-11-18', 0, 0, 1, 1, 14, 4, 605000, 98600, 400500, 405000, 'Нам нравится называть Laravel «прогрессивным» фреймворком. Под этим мы подразумеваем, что Laravel растет вместе с вами. Если вы только делаете первые шаги в веб-разработке, обширная библиотека документации, руководств и видеоуроков Laravel поможет вам изучить основы, не перегружая себя.', 11.00, '2023-08-12 03:00:05', '2023-08-12 03:00:05'),
(22, 8, 'Ностальгия', '2015-04-05', '2015-04-02', '2015-06-05', 0, 1, 1, 0, 10, 5, 684000, 368000, 454000, 685000, 'Если вы старший разработчик, Laravel предлагает вам надежные инструменты для внедрения зависимостей, модульного тестирования, создания очередей, событий в реальном времени и многое другое. Laravel оптимизирован для создания профессиональных веб-приложений и готов обрабатывать корпоративные рабочие нагрузки.', 23.00, '2023-08-12 03:00:05', '2023-08-12 03:00:05'),
(23, 18, 'Ягодицепс', '2016-09-07', '2016-09-04', '2016-12-07', 0, 1, 0, 1, 12, 2, 650400, 162400, 350000, 460800, 'Laravel невероятно масштабируем. Благодаря удобному для масштабирования характеру PHP и встроенной поддержке быстрых распределенных систем кеширования, таких как Redis, горизонтальное масштабирование с Laravel очень просто. Фактически, приложения Laravel легко масштабируются для обработки сотен миллионов запросов в месяц.', 27.00, '2023-08-12 03:00:05', '2023-08-12 03:00:05'),
(24, 19, 'Накручу', '2017-02-12', '2017-02-11', '2017-04-12', 0, 1, 0, 0, 8, 3, 320000, 685000, 98500, 650400, 'Laravel объединяет лучшие пакеты в экосистеме PHP, чтобы предложить наиболее надежный и удобный для разработчиков фреймворк. Кроме того, тысячи талантливых разработчиков со всего мира внесли свой вклад в фреймворк. Кто знает, возможно, вы даже станете соучастником Laravel.', 10.00, '2023-08-12 03:00:05', '2023-08-12 03:00:05'),
(25, 18, 'Фитнесго', '2020-07-24', '2020-07-20', '2020-10-24', 1, 0, 1, 0, 16, 2, 98500, 160000, 230500, 190500, 'Если вы разрабатываете на Mac и Docker Desktop уже установлен, то вы можете использовать простую команду терминала для создания нового проекта Laravel. Например, чтобы создать новое приложение Laravel в каталоге с именем example-app, вы можете запустить следующую команду в своем терминале:', 24.00, '2023-08-12 03:00:06', '2023-08-12 03:00:06');

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
(1, 1, 1, 2, '2023-08-12 01:10:07', '2023-08-12 01:10:07'),
(2, 1, 2, 3, '2023-08-12 02:58:40', '2023-08-12 02:58:42'),
(3, 1, 3, 3, '2023-08-12 02:59:42', '2023-08-12 02:59:45'),
(4, 1, 4, 2, '2023-08-12 03:00:03', '2023-08-12 03:00:03');

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
(1, 'Цветочный магазин', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(2, 'Обувной магазин', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(3, 'Закусочная', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(4, 'Кинотеатр для двоих', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(5, 'Лавка овощей и фруктов', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(6, 'Игровой клуб', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(7, 'Фитнес клуб', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(8, 'Игровой клуб', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(9, 'Парикмахерская', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(10, 'Спа салон', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(11, 'Магазин спортивной одежды', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(12, 'Магазин спортивного питания', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(13, 'Фитнес клуб', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(14, 'Лавка овощей и фруктов', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(15, 'Цветочный магазин', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(16, 'Магазин женского белья', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(17, 'Кафе', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(18, 'Фитнес клуб', '2023-08-12 01:10:10', '2023-08-12 01:10:10'),
(19, 'Парикмахерская', '2023-08-12 01:10:10', '2023-08-12 01:10:10');

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
(1, 'user', 'user@mail.ru', NULL, '$2y$10$cvFx8sMjSxWQjvBYqGtkB./Sp05clX9jdvT7wkacJMI2sRH9ntfFu', NULL, '2023-08-12 01:09:48', '2023-08-12 01:09:48');

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT для таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
