-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2015 at 03:41 AM
-- Server version: 5.6.15
-- PHP Version: 5.4.35

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Report`
--
CREATE DATABASE `Report` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `Report`;

-- --------------------------------------------------------

--
-- Table structure for table `Cmdb3Field`
--

CREATE TABLE IF NOT EXISTS `Cmdb3Field` (
  `fieldId` char(36) NOT NULL,
  `fieldName` varchar(128) NOT NULL,
  `fieldCName` varchar(4096) NOT NULL,
  `fieldSortName` varchar(128) NOT NULL,
  `callBack` text,
  `tableId` char(36) NOT NULL,
  `fieldType` varchar(32) NOT NULL,
  `fieldLength` text NOT NULL,
  `defaultSortKey` enum('1','0') NOT NULL,
  `defaultSortOrder` enum('ASC','DESC','') NOT NULL,
  `fieldPosition` int(11) NOT NULL,
  `fieldDisplay` int(11) NOT NULL DEFAULT '3' COMMENT '显示类型',
  `fieldVirtualValue` varchar(1024) DEFAULT NULL COMMENT '规则',
  `fieldMap` varchar(2048) NOT NULL,
  `defaultDisplay` tinyint(4) DEFAULT '1' COMMENT '默认显示',
  `defaultValue` varchar(1024) DEFAULT NULL COMMENT '默认值',
  `needMerge` tinyint(1) unsigned DEFAULT NULL COMMENT '是否合并',
  PRIMARY KEY (`fieldId`,`tableId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Cmdb3Field`
--

INSERT INTO `Cmdb3Field` (`fieldId`, `fieldName`, `fieldCName`, `fieldSortName`, `callBack`, `tableId`, `fieldType`, `fieldLength`, `defaultSortKey`, `defaultSortOrder`, `fieldPosition`, `fieldDisplay`, `fieldVirtualValue`, `fieldMap`, `defaultDisplay`, `defaultValue`, `needMerge`) VALUES
('07ecca21-fab9-a415-8024-16fa183123e1', 'enum', '枚举', 'enum', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'enum', '{"''wew''":"''wew''","''234''":"''234''"}', '1', '', 1, 2, '', '', 1, '', 0),
('0ddd9716-d65a-f32b-a965-03741999aad0', 'tableCName', '页面标题', 'tableCName', 'return ''<a href="javascript:void(0);" onclick="viewItem(\\'''' .$row[''tableId'']. ''\\'');"><span title="''.$row[''tableInfo''].''">'' . ((int)strlen($row[''tableCName'']) > 50 ? htmlspecialchars(sub_str($row[''tableCName''], 0, 60) . " ..."): htmlspecialchars($row[''tableCName'']))\n. ''</span></a>'';', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 0, 3, '', '', 1, '', 1),
('1b10793b-ac3d-9284-5219-b62c643fb50d', 'userName', '用户展现名', 'userName', '', 'bdb886aa-5fb4-09cc-293e-2603e76ee2f2', 'string', '', '1', '', 1, 2, '', '', 1, '', 0),
('32022ede-1b6e-1aa2-e58a-61dccaa9fe1b', 'set', 'set', 'set', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'enum', '{"''rere''":"''rere''","''redfd''":"''redfd''"}', '1', '', 11, 2, '', '', 1, '', 0),
('43670e37-0e9d-b90f-118a-ab7e0b99106b', 'bigint', '大整数', 'bigint', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'int', '', '1', '', 3, 1, '', '', 1, '', 0),
('4bb23a72-589f-3086-9153-c23007c5a583', 'longvarchar', 'longvarchar', 'longvarchar', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'text', '', '1', '', 0, 2, '', '', 1, '', 0),
('4e2d0dc9-5db6-03f4-08c2-8d0195010698', 'date', 'date', 'date', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'date', '', '1', '', 8, 2, '', '', 1, '', 0),
('52aaa1d1-3622-9acd-953a-9a7cee7dc5b5', 'date1', 'int类型日期', 'date1', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'date', '', '1', '', 14, 2, 'FROM_UNIXTIME(date1,''%Y-%m-%d'')', '', 1, '', 0),
('54772e4b-a531-174c-1ff5-11ffc792a229', 'float', 'float', 'float', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'float', '', '1', '', 4, 1, '', '', 1, '', 0),
('552e0e3c-eb5b-1ce3-e35b-223d589c167b', 'authorName', '作者', 'authorName', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 1, 2, '', '', 1, '', 0),
('561f2318-f5ff-eafa-d933-372663abd639', '编辑', '编辑', '', 'return ''<input type="button" class="button" value="编辑" onclick="editItem(\\'''' .$row[''tableId'']. ''\\'')"/>'';', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 9, 2, '''''', '', 1, '', 0),
('60e2047b-5790-58d2-cc78-95635747b0cb', 'createTime', '创建时间', 'createTime', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'datetime', '', '1', '', 4, 2, '', '', 1, '', 0),
('694fc58e-9808-5353-eed5-f4c123025df8', 'real', 'real', 'real', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'float', '', '1', '', 7, 1, '', '', 1, '', 0),
('73232f73-ca10-f2b7-f244-e2baea0dd622', '删除', '删除', '', 'return ''<input type="button" class="button" value="删除" onclick="deleteItem(\\'''' .$row[''tableId'']. ''\\'')"/>'';', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 10, 2, '''''', '', 1, '', 0),
('7a95e306-cd9e-05f3-bc2d-d770e280d4fc', 'tableId', '页面ID', 'tableId', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 3, 1, '', '', 1, '', 0),
('8d5e6e2c-2588-dd7a-4719-fe696f846657', 'time2', 'time2', 'time2', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'datetime', '', '1', '', 13, 2, 'FROM_UNIXTIME(time2,''%Y-%m-%d %H:%i:%s'')', '', 1, '', 0),
('9e29d438-d28b-c88e-b1f4-ba8265fdc6c0', 'decimal', 'decimal', 'decimal', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'float', '', '1', '', 5, 1, '', '', 1, '', 0),
('a5aea215-45be-296c-c79b-2850540fef8f', 'double', 'double', 'double', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'float', '', '1', '', 6, 1, '', '', 1, '', 0),
('a68c50f0-8f4d-1b36-bdda-a06b17081ae9', 'datetime', 'datetime', 'datetime', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'datetime', '', '1', '', 9, 2, '', '', 1, '', 0),
('ad32edf6-58ba-7c88-802b-6163e84ef2e6', 'timestamp', 'timestamp', 'timestamp', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'datetime', '', '1', '', 10, 2, '', '', 1, '', 0),
('b42e3647-5376-0b81-9fbc-7965eb557ae6', 'enable', '可用', 'enable', '', 'bdb886aa-5fb4-09cc-293e-2603e76ee2f2', 'int', '', '1', '', 2, 1, '', '', 1, '', 0),
('b4b51580-57e4-a15d-ae97-a36601a431ac', 'sourceHost', 'sourceHost', 'sourceHost', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'string', '', '1', '', 6, 2, '', '', 1, '', 0),
('b519b26d-6fce-1dd3-7f3c-43da906e3e3f', '复制', '复制', '', 'return ''<input type="button" class="button" value="复制" onclick="copyItem(\\'''' .$row[''tableId'']. ''\\'')"/>'';', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 8, 2, '''''', '', 1, '', 0),
('c0909d1e-31e9-e971-cd74-97b49073fad4', 'userId', '用户名', 'userId', '', 'bdb886aa-5fb4-09cc-293e-2603e76ee2f2', 'string', '', '1', '', 0, 2, '', '', 1, '', 0),
('d2dd2528-6a69-b71c-64af-021bb4725b15', 'test2', 'test2', 'test2', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'int', '', '1', '', 2, 1, '', '', 1, '', 0),
('d9ede26c-83e5-7ce3-773b-5e88e7ac2fd2', 'time1', 'time1', 'time1', '', '5ae5bec9-605c-a818-ec0c-fad631eed6d1', 'datetime', '', '1', '', 12, 2, 'FROM_UNIXTIME(time1,''%Y-%m-%d %H:%i:%s'')', '', 1, '', 0),
('dc5a5fb0-a846-ce56-8a59-6a10a63764a8', 'tableInfo', '页面详细说明', 'tableInfo', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 7, 0, '', '', 1, '', 0),
('e0c3f5e0-6144-e349-9486-ce8bcf1007c9', 'sourceTable', '表名', 'sourceTable', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'string', '', '1', '', 2, 2, '', '', 1, '', 0),
('f09d9de5-5307-853a-ca60-e44adddfaffc', 'lastModifyTime', '最近修改时间', 'lastModifyTime', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'datetime', '', '1', '', 5, 2, '', '', 1, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Cmdb3Table`
--

CREATE TABLE IF NOT EXISTS `Cmdb3Table` (
  `tableId` char(36) NOT NULL,
  `tableName` varchar(128) NOT NULL,
  `tableCName` varchar(4096) DEFAULT NULL,
  `tableInfo` text NOT NULL,
  `extraJsCss` text NOT NULL COMMENT '额外的Js和Css',
  `pagination` int(11) NOT NULL DEFAULT '20',
  `authorId` int(11) DEFAULT NULL,
  `authorName` varchar(128) NOT NULL,
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastModifyTime` datetime NOT NULL,
  `sourceType` varchar(16) DEFAULT 'mysqli',
  `sourceHost` varchar(16) NOT NULL,
  `sourceDb` varchar(128) NOT NULL,
  `sourcePort` int(11) NOT NULL,
  `sourceTable` varchar(128) NOT NULL,
  `sourceUser` varchar(20) NOT NULL,
  `sourcePass` varchar(20) NOT NULL,
  `sourceCallBack` text NOT NULL,
  `editFlag` tinyint(1) unsigned NOT NULL COMMENT '编辑的标识',
  `bookFlag` tinyint(1) unsigned NOT NULL COMMENT '订阅的标识',
  `excelFlag` tinyint(1) unsigned NOT NULL COMMENT '导出Excel的标识',
  `groupFlag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '分组计算的标识',
  `chartFlag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '绘图的标识',
  PRIMARY KEY (`tableId`),
  UNIQUE KEY `tableName` (`tableName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Cmdb3Table`
--

INSERT INTO `Cmdb3Table` (`tableId`, `tableName`, `tableCName`, `tableInfo`, `extraJsCss`, `pagination`, `authorId`, `authorName`, `createTime`, `lastModifyTime`, `sourceType`, `sourceHost`, `sourceDb`, `sourcePort`, `sourceTable`, `sourceUser`, `sourcePass`, `sourceCallBack`, `editFlag`, `bookFlag`, `excelFlag`, `groupFlag`, `chartFlag`) VALUES
('5ae5bec9-605c-a818-ec0c-fad631eed6d1', '测试', '测试', '', '', 10, NULL, 'guest', '2015-02-27 08:05:49', '2015-02-27 16:05:49', '', '127.0.0.1', 'test', 3306, 'test', 'root', 'root', '', 0, 0, 0, 0, 0),
('bdb886aa-5fb4-09cc-293e-2603e76ee2f2', '用户管理', '用户管理', '', '', 10, NULL, 'guest', '2015-02-27 10:16:43', '2015-02-28 09:32:25', '', '127.0.0.1', 'Web', 3306, 'cUser', 'root', 'root', '', 0, 0, 0, 0, 0),
('ee524ea0-10e2-edf9-9ad5-7222c4b567cc', '自助数据展现V3 - 列表 <a href="javascript:void(0);"onclick="editItem();">新建</a>', '自助数据展现V3 - 列表 <a href="javascript:void(0);"onclick="editItem();">新建</a>', '', '<script>\n\nseajs.use(''lib'', function(lib) {\n\n	//查看连接\n	window.viewItem = function(tableId) {\n		var url = SITE_URL + ''DiyData/report?tableId='' + tableId;\n		window.open(url);\n	};\n\n	//编辑按钮\n	window.editItem = function(tableId) {\n		var url = SITE_URL + ''DiyConfig/edit?tableId='' + (tableId || '''');\n		window.open(url);\n	};\n\n	//复制按钮\n	window.copyItem = function(tableId) {\n		if (!confirm(''确定要复制吗？'')) { return false; }\n\n		var url = lib.url + "diyConfig/copyTable";\n		var data = {tableId:tableId};\n		lib.post(url, data, function(objResult) {\n			if (objResult.result) {\n				lib.showTip(''复制成功！'');\n				seajs.use("js/diy_table", function(page) {\n					page.loadTable();\n				});\n			} else {\n				lib.showErrorTip(objResult.msg);\n			}\n		}, {\n		    loading : true\n		});\n	};\n\n	//删除按钮\n	window.deleteItem = function(tableId) {\n		if (!confirm(''确定要删除吗？'')) { return false; }\n		\n		var url = lib.url + "diyConfig/deleteTable";\n		var data = {tableId:tableId};\n		lib.post(url, data, function(objResult) {\n			if (objResult.result) {\n				lib.showTip(''删除成功！'');\n				seajs.use("js/diy_table", function(page) {\n					page.loadTable();\n				});\n			} else {\n				lib.showErrorTip(objResult.msg);\n			}\n		}, {\n		    loading : true\n		});\n	};\n});\n</script>\n', 20, 50013623, 'dw_zhanchaojiang', '2014-03-16 15:35:36', '2015-02-27 18:19:54', '', '127.0.0.1', 'Report', 3306, 'Cmdb3Table', 'root', 'root', 'return "(SELECT * FROM Cmdb3Table WHERE tableId != ''ee524ea0-10e2-edf9-9ad5-7222c4b567cc'') AS t";', 0, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `Cmdb3TableMeta`
--

CREATE TABLE IF NOT EXISTS `Cmdb3TableMeta` (
  `tableId` char(36) NOT NULL,
  `metaKey` varchar(128) NOT NULL,
  `metaValue` varchar(2048) NOT NULL,
  `lastUpdateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tableId`,`metaKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Cmdb3TableMeta`
--

INSERT INTO `Cmdb3TableMeta` (`tableId`, `metaKey`, `metaValue`, `lastUpdateTime`) VALUES
('bdb886aa-5fb4-09cc-293e-2603e76ee2f2', 'tableDefaultCondition', '[["userName","like",""]]', '2015-02-27 10:17:02'),
('ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'tableDefaultCondition', '[["authorName","like",""],["tableCName","like",""],["createTime",":","",""]]', '2015-02-27 01:19:51');
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='节点表' AUTO_INCREMENT=29 ;

--
-- Dumping data for table `cMenuNode`
--

INSERT INTO `cMenuNode` (`nodeId`, `nodeName`, `leftUrl`, `rightUrl`) VALUES
(2, 'DIY系统', '', '/DiyData/report?tableId=ee524ea0-10e2-edf9-9ad5-7222c4b567cc'),
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
(15, 'Node 11', '', ''),
(16, 'Node 10', '', ''),
(18, 'Node 12', '', ''),
(24, 'Node 14', '', ''),
(26, 'Node 15', '', ''),
(27, 'DIY系统(旧的)', '', '/diy/standardReport.php?tableId=ee524ea0-10e2-edf9-9ad5-7222c4b567cc'),
(28, '用户管理', '', '/DiyData/report?tableId=bdb886aa-5fb4-09cc-293e-2603e76ee2f2');

-- --------------------------------------------------------

--
-- Table structure for table `cUser`
--

CREATE TABLE IF NOT EXISTS `cUser` (
  `userId` varchar(100) NOT NULL COMMENT '用户名',
  `userName` varchar(100) NOT NULL COMMENT '用户展现名',
  `enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '可用',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

--
-- Dumping data for table `cUser`
--

INSERT INTO `cUser` (`userId`, `userName`, `enable`) VALUES
('dw_zhanchaojiang', 'Ben', 1);

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
(2, 3, 1),
(3, 0, 2),
(4, 3, 2),
(27, 3, 3),
(28, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `rUserNode`
--

CREATE TABLE IF NOT EXISTS `rUserNode` (
  `nodeId` int(11) NOT NULL COMMENT '节点id',
  `userId` varchar(100) NOT NULL COMMENT '用户名',
  PRIMARY KEY (`nodeId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rUserNode`
--

INSERT INTO `rUserNode` (`nodeId`, `userId`) VALUES
(0, 'dw_chenyinle'),
(0, 'dw_zhanchaojiang'),
(3, ''),
(28, '');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vMenuNode`
--
CREATE TABLE IF NOT EXISTS `vMenuNode` (
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
-- Structure for view `vMenuNode`
--
DROP TABLE IF EXISTS `vMenuNode`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vmenunode` AS (select `c`.`nodeId` AS `nodeId`,`c`.`nodeName` AS `nodeName`,`c`.`leftUrl` AS `leftUrl`,`c`.`rightUrl` AS `rightUrl`,`r`.`parentNodeId` AS `parentNodeId`,`c2`.`nodeName` AS `parentNodeName`,`r`.`sortPos` AS `sortPos` from ((`rmenunode` `r` left join `cmenunode` `c` on((`c`.`nodeId` = `r`.`nodeId`))) left join `cmenunode` `c2` on((`c2`.`nodeId` = `r`.`parentNodeId`))));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
