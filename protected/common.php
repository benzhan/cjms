<?php

$startTime = microtime(true);

// 根路径 ROOT_PATH 定义, 5.3以后可以直接使用__DIR__
// 所有被包含文件均须使用此常量确定路径
define('ROOT_PATH', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('BASE_DIR', ROOT_PATH . '/../');

// 公共函数
require_once ROOT_PATH . "extensions/function_extend.php";

// 公共配置文件
require_once ROOT_PATH . 'config.inc.php';

/**
 * 根据公共配置文件的 DEBUG 常量定义来选择出错提示，
 * 在程序正式发布时候，应将改常量定义为 false
 */
if (true == DEBUG) {
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(0);
}


//end of script
