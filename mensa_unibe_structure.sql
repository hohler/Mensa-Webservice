-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 27, 2013 at 02:26 AM
-- Server version: 5.5.29-0ubuntu1
-- PHP Version: 5.4.9-4ubuntu2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mensa_unibe`
--

-- --------------------------------------------------------

--
-- Table structure for table `mensa`
--

CREATE TABLE IF NOT EXISTS `mensa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plz` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `menu` text COLLATE utf8_unicode_ci NOT NULL,
  `price` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `mensa_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=264 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_mensa`
--
CREATE TABLE IF NOT EXISTS `view_mensa` (
`id` int(11)
,`name` varchar(255)
,`street` varchar(255)
,`plz` varchar(255)
,`lat` float
,`lon` float
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_menu`
--
CREATE TABLE IF NOT EXISTS `view_menu` (
`menu_id` int(11)
,`mensa_id` int(11)
,`title` varchar(120)
,`menu` text
,`date` date
,`mensa` varchar(255)
,`week` int(2)
,`yearweek` int(6)
,`day` varchar(64)
,`modified` datetime
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_update`
--
CREATE TABLE IF NOT EXISTS `view_update` (
`id` int(11)
,`name` varchar(255)
,`modified` datetime
,`created` datetime
);
-- --------------------------------------------------------

--
-- Structure for view `view_mensa`
--
DROP TABLE IF EXISTS `view_mensa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_mensa` AS select `mensa`.`id` AS `id`,`mensa`.`name` AS `name`,`mensa`.`street` AS `street`,`mensa`.`plz` AS `plz`,`mensa`.`lat` AS `lat`,`mensa`.`lon` AS `lon` from `mensa`;

-- --------------------------------------------------------

--
-- Structure for view `view_menu`
--
DROP TABLE IF EXISTS `view_menu`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_menu` AS select `menu`.`id` AS `menu_id`,`mensa`.`id` AS `mensa_id`,`menu`.`title` AS `title`,`menu`.`menu` AS `menu`,`menu`.`date` AS `date`,`mensa`.`name` AS `mensa`,week(`menu`.`date`,1) AS `week`,yearweek(`menu`.`date`,1) AS `yearweek`,date_format(`menu`.`date`,'%W') AS `day`,`menu`.`modified` AS `modified` from (`menu` join `mensa` on((`mensa`.`id` = `menu`.`mensa_id`)));

-- --------------------------------------------------------

--
-- Structure for view `view_update`
--
DROP TABLE IF EXISTS `view_update`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_update` AS select `mensa`.`id` AS `id`,`mensa`.`name` AS `name`,max(`menu`.`modified`) AS `modified`,max(`menu`.`created`) AS `created` from (`mensa` join `menu` on((`mensa`.`id` = `menu`.`mensa_id`))) group by `mensa`.`name` order by `mensa`.`id`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
