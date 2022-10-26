-- Adminer 4.8.1 MySQL 10.7.4-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `action`;
CREATE TABLE `action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(36) NOT NULL,
  `description` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `task_queue`;
CREATE TABLE `task_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(36) NOT NULL,
  `action` int(11) unsigned NOT NULL,
  `company` int(11) unsigned NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `elapse` int(11) DEFAULT NULL,
  `log` varchar(128) DEFAULT NULL,
  `message` varchar(128) DEFAULT NULL,
  `status` enum('complete','progress','waiting','error') NOT NULL DEFAULT 'waiting',
  `immediate` tinyint(1) NOT NULL DEFAULT 0,
  `arguments` varchar(128) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `action` (`action`),
  KEY `company` (`company`),
  CONSTRAINT `task_queue_ibfk_1` FOREIGN KEY (`action`) REFERENCES `action` (`id`),
  CONSTRAINT `task_queue_ibfk_2` FOREIGN KEY (`company`) REFERENCES `company` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `worker`;
CREATE TABLE `worker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(36) NOT NULL,
  `url` varchar(72) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- 2022-10-26 13:25:29

