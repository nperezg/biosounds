-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 11, 2021 at 07:48 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biosounds`
--
CREATE DATABASE IF NOT EXISTS `biosounds` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `biosounds`;

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE `collection` (
  `collection_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 101,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `doi` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Citation in cientific format or full URL',
  `note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `view` enum('gallery','list') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'gallery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `collection`
--

INSERT INTO `collection` (`collection_id`, `project_id`, `name`, `author`, `doi`, `note`, `view`) VALUES
(1, 101, 'Demo collection', 'BioSounds', '', 'open access', 'gallery');

-- --------------------------------------------------------

--
-- Table structure for table `file_upload`
--

CREATE TABLE `file_upload` (
  `file_upload_id` int(11) NOT NULL,
  `path` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `filename` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `doi` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `license_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `recording_id` int(11) DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `directory` int(11) NOT NULL,
  `sensor_id` int(11) NOT NULL,
  `species_id` int(11) DEFAULT NULL,
  `sound_type_id` int(11) DEFAULT NULL,
  `subtype` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rating` enum('A','B','C','D','E') COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `error` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `license`
--

CREATE TABLE `license` (
  `license_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `license`
--

INSERT INTO `license` (`license_id`, `name`, `link`) VALUES
(1, 'Copyright', ''),
(2, 'CC0', 'https://creativecommons.org/publicdomain/zero/1.0/'),
(3, 'CC-BY', 'https://creativecommons.org/licenses/by/4.0'),
(4, 'CC-BY-SA', 'https://creativecommons.org/licenses/by-sa/4.0/'),
(5, 'CC-BY-NC', 'https://creativecommons.org/licenses/by-nc/4.0'),
(6, 'CC-BY-NC-SA', 'https://creativecommons.org/licenses/by-nc-sa/4.0'),
(7, 'CC-BY-ND', 'https://creativecommons.org/licenses/by-nd/4.0/'),
(8, 'CC-BY-NC-ND', 'https://creativecommons.org/licenses/by-nc-nd/4.0');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permission_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permission_id`, `name`) VALUES
(1, 'View'),
(2, 'Review'),
(3, 'Access');


-- --------------------------------------------------------

--
-- Table structure for table `play_log`
--

CREATE TABLE `play_log` (
  `play_log_id` int(11) NOT NULL,
  `recording_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `stop_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `open` int(1) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recording`
--

CREATE TABLE `recording` (
  `recording_id` int(11) NOT NULL,
  `col_id` int(11) NOT NULL,
  `directory` int(11) NOT NULL,
  `sensor_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `sound_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `md5_hash` char(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'MD5 hash of the file, to verify that the file has not been changed.',
  `file_date` date DEFAULT NULL,
  `file_time` time DEFAULT NULL,
  `license_id` int(11) NOT NULL DEFAULT 1,
  `DOI` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sampling_rate` int(11) NOT NULL DEFAULT 44100,
  `bitrate` int(11) NOT NULL DEFAULT 16,
  `channel_num` int(1) NOT NULL DEFAULT 1,
  `duration` float NOT NULL,
  `note` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `name`) VALUES
(1, 'Administrator'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `sensor`
--

CREATE TABLE `sensor` (
  `sensor_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `microphone` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `recorder` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sensor`
--

INSERT INTO `sensor` (`sensor_id`, `name`, `microphone`, `recorder`, `note`) VALUES
(1, 'None', '', NULL, ''),
(2, 'pre-2014 SMX-II (Wildlife acoustics)', 'WM61A (Panasonic)', 'SM2+', 'info according to WA, but several types were found'),
(3, 'SMX-U1 (Wildlife acoustics)', 'FG-23629-C36-1 (Knowles)', 'SM2Bat+', ''),
(4, 'SMX-US (Wildlife acoustics)', 'SPM0404UD5 (Knowles)', 'SM2Bat+', ''),
(5, 'BMX-U1 (Biotope.fr)', 'SPU0410LR5H-QB (Knowles)', 'SM2Bat+', ''),
(6, 'SMO1 (Sonitor Parus precursor)', 'SPU0410LR5H-QB (Knowles)', 'SM2Bat+', ''),
(7, 'Primo EM172', 'Primo EM172', 'Solo', ''),
(8, 'mixed', '', NULL, ''),
(9, 'Audiomoth 1.0, 1.1', 'SPM0408LE5H-TB (<6) (Knowles)', 'Audiomoth 1.0, 1.1', ''),
(10, 'Sennheiser ME66', '', NULL, ''),
(11, 'RØDE VideoMic', '', NULL, ''),
(12, 'Olympus LS-P4', '', 'Olympus LS-P4', ''),
(13, 'Sony Ericsson K600i', '', 'Sony Ericsson K600i', ''),
(14, 'Roland R05', '', 'Roland R05', ''),
(15, 'SM4 (internal acoustic)', '', 'SM4 (Wildlife Acoustics)', ''),
(16, 'post-2014 SMX-II (Wildlife acoustics)', 'unknown but similar to PUI mic', 'SM2+', ''),
(17, 'Audiomoth 1.2', 'SPM0408LE5H-TB (>5) (Knowles)', 'Audiomoth 1.2', ''),
(18, 'Petterson D240X', 'unspecified', 'Petterson D240X', '');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`name`, `value`) VALUES
('allow_upload', '0'),
('cores_to_use', '1'),
('default_qf', '0'),
('fft', '512'),
('filesLicense', 'Copyright'),
('filesLicenseDetail', 'Kevin Darras'),
('googleanalytics_ID', ''),
('googlemaps_key', ''),
('googlemaps3_key', ''),
('guests_can_dl', '0'),
('guests_can_open', '0'),
('hide_latlon_guests', '0'),
('map_only', '0'),
('projectDescription', ''),
('projectName', 'SoundEFForTS'),
('public_leveldata', '0'),
('sidetoside_comp', '1'),
('sox_version', '14.4.1'),
('spectrogram_palette', '1'),
('temp_add_dir', ''),
('use_chorus', '0'),
('use_googlemaps', '0'),
('use_tags', '0'),
('use_xml', '1'),
('wav_toflac', '1');

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE `site` (
  `site_id` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `elevation` double DEFAULT NULL,
  `country` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`site_id`, `name`, `note`, `latitude`, `longitude`, `elevation`, `country`) VALUES
(0, 'Demo site', NULL, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sound`
--

CREATE TABLE `sound` (
  `sound_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `sound_type_id` int(11) NOT NULL,
  `subtype` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `distance` int(4) DEFAULT NULL,
  `not_estimable_distance` tinyint(1) DEFAULT NULL,
  `individual_num` int(2) NOT NULL DEFAULT 1,
  `uncertain` tinyint(1) NOT NULL DEFAULT 0,
  `rating` enum('A','B','C','D','E') COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sound_type`
--

CREATE TABLE `sound_type` (
  `sound_type_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `taxon_class` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `taxon_order` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sound_type`
--

INSERT INTO `sound_type` (`sound_type_id`, `name`, `taxon_class`, `taxon_order`) VALUES
(1, '(Bird) Call - unspecific', 'AVES', ''),
(2, '(Bird) Song', 'AVES', ''),
(3, '(Bird) Non-vocal', 'AVES', ''),
(4, '(Bat) Searching', 'MAMMALIA', 'CHIROPTERA'),
(5, '(Bat) Feeding', 'MAMMALIA', 'CHIROPTERA'),
(6, '(Bat) Social', 'MAMMALIA', 'CHIROPTERA'),
(7, 'Unknown', '', ''),
(8, '(Bird) Call - contact', 'AVES', ''),
(9, '(Bird) Call - flight', 'AVES', ''),
(10, '(Bird) Call - begging', 'AVES', ''),
(11, '(Amphibia) Courtship', 'AMPHIBIA', ''),
(12, '(Amphibia) Advertisement towards males', 'AMPHIBIA', ''),
(13, '(Amphibia) Acquisition/defense of reproductive territories', 'AMPHIBIA', ''),
(14, '(Amphibia) Discouraging takeover attempts by other males during amplexus', 'AMPHIBIA', ''),
(15, '(Amphibia) defense of diurnal retreats not used for reproduction', 'AMPHIBIA', ''),
(16, '(Primate) Agonistic', 'MAMMALIA', 'PRIMATA'),
(17, '(Primate) Affiliative', 'MAMMALIA', 'PRIMATA'),
(18, '(Primate) Contact', 'MAMMALIA', 'PRIMATA'),
(20, '(Primate) Song', 'MAMMALIA', 'PRIMATA'),
(21, '(Primate) Advertisement - territory', 'MAMMALIA', 'PRIMATA'),
(22, '(Primate) Advertisement - mating', 'MAMMALIA', 'PRIMATA'),
(23, '(Primate) Foraging', 'MAMMALIA', 'PRIMATA'),
(24, '(Primate) Alarm', 'MAMMALIA', 'PRIMATA'),
(25, '(Primate) Begging', 'MAMMALIA', 'PRIMATA'),
(26, '(Primate) Adult - offspring', 'MAMMALIA', 'PRIMATA');

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

CREATE TABLE `species` (
  `species_id` int(11) NOT NULL,
  `binomial` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `genus` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `family` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `taxon_order` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `common_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  `region` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `species`
--

INSERT INTO `species` (`species_id`, `binomial`, `genus`, `family`, `taxon_order`, `class`, `common_name`, `level`, `region`) VALUES
(1, 'Test species', 'Test Genus', 'Test Family', 'Test Order', 'Test Class', 'Test common name', 1, 'Test region');

-- --------------------------------------------------------

--
-- Table structure for table `spectrogram`
--

CREATE TABLE `spectrogram` (
  `spectrogram_id` int(11) NOT NULL,
  `recording_id` int(11) NOT NULL,
  `filename` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('spectrogram','waveform','spectrogram-small','waveform-small','spectrogram-large','waveform-large','spectrogram-player') COLLATE utf8_unicode_ci NOT NULL,
  `max_frequency` int(11) DEFAULT NULL,
  `fft` int(11) NOT NULL DEFAULT 1024
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `tag_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `recording_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `min_time` float NOT NULL,
  `max_time` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `min_freq` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `max_freq` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `uncertain` tinyint(1) NOT NULL COMMENT 'data lost before 18.10.2015',
  `call_distance_m` int(4) DEFAULT NULL,
  `distance_not_estimable` tinyint(1) DEFAULT NULL,
  `number_of_individuals` int(2) NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `reference_call` tinyint(1) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `comments` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_review`
--

CREATE TABLE `tag_review` (
  `tag_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tag_review_status_id` int(11) NOT NULL,
  `species_id` int(11) DEFAULT NULL,
  `note` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tag_review_status`
--

CREATE TABLE `tag_review_status` (
  `tag_review_status_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tag_review_status`
--

INSERT INTO `tag_review_status` (`tag_review_status_id`, `name`) VALUES
(1, 'Accepted'),
(2, 'Corrected'),
(3, 'Deleted'),
(4, 'Uncertain');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 101,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `role_id`, `project_id`, `username`, `password`, `name`, `email`, `color`, `active`) VALUES
(100, 1, 101, 'admin', 'JDJ5JDEwJHguRG9TQmZ5dmtiRTRPUEkxRlRKR3VRMTFXUmVNZWVDZkRDcy5QTDRSdENiMWpMNVF6TlMu', 'Administrator', 'admin@biosounds.admin', '#bd2929', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--

CREATE TABLE `user_permission` (
  `user_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`collection_id`);

--
-- Indexes for table `file_upload`
--
ALTER TABLE `file_upload`
  ADD PRIMARY KEY (`file_upload_id`);

--
-- Indexes for table `license`
--
ALTER TABLE `license`
  ADD PRIMARY KEY (`license_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `play_log`
--
ALTER TABLE `play_log`
  ADD PRIMARY KEY (`play_log_id`) USING BTREE,
  ADD KEY `recording_id` (`recording_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `recording`
--
ALTER TABLE `recording`
  ADD PRIMARY KEY (`recording_id`),
  ADD KEY `site_id_idx` (`site_id`) USING BTREE,
  ADD KEY `col_id_idx` (`col_id`) USING BTREE,
  ADD KEY `sensor_id_idx` (`sensor_id`) USING BTREE,
  ADD KEY `sound_id_idx` (`sound_id`) USING BTREE;

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `ID` (`role_id`);

--
-- Indexes for table `sensor`
--
ALTER TABLE `sensor`
  ADD PRIMARY KEY (`sensor_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`site_id`);

--
-- Indexes for table `sound`
--
ALTER TABLE `sound`
  ADD PRIMARY KEY (`sound_id`),
  ADD KEY `species_id_idx` (`species_id`) USING BTREE,
  ADD KEY `sound_type_id_idx` (`sound_type_id`) USING BTREE;

--
-- Indexes for table `sound_type`
--
ALTER TABLE `sound_type`
  ADD PRIMARY KEY (`sound_type_id`);

--
-- Indexes for table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`species_id`);

--
-- Indexes for table `spectrogram`
--
ALTER TABLE `spectrogram`
  ADD PRIMARY KEY (`spectrogram_id`),
  ADD KEY `recording_id_idx` (`recording_id`) USING BTREE;

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`tag_id`),
  ADD KEY `species_id_idx` (`species_id`) USING BTREE,
  ADD KEY `user_id` (`user_id`) USING BTREE,
  ADD KEY `recording_id_idx` (`recording_id`);

--
-- Indexes for table `tag_review`
--
ALTER TABLE `tag_review`
  ADD PRIMARY KEY (`tag_id`,`user_id`),
  ADD KEY `tag_review_status_id_idx` (`tag_review_status_id`) USING BTREE,
  ADD KEY `tag_review_user_id_idx` (`user_id`) USING BTREE,
  ADD KEY `tag_review_species_id_idx` (`species_id`) USING BTREE;

--
-- Indexes for table `tag_review_status`
--
ALTER TABLE `tag_review_status`
  ADD PRIMARY KEY (`tag_review_status_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD PRIMARY KEY (`user_id`,`collection_id`),
  ADD KEY `permission` (`permission_id`),
  ADD KEY `collection` (`collection_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_upload`
--
ALTER TABLE `file_upload`
  MODIFY `file_upload_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `license`
--
ALTER TABLE `license`
  MODIFY `license_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `play_log`
--
ALTER TABLE `play_log`
  MODIFY `play_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recording`
--
ALTER TABLE `recording`
  MODIFY `recording_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sensor`
--
ALTER TABLE `sensor`
  MODIFY `sensor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site`
--
ALTER TABLE `site`
  MODIFY `site_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sound`
--
ALTER TABLE `sound`
  MODIFY `sound_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sound_type`
--
ALTER TABLE `sound_type`
  MODIFY `sound_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spectrogram`
--
ALTER TABLE `spectrogram`
  MODIFY `spectrogram_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tag_review_status`
--
ALTER TABLE `tag_review_status`
  MODIFY `tag_review_status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `play_log`
--
ALTER TABLE `play_log`
  ADD CONSTRAINT `recording_id_fk` FOREIGN KEY (`recording_id`) REFERENCES `recording` (`recording_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recording`
--
ALTER TABLE `recording`
  ADD CONSTRAINT `col_id_fk` FOREIGN KEY (`col_id`) REFERENCES `collection` (`collection_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sensor_id_fk` FOREIGN KEY (`sensor_id`) REFERENCES `sensor` (`sensor_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sound_id_fk` FOREIGN KEY (`sound_id`) REFERENCES `sound` (`sound_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sound`
--
ALTER TABLE `sound`
  ADD CONSTRAINT `sound_type_id_fk` FOREIGN KEY (`sound_type_id`) REFERENCES `sound_type` (`sound_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `species_id_fk` FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`) ON UPDATE CASCADE;

--
-- Constraints for table `spectrogram`
--
ALTER TABLE `spectrogram`
  ADD CONSTRAINT `image_recording_id_fk` FOREIGN KEY (`recording_id`) REFERENCES `recording` (`recording_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `tag_recording_id_fk` FOREIGN KEY (`recording_id`) REFERENCES `recording` (`recording_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag_species_id_fk` FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tag_review`
--
ALTER TABLE `tag_review`
  ADD CONSTRAINT `tag_review_species_id_fk` FOREIGN KEY (`species_id`) REFERENCES `species` (`species_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag_review_status_id_fk` FOREIGN KEY (`tag_review_status_id`) REFERENCES `tag_review_status` (`tag_review_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag_review_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tag_review_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_permission_ibfk_3` FOREIGN KEY (`collection_id`) REFERENCES `collection` (`collection_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
