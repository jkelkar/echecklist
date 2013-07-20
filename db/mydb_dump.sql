-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 20, 2013 at 01:04 AM
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
  `labname` varchar(100) NOT NULL,
  `labnum` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `street` varchar(100) NOT NULL,
  `street2` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(60) NOT NULL,
  `country` varchar(40) NOT NULL,
  `county_code` varchar(3) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `level` char(1) NOT NULL,
  `affiliation` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Lab info ' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lab`
--

INSERT INTO `lab` (`id`, `labname`, `labnum`, `description`, `street`, `street2`, `city`, `state`, `country`, `county_code`, `postcode`, `level`, `affiliation`) VALUES
(1, 'Best Lab, Inc', 'lab-007', 'The best lab in all of Africa capable of doing almost anything.', 'No. 19 Fifth Link Road ', 'Cantonments', 'Accra', '', 'Ghana', 'GH', '', 'R', 'R');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) NOT NULL,
  `tag` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lang_text`
--

CREATE TABLE IF NOT EXISTS `lang_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(2) NOT NULL,
  `row_name` varchar(32) NOT NULL,
  `prefix` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `ss_hint` text NOT NULL,
  `row_hint` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lang_row` (`tag`,`row_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='language specific texts' AUTO_INCREMENT=367 ;

--
-- Dumping data for table `lang_text`
--

INSERT INTO `lang_text` (`id`, `tag`, `row_name`, `prefix`, `heading`, `text`, `ss_hint`, `row_hint`) VALUES
(127, 'EN', 'p2_t_s01', '1.0', 'Documents & Records', '', '', ''),
(128, 'EN', 'p2_s01_01', '1.1', 'Laboratory Quality Manual', 'Is there a current laboratory manual, composed of the quality management system''s policies and procedures, and has the manual content been communicated to and understood and implemented by all staff?', '', ''),
(129, 'EN', ' p2_s01_01_01', '', '', 'Structure defined per ISO15189, section 4.2.4', '', ''),
(130, 'EN', 'p1_pas', '', '', 'Prior Audit Status', '', ''),
(131, 'EN', 'p1_t_01', 'PART I:', 'laboratory profile', '', '', ''),
(132, 'EN', 'p1_doa', '', '', 'Date of Audit', '', ''),
(133, 'EN', 'p1_dola', '', '', 'Date of Last Audit', '', ''),
(134, 'EN', 'p1_labaddr', '', '', 'Laboratory Address', '', ''),
(135, 'EN', 'p1_lablevel', '', '', 'Laboratory Level', '', ''),
(136, 'EN', 'p1_labaffil', '', '', 'Type of Laboratory/Laboratory Affiliation', '', ''),
(137, 'EN', 'p1_t_lss', '', 'Laboraroty Staffing Summary', '', '', ''),
(138, 'EN', 'p1_t_emp', 'Profession', 'Number of Full Time Employees', 'Adequate for facility operations?', '', ''),
(139, 'EN', 'p1_dg_hps', '', '', 'Degree Holding Professional Staff', '', ''),
(140, 'EN', ' p2_s01_01_02', '0', '', 'Quality policy statement that includes scope of service, standard of service, objectives of the quality management system, and management commitment to compliance', '', ''),
(141, 'EN', ' p2_s01_01_03', '0', '', 'Description of the quality management system and the structure of its documentation ', '', ''),
(142, 'EN', ' p2_s01_01_04', '0', '', 'Reference to supporting procedures, including technical procedures', '', ''),
(143, 'EN', ' p2_s01_01_05', '0', '', 'Description of the roles and responsibilities of the laboratory manager, quality manager, and other personnel responsible for ensuring compliance ', '', ''),
(144, 'EN', 'p2_s01_01_06', '0', '', 'Documentation of at least annual management review and approval', '', ''),
(145, 'EN', 'p1_cleaner', '', '', 'Cleaner', '', ''),
(146, 'EN', 'p1_cleaner_dedicated', '', '', 'Is the driver(s) dedicated to the laboratory only?', '', ''),
(147, 'EN', 'p1_cleaner_trained', '', '', 'Has the cleaner(s) been trained in safe waste handling?', '', ''),
(148, 'EN', 'p1_driver', '', '', 'Driver', '', ''),
(149, 'EN', 'p1_driver_dedicated', '', '', 'Is the driver(s) dedicated to the laboratory only?', '', ''),
(150, 'EN', 'p1_driver_trained', '', '', 'Has the driver(s) been trained in biosafety?', '', ''),
(151, 'EN', 'p1_t_it_spec', '', '', 'If the laboratory has IT specialists, accountants or non-laboratory-trained management staff, this should be indicated in the description of the organizational structure on the following page.', '', ''),
(152, 'EN', 'p1_t_suff_space', '0', 'Does the laboratory have sufficient space, equipment, supplies, personnel, infrastructure, etc. to execute the correct and timely performance of each test and maintain the quality management system?', 'If no, please elaborate in the summary and recommendations section at the end of the checklist.', '', ''),
(153, 'EN', 'p1_sufficient_space', '', '', 'Sufficient space', '', ''),
(154, 'EN', 'p1_sufficient_equipment', '', '', 'Equipment', '', ''),
(157, 'EN', '', '1.2', 'Monotoring with internal standards', '', '', ''),
(158, 'EN', '', '1.3', 'Monotoring quality of each new batch of kits', '', '', ''),
(159, 'EN', '', '1.4', 'Documentation of internal controls and kits validation', '', '', ''),
(190, 'FR', 'p2_t_s01', '1.0', 'Documents & Records', '', '', ''),
(191, 'FR', 'p2_s01_01', '1.1', 'Laboratory Quality Manual', 'Is there a current laboratory manual, composed of the quality management system''s policies and procedures, and has the manual content been communicated to and understood and implemented by all staff?', '', ''),
(192, 'FR', ' p2_s01_01_01', '', '', 'Structure defined per ISO15189, section 4.2.4', '', ''),
(193, 'FR', 'p1_pas', '', '', 'Prior Audit Status', '', ''),
(194, 'FR', 'p1_t_01', 'PART I:', 'laboratory profile', '', '', ''),
(195, 'FR', 'p1_doa', '', '', 'Date of Audit', '', ''),
(196, 'FR', 'p1_dola', '', '', 'Date of Last Audit', '', ''),
(197, 'FR', 'p1_labaddr', '', '', 'Location de laboratorie', '', ''),
(198, 'FR', 'p1_lablevel', '', '', 'Laboratory Level', '', ''),
(199, 'FR', 'p1_labaffil', '', '', 'Type of Laboratory/Laboratory Affiliation', '', ''),
(200, 'FR', 'p1_t_lss', '', 'Laboraroty Staffing Summary', '', '', ''),
(201, 'FR', 'p1_t_emp', 'Profession', 'Number of Full Time Employees', 'Adequate for facility operations?', '', ''),
(202, 'FR', 'p1_dg_hps', '', '', 'Degree Holding Professional Staff', '', ''),
(203, 'FR', ' p2_s01_01_02', '0', '', 'Quality policy statement that includes scope of service, standard of service, objectives of the quality management system, and management commitment to compliance', '', ''),
(204, 'FR', ' p2_s01_01_03', '0', '', 'Description of the quality management system and the structure of its documentation ', '', ''),
(205, 'FR', ' p2_s01_01_04', '0', '', 'Reference to supporting procedures, including technical procedures', '', ''),
(206, 'FR', ' p2_s01_01_05', '0', '', 'Description of the roles and responsibilities of the laboratory manager, quality manager, and other personnel responsible for ensuring compliance ', '', ''),
(207, 'FR', 'p2_s01_01_06', '0', '', 'Documentation of at least annual management review and approval', '', ''),
(208, 'FR', 'p1_cleaner', '', '', 'Sweeper', '', ''),
(209, 'FR', 'p1_cleaner_dedicated', '', '', 'Is the driver(s) dedicated to the laboratory only?', '', ''),
(210, 'FR', 'p1_cleaner_trained', '', '', 'Has the cleaner(s) been trained in safe waste handling?', '', ''),
(211, 'FR', 'p1_driver', '', '', 'Driver', '', ''),
(212, 'FR', 'p1_driver_dedicated', '', '', 'Is the driver(s) dedicated to the laboratory only?', '', ''),
(213, 'FR', 'p1_driver_trained', '', '', 'Has the driver(s) been trained in biosafety?', '', ''),
(214, 'FR', 'p1_t_it_spec', '', '', 'If the laboratory has IT specialists, accountants or non-laboratory-trained management staff, this should be indicated in the description of the organizational structure on the following page.', '', ''),
(215, 'FR', 'p1_t_suff_space', '0', 'Does the laboratory have sufficient space, equipment, supplies, personnel, infrastructure, etc. to execute the correct and timely performance of each test and maintain the quality management system?', 'If no, please elaborate in the summary and recommendations section at the end of the checklist.', '', ''),
(216, 'FR', 'p1_sufficient_space', '', '', 'Sufficient space', '', ''),
(217, 'FR', 'p1_sufficient_equipment', '', '', 'Equipment', '', ''),
(220, 'FR', '', '1.2', 'Monotoring with internal standards', '', '', ''),
(221, 'FR', '', '1.3', 'Monotoring quality of each new batch of kits', '', '', ''),
(222, 'FR', '', '1.4', 'Documentation of internal controls and kits validation', '', '', ''),
(253, 'VI', '', '1.2', 'Monotoring with internal standards', '', '', ''),
(254, 'VI', '', '1.3', 'Monotoring quality of each new batch of kits', '', '', ''),
(255, 'VI', '', '1.4', ' TÀI LIỆU VÀ HỒ SƠ ', '', '', ''),
(256, 'VI', ' p2_s01_01_01', '', '', 'Structure defined per ISO15189, section 4.2.4', '', ''),
(257, 'VI', ' p2_s01_01_02', '0', '', 'Quality policy statement that includes scope of service, standard of service, objectives of the quality management system, and management commitment to compliance', '', ''),
(258, 'VI', ' p2_s01_01_03', '0', '', 'Unicode là gì? Tóm tắt <- That is in Vietnamese\r\n', '', ''),
(259, 'VI', ' p2_s01_01_04', '0', '', 'Reference to supporting procedures, including technical procedures', '', ''),
(260, 'VI', ' p2_s01_01_05', '0', '', 'Description of the roles and responsibilities of the laboratory manager, quality manager, and other personnel responsible for ensuring compliance ', '', ''),
(261, 'VI', 'p1_cleaner', '', '', 'Tạp vụ', '', ''),
(262, 'VI', 'p1_cleaner_dedicated', '', '', 'Firefox Tiếng Việt | Trình duyệt web Việt hóa nhanh hơn, an toàn ...', '', ''),
(263, 'VI', 'p1_cleaner_trained', '', '', 'Has the cleaner(s) been trained in safe waste handling?', '', ''),
(264, 'VI', 'p1_dg_hps', '', '', 'Degree Holding Professional Staff', '', ''),
(265, 'VI', 'p1_doa', '', '', 'Date of Audit', '', ''),
(266, 'VI', 'p1_dola', '', '', 'Date of Last Audit', '', ''),
(267, 'VI', 'p1_driver', '', '', 'Driver', '', ''),
(268, 'VI', 'p1_driver_dedicated', '', '', 'Is the driver(s) dedicated to the laboratory only?', '', ''),
(269, 'VI', 'p1_driver_trained', '', '', 'Has the driver(s) been trained in biosafety?', '', ''),
(270, 'VI', 'p1_labaddr', '', '', 'Laboratory Address', '', ''),
(271, 'VI', 'p1_labaffil', '', '', 'Type of Laboratory/Laboratory Affiliation', '', ''),
(272, 'VI', 'p1_lablevel', '', '', 'Laboratory Level', '', ''),
(273, 'VI', 'p1_pas', '', '', 'Prior Audit Status', '', ''),
(274, 'VI', 'p1_sufficient_equipment', '', '', 'Equipment', '', ''),
(275, 'VI', 'p1_sufficient_space', '', '', 'Sufficient space', '', ''),
(276, 'VI', 'p1_t_01', 'PART I:', 'laboratory profile', '', '', ''),
(277, 'VI', 'p1_t_emp', 'Profession', 'Number of Full Time Employees', 'Adequate for facility operations?', '', ''),
(278, 'VI', 'p1_t_it_spec', '', '', 'If the laboratory has IT specialists, accountants or non-laboratory-trained management staff, this should be indicated in the description of the organizational structure on the following page.', '', ''),
(279, 'VI', 'p1_t_lss', '', 'Laboraroty Staffing Summary', '', '', ''),
(280, 'VI', 'p1_t_suff_space', '0', 'Does the laboratory have sufficient space, equipment, supplies, personnel, infrastructure, etc. to execute the correct and timely performance of each test and maintain the quality management system?', 'If no, please elaborate in the summary and recommendations section at the end of the checklist.', '', ''),
(281, 'VI', 'p2_s01_01', '1.1', 'Laboratory Quality Manual', 'Is there a current laboratory manual, composed of the quality management system''s policies and procedures, and has the manual content been communicated to and understood and implemented by all staff?', '', ''),
(282, 'VI', 'p2_s01_01_06', '0', '', 'Documentation of at least annual management review and approval', '', ''),
(283, 'VI', 'p2_t_s01', 'PHẦN 1.', ' TÀI LIỆU VÀ HỒ SƠ', 'Unicode là gì? Tóm tắt, Unicode là một tiêu chuẩn quốc tế để bao gồm tất cả các chữ viết của những ngôn ngữ thế giới. Những chi tiết của kiểu chữ thì không có  trong Unicode, chỉ có thứ tự của mỗi mẫu tự (mã). Nếu bạn muốn tìm hiểu thêm về Unicode, ghé lại trang Unicode tại ', '', ''),
(316, 'EN', 'p3_t_s1', 'Criteria 1', 'Are internal quality control procedures routinely conducted for all test methods?', '', '', ''),
(317, 'EN', 'p3_s1_01', '1.1', 'Monitoring of control values', '', '', ''),
(318, 'EN', 'p3_s1_02', '1.2', 'Monotoring with internal standards', '', '', ''),
(319, 'EN', 'p3_s1_03', '1.3', 'Monotoring quality of each new batch of kits', '', '', ''),
(320, 'EN', 'p3_s1_04', '1.4', 'Documentation of internal controls and kits validation', '', '', ''),
(321, 'EN', 'p3_t_s2', 'Criteria 2', 'Has the laboratory achieved acceptable PT results of at least 80% on the two most recent PT challenges?', '', '', ''),
(322, 'EN', 'p3_t_s2_00_01', '', 'HIV Serology', '', '', ''),
(323, 'EN', 'p3_s2_01', '2.1', 'Most recent HIV panel', '', '', ''),
(324, 'EN', 'p3_s2_02', '2.2', 'Second most recent HIV panel', '', '', ''),
(325, 'EN', 'p3_t_s2_02_01', '', 'HIV DNA PCR', '', '', ''),
(326, 'EN', 'p3_s2_03', '2.3', 'Most recent HIV DNA PCR panel', '', '', ''),
(327, 'EN', 'p3_s2_04', '2.4', 'Second most recent HIV panel', '', '', ''),
(328, 'EN', 'p3_t_s2_04_01', '0', 'HIV Viral Load', '', '', ''),
(329, 'EN', 'p3_s2_05', '2.5', 'Most recent HIV DNA PCR panel', '', '', ''),
(330, 'EN', 'p3_s2_06', '2.6', 'Second most recent HIV panel', '', '', ''),
(331, 'FR', 'p3_s1_02', '1.2', 'Monotoring with internal standards', '', '', ''),
(332, 'FR', 'p3_s1_03', '1.3', 'Monotoring quality of each new batch of kits', '', '', ''),
(333, 'FR', 'p3_s1_04', '1.4', 'Documentation of internal controls and kits validation', '', '', ''),
(334, 'FR', 'p3_t_s2', 'Criteria 2', 'Has the laboratory achieved acceptable PT results of at least 80% on the two most recent PT challenges?', '', '', ''),
(335, 'FR', 'p3_t_s2_00_01', '', 'HIV Serology', '', '', ''),
(336, 'FR', 'p3_s2_01', '2.1', 'Most recent HIV panel', '', '', ''),
(337, 'FR', 'p3_s2_02', '2.2', 'Second most recent HIV panel', '', '', ''),
(338, 'FR', 'p3_t_s2_02_01', '', 'HIV DNA PCR', '', '', ''),
(339, 'FR', 'p3_s2_03', '2.3', 'Most recent HIV DNA PCR panel', '', '', ''),
(340, 'FR', 'p3_s2_04', '2.4', 'Second most recent HIV panel', '', '', ''),
(341, 'FR', 'p3_t_s2_04_01', '0', 'HIV Viral Load', '', '', ''),
(342, 'FR', 'p3_s2_05', '2.5', 'Most recent HIV DNA PCR panel', '', '', ''),
(343, 'FR', 'p3_s2_06', '2.6', 'Second most recent HIV panel', '', '', ''),
(346, 'VI', 'p3_s1_02', '1.2', 'Monotoring with internal standards', '', '', ''),
(347, 'VI', 'p3_s1_03', '1.3', 'Monotoring quality of each new batch of kits', '', '', ''),
(348, 'VI', 'p3_s1_04', '1.4', 'Documentation of internal controls and kits validation', '', '', ''),
(349, 'VI', 'p3_t_s2', 'Criteria 2', 'Has the laboratory achieved acceptable PT results of at least 80% on the two most recent PT challenges?', '', '', ''),
(350, 'VI', 'p3_t_s2_00_01', '', 'HIV Serology', '', '', ''),
(351, 'VI', 'p3_s2_01', '2.1', 'Most recent HIV panel', '', '', ''),
(352, 'VI', 'p3_s2_02', '2.2', 'Second most recent HIV panel', '', '', ''),
(353, 'VI', 'p3_t_s2_02_01', '', 'HIV DNA PCR', '', '', ''),
(354, 'VI', 'p3_s2_03', '2.3', 'Most recent HIV DNA PCR panel', '', '', ''),
(355, 'VI', 'p3_s2_04', '2.4', 'Second most recent HIV panel', '', '', ''),
(356, 'VI', 'p3_t_s2_04_01', '0', 'HIV Viral Load', '', '', ''),
(357, 'VI', 'p3_s2_05', '2.5', 'Most recent HIV DNA PCR panel', '', '', ''),
(358, 'VI', 'p3_s2_06', '2.6', 'Second most recent HIV panel', '', '', ''),
(361, 'VI', 'p3_t_s1', 'Criteria 1', 'Are internal quality control procedures routinely conducted for all test methods?', '', '', ''),
(362, 'VI', 'p3_s1_01', '1.1', 'Monitoring of control values', '', '', ''),
(364, 'FR', 'p3_t_s1', 'Criteria 1', 'Are internal quality control procedures routinely conducted for all test methods?', '', '', ''),
(365, 'FR', 'p3_s1_01', '1.1', 'Monitoring of control values', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `lang_word`
--

CREATE TABLE IF NOT EXISTS `lang_word` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trans_word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Has translations of commonly used words' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `lang_word`
--

INSERT INTO `lang_word` (`id`, `tag`, `word`, `trans_word`) VALUES
(1, 'EN', 'Yes', 'Yes'),
(2, 'FR', 'Yes', 'Oui'),
(3, 'VI', 'Yes', 'Có'),
(4, 'EN', 'No', 'No'),
(5, 'FR', 'No', 'Non'),
(6, 'VI', 'No', 'Không');

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
(1, 2, 'Section1.8-P3 Criteria 1'),
(1, 3, 'P3 Criteria 2');

-- --------------------------------------------------------

--
-- Table structure for table `Sheet1`
--

CREATE TABLE IF NOT EXISTS `Sheet1` (
  `tag` varchar(2) DEFAULT NULL,
  `word` varchar(3) DEFAULT NULL,
  `trans_word` varchar(6) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Sheet1`
--

INSERT INTO `Sheet1` (`tag`, `word`, `trans_word`) VALUES
('EN', 'Yes', 'Yes'),
('FR', 'Yes', 'Oui'),
('VI', 'Yes', 'Có'),
('EN', 'No', 'No'),
('FR', 'No', 'Non'),
('VI', 'No', 'Không'),
('EN', 'Yes', 'Yes'),
('FR', 'Yes', 'Oui'),
('VI', 'Yes', 'Có'),
('EN', 'No', 'No'),
('FR', 'No', 'Non'),
('VI', 'No', 'Không');

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
  `row_name` varchar(32) NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stores template info of a display row.' AUTO_INCREMENT=44 ;

--
-- Dumping data for table `tmpl_row`
--

INSERT INTO `tmpl_row` (`id`, `tmpl_head_id`, `row_name`, `varname`, `row_type`, `part`, `level1`, `level2`, `level3`, `level4`, `level5`, `element`, `prefix`, `heading`, `text`, `score`, `page_num`) VALUES
(1, 1, 'p2_t_s01', 'abc', 'sec_head', 2, 1, 0, 0, 0, 0, 0, '1.0', 'Documents & Records', '', 0, 1),
(2, 1, 'p2_s01_01', 'bcd', 'sub_sec_head', 2, 1, 0, 0, 0, 0, 0, '1.1', 'Laboratory Quality Manual', 'Is there a current laboratory manual, composed of the quality management system''s policies and procedures, and has the manual content been communicated to and understood and implemented by all staff?', 4, 1),
(3, 1, ' p2_s01_01_01', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 0, '', '', 'Structure defined per ISO15189, section 4.2.4', 0, 1),
(4, 1, 'p1_pas', 'pas', 'stars', 1, 1, 0, 0, 0, 3, 0, '', '', 'Prior Audit Status', 0, 1),
(5, 1, 'p1_t_01', '', 'sec_head', 1, 0, 0, 0, 0, 0, 0, 'PART I:', 'laboratory profile', '', 0, 1),
(6, 1, 'p1_doa', 'audit_date', 'date', 1, 1, 0, 0, 0, 1, 0, '', '', 'Date of Audit', 0, 1),
(7, 1, 'p1_dola', 'dola', 'date', 1, 1, 0, 0, 0, 2, 0, '', '', 'Date of Last Audit', 0, 1),
(8, 1, 'p1_labaddr', 'labaddr', 'text', 1, 1, 0, 0, 0, 7, 0, '', '', 'Laboratory Address', 0, 1),
(9, 1, 'p1_lablevel', 'lablevel', 'lablevel', 1, 1, 1, 0, 0, 14, 0, '', '', 'Laboratory Level', 0, 1),
(10, 1, 'p1_labaffil', 'labaffil', 'labaffil', 1, 1, 1, 0, 0, 15, 0, '', '', 'Type of Laboratory/Laboratory Affiliation', 0, 1),
(11, 1, 'p1_t_lss', '', 'sec_head_small', 1, 1, 1, 0, 0, 16, 0, '', 'Laboraroty Staffing Summary', '', 0, 1),
(12, 1, 'p1_t_emp', '', 'tab_head3', 1, 1, 1, 0, 0, 17, 0, 'Profession', 'Number of Full Time Employees', 'Adequate for facility operations?', 0, 1),
(13, 1, 'p1_dg_hps', 'dg_hps', 'pinfo', 1, 1, 1, 0, 0, 18, 0, '', '', 'Degree Holding Professional Staff', 0, 1),
(14, 1, ' p2_s01_01_02', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 2, '0', '', 'Quality policy statement that includes scope of service, standard of service, objectives of the quality management system, and management commitment to compliance', 0, 1),
(15, 1, ' p2_s01_01_03', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 3, '0', '', 'Description of the quality management system and the structure of its documentation ', 0, 1),
(16, 1, ' p2_s01_01_04', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 4, '0', '', 'Reference to supporting procedures, including technical procedures', 0, 1),
(17, 1, ' p2_s01_01_05', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 5, '0', '', 'Description of the roles and responsibilities of the laboratory manager, quality manager, and other personnel responsible for ensuring compliance ', 0, 1),
(18, 1, 'p2_s01_01_06', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 6, '0', '', 'Documentation of at least annual management review and approval', 0, 1),
(19, 1, 'p1_cleaner', 'cleaner', 'pinfo', 1, 1, 1, 0, 0, 24, 0, '', '', 'Cleaner', 0, 2),
(20, 1, 'p1_cleaner_dedicated', 'cleaner_dedicated', 'pinfo2_i', 1, 1, 1, 0, 0, 25, 0, '', '', 'Is the driver(s) dedicated to the laboratory only?', 0, 2),
(21, 1, 'p1_cleaner_trained', 'cleaner_trained', 'pinfo2_i', 1, 1, 1, 0, 0, 26, 0, '', '', 'Has the cleaner(s) been trained in safe waste handling?', 0, 2),
(22, 1, 'p1_driver', 'driver', 'pinfo', 1, 1, 1, 0, 0, 27, 0, '', '', 'Driver', 0, 2),
(23, 1, 'p1_driver_dedicated', 'driver_dedicated', 'pinfo2_i', 1, 1, 1, 0, 0, 28, 0, '', '', 'Is the driver(s) dedicated to the laboratory only?', 0, 2),
(24, 1, 'p1_driver_trained', 'driver_trained', 'pinfo2_i', 1, 1, 1, 0, 0, 29, 0, '', '', 'Has the driver(s) been trained in biosafety?', 0, 2),
(25, 1, 'p1_t_it_spec', '', 'info_i', 1, 1, 1, 0, 0, 31, 0, '', '', 'If the laboratory has IT specialists, accountants or non-laboratory-trained management staff, this should be indicated in the description of the organizational structure on the following page.', 0, 2),
(26, 1, 'p1_t_suff_space', '', 'info_bn', 1, 1, 1, 0, 0, 32, 0, '0', 'Does the laboratory have sufficient space, equipment, supplies, personnel, infrastructure, etc. to execute the correct and timely performance of each test and maintain the quality management system?', 'If no, please elaborate in the summary and recommendations section at the end of the checklist.', 0, 2),
(27, 1, 'p1_sufficient_space', 'sufficient_space', 'pinfo2', 1, 1, 1, 0, 0, 33, 0, '', '', 'Sufficient space', 0, 2),
(28, 1, 'p1_sufficient_equipment', 'sufficient_equipment', 'pinfo2', 1, 1, 1, 0, 0, 34, 0, '', '', 'Equipment', 0, 2),
(29, 1, 'p3_t_s1', '', 'criteria_1_heading', 3, 1, 0, 0, 0, 0, 0, 'Criteria 1', 'Are internal quality control procedures routinely conducted for all test methods?', '', 0, 2),
(30, 1, 'p3_s1_01', 'monconval', 'criteria_1_values', 3, 1, 1, 0, 0, 0, 0, '1.1', 'Monitoring of control values', '', 0, 2),
(31, 1, 'p3_s1_02', 'monintstd', 'criteria_1_values', 3, 1, 2, 0, 0, 0, 0, '1.2', 'Monotoring with internal standards', '', 0, 2),
(32, 1, 'p3_s1_03', 'monqualkit', 'criteria_1_values', 3, 1, 3, 0, 0, 0, 0, '1.3', 'Monotoring quality of each new batch of kits', '', 0, 2),
(33, 1, 'p3_s1_04', 'docconkit', 'criteria_1_values', 3, 1, 4, 0, 0, 0, 0, '1.4', 'Documentation of internal controls and kits validation', '', 0, 2),
(34, 1, 'p3_t_s2', '', 'criteria_2_heading', 3, 2, 0, 0, 0, 0, 0, 'Criteria 2', 'Has the laboratory achieved acceptable PT results of at least 80% on the two most recent PT challenges?', '', 0, 3),
(35, 1, 'p3_t_s2_00_01', '', 'panel_heading', 3, 2, 0, 1, 0, 0, 0, '', 'HIV Serology', '', 0, 3),
(36, 1, 'p3_s2_01', 'panel_res', 'panel_result', 3, 2, 1, 0, 0, 0, 0, '2.1', 'Most recent HIV panel', '', 0, 3),
(37, 1, 'p3_s2_02', 'panel_res', 'panel_result', 3, 2, 2, 0, 0, 0, 0, '2.2', 'Second most recent HIV panel', '', 0, 3),
(38, 1, 'p3_t_s2_02_01', '', 'panel_heading', 3, 2, 2, 1, 0, 0, 0, '', 'HIV DNA PCR', '', 0, 3),
(39, 1, 'p3_s2_03', 'panel_res', 'panel_result', 3, 2, 3, 0, 0, 0, 0, '2.3', 'Most recent HIV DNA PCR panel', '', 0, 3),
(40, 1, 'p3_s2_04', 'panel_res', 'panel_result', 3, 2, 4, 0, 0, 0, 0, '2.4', 'Second most recent HIV panel', '', 0, 3),
(41, 1, 'p3_t_s2_04_01', '', 'panel_heading', 3, 2, 4, 1, 0, 0, 0, '0', 'HIV Viral Load', '', 0, 3),
(42, 1, 'p3_s2_05', 'panel_res', 'panel_result', 3, 2, 5, 0, 0, 0, 0, '2.5', 'Most recent HIV DNA PCR panel', '', 0, 3),
(43, 1, 'p3_s2_06', 'panel_res', 'panel_result', 3, 2, 6, 0, 0, 0, 0, '2.6', 'Second most recent HIV panel', '', 0, 3);

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
  `username` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_type` varchar(10) NOT NULL,
  `password` varchar(80) NOT NULL,
  `languages` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Used to store allowed users' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `name`, `user_type`, `password`, `languages`) VALUES
(1, 'hierarchy', 'Hier Archy', 'ADMIN', 'hierarchy', 'EN,FR,VI'),
(2, 'oddeater', 'Odd Eater', 'USER', 'oddeater', 'EN,VI'),
(3, 'analist', 'An ALIST', 'ANALYST', 'analist', 'EN');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Maps userlevel to usertype. Lower user level can do all action of higher levels' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`id`, `user_type`, `description`, `user_level`) VALUES
(1, 'ADMIN', 'Administrator', 1),
(2, 'USER', 'Assessor and general user', 2),
(3, 'ANALYST', 'Analyst ', 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
