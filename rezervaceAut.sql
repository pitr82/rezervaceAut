-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `rezervaceAut`;
CREATE DATABASE `rezervaceAut` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `rezervaceAut`;

DROP TABLE IF EXISTS `auto`;
CREATE TABLE `auto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firma_id` int(11) NOT NULL,
  `typAuta_id` int(11) NOT NULL,
  `znackaAuta_id` int(11) NOT NULL,
  `spz` varchar(9) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `zruseno` enum('1','0') COLLATE utf8_czech_ci DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `spz` (`spz`),
  KEY `firma_id` (`firma_id`),
  KEY `typAuta_id` (`typAuta_id`),
  KEY `znackaAuta_id` (`znackaAuta_id`),
  CONSTRAINT `auto_ibfk_1` FOREIGN KEY (`firma_id`) REFERENCES `firma` (`id`),
  CONSTRAINT `auto_ibfk_2` FOREIGN KEY (`typAuta_id`) REFERENCES `typAuta` (`id`),
  CONSTRAINT `auto_ibfk_3` FOREIGN KEY (`znackaAuta_id`) REFERENCES `znackaAuta` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `auto` (`id`, `firma_id`, `typAuta_id`, `znackaAuta_id`, `spz`, `popis`, `zruseno`) VALUES
(1,	1,	1,	1,	'6T0 2716',	'Opel Combo - Technika 704',	'0'),
(2,	1,	3,	1,	'3T1 2587',	'Oopel -Technika 704',	'0'),
(3,	1,	2,	1,	'2T1 3654',	'Opel Zafira -  NS 750',	'0'),
(4,	1,	1,	9,	'4A1 2763',	'Nová Škoda Rapid',	'0');

DROP TABLE IF EXISTS `firma`;
CREATE TABLE `firma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `ico` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `dic` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `ulice` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `mesto` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `psc` varchar(5) COLLATE utf8_czech_ci DEFAULT NULL,
  `stat` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazev` (`nazev`),
  UNIQUE KEY `ico` (`ico`),
  UNIQUE KEY `dic` (`dic`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `firma` (`id`, `nazev`, `ico`, `dic`, `ulice`, `mesto`, `psc`, `stat`) VALUES
(1,	'Vítkovice Mechanika a.s.',	'5464',	'',	'Ruská 2929/101a',	'Ostrava-Vítkovice',	'70602',	'Česká Republika');

DROP TABLE IF EXISTS `rezervace`;
CREATE TABLE `rezervace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_id` int(11) NOT NULL,
  `zamestnanec_id` int(11) NOT NULL,
  `rezervaceOd` datetime NOT NULL,
  `rezervaceDo` datetime NOT NULL,
  `spravce_id` int(11) NOT NULL,
  `vytvoreno` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `zruseno` enum('1','0') COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `potvrzeno` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auto_id` (`auto_id`),
  KEY `rezervaceOd` (`rezervaceOd`),
  KEY `rezervaceDo` (`rezervaceDo`),
  KEY `spravce_id` (`spravce_id`),
  KEY `zamestnanec_id` (`zamestnanec_id`),
  CONSTRAINT `rezervace_ibfk_1` FOREIGN KEY (`auto_id`) REFERENCES `auto` (`id`),
  CONSTRAINT `rezervace_ibfk_5` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`id`),
  CONSTRAINT `rezervace_ibfk_6` FOREIGN KEY (`spravce_id`) REFERENCES `zamestnanec` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `rezervace` (`id`, `auto_id`, `zamestnanec_id`, `rezervaceOd`, `rezervaceDo`, `spravce_id`, `vytvoreno`, `zruseno`, `potvrzeno`) VALUES
(2,	1,	5,	'2014-06-05 11:30:00',	'2014-06-05 13:30:00',	5,	'2014-05-30 11:15:13',	'0',	NULL),
(3,	1,	21,	'2014-06-02 05:00:00',	'2014-06-02 08:00:00',	21,	'2014-06-02 04:48:12',	'0',	NULL),
(4,	1,	5,	'2014-06-02 20:30:00',	'2014-06-02 21:30:00',	5,	'2014-06-02 05:25:55',	'0',	NULL),
(5,	1,	5,	'2014-06-02 08:00:00',	'2014-06-02 11:30:00',	5,	'2014-06-02 05:26:42',	'0',	NULL),
(6,	1,	12,	'2014-06-04 04:30:00',	'2014-06-04 11:00:00',	12,	'2014-06-02 05:35:40',	'0',	NULL),
(7,	1,	12,	'2014-06-03 08:00:00',	'2014-06-03 08:30:00',	12,	'2014-06-02 06:20:58',	'0',	NULL),
(8,	1,	5,	'2014-06-03 09:00:00',	'2014-06-03 11:00:00',	5,	'2014-06-02 09:11:33',	'0',	NULL),
(9,	1,	21,	'2014-06-02 12:00:00',	'2014-06-02 14:00:00',	21,	'2014-06-02 09:28:39',	'0',	NULL),
(10,	1,	1,	'2014-06-06 08:00:00',	'2014-06-06 12:00:00',	30,	'2014-06-02 10:24:31',	'0',	NULL),
(12,	1,	1,	'2014-06-11 08:00:00',	'2014-06-11 12:30:00',	30,	'2014-06-03 04:53:44',	'0',	NULL),
(13,	1,	30,	'2014-06-04 06:00:00',	'2014-06-04 07:00:00',	30,	'2014-06-03 08:32:45',	'0',	NULL),
(14,	1,	19,	'2014-06-03 11:30:00',	'2014-06-03 14:00:00',	19,	'2014-06-03 09:19:55',	'0',	NULL),
(16,	1,	5,	'2014-06-05 07:30:00',	'2014-06-05 09:30:00',	5,	'2014-06-05 05:19:02',	'0',	NULL),
(17,	1,	29,	'2014-06-09 06:00:00',	'2014-06-09 14:00:00',	29,	'2014-06-06 04:44:34',	'0',	NULL),
(19,	1,	5,	'2014-06-19 07:00:00',	'2014-06-19 15:30:00',	5,	'2014-06-09 05:23:33',	'0',	NULL),
(20,	1,	19,	'2014-06-10 07:30:00',	'2014-06-10 10:00:00',	19,	'2014-06-09 10:42:56',	'0',	NULL),
(21,	1,	30,	'2014-06-12 07:30:00',	'2014-06-12 12:00:00',	30,	'2014-06-10 06:55:04',	'0',	NULL),
(23,	1,	30,	'2014-06-16 09:00:00',	'2014-06-16 12:00:00',	30,	'2014-06-12 08:26:45',	'0',	NULL),
(24,	1,	30,	'2014-06-17 07:30:00',	'2014-06-17 12:30:00',	30,	'2014-06-12 08:27:12',	'0',	NULL),
(25,	1,	30,	'2014-06-16 08:00:00',	'2014-06-16 09:00:00',	30,	'2014-06-12 08:43:37',	'0',	NULL),
(26,	1,	19,	'2014-06-12 12:00:00',	'2014-06-12 13:30:00',	19,	'2014-06-12 09:23:22',	'0',	NULL),
(28,	1,	5,	'2014-06-13 09:30:00',	'2014-06-13 11:30:00',	5,	'2014-06-13 05:16:29',	'0',	NULL),
(29,	1,	5,	'2014-06-20 08:00:00',	'2014-06-20 11:30:00',	5,	'2014-06-17 09:26:30',	'0',	NULL),
(31,	1,	5,	'2014-06-18 10:00:00',	'2014-06-18 14:00:00',	5,	'2014-06-18 07:32:51',	'0',	NULL),
(32,	1,	30,	'2014-06-23 06:30:00',	'2014-06-23 15:30:00',	30,	'2014-06-19 06:18:10',	'0',	NULL),
(33,	1,	30,	'2014-06-25 07:30:00',	'2014-06-25 12:30:00',	30,	'2014-06-20 06:06:25',	'0',	NULL),
(34,	1,	27,	'2014-06-26 07:30:00',	'2014-06-26 13:00:00',	27,	'2014-06-20 10:34:45',	'0',	NULL),
(36,	1,	27,	'2014-06-24 08:00:00',	'2014-06-24 10:00:00',	27,	'2014-06-23 11:00:56',	'0',	NULL),
(37,	1,	5,	'2014-06-30 07:00:00',	'2014-06-30 10:00:00',	5,	'2014-06-23 12:13:07',	'0',	NULL),
(40,	1,	27,	'2014-07-01 10:00:00',	'2014-07-01 15:00:00',	27,	'2014-06-24 08:07:13',	'0',	NULL),
(41,	1,	19,	'2014-07-01 06:30:00',	'2014-07-01 10:00:00',	19,	'2014-06-25 10:22:37',	'0',	NULL),
(42,	1,	5,	'2014-07-01 13:10:01',	'2014-07-01 13:30:00',	1,	'2014-07-01 10:46:23',	'0',	NULL),
(43,	1,	11,	'2014-07-04 15:00:00',	'2014-07-04 18:30:00',	1,	'2014-07-02 06:21:43',	'0',	NULL),
(46,	1,	1,	'2014-07-07 12:30:00',	'2014-07-07 13:00:00',	1,	'2014-07-07 10:28:19',	'0',	NULL),
(47,	1,	1,	'2014-07-08 09:00:00',	'2014-07-08 09:30:00',	1,	'2014-07-08 06:14:44',	'0',	NULL),
(48,	1,	5,	'2014-07-08 10:00:00',	'2014-07-08 10:30:00',	1,	'2014-07-08 06:15:25',	'0',	NULL),
(49,	1,	1,	'2014-07-08 10:30:00',	'2014-07-08 11:00:00',	1,	'2014-07-08 06:15:42',	'0',	NULL),
(50,	1,	1,	'2014-07-08 05:00:00',	'2014-07-08 05:30:00',	1,	'2014-07-08 07:18:23',	'0',	NULL),
(52,	1,	1,	'2014-07-08 15:00:00',	'2014-07-08 18:00:00',	1,	'2014-07-08 09:02:02',	'0',	NULL),
(57,	1,	5,	'2014-07-09 11:00:00',	'2014-07-09 12:30:00',	3,	'2014-07-08 11:02:19',	'0',	NULL),
(59,	1,	11,	'2014-07-08 14:30:00',	'2014-07-08 15:00:00',	11,	'2014-07-08 12:02:36',	'0',	NULL),
(62,	1,	1,	'2014-07-09 14:30:00',	'2014-07-09 15:00:00',	1,	'2014-07-08 12:47:30',	'0',	NULL),
(63,	1,	3,	'2014-07-09 06:30:00',	'2014-07-09 08:00:00',	3,	'2014-07-08 12:50:18',	'0',	NULL),
(66,	1,	1,	'2014-07-10 14:00:00',	'2014-07-10 16:00:00',	1,	'2014-07-10 09:09:53',	'0',	NULL),
(69,	4,	3,	'2014-07-10 13:30:00',	'2014-07-10 17:00:00',	3,	'2014-07-10 09:12:19',	'0',	NULL),
(74,	4,	1,	'2014-07-10 18:00:00',	'2014-07-10 20:00:00',	1,	'2014-07-10 10:26:50',	'0',	NULL),
(75,	3,	5,	'2014-07-13 11:30:00',	'2014-07-13 12:00:00',	1,	'2014-07-11 09:48:54',	'0',	NULL),
(76,	1,	1,	'2014-07-12 11:30:00',	'2014-07-12 12:00:00',	1,	'2014-07-11 09:49:03',	'0',	NULL),
(77,	2,	3,	'2014-07-14 11:30:00',	'2014-07-14 12:00:00',	3,	'2014-07-11 09:49:46',	'0',	NULL),
(78,	3,	5,	'2014-07-15 14:00:00',	'2014-07-15 23:00:00',	1,	'2014-07-14 12:29:34',	'0',	NULL),
(79,	1,	1,	'2014-07-21 11:00:00',	'2014-07-21 15:00:00',	1,	'2014-07-21 08:38:34',	'0',	NULL),
(80,	1,	1,	'2014-07-23 11:00:00',	'2014-07-23 11:30:00',	1,	'2014-07-21 09:11:17',	'0',	NULL),
(81,	1,	1,	'2014-08-03 11:00:00',	'2014-08-03 13:30:00',	1,	'2014-07-21 09:14:29',	'0',	NULL),
(83,	1,	1,	'2014-07-22 11:00:00',	'2014-07-22 12:00:00',	1,	'2014-07-22 08:34:23',	'0',	NULL);

DROP TABLE IF EXISTS `rezervaceHistory`;
CREATE TABLE `rezervaceHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rezervace_id` int(11) NOT NULL,
  `zamestnanec_id` int(11) NOT NULL,
  `spravece_id` int(11) NOT NULL,
  `auto_id` int(11) NOT NULL,
  `rezervaceOd` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `rezervaceDo` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `vytvoreno` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `popis` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zamestnanec_id` (`zamestnanec_id`),
  KEY `spravece_id` (`spravece_id`),
  KEY `rezervace_id` (`rezervace_id`),
  KEY `auto_id` (`auto_id`),
  CONSTRAINT `rezervaceHistory_ibfk_2` FOREIGN KEY (`zamestnanec_id`) REFERENCES `zamestnanec` (`id`),
  CONSTRAINT `rezervaceHistory_ibfk_3` FOREIGN KEY (`spravece_id`) REFERENCES `zamestnanec` (`id`),
  CONSTRAINT `rezervaceHistory_ibfk_4` FOREIGN KEY (`rezervace_id`) REFERENCES `rezervace` (`id`),
  CONSTRAINT `rezervaceHistory_ibfk_5` FOREIGN KEY (`auto_id`) REFERENCES `auto` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `typAuta`;
CREATE TABLE `typAuta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typ` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `typAuta` (`id`, `typ`) VALUES
(1,	'Osobní'),
(2,	'Nákladní'),
(3,	'Traktor'),
(4,	'Motorka'),
(5,	'Kolo'),
(6,	'Dodávka');

DROP TABLE IF EXISTS `utvar`;
CREATE TABLE `utvar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `vytvoreno` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazev` (`nazev`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `utvar` (`id`, `nazev`, `popis`, `vytvoreno`) VALUES
(1,	'Technika - správní budova',	'Technika na červené budově.',	'2014-06-23 11:36:34'),
(2,	'NS 750',	'Nákladové středisko 750',	'2014-06-09 07:36:31');

DROP TABLE IF EXISTS `utvar_auto`;
CREATE TABLE `utvar_auto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utvar_id` int(11) NOT NULL,
  `auto_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utvar_id` (`utvar_id`),
  KEY `auto_id` (`auto_id`),
  CONSTRAINT `utvar_auto_ibfk_1` FOREIGN KEY (`utvar_id`) REFERENCES `utvar` (`id`),
  CONSTRAINT `utvar_auto_ibfk_2` FOREIGN KEY (`auto_id`) REFERENCES `auto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `utvar_auto` (`id`, `utvar_id`, `auto_id`) VALUES
(1,	1,	1),
(19,	2,	3),
(20,	2,	2),
(21,	2,	4);

DROP VIEW IF EXISTS `vw_autoPujcenoHodin`;
CREATE TABLE `vw_autoPujcenoHodin` (`soucet` decimal(46,4), `auto_id` int(11));


DROP VIEW IF EXISTS `vw_nejvicePujceneAuto`;
CREATE TABLE `vw_nejvicePujceneAuto` (`counted` bigint(21), `auto_id` int(11));


DROP VIEW IF EXISTS `vw_nejvicePujcuje`;
CREATE TABLE `vw_nejvicePujcuje` (`counted` bigint(21), `zamestnanec_id` int(11));


DROP VIEW IF EXISTS `vw_zamestnanecPujcenoHodin`;
CREATE TABLE `vw_zamestnanecPujcenoHodin` (`zamestnanec_id` int(11), `soucet` decimal(46,4));


DROP TABLE IF EXISTS `zamestnanec`;
CREATE TABLE `zamestnanec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firma_id` int(11) NOT NULL,
  `utvar_id` int(11) NOT NULL DEFAULT '2',
  `jmeno` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `ulice` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `mesto` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `psc` tinyint(5) DEFAULT NULL,
  `stat` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `tel` tinyint(9) DEFAULT NULL,
  `login` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `povolen` tinyint(4) NOT NULL DEFAULT '1',
  `heslo` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `role` enum('zamestnanec','vedouci','admin') COLLATE utf8_czech_ci NOT NULL,
  `vytvoreno` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `zruseno` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `firma_id` (`firma_id`),
  KEY `utvar_id` (`utvar_id`),
  CONSTRAINT `zamestnanec_ibfk_1` FOREIGN KEY (`firma_id`) REFERENCES `firma` (`id`),
  CONSTRAINT `zamestnanec_ibfk_2` FOREIGN KEY (`utvar_id`) REFERENCES `utvar` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `zamestnanec` (`id`, `firma_id`, `utvar_id`, `jmeno`, `prijmeni`, `ulice`, `mesto`, `psc`, `stat`, `email`, `tel`, `login`, `povolen`, `heslo`, `role`, `vytvoreno`, `zruseno`) VALUES
(1,	1,	1,	'Petr',	'Stefan',	'',	'',	NULL,	'',	'petr.stefan@vitkovice.cz',	NULL,	'stefan',	1,	'$2y$10$libw9vZZCu9/OJGb.efJkOmkRyhNNsDqNQWfkdzTkMOj5uIfe1.ti',	'admin',	'2014-06-30 10:43:39',	0),
(3,	1,	2,	'Ondřej',	'Běhálek',	'',	'',	NULL,	'',	'pitr82@gmail.com',	NULL,	'behalek',	1,	'$2y$10$pn21n5ru.hHMVxiaJEI2duqj1jpi4Al2Rc6ON6i90ddQRP5Pu7/jK',	'zamestnanec',	'2014-07-10 09:10:27',	0),
(4,	1,	1,	'Vladimír',	'Bernát',	NULL,	NULL,	NULL,	NULL,	'vladimir.bernat@vitkovice.cz',	NULL,	'bernatv',	1,	'$2y$10$q/bG/AstMaZoBB7hEzrQLenejJmOiMRoWnSWC6dM/bP47AhTzg9Im',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(5,	1,	1,	'Vladimír',	'Bujnošek',	NULL,	NULL,	NULL,	NULL,	'vladimir.bujnosek@vitkovice.cz',	NULL,	'bujnosek',	1,	'$2y$10$6so5PcAUEQeY46q9QkJzUuQuW6Sd45va71mTZB3vmIIepOcBPwQVS',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(6,	1,	1,	'Rastislav',	'Cerma',	NULL,	NULL,	NULL,	NULL,	'rastislav.cerman@vitkovice.cz',	NULL,	'cerman',	1,	'$2y$10$DOLQCwdfL6DG6OlLcYpdh.F/EQLSN0Ww6G8CD9IuhUKiYNIZh/g1a',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(7,	1,	1,	'Miloš',	'Frajs',	NULL,	NULL,	NULL,	NULL,	'milos.frajs@vitkovice.cz',	NULL,	'frajs',	1,	'$2y$10$h3h1jzoA5/mOsWIKuTc0LuFuK1.IeL1Jfif4L5VpHY8Beo7AMxkdK',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(8,	1,	1,	'Jan',	'Holeš',	NULL,	NULL,	NULL,	NULL,	'jan.holes@vitkovice.cz',	NULL,	'holesj',	1,	'$2y$10$ejQKrHAARawBHC2fiZ85TeMjiArUbe536Eb.AiEAvrdOh4kK9zKnC',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(9,	1,	1,	'Ivana',	'Hrnčárková',	NULL,	NULL,	NULL,	NULL,	'ivana.hrncarkova@vitkovice.cz',	NULL,	'hrncarkova',	1,	'$2y$10$YKibxjRLrJnjkiURDnzI/OgMgzK2HHl4L9rGeC6cvTyesLw5b8zJ2',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(10,	1,	1,	'Karel',	'Chvostek',	NULL,	NULL,	NULL,	NULL,	'karel.chvostek@vitkovice.cz',	NULL,	'chvostek',	1,	'$2y$10$BeppBkieTmjNt11SGoIl8O19nsPRRnhXVgA0k04QzvarjB6yBjEHq',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(11,	1,	1,	'Zdeněk',	'Vomočil',	NULL,	NULL,	NULL,	NULL,	'zdenek.vomocil@vitkovice.cz',	NULL,	'vomocil',	1,	'$2y$10$nzDmK8fp3qUYRX938Xm4K.zqU8Z73EvEIGI7NcB8ieglJDGZ3StWW',	'admin',	'2014-06-30 10:43:39',	0),
(12,	1,	1,	'Vojtěch',	'Kocián',	NULL,	NULL,	NULL,	NULL,	'vojtech.kocian@vitkovice.cz',	NULL,	'kocianv',	1,	'$2y$10$3UgBHv4u3FPo0NRkkN3x8ebtROqNhmcA1Wa.ROrL1qETciCv.OMf6',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(14,	1,	1,	'Přemysl',	'Kostka',	NULL,	NULL,	NULL,	NULL,	'premysl.kostka@vitkovice.cz',	NULL,	'kostkap',	1,	'$2y$10$MalIfOFTN0RFUUqMu7HBoezv1nlF/wzZrQCf.Q0gW7z.LJbgMsXDm',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(15,	1,	1,	'Michaela',	'Kocurová',	NULL,	NULL,	NULL,	NULL,	'michaela.kocurova@vitkovice.cz',	NULL,	'kocurovam',	1,	'$2y$10$L1zIawM51AOcs2f9ZxDRseb.axfQfzYwnkOqYn4FT5FxCkW6KQbga',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(16,	1,	1,	'Lubomír',	'Kühtreiber',	NULL,	NULL,	NULL,	NULL,	'lubomir.kuhtreiber@vitkovice.cz',	NULL,	'kuhtreiber',	1,	'$2y$10$A.aRl.kaALFuTSw33xEHfeMmTT8U13eTvMEeEbIcTJg7zOChYQ/f2',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(17,	1,	1,	'Přemysl',	'Liška',	NULL,	NULL,	NULL,	NULL,	'premysl.liska@vitkovice.cz',	NULL,	'liskap',	1,	'$2y$10$/2tpmo0f7BbwT7xvB3KBSO5heCcPthnz.tLUIkdHmDnIlMzlSNtrS',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(18,	1,	1,	'Jan',	'Lukšík',	NULL,	NULL,	NULL,	NULL,	'jan.luksik@vitkovice.cz',	NULL,	'luksik',	1,	'$2y$10$AxkAwHQZkqSUlaavVhUi..Genwh0eVLtAhf4ln4l8O7QWiHqwmpP2',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(19,	1,	1,	'David',	'Mohyla',	NULL,	NULL,	NULL,	NULL,	'david.mohyla@vitkovice.cz',	NULL,	'mohylad',	1,	'$2y$10$1uELxz8z1Jcyjn6Cp6qHIeZBBpF64tg/KHy/XiFZUpqGHMZ5Rlj6G',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(20,	1,	1,	'Jan',	'Moravec',	NULL,	NULL,	NULL,	NULL,	'jan.moravec@vitkovice.cz',	NULL,	'moravecj',	1,	'$2y$10$Dq.FrsfRT80TGajR0sGgIOtBiCNsLZ8OXnfDuK4p0ONBVzKpo3iPm',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(21,	1,	1,	'Jaromír',	'Osyčka',	NULL,	NULL,	NULL,	NULL,	'jaromir.osycka@vitkovice.cz',	NULL,	'osycka',	1,	'$2y$10$DmRysjr3jjWH7F8vtUOskusmCTeKcZDlrX1vpYalAJVhGYvZl/Qkm',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(22,	1,	1,	'Věroslav',	'Ptašek',	NULL,	NULL,	NULL,	NULL,	'veroslav.ptasek@vitkovice.cz',	NULL,	'ptasek',	1,	'$2y$10$oto9VT8Li0fYTKBLmqIQ4uDrgKU3E8YMT1/pdhIx6DibStsZWVKC.',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(23,	1,	1,	'Kamil',	'Sikora',	NULL,	NULL,	NULL,	NULL,	'kamil.sikora@vitkovice.cz',	NULL,	'sikorak',	1,	'$2y$10$DrIqtHkpYKWXPZJ6wvDFPOe.quZbv30MuCgBsdRE.dQ90n7l8yjV.',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(24,	1,	1,	'Dagmar',	'Smetanová',	NULL,	NULL,	NULL,	NULL,	'dagmar.smetanova@vitkovice.cz',	NULL,	'smetanovad',	1,	'$2y$10$pBXcDhCW5Hp9NUQbObU5b.A8A26ZDj0NtxfvMXYbZE1vlTXI7iEFG',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(25,	1,	1,	'Petr',	'Sojka',	NULL,	NULL,	NULL,	NULL,	'petr.sojka@vitkovice.cz',	NULL,	'sojkap',	1,	'$2y$10$HO0vxJM/C9zCLLcwiC121.UnaHDtO/gQmM7O16Y4MNO0UXEvUYPYm',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(26,	1,	1,	'Martin',	'Szewieczek',	NULL,	NULL,	NULL,	NULL,	'martin.szewieczek@vitkovice.cz',	NULL,	'szewieczek',	1,	'$2y$10$T.Yr9jxJl4taornDoK79M.iQ4b0TP3f.Hpm5q9tk7dOiv2pCOcPme',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(27,	1,	1,	'Dušan',	'Vašíček',	NULL,	NULL,	NULL,	NULL,	'dusan.vasicek@vitkovice.cz',	NULL,	'vasicekd',	1,	'$2y$10$4Fthd36aXNvsCwiarorMi.Iv9GM6.9juIXEKQWIFP2fC.h7AVbe6K',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(28,	1,	1,	'Martin',	'Tichopad',	NULL,	NULL,	NULL,	NULL,	'martin.tichopad@vitkovice.cz',	NULL,	'tichopad',	1,	'$2y$10$giZe6KjY6AfAPyciEHcQA.d8u8qZOTboYhHn9oBa4v8274NgXvdDW',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(29,	1,	1,	'Josef',	'Sedláček',	NULL,	NULL,	NULL,	NULL,	'josef.sedlacek@vitkovice.cz',	NULL,	'sedlacekj',	1,	'$2y$10$jY0CfATNe.ev.19uaHL3ROiLmYnK0qaqe1CNS.nvHvArkA2SSORd2',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(30,	1,	1,	'Alena',	'Bezecná',	NULL,	NULL,	NULL,	NULL,	'alena.bezecna@vitkovice.cz',	NULL,	'bezecna',	1,	'$2y$10$VvTlD5eXs5ZYwa5IGr706umlUXyqkXV2vxfPtlsImW7.Rg2OXzNXy',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(31,	1,	1,	'Jiří',	'Gebauer',	NULL,	NULL,	NULL,	NULL,	'jiri.gebauer@vitkovice.cz',	NULL,	'gebauer',	1,	'$2y$10$6t4jqp6iCcKiRqdKlVdqoOB3ws4jtEBgE3uvuCfaX2CjopUDqJzem',	'zamestnanec',	'2014-06-30 10:43:39',	0),
(32,	1,	1,	'Ladislav',	'Kozok',	NULL,	NULL,	NULL,	NULL,	'ladislav.kozok@vitkovice.cz',	NULL,	'kozok',	1,	'$2y$10$ah2WCZgfZBeVq.xWDgY4IuPj3zlvQIVh6HoBo6WSuS/YDKFWIbfg2',	'zamestnanec',	'2014-06-30 10:43:39',	0);

DROP TABLE IF EXISTS `znackaAuta`;
CREATE TABLE `znackaAuta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `znacka` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `znackaAuta` (`id`, `znacka`) VALUES
(1,	'Opel Combo'),
(2,	'Škoda Octávia'),
(4,	'Renault'),
(5,	'Citroën'),
(6,	'VW Passat'),
(7,	'Audi A4'),
(8,	'Seat'),
(9,	'Škoda Rapid'),
(10,	'Škoda CityGo'),
(11,	'Opel Berlingo'),
(12,	'Audi A6'),
(13,	'Audi A8'),
(14,	'Škoda SuperB');

DROP TABLE IF EXISTS `vw_autoPujcenoHodin`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_autoPujcenoHodin` AS select sum((timestampdiff(MINUTE,`rezervace`.`rezervaceOd`,`rezervace`.`rezervaceDo`) / 60)) AS `soucet`,`rezervace`.`auto_id` AS `auto_id` from `rezervace` group by `rezervace`.`auto_id`;

DROP TABLE IF EXISTS `vw_nejvicePujceneAuto`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_nejvicePujceneAuto` AS select count(0) AS `counted`,`rezervace`.`auto_id` AS `auto_id` from `rezervace` group by `rezervace`.`auto_id`;

DROP TABLE IF EXISTS `vw_nejvicePujcuje`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_nejvicePujcuje` AS select count(0) AS `counted`,`rezervace`.`zamestnanec_id` AS `zamestnanec_id` from `rezervace` group by `rezervace`.`zamestnanec_id`;

DROP TABLE IF EXISTS `vw_zamestnanecPujcenoHodin`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_zamestnanecPujcenoHodin` AS select `rezervace`.`zamestnanec_id` AS `zamestnanec_id`,sum((timestampdiff(MINUTE,`rezervace`.`rezervaceOd`,`rezervace`.`rezervaceDo`) / 60)) AS `soucet` from `rezervace` group by `rezervace`.`zamestnanec_id`;

-- 2014-07-23 07:24:41
