-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 23, 2013 at 02:58 PM
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
  `info` text NOT NULL,
  `score` tinyint(4) NOT NULL,
  `page_num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stores template info of a display row.' AUTO_INCREMENT=44 ;

--
-- Dumping data for table `tmpl_row`
--

INSERT INTO `tmpl_row` (`id`, `tmpl_head_id`, `row_name`, `varname`, `row_type`, `part`, `level1`, `level2`, `level3`, `level4`, `level5`, `element`, `prefix`, `heading`, `text`, `info`, `score`, `page_num`) VALUES
(1, 1, 'p2_t_s01', 'abc', 'sec_head', 2, 1, 0, 0, 0, 0, 0, '1.0', 'Documents & Records', '', '', 0, 1),
(2, 1, 'p2_s01_01', 'bcd', 'sub_sec_head', 2, 1, 0, 0, 0, 0, 0, '1.1', 'Laboratory Quality Manual', 'Is there a current laboratory manual, composed of the quality management system''s policies and procedures, and has the manual content been communicated to and understood and implemented by all staff?', '', 4, 1),
(3, 1, ' p2_s01_01_01', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 0, '', '', 'Structure defined per ISO15189, section 4.2.4', '', 0, 1),
(4, 1, 'p1_pas', 'pas', 'stars', 1, 1, 0, 0, 0, 3, 0, '', '', 'Prior Audit Status', '', 0, 1),
(5, 1, 'p1_t_01', '', 'sec_head', 1, 0, 0, 0, 0, 0, 0, 'PART I:', 'laboratory profile', '', '', 0, 1),
(6, 1, 'p1_doa', 'audit_date', 'date', 1, 1, 0, 0, 0, 1, 0, '', '', 'Date of Audit', '', 0, 1),
(7, 1, 'p1_dola', 'dola', 'date', 1, 1, 0, 0, 0, 2, 0, '', '', 'Date of Last Audit', '', 0, 1),
(8, 1, 'p1_labaddr', 'labaddr', 'text', 1, 1, 0, 0, 0, 7, 0, '', '', 'Laboratory Address', '', 0, 1),
(9, 1, 'p1_lablevel', 'lablevel', 'lablevel', 1, 1, 1, 0, 0, 14, 0, '', '', 'Laboratory Level', '', 0, 1),
(10, 1, 'p1_labaffil', 'labaffil', 'labaffil', 1, 1, 1, 0, 0, 15, 0, '', '', 'Type of Laboratory/Laboratory Affiliation', '', 0, 1),
(11, 1, 'p1_t_lss', '', 'sec_head_small', 1, 1, 1, 0, 0, 16, 0, '', 'Laboraroty Staffing Summary', '', '', 0, 1),
(12, 1, 'p1_t_emp', '', 'tab_head3', 1, 1, 1, 0, 0, 17, 0, 'Profession', 'Number of Full Time Employees', 'Adequate for facility operations?', '', 0, 1),
(13, 1, 'p1_dg_hps', 'dg_hps', 'pinfo', 1, 1, 1, 0, 0, 18, 0, '', '', 'Degree Holding Professional Staff', '', 0, 1),
(14, 1, ' p2_s01_01_02', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 2, '0', '', 'Quality policy statement that includes scope of service, standard of service, objectives of the quality management system, and management commitment to compliance', '', 0, 1),
(15, 1, ' p2_s01_01_03', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 3, '0', '', 'Description of the quality management system and the structure of its documentation ', '', 0, 1),
(16, 1, ' p2_s01_01_04', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 4, '0', '', 'Reference to supporting procedures, including technical procedures', '', 0, 1),
(17, 1, ' p2_s01_01_05', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 5, '0', '', 'Description of the roles and responsibilities of the laboratory manager, quality manager, and other personnel responsible for ensuring compliance ', '', 0, 1),
(18, 1, 'p2_s01_01_06', 'bcd', 'sec_element', 2, 1, 1, 0, 0, 0, 6, '0', '', 'Documentation of at least annual management review and approval', '', 0, 1),
(19, 1, 'p1_cleaner', 'cleaner', 'pinfo', 1, 1, 1, 0, 0, 24, 0, '', '', 'Cleaner', '', 0, 2),
(20, 1, 'p1_cleaner_dedicated', 'cleaner_dedicated', 'pinfo2_i', 1, 1, 1, 0, 0, 25, 0, '', '', 'Is the driver(s) dedicated to the laboratory only?', '', 0, 2),
(21, 1, 'p1_cleaner_trained', 'cleaner_trained', 'pinfo2_i', 1, 1, 1, 0, 0, 26, 0, '', '', 'Has the cleaner(s) been trained in safe waste handling?', '', 0, 2),
(22, 1, 'p1_driver', 'driver', 'pinfo', 1, 1, 1, 0, 0, 27, 0, '', '', 'Driver', '', 0, 2),
(23, 1, 'p1_driver_dedicated', 'driver_dedicated', 'pinfo2_i', 1, 1, 1, 0, 0, 28, 0, '', '', 'Is the driver(s) dedicated to the laboratory only?', '', 0, 2),
(24, 1, 'p1_driver_trained', 'driver_trained', 'pinfo2_i', 1, 1, 1, 0, 0, 29, 0, '', '', 'Has the driver(s) been trained in biosafety?', '', 0, 2),
(25, 1, 'p1_t_it_spec', '', 'info_i', 1, 1, 1, 0, 0, 31, 0, '', '', 'If the laboratory has IT specialists, accountants or non-laboratory-trained management staff, this should be indicated in the description of the organizational structure on the following page.', '', 0, 2),
(26, 1, 'p1_t_suff_space', '', 'info_bn', 1, 1, 1, 0, 0, 32, 0, '0', 'Does the laboratory have sufficient space, equipment, supplies, personnel, infrastructure, etc. to execute the correct and timely performance of each test and maintain the quality management system?', 'If no, please elaborate in the summary and recommendations section at the end of the checklist.', '', 0, 2),
(27, 1, 'p1_sufficient_space', 'sufficient_space', 'pinfo2', 1, 1, 1, 0, 0, 33, 0, '', '', 'Sufficient space', '', 0, 2),
(28, 1, 'p1_sufficient_equipment', 'sufficient_equipment', 'pinfo2', 1, 1, 1, 0, 0, 34, 0, '', '', 'Equipment', '', 0, 2),
(29, 1, 'p3_t_s1', '', 'criteria_1_heading', 3, 1, 0, 0, 0, 0, 0, 'Criteria 1', 'Are internal quality control procedures routinely conducted for all test methods?', '', '', 0, 2),
(30, 1, 'p3_s1_01', 'monconval', 'criteria_1_values', 3, 1, 1, 0, 0, 0, 0, '1.1', 'Monitoring of control values', '', '', 0, 2),
(31, 1, 'p3_s1_02', 'monintstd', 'criteria_1_values', 3, 1, 2, 0, 0, 0, 0, '1.2', 'Monotoring with internal standards', '', '', 0, 2),
(32, 1, 'p3_s1_03', 'monqualkit', 'criteria_1_values', 3, 1, 3, 0, 0, 0, 0, '1.3', 'Monotoring quality of each new batch of kits', '', '', 0, 2),
(33, 1, 'p3_s1_04', 'docconkit', 'criteria_1_values', 3, 1, 4, 0, 0, 0, 0, '1.4', 'Documentation of internal controls and kits validation', '', '', 0, 2),
(34, 1, 'p3_t_s2', '', 'criteria_2_heading', 3, 2, 0, 0, 0, 0, 0, 'Criteria 2', 'Has the laboratory achieved acceptable PT results of at least 80% on the two most recent PT challenges?', '', '', 0, 3),
(35, 1, 'p3_t_s2_00_01', '', 'panel_heading', 3, 2, 0, 1, 0, 0, 0, '', 'HIV Serology', '', '', 0, 3),
(36, 1, 'p3_s2_01', 'panel_res', 'panel_result', 3, 2, 1, 0, 0, 0, 0, '2.1', 'Most recent HIV panel', '', '', 0, 3),
(37, 1, 'p3_s2_02', 'panel_res', 'panel_result', 3, 2, 2, 0, 0, 0, 0, '2.2', 'Second most recent HIV panel', '', '', 0, 3),
(38, 1, 'p3_t_s2_02_01', '', 'panel_heading', 3, 2, 2, 1, 0, 0, 0, '', 'HIV DNA PCR', '', '', 0, 3),
(39, 1, 'p3_s2_03', 'panel_res', 'panel_result', 3, 2, 3, 0, 0, 0, 0, '2.3', 'Most recent HIV DNA PCR panel', '', '', 0, 3),
(40, 1, 'p3_s2_04', 'panel_res', 'panel_result', 3, 2, 4, 0, 0, 0, 0, '2.4', 'Second most recent HIV panel', '', '', 0, 3),
(41, 1, 'p3_t_s2_04_01', '', 'panel_heading', 3, 2, 4, 1, 0, 0, 0, '0', 'HIV Viral Load', '', '', 0, 3),
(42, 1, 'p3_s2_05', 'panel_res', 'panel_result', 3, 2, 5, 0, 0, 0, 0, '2.5', 'Most recent HIV DNA PCR panel', '', '', 0, 3),
(43, 1, 'p3_s2_06', 'panel_res', 'panel_result', 3, 2, 6, 0, 0, 0, 0, '2.6', 'Second most recent HIV panel', '', '', 0, 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
