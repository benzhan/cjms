<?php

/** 操作符号 */
$GLOBALS['diy']['opts'] = array(
    "=" => "等于",
    "!=" => "不等于",
    ">" => "大于",
    ">=" => "大于等于",
    "<" => "小于",
    "<=" => "小于等于",
    "like" => "类似于",
    "like .%" => "开头类似于",
    "like %." => "结尾类似于",
    "in" => "包含于",
    "not in" => "不包含于",
    ":" => "区间于",
);

/** 字段的数据类型 */
$GLOBALS['diy']['fieldTypes'] = array(
    "int" => "int",
    "float" => "float",
    "string" => "string",
    "enum" => "enum",
    "date" => "date",
    "datetime" => "datetime",
    "text" => "text",
    // 自定义类型
    "ip" => "ip",
);

/** 字段的数据类型 */
$GLOBALS['diy']['fieldTypeMap'] = array(
    "tinyint" => "int",
    "smallint" => "int",
    "mediumint" => "int",
    "int" => "int",
    'bigint' => "int",

    "decimal" => "float",
    "float" => "float",
    "double" => "float",

    "varchar" => "string",
    "char" => "string",
    
    "date" => "date",
    "datetime" => "datetime",
    "timestamp" => "datetime",
    
    'tinytext' => 'text',
    "text" => "text",
    'mediumtext' => 'text',
    'longtext' => 'text',
    
    'tinyblob' => 'text',
    'mediumblob' => 'text',
    'blob' => 'text',
    'longblob' => 'text',
    
    "enum" => "enum",
    'set' => 'enum'
);


/** 字段的数据类型 */
$GLOBALS['diy']['pageSizes'] = array(
    10,
    20,
    50, 
    100
);

$GLOBALS['diy']['map'] = array(
    array('desc' => '无字典', 'func' => ''),
    array('desc' => '应用ID->应用名称', 'func' => '{"name":"getAppName","keyField":"appId","valField":"cName"}'),
    array('desc' => '国家ID->国家名称', 'func' => '{"name":"getCountry","keyField":"countryId","valField":"countryName"}'),
    array('desc' => '省份ID->省份名称', 'func' => '{"name":"getProv","keyField":"provinceId","valField":"provinceName"}'),
    array('desc' => '城市ID->城市名称', 'func' => '{"name":"getCity","keyField":"cityId","valField":"cityName"}'),
    array('desc' => '运营商ID->运营商名称', 'func' => '{"name":"getIsp","keyField":"ispId","valField":"ispName"}'),
    array('desc' => 'page->页面名称', 'func' => '{"name":"getPage","keyField":"page","valField":"pageName"}'),
    array('desc' => 'action->动作名称', 'func' => '{"name":"getAction","keyField":"action","valField":"actionName"}'),
    array('desc' => '模块ID->模块名称', 'func' => '{"name":"getModName","keyField":"modId","valField":"modName"}'),
    array('desc' => '渠道->渠道名称', 'func' => '{"name":"getChannelName","keyField":"channel","valField":"channelName"}'),
    array('desc' => '充值类型->充值类型名称', 'func' => '{"name":"getChargeTypeName","keyField":"chargeType","valField":"chargeTypeName"}'),
);

//白名单
$GLOBALS['diy']['whiteList'] = array('dw_zhanchaojiang', 'dw_chenyinle', 'dw_sushaoyong');

//end of script
