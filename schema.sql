CREATE DATABASE `bwitter`;
USE `bwitter`;

CREATE TABLE `data` (
  `name` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `data` (name,value) VALUES
     ('notice','');

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `bweet` int(11) NOT NULL,
  `reporter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `timezones` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tweets` (
  `id` bigint(20) NOT NULL,
  `content` text NOT NULL,
  `user` text NOT NULL,
  `timestamp` int(8) NOT NULL,
  `date` DATE NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `fullname` text NOT NULL,
  `email` text NOT NULL,
  `username` text NOT NULL,
  `timezone` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT 1,
  `data` text NOT NULL DEFAULT '{}',
  `bio` text NOT NULL DEFAULT '',
  `location` text NOT NULL DEFAULT '',
  `web` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `invites` (
  `id` text NOT NULL,
  `email` text NOT NULL,
  `username` text NOT NULL
)

INSERT INTO `timezones` (`id`, `name`) VALUES
(1, 'UTC'),
(2, 'AEDT (UTC+11)'),
(3, 'PST (UTC-8)'),
(4, 'CET (UTC+1)'),
(5, 'JST (UTC+9)'),
(6, 'IST (UTC+5:30)'),
(7, 'AST (UTC+3)'),
(8, 'ART (UTC-3)'),
(9, 'MSK (UTC+3)'),
(10, 'HKT (UTC+8)'),
(11, 'No timezone.');
COMMIT;