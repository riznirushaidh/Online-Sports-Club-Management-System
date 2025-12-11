-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 12:03 PM
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
-- Database: `sports_club_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `available_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `item_name`, `total_quantity`, `available_quantity`) VALUES
(1, 'Foot Ball', 10, 5),
(2, 'Cricket Ball', 20, 18),
(3, 'Helmet', 200, 179),
(4, 'Badminton Net', 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `equipmentborrowing`
--

CREATE TABLE `equipmentborrowing` (
  `borrow_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `borrow_date` datetime NOT NULL,
  `return_deadline` datetime NOT NULL,
  `actual_return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipmentborrowing`
--

INSERT INTO `equipmentborrowing` (`borrow_id`, `member_id`, `equipment_id`, `borrow_date`, `return_deadline`, `actual_return_date`) VALUES
(1, 2, 4, '2025-11-12 15:10:22', '2025-11-19 15:10:22', NULL),
(2, 2, 2, '2025-11-12 15:10:24', '2025-11-19 15:10:24', NULL),
(3, 2, 1, '2025-11-12 15:10:29', '2025-11-19 15:10:29', NULL),
(4, 2, 4, '2025-11-12 15:11:21', '2025-11-19 15:11:21', '2025-11-12 19:41:32'),
(5, 2, 4, '2025-11-12 15:11:28', '2025-11-19 15:11:28', '2025-11-12 19:41:35'),
(6, 10, 4, '2025-11-12 15:44:04', '2025-11-19 15:44:04', '2025-11-12 20:14:13'),
(7, 10, 2, '2025-11-12 15:44:05', '2025-11-19 15:44:05', NULL),
(8, 10, 1, '2025-11-12 15:44:07', '2025-11-19 15:44:07', NULL),
(9, 10, 3, '2025-11-12 15:44:09', '2025-11-19 15:44:09', NULL),
(10, 10, 4, '2025-11-12 15:44:11', '2025-11-19 15:44:11', NULL),
(11, 10, 3, '2025-11-12 15:44:15', '2025-11-19 15:44:15', '2025-11-12 20:14:17'),
(12, 11, 4, '2025-11-12 15:46:46', '2025-11-19 15:46:46', NULL),
(13, 11, 1, '2025-11-12 15:46:48', '2025-11-19 15:46:48', NULL),
(14, 11, 4, '2025-11-12 15:46:50', '2025-11-19 15:46:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eventregistrations`
--

CREATE TABLE `eventregistrations` (
  `registration_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventregistrations`
--

INSERT INTO `eventregistrations` (`registration_id`, `member_id`, `event_id`, `registration_date`) VALUES
(1, 2, 1, '2025-11-12 13:14:31'),
(2, 2, 6, '2025-11-12 14:09:59'),
(3, 10, 1, '2025-11-12 14:43:54'),
(4, 10, 6, '2025-11-12 14:43:54'),
(5, 10, 3, '2025-11-12 14:43:55'),
(6, 10, 4, '2025-11-12 14:43:56'),
(7, 11, 1, '2025-11-12 14:46:41'),
(8, 11, 6, '2025-11-12 14:46:42'),
(9, 11, 3, '2025-11-12 14:46:43');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `description`, `event_date`, `location`) VALUES
(1, 'Badminton Practice', 'Please register earlier!!', '2025-11-13 07:42:00', 'OUSL Main Play Ground'),
(3, 'General Meeting', '', '2025-11-18 19:30:00', 'OUSL CRC Room 12'),
(4, 'Badminton Final Match', '', '2025-12-28 10:30:00', 'OUSL Main Play Ground'),
(5, 'Cricket', '', '2025-11-07 07:33:00', 'OUSL Main Play Ground'),
(6, 'Foot Ball', '', '2025-11-16 12:00:00', 'OUSL Main Play Ground');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `username`, `email`, `password_hash`, `full_name`, `role`, `registration_date`) VALUES
(2, 'rizni_rushaidh', 'riznirushaidhforapps@gmail.com', '$2y$10$RI1X1VZoEEhA8lCooIkZk.hr9q42wBGP1i3ZQsvbZwg.lFiemdlBa', 'Rameem Rizni Rushaidh', 'member', '2025-11-12 12:16:31'),
(9, 'admin', 'admin@club.com', '$2y$10$1dA3tfyqXhfkpSllvxV4jet0cuNWs94WT9kURvgmSo0V5uZ7QfDeK', 'Admin User', 'admin', '2025-11-12 13:10:24'),
(10, 'rifthi', 'rifthi@gmail.com', '$2y$10$rsIAALamBNiwfsdv2PCZWubKw2QTBS9iopnlilbQiVABHD8yCMOnG', 'Rifthi Rushaidh', 'member', '2025-11-12 14:43:37'),
(11, 'rizla', 'rizla@gmail.com', '$2y$10$EMuta.N8qmXPS82QqG0jQ.1eNcePMlCsadh5i4qhv3lodji9a13mi', 'Fathima Rizla', 'member', '2025-11-12 14:46:18');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_content`, `timestamp`, `is_read`) VALUES
(3, 2, 9, 'Hello Admin!\r\n', '2025-11-12 13:48:50', 0),
(4, 2, 9, 'Hello Rizni! How are you?\r\n', '2025-11-12 13:49:12', 0),
(5, 2, 9, 'Are you ready to the next meeting on 15.11.2025 at 9 a.m?', '2025-11-12 13:49:58', 0),
(6, 2, 9, 'Yes Sir! I am ready! Let\'s meet at OUSL CRC Room 12!', '2025-11-12 13:51:02', 0),
(7, 9, 2, 'Okay!', '2025-11-12 14:08:18', 0),
(8, 9, 2, 'I will call you today night!', '2025-11-12 14:09:03', 0),
(9, 2, 9, 'Okay! I am waiting! Let\'s talk!!\r\n', '2025-11-12 14:11:03', 0),
(10, 10, 9, 'Dear Sir, can I get an appointment to meet you?\r\n', '2025-11-12 14:45:12', 0),
(11, 10, 9, 'I have to discuss about the coming general meeting!\r\n', '2025-11-12 14:45:39', 0),
(12, 11, 9, 'Dear Sir! We have to change the date of the general meeting because of the weather condition!\r\n', '2025-11-12 14:48:10', 0),
(13, 9, 10, 'Okay! I will give a date as soon as possible!', '2025-11-12 14:50:17', 0),
(14, 9, 11, 'Yes! ', '2025-11-12 14:50:31', 0),
(15, 9, 11, 'Let\'s talk about it today evening!', '2025-11-12 14:50:58', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `equipmentborrowing`
--
ALTER TABLE `equipmentborrowing`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `eventregistrations`
--
ALTER TABLE `eventregistrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `member_id` (`member_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipmentborrowing`
--
ALTER TABLE `equipmentborrowing`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `eventregistrations`
--
ALTER TABLE `eventregistrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equipmentborrowing`
--
ALTER TABLE `equipmentborrowing`
  ADD CONSTRAINT `equipmentborrowing_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipmentborrowing_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `eventregistrations`
--
ALTER TABLE `eventregistrations`
  ADD CONSTRAINT `eventregistrations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eventregistrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
