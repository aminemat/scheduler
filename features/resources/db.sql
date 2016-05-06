DROP DATABASE IF EXISTS `scheduler-test`;

CREATE DATABASE `scheduler-test`;

USE `scheduler-test`;

--
-- Structure for table: access_token
--
CREATE TABLE `access_token` (
  `token` varchar(512) NOT NULL,
  `user_id` varchar(512) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `access_token_uniq_token` (`token`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Structure for table: shift
--
CREATE TABLE `shift` (
  `id` varchar(36) NOT NULL,
  `manager_id` varchar(32) NOT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `break` float DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Structure for table: user
--
CREATE TABLE `user` (
  `id` varchar(36) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `password` varchar(512) NOT NULL,
  `phone` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
