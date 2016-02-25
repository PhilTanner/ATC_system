INSERT INTO `activity` (`activity_id`, `personnel_id`, `startdate`, `enddate`, `title`, `location_id`, `activity_type_id`, `dress_code`) VALUES
(0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0);
INSERT INTO `activity_type` (`activity_type_id`, `type`, `nzcf_status`) VALUES
(0, '', 0);
INSERT INTO `location` (`location_id`, `name`, `address`) VALUES
(0, '', NULL);
INSERT INTO `personnel` (`personnel_id`, `firstname`, `lastname`, `is_female`, `email`, `dob`, `password`, `access_rights`, `created`, `joined_date`, `left_date`, `enabled`) VALUES
(0, '', '', 0, '', '0000-00-00', '', 0, '2016-02-23 04:36:48', '0000-00-00', NULL, -1),
(1, 'Phil', 'Tanner', 0, 'phil.tanner@49squadron.org.nz', '1975-09-17', 'sha256:1000:/8sO+9s6F7hZoRCW6CF7lyvtGU/aUGb2:E2Dxpue7Jh0qv1oL9JAfQTrE17aqjsL4', 59647, '2016-02-05 07:32:51', '2015-05-01', NULL, -1);

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
