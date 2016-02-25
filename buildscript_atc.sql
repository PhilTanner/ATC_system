-- phpMyAdmin SQL Dump
-- version 4.1.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 26, 2016 at 08:43 AM
-- Server version: 5.1.67-andiunpam
-- PHP Version: 5.6.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
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

DROP TABLE IF EXISTS `activity`;
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

-- --------------------------------------------------------

--
-- Table structure for table `activity_register`
--

DROP TABLE IF EXISTS `activity_register`;
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

DROP TABLE IF EXISTS `activity_type`;
CREATE TABLE IF NOT EXISTS `activity_type` (
  `activity_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `nzcf_status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_type_document`
--

DROP TABLE IF EXISTS `activity_type_document`;
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

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `date` date NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_register`
--

DROP TABLE IF EXISTS `attendance_register`;
CREATE TABLE IF NOT EXISTS `attendance_register` (
  `personnel_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `presence` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`personnel_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
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

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `loc_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `location_document`
--

DROP TABLE IF EXISTS `location_document`;
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

DROP TABLE IF EXISTS `log_changes`;
CREATE TABLE IF NOT EXISTS `log_changes` (
  `personnel_id` int(11) NOT NULL COMMENT 'User who performed update',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `table_updated` varchar(255) NOT NULL COMMENT 'What table was affected',
  `sql_executed` text NOT NULL COMMENT 'What was the SQL that was run',
  `row_updated` int(11) unsigned NOT NULL,
  KEY `personnel_id_idx` (`personnel_id`),
  KEY `table_updated_idx` (`table_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log of changes';

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--

DROP TABLE IF EXISTS `user_session`;
CREATE TABLE IF NOT EXISTS `user_session` (
  `personnel_id` int(11) unsigned NOT NULL,
  `session_code` varchar(64) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` varchar(128) DEFAULT NULL,
  `ip_address` int(11) DEFAULT NULL,
  PRIMARY KEY (`session_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
