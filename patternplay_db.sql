-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 04:53 PM
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
-- Database: `patternplay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` varchar(255) NOT NULL,
  `answer_image` varchar(255) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `answer_image`, `is_correct`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, '10', NULL, 1, 'Benar! Pola ini bertambah 2 setiap langkah. 8 + 2 = 10', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(2, 1, '9', NULL, 0, 'Pola ini bukan bertambah 1, tapi bertambah 2. Jawaban yang benar adalah 10', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(3, 1, '12', NULL, 0, 'Pola ini bertambah 2, bukan 4. Jawaban yang benar adalah 10', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(4, 1, '7', NULL, 0, 'Pola ini naik, bukan turun. Jawaban yang benar adalah 10', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(5, 2, '25', NULL, 1, 'Benar! Pola ini adalah kelipatan 5. 20 + 5 = 25', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(6, 2, '24', NULL, 0, 'Ini bukan pola +4. Pola ini adalah kelipatan 5. Jawaban yang benar adalah 25', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(7, 2, '30', NULL, 0, 'Pola ini +5, bukan +10. Jawaban yang benar adalah 25', '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(8, 2, '22', NULL, 0, 'Pola ini +5, bukan +2. Jawaban yang benar adalah 25', '2025-11-27 14:54:56', '2025-11-27 14:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `category` enum('angka','huruf','gambar','kalender') NOT NULL,
  `level_number` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `category`, `level_number`, `title`, `description`, `color`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'angka', 1, 'Pola Angka Level', 'Mengenal pola angka sederhana', '#d25a4b', NULL, 1, '2025-11-27 13:52:24', '2025-11-27 14:41:00'),
(2, 'huruf', 1, 'Pola Huruf Level ', 'Mengenal pola huruf sederhana', '#88b751', NULL, 1, '2025-11-27 13:52:24', '2025-11-27 14:41:10'),
(3, 'gambar', 1, 'Pola Gambar Level ', 'Mengenal pola gambar sederhana', '#efb739', NULL, 1, '2025-11-27 13:52:24', '2025-11-27 14:41:18'),
(4, 'kalender', 1, 'Pola Kalender Level ', 'Mengenal pola tanggal', '#f17cc9', NULL, 1, '2025-11-27 13:52:24', '2025-11-27 14:41:26'),
(6, 'angka', 2, 'Pola Angka Dasar', 'Latihan pola angka sederhana tingkat 2', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(7, 'angka', 3, 'Pola Angka Berganda', 'Pola angka dengan penggandaan dan lompatan', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(8, 'angka', 4, 'Pola Angka Campuran', 'Gabungan penjumlahan dan pengurangan', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(9, 'angka', 5, 'Pola Deret Meningkat', 'Deret yang meningkat secara teratur', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(10, 'angka', 6, 'Pola Deret Menurun', 'Penurunan angka dengan pola tertentu', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(11, 'angka', 7, 'Pola Angka Acak Teratur', 'Angka acak tapi berpola', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(12, 'angka', 8, 'Pola Aritmatika', 'Deret aritmatika dasar', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(13, 'angka', 9, 'Pola Geometri', 'Deret geometri tingkat awal', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(14, 'angka', 10, 'Pola Kombinasi Kompleks', 'Kombinasi aritmatika dan geometri untuk level lanjutan', NULL, NULL, 1, '2025-11-27 14:35:38', '2025-11-27 14:35:38'),
(15, 'huruf', 2, 'Lompatan Abjad', 'Pola huruf dengan lompatan satu atau dua huruf', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(16, 'huruf', 3, 'Abjad Mundur', 'Pola huruf dengan urutan terbalik', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(17, 'huruf', 4, 'Huruf Campuran', 'Pola huruf besar dan kecil secara bergantian', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(18, 'huruf', 5, 'Pola Vokal & Konsonan', 'Mengelompokkan huruf berdasarkan jenisnya', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(19, 'huruf', 6, 'Abjad Berulang', 'Pola huruf yang memiliki pengulangan tertentu', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(20, 'huruf', 7, 'Abjad Acak Berpola', 'Urutan acak tapi tetap mengikuti aturan', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(21, 'huruf', 8, 'Urutan Berganda', 'Dua pola berjalan bersamaan, misalnya A-C-E dan B-D-F', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(22, 'huruf', 9, 'Pola Kombinasi Huruf', 'Gabungan pola naik dan turun sekaligus', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(23, 'huruf', 10, 'Huruf Lanjutan', 'Level huruf dengan variasi pola yang lebih sulit', NULL, NULL, 1, '2025-11-27 14:38:01', '2025-11-27 14:38:01'),
(24, 'gambar', 2, 'Pola Warna Sederhana', 'Menentukan pola berdasarkan warna yang berulang', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(25, 'gambar', 3, 'Ukuran & Skala', 'Pola gambar berdasarkan perubahan ukuran', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(26, 'gambar', 4, 'Rotasi Bentuk', 'Menebak pola dari gambar yang diputar', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(27, 'gambar', 5, 'Pola Dua Dimensi', 'Level dengan variasi bentuk 2D', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(28, 'gambar', 6, 'Pengulangan Pola Visual', 'Mengenali pengulangan bentuk secara teratur', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(29, 'gambar', 7, 'Pola Warna Kombinasi', 'Warna berubah mengikuti pola tertentu', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(30, 'gambar', 8, 'Transformasi Gambar', 'Perubahan bentuk secara perlahan (transformasi)', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(31, 'gambar', 9, 'Pola Bentuk Kompleks', 'Gabungan perubahan warna, bentuk, dan rotasi', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(32, 'gambar', 10, 'Visual Tingkat Lanjut', 'Pola gambar paling menantang di kategori ini', NULL, NULL, 1, '2025-11-27 14:38:26', '2025-11-27 14:38:26'),
(33, 'kalender', 2, 'Lompatan Hari', 'Pola hari meloncat seperti Senin–Rabu–Jumat', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(34, 'kalender', 3, 'Pola Tanggal Dasar', 'Menentukan tanggal berikutnya dari pola sederhana', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(35, 'kalender', 4, 'Urutan Bulan', 'Pola Januari–Februari–Maret dan seterusnya', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(36, 'kalender', 5, 'Tanggal Kelipatan', 'Pola tanggal seperti 2–4–6 atau 5–10–15', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(37, 'kalender', 6, 'Hari Campuran', 'Pola hari maju dan mundur', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(38, 'kalender', 7, 'Pola Mingguan', 'Pola empat minggu dengan interval tertentu', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(39, 'kalender', 8, 'Pola Bulanan', 'Pola tanggal tiap awal/akhir bulan', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(40, 'kalender', 9, 'Pola Waktu Kompleks', 'Gabungan hari, tanggal, dan bulan', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46'),
(41, 'kalender', 10, 'Kalender Lanjutan', 'Level kalender paling kompleks dengan interval bervariasi', NULL, NULL, 1, '2025-11-27 14:38:46', '2025-11-27 14:38:46');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_image` varchar(255) DEFAULT NULL,
  `question_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `level_id`, `question_text`, `question_image`, `question_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lengkapi pola berikut: 2, 4, 6, 8, __', NULL, 1, '2025-11-27 14:54:56', '2025-11-27 14:54:56'),
(2, 1, 'Lengkapi pola berikut: 5, 10, 15, 20, __', NULL, 2, '2025-11-27 14:54:56', '2025-11-27 14:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@patternplay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-11-27 14:54:56'),
(2, 'gres', 'gres@patternplay.com', '$2y$10$5cA667lJoqpGslD6uCKqjeNikCMyCPnqAQ/PnyvYJFG9lxsnssNqe', 'user', '2025-11-27 14:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question` (`question_id`),
  ADD KEY `idx_correct` (`is_correct`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_category_level` (`category`,`level_number`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_level` (`level_id`),
  ADD KEY `idx_order` (`question_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `answer_id` (`answer_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_level` (`level_id`),
  ADD KEY `idx_completed` (`completed_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_progress_ibfk_4` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
