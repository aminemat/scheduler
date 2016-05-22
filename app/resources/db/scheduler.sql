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


INSERT INTO `user` (`id`,`role`,`name`,`email`,`password`,`phone`,`created_at`,`updated_at`) VALUES
('employee_1','employee','Lazy employee John','employee@foo.com','$2y$10$1gpjI.GR10owbYEeDQFblOIvmDVo3reJfAnbpsB1eF9RGmO7QTCca','952 952 9521','2016-05-22 00:00:00','2016-05-22 11:18:21'),
('manager_1','manager','Bossy McBossFace','manager@foo.com','$2y$10$Y2UE5D2T3PtiW0Rs/gGKeuuTLNHUczFQolCVtJS2vmXcum.aag4Ke',NULL,'2016-05-22 00:00:00','2016-05-22 11:18:35');
