-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 16, 2021 at 05:45 PM
-- Server version: 8.0.26-0ubuntu0.21.04.3
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
  `uid` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `geo` varchar(255) DEFAULT NULL,
  `cache` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `uid` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` int UNSIGNED DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE `nodes` (
  `uid` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` int NOT NULL,
  `type` varchar(45) NOT NULL,
  `unit` varchar(12) NOT NULL,
  `photograph` varchar(255) DEFAULT NULL,
  `serial` varchar(45) DEFAULT NULL,
  `mprn` varchar(45) DEFAULT NULL,
  `retention_days` int NOT NULL DEFAULT '0',
  `billed` varchar(45) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `geo` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `account_no` varchar(255) DEFAULT NULL,
  `address` text,
  `cache` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `readings`
--

CREATE TABLE `readings` (
  `uid` int NOT NULL,
  `node` int NOT NULL,
  `date` datetime NOT NULL,
  `reading1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `uid` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`uid`, `name`, `description`, `value`) VALUES
(33, 'node_types', 'Comma separated list of available node types', 'Electric,Gas,Water,Refuse'),
(34, 'node_units', 'Comma separated list of available node units', 'm³,kWh,KG,C'),
(35, 'datetime_format_long', 'The format of long-form dates (2020-11-04 13:45:11).  Uses PHP datetime format [d-m-y]', 'Y-m-d H:i:s'),
(36, 'datetime_format_short', 'The format of short-form dates (2020-11-04).  Uses PHP datetime format [d-m-y]', 'Y-m-d'),
(37, 'site_geolocation', 'lat, long of the default site location', '51.752879, -1.249675'),
(38, 'node_graph_monthly_display', 'The number of months to display in the monthly consumption graph for each node', '12'),
(39, 'node_graph_yearly_display', 'The number of years to display in the yearly consumption graph for each node', '5'),
(40, 'unit_cost_gas', 'Cost per unit for 1m3 of gas', '0.358'),
(41, 'unit_cost_water', 'Cost per unit for 1m3 of water', '0.9'),
(42, 'unit_cost_electric', 'Cost per unit for 1kWh of electricity', '0.127'),
(43, 'unit_co2e_gas', 'kgCO2e/m3 Natural gas using gross calorific value as stated on most energy bills', '2.03473'),
(44, 'unit_co2e_electric', 'kgCO2e/kWh using gross calorific value as stated on most energy bills', '0'),
(45, 'unit_co2e_water', 'kgCO2e/m3 using gross calorific value as stated on most energy bills', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `nodes`
--
ALTER TABLE `nodes`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `readings`
--
ALTER TABLE `readings`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `meter` (`node`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `uid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `uid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nodes`
--
ALTER TABLE `nodes`
  MODIFY `uid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `readings`
--
ALTER TABLE `readings`
  MODIFY `uid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `uid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
