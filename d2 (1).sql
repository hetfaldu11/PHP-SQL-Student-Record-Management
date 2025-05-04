-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 02:33 PM
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
-- Database: `d2`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `description`) VALUES
(1, 'Computer Science', 'Learn CS fundamentals'),
(2, 'Electrical Engineering', 'Circuits and systems'),
(3, 'Mechanical Engineering', 'Machines and mechanics'),
(4, 'Chemical Engineering', 'Related to environmental chemistry'),
(5, 'Instrumental Control', 'About handling various Technical instruments'),
(6, 'Instrumental Control', 'About handling various Technical instruments'),
(8, 'Chemical Engineering', 'gytryth');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `reg_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studentfees`
--

CREATE TABLE `studentfees` (
  `student_id` int(11) NOT NULL,
  `fees_status` enum('paid','pending') NOT NULL DEFAULT 'pending',
  `last_email_sent` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentfees`
--

INSERT INTO `studentfees` (`student_id`, `fees_status`, `last_email_sent`, `last_updated`) VALUES
(1, 'pending', '2025-03-10 10:30:00', '2025-04-19 17:43:50'),
(2, 'paid', '2025-02-20 12:15:00', '2025-04-19 15:47:16'),
(3, 'pending', '2025-04-19 15:48:05', '2025-04-19 15:48:05'),
(4, 'pending', '2025-03-25 16:45:00', '2025-04-19 15:47:16'),
(5, 'paid', '2025-01-15 14:20:00', '2025-04-19 15:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `phone`, `course_id`, `created_at`) VALUES
(1, 'Aryan Sharma', 'aryan.sharma@example.com', '1234567890', 5, '2025-04-19 09:39:26'),
(2, 'Neha Verma', 'neha.verma@example.com', '789685345', 1, '2025-04-19 09:39:26'),
(3, 'Ravi Kumar', 'khandrarudra98@gmail.com', '789685345', 1, '2025-04-19 09:39:26'),
(4, 'Priya Singh', 'priya.singh@example.com', '07069479377', 3, '2025-04-19 09:39:26'),
(5, 'Karan Mehta', 'karan.mehta@example.com', '656613161', 8, '2025-04-19 09:39:26'),
(6, 'Krish', 'dynamoking72@gmail.com', '065661454545', 2, '2025-04-19 10:41:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`reg_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `studentfees`
--
ALTER TABLE `studentfees`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `studentfees`
--
ALTER TABLE `studentfees`
  ADD CONSTRAINT `studentfees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
