-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 23, 2015 at 03:38 PM
-- Server version: 5.1.67
-- PHP Version: 5.4.14-1~lucid+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `optimus`
--

-- --------------------------------------------------------

--
-- Table structure for table `ActionPlans`
--

CREATE TABLE IF NOT EXISTS `ActionPlans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `fk_Building` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_59FEF3172A31405C` (`fk_Building`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

--
-- Dumping data for table `ActionPlans`
--

-- --------------------------------------------------------

--
-- Table structure for table `APCalculation`
--

CREATE TABLE IF NOT EXISTS `APCalculation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_actionplan` int(11) DEFAULT NULL,
  `starting_date` datetime NOT NULL,
  `date_creation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_48A803EB5DA51654` (`fk_actionplan`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=815 ;

--
-- Dumping data for table `APCalculation`
--

-- --------------------------------------------------------

--
-- Table structure for table `APPVMOutput`
--

CREATE TABLE IF NOT EXISTS `APPVMOutput` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ap_calculation` int(11) DEFAULT NULL,
  `hour` datetime NOT NULL,
  `alarm_power` int(11) NOT NULL,
  `alarm_temperature` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7B22EE0EDC5C9F14` (`fk_ap_calculation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=337 ;

--
-- Dumping data for table `APPVMOutput`
--

-- --------------------------------------------------------

--
-- Table structure for table `APPVMOutputDay`
--

CREATE TABLE IF NOT EXISTS `APPVMOutputDay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ap_calculation` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2EEDD87DC5C9F14` (`fk_ap_calculation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=337 ;

--
-- Dumping data for table `APPVMOutputDay`
--

-- --------------------------------------------------------

--
-- Table structure for table `APPVOutput`
--

CREATE TABLE IF NOT EXISTS `APPVOutput` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ap_calculation` int(11) DEFAULT NULL,
  `hour` datetime NOT NULL,
  `energy_price` double NOT NULL,
  `energy_price_selling` double NOT NULL,
  `energy_production` double NOT NULL,
  `consumption` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F33DADEDC5C9F14` (`fk_ap_calculation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7897 ;

--
-- Dumping data for table `APPVOutput`
--


-- --------------------------------------------------------

--
-- Table structure for table `APPVOutputDay`
--

CREATE TABLE IF NOT EXISTS `APPVOutputDay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ap_calculation` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `status` int(11) NOT NULL,
  `strategy` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9EFD3B46DC5C9F14` (`fk_ap_calculation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=344 ;

--
-- Dumping data for table `APPVOutputDay`
--

-- --------------------------------------------------------

--
-- Table structure for table `APSensors`
--

CREATE TABLE IF NOT EXISTS `APSensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_actionplan` int(11) DEFAULT NULL,
  `fk_sensor` int(11) DEFAULT NULL,
  `fk_BuildingPartitioning` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F8C4EC5E5DA51654` (`fk_actionplan`),
  KEY `IDX_F8C4EC5E9C287F20` (`fk_sensor`),
  KEY `IDX_F8C4EC5EA19DCE55` (`fk_BuildingPartitioning`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=23 ;

--
-- Dumping data for table `APSensors`
--

-- --------------------------------------------------------

--
-- Table structure for table `APSwitchOutput`
--

CREATE TABLE IF NOT EXISTS `APSwitchOutput` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_ap_calculation` int(11) DEFAULT NULL,
  `typeSensor` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start` time NOT NULL,
  `stop` time NOT NULL,
  `day` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_844B3F0FDC5C9F14` (`fk_ap_calculation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `APSwitchOutput`
--


-- --------------------------------------------------------

--
-- Table structure for table `Building`
--

CREATE TABLE IF NOT EXISTS `Building` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `use_building` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `year_of_construction` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `surface` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `occupation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `energy_rating` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `electricity_consumption` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gas_consumption` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `energy_production_from_RES` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `electricity_energy_cost` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gas_energy_cost` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Building`
--

-- --------------------------------------------------------

--
-- Table structure for table `BuildingPartitioning`
--

CREATE TABLE IF NOT EXISTS `BuildingPartitioning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partition_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fk_Building` int(11) DEFAULT NULL,
  `fk_BuildingPartitioning` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7F7FC392A31405C` (`fk_Building`),
  KEY `IDX_7F7FC39A19DCE55` (`fk_BuildingPartitioning`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=67 ;

--
-- Dumping data for table `BuildingPartitioning`
--


-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE IF NOT EXISTS `Events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `textEvent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `context` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `fk_Building` int(11) DEFAULT NULL,
  `id_context` int(11) DEFAULT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_542B527C1AD0877` (`fk_user`),
  KEY `IDX_542B527C2A31405C` (`fk_Building`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=178 ;

--
-- Dumping data for table `Events`
--

-- --------------------------------------------------------

--
-- Table structure for table `Prediction`
--

CREATE TABLE IF NOT EXISTS `Prediction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_create` datetime NOT NULL,
  `date_user` datetime NOT NULL,
  `fk_Building` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_79646C182A31405C` (`fk_Building`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=458 ;

--
-- Dumping data for table `Prediction`
--


-- --------------------------------------------------------

--
-- Table structure for table `RegisterPredictions`
--

CREATE TABLE IF NOT EXISTS `RegisterPredictions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_sensor` int(11) DEFAULT NULL,
  `fk_prediction` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_185961679C287F20` (`fk_sensor`),
  KEY `IDX_18596167C657A55B` (`fk_prediction`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10072 ;

--
-- Dumping data for table `RegisterPredictions`
--

-- --------------------------------------------------------

--
-- Table structure for table `Sensor`
--

CREATE TABLE IF NOT EXISTS `Sensor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fk_Building` int(11) DEFAULT NULL,
  `urlService` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastData` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `units` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BB2A12862A31405C` (`fk_Building`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `Sensor`
--

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rol` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`id`, `name`, `lastname`, `rol`, `password`, `salt`, `is_active`) VALUES
(1, 'admin', 'admin', 1, 'd033e22ae348aeb5660fc2140aec35850c4da997', '', 0),
(2, 'user', 'user', 0, '12dea96fec20593566ab75692c9949596833adc9', '', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ActionPlans`
--
ALTER TABLE `ActionPlans`
  ADD CONSTRAINT `FK_59FEF3172A31405C` FOREIGN KEY (`fk_Building`) REFERENCES `Building` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APCalculation`
--
ALTER TABLE `APCalculation`
  ADD CONSTRAINT `FK_48A803EB5DA51654` FOREIGN KEY (`fk_actionplan`) REFERENCES `ActionPlans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APPVMOutput`
--
ALTER TABLE `APPVMOutput`
  ADD CONSTRAINT `FK_7B22EE0EDC5C9F14` FOREIGN KEY (`fk_ap_calculation`) REFERENCES `APCalculation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APPVMOutputDay`
--
ALTER TABLE `APPVMOutputDay`
  ADD CONSTRAINT `FK_C2EEDD87DC5C9F14` FOREIGN KEY (`fk_ap_calculation`) REFERENCES `APCalculation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APPVOutput`
--
ALTER TABLE `APPVOutput`
  ADD CONSTRAINT `FK_8F33DADEDC5C9F14` FOREIGN KEY (`fk_ap_calculation`) REFERENCES `APCalculation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APPVOutputDay`
--
ALTER TABLE `APPVOutputDay`
  ADD CONSTRAINT `FK_9EFD3B46DC5C9F14` FOREIGN KEY (`fk_ap_calculation`) REFERENCES `APCalculation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APSensors`
--
ALTER TABLE `APSensors`
  ADD CONSTRAINT `FK_F8C4EC5E5DA51654` FOREIGN KEY (`fk_actionplan`) REFERENCES `ActionPlans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F8C4EC5E9C287F20` FOREIGN KEY (`fk_sensor`) REFERENCES `Sensor` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F8C4EC5EA19DCE55` FOREIGN KEY (`fk_BuildingPartitioning`) REFERENCES `BuildingPartitioning` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `APSwitchOutput`
--
ALTER TABLE `APSwitchOutput`
  ADD CONSTRAINT `FK_844B3F0FDC5C9F14` FOREIGN KEY (`fk_ap_calculation`) REFERENCES `APCalculation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `BuildingPartitioning`
--
ALTER TABLE `BuildingPartitioning`
  ADD CONSTRAINT `FK_7F7FC392A31405C` FOREIGN KEY (`fk_Building`) REFERENCES `Building` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_7F7FC39A19DCE55` FOREIGN KEY (`fk_BuildingPartitioning`) REFERENCES `BuildingPartitioning` (`id`);

--
-- Constraints for table `Events`
--
ALTER TABLE `Events`
  ADD CONSTRAINT `FK_542B527C1AD0877` FOREIGN KEY (`fk_user`) REFERENCES `Users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_542B527C2A31405C` FOREIGN KEY (`fk_Building`) REFERENCES `Building` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Prediction`
--
ALTER TABLE `Prediction`
  ADD CONSTRAINT `FK_79646C182A31405C` FOREIGN KEY (`fk_Building`) REFERENCES `Building` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `RegisterPredictions`
--
ALTER TABLE `RegisterPredictions`
  ADD CONSTRAINT `FK_185961679C287F20` FOREIGN KEY (`fk_sensor`) REFERENCES `Sensor` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_18596167C657A55B` FOREIGN KEY (`fk_prediction`) REFERENCES `Prediction` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Sensor`
--
ALTER TABLE `Sensor`
  ADD CONSTRAINT `FK_BB2A12862A31405C` FOREIGN KEY (`fk_Building`) REFERENCES `Building` (`id`) ON DELETE CASCADE;
