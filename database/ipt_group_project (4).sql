-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 06:58 AM
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
-- Database: `ipt_group_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `career_objectives`
--

CREATE TABLE `career_objectives` (
  `id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `objective` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `career_objectives`
--

INSERT INTO `career_objectives` (`id`, `personal_info_id`, `objective`, `created_at`, `updated_at`) VALUES
(5, 11, 'to achieve 100k salary', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(6, 12, 'maging mayaman\r\n', '2026-02-14 02:34:15', '2026-02-14 02:34:15'),
(15, 17, 'd', '2026-02-14 12:35:57', '2026-02-14 12:35:57'),
(18, 20, 'adad', '2026-02-14 14:14:19', '2026-02-14 14:14:19'),
(19, 21, 'sdasd', '2026-02-14 17:10:32', '2026-02-14 17:10:32'),
(24, 26, 'sdasdasd', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(25, 27, 'to work at your company', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(26, 28, 'to work at your company', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(27, 29, 'ae231', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(28, 30, 'sdadsasd', '2026-02-14 20:57:19', '2026-02-14 20:57:19'),
(29, 31, 'asdadad', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(30, 32, 'Handa akong manatili\r\nHanggang sa\'yong pagkalma\r\nMagsisilbing lakas mo\r\nTuwing nanghihina ka\r\nPatatahanin kita kapag\r\nMay problema ka\r\nDadamayan ka sa lahat\r\nAking sinta\r\nKahit ga\'no man kalalim\r\nAy akin yang lalanguyin\r\nKahit ga\'no man kalalim\r\nAy akin yang lalanguyin\r\nSasagipin kita\r\nSasagipin kita\r\nSasagipin\r\nSasagipin kita', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(33, 18, 's', '2026-02-15 03:12:39', '2026-02-15 03:12:39'),
(39, 13, 'di ko alam ', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(62, 10, 'to work at you company', '2026-02-15 05:46:53', '2026-02-15 05:46:53'),
(63, 33, 'To build it properly, I need some information from you. Please fill in the template below (you can copy and paste it and answer each section', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(64, 34, 'asdad', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `personal_info_id`, `degree`, `institution`, `start_date`, `end_date`, `description`, `created_at`, `updated_at`) VALUES
(2, 11, 'Bachelor of Science Major in Information Technology', 'Pangasinan State University', '2005-01-01', '2009-12-01', 'avoidant', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(3, 11, 'Bachelor of Science Major in Information Technology', 'nation', '2003-02-12', '2005-01-21', 'wala', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(4, 12, 'Bachelor of Science Major in Information Technology', 'Pangasinan State University', '2005-12-07', '2009-07-01', 'Magaling lang chumismis', '2026-02-14 02:34:15', '2026-02-14 02:34:15'),
(15, 26, 'PSu', 'asdad', '2001-12-12', '2005-12-12', 'ada', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(16, 27, 'PSu', 'asdad', '2001-12-12', '2005-12-12', 'ada', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(17, 28, 'PSu', 'asdad', '2001-12-12', '2005-12-12', 'ada', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(18, 29, 'Bachelor of Science Major in Information Technology', 'Pangasinan State University', '2012-12-12', '2014-03-12', 'dasd', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(19, 30, 'BSIt', 'psu', '2000-12-12', '2000-01-01', '44242', '2026-02-14 20:57:19', '2026-02-14 20:57:19'),
(20, 31, 'BSIt', 'psu', '2222-12-12', '2222-12-12', '12121', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(21, 32, 'Bachelor of Science Major in Information Technology', 'nation', '0012-03-12', '0123-03-12', 'dada', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(24, 13, 'Bachelor of Science Major in Information Technology', 'Pangasinan State University', '2022-05-05', '2027-05-20', 'Cumlaude', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(47, 10, 'Bachelor of Science Major in Information Technology', 'Pangasinan State University', '2016-01-25', '2019-01-01', 'wew', '2026-02-15 05:46:53', '2026-02-15 05:46:53'),
(48, 33, 'Pangasinan State university', 'sdadda', '2005-01-01', '2009-10-01', 'asdasd', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(49, 34, 'asd', 'asdad', '0032-03-12', '4123-03-12', '123', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE `interests` (
  `id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `interests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`id`, `personal_info_id`, `interests`, `created_at`, `updated_at`) VALUES
(3, 11, 'Magsend ng reels', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(4, 12, 'Chumismis', '2026-02-14 02:34:15', '2026-02-14 02:34:15'),
(8, 20, 'qweqwe', '2026-02-14 14:14:19', '2026-02-14 14:14:19'),
(9, 26, 'asd', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(10, 27, 'chess', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(11, 28, 'chess', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(12, 29, 'wala', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(13, 30, '0202', '2026-02-14 20:57:19', '2026-02-14 20:57:19'),
(14, 31, '3asdad', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(15, 32, 'sadasdasd', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(18, 13, 'Chasing Sunsets', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(41, 10, 'asdadad', '2026-02-15 05:46:53', '2026-02-15 05:46:53'),
(42, 33, 'hiking', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(43, 34, 'asdad', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `personal_information`
--

CREATE TABLE `personal_information` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `cv_title` varchar(255) DEFAULT 'My Resume',
  `photo` varchar(255) DEFAULT NULL,
  `given_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) NOT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `birthdate` date NOT NULL,
  `birthplace` varchar(255) NOT NULL,
  `civil_status` enum('single','married','divorced','widowed') NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_information`
--

INSERT INTO `personal_information` (`id`, `user_id`, `cv_title`, `photo`, `given_name`, `middle_name`, `surname`, `extension`, `gender`, `birthdate`, `birthplace`, `civil_status`, `email`, `phone`, `address`, `website`, `created_at`, `updated_at`) VALUES
(10, 'armelcruz831@gmail.com', 'My Resume', 'photo_10_1771134413.jpg', 'Joshua ', 'soriano', 'Rosario', 'Jr.', 'male', '2005-12-07', 'Manila', 'single', 'armelcruz831@gmail.com', '09318388423', '0183 BAAY EAST LINGAYEN PANGASINAN ', 'https://github.com/Swxounih/IPT-CV-Group-Project/commits/main/', '2026-02-13 08:26:28', '2026-02-15 05:46:53'),
(11, 'iansalon@salon.com', 'My Resume', '', 'Ian', '', 'Salon', 'Jr', 'male', '2005-07-16', 'Lingayen', 'single', 'iansalon@salon.com', '1226522155', '', NULL, '2026-02-13 12:47:29', '2026-02-14 07:08:39'),
(12, 'yasmine@salon.com', 'My Resume', '', 'Yasmien', 'D', 'Deguzman', '', 'female', '2005-11-11', 'Manila Zoo', 'single', 'yasmine@salon.com', '09123456789', '', NULL, '2026-02-14 02:34:15', '2026-02-14 07:08:39'),
(13, 'jasmine@salon.php', 'My Resume', '', 'Jasmine', 'De Leon', 'Santos', '', 'female', '2005-11-01', 'Manila', 'single', 'jasmine@salon.php', '09123456789', 'Lingayen, Pangasinan', 'https://github.com/Swxounih/IPT-CV-Group-Project/', '2026-02-14 05:04:36', '2026-02-15 03:28:08'),
(17, 'kenji20212022@gmail.com', 'My Resume', '', 'kenjii', 'f', 'cel', '', 'male', '2005-04-03', 'jnd', 'single', 'kenji20212022@gmail.com', '091234567', 'hsbsd', 'http://localhost/codec/IPT-CV-Group-Project/personal-information.php', '2026-02-14 12:35:57', '2026-02-14 12:35:57'),
(18, 'armelcruz831@gmail.com', 'Additional Resume', '', 'kenjii', 'd', 'cel', '', 'male', '2005-12-07', 'jnd', 'married', 'armelcruz831@gmail.com', '90', '09', 'http://localhost/codec/IPT-CV-Group-Project/personal-information.php', '2026-02-14 12:45:38', '2026-02-15 03:12:39'),
(20, 'armellinium@gmail.com', 'My Resume', 'uploads/photos/photo_699082fd562ca3.22101918.webp', 'Kurt', 'Y', 'Palavino', '', 'male', '2005-02-02', 'Manila', 'single', 'armellinium@gmail.com', '09318388422', 'asdasda', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-14 14:14:19', '2026-02-14 14:14:19'),
(21, 'armelcruz831@gmail.com', 'My Resume', '', 'adasd', 'asdasd', 'asdad', '', 'male', '2005-03-12', '123fasdas', 'single', 'armelcruz831@gmail.com', '12313', 'asdasd', 'https://github.com/Swxounih/IPT-CV-Group-Project/', '2026-02-14 17:10:32', '2026-02-14 17:10:32'),
(26, 'armie@gmail.com', 'My Resume', '', 'Armie', 'Y', 'Cruz', '', 'male', '2007-03-12', 'Manila', 'single', 'armie@gmail.com', '09123456789', 'Baay east\r\nBaay', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(27, 'armie@gmail.com', 'My Resume', '', 'Armie', 'Y', 'Cruz', '', 'male', '2007-03-12', 'Manila', 'single', 'armie@gmail.com', '09123456789', 'Baay east\r\nBaay', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(28, 'armie@gmail.com', 'My Resume', '', 'Armie', 'Y', 'Cruz', '', 'male', '2007-03-12', 'Manila', 'single', 'armie@gmail.com', '09123456789', 'Baay east\r\nBaay', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(29, 'armelcruz831@gmail.com', 'My Resume', 'uploads/photos/photo_6990dfe30df1c7.54445316.jpg', 'asd', 'asdada', 'dddad', 'asd', 'male', '1231-12-12', '1231', 'single', 'armelcruz831@gmail.com', '09318388423', 'assdad', 'https://github.com/Swxounih/IPT-CV-Group-Project/commits/main/', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(30, 'armellinium@gmail.com', 'My Resume', '', 'Ralph', 'DS', 'Sadrian', '', 'male', '2005-01-01', 'Manila', 'single', 'armellinium@gmail.com', '09318388422', 'asd', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-14 20:57:19', '2026-02-14 20:57:19'),
(31, 'armellinium@gmail.com', 'My Resume', 'uploads/photos/photo_6990e2557281f0.39197781.jpg', 'Armel Meinard', 'M', 'Cruz', '', 'male', '0005-12-07', 'Manila', 'single', 'armellinium@gmail.com', '09318388422', '12123123', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(32, 'andrie@test.com', 'My Resume', 'uploads/photos/photo_69912e0d737e51.65024486.jpg', 'Andrie', 'Yucot', 'Cruz', '', 'male', '2005-01-01', 'Manila', 'single', 'andrie@test.com', '0912345678', 'Baay', 'http://localhost/sticky-problem/personal-information.php', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(33, 'armeltest@test.com', 'My Resume', '', 'Armel', 'Yucot', 'Cruz', '', 'male', '2005-12-07', 'Manila. Philippines', 'single', 'armeltest@test.com', '09123654789', 'Baay east', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(34, 'armie@gmail.com', 'My Resume', '', 'asd', 'asd', 'asd', '', 'male', '0003-02-12', 'Manila', 'single', 'armie@gmail.com', '09104134568', 'Baay east', 'http://localhost/IPT-CV-Group-Project/personal-information.php', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `reference`
--

CREATE TABLE `reference` (
  `id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reference`
--

INSERT INTO `reference` (`id`, `personal_info_id`, `company_name`, `contact_person`, `phone_number`, `email`, `created_at`, `updated_at`) VALUES
(2, 11, 'diyan lang sa tabi tabi', 'Armel Cruz', '12345678998', 'armelcruz831@gmail.com', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(3, 12, 'diyan lang sa tabi tabi', 'Armel Cruz', '12345678998', 'armelcruz831@gmail.com', '2026-02-14 02:34:15', '2026-02-14 02:34:15'),
(7, 20, '2131asdad', 'Armel Cruz', 'qwe', 'armellinium@gmail.com', '2026-02-14 14:14:19', '2026-02-14 14:14:19'),
(8, 26, 'asdad', 'ar', '09104134568', 'armie@gmail.com', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(9, 27, 'asdad', 'ar', '09104134568', 'armie@gmail.com', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(10, 28, 'asdad', 'ar', '09104134568', 'armie@gmail.com', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(11, 29, 'diyan lang sa tabi tabi', 'Armel Cruz', '12345678998', 'armelcruz831@gmail.com', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(12, 30, '2131asdad', 'Armel Cruz', '12312341', 'armellinium@gmail.com', '2026-02-14 20:57:19', '2026-02-14 20:57:19'),
(13, 31, '2131asdad', 'Armel Cruz', '1231', 'armellinium@gmail.com', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(14, 32, 'asdas', 'Armel Cruz', 'fasfa', 'armelcruz831@gmail.com', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(15, 32, 'diyan lang sa tabi tabi', 'Armel Cruz', '12345678998', 'armelcruz831@gmail.com', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(18, 13, 'diyan lang sa tabi tabi', 'Armel Cruz', '12345678998', 'armelcruz831@gmail.com', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(41, 10, 'ako lang', 'ako lang din', '123456789', 'armelcruz831@gmail.com', '2026-02-15 05:46:53', '2026-02-15 05:46:53'),
(42, 33, 'asdad', 'ar', '123325', 'armie@gmail.com', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(43, 34, 'asd', 'ar', 'asd', 'sda@dad', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `level` enum('Expert','Experienced','Skillful','Intermediate','Beginner') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `personal_info_id`, `skill_name`, `level`, `created_at`, `updated_at`) VALUES
(2, 11, 'programmer', 'Expert', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(3, 11, 'Artist', 'Expert', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(4, 11, 'Dancer', 'Experienced', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(5, 12, 'programmer', 'Expert', '2026-02-14 02:34:15', '2026-02-14 02:34:15'),
(13, 20, 'Programeer', 'Expert', '2026-02-14 14:14:19', '2026-02-14 14:14:19'),
(14, 26, 'programming', 'Expert', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(15, 27, 'programming', 'Expert', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(16, 28, 'programming', 'Expert', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(17, 29, 'Artist', 'Expert', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(18, 31, '12', 'Expert', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(19, 31, '121', 'Expert', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(20, 31, '1212', 'Expert', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(21, 32, 'programmer', 'Expert', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(22, 32, 'programmer', 'Expert', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(27, 13, 'programmer', 'Expert', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(28, 13, 'Dancer', 'Experienced', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(29, 13, 'programmer II', 'Expert', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(52, 10, 'programmer', 'Expert', '2026-02-15 05:46:53', '2026-02-15 05:46:53'),
(53, 33, 'programming', 'Expert', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(54, 34, 'programming', 'Expert', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `work_experience`
--

CREATE TABLE `work_experience` (
  `id` int(11) NOT NULL,
  `personal_info_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `employer` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_experience`
--

INSERT INTO `work_experience` (`id`, `personal_info_id`, `job_title`, `employer`, `city`, `start_date`, `end_date`, `description`, `created_at`, `updated_at`) VALUES
(2, 11, 'Programmer', 'Ako lang', 'Lingayen', '2009-02-20', '2013-02-02', 'analyst', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(3, 11, 'Multimedia Manager', 'ako lang', 'Lingayen', '2013-01-20', '2015-02-20', 'Mahilig lang mag edit', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(4, 11, 'Programmer', 'Ako lang', 'Lingayen', '1011-01-01', '1122-01-01', 'wasda', '2026-02-13 12:47:29', '2026-02-13 12:47:29'),
(5, 12, 'Programmer', 'Ako lang', 'Lingayen', '2009-02-02', '2010-02-02', 'isang taon lang tamad ako e', '2026-02-14 02:34:15', '2026-02-14 02:34:15'),
(9, 20, 'Progammer', 'ako langasda ', 'lingayen', '0012-12-12', '2112-02-21', 'asda', '2026-02-14 14:14:19', '2026-02-14 14:14:19'),
(10, 26, 'progarmer', '123', 'loingayen', '2001-12-12', '2001-12-20', 'asd', '2026-02-14 20:45:16', '2026-02-14 20:45:16'),
(11, 27, 'progarmer', '123', 'loingayen', '2001-12-12', '2001-12-20', 'asd', '2026-02-14 20:46:37', '2026-02-14 20:46:37'),
(12, 28, 'progarmer', '123', 'loingayen', '2001-12-12', '2001-12-20', 'asd', '2026-02-14 20:47:03', '2026-02-14 20:47:03'),
(13, 29, 'Programmer', 'Ako lang', 'Lingayen', '2001-03-12', '2002-02-12', 'dasda', '2026-02-14 20:49:47', '2026-02-14 20:49:47'),
(14, 30, 'Progammer', 'ako langasda ', 'lingayen', '2000-01-01', '2000-01-01', '', '2026-02-14 20:57:19', '2026-02-14 20:57:19'),
(15, 31, 'Progammer', '1231', 'lingayen', '2222-12-12', '2222-12-12', '', '2026-02-14 21:01:01', '2026-02-14 21:01:01'),
(16, 32, 'Programmer', 'Ako lang', 'Lingayen', '0012-12-12', '0012-12-12', 'csdasd', '2026-02-15 02:26:27', '2026-02-15 02:26:27'),
(19, 13, 'Programmer', 'Ako lang', 'Lingayen', '2019-12-14', '2020-02-20', 'ddfasdada', '2026-02-15 03:28:08', '2026-02-15 03:28:08'),
(42, 10, 'Programmer', 'Ako lang', 'Lingayen', '2005-12-12', '2006-12-12', 'asdasd', '2026-02-15 05:46:53', '2026-02-15 05:46:53'),
(43, 33, 'progarmer', '123', 'loingayen', '2008-02-01', '2009-02-02', '', '2026-02-15 05:51:37', '2026-02-15 05:51:37'),
(44, 34, 'sad', 'as', 'ad', '0123-03-12', '0214-03-12', 'asd', '2026-02-15 05:56:32', '2026-02-15 05:56:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `career_objectives`
--
ALTER TABLE `career_objectives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_education_person` (`personal_info_id`);

--
-- Indexes for table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_info_id` (`personal_info_id`);

--
-- Indexes for table `personal_information`
--
ALTER TABLE `personal_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_personal_name` (`given_name`,`middle_name`,`surname`),
  ADD KEY `idx_personal_email` (`email`),
  ADD KEY `idx_personal_phone` (`phone`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `reference`
--
ALTER TABLE `reference`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reference_person` (`personal_info_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_skills_person` (`personal_info_id`),
  ADD KEY `idx_skills_name` (`skill_name`);

--
-- Indexes for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_work_experience_person` (`personal_info_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `career_objectives`
--
ALTER TABLE `career_objectives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `interests`
--
ALTER TABLE `interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `personal_information`
--
ALTER TABLE `personal_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `reference`
--
ALTER TABLE `reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `work_experience`
--
ALTER TABLE `work_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `career_objectives`
--
ALTER TABLE `career_objectives`
  ADD CONSTRAINT `career_objectives_ibfk_1` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_information` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_information` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interests`
--
ALTER TABLE `interests`
  ADD CONSTRAINT `interests_ibfk_1` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_information` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reference`
--
ALTER TABLE `reference`
  ADD CONSTRAINT `reference_ibfk_1` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_information` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_information` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD CONSTRAINT `work_experience_ibfk_1` FOREIGN KEY (`personal_info_id`) REFERENCES `personal_information` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
