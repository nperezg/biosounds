-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 13, 2020 at 09:40 PM
-- Server version: 5.7.29-0ubuntu0.16.04.1
-- PHP Version: 7.1.16-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biosounds`
--

--
-- Dumping data for table `role`
--

INSERT INTO `collection` (`collection_id`, `name`, `author`) VALUES
(1, 'Demo Collection', 'BioSounds');

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `name`) VALUES
(1, 'Administrator'),
(2, 'User');

--
-- Dumping data for table `sensor`
--

INSERT INTO `sensor` (`sensor_id`, `name`, `microphone`, `note`) VALUES
(1, 'None', '', ''),
(2, 'SMX-II (Wildlife acoustics)', '', ''),
(3, 'SMX-U1 (Wildlife acoustics)', '', ''),
(4, 'SMX-US (Wildlife acoustics)', '', ''),
(5, 'BMX-U1 (Biotope.fr)', '', ''),
(6, 'Primo EM172', '', ''),
(7, 'mixed', '', ''),
(8, 'Audiomoth 1.0', '', '');

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`name`, `value`) VALUES
('fft', '1024'),
('filesLicense', ''),
('filesLicenseDetail', ''),
('projectDescription', ''),
('projectName', '');

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`site_id`, `name`, `note`, `latitude`, `longitude`, `elevation`) VALUES
(1, 'None', NULL, NULL, NULL, NULL);

--
-- Dumping data for table `sound_type`
--

INSERT INTO `sound_type` (`sound_type_id`, `name`) VALUES
(1, 'Call'),
(2, 'Song'),
(3, 'Non-vocal'),
(4, 'Searching (bat)'),
(5, 'Feeding (bat)'),
(6, 'Social (bat)'),
(7, 'Unknown');

--
-- Dumping data for table `species`
--

INSERT INTO `species` (`species_id`, `binomial`, `genus`, `family`, `taxon_order`, `class`, `common_name`, `level`, `region`) VALUES
(0, 'binomial', 'genus', 'family', 'taxon_order', 'class', 'common_name', 0, 'region'),
(1, 'test tag', '', '', '', '', 'fill in your species table', 0, 'none');

--
-- Dumping data for table `tag_review_status`
--

INSERT INTO `tag_review_status` (`tag_review_status_id`, `name`) VALUES
(1, 'Accepted'),
(2, 'Corrected'),
(3, 'Deleted'),
(4, 'Uncertain');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `role_id`, `username`, `password`, `name`, `email`, `color`, `active`) VALUES
(1, 1, 'admin', 'JDJ5JDEwJHguRG9TQmZ5dmtiRTRPUEkxRlRKR3VRMTFXUmVNZWVDZkRDcy5QTDRSdENiMWpMNVF6TlMu', 'Administrator', 'admin@biosounds.admin', '#4cc4c9', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
