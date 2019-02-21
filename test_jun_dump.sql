-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.58 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for test_jun
CREATE DATABASE IF NOT EXISTS `test_jun` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `test_jun`;

-- Dumping structure for table test_jun.forecast
CREATE TABLE IF NOT EXISTS `forecast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forecast_date` date NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `temperature` float NOT NULL,
  `humidity` int(11) NOT NULL,
  `pressure` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `forecast_date_city_name` (`forecast_date`,`city_name`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- Dumping data for table test_jun.forecast: ~36 rows (approximately)
DELETE FROM `forecast`;
/*!40000 ALTER TABLE `forecast` DISABLE KEYS */;
INSERT INTO `forecast` (`id`, `forecast_date`, `city_name`, `temperature`, `humidity`, `pressure`) VALUES
	(86, '2019-02-21', 'kyiv', 267.57, 76, 1018.16),
	(87, '2019-02-22', 'kyiv', 266.073, 78, 1027.6),
	(88, '2019-02-23', 'kyiv', 263.303, 80, 1037.89),
	(89, '2019-02-24', 'kyiv', 265.754, 85, 1031.27),
	(90, '2019-02-25', 'kyiv', 273.426, 94, 1020.1),
	(91, '2019-02-26', 'kyiv', 274.282, 93, 1018.36);
/*!40000 ALTER TABLE `forecast` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
