-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 06:25 PM
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
-- Database: `my_portofolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama_username` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama_username`, `kelas`, `umur`, `password`) VALUES
(1, 'admin_test', 'Administrator', 25, '$2y$10$n7nFAILTmnj9CSXhpPk/q.yzwI4iboHIHmbmI9cwQ82DQDgK/sY2e'),
(2, 'Roysihan', 'Administrator', 23, '$2y$10$BNbXIrfu88//whqJhpa46ulnajBeVViNShBQjsr0TZEZsMLH9KdI.'),
(3, 'roysihan208@gmail.com', 'administrator', 23, '$2y$10$cDUI04z38qViB/uTm5A4WuS.0xu3A.WVaj/59zj4Za8SS1T/GKhZC');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL,
  `name_certificate` varchar(255) DEFAULT NULL,
  `issuer` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `image_certificate` varchar(255) DEFAULT NULL,
  `link_certificate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certifications`
--

INSERT INTO `certifications` (`id`, `name_certificate`, `issuer`, `issue_date`, `image_certificate`, `link_certificate`) VALUES
(1, 'Awas', 'dad', '2025-11-26', '1765596230_cert_CV_Roysihan_Resume_2025.pdf', '');

-- --------------------------------------------------------

--
-- Table structure for table `dashboard`
--

CREATE TABLE `dashboard` (
  `id` int(11) NOT NULL,
  `carrousel_image` text DEFAULT NULL,
  `carrousel_teks` text DEFAULT NULL,
  `photo_profil` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dashboard`
--

INSERT INTO `dashboard` (`id`, `carrousel_image`, `carrousel_teks`, `photo_profil`) VALUES
(3, NULL, 'Welcome to my portfolio dashboard.', '1765031597_profile_IMG_8541OK.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `design_page`
--

CREATE TABLE `design_page` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `design_image` text DEFAULT NULL,
  `design_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `design_page`
--

INSERT INTO `design_page` (`id`, `title`, `design_image`, `design_link`) VALUES
(5, 'Mobil', '1765558105_design_0_land-o-lakes-inc-iVINr8-ZFmY-unsplash.jpg,1765558105_design_1_Olshop - Instagram Post.png', 'https://youtu.be/odHHpbOw--A'),
(6, 'Ferrari', '1765558166_design_0_eat-sleep-code-repeat-black-background-programmer-quotes-3840x2160-5947.png,1765558180_design_0_Olshop - Landing Page.png', 'https://youtu.be/odHHpbOw--A');

-- --------------------------------------------------------

--
-- Table structure for table `education_history`
--

CREATE TABLE `education_history` (
  `id` int(11) NOT NULL,
  `name_education` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_activity` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `still_studying` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education_history`
--

INSERT INTO `education_history` (`id`, `name_education`, `description`, `image_activity`, `start_date`, `end_date`, `still_studying`) VALUES
(1, 'University esa unggul', 'dwada', '1765596182_edu_0_Sertifikat 1.jpg,1765596182_edu_1_Peta_Kepulauan_Riau_-_Penyengat.png', '2025-10-09', '2026-01-15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `home_carousel`
--

CREATE TABLE `home_carousel` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_page`
--

CREATE TABLE `project_page` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `project_image` varchar(255) DEFAULT NULL,
  `tool_logo` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `project_link_github` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_page`
--

INSERT INTO `project_page` (`id`, `title`, `project_image`, `tool_logo`, `description`, `project_link_github`) VALUES
(1, 'Test', '1765555438_wallace-henry--r5wlBxk9NA-unsplash.jpg', '1765555438_tool_0_land-o-lakes-inc-iVINr8-ZFmY-unsplash.jpg', 'Testing', 'https://github.com/Roysintax/ruang_belajar.git');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `skill_name` varchar(255) DEFAULT NULL,
  `skill_category` varchar(100) DEFAULT NULL,
  `skill_level` int(11) DEFAULT 80,
  `skill_icon` varchar(255) DEFAULT NULL,
  `icon_size` int(11) DEFAULT 100,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `skill_name`, `skill_category`, `skill_level`, `skill_icon`, `icon_size`, `display_order`) VALUES
(1, 'Javascript', 'Framework', 90, '1765600952_skill_200px.png', 200, 7400);

-- --------------------------------------------------------

--
-- Table structure for table `work_experience_page`
--

CREATE TABLE `work_experience_page` (
  `id` int(11) NOT NULL,
  `image_activity_work_carrousel` text DEFAULT NULL,
  `name_company` varchar(255) DEFAULT NULL,
  `date_work_start` date DEFAULT NULL,
  `date_work_end` date DEFAULT NULL,
  `still_working` tinyint(1) DEFAULT 0,
  `work_status` enum('magang','kerja') DEFAULT 'kerja',
  `activity_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_experience_page`
--

INSERT INTO `work_experience_page` (`id`, `image_activity_work_carrousel`, `name_company`, `date_work_start`, `date_work_end`, `still_working`, `work_status`, `activity_description`) VALUES
(2, '1765551677_exp_0_wallace-henry--r5wlBxk9NA-unsplash.jpg,1765551677_exp_1_land-o-lakes-inc-iVINr8-ZFmY-unsplash.jpg', 'Universitas Esa Unggul', '2025-11-12', NULL, 1, 'magang', 'halo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_username` (`nama_username`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dashboard`
--
ALTER TABLE `dashboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `design_page`
--
ALTER TABLE `design_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education_history`
--
ALTER TABLE `education_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_carousel`
--
ALTER TABLE `home_carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_page`
--
ALTER TABLE `project_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `work_experience_page`
--
ALTER TABLE `work_experience_page`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dashboard`
--
ALTER TABLE `dashboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `design_page`
--
ALTER TABLE `design_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `education_history`
--
ALTER TABLE `education_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_carousel`
--
ALTER TABLE `home_carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_page`
--
ALTER TABLE `project_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `work_experience_page`
--
ALTER TABLE `work_experience_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
