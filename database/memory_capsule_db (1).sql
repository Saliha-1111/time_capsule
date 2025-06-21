-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 12:27 PM
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
-- Database: `memory_capsule_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `capsules`
--

CREATE TABLE `capsules` (
  `capsule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `open_date` date DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `capsules`
--

INSERT INTO `capsules` (`capsule_id`, `user_id`, `title`, `open_date`, `is_locked`) VALUES
(1, 1, 'Trip to Swat', '2025-07-01', 1),
(2, 1, 'Aesthetic Pinterest Board', '2024-01-01', 0),
(3, 1, 'Friendship Notes Collection', '2025-12-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `capsule_contents`
--

CREATE TABLE `capsule_contents` (
  `content_id` int(11) NOT NULL,
  `capsule_id` int(11) NOT NULL,
  `content_type` enum('video','image','note') DEFAULT NULL,
  `content_path` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `capsule_contents`
--

INSERT INTO `capsule_contents` (`content_id`, `capsule_id`, `content_type`, `content_path`) VALUES
(15, 1, 'video', 'uploads/videos/swat_trip.mp4'),
(16, 1, 'note', 'uploads/notes/swat_memories.txt'),
(17, 1, 'image', 'uploads/images/swat_valley1.jpg'),
(18, 2, 'image', 'uploads/images/aesthetics_1.jpg'),
(19, 2, 'image', 'uploads/images/aesthetic_2'),
(20, 2, 'note', 'uploads/notes/pinterest_quotes.txt'),
(21, 3, 'note', 'uploads/notes/friendship_letters.txt');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `friend_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `favorite_color` varchar(30) NOT NULL,
  `birthdate` date NOT NULL,
  `friendship_start_date` date NOT NULL,
  `meeting_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`friend_id`, `user_id`, `first_name`, `last_name`, `favorite_color`, `birthdate`, `friendship_start_date`, `meeting_description`) VALUES
(6, 1, 'Manahil', 'Ali', 'Lavender', '2003-06-15', '2017-03-01', 'We met at the art competition in 9th grade.'),
(7, 1, 'Zainab', 'Riaz', 'Sky Blue', '2003-09-25', '2016-07-18', 'First met during school orientation.'),
(8, 1, 'Elaf', 'Hashmi', 'Mint Green', '2004-01-09', '2018-02-22', 'Met in a bookstore over a shared book interest.'),
(9, 1, 'Aimen', 'Fatima', 'Rose Pink', '2003-12-10', '2015-12-01', 'Our friendship started during a group project.'),
(10, 1, 'Amina', 'Khan', 'Peach', '2002-08-21', '2019-06-11', 'Became friends after a surprise birthday celebration.');

-- --------------------------------------------------------

--
-- Table structure for table `friend_capsule_relation`
--

CREATE TABLE `friend_capsule_relation` (
  `id` int(11) NOT NULL,
  `capsule_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend_capsule_relation`
--

INSERT INTO `friend_capsule_relation` (`id`, `capsule_id`, `friend_id`) VALUES
(1, 1, 6),
(2, 1, 9),
(3, 2, 7),
(4, 3, 6),
(5, 3, 7),
(6, 3, 8),
(7, 3, 9),
(8, 3, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`) VALUES
(1, 'saliha', 'pass123', 'Saliha Naseer'),
(2, 'aimen22', 'helloWorld', 'Aimen Fatima');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `capsules`
--
ALTER TABLE `capsules`
  ADD PRIMARY KEY (`capsule_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `capsule_contents`
--
ALTER TABLE `capsule_contents`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `capsule_id` (`capsule_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`friend_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `friend_capsule_relation`
--
ALTER TABLE `friend_capsule_relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `capsule_id` (`capsule_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UNIQUE` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `capsules`
--
ALTER TABLE `capsules`
  MODIFY `capsule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `capsule_contents`
--
ALTER TABLE `capsule_contents`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `friend_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `friend_capsule_relation`
--
ALTER TABLE `friend_capsule_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `capsules`
--
ALTER TABLE `capsules`
  ADD CONSTRAINT `capsules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `capsule_contents`
--
ALTER TABLE `capsule_contents`
  ADD CONSTRAINT `capsule_contents_ibfk_1` FOREIGN KEY (`capsule_id`) REFERENCES `capsules` (`capsule_id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `friend_capsule_relation`
--
ALTER TABLE `friend_capsule_relation`
  ADD CONSTRAINT `friend_capsule_relation_ibfk_1` FOREIGN KEY (`capsule_id`) REFERENCES `capsules` (`capsule_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friend_capsule_relation_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `friends` (`friend_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
