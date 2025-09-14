-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2025 at 12:31 AM
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
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('present','absent') DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `session_id`, `student_id`, `token`, `program`, `class_id`, `course_name`, `timestamp`, `latitude`, `longitude`, `status`) VALUES
(1, 44, 10, NULL, NULL, 8, NULL, '2025-08-26 07:29:28', -6.21397530, 35.80956910, 'present'),
(18, 78, 19, '7d4e782160c92b30cd87e2d0cad68a36', 'Software Engineering', 32, 'web technology', '2025-08-30 08:02:49', -6.21732542, 35.81506943, 'present'),
(19, 92, 19, 'b3fb7981f64226650b3b7b3d0592b39c', 'Software Engineering', 32, 'web technology', '2025-08-30 21:35:41', -6.21717294, 35.81475508, 'present'),
(20, 94, 19, 'fe4efa590c85b5a8430b79254b7e0e50', 'Software Engineering', 32, 'web technology', '2025-08-30 22:23:35', -6.21706187, 35.81451529, 'present'),
(21, 94, 35, NULL, NULL, 32, 'web technology', '2025-08-30 22:26:39', NULL, NULL, 'present');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius` int(11) DEFAULT 100,
  `schedule` datetime NOT NULL,
  `course_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `teacher_id`, `latitude`, `longitude`, `radius`, `schedule`, `course_name`) VALUES
(1, 'lrb', 9, -6.21405800, 35.80964900, 100, '2025-08-21 16:13:00', ''),
(7, 'lrb', 9, -6.21405800, 35.80964900, 100, '2025-08-21 17:05:00', ''),
(8, 'lrb', 9, -6.21405800, 35.80964900, 100, '2025-08-21 17:13:00', ''),
(32, 'lrb', 20, -6.21405800, 35.80964900, 100, '2025-08-28 03:13:00', 'web technology'),
(55, 'lrb 106', 20, -6.21717300, 35.81475500, 1000, '2025-08-30 23:59:00', 'web technology'),
(56, 'lrb 106', 20, -6.21717300, 35.81475500, 100, '2025-08-31 01:22:00', 'web technology');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `session_time` time DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `qr_token` varchar(32) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `class_id`, `session_date`, `session_time`, `is_active`, `qr_token`, `expires_at`) VALUES
(40, 1, '2025-08-25', NULL, 1, 'c9db92abdbf58bf352fdc4732d332768', '2025-08-29 14:06:39'),
(44, 8, '2025-08-26', NULL, 1, 'fba65d7ee86ca3a518ac1ef8d1bc27fd', '2025-08-26 10:37:01'),
(45, 7, '2025-08-26', NULL, 1, 'da7801c2e2e227d160c35bb408c95e61', '2025-08-26 10:49:33'),
(46, 7, '2025-08-26', NULL, 1, '5538221f3474b522abedd1ed460d3b14', '2025-08-26 11:24:54'),
(47, 32, '2025-08-27', '03:13:00', 1, '828801174316b0024f1784518e355ab5', '2025-08-28 03:23:47'),
(48, 32, '2025-08-27', '03:27:00', 1, '67324590c4478b9c557a221532ddee99', '2025-08-28 03:37:28'),
(49, 32, '2025-08-27', '10:38:00', 1, '82563244ac71a5216c5a8f5a08a83872', '2025-08-28 10:48:17'),
(78, 32, '2025-08-29', '11:01:00', 1, '7d4e782160c92b30cd87e2d0cad68a36', '2025-08-30 11:31:44'),
(91, 32, '2025-08-31', '00:00:00', 1, 'a833ac7b2a2dcb5c7449e08ae803a6bf', '2025-08-31 00:30:24'),
(92, 32, '2025-08-31', '00:34:00', 1, 'b3fb7981f64226650b3b7b3d0592b39c', '2025-08-31 01:34:41'),
(93, 32, '2025-08-31', '00:58:00', 1, '996445be1fa811c619324a3146f5ce89', '2025-08-31 01:28:21'),
(94, 32, '2025-08-31', '01:22:00', 1, 'fe4efa590c85b5a8430b79254b7e0e50', '2025-08-31 01:52:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `program` varchar(100) DEFAULT NULL,
  `morning_course` varchar(100) DEFAULT NULL,
  `evening_course` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `registration_number`, `name`, `program`, `morning_course`, `evening_course`, `password`, `role`) VALUES
(1, NULL, 'Selemani Jumaa Ramadhani', NULL, NULL, NULL, '$2y$10$TLJMWmiHYvUEiPNLderqd.FtjnesvawGGNohFGg/pFhjm2qtcl23S', 'admin'),
(2, 'T24-03-24581', 'Selemani Jumaa Ramadhani', NULL, NULL, NULL, '$2y$10$vnToIZFcqzDHsFlO/4iErOe3ANLHnfe2WRAGpN9/0LKYrlkxq6izO', 'admin'),
(9, NULL, 'fidel fokas gwimbugwa', NULL, NULL, NULL, '$2y$10$C040rG4j7m/uHFlaa6Y4T.DhLR0XZFzNZSq4XSeTcYALtFWdjYXvK', 'teacher'),
(10, NULL, 'jackobo robert kasele', NULL, NULL, NULL, '$2y$10$UmGBjTl0RErJW55cdgNUb.GavGmw/WYH9SSTVnA2DMSxajL6MlVG6', 'student'),
(19, 'T24-03-22599', 'deogratius phabian', 'Software Engineering', 'Networking', 'Computer Maintenance', '$2y$10$/TsihVNC8BEESrawPGiYLuBHQ.hbg7ucOR6Q2CAadTp/GFry/BZtG', 'student'),
(20, 'T24-03-12868', 'fidel fokus', '', 'web technology', 'computer maintainance', '$2y$10$RUG0eTQe/e29RXwwfhRHFu7Tt7KmA74Cdv/D3UMexMypKUpUkbMWS', 'teacher'),
(28, 'T24-03-20052', 'Amani Samaga', 'Computer Science', 'Web Technology', 'Game Development', '$2y$10$esuXz5Y6AczRHQFWYcK.AetvMiCR/12y4tfy8jGyFXWXVCVhDK9zm', 'student'),
(29, 'T24-03-22349', 'Kulwa M James', 'Software Engineering', 'Web Technology', 'Data Science', '$2y$10$13CPHPIDS5KoRwlpGISkoeURFfMHxQQH5UmSxzFUQqWjtcPhW/19q', 'student'),
(30, 'T24-03-26011', 'Paulo Malando', 'Computer Science', 'Web Technology', 'Data Science', '$2y$10$7uwNehTDofUuS9VbdzDk7ejZi4YGRXGz37ON5xRJrXmAC0snzDX7m', 'student'),
(33, 'T24-03-14623', 'Thomas G. Sumba', 'Computer Science', 'Web Technology', 'Computer Maintenance', '$2y$10$iyp2iN/sSrDXo7V6yOkQ0ezlSqJAbru2KwdEIvmOTsFySZpBbMKCC', 'student'),
(34, 'T24-03-10376', 'Jacob Robert kasele', 'Information Technology', 'Web Technology', 'Computer Maintenance', '$2y$10$PI8BafmGyWMSfK6xlbIVqenaReHSX9tS577y8WoQAbs.NiNNEGpoa', 'student'),
(35, 'T24-02-13499', 'Ramadhan Twaha', 'Information Technology', 'Networking', 'Ethical Hacking', '$2y$10$qwUQwYyrhFM9GWN0k2Q5/.a92qrhIAzhVxAOalH6uhxFQfbWyuwkq', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `uniq_att` (`session_id`,`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `uniq_enroll` (`student_id`,`class_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `uq_qr_token` (`qr_token`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD UNIQUE KEY `registration_number_2` (`registration_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
