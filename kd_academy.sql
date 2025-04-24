-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2025 at 01:54 PM
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
-- Database: `kd_academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(10) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`) VALUES
('admin', 'HelloWorld'),
('admin@gmai', 'HelloWorld');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` enum('Present','Absent') DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `attendance_date`, `status`, `teacher_id`) VALUES
(1, 8, '2025-01-24', 'Present', 42),
(2, 20, '2025-01-24', 'Present', 42),
(3, 27, '2025-01-24', 'Present', 42),
(4, 51, '2025-01-24', 'Present', 42),
(5, 2, '2025-01-24', 'Present', 42),
(6, 5, '2025-01-24', 'Present', 42),
(7, 11, '2025-01-24', 'Present', 42),
(8, 17, '2025-01-24', 'Present', 42),
(9, 23, '2025-01-24', 'Present', 42),
(56, 1, '2025-01-24', 'Absent', 57),
(57, 4, '2025-01-24', 'Absent', 57),
(58, 7, '2025-01-24', 'Absent', 57),
(59, 13, '2025-01-24', 'Present', 57),
(60, 19, '2025-01-24', 'Absent', 57),
(61, 26, '2025-01-24', 'Present', 57),
(62, 47, '2025-01-24', 'Absent', 57),
(77, 10, '2025-01-24', 'Absent', 57),
(78, 16, '2025-01-24', 'Present', 57),
(79, 22, '2025-01-24', 'Absent', 57),
(80, 28, '2025-01-24', 'Present', 57),
(81, 30, '2025-01-24', 'Present', 57),
(89, 53, '2025-01-24', 'Present', 57);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `programme` enum('BCS','BBA','BMC') NOT NULL,
  `semester` tinyint(1) NOT NULL,
  `credits` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `programme`, `semester`, `credits`) VALUES
(1, 'BCS101', 'Introduction to Programming', 'BCS', 1, 3),
(2, 'BCS102', 'Data Structures', 'BCS', 1, 4),
(3, 'BCS201', 'Artificial Intelligence', 'BCS', 2, 4),
(4, 'BCS202', 'Machine Learning', 'BCS', 2, 4),
(5, 'BBA101', 'Principles of Management', 'BBA', 1, 3),
(6, 'BBA102', 'Business Economics', 'BBA', 1, 3),
(7, 'BBA201', 'Marketing Management', 'BBA', 2, 4),
(8, 'BBA202', 'Financial Accounting', 'BBA', 2, 4),
(9, 'BMC101', 'Media Studies', 'BMC', 1, 3),
(10, 'BMC102', 'Communication Theory', 'BMC', 1, 3),
(11, 'BMC201', 'Digital Media Production', 'BMC', 2, 4),
(12, 'BMC202', 'Journalism Ethics', 'BMC', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester` tinyint(1) NOT NULL,
  `registration_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `course_id`, `semester`, `registration_date`) VALUES
(1, 45, 5, 1, '2025-01-24 15:04:18'),
(2, 45, 6, 1, '2025-01-24 15:09:24'),
(3, 49, 5, 1, '2025-01-24 15:28:15'),
(4, 49, 6, 1, '2025-01-24 15:28:15'),
(5, 49, 7, 2, '2025-01-24 16:02:40'),
(6, 49, 8, 2, '2025-01-24 16:02:40'),
(7, 50, 5, 1, '2025-01-24 16:35:32'),
(8, 50, 7, 2, '2025-01-24 16:35:39'),
(9, 50, 8, 2, '2025-01-24 16:35:39'),
(10, 50, 6, 1, '2025-01-24 16:42:18'),
(12, 51, 2, 1, '2025-01-24 17:36:26'),
(13, 51, 1, 1, '2025-01-24 19:49:39'),
(14, 52, 1, 1, '2025-01-24 22:29:12'),
(15, 52, 2, 1, '2025-01-24 22:29:12'),
(17, 52, 4, 2, '2025-01-24 22:29:15'),
(18, 53, 10, 1, '2025-01-25 00:16:57'),
(19, 55, 1, 1, '2025-01-25 00:55:05'),
(20, 55, 2, 1, '2025-01-25 00:55:05'),
(21, 56, 1, 1, '2025-01-25 01:25:49'),
(22, 56, 3, 2, '2025-01-25 01:26:00'),
(23, 56, 4, 2, '2025-01-25 01:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `semester_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`semester_id`, `semester_name`) VALUES
(1, 'Semester 1'),
(2, 'Semester 2');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `grade_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `programme` enum('BCS','BBA','BMC') NOT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `test_score` decimal(5,2) DEFAULT NULL,
  `quiz_score` decimal(5,2) DEFAULT NULL,
  `final_grade` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_grades`
--

INSERT INTO `student_grades` (`grade_id`, `user_id`, `programme`, `semester_id`, `subject_id`, `course_id`, `test_score`, `quiz_score`, `final_grade`) VALUES
(1, 1, 'BCS', 1, 1, 1, 15.00, 18.00, 'A'),
(2, 1, 'BCS', 1, 2, 2, 14.00, 16.00, 'B+'),
(3, 1, 'BCS', 1, 3, 3, 17.00, 19.00, 'A-'),
(4, 1, 'BCS', 2, 4, 4, 16.00, 17.00, 'A'),
(5, 1, 'BCS', 2, 5, 5, 13.00, 15.00, 'B'),
(6, 1, 'BBA', 1, 6, 6, 18.00, 20.00, 'A'),
(7, 1, 'BBA', 1, 7, 7, 12.00, 14.00, 'C+'),
(8, 1, 'BBA', 2, 8, 8, 19.00, 18.00, 'A-'),
(9, 1, 'BBA', 2, 9, 9, 15.00, 16.00, 'B+'),
(10, 1, 'BBA', 2, 10, 10, 14.00, 15.00, 'B'),
(1, 1, 'BCS', 1, 1, 1, 15.00, 18.00, 'A'),
(2, 1, 'BCS', 1, 2, 2, 14.00, 16.00, 'B+'),
(3, 1, 'BCS', 1, 3, 3, 17.00, 19.00, 'A-'),
(4, 1, 'BCS', 2, 4, 4, 16.00, 17.00, 'A'),
(5, 1, 'BCS', 2, 5, 5, 13.00, 15.00, 'B'),
(6, 1, 'BBA', 1, 6, 6, 18.00, 20.00, 'A'),
(7, 1, 'BBA', 1, 7, 7, 12.00, 14.00, 'C+'),
(8, 1, 'BBA', 2, 8, 8, 19.00, 18.00, 'A-'),
(9, 1, 'BBA', 2, 9, 9, 15.00, 16.00, 'B+'),
(10, 1, 'BBA', 2, 10, 10, 14.00, 15.00, 'B');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`) VALUES
(1, 'Data Mining'),
(2, 'Deep Learning'),
(3, 'Web Development'),
(4, 'Problem Solving'),
(5, 'Artificial Intelligence'),
(6, 'Principle of Management'),
(7, 'Financial Accounting'),
(8, 'Marketing Management'),
(9, 'Human Resources Management'),
(10, 'Business Analytics');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `user_group` varchar(50) DEFAULT NULL,
  `user_type` enum('Teacher','Student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `programme`, `dob`, `user_group`, `user_type`) VALUES
(1, 'Alice Johnson', 'alice.johnson@example.com', 'password123', 'BMC', '2002-05-15', 'B', 'Student'),
(2, 'Bob Smith', 'bob.smith@example.com', 'qwerty456', 'BCS', '2001-03-10', 'B', 'Student'),
(3, 'Charlie Brown', 'charlie.brown@example.com', 'asdf7890', 'BBA', '2000-11-25', 'B', 'Student'),
(4, 'Diana White', 'diana.white@example.com', 'zxcv1234', 'BMC', '2003-02-18', 'B', 'Student'),
(5, 'Ethan Green', 'ethan.green@example.com', 'pass5678', 'BCS', '1999-07-09', 'B', 'Student'),
(6, 'Fiona Black', 'fiona.black@example.com', 'secure123', 'BBA', '2002-12-30', 'A', 'Student'),
(7, 'George Blue', 'george.blue@example.com', 'abc123xyz', 'BMC', '2001-06-20', 'B', 'Student'),
(8, 'Hannah Gray', 'hannah.gray@example.com', 'mypassword', 'BCS', '2000-04-15', 'A', 'Student'),
(9, 'Ian Brown', 'ian.brown@example.com', '123secure', 'BBA', '2003-08-22', 'A', 'Student'),
(10, 'Julia King', 'julia.king@example.com', 'password456', 'BMC', '2001-09-13', 'A', 'Student'),
(11, 'Kevin White', 'kevin.white@example.com', 'simple789', 'BCS', '2000-01-30', 'B', 'Student'),
(12, 'Laura Gold', 'laura.gold@example.com', 'complex456', 'BBA', '2002-10-11', 'A', 'Student'),
(13, 'Michael Young', 'michael.young@example.com', 'mypwd123', 'BMC', '1999-03-27', 'B', 'Student'),
(15, 'Oliver Stone', 'oliver.stone@example.com', 'topsecret', 'BBA', '2001-11-17', 'B', 'Student'),
(16, 'Paula Wright', 'paula.wright@example.com', 'letmein789', 'BMC', '2000-07-08', 'A', 'Student'),
(17, 'Quincy Adams', 'quincy.adams@example.com', 'wordpass', 'BCS', '1999-09-12', 'B', 'Student'),
(18, 'Rachel Green', 'rachel.green@example.com', 'opensecret', 'BBA', '2003-05-25', 'A', 'Student'),
(19, 'Steven Pink', 'steven.pink@example.com', 'hidden123', 'BMC', '2001-12-05', 'B', 'Student'),
(20, 'Tina Grayy', 'tina.gray@example.com', 'pass567', 'BCS', '2002-03-19', 'A', 'Student'),
(21, 'Uma Clark', 'uma.clark@example.com', 'coded789', 'BCS', '1999-10-07', '', 'Student'),
(22, 'Victor Lee', 'victor.lee@example.com', 'random123', 'BMC', '2000-02-25', 'A', 'Student'),
(23, 'Wendy Scott', 'wendy.scott@example.com', 'unknown456', 'BCS', '2003-08-03', 'B', 'Student'),
(24, 'Xavier Green', 'xavier.green@example.com', 'securepwd', 'BBA', '2002-11-20', 'A', 'Student'),
(25, 'heheheheh', 'yvonne.lewis@example.com', 'mypwd789', 'BBA', '2000-09-17', 'B', 'Student'),
(26, 'Zachary Carter', 'zachary.carter@example.com', 'simple123', 'BMC', '1999-05-21', 'B', 'Student'),
(27, 'Ava Mitchell', 'ava.mitchell@example.com', 'mypassword456', 'BCS', '2003-01-02', 'A', 'Student'),
(28, 'Ben Rogers', 'ben.rogers@example.com', 'randompwd', 'BMC', '2001-04-11', 'A', 'Student'),
(30, 'Daniel Harris', 'daniel.harris@example.com', 'mysecret123', 'BMC', '2002-06-28', 'A', 'Student'),
(31, 'Alan Baker', 'alan.baker@example.com', 'teach123', 'BBA', '1980-04-15', '', 'Teacher'),
(32, 'Brenda Wilson', 'brenda.wilson@example.com', 'educate456', 'BCS', '1978-03-11', 'A', 'Teacher'),
(33, 'Carl Davis', 'carl.davis@example.com', 'teachme789', 'BBA', '1982-07-21', 'B', 'Teacher'),
(34, 'Diana Martin', 'diana.martin@example.com', 'proteach123', 'BMC', '1985-01-30', 'A', 'Teacher'),
(35, 'Edward Hall', 'edward.hall@example.com', 'teacherpwd', 'BCS', '1979-05-17', 'B', 'Teacher'),
(36, 'Felicity Moore', 'felicity.moore@example.com', 'teachnow456', 'BBA', '1983-09-22', 'A', 'Teacher'),
(37, 'George Clark', 'george.clark@example.com', 'educator789', 'BMC', '1981-06-12', 'B', 'Teacher'),
(38, 'Hannah Price', 'hannah.price@example.com', 'mentor123', 'BCS', '1977-11-19', 'A', 'Teacher'),
(39, 'Isaac Turner', 'isaac.turner@example.com', 'guideme456', 'BBA', '1984-08-03', 'B', 'Teacher'),
(40, 'Julia Fisher', 'julia.fisher@example.com', 'instructor789', 'BMC', '1980-12-25', 'A', 'Teacher'),
(42, 'Nima Yoeze', 'nima@gmail.com', '$2y$10$eUnzm8A26ECv8nSKk6D.HecFNXZ5VW4UFIEGq/008N3pOIGZvw3hu', 'BCS', NULL, '', 'Teacher'),
(43, 'Yalaso', 'Yalaso@gmail.com', '$2y$10$gVLcaEHU6CJBT4hPp9a6TOe5tU83Wj4C/PND2xriZC7OYxIPoPlnq', 'BBA', NULL, '', 'Teacher'),
(44, 'Eren Yeager', 'abdula@gmail.com', '$2y$10$E4PnjWpmLVMgFkTaZPZtAubQcGgUoZustBium7obn.ypgaew8MAuG', 'BMC', '2025-01-14', '', 'Teacher'),
(45, 'Ugyen', 'ok@gmail.com', '$2y$10$2FceFi/80Qej/oiDXogvseTGavjOxLpY/0JzcFQv5HiHZJl9wcReq', 'BBA', '2025-01-15', 'B', 'Student'),
(47, 'Ugyen Tshering', 'asdfsad@gmailomc', '$2y$10$zGXxxdJm8ilzWvti.c9OBeDFU1aYgmrzrAQzgYuxPtIf5ynopTYFq', 'BMC', NULL, 'B', 'Student'),
(48, 'Okie Man', 'okieman@gmail.com', '$2y$10$EftEeXoGXqAdM/5CkVlEu.gn1W1KzJjVhnC1H/.OfWfvgzFbISpwG', 'BBA', NULL, 'A', 'Student'),
(49, 'Hamid', 'hamid@gmail.com', '$2y$10$IfbWvqbm7TLb3l7.kdWgH.9DuZsS79mh9liEm2EQPOJs8Oc4Gsd3a', 'BBA', '2025-01-22', 'B', 'Student'),
(50, 'Yals Sooo Song Song ', 'yaa@gmail.com', '$2y$10$p.SpvkjNb.5NrPTYu4Q8QOKy6v26jktBc6OiHya2hOFbZp/RXAcyK', 'BCS', '2025-01-08', 'B', 'Student'),
(51, 'Hamid Hamid', 'hamid@gmial.com', '$2y$10$dhTvFstoAPw4U2VsQi2oJey68tT9ZMc4ziDUrg.dO3PAaPxatAXS2', 'BCS', '2025-01-15', 'A', 'Student'),
(52, 'Ugyen Tshering', 'ugyen@gmail.com', '$2y$10$NkB3SfWSXEEHPiVLA5IHK.1fI7n.PtM/m8MdZrJ/e8CdWO7ECzAMW', 'BCS', '2025-01-07', 'A', 'Student'),
(53, 'Dorji Tshering Dorji', 'dorji@hotmail.com', '$2y$10$nTKmoDf9OXHtGS3d2j8SS.1e6tVbmZP28L69BqRoWxFDaaYwpBNsC', 'BMC', '2025-01-01', 'B', 'Student'),
(54, 'Mozarahel', 'moza@gmail.com', '$2y$10$zY153kSMVD.rG5ZzIHmh.Ok8g68x2FdA/V4TsfL8CzTu58XnI2LQO', 'BMC', NULL, 'A', 'Teacher'),
(55, 'Hamid Hamid Sok', 'sok@gmail.com', '$2y$10$IZwXjl8IEfE7YsfX33QO2uwAfzT3YDNZYRejOJOxbhLFCRVor9SFu', 'BCS', '2025-01-29', 'B', 'Student'),
(56, 'Student A', 'student@gmail.com', '$2y$10$FgenHuxA6T7Uq39vOjfeaeUuiuWUSTZ/JCb5LYAXJLamsv1K2LM32', 'BCS', '2025-01-07', 'A', 'Student'),
(57, 'Teacher Teacher B', 'teacher@gmail.com', '$2y$10$aAM8AI4fkmpe.Npbvyl18exUYVvU1RtNl9WXyGIZiijyT9NRgo6Aa', 'BCS', NULL, NULL, 'Teacher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`attendance_date`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`),
  ADD UNIQUE KEY `semester_name` (`semester_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
