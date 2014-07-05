-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 05, 2014 at 05:10 PM
-- Server version: 5.5.9
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dnb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dnb_geo`
--

CREATE TABLE `dnb_geo` (
  `lat` float(32,28) NOT NULL,
  `lng` float(32,28) NOT NULL,
  `city` text CHARACTER SET utf8 NOT NULL,
  `desc` text CHARACTER SET utf8 NOT NULL,
  `color` text CHARACTER SET utf8 NOT NULL,
  `source` text CHARACTER SET utf8 NOT NULL,
  `precision` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnb_job`
--

CREATE TABLE `dnb_job` (
  `id` text CHARACTER SET utf8 NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL,
  `hits` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`(10))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnb_parser`
--

CREATE TABLE `dnb_parser` (
  `id` int(11) NOT NULL,
  `character` bigint(20) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnb_persons`
--

CREATE TABLE `dnb_persons` (
  `id` text CHARACTER SET utf8 NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL,
  `life_start` int(11) NOT NULL,
  `life_end` int(11) NOT NULL,
  `valid` int(11) NOT NULL,
  `job_ids` text NOT NULL,
  UNIQUE KEY `id` (`id`(10))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnb_person_job`
--

CREATE TABLE `dnb_person_job` (
  `person_id` text NOT NULL,
  `job_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnb_person_place`
--

CREATE TABLE `dnb_person_place` (
  `person_id` text NOT NULL,
  `place_id` text NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnb_places`
--

CREATE TABLE `dnb_places` (
  `id` text CHARACTER SET utf8 NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL,
  `latitude` float(32,28) NOT NULL,
  `longitude` float(32,28) NOT NULL,
  `hits` int(11) NOT NULL,
  `checked` int(11) NOT NULL,
  `validconversion` tinyint(1) NOT NULL,
  `x` double(21,12) NOT NULL,
  `y` double(21,12) NOT NULL,
  `x0` double(21,12) NOT NULL,
  `y0` double(21,12) NOT NULL,
  `z1` bigint(20) NOT NULL,
  `z2` bigint(20) NOT NULL,
  `z3` bigint(20) NOT NULL,
  `z4` bigint(20) NOT NULL,
  `z5` bigint(20) NOT NULL,
  `z6` bigint(20) NOT NULL,
  `z7` bigint(20) NOT NULL,
  `z8` bigint(20) NOT NULL,
  `z9` bigint(20) NOT NULL,
  `z10` bigint(20) NOT NULL,
  `z11` bigint(20) NOT NULL,
  `z12` bigint(20) NOT NULL,
  `z13` bigint(20) NOT NULL,
  `z14` bigint(20) NOT NULL,
  `z15` bigint(20) NOT NULL,
  `z16` bigint(20) NOT NULL,
  `z17` bigint(20) NOT NULL,
  `z18` bigint(20) NOT NULL,
  `z19` bigint(20) NOT NULL,
  `z20` bigint(20) NOT NULL,
  `zh1` bigint(20) NOT NULL,
  `zh2` bigint(20) NOT NULL,
  `zh3` bigint(20) NOT NULL,
  `zh4` bigint(20) NOT NULL,
  `zh5` bigint(20) NOT NULL,
  `zh6` bigint(20) NOT NULL,
  `zh7` bigint(20) NOT NULL,
  `zh8` bigint(20) NOT NULL,
  `zh9` bigint(20) NOT NULL,
  `zh10` bigint(20) NOT NULL,
  `zh11` bigint(20) NOT NULL,
  `zh12` bigint(20) NOT NULL,
  `zh13` bigint(20) NOT NULL,
  `zh14` bigint(20) NOT NULL,
  `zh15` bigint(20) NOT NULL,
  `zh16` bigint(20) NOT NULL,
  `zh17` bigint(20) NOT NULL,
  `zh18` bigint(20) NOT NULL,
  `zh19` bigint(20) NOT NULL,
  `zh20` bigint(20) NOT NULL,
  `hits_0` int(11) NOT NULL,
  `hits_10` int(11) NOT NULL,
  `hits_20` int(11) NOT NULL,
  `hits_30` int(11) NOT NULL,
  `hits_40` int(11) NOT NULL,
  `hits_50` int(11) NOT NULL,
  `hits_60` int(11) NOT NULL,
  `hits_70` int(11) NOT NULL,
  `hits_80` int(11) NOT NULL,
  `hits_90` int(11) NOT NULL,
  `hits_100` int(11) NOT NULL,
  `hits_110` int(11) NOT NULL,
  `hits_120` int(11) NOT NULL,
  `hits_130` int(11) NOT NULL,
  `hits_140` int(11) NOT NULL,
  `hits_150` int(11) NOT NULL,
  `hits_160` int(11) NOT NULL,
  `hits_170` int(11) NOT NULL,
  `hits_180` int(11) NOT NULL,
  `hits_190` int(11) NOT NULL,
  `hits_200` int(11) NOT NULL,
  `hits_210` int(11) NOT NULL,
  `hits_220` int(11) NOT NULL,
  `hits_230` int(11) NOT NULL,
  `hits_240` int(11) NOT NULL,
  `hits_250` int(11) NOT NULL,
  `hits_260` int(11) NOT NULL,
  `hits_270` int(11) NOT NULL,
  `hits_280` int(11) NOT NULL,
  `hits_290` int(11) NOT NULL,
  `hits_300` int(11) NOT NULL,
  `hits_310` int(11) NOT NULL,
  `hits_320` int(11) NOT NULL,
  `hits_330` int(11) NOT NULL,
  `hits_340` int(11) NOT NULL,
  `hits_350` int(11) NOT NULL,
  `hits_360` int(11) NOT NULL,
  `hits_370` int(11) NOT NULL,
  `hits_380` int(11) NOT NULL,
  `hits_390` int(11) NOT NULL,
  `hits_400` int(11) NOT NULL,
  `hits_410` int(11) NOT NULL,
  `hits_420` int(11) NOT NULL,
  `hits_430` int(11) NOT NULL,
  `hits_440` int(11) NOT NULL,
  `hits_450` int(11) NOT NULL,
  `hits_460` int(11) NOT NULL,
  `hits_470` int(11) NOT NULL,
  `hits_480` int(11) NOT NULL,
  `hits_490` int(11) NOT NULL,
  `hits_500` int(11) NOT NULL,
  `hits_510` int(11) NOT NULL,
  `hits_520` int(11) NOT NULL,
  `hits_530` int(11) NOT NULL,
  `hits_540` int(11) NOT NULL,
  `hits_550` int(11) NOT NULL,
  `hits_560` int(11) NOT NULL,
  `hits_570` int(11) NOT NULL,
  `hits_580` int(11) NOT NULL,
  `hits_590` int(11) NOT NULL,
  `hits_600` int(11) NOT NULL,
  `hits_610` int(11) NOT NULL,
  `hits_620` int(11) NOT NULL,
  `hits_630` int(11) NOT NULL,
  `hits_640` int(11) NOT NULL,
  `hits_650` int(11) NOT NULL,
  `hits_660` int(11) NOT NULL,
  `hits_670` int(11) NOT NULL,
  `hits_680` int(11) NOT NULL,
  `hits_690` int(11) NOT NULL,
  `hits_700` int(11) NOT NULL,
  `hits_710` int(11) NOT NULL,
  `hits_720` int(11) NOT NULL,
  `hits_730` int(11) NOT NULL,
  `hits_740` int(11) NOT NULL,
  `hits_750` int(11) NOT NULL,
  `hits_760` int(11) NOT NULL,
  `hits_770` int(11) NOT NULL,
  `hits_780` int(11) NOT NULL,
  `hits_790` int(11) NOT NULL,
  `hits_800` int(11) NOT NULL,
  `hits_810` int(11) NOT NULL,
  `hits_820` int(11) NOT NULL,
  `hits_830` int(11) NOT NULL,
  `hits_840` int(11) NOT NULL,
  `hits_850` int(11) NOT NULL,
  `hits_860` int(11) NOT NULL,
  `hits_870` int(11) NOT NULL,
  `hits_880` int(11) NOT NULL,
  `hits_890` int(11) NOT NULL,
  `hits_900` int(11) NOT NULL,
  `hits_910` int(11) NOT NULL,
  `hits_920` int(11) NOT NULL,
  `hits_930` int(11) NOT NULL,
  `hits_940` int(11) NOT NULL,
  `hits_950` int(11) NOT NULL,
  `hits_960` int(11) NOT NULL,
  `hits_970` int(11) NOT NULL,
  `hits_980` int(11) NOT NULL,
  `hits_990` int(11) NOT NULL,
  `hits_1000` int(11) NOT NULL,
  `hits_1010` int(11) NOT NULL,
  `hits_1020` int(11) NOT NULL,
  `hits_1030` int(11) NOT NULL,
  `hits_1040` int(11) NOT NULL,
  `hits_1050` int(11) NOT NULL,
  `hits_1060` int(11) NOT NULL,
  `hits_1070` int(11) NOT NULL,
  `hits_1080` int(11) NOT NULL,
  `hits_1090` int(11) NOT NULL,
  `hits_1100` int(11) NOT NULL,
  `hits_1110` int(11) NOT NULL,
  `hits_1120` int(11) NOT NULL,
  `hits_1130` int(11) NOT NULL,
  `hits_1140` int(11) NOT NULL,
  `hits_1150` int(11) NOT NULL,
  `hits_1160` int(11) NOT NULL,
  `hits_1170` int(11) NOT NULL,
  `hits_1180` int(11) NOT NULL,
  `hits_1190` int(11) NOT NULL,
  `hits_1200` int(11) NOT NULL,
  `hits_1210` int(11) NOT NULL,
  `hits_1220` int(11) NOT NULL,
  `hits_1230` int(11) NOT NULL,
  `hits_1240` int(11) NOT NULL,
  `hits_1250` int(11) NOT NULL,
  `hits_1260` int(11) NOT NULL,
  `hits_1270` int(11) NOT NULL,
  `hits_1280` int(11) NOT NULL,
  `hits_1290` int(11) NOT NULL,
  `hits_1300` int(11) NOT NULL,
  `hits_1310` int(11) NOT NULL,
  `hits_1320` int(11) NOT NULL,
  `hits_1330` int(11) NOT NULL,
  `hits_1340` int(11) NOT NULL,
  `hits_1350` int(11) NOT NULL,
  `hits_1360` int(11) NOT NULL,
  `hits_1370` int(11) NOT NULL,
  `hits_1380` int(11) NOT NULL,
  `hits_1390` int(11) NOT NULL,
  `hits_1400` int(11) NOT NULL,
  `hits_1410` int(11) NOT NULL,
  `hits_1420` int(11) NOT NULL,
  `hits_1430` int(11) NOT NULL,
  `hits_1440` int(11) NOT NULL,
  `hits_1450` int(11) NOT NULL,
  `hits_1460` int(11) NOT NULL,
  `hits_1470` int(11) NOT NULL,
  `hits_1480` int(11) NOT NULL,
  `hits_1490` int(11) NOT NULL,
  `hits_1500` int(11) NOT NULL,
  `hits_1510` int(11) NOT NULL,
  `hits_1520` int(11) NOT NULL,
  `hits_1530` int(11) NOT NULL,
  `hits_1540` int(11) NOT NULL,
  `hits_1550` int(11) NOT NULL,
  `hits_1560` int(11) NOT NULL,
  `hits_1570` int(11) NOT NULL,
  `hits_1580` int(11) NOT NULL,
  `hits_1590` int(11) NOT NULL,
  `hits_1600` int(11) NOT NULL,
  `hits_1610` int(11) NOT NULL,
  `hits_1620` int(11) NOT NULL,
  `hits_1630` int(11) NOT NULL,
  `hits_1640` int(11) NOT NULL,
  `hits_1650` int(11) NOT NULL,
  `hits_1660` int(11) NOT NULL,
  `hits_1670` int(11) NOT NULL,
  `hits_1680` int(11) NOT NULL,
  `hits_1690` int(11) NOT NULL,
  `hits_1700` int(11) NOT NULL,
  `hits_1710` int(11) NOT NULL,
  `hits_1720` int(11) NOT NULL,
  `hits_1730` int(11) NOT NULL,
  `hits_1740` int(11) NOT NULL,
  `hits_1750` int(11) NOT NULL,
  `hits_1760` int(11) NOT NULL,
  `hits_1770` int(11) NOT NULL,
  `hits_1780` int(11) NOT NULL,
  `hits_1790` int(11) NOT NULL,
  `hits_1800` int(11) NOT NULL,
  `hits_1810` int(11) NOT NULL,
  `hits_1820` int(11) NOT NULL,
  `hits_1830` int(11) NOT NULL,
  `hits_1840` int(11) NOT NULL,
  `hits_1850` int(11) NOT NULL,
  `hits_1860` int(11) NOT NULL,
  `hits_1870` int(11) NOT NULL,
  `hits_1880` int(11) NOT NULL,
  `hits_1890` int(11) NOT NULL,
  `hits_1900` int(11) NOT NULL,
  `hits_1910` int(11) NOT NULL,
  `hits_1920` int(11) NOT NULL,
  `hits_1930` int(11) NOT NULL,
  `hits_1940` int(11) NOT NULL,
  `hits_1950` int(11) NOT NULL,
  `hits_1960` int(11) NOT NULL,
  `hits_1970` int(11) NOT NULL,
  `hits_1980` int(11) NOT NULL,
  `hits_1990` int(11) NOT NULL,
  `hits_2000` int(11) NOT NULL,
  `hits_2010` int(11) NOT NULL,
  `hits_2020` int(11) NOT NULL,
  PRIMARY KEY (`id`(10))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `locations_max_table`
--

CREATE TABLE `locations_max_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zoom` int(11) NOT NULL,
  `key` text COLLATE latin1_german2_ci NOT NULL,
  `value` text COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=27513 ;
