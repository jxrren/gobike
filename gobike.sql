-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2024 at 09:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gobike`
--

-- --------------------------------------------------------

--
-- Table structure for table `bikes`
--

CREATE TABLE `bikes` (
  `bike_id` int(11) NOT NULL,
  `renting_location` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cost_per_hour` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bikes`
--

INSERT INTO `bikes` (`bike_id`, `renting_location`, `description`, `cost_per_hour`) VALUES
(1, 'Marina Bay', 'This is the bike at Marina Bay.', 5.00),
(2, 'Jurong East', 'This is the bike at Jurong East.', 2.00),
(3, 'Woodlands', 'This is the bike at Woodlands.', 4.00),
(4, 'Tampines', 'This is the bike at Tampines.', 3.00),
(5, 'Bedok', 'This is the bike at Bedok.', 1.00),
(6, 'Bukit Panjang', 'This is the bike at Bukit Panjang.', 8.00),
(7, 'Yishun', 'This is the bike at Yishun.', 11.00),
(8, ' Orchard Boulevard', 'This is the bike at Orchard Boulevard.', 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bike_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `user_id`, `bike_id`, `start_time`, `end_time`, `total_cost`) VALUES
(13, 11, 3, '2024-08-18 21:32:39', '2024-08-19 01:41:22', 20.00),
(14, 11, 1, '2024-08-18 21:39:13', '2024-08-18 22:23:28', 5.00),
(18, 11, 2, '2024-08-18 23:57:51', '2024-08-18 23:58:07', 2.00),
(19, 11, 3, '2024-08-19 00:09:13', '2024-08-19 00:09:17', 4.00),
(20, 11, 1, '2024-08-19 13:30:21', '2024-08-19 13:30:38', 5.00),
(21, 11, 1, '2024-08-19 13:34:06', '2024-08-19 13:34:21', 5.00),
(22, 11, 4, '2024-08-19 13:34:14', '2024-08-19 13:34:27', 3.00),
(23, 11, 1, '2024-08-19 13:36:11', '2024-08-19 13:36:37', 5.00),
(24, 11, 5, '2024-08-19 13:36:16', '2024-08-19 13:36:39', 1.00),
(29, 11, 1, '2024-08-19 20:32:27', '2024-08-19 13:04:36', 2.68),
(30, 11, 1, '2024-08-19 21:04:56', '2024-08-19 13:05:05', 0.01),
(31, 11, 1, '2024-08-19 21:08:50', '2024-08-19 13:09:02', 5.00),
(32, 11, 1, '2024-08-19 21:09:16', '2024-08-19 13:09:20', 0.01),
(33, 11, 1, '2024-08-19 21:10:18', '2024-08-19 13:10:22', 5.00),
(34, 11, 5, '2024-08-19 21:11:56', '2024-08-19 13:12:00', 0.00),
(35, 11, 1, '2024-08-19 21:20:09', '2024-08-19 13:20:14', 0.01),
(36, 11, 1, '2024-08-19 21:23:39', '2024-08-19 13:23:43', 5.00),
(37, 11, 5, '2024-08-19 21:23:48', '2024-08-19 13:24:01', 1.00),
(38, 11, 1, '2024-08-19 21:41:56', '2024-08-19 13:42:10', 5.00),
(39, 11, 1, '2024-08-19 21:52:44', '2024-08-19 13:52:52', 5.00),
(40, 11, 1, '2024-08-19 22:54:37', '2024-08-19 14:54:46', 5.00),
(41, 11, 1, '2024-08-19 23:04:28', '2024-08-19 15:04:44', 5.00),
(42, 11, 1, '2024-08-19 23:21:43', '2024-08-19 17:22:16', 0.00),
(43, 11, 1, '2024-08-19 17:22:28', '2024-08-19 17:22:38', 5.00),
(44, 11, 5, '2024-08-19 23:22:56', '2024-08-19 17:23:33', 0.00),
(45, 11, 1, '2024-08-20 02:03:55', '2024-08-19 21:42:32', 0.00),
(46, 13, 2, '2024-08-20 03:16:32', '2024-08-19 19:16:39', 2.00),
(47, 13, 2, '2024-08-20 03:20:19', '2024-08-20 08:49:51', 28.00),
(48, 11, 1, '2024-08-19 21:42:24', '2024-08-19 21:42:32', 0.00),
(49, 13, 3, '2024-08-19 21:42:45', '2024-08-19 21:42:50', 4.00),
(50, 13, 5, '2024-08-19 21:42:56', '2024-08-19 21:43:00', 1.00),
(51, 11, 1, '2024-08-20 09:40:22', '2024-08-20 09:40:34', 5.00),
(52, 11, 1, '2024-08-20 10:39:21', '2024-08-20 10:40:59', 5.00),
(53, 11, 1, '2024-08-20 10:40:34', '2024-08-20 10:40:59', 5.00),
(54, 11, 1, '2024-08-20 10:40:51', '2024-08-20 10:40:59', 5.00),
(55, 11, 1, '2024-08-20 16:49:23', '2024-08-20 08:49:31', 5.00),
(56, 11, 5, '2024-08-20 20:06:55', '2024-08-20 12:07:00', 1.00),
(57, 11, 5, '2024-08-20 20:07:07', '2024-08-20 12:07:14', 1.00),
(58, 11, 4, '2024-08-20 20:07:18', '2024-08-20 14:07:53', 0.00),
(59, 11, 5, '2024-08-20 14:08:07', '2024-08-20 14:08:11', 1.00),
(60, 11, 1, '2024-08-20 21:16:42', '2024-08-20 13:37:30', -38.27),
(61, 11, 6, '2024-08-20 21:41:09', '2024-08-20 13:41:27', -63.96),
(62, 11, 2, '2024-08-20 21:43:10', '2024-08-20 13:43:31', -15.99),
(63, 11, 5, '2024-08-20 21:44:54', '2024-08-20 13:44:59', -8.00),
(64, 11, 5, '2024-08-20 21:45:23', '2024-08-20 13:45:27', -8.00),
(65, 11, 5, '2024-08-20 21:48:12', '2024-08-20 13:48:16', 8.00),
(66, 11, 5, '2024-08-20 21:54:53', '2024-08-20 13:54:57', 8.00),
(67, 11, 5, '2024-08-20 21:56:24', '2024-08-20 13:56:28', 8.00),
(68, 11, 6, '2024-08-20 21:56:33', '2024-08-20 13:56:39', 64.00),
(69, 11, 5, '2024-08-20 21:58:24', '2024-08-20 13:58:27', 8.00),
(70, 11, 6, '2024-08-20 21:58:31', '2024-08-20 13:58:35', 64.00),
(71, 11, 5, '2024-08-20 22:00:05', '2024-08-20 14:01:00', 8.00),
(72, 11, 1, '2024-08-20 22:01:09', '2024-08-20 14:01:12', 40.00),
(73, 11, 5, '2024-08-20 22:02:31', '2024-08-20 14:07:29', 8.00),
(74, 11, 5, '2024-08-20 22:08:09', '2024-08-20 14:08:29', 8.00),
(75, 11, 6, '2024-08-20 22:08:51', '2024-08-20 14:08:55', 64.00),
(76, 11, 5, '2024-08-20 22:12:02', '2024-08-20 14:12:10', 8.00),
(77, 11, 5, '2024-08-20 22:19:14', '2024-08-20 14:19:18', 8.00),
(78, 11, 5, '2024-08-20 22:20:29', '2024-08-20 14:20:32', 8.00),
(79, 11, 5, '2024-08-20 22:20:59', '2024-08-20 14:21:11', 8.00),
(80, 11, 5, '2024-08-20 22:23:13', '2024-08-20 14:23:17', 8.00),
(81, 11, 2, '2024-08-20 22:23:45', '2024-08-20 14:23:48', 16.00),
(82, 11, 5, '2024-08-20 22:28:15', '2024-08-20 14:28:26', 8.00),
(83, 11, 5, '2024-08-20 22:38:52', '2024-08-20 14:38:54', 8.00),
(84, 11, 5, '2024-08-20 22:40:16', '2024-08-20 14:45:45', 8.00),
(85, 11, 5, '2024-08-20 22:49:10', '2024-08-20 16:49:13', 6.00),
(86, 11, 5, '2024-08-20 22:49:18', '2024-08-20 16:49:50', 6.00),
(87, 11, 5, '2024-08-20 22:54:08', '2024-08-20 16:54:11', 6.00),
(88, 11, 5, '2024-08-20 22:55:35', '2024-08-20 16:55:46', 6.00),
(89, 11, 5, '2024-08-20 23:01:36', '2024-08-20 17:01:39', 6.00),
(90, 11, 5, '2024-08-20 23:05:14', '2024-08-20 17:05:17', 6.00),
(91, 11, 5, '2024-08-20 23:06:50', '2024-08-20 23:06:53', 1.00),
(92, 11, 5, '2024-08-20 23:07:01', '2024-08-20 23:07:08', 1.00),
(93, 11, 1, '2024-08-20 23:11:13', '2024-08-20 23:11:27', 5.00),
(94, 11, 5, '2024-08-20 23:13:22', '2024-08-20 23:13:25', 1.00),
(95, 11, 5, '2024-08-20 23:22:39', '2024-08-20 23:23:01', 1.00),
(96, 11, 1, '2024-08-20 17:48:55', '2024-08-20 17:49:41', 5.00),
(97, 11, 5, '2024-08-20 18:13:01', '2024-08-20 18:21:00', 1.00),
(99, 11, 4, '2024-08-20 18:14:44', '2024-08-20 18:15:00', 3.00),
(100, 11, 1, '2024-08-20 18:21:15', '2024-08-20 18:21:27', 5.00),
(101, 13, 5, '2024-08-20 18:21:32', '2024-08-20 18:21:42', 1.00),
(102, 11, 1, '2024-08-20 18:22:27', '2024-08-20 18:22:30', 5.00),
(103, 13, 6, '2024-08-20 18:22:39', '2024-08-20 18:22:49', 8.00),
(104, 13, 3, '2024-08-21 00:25:08', '2024-08-20 18:25:36', 0.00),
(105, 11, 1, '2024-08-21 00:55:33', '2024-08-21 00:57:36', 5.00),
(106, 11, 1, '2024-08-21 00:57:26', '2024-08-21 00:57:33', 5.00),
(107, 11, 1, '2024-08-21 00:59:07', '2024-08-21 01:03:39', 5.00),
(108, 13, 2, '2024-08-21 01:00:24', '2024-08-20 20:30:20', 0.00),
(109, 11, 1, '2024-08-20 20:28:08', '2024-08-20 20:28:19', 5.00),
(110, 11, 8, '2024-08-20 20:29:38', '2024-08-20 20:30:11', 15.00),
(111, 13, 8, '2024-08-20 20:30:33', '2024-08-20 20:32:33', 15.00),
(112, 11, 8, '2024-08-20 20:32:38', '2024-08-20 20:32:51', 15.00),
(113, 11, 8, '2024-08-20 20:33:40', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `surname` varchar(25) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` enum('Admin','User') NOT NULL,
  `password` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `phone`, `email`, `type`, `password`) VALUES
(11, 'jarren', 'png', '97264310', 'jarren@gmail.com', 'User', '123'),
(12, 'admin', 'png', '97264310', 'admin@gmail.com', 'Admin', '123'),
(13, 'john', 'png', '90999495', 'johnpng@gmail.com', 'User', '123'),
(14, 'Lily', 'Png', '90982737', 'lily@gmail.com', 'User', '123'),
(15, 'admin2', 'png', '87269172', 'admin2@gmail.com', 'Admin', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bikes`
--
ALTER TABLE `bikes`
  ADD PRIMARY KEY (`bike_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `bike_id` (`bike_id`),
  ADD KEY `rentals_ibfk_2` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bikes`
--
ALTER TABLE `bikes`
  MODIFY `bike_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`bike_id`) REFERENCES `bikes` (`bike_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
