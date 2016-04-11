-- phpMyAdmin SQL Dump
-- version 4.1.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 29, 2016 at 05:21 PM
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
CREATE DATABASE IF NOT EXISTS `atc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `atc`;

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--
-- Creation: Mar 13, 2016 at 02:48 AM
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) NOT NULL,
  `2ic_personnel_id` int(11) unsigned NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `location_id` int(11) unsigned NOT NULL,
  `activity_type_id` int(11) unsigned NOT NULL,
  `dress_code` tinyint(3) unsigned NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`activity_id`),
  KEY `personnel_id` (`personnel_id`),
  KEY `location_id` (`location_id`),
  KEY `activity_type_id` (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `activity`
--

TRUNCATE TABLE `activity`;
--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `personnel_id`, `2ic_personnel_id`, `startdate`, `enddate`, `title`, `location_id`, `activity_type_id`, `dress_code`, `cost`) VALUES
(0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 4, 7, 0, '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `activity_register`
--
-- Creation: Mar 27, 2016 at 03:23 AM
-- Last update: Mar 27, 2016 at 05:18 AM
--

DROP TABLE IF EXISTS `activity_register`;
CREATE TABLE IF NOT EXISTS `activity_register` (
  `personnel_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `presence` tinyint(4) unsigned DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`personnel_id`,`activity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `activity_register`
--

TRUNCATE TABLE `activity_register`;
--
-- Dumping data for table `activity_register`
--


-- --------------------------------------------------------

--
-- Table structure for table `activity_type`
--
-- Creation: Mar 04, 2016 at 07:24 AM
--

DROP TABLE IF EXISTS `activity_type`;
CREATE TABLE IF NOT EXISTS `activity_type` (
  `activity_type_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `nzcf_status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Truncate table before insert `activity_type`
--

TRUNCATE TABLE `activity_type`;
--
-- Dumping data for table `activity_type`
--

INSERT INTO `activity_type` (`activity_type_id`, `type`, `nzcf_status`) VALUES
(8, 'Profile Raising', 0),
(9, 'Fund raising', 0),
(10, 'Planning', 0),
(11, 'Camp', 0),
(12, 'Presentation', 0),
(13, 'Gliding', 0),
(14, 'NZCF Course', 0),
(15, 'Rememberance Ceremony', 0),
(16, 'Base visit', 0),
(17, 'Shoot', 0),
(19, 'Powered Flying', 0),
(20, 'Tramp', 0),
(21, 'Airshow', 0),
(22, 'Drill competition', 0);

-- --------------------------------------------------------

--
-- Table structure for table `activity_type_document`
--
-- Creation: Mar 04, 2016 at 07:24 AM
--

DROP TABLE IF EXISTS `activity_type_document`;
CREATE TABLE IF NOT EXISTS `activity_type_document` (
  `activity_type_id` int(11) unsigned NOT NULL,
  `document_id` int(11) unsigned NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`activity_type_id`,`document_id`),
  KEY `document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `activity_type_document`
--

TRUNCATE TABLE `activity_type_document`;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_register`
--
-- Creation: Mar 18, 2016 at 08:44 AM
--

DROP TABLE IF EXISTS `attendance_register`;
CREATE TABLE IF NOT EXISTS `attendance_register` (
  `personnel_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `presence` tinyint(4) unsigned NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`personnel_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `attendance_register`
--

TRUNCATE TABLE `attendance_register`;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--
-- Creation: Mar 04, 2016 at 07:24 AM
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

--
-- Truncate table before insert `document`
--

TRUNCATE TABLE `document`;
-- --------------------------------------------------------

--
-- Table structure for table `location`
--
-- Creation: Mar 04, 2016 at 07:24 AM
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `loc_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `location`
--

TRUNCATE TABLE `location`;

-- --------------------------------------------------------

--
-- Table structure for table `location_document`
--
-- Creation: Mar 04, 2016 at 07:24 AM
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

--
-- Truncate table before insert `location_document`
--

TRUNCATE TABLE `location_document`;
-- --------------------------------------------------------

--
-- Table structure for table `log_changes`
--
-- Creation: Mar 04, 2016 at 07:24 AM
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

--
-- Truncate table before insert `log_changes`
--

TRUNCATE TABLE `log_changes`;


-- --------------------------------------------------------

--
-- Table structure for table `next_of_kin`
--
-- Creation: Mar 04, 2016 at 07:24 AM
-- Last update: Mar 04, 2016 at 07:24 AM
--

DROP TABLE IF EXISTS `next_of_kin`;
CREATE TABLE IF NOT EXISTS `next_of_kin` (
  `kin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) unsigned NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `relationship` tinyint(3) unsigned NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `home_number` varchar(20) DEFAULT NULL,
  `address1` varchar(150) NOT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `postcode` int(11) unsigned DEFAULT NULL,
  `sort_order` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`kin_id`),
  UNIQUE KEY `personnel_id` (`personnel_id`,`relationship`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `next_of_kin`
--

TRUNCATE TABLE `next_of_kin`;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--
-- Creation: Mar 22, 2016 at 06:18 PM
-- Last update: Mar 28, 2016 at 03:58 AM
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(35) DEFAULT NULL,
  `payment_type` smallint(5) unsigned NOT NULL,
  `related_to_id` int(11) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) unsigned NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Truncate table before insert `payment`
--

TRUNCATE TABLE `payment`;

-- --------------------------------------------------------

--
-- Table structure for table `personnel`
--
-- Creation: Mar 12, 2016 at 08:06 AM
--

DROP TABLE IF EXISTS `personnel`;
CREATE TABLE IF NOT EXISTS `personnel` (
  `personnel_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `is_female` tinyint(1) NOT NULL COMMENT 'User self identifies as',
  `email` varchar(255) NOT NULL,
  `mobile_phone` varchar(50) DEFAULT NULL,
  `allergies` varchar(255) DEFAULT NULL,
  `medical_conditions` varchar(255) DEFAULT NULL,
  `medicinal_reactions` varchar(255) DEFAULT NULL,
  `dietary_requirements` varchar(255) DEFAULT NULL,
  `other_notes` varchar(255) DEFAULT NULL,
  `flight` varchar(15) DEFAULT NULL,
  `social_media_approved` tinyint(1) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Truncate table before insert `personnel`
--

TRUNCATE TABLE `personnel`;
--
-- Dumping data for table `personnel`
--

INSERT INTO `personnel` (`personnel_id`, `firstname`, `lastname`, `is_female`, `email`, `mobile_phone`, `allergies`, `medical_conditions`, `medicinal_reactions`, `dietary_requirements`, `other_notes`, `flight`, `social_media_approved`, `dob`, `password`, `access_rights`, `created`, `joined_date`, `left_date`, `enabled`) VALUES
(0, '', '', 0, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0000-00-00', '', 0, '2016-02-23 04:36:48', '0000-00-00', NULL, -1),
(1, 'Adjutant', 'Adjutant', 0, 'adjutant@49squadron.org.nz', '', '', '', '', '', '', '', -1, '1975-09-17', 'sha256:1000:znLt2isj/Y+BTjzWUMz2C7pBA0NKWGrP:5Hjx09RePKpr21CQYMHTfuA1SnGBZuN8', 62975, '2016-02-05 07:32:51', '2015-05-01', NULL, -1),
(8, 'ADMIN', 'ADMIN', 0, 'admin@49squadron.org.nz', '', '', '', '', '', '', '', 0, '1970-01-01', 'sha256:1000:t8fpuwn3fwQWGZlFfm+gjmK+jsaWgqZX:4wFTGNc3CkEABKGSA9Ys1Phj1a55Aw08', 16777215, '2016-02-28 00:55:38', '1990-06-25', NULL, -1),
(10, 'Treasurer', 'Treasurer', -1, 'treasurer@49squadron.org.nz', '', '', '', '', '', '', '', 0, '1966-07-09', 'sha256:1000:jQxcXvuCf74Wa8maYeRkmk6UZb+2Cglr:F4Qyc8Q65ly5K+cXDztFQ+F/3E0S7agp', 33237, '2016-02-28 03:28:26', '2015-04-25', NULL, -1),
(11, 'CUCDR', 'CUCDR', 0, 'cucdr@49squadron.org.nz', '0299784532', '', '', '', '', '', '', 0, '1967-09-01', 'sha256:1000:wwgdeY+s9ZkkRMGdiEqkTaDRWMnQJUcp:o0jjSxEmfrw1sWoYUNA2OPqS8w3QwNwJ', 54615, '2016-02-29 08:47:04', '1970-01-01', NULL, -1),
(12, 'Officer', 'Officer', 0, 'officer@49squadron.org.nz', '', '', '', '', '', '', '', 0, '1970-01-01', 'sha256:1000:0pAfXC2DJfLHPtCKRiyZOqfLEce1kLqR:Q81FDCgQEKFnVLzVTUZdf965ngaSWQqB', 38229, '2016-02-29 18:01:12', '1970-01-01', NULL, -1),
(13, 'Training', 'Training', -1, 'training@49squadron.org.nz', '', '', '', '', '', '', '', 0, '1970-01-01', 'sha256:1000:b56TSe3u82PuKiI3LF/DxqfUAnVJdiHj:9qB7un5DyPAhIGNgxtUZnuhBA62p82/i', 128373, '2016-02-29 18:04:00', '1970-01-01', NULL, -1),
(14, 'Stores', 'Stores', -1, 'stores@49squadron.org.nz', '0274851540', '', '', '', '', '', '', 0, '1970-01-01', 'sha256:1000:griGwGU3sbgocJjiZxy9b2nNNNvp0k6i:e8l0MvGY29/Rv+aeB9bqA0l9KolIz3wp', 40277, '2016-02-29 18:06:04', '1970-01-01', NULL, -1),
(15, 'W/O', 'W/O', -1, 'w/o@49squadron.org.nz', '', '', '', '', 'No red meat', '', '', 0, '1998-03-03', 'sha256:1000:SHXsHlm94gDyD38g/o5v6kQ2QkNXRw3T:iiKz1mwFHvx/T1GYFl3GS51bFGtBGPol', 36881, '2016-03-02 03:20:59', '2012-09-19', NULL, -1),
(32, 'CPL', 'CPL', 0, 'cpl@49squadron.org.nz', '', '', '', '', '', '', '', 0, '2000-02-20', 'sha256:1000:cPT85xR4dfZZayutnlP4R/Xucdhy955p:+oZ9bLwlmS6ySVRQQ/xA/fr/hBG0/dmk', 32784, '2016-03-02 18:26:25', '2013-02-11', NULL, -1),
(40, 'UO', 'UO', 0, 'uo@49squadron.org.nz', '', '', '', '', '', '', '', 0, '1996-05-18', 'sha256:1000:FehdV4qqHQ5u5kr1e3Ytld+cEpbj9Gcu:x7Ao6BM2VQP0G63WOBTi6+A3aH4AI6wu', 36881, '2016-03-03 04:36:11', '2010-12-14', NULL, -1),
(41, 'LAC', 'LAC', 0, 'lac@49squadron.org.nz', '', '', 'Hayfever', '', '', '', '', 0, '2000-07-08', 'sha256:1000:3eMg4JxIIuMjYwXrl2jLBHsIKiDX/j2H:VNjpcg7EcwvRCIDikguehpV3Rcxxi1NI', 32784, '2016-03-03 04:40:09', '2013-09-04', NULL, -1),
(45, 'NCO', 'NCO', 0, 'nco@49squadron.org.nz', '', '', '', '', '', '', '', 0, '1996-05-02', 'sha256:1000:JhPkXsY24Tdkeg4AfR2K9t+5FcglYyMk:46WYLo40DdMDAxcUsNkBNwMQBw8Ajovg', 36881, '2016-03-03 04:55:56', '2010-09-02', NULL, -1),
(48, 'F/S', 'F/S', 0, 'f/s@49squadron.org.nz', '', '', 'Asthma, Hayfever', '', '', '', 'C', 0, '1997-12-29', 'sha256:1000:Q0PZlwX7D9wjILPdHlVwvlP4VbDjQa25:RIE7ximA6pHAOUdgQXFoyCTBNIHwHXe9', 32784, '2016-03-03 05:00:29', '2011-03-09', NULL, -1),
(51, 'CADET', 'CADET', 0, 'cadet@49squadron.org.nz', '', '', 'Dyslexia', '', '', '', 'A', -1, '2003-05-05', 'sha256:1000:pWk6K/fibXlh3l8T0GZy80bwlnnVmvkG:bNP0LdhSW4LhZRVdCj+AslmVqDMuoFkb', 32784, '2016-03-03 05:11:18', '2016-02-10', NULL, -1);

-- --------------------------------------------------------

--
-- Table structure for table `personnel_rank`
--
-- Creation: Mar 05, 2016 at 08:06 AM
-- Last update: Mar 28, 2016 at 03:52 AM
--

DROP TABLE IF EXISTS `personnel_rank`;
CREATE TABLE IF NOT EXISTS `personnel_rank` (
  `rank_id` int(11) unsigned NOT NULL,
  `personnel_id` int(11) unsigned NOT NULL,
  `acting` tinyint(1) NOT NULL DEFAULT '0',
  `date_achieved` date NOT NULL,
  PRIMARY KEY (`rank_id`,`personnel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Truncate table before insert `personnel_rank`
--

TRUNCATE TABLE `personnel_rank`;

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--
-- Creation: Mar 05, 2016 at 07:54 AM
-- Last update: Mar 06, 2016 at 05:42 AM
--

DROP TABLE IF EXISTS `rank`;
CREATE TABLE IF NOT EXISTS `rank` (
  `rank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank` varchar(50) NOT NULL,
  `rank_shortname` varchar(6) NOT NULL,
  `ordering` mediumint(8) unsigned NOT NULL,
  `nzcf20_order` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Truncate table before insert `rank`
--

TRUNCATE TABLE `rank`;
--
-- Dumping data for table `rank`
--

INSERT INTO `rank` (`rank_id`, `rank`, `rank_shortname`, `ordering`, `nzcf20_order`) VALUES
(1, 'Squadron Leader', 'SQNLDR', 10, 10),
(2, 'Pilot Officer', 'PLTOFF', 40, 40),
(3, 'Under Officer', 'U/O', 45, 45),
(4, 'Warrant Officer', 'W/O', 47, 1),
(5, 'Flight Sergent', 'F/S', 50, 2),
(6, 'Sergent', 'SGT', 60, 3),
(7, 'Corpral', 'CPL', 70, 4),
(8, 'Leading Air Cadet', 'LAC', 80, 5),
(9, 'Cadet', 'CDT', 90, 6),
(10, 'Flight Lieutenant', 'FLTLT', 15, 15),
(11, 'Not Applicable', 'N/A', 999, 999);

-- --------------------------------------------------------

--
-- Table structure for table `term`
--
-- Creation: Mar 20, 2016 at 08:03 AM
-- Last update: Mar 20, 2016 at 08:03 AM
--

DROP TABLE IF EXISTS `term`;
CREATE TABLE IF NOT EXISTS `term` (
  `term_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  PRIMARY KEY (`term_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Truncate table before insert `term`
--

TRUNCATE TABLE `term`;
--
-- Dumping data for table `term`
--

INSERT INTO `term` (`term_id`, `startdate`, `enddate`) VALUES
(1, '2016-02-03', '2016-04-13');

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--
-- Creation: Mar 04, 2016 at 07:24 AM
-- Last update: Mar 28, 2016 at 01:36 AM
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
-- Truncate table before insert `user_session`
--

TRUNCATE TABLE `user_session`;


-- Version 0.8.0
-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE IF NOT EXISTS `lesson` (
  `lesson_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_category_id` int(11) unsigned NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `dress_code` smallint(5) unsigned NOT NULL,
  `nzqa_qualifies` tinyint(1) NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL,
  `nzcf` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`lesson_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_category`
--

CREATE TABLE IF NOT EXISTS `lesson_category` (
  `lesson_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  `colour` varchar(7) NOT NULL DEFAULT '#ffffff',
  `text_colour` varchar(7) NOT NULL DEFAULT '#000000',
  `category_short` varchar(9) NOT NULL,
  `suggested_nzcf` tinyint(4) NOT NULL,
  PRIMARY KEY (`lesson_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_timetable`
--

CREATE TABLE IF NOT EXISTS `lesson_timetable` (
  `lesson_timetable_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) unsigned NOT NULL,
  `personnel_id` int(11) unsigned NOT NULL,
  `location_id` int(11) unsigned NOT NULL DEFAULT '7',
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `group` varchar(5) NOT NULL COMMENT 'ADV/PROF/BASIC',
  `dress_code` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`lesson_timetable_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- /Version 0.8.0

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
