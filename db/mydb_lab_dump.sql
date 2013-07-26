-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 26, 2013 at 12:55 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Lab info ' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `lab`
--

INSERT INTO `lab` (`id`, `labname`, `labnum`, `description`, `street`, `street2`, `city`, `state`, `country`, `county_code`, `postcode`, `level`, `affiliation`) VALUES
(1, 'Best Lab, Inc', 'lab-007', 'The best lab in all of Africa capable of doing almost anything.', 'No. 19 Fifth Link Road ', 'Cantonments', 'Accra', '', 'Ghana', 'GH', '', 'R', 'R'),
(2, 'Bongo Mongo', '340dhjj', '', '', '', '', '', '', 'USA', '', '', ''),
(3, 'Fast Bux', '857hh', '', '', '', '', '', '', 'KEN', '', '', ''),
(4, 'ruby con', '987 tyr', '', '', '', '', '', '', 'EGY', '', '', ''),
(5, 'Akbar labs', '332gtrf', '', '', '', '', '', '', 'OMA', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
