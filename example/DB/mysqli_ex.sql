-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2015 at 01:15 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mysqli_ex`
--

-- --------------------------------------------------------

--
-- Table structure for table `sample`
--

CREATE TABLE IF NOT EXISTS `sample` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `age` int(11) NOT NULL,
  `country` text NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `sample`
--

INSERT INTO `sample` (`id`, `name`, `age`, `country`, `type`) VALUES
(1, 'ali', 20, 'egypt', 1),
(2, 'ahmed', 12, 'bahrain', 2),
(3, 'khaled', 30, 'sudan', 1),
(4, 'ibrahim', 50, 'algeria', 2);

-- --------------------------------------------------------

--
-- Table structure for table `Sample2`
--

CREATE TABLE IF NOT EXISTS `Sample2` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Number` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

--
-- Dumping data for table `Sample2`
--

INSERT INTO `Sample2` (`ID`, `Number`) VALUES
(1, 64486),
(2, 64486),
(3, 64486),
(4, 64486),
(5, 64486),
(6, 64486),
(7, 64486),
(8, 64486),
(9, 64486),
(10, 44879),
(11, 81383),
(12, 22115),
(13, 47816),
(14, 84137),
(15, 94810),
(16, 45158),
(17, 15356),
(18, 93525),
(19, 52293),
(20, 65339),
(21, 21735),
(22, 64450),
(23, 21250),
(24, 5947),
(25, 9115),
(26, 80929),
(27, 6107),
(28, 81067),
(29, 19332),
(30, 3720),
(31, 256),
(32, 67142),
(33, 35788),
(34, 40395),
(35, 18023),
(36, 84110),
(37, 17954),
(38, 18478),
(39, 79750),
(40, 2075),
(41, 45425),
(42, 99889),
(43, 60176),
(44, 12116),
(45, 86834),
(46, 1599),
(47, 57741),
(48, 64817),
(49, 2259),
(50, 78937),
(51, 55964),
(52, 11045),
(53, 2365),
(54, 37342),
(55, 68486),
(56, 35901),
(57, 62289),
(58, 79332),
(59, 92067),
(60, 73746),
(61, 97564),
(62, 46478),
(63, 72076),
(64, 36676);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
