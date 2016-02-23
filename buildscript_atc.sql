-- phpMyAdmin SQL Dump
-- version 4.1.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 24, 2016 at 02:32 AM
-- Server version: 5.1.67-andiunpam
-- PHP Version: 5.6.4

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

CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `location_id` int(11) unsigned NOT NULL,
  `activity_type_id` int(11) unsigned NOT NULL,
  `dress_code` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `personnel_id` (`personnel_id`),
  KEY `location_id` (`location_id`),
  KEY `activity_type_id` (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `personnel_id`, `startdate`, `enddate`, `title`, `location_id`, `activity_type_id`, `dress_code`) VALUES
(0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0),
(1, 1, '2016-02-21 00:00:00', '2016-02-14 13:00:00', 'Waikanae Carnival', 2, 2, 0),
(4, 1, '2016-12-31 12:59:00', '2016-12-31 01:00:00', 'Waikanae show', 1, 2, 0),
(5, 1, '2016-12-31 12:59:00', '2016-12-31 12:58:00', 'Shoot', 1, 1, 0),
(6, 1, '2016-02-22 17:36:00', '2016-03-22 17:36:00', 'Gliding', 3, 3, 1),
(9, 1, '2016-03-01 07:00:00', '2016-03-01 15:00:00', 'test', 1, 6, 0),
(10, 1, '2016-04-01 07:00:00', '2016-05-01 09:00:00', 'testing2', 1, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `activity_register`
--

CREATE TABLE IF NOT EXISTS `activity_register` (
  `personnel_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `presence` tinyint(4) unsigned DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`personnel_id`,`activity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `activity_type`
--

CREATE TABLE IF NOT EXISTS `activity_type` (
  `activity_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `nzcf_status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `activity_type`
--

INSERT INTO `activity_type` (`activity_type_id`, `type`, `nzcf_status`) VALUES
(0, '', 0),
(1, 'Shoot', 0),
(2, 'Profile raising', 1),
(3, 'Flying', 0),
(6, 'Testing', 0);

-- --------------------------------------------------------

--
-- Table structure for table `activity_type_document`
--

CREATE TABLE IF NOT EXISTS `activity_type_document` (
  `activity_type_id` int(11) unsigned NOT NULL,
  `document_id` int(11) unsigned NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`activity_type_id`,`document_id`),
  KEY `document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE IF NOT EXISTS `attendance` (
  `date` date NOT NULL,
  PRIMARY KEY (`date`)
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

CREATE TABLE IF NOT EXISTS `attendance_register` (
  `personnel_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `presence` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`personnel_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attendance_register`
--

INSERT INTO `attendance_register` (`personnel_id`, `date`, `time_in`, `time_out`, `presence`) VALUES
(1, '2016-02-03', NULL, NULL, 0),
(1, '2016-02-10', NULL, NULL, 0),
(1, '2016-02-17', NULL, NULL, 0),
(3, '2016-02-03', NULL, NULL, 0),
(3, '2016-02-10', NULL, NULL, 0),
(3, '2016-02-17', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE IF NOT EXISTS `document` (
  `document_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nzcf_code` varchar(10) NOT NULL,
  `days_valid` smallint(5) unsigned NOT NULL DEFAULT '1',
  `version` smallint(5) unsigned NOT NULL,
  `version_date` date NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`document_id`),
  UNIQUE KEY `nzcf_code` (`nzcf_code`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `loc_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `name`, `address`) VALUES
(0, '', NULL),
(1, 'No 49 Squadron Unit HQ', ''),
(2, 'Waikanae Showground', ''),
(3, 'Matamata', '');

-- --------------------------------------------------------

--
-- Table structure for table `location_document`
--

CREATE TABLE IF NOT EXISTS `location_document` (
  `location_document_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(11) unsigned NOT NULL,
  `document_id` int(11) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`location_document_id`),
  UNIQUE KEY `location_id` (`location_id`,`document_id`,`valid_from`),
  KEY `document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_changes`
--

CREATE TABLE IF NOT EXISTS `log_changes` (
  `personnel_id` int(11) NOT NULL COMMENT 'User who performed update',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `table_updated` varchar(255) NOT NULL COMMENT 'What table was affected',
  `sql_executed` text NOT NULL COMMENT 'What was the SQL that was run',
  `row_updated` int(11) unsigned NOT NULL,
  KEY `personnel_id_idx` (`personnel_id`),
  KEY `table_updated_idx` (`table_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log of changes';

--
-- Dumping data for table `log_changes`
--

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

CREATE TABLE IF NOT EXISTS `personnel` (
  `personnel_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `is_female` tinyint(1) NOT NULL COMMENT 'User self identifies as',
  `email` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `access_rights` mediumint(5) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `joined_date` date NOT NULL,
  `left_date` date DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`personnel_id`),
  UNIQUE KEY `uniq_email` (`email`),
  KEY `email` (`email`,`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_id`, `firstname`, `lastname`, `is_female`, `email`, `dob`, `password`, `access_rights`, `created`, `joined_date`, `left_date`, `enabled`) VALUES
(0, '', '', 0, '', '0000-00-00', '', 0, '2016-02-23 04:36:48', '0000-00-00', NULL, -1),
(1, 'Phil', 'Tanner', 0, 'phil.tanner@49squadron.org.nz', '1975-09-17', 'sha256:1000:/8sO+9s6F7hZoRCW6CF7lyvtGU/aUGb2:E2Dxpue7Jh0qv1oL9JAfQTrE17aqjsL4', 59647, '2016-02-05 07:32:51', '2015-05-01', NULL, -1);

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