-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2016 at 11:53 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `citytool_v2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `actionplan`
--

CREATE TABLE `actionplan` (
  `id` int(11) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `heating_min` float NOT NULL,
  `cooling_min` float NOT NULL,
  `other_min` float NOT NULL,
  `cost_min` float NOT NULL,
  `res_min` float NOT NULL,
  `active` int(11) NOT NULL,
  `heating_max` float NOT NULL,
  `cooling_max` float NOT NULL,
  `other_max` float NOT NULL,
  `cost_max` float NOT NULL,
  `res_max` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `actionplan`
--

INSERT INTO `actionplan` (`id`, `description`, `type`, `heating_min`, `cooling_min`, `other_min`, `cost_min`, `res_min`, `active`, `heating_max`, `cooling_max`, `other_max`, `cost_max`, `res_max`) VALUES
(1, 'Management of the air side economizer', 'consumption', 5, 10, 0, 0, 0, 1, 10, 20, 0, 0, 0),
(2, 'Scheduling and management of the occupancy', 'consumption', 2, 5, 0, 0, 0, 1, 4, 9, 0, 0, 0),
(3, 'Scheduling the set-point temperature', 'consumption', 5, 5, 0, 0, 0, 1, 9, 9, 0, 0, 0),
(4, 'Scheduling the sale/consumption of the electricity produced through the PV system', 'cost', 0, 0, 0, 5, 0, 1, 0, 0, 0, 10, 0),
(5, 'Scheduling the PV maintenance', 'production', 0, 0, 0, 0, 3, 1, 0, 0, 0, 0, 8),
(6, 'Scheduling the ON/OFF of the heating system', 'consumption', 5, 0, 0, 0, 0, 1, 10, 0, 0, 0, 0),
(8, 'Scheduling the operation of heating and electricity systems towards energy cost optimization', 'cost', 0, 0, 0, 5, 0, 1, 0, 0, 0, 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `building`
--

CREATE TABLE `building` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building_actionplans`
--

CREATE TABLE `building_actionplans` (
  `submission_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `actionplan_id` int(11) NOT NULL,
  `minmax` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'min'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building_consumption`
--

CREATE TABLE `building_consumption` (
  `submission_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `heating` int(11) NOT NULL,
  `cooling` int(11) NOT NULL,
  `other` int(11) NOT NULL,
  `included` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building_prices`
--

CREATE TABLE `building_prices` (
  `submission_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `electricity` float NOT NULL,
  `naturalGas` float NOT NULL,
  `fuel` float NOT NULL,
  `other` float NOT NULL,
  `included` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building_production`
--

CREATE TABLE `building_production` (
  `submission_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `production` int(11) NOT NULL,
  `included` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building_sources`
--

CREATE TABLE `building_sources` (
  `submission_id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `electricity_heating` float NOT NULL,
  `electricity_cooling` float NOT NULL,
  `electricity_other` float NOT NULL,
  `naturalGas_heating` float NOT NULL,
  `naturalGas_cooling` float NOT NULL,
  `naturalGas_other` float NOT NULL,
  `fuel_heating` float NOT NULL,
  `fuel_cooling` float NOT NULL,
  `fuel_other` float NOT NULL,
  `other_heating` float NOT NULL,
  `other_cooling` float NOT NULL,
  `other_other` float NOT NULL,
  `included` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emissionfactors`
--

CREATE TABLE `emissionfactors` (
  `submission_id` int(11) NOT NULL,
  `electricity` float NOT NULL,
  `naturalGas` float NOT NULL,
  `fuel` float NOT NULL,
  `other` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `emissionfactors`
--

INSERT INTO `emissionfactors` (`submission_id`, `electricity`, `naturalGas`, `fuel`, `other`) VALUES
(0, 1.149, 0.0004, 0.267, 0.0001);

-- --------------------------------------------------------

--
-- Table structure for table `results_current`
--

CREATE TABLE `results_current` (
  `submission_id` int(11) NOT NULL,
  `consumption` float NOT NULL,
  `consumption_percent` float NOT NULL,
  `emissions` float NOT NULL,
  `emissions_percent` float NOT NULL,
  `cost` float NOT NULL,
  `cost_percent` float NOT NULL,
  `res` float NOT NULL,
  `res_percent` float NOT NULL,
  `view` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results_initial`
--

CREATE TABLE `results_initial` (
  `submission_id` int(11) NOT NULL,
  `view` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `consumption` float NOT NULL,
  `emissions` float NOT NULL,
  `cost` float NOT NULL,
  `res` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results_projected`
--

CREATE TABLE `results_projected` (
  `submission_id` int(11) NOT NULL,
  `consumption` float NOT NULL,
  `consumption_percent` float NOT NULL,
  `emissions` float NOT NULL,
  `emissions_percent` float NOT NULL,
  `cost` float NOT NULL,
  `cost_percent` float NOT NULL,
  `res` float NOT NULL,
  `res_percent` float NOT NULL,
  `view` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE `submission` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `date` date NOT NULL,
  `visibility` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `baseline` int(11) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `targets`
--

CREATE TABLE `targets` (
  `city_id` int(11) NOT NULL,
  `consumption` float NOT NULL,
  `emissions` float NOT NULL,
  `cost` float NOT NULL,
  `res` float NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `type`, `email`, `firstname`, `lastname`, `active`) VALUES
(1, 'admin', 'admin', 'user', 'admin@domain.com', 'admin', 'admin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actionplan`
--
ALTER TABLE `actionplan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `building_actionplans`
--
ALTER TABLE `building_actionplans`
  ADD PRIMARY KEY (`actionplan_id`,`building_id`,`submission_id`);

--
-- Indexes for table `building_consumption`
--
ALTER TABLE `building_consumption`
  ADD PRIMARY KEY (`submission_id`,`building_id`);

--
-- Indexes for table `building_prices`
--
ALTER TABLE `building_prices`
  ADD PRIMARY KEY (`submission_id`,`building_id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `building_production`
--
ALTER TABLE `building_production`
  ADD PRIMARY KEY (`submission_id`,`building_id`);

--
-- Indexes for table `building_sources`
--
ALTER TABLE `building_sources`
  ADD PRIMARY KEY (`submission_id`,`building_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emissionfactors`
--
ALTER TABLE `emissionfactors`
  ADD PRIMARY KEY (`submission_id`);

--
-- Indexes for table `results_current`
--
ALTER TABLE `results_current`
  ADD PRIMARY KEY (`submission_id`,`view`);

--
-- Indexes for table `results_initial`
--
ALTER TABLE `results_initial`
  ADD PRIMARY KEY (`submission_id`,`view`);

--
-- Indexes for table `results_projected`
--
ALTER TABLE `results_projected`
  ADD PRIMARY KEY (`submission_id`,`view`);

--
-- Indexes for table `submission`
--
ALTER TABLE `submission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `targets`
--
ALTER TABLE `targets`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actionplan`
--
ALTER TABLE `actionplan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `building`
--
ALTER TABLE `building`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT for table `submission`
--
ALTER TABLE `submission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
