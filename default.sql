INSERT INTO `activity` (`activity_id`, `personnel_id`, `startdate`, `enddate`, `title`, `location_id`, `activity_type_id`, `dress_code`) VALUES
(0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0);
INSERT INTO `activity_type` (`activity_type_id`, `type`, `nzcf_status`) VALUES
(0, '', 0);
INSERT INTO `location` (`location_id`, `name`, `address`) VALUES
(0, '', NULL);
INSERT INTO `personnel` (`personnel_id`, `firstname`, `lastname`, `is_female`, `email`, `dob`, `password`, `access_rights`, `created`, `joined_date`, `left_date`, `enabled`) VALUES
(0, '', '', 0, '', '0000-00-00', '', 0, '2016-02-23 04:36:48', '0000-00-00', NULL, -1),
(1, 'Phil', 'Tanner', 0, 'phil.tanner@49squadron.org.nz', '1975-09-17', 'sha256:1000:/8sO+9s6F7hZoRCW6CF7lyvtGU/aUGb2:E2Dxpue7Jh0qv1oL9JAfQTrE17aqjsL4', 59647, '2016-02-05 07:32:51', '2015-05-01', NULL, -1);

ALTER TABLE `personnel` ADD `mobile_phone` VARCHAR(50) NULL AFTER `email`;
ALTER TABLE `attendance_register` ADD `comment` VARCHAR(255) NULL ;
ALTER TABLE `activity` ADD `2ic_personnel_id` INT(11) UNSIGNED NOT NULL AFTER `personnel_id`;
ALTER TABLE `personnel` ADD `allergies` VARCHAR(255) NULL AFTER `mobile_phone`, ADD `medical_conditions` VARCHAR(255) NULL AFTER `allergies`, ADD `medicinal_reactions` VARCHAR(255) NULL AFTER `medical_conditions`, ADD `dietary_requirements` VARCHAR(255) NULL AFTER `medicinal_reactions`;
ALTER TABLE `personnel` ADD `other_notes` VARCHAR(255) NULL AFTER `dietary_requirements`;

CREATE TABLE IF NOT EXISTS `rank` (
  `rank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank` varchar(50) NOT NULL,
  `rank_shortname` varchar(6) NOT NULL,
  `ordering` mediumint(8) unsigned NOT NULL,
  `nzcf20_order` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;
CREATE TABLE IF NOT EXISTS `personnel_rank` (
  `rank_id` int(11) unsigned NOT NULL,
  `personnel_id` int(11) unsigned NOT NULL,
  `acting` tinyint(1) NOT NULL DEFAULT '0',
  `date_achieved` date NOT NULL,
  PRIMARY KEY (`rank_id`,`personnel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `rank` (`rank_id`, `rank`, `rank_shortname`, `ordering`, `nzcf20_order`) VALUES
(1, 'Squadron Leader', 'SQNLDR', 10, 10),
(2, 'Pilot Officer', 'PLTOFF', 40, 40),
(3, 'Under Officer', 'U/O', 45, 45),
(4, 'Warrant Officer', 'W/O', 47, 10),
(5, 'Flight Sergeant', 'F/S', 50, 20),
(6, 'Sergeant', 'SGT', 60, 30),
(7, 'Corporal', 'CPL', 70, 40),
(8, 'Leading Air Cadet', 'LAC', 80, 50),
(9, 'Cadet', 'CDT', 90, 60),
(10, 'Flight Lieutenant', 'FLTLT', 15, 15),
(11, 'Not Applicable', 'N/A', '999', '999');
INSERT INTO `personnel_rank` ( personnel_id, date_achieved, rank_id ) SELECT personnel_id, joined_date, 9 AS rank_id FROM personnel WHERE NOT personnel_id IN (10,1,13);


ALTER TABLE `personnel` ADD `flight` VARCHAR(15) NULL AFTER `other_notes`;
ALTER TABLE `personnel` ADD `social_media_approved` BOOLEAN NOT NULL DEFAULT FALSE AFTER `flight`;


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
