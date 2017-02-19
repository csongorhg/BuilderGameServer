-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.1.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Verzió:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for spinnerserver
CREATE DATABASE IF NOT EXISTS `spinnerserver` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci */;
USE `spinnerserver`;


-- Dumping structure for tábla spinnerserver.battle
CREATE TABLE IF NOT EXISTS `battle` (
  `attackerid` int(11) NOT NULL,
  `defenderid` int(11) NOT NULL,
  `attackeriswinner` tinyint(1) DEFAULT NULL,
  `newattackerdata` int(11) DEFAULT NULL,
  `newdefenderdata` int(11) DEFAULT NULL,
  PRIMARY KEY (`attackerid`),
  UNIQUE KEY `defenderid_UNIQUE` (`defenderid`),
  UNIQUE KEY `newattackerdata_UNIQUE` (`newattackerdata`),
  UNIQUE KEY `newdefenderdata_UNIQUE` (`newdefenderdata`),
  CONSTRAINT `to_attacker_user` FOREIGN KEY (`attackerid`) REFERENCES `online` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `to_defender_user` FOREIGN KEY (`defenderid`) REFERENCES `online` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `to_new_attacker_data` FOREIGN KEY (`newattackerdata`) REFERENCES `data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `to_new_defender_data` FOREIGN KEY (`newdefenderdata`) REFERENCES `data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- Dumping data for table spinnerserver.battle: ~0 rows (approximately)
/*!40000 ALTER TABLE `battle` DISABLE KEYS */;
INSERT INTO `battle` (`attackerid`, `defenderid`, `attackeriswinner`, `newattackerdata`, `newdefenderdata`) VALUES
	(1, 1, 1, 2, 1);
/*!40000 ALTER TABLE `battle` ENABLE KEYS */;


-- Dumping structure for tábla spinnerserver.data
CREATE TABLE IF NOT EXISTS `data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `soldier` int(11) DEFAULT NULL,
  `gold` int(11) DEFAULT NULL,
  `stb` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- Dumping data for table spinnerserver.data: ~2 rows (approximately)
/*!40000 ALTER TABLE `data` DISABLE KEYS */;
INSERT INTO `data` (`id`, `soldier`, `gold`, `stb`) VALUES
	(1, 222, 2222, 2222),
	(2, 3333, 333, 333);
/*!40000 ALTER TABLE `data` ENABLE KEYS */;


-- Dumping structure for tábla spinnerserver.online
CREATE TABLE IF NOT EXISTS `online` (
  `userid` int(11) NOT NULL,
  `lasthellotime` datetime NOT NULL,
  `offensedata` int(11) NOT NULL,
  `defensedata` int(11) NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `offensedata_UNIQUE` (`offensedata`),
  UNIQUE KEY `defensedata_UNIQUE` (`defensedata`),
  CONSTRAINT `id` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `to_data_defensive` FOREIGN KEY (`defensedata`) REFERENCES `data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `to_data_offensive` FOREIGN KEY (`offensedata`) REFERENCES `data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- Dumping data for table spinnerserver.online: ~0 rows (approximately)
/*!40000 ALTER TABLE `online` DISABLE KEYS */;
INSERT INTO `online` (`userid`, `lasthellotime`, `offensedata`, `defensedata`) VALUES
	(1, '2017-02-19 18:22:49', 1, 2),
	(2, '2017-02-19 18:23:05', 2, 1);
/*!40000 ALTER TABLE `online` ENABLE KEYS */;


-- Dumping structure for tábla spinnerserver.user
CREATE TABLE IF NOT EXISTS `user` (
  `name` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- Dumping data for table spinnerserver.user: ~3 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`name`, `password`, `id`) VALUES
	('asd', '123', 1),
	('asd1', '123', 2),
	('asd3', 'a123', 3);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
