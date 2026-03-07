-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 07:47 AM
-- Server version: 8.0.45
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `organization`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int NOT NULL,
  `emp_id` int NOT NULL,
  `org_id` int NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late') DEFAULT 'present',
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `emp_id`, `org_id`, `date`, `status`, `time_in`, `time_out`, `created_at`) VALUES
(1, 1, 1, '2026-03-07', 'absent', '12:11:03', NULL, '2026-03-07 06:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int NOT NULL,
  `org_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `org_id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 1, 'sakib shaikh', 'sakib2@gmail.com', 'for testing', 'checking is working', '2026-03-07 05:27:37'),
(2, 2, 'sakib shaikh', 'sakib11@gmail.com', 'testing', 'another org added', '2026-03-07 06:08:47');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` int NOT NULL,
  `org_id` int NOT NULL,
  `empname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `category` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `org_id`, `empname`, `email`, `password`, `dob`, `mobile`, `category`, `created_at`) VALUES
(1, 1, 'sakib shaikh', 'sakib1@gmail.com', '$2y$10$jZw9U/d.N7F8eLcsTQGMu.Ca81d676l52D4ZHfzo8Z.IxeiTdy62C', '2006-01-17', '9921989670', 'Highly Skilled', '2026-03-07 05:25:34'),
(2, 2, 'sakib turuk', 'sakib123@gamil.com', '$2y$10$t0d2rkPtPeHn7lZu64cnuezTSsdtoeqxscnE1aqxEhxlFJraSB2Ku', '2006-01-10', '9130873727', 'Skilled', '2026-03-07 06:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `employees_master`
--

CREATE TABLE `employees_master` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees_master`
--

INSERT INTO `employees_master` (`id`, `email`, `mobile`, `created_at`) VALUES
(1, 'sakib1@gmail.com', '9921989670', '2026-03-07 05:25:34'),
(2, 'sakib123@gamil.com', '9130873727', '2026-03-07 06:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `organization_register`
--

CREATE TABLE `organization_register` (
  `org_id` int NOT NULL,
  `org_name` varchar(100) NOT NULL,
  `org_email` varchar(100) NOT NULL,
  `org_password` varchar(255) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `organization_register`
--

INSERT INTO `organization_register` (`org_id`, `org_name`, `org_email`, `org_password`, `mobile_number`, `created_at`) VALUES
(1, 'KGN ', 'kgnenterprises9670@gmail.com', '$2y$10$ZgIM.ReMFCz9LfabVvAVe.Qmvvs4Qsvehsh3GHjGgMP7FkOgeOspe', '9921989670', '2026-03-07 05:16:22'),
(2, 'sainath ', 'sainathenterprises1618@gmail.com', '$2y$10$68DaLqTLZA1W5r669p5/eufw.HJpJeYau0vh/Eyq756472Z8dDGmK', '9130873727', '2026-03-07 06:07:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`emp_id`,`date`),
  ADD KEY `org_id` (`org_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD KEY `org_id` (`org_id`);

--
-- Indexes for table `employees_master`
--
ALTER TABLE `employees_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`);

--
-- Indexes for table `organization_register`
--
ALTER TABLE `organization_register`
  ADD PRIMARY KEY (`org_id`),
  ADD UNIQUE KEY `org_email` (`org_email`),
  ADD UNIQUE KEY `mobile_number` (`mobile_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `emp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees_master`
--
ALTER TABLE `employees_master`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `organization_register`
--
ALTER TABLE `organization_register`
  MODIFY `org_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`org_id`) REFERENCES `organization_register` (`org_id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organization_register` (`org_id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organization_register` (`org_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
