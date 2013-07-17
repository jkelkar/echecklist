-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2013 at 11:18 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `artist`, `title`) VALUES
(1, 'Paolo Nutine', 'Sunny Side Up'),
(2, 'Florence + The Machine', 'Lungs'),
(3, 'Massive Attack', 'Heligoland'),
(4, 'Andre Rieu', 'Forever Vienna'),
(5, 'D''Sade', 'Soldier of Love'),
(6, 'Baby Too', 'Stinky Poo');

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE IF NOT EXISTS `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_level` int(11) NOT NULL,
  `action` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Authorization structure' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `data_head`
--

CREATE TABLE IF NOT EXISTS `data_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tmpl_id` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Header for data for one document' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `data_head`
--

INSERT INTO `data_head` (`id`, `tmpl_id`, `created_at`, `updated_at`, `updated_by`, `lab_id`, `status`) VALUES
(1, 1, '2013-07-14 00:38:55', '0000-00-00 00:00:00', 1, 1, 'INPROGRESS');

-- --------------------------------------------------------

--
-- Table structure for table `data_item`
--

CREATE TABLE IF NOT EXISTS `data_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_head_id` int(11) NOT NULL,
  `field_name` varchar(30) NOT NULL,
  `int_val` int(11) NOT NULL DEFAULT '0',
  `text_val` text NOT NULL,
  `string_val` varchar(256) NOT NULL,
  `date_val` date NOT NULL DEFAULT '2000-01-01',
  `field_type` varchar(10) NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='One row per field of data saved.' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `data_item`
--

INSERT INTO `data_item` (`id`, `data_head_id`, `field_name`, `int_val`, `text_val`, `string_val`, `date_val`, `field_type`) VALUES
(1, 1, 'dola', 0, '', '', '2013-01-13', 'date'),
(2, 1, 'audit_date', 0, '', '', '2013-07-14', 'date'),
(3, 1, 'stars', 0, '', '2', '2000-01-01', 'string'),
(4, 1, 'labaddr', 0, 'street,\r\nstreet2\r\ncity, state, zip\r\ncountry', '', '2000-01-01', 'text'),
(5, 1, 'cleaner_num', 5, '', '', '2000-01-01', 'integer'),
(6, 1, 'cleaner_dedicated_yn', 0, '', 'Y', '2000-01-01', 'string'),
(7, 1, 'cleaner_trained_yn', 0, '', 'N', '2000-01-01', 'string'),
(8, 1, 'cleaner_yni', 0, '', 'Y', '2000-01-01', 'string'),
(9, 1, 'driver_num', 100, '', '', '2000-01-01', 'integer'),
(10, 1, 'driver_yni', 0, '', 'I', '2000-01-01', 'string'),
(11, 1, 'driver_dedicated_yn', 0, '', 'Y', '2000-01-01', 'string'),
(12, 1, 'driver_trained_yn', 0, '', 'N', '2000-01-01', 'string'),
(13, 1, 'sufficient_space_yn', 0, '', 'N', '2000-01-01', 'string'),
(14, 1, 'sufficient_equipment_yn', 0, '', 'N', '2000-01-01', 'string');

-- --------------------------------------------------------

--
-- Table structure for table `lab`
--

CREATE TABLE IF NOT EXISTS `lab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `labnum` varchar(40) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Lab info ' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lab`
--

INSERT INTO `lab` (`id`, `labnum`, `description`) VALUES
(1, 'Best Lab, Inc', 'The best lab in all of Africa capable of doing almost anything.');

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `tmpl_head_id` int(11) NOT NULL,
  `page_num` int(11) NOT NULL,
  `tag` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`tmpl_head_id`, `page_num`, `tag`) VALUES
(1, 1, 'Section 1.3-1.7'),
(1, 2, 'Section1.8-End');

-- --------------------------------------------------------

--
-- Table structure for table `tmpl_head`
--

CREATE TABLE IF NOT EXISTS `tmpl_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_by` int(11) NOT NULL,
  `version` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tracks template versions' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tmpl_head`
--

INSERT INTO `tmpl_head` (`id`, `tag`, `created_at`, `updated_at`, `update_by`, `version`) VALUES
(1, 'SLIPTA', '2013-07-11 12:01:26', '0000-00-00 00:00:00', 1, '0.5');

-- --------------------------------------------------------

--
-- Table structure for table `tmpl_row`
--

CREATE TABLE IF NOT EXISTS `tmpl_row` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tmpl_head_id` int(11) NOT NULL,
  `varname` varchar(25) NOT NULL,
  `row_type` varchar(20) NOT NULL,
  `part` tinyint(4) NOT NULL,
  `level1` tinyint(4) NOT NULL,
  `level2` tinyint(4) NOT NULL,
  `level3` int(11) NOT NULL,
  `level4` int(11) NOT NULL,
  `level5` int(11) NOT NULL,
  `element` tinyint(4) NOT NULL,
  `prefix` varchar(20) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `score` tinyint(4) NOT NULL,
  `page_num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stores template info of a display row.' AUTO_INCREMENT=34 ;

--
-- Dumping data for table `tmpl_row`
--

INSERT INTO `tmpl_row` (`id`, `tmpl_head_id`, `varname`, `row_type`, `part`, `level1`, `level2`, `level3`, `level4`, `level5`, `element`, `prefix`, `heading`, `text`, `score`, `page_num`) VALUES
(1, 1, 'abc', 'sec_head', 2, 1, 0, 0, 0, 0, 0, '1.0', 'Documents & Records', '', 0, 1),
(2, 1, 'bcd', 'sub_sec_head', 2, 1, 0, 0, 0, 0, 0, '1.1', 'Laboratory Quality Manual', 'Is there a current laboratory manual, composed of the quality management system''s policies and procedures, and has the manual content been communicated to and understood and implemented by all staff?', 4, 1),
(3, 1, 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 0, '', '', 'Structure defined per ISO15189, section 4.2.4', 0, 1),
(4, 1, 'pas', 'stars', 1, 1, 0, 0, 0, 3, 0, '', '', 'Prior Audit Status', 0, 1),
(5, 1, '', 'sec_head', 1, 0, 0, 0, 0, 0, 0, 'PART I:', 'laboratory profile', '', 0, 1),
(6, 1, 'audit_date', 'date', 1, 1, 0, 0, 0, 1, 0, '', '', 'Date of Audit', 0, 1),
(7, 1, 'dola', 'date', 1, 1, 0, 0, 0, 2, 0, '', '', 'Date of Last Audit', 0, 1),
(8, 1, 'labaddr', 'text', 1, 1, 0, 0, 0, 7, 0, '', '', 'Laboratory Address', 0, 1),
(9, 1, 'lablevel', 'lablevel', 1, 1, 1, 0, 0, 14, 0, '', '', 'Laboratory Level', 0, 1),
(10, 1, 'labaffil', 'labaffil', 1, 1, 1, 0, 0, 15, 0, '', '', 'Type of Laboratory/Laboratory Affiliation', 0, 1),
(11, 1, '', 'sec_head_small', 1, 1, 1, 0, 0, 16, 0, '', 'Laboraroty Staffing Summary', '', 0, 1),
(12, 1, '', 'tab_head3', 1, 1, 1, 0, 0, 17, 0, 'Profession', 'Number of Full Time Employees', 'Adequate for facility operations?', 0, 1),
(13, 1, 'dghps', 'pinfo', 1, 1, 1, 0, 0, 18, 0, '', '', 'Degree Holding Professional Staff', 0, 1),
(14, 1, 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 2, '0', '', 'Quality policy statement that includes scope of service, standard of service, objectives of the quality management system, and management commitment to compliance', 0, 1),
(15, 1, 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 3, '0', '', 'Description of the quality management system and the structure of its documentation ', 0, 1),
(16, 1, 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 4, '0', '', 'Reference to supporting procedures, including technical procedures', 0, 1),
(17, 1, 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 5, '0', '', 'Description of the roles and responsibilities of the laboratory manager, quality manager, and other personnel responsible for ensuring compliance ', 0, 1),
(18, 1, 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 6, '0', '', 'Documentation of at least annual management review and approval', 0, 1),
(19, 1, 'cleaner', 'pinfo', 1, 1, 1, 0, 0, 24, 0, '', '', 'Cleaner', 0, 2),
(20, 1, 'cleaner_dedicated', 'pinfo2_i', 1, 1, 1, 0, 0, 25, 0, '', '', 'Is the driver(s) dedicated to the laboratory only?', 0, 2),
(21, 1, 'cleaner_trained', 'pinfo2_i', 1, 1, 1, 0, 0, 26, 0, '', '', 'Has the cleaner(s) been trained in safe waste handling?', 0, 2),
(22, 1, 'driver', 'pinfo', 1, 1, 1, 0, 0, 27, 0, '', '', 'Driver', 0, 2),
(23, 1, 'driver_dedicated', 'pinfo2_i', 1, 1, 1, 0, 0, 28, 0, '', '', 'Is the driver(s) dedicated to the laboratory only?', 0, 2),
(24, 1, 'driver_trained', 'pinfo2_i', 1, 1, 1, 0, 0, 29, 0, '', '', 'Has the driver(s) been trained in biosafety?', 0, 2),
(25, 1, '', 'info_i', 1, 1, 1, 0, 0, 31, 0, '', '', 'If the laboratory has IT specialists, accountants or non-laboratory-trained management staff, this should be indicated in the description of the organizational structure on the following page.', 0, 2),
(26, 1, '', 'info_bn', 1, 1, 1, 0, 0, 32, 0, '0', 'Does the laboratory have sufficient space, equipment, supplies, personnel, infrastructure, etc. to execute the correct and timely performance of each test and maintain the quality management system?', 'If no, please elaborate in the summary and recommendations section at the end of the checklist.', 0, 2),
(27, 1, 'sufficient_space', 'pinfo2', 1, 1, 1, 0, 0, 33, 0, '', '', 'Sufficient space', 0, 2),
(28, 1, 'sufficient_equipment', 'pinfo2', 1, 1, 1, 0, 0, 34, 0, '', '', 'Equipment', 0, 2),
(29, 1, '', 'criteria_1_heading', 3, 1, 1, 0, 0, 0, 0, 'Criteria 1', 'Are internal quality control procedures routinely conducted for all test methods?', '', 0, 2),
(30, 1, 'monconval', 'criteria_1_values', 3, 1, 1, 1, 0, 0, 0, '1.1', 'Monitoring of control values', '', 0, 2),
(31, 1, 'monintstd', 'criteria_1_values', 3, 1, 1, 2, 0, 0, 0, '1.2', 'Monotoring with internal standards', '', 0, 2),
(32, 1, 'monqualkit', 'criteria_1_values', 3, 1, 1, 3, 0, 0, 0, '1.3', 'Monotoring quality of each new batch of kits', '', 0, 2),
(33, 1, 'docconkit', 'criteria_1_values', 3, 1, 1, 4, 0, 0, 0, '1.4', 'Documentation of internal controls and kits validation', '', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tmpl_type`
--

CREATE TABLE IF NOT EXISTS `tmpl_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(10) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tmpl_tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Describes templates at high level' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tmpl_type`
--

INSERT INTO `tmpl_type` (`id`, `tag`, `description`) VALUES
(1, 'SLIPTA', 'SLIPTA Template');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_type` varchar(10) NOT NULL,
  `password` varchar(80) NOT NULL,
  `languages` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Used to store allowed users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE IF NOT EXISTS `user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(20) NOT NULL,
  `description` varchar(80) NOT NULL,
  `user_level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_type` (`user_type`),
  UNIQUE KEY `user_level` (`user_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maps userlevel to usertype. Lower user level can do all action of higher levels' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
