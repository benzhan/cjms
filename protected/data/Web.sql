-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2015 at 03:31 AM
-- Server version: 5.6.15
-- PHP Version: 5.4.35

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Web`
--
CREATE DATABASE `Web` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `Web`;

-- --------------------------------------------------------

--
-- Table structure for table `cMenuNode`
--

CREATE TABLE IF NOT EXISTS `cMenuNode` (
  `nodeId` int(11) NOT NULL AUTO_INCREMENT COMMENT '节点id',
  `nodeName` varchar(64) DEFAULT NULL COMMENT '节点名称',
  `leftUrl` varchar(1024) DEFAULT NULL COMMENT '左边iframe的url',
  `rightUrl` varchar(1024) DEFAULT NULL COMMENT '右边iframe的url',
  PRIMARY KEY (`nodeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='节点表' AUTO_INCREMENT=27 ;

--
-- Dumping data for table `cMenuNode`
--

INSERT INTO `cMenuNode` (`nodeId`, `nodeName`, `leftUrl`, `rightUrl`) VALUES
(1, '业务', '', ''),
(2, 'DIY系统', '', '/diy/standardReport.php?tableId=ee524ea0-10e2-edf9-9ad5-7222c4b567cc'),
(3, '系统管理', '', ''),
(4, '菜单管理', '', '/menu/index'),
(5, '订单管理', '', '/diy/standardReport.php?tableId=9a118bfa-589c-08c2-9b4f-f6dd567bf5b1#null'),
(6, '特坦然的说', '', 'http://www.pengyou.com/?http%3A%2F%2Fhome.pengyou.com%2Findex.php%3Fmod%3Dhome'),
(7, '用户数据', '', 'http://ben.platform.com/diy/standardReport.php?tableId=b27622b7-7de5-3be3-45fa-96a5e2eab575#null'),
(8, '百度', 'http://www.5253.com/', 'http://www.baidu.com/'),
(9, '用户管理', '', '/diy/standardReport.php?tableId=b27622b7-7de5-3be3-45fa-96a5e2eab575'),
(10, 'Node 7', '', ''),
(11, '添加升级', '', 'http://test.api.5253.com/app/toAddAppVersion.do '),
(13, '应用管理', '', '/diy/standardReport.php?tableId=24c2fa74-49e6-5278-564b-8381b3f049c9#null'),
(14, 'Node 8', '', ''),
(15, 'Node 11', '', ''),
(16, 'Node 10', '', ''),
(17, 'Node 11', '', ''),
(18, 'Node 12', '', ''),
(24, 'Node 14', '', ''),
(25, 'Node 14', '', ''),
(26, 'Node 15', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `rMenuNode`
--

CREATE TABLE IF NOT EXISTS `rMenuNode` (
  `nodeId` int(11) NOT NULL DEFAULT '0' COMMENT '节点id',
  `parentNodeId` int(11) NOT NULL DEFAULT '0' COMMENT '父节点的id',
  `sortPos` int(11) DEFAULT NULL COMMENT '节点的位置',
  PRIMARY KEY (`nodeId`),
  KEY `parentNodeId` (`parentNodeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='节点关系表';

--
-- Dumping data for table `rMenuNode`
--

INSERT INTO `rMenuNode` (`nodeId`, `parentNodeId`, `sortPos`) VALUES
(1, 0, 1),
(2, 3, 1),
(3, 0, 2),
(4, 3, 2),
(5, 1, 1),
(9, 1, 2),
(13, 1, 3),
(14, 1, 4),
(15, 14, 1),
(16, 17, 1),
(17, 14, 2),
(18, 14, 3),
(25, 16, 1),
(26, 25, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vmenunode`
--
CREATE TABLE IF NOT EXISTS `vmenunode` (
`nodeId` int(11)
,`nodeName` varchar(64)
,`leftUrl` varchar(1024)
,`rightUrl` varchar(1024)
,`parentNodeId` int(11)
,`parentNodeName` varchar(64)
,`sortPos` int(11)
);
-- --------------------------------------------------------

--
-- Structure for view `vmenunode`
--
DROP TABLE IF EXISTS `vmenunode`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vmenunode` AS (select `c`.`nodeId` AS `nodeId`,`c`.`nodeName` AS `nodeName`,`c`.`leftUrl` AS `leftUrl`,`c`.`rightUrl` AS `rightUrl`,`r`.`parentNodeId` AS `parentNodeId`,`c2`.`nodeName` AS `parentNodeName`,`r`.`sortPos` AS `sortPos` from ((`rmenunode` `r` left join `cmenunode` `c` on((`c`.`nodeId` = `r`.`nodeId`))) left join `cmenunode` `c2` on((`c2`.`nodeId` = `r`.`parentNodeId`))));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
