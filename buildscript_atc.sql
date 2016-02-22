-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2016 at 12:14 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `atc`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `activity_id` int(11) UNSIGNED NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `location_id` int(11) UNSIGNED NOT NULL,
  `activity_type_id` int(11) UNSIGNED NOT NULL,
  `dress_code` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `personnel_id`, `startdate`, `enddate`, `title`, `location_id`, `activity_type_id`, `dress_code`) VALUES
(1, 1, '2016-02-21 00:00:00', '2016-02-14 13:00:00', 'Waikanae Carnival', 2, 2, 0),
(4, 1, '2016-12-31 12:59:00', '2016-12-31 01:00:00', 'Waikanae show', 1, 2, 0),
(5, 1, '2016-12-31 12:59:00', '2016-12-31 12:58:00', 'Shoot', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `activity_type`
--

CREATE TABLE `activity_type` (
  `activity_type_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `nzcf_status` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activity_type`
--

INSERT INTO `activity_type` (`activity_type_id`, `type`, `nzcf_status`) VALUES
(1, 'Shoot', 0),
(2, 'Profile raising', 1);

-- --------------------------------------------------------

--
-- Table structure for table `activity_type_document`
--

CREATE TABLE `activity_type_document` (
  `activity_type_id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `required` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`date`) VALUES
('2016-02-03'),
('2016-02-10'),
('2016-02-17'),
('2016-02-24'),
('2016-03-02'),
('2016-03-09'),
('2016-03-16'),
('2016-03-23');

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

--
-- Dumping data for table `attendance_register`
--

INSERT INTO `attendance_register` (`personnel_id`, `date`, `time_in`, `time_out`, `presence`) VALUES
(1, '2016-02-03', NULL, NULL, 0),
(1, '2016-02-10', NULL, NULL, 0),
(1, '2016-02-17', NULL, NULL, 1),
(22, '2016-02-03', NULL, NULL, 0),
(22, '2016-02-10', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(10) UNSIGNED NOT NULL,
  `nzcf_code` varchar(10) NOT NULL,
  `days_valid` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `version` smallint(5) UNSIGNED NOT NULL,
  `version_date` date NOT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `name`, `address`) VALUES
(1, 'No 49 Squadron Unit HQ', NULL),
(2, 'Waikanae Showground', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `location_document`
--

CREATE TABLE `location_document` (
  `location_document_id` int(11) UNSIGNED NOT NULL,
  `location_id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `filename` varchar(255) NOT NULL
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
  `row_updated` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log of changes';

--
-- Dumping data for table `log_changes`
--

INSERT INTO `log_changes` (`personnel_id`, `timestamp`, `table_updated`, `sql_executed`, `row_updated`) VALUES
(1, '2016-02-07 22:40:42', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-07 23:41:56', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Sue&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;treasurer@49squadron.org.nz&quot;, `dob` = &quot;1966-06-09&quot;, `joined_date` = &quot;2015-04-01&quot;, `left_date` = NULL, `access_rights` = 2261, `enabled` = 0, `is_female` = -1 WHERE personnel_id = 4 LIMIT 1;', 0),
(1, '2016-02-07 23:42:08', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Sue&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;treasurer@49squadron.org.nz&quot;, `dob` = &quot;1966-06-09&quot;, `joined_date` = &quot;2015-04-01&quot;, `left_date` = NULL, `access_rights` = 2261, `enabled` = -1, `is_female` = -1 WHERE personnel_id = 4 LIMIT 1;', 0),
(1, '2016-02-07 23:50:40', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = 0, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-07 23:52:47', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:24:15', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:25:09', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:25:54', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:30:05', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:30:37', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:31:53', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:35:09', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:38:11', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:38:16', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:38:17', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:38:21', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 00:47:29', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 01:03:09', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 01:09:52', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 01:24:28', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 01:29:18', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:18:39', 'personnel', '"UPDATE `personnel` SET `firstname` = "Phil", `lastname` = "Tanner", `email` = "phil.tanner@49squadron.org.nz", `dob` = "1975-09-17", `joined_date` = "2015-05-01", `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;"', 0),
(1, '2016-02-08 02:19:57', 'personnel', '"UPDATE `personnel` SET `firstname` = "Phil", `lastname` = "Tanner", `email` = "phil.tanner@49squadron.org.nz", `dob` = "1975-09-17", `joined_date` = "2015-05-01", `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;"', 0),
(1, '2016-02-08 02:22:05', 'personnel', '"UPDATE `personnel` SET `firstname` = "Phil", `lastname` = "Tanner", `email` = "phil.tanner@49squadron.org.nz", `dob` = "1975-09-17", `joined_date` = "2015-05-01", `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;"', 0),
(1, '2016-02-08 02:22:41', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;O''Leary&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:24:48', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;O''Leary&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:30:15', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:31:47', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:32:31', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;&amp;quot;Tanner&amp;quot;&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:33:52', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:38:18', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Tanner&quot;, `email` = &quot;phil.tanner@49squadron.org.nz&quot;, `dob` = &quot;1975-09-17&quot;, `joined_date` = &quot;2015-05-01&quot;, `left_date` = NULL, `access_rights` = 16777215, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 1 LIMIT 1;', 0),
(1, '2016-02-08 02:45:14', 'personnel', 'INSERT INTO `personnel` (`firstname`, `lastname`, `email`, `dob`, `password`, `joined_date`, `left_date`, `access_rights`, `is_female`, `enabled` ) VALUES ( &quot;Phil&quot;, &quot;Baker&quot;, &quot;cucdr@49squadron.org.nz&quot;, &quot;2016-01-01&quot;, &quot;sha256:1000:pAXirFBFeMXm/H+iqD2uMdGn9l1tWB9H:Hn1y+PkEflH/4bCMIIjkg4t+Rvczt4D9&quot;, &quot;2016-01-01&quot;, NULL, 2135, 0, -1 );', 0),
(1, '2016-02-08 03:49:52', 'personnel', 'UPDATE `personnel` SET `firstname` = &quot;Phil&quot;, `lastname` = &quot;Baker&quot;, `email` = &quot;cucdr@49squadron.org.nz&quot;, `dob` = &quot;2016-01-01&quot;, `joined_date` = &quot;2016-01-01&quot;, `left_date` = NULL, `access_rights` = 2135, `enabled` = -1, `is_female` = 0 WHERE personnel_id = 22 LIMIT 1;', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:09:57', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:02', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 2) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:12', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:19', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 22, ''2016-02-24'', 1) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 2) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:10:49', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 22, ''2016-02-24'', 1) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 2) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 1) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:11:21', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 22, ''2016-02-24'', 1) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-24'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:11:35', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 22, ''2016-02-24'', 1) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-03'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-24'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:16:46', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:17:27', 'attendance', 'INSERT INTO `attendance` (`date` ) VALUES ( ''2016-01-27'' );', 0),
(1, '2016-02-12 08:18:13', 'attendance', 'INSERT INTO `attendance` (`date` ) VALUES ( ''2016-02-10'' );', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-01-27'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 22, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 22, ''2016-02-10'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 22 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-01-27'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-10'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-17'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:18:47', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-03'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-10'', 0) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( 1, ''2016-02-17'', 1) ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-02-24'';', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-02'';', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-09'';', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-16'';', 0),
(1, '2016-02-12 08:33:30', 'attendance_register', 'DELETE FROM `attendance_register` WHERE `personnel_id` = 1 AND `date` = ''2016-03-23'';', 0),
(1, '2016-02-13 09:47:47', 'activity', '\r\n				INSERT INTO `activities` (\r\n					`startdate`,\r\n					`enddate`,\r\n					`personnel_id`,\r\n					`title`,\r\n					`location`,\r\n					`status` \r\n				) VALUES ( \r\n					''2016-12-31 12:59'',\r\n					''2016-12-31 01:00'',\r\n					1,\r\n					''Waikanae show'', \r\n					''Waikanae showground'',\r\n					0\r\n				);', 4),
(1, '2016-02-13 09:48:12', 'activity', '\r\n				INSERT INTO `activities` (\r\n					`startdate`,\r\n					`enddate`,\r\n					`personnel_id`,\r\n					`title`,\r\n					`location`,\r\n					`status` \r\n				) VALUES ( \r\n					''2016-12-31 12:59'',\r\n					''2016-12-31 12:58'',\r\n					1,\r\n					''Shoot'', \r\n					''Levin'',\r\n					1\r\n				);', 5);

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
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `personnel_id` (`personnel_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `activity_type_id` (`activity_type_id`);

--
-- Indexes for table `activity_type`
--
ALTER TABLE `activity_type`
  ADD PRIMARY KEY (`activity_type_id`);

--
-- Indexes for table `activity_type_document`
--
ALTER TABLE `activity_type_document`
  ADD PRIMARY KEY (`activity_type_id`,`document_id`),
  ADD KEY `document_id` (`document_id`);

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
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`),
  ADD UNIQUE KEY `nzcf_code` (`nzcf_code`,`version`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `loc_name` (`name`);

--
-- Indexes for table `location_document`
--
ALTER TABLE `location_document`
  ADD PRIMARY KEY (`location_document_id`),
  ADD UNIQUE KEY `location_id` (`location_id`,`document_id`,`valid_from`),
  ADD KEY `document_id` (`document_id`);

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
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `activity_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `activity_type`
--
ALTER TABLE `activity_type`
  MODIFY `activity_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `location_document`
--
ALTER TABLE `location_document`
  MODIFY `location_document_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `personnel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_ibfk_3` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_type` (`activity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `activity_type_document`
--
ALTER TABLE `activity_type_document`
  ADD CONSTRAINT `activity_type_document_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_type_document_ibfk_2` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_type` (`activity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `attendance_register`
--
ALTER TABLE `attendance_register`
  ADD CONSTRAINT `attendance_register_ibfk_1` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendance_register_ibfk_2` FOREIGN KEY (`date`) REFERENCES `attendance` (`date`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `location_document`
--
ALTER TABLE `location_document`
  ADD CONSTRAINT `location_document_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `location_document_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `document` (`document_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`location` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`activity-type` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`activity` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`activity_type_document` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`document` TO 'atc'@'localhost';
GRANT SELECT, INSERT, UPDATE, REFERENCES ON `atc`.`location_document` TO 'atc'@'localhost';