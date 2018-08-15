-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Aug 15, 2018 at 02:02 PM
-- Server version: 8.0.12
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tec_intern_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `title`, `description`) VALUES
(1, 'Buatlah CV', 'Bikin CV semenarik mungkin'),
(2, 'Buatlah Resume', 'Bikin resume pertemuan kemarin'),
(3, 'Buatlah Portofolio', 'Bikin portfolio');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `coupon` varchar(255) NOT NULL,
  `lunas` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `coupon`, `lunas`) VALUES
(1, 'JKZDXOPT', 1),
(2, 'PLCKEBRL', 1),
(3, 'IJUGYHOA', 1),
(5, 'HGDRKVTG', 1),
(6, 'DTNHCESP', 1),
(7, 'GYXOEGEX', 1),
(8, 'LVRFBAVF', 1),
(9, 'LKAPRZRL', 1),
(10, 'TUWJTCZY', 1),
(11, 'SDECIZDM', 1),
(12, 'MZQFHYKZ', 1),
(13, 'LLMVXWUH', 1),
(15, 'KJKHMGYA', 1),
(16, 'XGZQZPAG', 1),
(17, 'LBEECWCN', 1),
(18, 'WDHYKIWW', 1),
(19, 'RYAOWDOS', 1),
(20, 'QMLIOMJT', 1),
(21, 'OGLUQBDL', 1),
(22, 'DMNEYLWB', 1),
(23, 'TRIXDOZB', 1),
(24, 'AFZBUKNT', 1),
(25, 'XMUWAWGC', 1),
(26, 'VRKANDFZ', 1),
(27, 'BNXOVCTQ', 1),
(28, 'FZBWCGIA', 1),
(29, 'WKDXPNIN', 1),
(30, 'QAAPMIYE', 1),
(31, 'CPJKOQCW', 1),
(32, 'XMWHKFIK', 1),
(33, 'OPECIJZK', 1),
(34, 'MZILLJCM', 1),
(35, 'XANIUXBK', 1),
(36, 'UWIYKIFB', 1),
(37, 'IYFTRKKZ', 1),
(38, 'FSPPMJOA', 1),
(39, 'SQXDTUOO', 1),
(40, 'UUPKATVZ', 1),
(41, 'NFQZRRHH', 1),
(42, 'KSDVBBGL', 1),
(43, 'XDVSCAXN', 1),
(44, 'JDGAQBFQ', 1),
(45, 'JOECIPBY', 1),
(46, 'ISOCFUUG', 1),
(47, 'SNUMWPFJ', 1),
(48, 'UDWIACFF', 1),
(49, 'QYDWBMDA', 1),
(50, 'WZFZXJXV', 1),
(51, 'LNJMNOBK', 1),
(52, 'POREOZVQ', 1),
(53, 'VVDMOQNJ', 1),
(54, 'UPLTNEFF', 1),
(55, 'QFVWZGIA', 1),
(56, 'MGSPEECR', 1),
(57, 'PZIQVVWQ', 1),
(58, 'AMLHYHVE', 1),
(59, 'ZFFECMIG', 1),
(60, 'ZEKHELCH', 1),
(61, 'CENDLHLX', 1),
(62, 'VUGCAIKJ', 1),
(63, 'LDBOLRTE', 1),
(64, 'BNGSNBZR', 1),
(65, 'DCKNWYDB', 1),
(66, 'CHXQQJAK', 1),
(67, 'IMZGFLTG', 1),
(68, 'WZSXUIHO', 1),
(69, 'MSQQWMVV', 1),
(70, 'KWWYFAJW', 1),
(71, 'CYGHHMMS', 1),
(72, 'IFCIUOVA', 1),
(73, 'JJOGMRBS', 1),
(74, 'WBBWZJAF', 1),
(75, 'ZQEAZACZ', 1),
(76, 'SYEVQXLF', 1),
(77, 'SNQFVAJV', 1),
(78, 'NFOXSURI', 1),
(79, 'RNOILHAH', 1),
(80, 'XAWHZOSC', 1),
(81, 'URKVLGCR', 1),
(82, 'LDNQNEKS', 1),
(83, 'WMAHCSZT', 1),
(84, 'DCIOXRRZ', 1),
(85, 'MJUBXLRI', 1),
(86, 'VIKMUHMO', 1),
(87, 'VPGFCNCU', 1),
(88, 'DOGBCCEV', 1),
(89, 'NQLQXUVO', 1),
(90, 'THUUNDPH', 1),
(91, 'NHMCMCNT', 1),
(92, 'BDXZLFEB', 1),
(93, 'NDEPTLPY', 1);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `type` int(11) NOT NULL,
  `head` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `type`, `head`) VALUES
(1, 'Lapangan', 2, 1),
(2, 'Tim UNIX', 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `peer_to_peer`
--

CREATE TABLE `peer_to_peer` (
  `id` int(10) UNSIGNED NOT NULL,
  `penilai` int(10) UNSIGNED NOT NULL,
  `dinilai` int(10) UNSIGNED NOT NULL,
  `nilai` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `peer_to_peer`
--

INSERT INTO `peer_to_peer` (`id`, `penilai`, `dinilai`, `nilai`) VALUES
(1, 1, 3, 5),
(3, 1, 2, 1);

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
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `NIM` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lunas` tinyint(1) NOT NULL,
  `verified` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` int(11) NOT NULL DEFAULT '1',
  `interests` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `about_me` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `line_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `instagram` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tec_regno` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_picture_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` int(11) NOT NULL DEFAULT '1',
  `gid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `NIM`, `created_at`, `updated_at`, `lunas`, `verified`, `isAdmin`, `is_active`, `interests`, `nickname`, `about_me`, `line_id`, `instagram`, `mobile`, `tec_regno`, `address`, `profile_picture`, `profile_picture_url`, `role`, `gid`) VALUES
(1, 'Terry Djony', 'demokader@tec.itb.ac.id', '$2y$10$W/sVuNC73RgVexLhMwRMj.jA2rN0th7owX7hrdxn.m2YuAoIAt29G', 13316014, '2018-05-24 01:10:00', '2018-05-24 01:10:48', 0, 'Yes', 1, 1, '', '', '', '', '', '', 'A17001', '', 'userpic/user_1_71f02111cf64d7a5.png', 'https://tec-test.sgp1.digitaloceanspaces.com/userpic/user_1_71f02111cf64d7a5.png', 1, NULL),
(2, 'John Terry', 'johnterry@gmail.com', '$2y$10$AcfUvDusmZN5/ZRbWAFHZOswg22gD/UOt.8gFfJy8NAJwaIo0r8Z2', 0, '2018-05-25 10:39:53', '2018-05-25 16:39:54', 0, '659a6d82e0ec8cbb5ac3f60adb9fcaf4', 0, 1, '', '', '', '', '', '', '', '', '', '', 1, NULL),
(3, 'Terry Jhonny', 'terryjhonny@gmail.com', '$2y$10$tG/20YpUK9diSPB75aJfSu.yqr8POHssDIP6fGgb4kEdkxR6sQpDW', 0, '2018-05-25 13:48:49', '2018-05-25 19:48:50', 1, 'fb2206e6c003e65c3dfc00caefd67fcf', 0, 1, '', '', '', '', '', '', '', '', '', '', 1, NULL),
(4, 'Muhammad Aditya Hilmy', 'didithilmy@gmail.com', '$2y$10$nq6vUexGUmP.noqWQwjAAu05eunkqZ9cYriAYd0Wz.SHiKw4cmnHC', 16517292, '2018-06-23 03:27:06', '2018-06-23 03:27:06', 1, 'c4dd7ea1dd5f70f7ba7da117cb84c271', 0, 1, 'tech,financial', 'Didit', 'Technology enthusiast, problem solver, curious person.', 'webid', 'didithilmy', '087870408551', 'TEC001', 'Jl. Bogor', NULL, '', 1, NULL),
(5, 'Adyaksa Wisanggeni', 'adyaksa@iwa.ng', '$2y$10$GgJiXV0Wfn.57Je7ZWkFOePrtZ9iCEtPlL.vP1AHZ13APhe4SiTz2', 16517351, '2018-06-23 03:58:28', '2018-06-23 03:58:28', 1, 'ca2f4a7e9673829acde6eaacb1629912', 0, 1, 'tech,artsndesign', 'Iwang', 'Saya wibu', 'wangky', '-', '08111111111', 'TEC044', 'Tokopedia Tower', NULL, '', 1, NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `user_assignment`
--

CREATE TABLE `user_assignment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_url` text NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_memories`
--

CREATE TABLE `user_memories` (
  `id` bigint(20) NOT NULL,
  `user_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `memories_with` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Entity UID (TEC regno)',
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `img_path` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `img_filename` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_memories`
--

INSERT INTO `user_memories` (`id`, `user_id`, `memories_with`, `text`, `img_path`, `img_filename`) VALUES
(1, '1', 'TEC044', 'Iwang is probably the most unusual person I have ever met. As far as I know, he is the most *gaptek* person in STEI 2017, despite having a medal on Computer Science. Yes, computer science. One day he told me that there was a day when he could not get a glass of water just because he was unable to operate the water dispenser in his workplace. A freakin water dispenser. So, my impression to Iwang would be the guy is very much gaptek. He is really smart, though. He was able to solve a CP problem just by brushing his infamous beard. But a guy like that does not go without weakness, as he is very very gaptek.\r\n\r\nNice one wang!', 'https://tec-test.sgp1.digitaloceanspaces.com/memories/1_TEC044_5b5f3a21dd8599f0944e32a5be614.jpg', 'memories/1_TEC044_5b5f3a21dd8599f0944e32a5be614.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_relations`
--

CREATE TABLE `user_relations` (
  `id` bigint(20) NOT NULL,
  `user_id` text NOT NULL,
  `relation_with` text NOT NULL COMMENT 'TEC ID no',
  `vcard` text NOT NULL,
  `full_name` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  `last_modified` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_relations`
--

INSERT INTO `user_relations` (`id`, `user_id`, `relation_with`, `vcard`, `full_name`, `timestamp`, `is_deleted`, `last_modified`) VALUES
(4, '1', 'PN012', 'BEGIN:VCARD\r\nFN:Umar Hilmi Fadhilah\r\nEMAIL;INTERNET=:umarhilmif@gmail.com\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:01234567899\r\nADR;HOME=:Kantor Pusat Ditjen Pajak; Jl. Gatot Subroto Kav. 40-43; Jakarta \r\n Selatan\r\nUID:PN012\r\nNOTE:Biochemistry addict; part-time trader; ultimate weaboo\r\nX-LINE:umarhilmif\r\nX-INSTAGRAM:@umarhilmif\r\nX-TWITTER:@umarhilmif\r\nEND:VCARD', 'Umar Hilmi Fadhilah', '2018-06-25 16:08:23', 0, 0),
(8, '1', 'TEC193', 'BEGIN:VCARD\r\nFN:Galih Fajar Fitra Ady\r\nEMAIL;INTERNET=:galih@tec.itb.ac.id\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:084456545657\r\nADR;HOME=:HQ DS Corp; Jl. Sangkuriang No. 13; Bandung 40135\r\nUID:TEC193\r\nNOTE:Math addict; Agus Yodi lover; fans of Hudang\r\nX-LINE:galihfajar\r\nX-INSTAGRAM:@galihfajar\r\nEND:VCARD', 'Galih Fajar Fitra Ady', '2018-06-25 16:15:44', 0, 0),
(22, '4', 'TEC204', 'BEGIN:VCARD\r\nFN:Almyra Ramadhina\r\nEMAIL;INTERNET=:almyra@tec.itb.ac.id\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:08123456554\r\nADR;HOME=:Perpustakaan Pusat ITB; Jl. Ganesha No. 10; Bandung 40135\r\nUID:TEC204\r\nNOTE:Queen of receh; ultrasonic expert; (Masjid) Salman resident\r\nX-LINE:sayareceh\r\nX-INSTAGRAM:@almyramadhina\r\nEND:VCARD', 'Almyra Ramadhina', '2018-06-28 12:25:38', 0, 0),
(23, '4', 'TEC054', 'BEGIN:VCARD\r\nFN:Bimo Adityarahman Wiraputra\r\nEMAIL;INTERNET=:b@imo-official.org\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:087766545626\r\nADR;HOME=:Nasi Goreng Mafia; Jl. Dipatiukur No.51; Bandung  40132\r\nUID:TEC054\r\nNOTE:Bimo tanpa IMO; B aja\r\nX-LINE:bimoaw\r\nX-INSTAGRAM:@bimoaw\r\nEND:VCARD', 'Bimo Adityarahman Wiraputra', '2018-06-28 12:25:46', 0, 0),
(28, '4', 'TEC044', 'BEGIN:VCARD\r\nFN:Adyaksa Wisanggeni\r\nEMAIL;INTERNET=:adyaksa@iwa.ng\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:08774654134\r\nADR;HOME=:Jl. Cisitu Lama V No. 13; Bandung 40135\r\nUID:TEC044\r\nNOTE:Competitive programmer; absolute gaptek; part-time weaboo\r\nX-LINE:adyaksa.w\r\nX-INSTAGRAM:@adyaksa.w\r\nEND:VCARD', 'Adyaksa Wisanggeni', '2018-06-28 12:30:36', 0, 0),
(30, '5', 'A17001', 'BEGIN:VCARD\r\nFN:Umar Hilmi Fadhilah\r\nEMAIL;INTERNET=:umarhilmif@gmail.com\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:01234567899\r\nADR;HOME=:Kantor Pusat Ditjen Pajak; Jl. Gatot Subroto Kav. 40-43; Jakarta \r\n Selatan\r\nUID:PN012\r\nNOTE:Biochemistry addict; part-time trader; ultimate weaboo\r\nX-LINE:umarhilmif\r\nX-INSTAGRAM:@umarhilmif\r\nX-TWITTER:@umarhilmif\r\nEND:VCARD', 'Umar Hilmi Fadhilah', '2018-06-29 12:24:34', 0, 0),
(31, '1', 'TEC204', 'BEGIN:VCARD\r\nFN:Almyra Ramadhina\r\nEMAIL;INTERNET=:almyra@tec.itb.ac.id\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:08123456554\r\nADR;HOME=:Perpustakaan Pusat ITB; Jl. Ganesha No. 10; Bandung 40135\r\nUID:TEC204\r\nNOTE:Queen of receh; ultrasonic expert; (Masjid) Salman resident\r\nX-LINE:sayareceh\r\nX-INSTAGRAM:@almyramadhina\r\nEND:VCARD', 'Almyra Ramadhina', '2018-07-26 03:21:18', 0, 1532966487),
(36, '1', 'TEC054', 'BEGIN:VCARD\r\nFN:Bimo Adityarahman Wiraputra\r\nEMAIL;INTERNET=:b@imo-official.org\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:087766545626\r\nADR;HOME=:Nasi Goreng Mafia; Jl. Dipatiukur No.51; Bandung  40132\r\nUID:TEC054\r\nNOTE:Bimo tanpa IMO; B aja\r\nX-LINE:bimoaw\r\nX-INSTAGRAM:@bimoaw\r\nEND:VCARD', 'Bimo Adityarahman Wiraputra', '2018-07-26 08:15:28', 0, 1532964276),
(37, '1', 'TEC017', 'BEGIN:VCARD\r\nFN:Reyhan Naufal Hakim\r\nEMAIL;INTERNET=:reyhan_kim@icloud.com\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:087744851546\r\nADR;HOME=:Jl. Cisitu Lama V No. 13;Bandung;40135\r\nUID:TEC017\r\nNOTE:Computer geek; entrepreneur wannabe; full-time weeabo\r\nX-LINE:reyhankim\r\nX-INSTAGRAM:@reyhankim\r\nX-TWITTER:@reyhankim\r\nEND:VCARD', 'Reyhan Naufal Hakim', '2018-07-28 03:08:09', 0, 1532965984),
(42, '1', 'TEC085', 'BEGIN:VCARD\r\nFN:Muhammad Fathiyakan Ramadhan\r\nEMAIL;INTERNET=:fathi@tec.itb.ac.id\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:087744851546\r\nADR;HOME=:Labtek XIV\\, Kampus ITB Ganesha;Jl. Ganesha No. 10;Bandung 40135\r\nUID:TEC085\r\nNOTE:Future businessman; open-minded; decision maker\r\nX-LINE:fathiyakan.r\r\nX-INSTAGRAM:@fathiyakan.r\r\nX-TWITTER:@fathiyakan.r\r\nEND:VCARD', 'Muhammad Fathiyakan Ramadhan', '2018-07-29 11:06:53', 1, 1532964087),
(44, '1', 'TEC044', 'BEGIN:VCARD\r\nFN:Adyaksa Wisanggeni\r\nEMAIL;INTERNET=:adyaksa@iwa.ng\r\nORG:Techno Entrepreneur Club ITB\r\nTEL;CELL=:08774654134\r\nADR;HOME=:Jl. Cisitu Lama V No. 13; Bandung 40135\r\nUID:TEC044\r\nNOTE:Competitive programmer; absolute gaptek; part-time weaboo\r\nX-LINE:adyaksa.w\r\nX-INSTAGRAM:@adyaksa.w\r\nEND:VCARD', 'Adyaksa Wisanggeni', '2018-07-30 03:53:15', 0, 1532966469);

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
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupon` (`coupon`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `peer_to_peer`
--
ALTER TABLE `peer_to_peer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `p2p_idx` (`penilai`,`dinilai`);

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
-- Indexes for table `user_assignment`
--
ALTER TABLE `user_assignment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_memories`
--
ALTER TABLE `user_memories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_relations`
--
ALTER TABLE `user_relations`
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
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peer_to_peer`
--
ALTER TABLE `peer_to_peer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_answer`
--
ALTER TABLE `user_answer`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_assignment`
--
ALTER TABLE `user_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
