-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2015 at 02:11 PM
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
('023e6e21-5271-7ac1-d6ed-eb8719ffc30c', 'nick_name', 'nick_name', 'nick_name', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 4, 2, '', '', 1, '', 0),
('081d2251-9447-966a-2640-58b5b8c28c21', 'card_pass', '卡密', 'card_pass', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 19, 2, '', '', 1, '', 0),
('0ddd9716-d65a-f32b-a965-03741999aad0', 'tableCName', '页面标题', 'tableCName', 'return ''<a href="javascript:void(0);" onclick="viewItem(\\'''' .$row[''tableId'']. ''\\'');"><span title="''.$row[''tableInfo''].''">'' . ((int)strlen($row[''tableCName'']) > 50 ? htmlspecialchars(sub_str($row[''tableCName''], 0, 60) . " ..."): htmlspecialchars($row[''tableCName'']))\n. ''</span></a>'';', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 0, 2, '', '', 1, '', 0),
('0dea0335-e4c2-77aa-0dce-2411c8da978c', 'pay_notify_url', '发货回调', 'pay_notify_url', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'text', '', '1', '', 4, 2, '', '', 1, '', 0),
('0f4d79b6-3c70-7c5a-688f-8802fe3e7c27', 'ch_order_id', '渠道订单号', 'ch_order_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 27, 2, '', '', 1, '', 0),
('1585728a-75aa-f222-809f-904d7df69d05', 'app_id', 'appId', 'app_id', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'int', '', '1', '', 1, 2, '', '', 1, '', 0),
('1dab186d-f6fb-5259-87a4-d91579a16e63', 'card_total_amount', '卡支付额', 'card_total_amount', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'float', '', '1', '', 20, 3, '', '', 1, '', 0),
('2105252e-c18c-af05-f259-58295d8b5759', 'app_order_id', '订单id', 'app_order_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 0, 2, '', '', 1, '', 0),
('22e97d77-f728-23b6-8ddf-02942c68c29b', 'd_device_info', 'd_device_info', 'd_device_info', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'text', '', '1', '', 13, 2, '', '', 1, '', 0),
('27bac780-09fc-7613-df91-9ccf55861452', 'update_time', 'update_time', 'update_time', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 9, 2, '', '', 1, '', 0),
('28c67821-8d49-1ca2-43a3-4b07cc2679d3', 'yyuid', 'yyuid', 'yyuid', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 3, 2, '', '', 1, '', 0),
('29d3df49-c8b0-02ba-2dbb-4a0ed028f457', 'editField', '操作', 'yyuid', '$app_order_id = "''{$_row[''app_order_id'']}''";\n$amount = "''{$_row[''amount'']}''";\n\nreturn <<<EOF\n<input type="button" class="button" value="发货" onclick="rePost($app_order_id);"/>\n<input type="button" class="button" value="退款" onclick="backCharge($app_order_id, $amount)"/>\nEOF\n;', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 41, 2, '123', '', 1, '', 0),
('2bda1834-180b-4036-ba55-9eae59e302a2', 'notify_url', 'notify_url', 'notify_url', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 12, 0, '', '', 1, '', 0),
('2c79eca7-44ea-91ea-23ac-3e1e5facf79a', 'g_cid', 'g_cid', 'g_cid', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 10, 2, '', '', 1, '', 0),
('2d7d84fe-d530-b712-11a5-23177d45ace7', 'id', 'id', 'id', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 0, 2, '', '', 1, '', 0),
('313d4780-ad0f-210b-f45c-0d5eb819f78b', 'log_time', 'log_time', 'log_time', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 0, 0, '', '', 1, '', 0),
('315a8cb5-2a63-7f6b-e850-ad0140578617', 'email', 'email', 'email', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 3, 2, '', '', 1, '', 0),
('3428bd0f-c1dc-f7c3-bd4c-9307f3515f08', 'yy_oper', '是否对 Y 币账户 操作', 'yy_oper', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 21, 2, '', '', 1, '', 0),
('391e53c6-0191-751b-f6a1-024f08fd7949', 'ch_deal_id', '渠道交易号', 'ch_deal_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 26, 2, '', '', 1, '', 0),
('3ae7d289-e86f-6e82-92ad-7a508a27947c', 'package_name', 'package_name', 'package_name', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'text', '', '1', '', 6, 2, '', '', 1, '', 0),
('3b30bd49-654b-b932-24b3-e2a23f843a13', 'product_id', '产品ID', 'product_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 7, 2, '', '', 1, '', 0),
('3e7d3709-3557-e656-979a-b2b8778ab1a3', 'player_id', 'player_id', 'player_id', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 9, 2, '', '', 1, '', 0),
('4012258d-7a8c-0a7c-7e4b-a3b2697d9507', 'zone', 'zone', 'zone', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 2, 2, '', '', 1, '', 0),
('412aa53b-66ba-4444-d60d-687d5b2813fd', 'lastLoginTime', 'lastLoginTime', 'lastLoginTime', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 4, 2, '', '', 1, '', 0),
('4461254a-7173-cbb6-61b6-1582c02c3602', 'channel_id', 'channel_id', 'channel_id', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 10, 2, '', '', 1, '', 0),
('4465578e-caa4-095b-94a1-9729b015292c', 'return_url', 'return_url', 'return_url', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 11, 0, '', '', 1, '', 0),
('4507385f-f829-d8e8-e2c8-ddde59368db4', 'user_addiinfo', 'user_addiinfo', 'user_addiinfo', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 17, 0, '', '', 1, '', 0),
('45d7f972-d537-548e-2aa6-05888741ba47', 'create_time', 'create_time', 'create_time', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 12, 2, '', '', 1, '', 0),
('486dd852-db63-7e59-a6aa-d505ac016a35', 'game_appid', 'game_appid', 'game_appid', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 6, 2, '', '', 1, '', 0),
('4911abaf-06de-d373-789d-aadd97e23abc', 'user_name', 'user_name', 'user_name', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 15, 0, '', '', 1, '', 0),
('4920a23b-3247-9ebf-f9f9-e477575520c2', 'create_time', 'create_time', 'create_time', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'datetime', '', '1', '', 10, 2, '', '', 1, '', 0),
('4ad28a87-6911-8e22-e90f-01161e230b9b', 'prod_addiinfo', 'prod_addiinfo', 'prod_addiinfo', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'text', '', '1', '', 10, 0, '', '', 1, '', 0),
('4ae9d1ac-1104-a57a-1c8d-2d6de70a7ca0', 'sdk_ver', 'sdk_ver', 'sdk_ver', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 9, 2, '', '', 1, '', 0),
('4e11dc06-6973-14c8-aeb3-834117260903', 'c_ip', 'c_ip', 'c_ip', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 14, 2, '', '', 1, '', 0),
('5321c45c-0699-f17d-dd01-4a05fad86382', 'notify_status_msg', 'notify_status_msg', 'notify_status_msg', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 31, 2, '', '', 1, '', 0),
('54c333c6-1990-f172-eb24-ce92e806d5df', 'loginCount', 'loginCount', 'loginCount', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 6, 2, '', '', 1, '', 0),
('552e0e3c-eb5b-1ce3-e35b-223d589c167b', 'authorName', '作者', 'authorName', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 1, 2, '', '', 1, '', 0),
('55919436-b579-6e71-7e1d-e2ef531e75f5', 'hiido_appkey', 'hiido_appkey', 'hiido_appkey', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'text', '', '1', '', 8, 2, '', '', 1, '', 0),
('58797f01-5a88-aaf4-3a57-34ff1b98c6c3', 'id', 'id', 'id', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 0, 2, '', '', 1, '', 0),
('59413b67-89ce-64f4-0de7-a38c1bbc04a7', 'status', 'status', 'status', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 11, 2, '', '', 1, '', 0),
('5a2d5192-fc76-7467-8c44-a7ddaa4792c4', 'ch_id', '支付渠道', 'ch_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 3, 2, '', '', 1, '', 0),
('5b223900-5f4f-cb30-abf0-f4d5bb1a8739', 'user_ip', 'user_ip', 'user_ip', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 13, 2, '', '', 1, '', 0),
('5ffd2e78-107a-ac21-3fb3-84c2a3b15ed4', 'zone_name', 'zone_name', 'zone_name', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 8, 2, '', '', 1, '', 0),
('60e2047b-5790-58d2-cc78-95635747b0cb', 'createTime', '创建时间', 'createTime', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'datetime', '', '1', '', 4, 2, '', '', 1, '', 0),
('699a41b3-ee6b-d2b6-5bc1-a12da821d504', 'bank_id', '银行 ID', 'bank_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 4, 2, '', '', 1, '', 0),
('6ced088c-d88f-9e9c-9355-513de558fc85', 'roleId', 'roleId', 'roleId', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 10, 2, '', '', 1, '', 0),
('6e6b160d-b772-560f-2334-10bfcb0425bd', 'ch_fee', 'ch_fee', 'ch_fee', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'float', '', '1', '', 33, 1, '', '', 1, '', 0),
('74692f67-1d62-e905-4ac7-718059d15a6d', 'update_time', '更新时间', 'update_time', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 38, 2, '', '', 1, '', 0),
('75812037-32ac-b93f-e499-449bdefd2312', 'ch_deal_time', 'ch_deal_time', 'ch_deal_time', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 36, 0, '', '', 1, '', 0),
('7778e54f-b066-9cb2-ca8f-b4002c3f2cfb', 'unit', 'unit', 'unit', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 2, 0, '', '', 1, '', 0),
('793839ef-1c27-5ece-c413-3f12de351cd7', 'phoneNumber', 'phoneNumber', 'phoneNumber', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 9, 2, '', '', 1, '', 0),
('7a95e306-cd9e-05f3-bc2d-d770e280d4fc', 'tableId', '页面ID', 'tableId', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 3, 2, '', '', 1, '', 0),
('7c2a2cd1-b901-88c8-f671-9785b61fc636', 'hiido_appid', 'hiido_appid', 'hiido_appid', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'text', '', '1', '', 7, 2, '', '', 1, '', 0),
('7ef25a44-0a0e-0842-5392-11aec7eb2027', 'level', 'level', 'level', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 5, 2, '', '', 1, '', 0),
('8139e578-76e2-7b22-30d8-1493242a1888', 'udbAccount', 'udbAccount', 'udbAccount', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 12, 2, '', '', 1, '', 0),
('82679515-b263-78af-0ba1-deafdaeeda01', 'editField', '操作', 'sourceTable', 'return ''<input type="button" class="button" value="编辑" onclick="editItem(\\'''' .$row[''tableId'']. ''\\'');"/>'' \n       . ''<input type="button" class="button" value="复制" onclick="copyItem(\\'''' .$row[''tableId'']. ''\\'')"/>'' \n       . ''<input type="button" class="button" value="删除" onclick="deleteItem(\\'''' .$row[''tableId'']. ''\\'')"/>'';', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 7, 2, '''编辑''', '', 1, '', 0),
('835fde71-8d15-26dd-b924-3b998ec7cd82', 'g_ver', 'g_ver', 'g_ver', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 8, 2, '', '', 1, '', 0),
('84ee5743-2bc7-b193-fac9-35e9802f5bba', 'coupons', 'coupons', 'coupons', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 24, 0, '', '', 1, '', 0),
('8982fa77-c644-112c-4a49-b039f1d7505e', 'd_uuid', 'd_uuid', 'd_uuid', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 7, 2, '', '', 1, '', 0),
('89bc9a77-6275-059f-bb1b-942cae48ccd4', 'yyuid', 'yyuid', 'yyuid', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 5, 2, '', '', 1, '', 0),
('8cf340f5-c930-69c0-0d64-54e3a2f186db', 'id', 'id', 'id', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'int', '', '1', '', 0, 2, '', '', 1, '', 0),
('8d59e578-eee4-2595-3b77-b3e4740a533e', 'game_appid', 'game_appid', 'game_appid', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 6, 2, '', '', 1, '', 0),
('8d91e7d8-b6a0-fd93-071a-59dc944d217b', 'g_error_msg', 'g_error_msg', 'g_error_msg', 'return urldecode($_val);', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'text', '', '1', '', 12, 2, '', '', 1, '', 0),
('8ed71416-f4a6-81ad-466d-c77ea80bd9ac', 'address', 'address', 'address', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 1, 2, '', '', 1, '', 0),
('8f92b0d2-f369-ea67-708e-e5286ee197f0', 'product_desc', 'product_desc', 'product_desc', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 9, 0, '', '', 1, '', 0),
('903ba820-b1fa-4a9d-bdcf-f992a4ca215d', 'app_order_time', '订单提交时间', 'app_order_time', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 6, 2, '', '', 1, '', 0),
('93057d5d-0f78-92f6-e11a-abe3eaa7edc3', 'bbs_url', '论坛Url', 'bbs_url', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'text', '', '1', '', 5, 2, '', '', 1, '', 0),
('9c47f3f9-9def-7921-f5b3-041f44b959fc', 'g_error_type', 'g_error_type', 'g_error_type', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 11, 2, '', '', 1, '', 0),
('a15633ea-bef9-7f85-04b1-e1fc78bef0fd', 'product_name', '产品名', 'product_name', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 8, 2, '', '', 1, '', 0),
('a7491f0a-825c-26d5-c41c-ad5b631fdde1', 'open_id', 'open_id', 'open_id', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 1, 2, '', '', 1, '', 0),
('a8e44ea1-8837-8284-0634-d19c0c6cd7ab', 'pay_status', '支付状态', 'pay_status', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 28, 2, '', '', 1, '', 0),
('aa959b6b-bfe0-b879-4bd0-f1685536efbd', 'bank_deal_id', '银行流水号', 'bank_deal_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 34, 2, '', '', 1, '', 0),
('acf3bf8b-debc-da96-aa40-07444ce0b3a2', 'good_deliver_status', '发货状态', 'good_deliver_status', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 29, 2, '', '', 1, '', 0),
('ad3ce246-4a80-761f-5121-f3ae55043d2d', 'a_time', 'a_time', 'a_time', 'return date(''Y-m-d'', $_val / 1000) ;', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'datetime', '', '1', '', 1, 2, '', '', 1, '', 0),
('af73a8d1-f48b-5fb0-0e4a-5b1819fafbbf', 'lastUpdateTime', 'lastUpdateTime', 'lastUpdateTime', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 5, 2, '', '', 1, '', 0),
('b0d69922-524f-44a3-6443-fce6e093f5a9', 'submit_time', 'submit_time', 'submit_time', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 37, 0, '', '', 1, '', 0),
('b0f60625-e15c-f7f0-3b9a-92046f95bc9e', 'name', '游戏名', 'name', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'text', '', '1', '', 3, 2, '', '', 1, '', 0),
('b2ea1330-19db-76a6-b34b-6d73245d43d9', 'auto_redirect', 'auto_redirect', 'auto_redirect', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 23, 0, '', '', 1, '', 0),
('b4b51580-57e4-a15d-ae97-a36601a431ac', 'sourceHost', 'sourceHost', 'sourceHost', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'string', '', '1', '', 6, 2, '', '', 1, '', 0),
('bf671202-3ddf-9207-b33f-c7ba4d9d33d9', 'level', 'level', 'level', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 5, 2, '', '', 1, '', 0),
('c252ba8f-e75c-d259-b750-9968d409373d', 'yy_amount', 'yy_amount', 'yy_amount', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'float', '', '1', '', 22, 1, '', '', 1, '', 0),
('c45c993d-08de-4476-c05e-9efbffc6ec73', 'd_uuid', 'd_uuid', 'd_uuid', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 6, 2, '', '', 1, '', 0),
('c6cec291-a4ff-f1fc-6795-44beb3cb17d7', 'amount', '金额', 'amount', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'float', '', '1', '', 1, 1, '', '', 1, '', 0),
('c8801429-ae90-e84b-c0f5-8d0118053d5a', 'user_id', 'user_id', 'user_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'text', '', '1', '', 14, 0, '', '', 1, '', 0),
('ca2e3d1f-0fec-918d-a5fc-00e30d451328', 'd_uuid', 'd_uuid', 'd_uuid', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 7, 2, '', '', 1, '', 0),
('cc474de8-936a-56d4-735c-fcace98a8c61', 'open_id', 'open_id', 'open_id', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 1, 2, '', '', 1, '', 0),
('d307cd1d-a598-854d-f5c2-08fa715b5873', 'user_defined_params', '自定义参数', 'user_defined_params', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 39, 2, '', '', 1, '', 0),
('d75adab0-3adc-b7b7-3346-72ecee4f6739', 'name', 'name', 'name', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 7, 2, '', '', 1, '', 0),
('d891ea14-c9b0-02ae-bece-2cd5be752d12', 'user_contact', 'user联系方式', 'user_contact', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 16, 2, '', '', 1, '', 0),
('da6ad28e-522c-211f-c624-9fccdde4ea1a', 'ch_account_id', '渠道对应的收钱 账号', 'ch_account_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 32, 2, '', '', 1, '', 0),
('dc5a5fb0-a846-ce56-8a59-6a10a63764a8', 'tableInfo', '页面详细说明', 'tableInfo', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'int', '', '1', '', 8, 0, '', '', 1, '', 0),
('dd7ecd74-5f03-1b2b-1a45-ec79d11b9cb1', 'action', 'action', 'action', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 3, 2, '', '', 1, '', 0),
('ddd07c49-11cf-6aed-331c-f14da5e90510', 'id', 'id', 'id', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 2, 2, '', '', 1, '', 0),
('dde24e52-97bf-ef0c-c36d-1fe320225630', 'bank_deal_time', '银行交易时间', 'bank_deal_time', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 35, 2, '', '', 1, '', 0),
('deb0717f-d63a-c0dd-954b-7c076a3b33c2', 'zone_name', 'zone_name', 'zone_name', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 8, 2, '', '', 1, '', 0),
('df03877f-d050-299f-682a-4bccbcd60fe6', 'pay_method', 'pay_method', 'pay_method', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 5, 0, '', '', 1, '', 0),
('df665910-9e0e-a949-8e31-dc23cdee462d', 'note', 'note', 'note', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 8, 2, '', '', 1, '', 0),
('e0c3f5e0-6144-e349-9486-ce8bcf1007c9', 'sourceTable', '表名', 'sourceTable', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'string', '', '1', '', 2, 2, '', '', 1, '', 0),
('e3e36927-5f71-d9c0-10ff-33316fefa44a', 'nick_name', 'nick_name', 'nick_name', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 4, 2, '', '', 1, '', 0),
('e415085d-2a84-573a-2734-1fa636bc0cab', 'zone', 'zone', 'zone', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 2, 2, '', '', 1, '', 0),
('ec8df39f-c207-6d6c-f889-2ef7599a5374', 'sign', 'sign', 'sign', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 4, 2, '', '', 1, '', 0),
('ef15d71a-a920-7e3d-f8ea-4d6ba2e22cc6', 'yyuid', 'yyuid', 'yyuid', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 3, 2, '', '', 1, '', 0),
('f07c5d4c-cff7-6c4f-ecb7-80d3d4e2774e', 'game_app_id', 'game_app_id', 'game_app_id', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 30, 2, '', '', 1, '', 0),
('f09d9de5-5307-853a-ca60-e44adddfaffc', 'lastModifyTime', '最近修改时间', 'lastModifyTime', '', 'ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'datetime', '', '1', '', 5, 2, '', '', 1, '', 0),
('f4045e51-1a1e-f275-9340-8d527fa1aa02', 'update_time', 'update_time', 'update_time', '', '2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'int', '', '1', '', 11, 2, '', '', 1, '', 0),
('f40af997-67d7-7f09-bbe7-e0bbe1bc1583', 'id', 'id', 'id', '', 'c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'int', '', '1', '', 0, 2, '', '', 1, '', 0),
('f48a969f-0874-8258-a999-4acff2a643a1', 'yyuid', 'yyuid', 'yyuid', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 40, 2, '', '', 1, '', 0),
('f581c0a8-7919-c2a5-5b72-56aeafe14e87', 'createTime', 'createTime', 'createTime', '', 'b27622b7-7de5-3be3-45fa-96a5e2eab575', 'int', '', '1', '', 2, 2, '', '', 1, '', 0),
('f68fde6f-1aab-d6f9-af64-160c3d78fb0f', 'app_key', 'appKey', 'app_key', '', '24c2fa74-49e6-5278-564b-8381b3f049c9', 'int', '', '1', '', 2, 2, '', '', 1, '', 0),
('fc34fb36-aa9e-6a9f-6645-7fce85e63f73', 'g_appid', 'g_appid', 'g_appid', '', '517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'int', '', '1', '', 7, 2, '', '', 1, '', 0),
('fca2247b-47dc-f30c-d55f-900946441b4d', 'card_num', '卡号', 'card_num', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'int', '', '1', '', 18, 2, '', '', 1, '', 0),
('fe93def9-2ec5-a811-a5c6-3c91a63f828f', 'ori_amount', 'ori_amount', 'ori_amount', '', '9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'float', '', '1', '', 25, 1, '', '', 1, '', 0);

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
('24c2fa74-49e6-5278-564b-8381b3f049c9', '应用管理', '应用管理', '', '', 20, 50013623, 'dw_zhanchaojiang', '2014-04-16 09:30:19', '2014-12-15 14:54:45', 'mysqli', '183.61.6.96', 'sdk_service', 6301, 'application', 'net_sdk_rw', 'ajYI03cKOhR', '', 1, 0, 0, 0, 0),
('2bc0ab57-280d-fdea-0e17-2f4223df6c74', 'Service表', 'Service表', '', '', 20, 50013623, 'dw_zhanchaojiang', '2014-06-24 12:27:51', '2014-06-24 20:28:18', 'mysqli', '183.61.6.96', 'sdk_service', 6301, 'gameplayer_10007_log', 'net_sdk_rw', 'ajYI03cKOhR', '', 1, 0, 0, 0, 0),
('517b5339-00ed-2b05-c84c-f598d1ee9ef8', '错误日志', '错误日志', '', '', 20, 50013623, 'dw_zhanchaojiang', '2014-03-31 08:16:58', '2014-05-06 15:02:22', 'mysqli', '183.61.6.96', 'sdk_data', 6301, 'log_g_error', 'net_sdk_rw', 'ajYI03cKOhR', '$_conditions[''a_time'']['':''][0] && $_conditions[''a_time'']['':''][0] = strtotime($_conditions[''a_time'']['':''][0] ) * 1000;\n$_conditions[''a_time'']['':''][1] && $_conditions[''a_time'']['':''][1] = strtotime($_conditions[''a_time'']['':''][1] ) * 1000;\n\nreturn "log_g_error";', 0, 0, 1, 1, 1),
('9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', '订单管理', '订单管理', '', '<script type=''text/javascript''>\nfunction rePost(appOrderId) {\nif(confirm(''确定重新发货？'')) {\n$.getJSON(''http://sandbox.api.5253.com/order/syncOrderStatus.do?appOrderId=''+appOrderId, function(json){\nif(json.result == 1) {\n alert(json.data);\n}else {\nalert(''发货失败'');\n}\n});\n}\n}\n\nfunction backCharge(appOrderId, amount) {\nif(confirm(''确定退款？'')) {\n$.getJSON(''http://sandbox.api.5253.com/order/reverseYB.do?appOrderId=''+appOrderId+''&amount=''+amount, function(json){\nif(json.result == 1) {\nalert(''退款成功'');\n}else {\nalert(''退款失败'');\n}\n});\n}\n}\n\n</script>', 20, 50013623, 'dw_zhanchaojiang', '2014-03-16 15:43:51', '2014-04-10 11:18:18', 'mysqli', '183.61.6.96', 'sdk_service', 6301, 'order_primary', 'net_sdk_rw', 'ajYI03cKOhR', '', 0, 1, 0, 1, 1),
('b27622b7-7de5-3be3-45fa-96a5e2eab575', '用户数据', '用户数据', '', '', 20, 50013623, 'dw_zhanchaojiang', '2014-03-17 02:23:49', '2014-07-08 15:59:30', 'mysqli', '183.61.6.96', 'sdk_data', 6301, 'sys_user', 'net_sdk_rw', 'ajYI03cKOhR', '', 1, 0, 0, 0, 0),
('c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', '角色信息', '角色信息', '', '', 20, 50013623, 'dw_zhanchaojiang', '2014-06-09 12:27:22', '2014-06-21 09:49:39', 'mysqli', '183.61.6.96', 'sdk_service', 6301, 'game_player', 'net_sdk_rw', 'ajYI03cKOhR', '', 1, 0, 0, 0, 0),
('ee524ea0-10e2-edf9-9ad5-7222c4b567cc', '自助数据展现V2 - 列表&nbsp;<a href="javascript:void(0);"onclick="editItem();">新建</a>', '自助数据展现V2 - 列表&nbsp;<a href="javascript:void(0);"onclick="editItem();">新建</a>', '', '<script>\n//查看连接\nfunction viewItem(tableId) {\n    $.dialog(SITE_URL + ''/diy/standardReport.php?tableId='' + tableId, ''查看'');\n}\n\n//编辑按钮\nfunction editItem(tableId) {\n    var url = SITE_URL + ''/diy/config/edit.php?tableId='';\n    tableId && (url += tableId);\n    $.dialog(url, ''编辑'');\n}\n\n//复制按钮\nfunction copyItem(tableId) {\n	if (!confirm(''确定要复制吗？'')) { return false; }\n\n	var interfaceName = ''Diy_Config_Controller_View.copyTable'';\n	var $loadingDiv = $.getLoadingDiv();\n	var data = {tableId:tableId};\n	$.callInterface(interfaceName, data, function(objResult) {\n	    $loadingDiv && $loadingDiv.end();\n	    if (objResult.result) {\n	        $.msgbox.succ(''复制成功！'', 500);\n	        JF.M("Diy_StandardReport").loadTableData();\n	    } else {\n	        $.msgbox.err(objResult.msg);\n	    }\n	});\n}\n\n//删除按钮\nfunction deleteItem(tableId) {\n    if (!confirm(''确定要删除吗？'')) { return false; }\n\n    var interfaceName = ''Diy_Config_Controller_View.delecteTable'';\n    var $loadingDiv = $.getLoadingDiv();\n    var data = {tableId:tableId};\n    $.callInterface(interfaceName, data, function(objResult) {\n        $loadingDiv && $loadingDiv.end();\n        if (objResult.result) {\n            $.msgbox.succ(''删除成功！'', 500);\n            JF.M("Diy_StandardReport").loadTableData();\n        } else {\n            $.msgbox.err(objResult.msg);\n        }\n    });\n}\n\n</script>', 20, 50013623, 'dw_zhanchaojiang', '2014-03-16 15:35:36', '2014-03-31 15:56:02', 'mysqli', '183.61.6.96', 'Report', 6301, 'Cmdb3Table', 'net_sdk_rw', 'ajYI03cKOhR', 'return "(SELECT * FROM Cmdb3Table WHERE tableId != ''ee524ea0-10e2-edf9-9ad5-7222c4b567cc'') AS t";', 0, 0, 0, 0, 0);

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
('517b5339-00ed-2b05-c84c-f598d1ee9ef8', 'tableDefaultCondition', '[]', '2014-05-27 06:18:08'),
('9a118bfa-589c-08c2-9b4f-f6dd567bf5b1', 'tableDefaultCondition', '[["app_order_id","like",""],["amount",":","",""],["pay_status","=",""],["good_deliver_status","=",""],["game_app_id","=",""]]', '2014-03-31 09:09:18'),
('b27622b7-7de5-3be3-45fa-96a5e2eab575', 'tableDefaultCondition', '[["id","=",""],["udbAccount","like",""]]', '2014-03-17 02:24:53'),
('c5f13e95-9bda-c1d3-0ad2-ec18f69db9d3', 'tableDefaultCondition', '[["game_appid","=",""],["create_time",":","",""],["yyuid","=",""],["nick_name","like",""]]', '2014-07-03 01:21:49'),
('ee524ea0-10e2-edf9-9ad5-7222c4b567cc', 'tableDefaultCondition', '[["tableCName","like",""],["authorName","like",""],["createTime",":","",""]]', '2014-03-17 01:39:02');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
