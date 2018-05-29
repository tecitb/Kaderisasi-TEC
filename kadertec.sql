-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 29, 2018 at 05:38 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kadertec`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `coupon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `coupon`) VALUES
(14, 'BJEQESBP'),
(4, 'DBQLJTRH'),
(6, 'DTNHCESP'),
(7, 'GYXOEGEX'),
(5, 'HGDRKVTG'),
(3, 'IJUGYHOA'),
(1, 'JKZDXOPT'),
(15, 'KJKHMGYA'),
(17, 'LBEECWCN'),
(9, 'LKAPRZRL'),
(13, 'LLMVXWUH'),
(8, 'LVRFBAVF'),
(12, 'MZQFHYKZ'),
(2, 'PLCKEBRL'),
(20, 'QMLIOMJT'),
(19, 'RYAOWDOS'),
(11, 'SDECIZDM'),
(10, 'TUWJTCZY'),
(18, 'WDHYKIWW'),
(16, 'XGZQZPAG');

-- --------------------------------------------------------

--
-- Table structure for table `question_answer`
--

CREATE TABLE `question_answer` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `decoy` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quiz_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `question_answer`
--

INSERT INTO `question_answer` (`id`, `type`, `question`, `answer`, `decoy`, `created_at`, `quiz_id`) VALUES
(1, 'pilgan', 'Siapa pendiri Microsoft?', 'Bill Gates', 'Elon Musk, Mark Zuckerberg, Steve Wozniak', '2018-05-24 10:55:00', 1),
(2, 'isian', 'Nama perusahaan yang mengeluarkan iPod?', 'Apple', '', '2018-05-24 11:00:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `title`) VALUES
(1, 'Kuis Pendiri Startup');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lunas` tinyint(1) NOT NULL,
  `verified` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`, `lunas`, `verified`, `isAdmin`) VALUES
(1, 'Terry Djony', 'demokader@tec.itb.ac.id', '$2y$10$W/sVuNC73RgVexLhMwRMj.jA2rN0th7owX7hrdxn.m2YuAoIAt29G', '2018-05-24 01:10:00', '2018-05-24 01:10:48', 0, 'Yes', 1),
(2, 'John Terry', 'johnterry@gmail.com', '$2y$10$AcfUvDusmZN5/ZRbWAFHZOswg22gD/UOt.8gFfJy8NAJwaIo0r8Z2', '2018-05-25 10:39:53', '2018-05-25 16:39:54', 0, '659a6d82e0ec8cbb5ac3f60adb9fcaf4', 0),
(3, 'Terry Jhonny', 'terryjhonny@gmail.com', '$2y$10$tG/20YpUK9diSPB75aJfSu.yqr8POHssDIP6fGgb4kEdkxR6sQpDW', '2018-05-25 13:48:49', '2018-05-25 19:48:50', 1, 'fb2206e6c003e65c3dfc00caefd67fcf', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_answer`
--

CREATE TABLE `user_answer` (
  `id` int(10) UNSIGNED NOT NULL,
  `answer` text NOT NULL,
  `qa_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_answer`
--

INSERT INTO `user_answer` (`id`, `answer`, `qa_id`, `user_id`) VALUES
(1, 'Bill Gates', 1, 1),
(2, 'Djarum', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_reset`
--

CREATE TABLE `user_reset` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `resetToken` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_reset`
--

INSERT INTO `user_reset` (`user_id`, `resetToken`) VALUES
(1, '46110f3354c5cf90ee73c57b243364e7');

-- --------------------------------------------------------

--
-- Table structure for table `user_score`
--

CREATE TABLE `user_score` (
  `id` int(10) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_score`
--

INSERT INTO `user_score` (`id`, `score`, `quiz_id`, `user_id`) VALUES
(1, 50, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon` (`coupon`);

--
-- Indexes for table `question_answer`
--
ALTER TABLE `question_answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_answer`
--
ALTER TABLE `user_answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_reset`
--
ALTER TABLE `user_reset`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_score`
--
ALTER TABLE `user_score`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `question_answer`
--
ALTER TABLE `question_answer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_answer`
--
ALTER TABLE `user_answer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_score`
--
ALTER TABLE `user_score`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
