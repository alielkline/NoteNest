-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 27, 2025 at 04:34 PM
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
-- Database: `notenestdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`note_id`, `user_id`) VALUES
(2, 28),
(3, 28),
(3, 29);

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `classroom_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `invite_code` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `members` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `visibility` enum('public','private') NOT NULL DEFAULT 'public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`classroom_id`, `name`, `creator_id`, `invite_code`, `created_at`, `members`, `description`, `visibility`) VALUES
(1, 'testing1', 22, 'xa213', '2025-04-22 18:33:47', 1, 'testing 1234', 'public'),
(4, 'Hello', 22, '8HndWHZx', '2025-04-22 21:42:09', 1, 'Hello from ALi', 'public'),
(8, 'Abass Class', 29, '520aed5b', '2025-04-22 22:02:29', 1, 'Abass Class iz za bezt', 'public'),
(9, 'hehehe', 29, 'c3189d4f', '2025-04-22 22:03:30', 1, 'heheh', 'private'),
(10, 'BomboClat', 22, '5cfbfcd4', '2025-04-26 07:36:06', 1, 'HEHEHEHEHE', 'private'),
(12, 'asd', 22, 'f22e27c7', '2025-04-26 16:24:30', 1, 'asd', 'public');

-- --------------------------------------------------------

--
-- Table structure for table `classroom_members`
--

CREATE TABLE `classroom_members` (
  `classroom_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classroom_members`
--

INSERT INTO `classroom_members` (`classroom_id`, `user_id`) VALUES
(1, 22),
(4, 22),
(8, 22),
(10, 22),
(12, 22),
(1, 25),
(8, 29),
(9, 29);

-- --------------------------------------------------------

--
-- Table structure for table `classroom_notes`
--

CREATE TABLE `classroom_notes` (
  `note_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `uploader_user_id` int(11) NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `bookmarkes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classroom_notes`
--

INSERT INTO `classroom_notes` (`note_id`, `subject_id`, `title`, `content`, `uploader_user_id`, `upload_date`, `likes`, `bookmarkes`) VALUES
(1, 2, 'Transformers', 'HELLO THIS IS Transforms note', 22, '2025-04-26 15:27:10', 0, 0),
(2, 4, 'Gandgun style', 'HELLO THIS IS gungum style', 22, '2025-04-26 15:27:58', 0, 0),
(3, 7, 'Testing note', 'asdasdasdasd', 22, '2025-04-26 15:29:22', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `classroom_subjects`
--

CREATE TABLE `classroom_subjects` (
  `subject_id` int(11) NOT NULL,
  `classroom_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `notes` int(11) NOT NULL DEFAULT 0,
  `subject_desc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classroom_subjects`
--

INSERT INTO `classroom_subjects` (`subject_id`, `classroom_id`, `subject_name`, `notes`, `subject_desc`) VALUES
(2, 4, 'ML', 0, 'this is the ml'),
(4, 4, 'Science', 0, 'hohohh'),
(5, 4, 'DATA', 0, 'hehehehe'),
(7, 1, 'testing', 0, 'hahahaha'),
(8, 1, 'daqs', 0, NULL),
(9, 1, 'asd', 0, ''),
(10, 1, 'asd', 0, 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `comment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`note_id`, `user_id`) VALUES
(3, 24),
(1, 28),
(3, 29);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `profile_image`) VALUES
(22, 'AliElkline', 'alielkline7@gmail.com', '$2y$10$kdmArEs53pmoq0Ak5x7jd..reiF9vHzlnK/at2XeaojGOKtpWLqU2', '2025-04-19 14:50:49', '22_profile.jpg'),
(23, 'omar', 'omar@gmail.com', '$2y$10$jVBckmFU35c2pZZqaDA1MeBCFNqLGjl1P5niTGyTWaGMU1qk3xgAC', '2025-04-19 14:52:11', NULL),
(24, 'adam', 'adam@gmail.com', '$2y$10$RaeK48Y.oxKP/.lmH95u0ebb.rrftn/GeF.EHtsUMpUiNnLB8Kx4a', '2025-04-19 15:13:15', NULL),
(25, 'Ali2', 'alielkline10@gmail.com', '$2y$10$qowKctW2KZT/CzC7zyFZFeq5sgCQ.4UIqkFF5.zDCCt6NLVDevOHq', '2025-04-22 19:56:16', NULL),
(26, 'Ali3', 'alielkline12@gmail.com', '$2y$10$NxrjaJ7sBE.wZrtVszVxyeMX1crQ5uXQC6RbRJyII9zE5Tdup2ijq', '2025-04-22 19:59:27', NULL),
(27, 'aliali', 'alielkline10@gmail.com', '$2y$10$cqWtTOvELNVw7FOOnOdewu7tISzx1rJPtcpAeyllZ2pgTpc.E5qsG', '2025-04-22 20:00:52', NULL),
(28, 'ali123', 'a123li@gmail.com', '$2y$10$7sZBybJghuZv.8AcM9gX8urtoMBRmCXHkEloZJaSJDdjacScWERO6', '2025-04-22 20:01:22', NULL),
(29, 'abass', 'abass@gmail.com', '$2y$10$VQOCqoLwO0.SGu7EUWqM7.M5JGqxDMpdP0SWvg.4cN0X08i41QR5m', '2025-04-22 20:01:56', '29_profile.png'),
(30, 'john', 'john@gmail.com', '$2y$10$nFAtVQddcYyda.klUEFRV.zaopEBjvaYW/A8z6sOiGnhYTrmQ2I1S', '2025-04-26 04:51:04', 'default-image.jpg'),
(31, 'hehe', 'hehe@gmail.com', '$2y$10$6R.27rv0ACj7wWgUfW2Xi.ZCeL064l2k2Rl4ZRKkqbrRB855zxAxC', '2025-04-26 04:51:47', 'profile-default.jpg'),
(32, 'doe', 'doe@gmail.com', '$2y$10$Zl/g9nkhnNKCtNr658aok.srTSElipjIxWG5cTrJDM5GPTzA.BSqe', '2025-04-26 04:53:08', 'profile-default.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`user_id`,`note_id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`classroom_id`),
  ADD UNIQUE KEY `invite_code` (`invite_code`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `classroom_members`
--
ALTER TABLE `classroom_members`
  ADD PRIMARY KEY (`user_id`,`classroom_id`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- Indexes for table `classroom_notes`
--
ALTER TABLE `classroom_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `uploader_user_id` (`uploader_user_id`);

--
-- Indexes for table `classroom_subjects`
--
ALTER TABLE `classroom_subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`user_id`,`note_id`),
  ADD KEY `note_id` (`note_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `classroom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `classroom_notes`
--
ALTER TABLE `classroom_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classroom_subjects`
--
ALTER TABLE `classroom_subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `classroom_notes` (`note_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD CONSTRAINT `classrooms_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classroom_members`
--
ALTER TABLE `classroom_members`
  ADD CONSTRAINT `classroom_members_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classroom_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classroom_notes`
--
ALTER TABLE `classroom_notes`
  ADD CONSTRAINT `classroom_notes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `classroom_subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classroom_notes_ibfk_2` FOREIGN KEY (`uploader_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classroom_subjects`
--
ALTER TABLE `classroom_subjects`
  ADD CONSTRAINT `classroom_subjects_ibfk_1` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `classroom_notes` (`note_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `classroom_notes` (`note_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
