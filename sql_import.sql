-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 03, 2020 at 08:44 AM
-- Server version: 5.7.29-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `readings`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `other` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `meters`
--

CREATE TABLE `meters` (
  `uid` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `location` int(11) NOT NULL,
  `type` varchar(45) NOT NULL,
  `photograph` varchar(255) DEFAULT NULL,
  `serial` varchar(45) DEFAULT NULL,
  `billed` varchar(45) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `readings`
--

CREATE TABLE `readings` (
  `uid` int(11) NOT NULL,
  `meter` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `reading1` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `readings_by_month`
-- (See below for the actual view)
--
CREATE TABLE `readings_by_month` (
`meter` int(11)
,`reading1` int(11)
,`year` int(4)
,`month` int(2)
,`location` int(11)
,`type` varchar(45)
);

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE `site` (
  `uid` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `readings_by_month`
--
DROP TABLE IF EXISTS `readings_by_month`;

CREATE ALGORITHM=UNDEFINED DEFINER=`itsupport`@`localhost` SQL SECURITY DEFINER VIEW `readings_by_month`  AS  select `s`.`meter` AS `meter`,max(`s`.`reading1`) AS `reading1`,`s`.`year` AS `year`,`s`.`month` AS `month`,`s`.`location` AS `location`,`s`.`type` AS `type` from (select `meter` AS `meter`,year(`date`) AS `year`,month(`date`) AS `month`,`reading1` AS `reading1`,`meters`.`location` AS `location`,`meters`.`type` AS `type` from (`readings` join `meters`) where (`meter` = `meters`.`uid`)) `s` group by `s`.`meter`,`s`.`year`,`s`.`month` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `meters`
--
ALTER TABLE `meters`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `readings`
--
ALTER TABLE `readings`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meters`
--
ALTER TABLE `meters`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `readings`
--
ALTER TABLE `readings`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site`
--
ALTER TABLE `site`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
