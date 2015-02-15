<?php

/**
 * Cookie 设置
 */
define('COOKIE_PREFIX', 'fw');
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', $_SERVER['HTTP_HOST']);

// 站点根目录
define('PUBLIC_PATH', '/');

// 随机安全字符串,一旦确定不要更改,否则会重建所有缓存
define('SECURITY', 'fw_');

// 设置时区
date_default_timezone_set('PRC');

// 编码定义
define('DEFAULT_CHARSET', 'utf-8');

// 定义当前时间
define('TIME', time());
define('NOW', date('Y-m-d H:i:s', TIME));
define('TODAY', date('Y-m-d', TIME));


define('SITE_NAME', '游戏中心管理平台');

$localIps = array(
    '127.0.0.1',
    '192.168.1.104'
);

$devIps = array(
    '183.61.6.104'
);

define('ENV_LOCAL', 'local');
define('ENV_DEV', 'dev');
define('ENV_FORMAL', 'formal');

$addr = $_SERVER['SERVER_ADDR'];
if (in_array($addr, $localIps) || empty($addr)) {
    define('ENV', ENV_LOCAL);
    define('DEBUG', true);
} else if (in_array($addr, $devIps)) {
    define('ENV', ENV_DEV);
    define('DEBUG', true);
} else {
    define('ENV', ENV_FORMAL);
    define('DEBUG', true);
}

require_once ROOT_PATH . "globalCode.inc.php";

if (ENV != ENV_FORMAL) {
    //sdk数据库配置
    $GLOBALS['dbInfo']['default'] = array(
        'enable' => true,
        'dbType' => 'mysqli',
        'dbHost' => '127.0.0.1',
        'dbHost' => '123.57.34.130',
        'dbPort' => 3306,
        'dbName' => 'lhc',
        'dbUser' => 'root',
        'dbPass' => 'mygod123456',
    );

    $GLOBALS['redisInfo']['logstash_redis'] = array(
        'host' => '127.0.0.1',
        'host' => '123.57.34.130',
        'pass' => 'zcjmygod123456abc987',
        'port' => 6379
    );
    
    // 站点URL，最后不带斜杠
    define('SITE_DIR', '/cjms/');
} else {
    $GLOBALS['dbInfo']['default'] = array(
        'enable' => true,
        'dbType' => 'mysqli',
        'dbHost' => '123.57.34.130',
        'dbPort' => 3306,
        'dbName' => 'lhc',
        'dbUser' => 'root',
        'dbPass' => 'mygod123456',
    );
    
    $GLOBALS['redisInfo']['logstash_redis'] = array(
        'host' => '123.57.34.130',
        'pass' => 'zcjmygod123456abc987',
        'port' => 6379
    );
    
    // 站点URL，最后不带斜杠
    define('SITE_DIR', '/lh.ben.com/');
}
/* 
$GLOBALS['dbInfo']['default'] = array(
    'enable' => true,
    'dbType' => 'mysqli',
    'dbHost' => '123.57.34.130',
    'dbPort' => 3306,
    'dbName' => 'lhc',
    'dbUser' => 'root',
    'dbPass' => 'root',
);
 */
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . SITE_DIR);


define('TIME_STOP_SALE', '21:15:00');
define('TIME_RESULT', '21:30:00');

define("TYPE_MODULE_CALL", "yb_mobile_modulecall");
define("TYPE_SELF_CALL", "yb_mobile_selfcall");
define("TYPE_CUSTOM_LOG", "yb_mobile_customlog");


//end of script
