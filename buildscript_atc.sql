-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2016 at 10:11 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atc`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) UNSIGNED NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL COMMENT 'NZCF Recognised/Authorised'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_register`
--

CREATE TABLE `attendance_register` (
  `personnel_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `presence` tinyint(4) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `log_changes`
--

CREATE TABLE `log_changes` (
  `personnel_id` int(11) NOT NULL COMMENT 'User who performed update',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `table_updated` varchar(255) NOT NULL COMMENT 'What table was affected',
  `sql_executed` text NOT NULL COMMENT 'What was the SQL that was run',
  `row_updated` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log of changes';

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE `personnel` (
  `personnel_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `is_female` tinyint(1) NOT NULL COMMENT 'User self identifies as',
  `email` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `access_rights` mediumint(5) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `joined_date` date NOT NULL,
  `left_date` date DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_id`, `firstname`, `lastname`, `is_female`, `email`, `dob`, `password`, `access_rights`, `created`, `joined_date`, `left_date`, `enabled`) VALUES
(1, 'Phil', 'Tanner', 0, 'phil.tanner@49squadron.org.nz', '1975-09-17', 'sha256:1000:/8sO+9s6F7hZoRCW6CF7lyvtGU/aUGb2:E2Dxpue7Jh0qv1oL9JAfQTrE17aqjsL4', 100421, '2016-02-05 07:32:51', '2015-05-01', NULL, -1),
(4, 'Sue', 'Tanner', -1, 'treasurer@49squadron.org.nz', '1966-06-09', 'sha256:1000:s52pl7UwRRa7Dk0wZ0+2zoo9tYFRJaax:bqeN5zaNm1lRazugRrJkDlaQ7CuB6bO4', 2261, '2016-02-06 06:38:40', '2015-04-01', NULL, -1),
(22, 'Phil', 'Baker', 0, 'cucdr@49squadron.org.nz', '2016-01-01', 'sha256:1000:pAXirFBFeMXm/H+iqD2uMdGn9l1tWB9H:Hn1y+PkEflH/4bCMIIjkg4t+Rvczt4D9', 2135, '2016-02-08 02:45:14', '2016-01-01', NULL, -1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `personnel_id` (`personnel_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`date`);

--
-- Indexes for table `attendance_register`
--
ALTER TABLE `attendance_register`
  ADD PRIMARY KEY (`personnel_id`,`date`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `log_changes`
--
ALTER TABLE `log_changes`
  ADD KEY `personnel_id_idx` (`personnel_id`),
  ADD KEY `table_updated_idx` (`table_updated`);

--
-- Indexes for table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`personnel_id`),
  ADD UNIQUE KEY `uniq_email` (`email`),
  ADD KEY `email` (`email`,`password`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `personnel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `attendance_register`
--
ALTER TABLE `attendance_register`
  ADD CONSTRAINT `attendance_register_ibfk_1` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendance_register_ibfk_2` FOREIGN KEY (`date`) REFERENCES `attendance` (`date`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `log_changes`
--
ALTER TABLE `log_changes`
  ADD CONSTRAINT `log_changes_ibfk_1` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE USER 'atc'@'localhost' IDENTIFIED BY 'ZIERIESs5ESa';
GRANT SELECT, INSERT ON `atc`.`log_changes` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES ON `atc`.`attendance` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES ON `atc`.`attendance_register` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`personnel` TO 'atc'@'localhost';