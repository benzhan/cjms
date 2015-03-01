<?php

// ini_set("memory_limit", "3072M");
require_once './protected/common.php';

header("Cache-control: private");
header("Content-type: text/html; charset=" . DEFAULT_CHARSET);

$url = $_SERVER["REQUEST_URI"];
$helper = new RouterHelper($url);
$className = $helper->getClassName();
$funcName = $helper->getFunName();


define('CONTROLLER_NAME', $className);
define('ACTION_NAME', $funcName);

$actionName = "action{$funcName}";
// 如果带有doc参数则会转化为文档模式
$helper->genDoc($className, $actionName);

if (class_exists($className)) {
    $oClass = new $className();
} else {
    $msg = "class {$className} is not exist.";
    $helper->error($actionName, $msg);
}

if (method_exists($oClass, $actionName)) {
    $args = $_GET;
    $args || $args = $_POST;
    
    $data = $oClass->$actionName($args);
    Response::success($data);
} else {
    $msg = "method {$actionName} is not exist.";
    $helper->error($actionName, $msg);
}





