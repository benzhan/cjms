<?php

// ini_set("memory_limit", "3072M");
require_once './protected/common.php';

header("Cache-control: private");
header("Content-type: text/html; charset=" . DEFAULT_CHARSET);

$url = $_SERVER["REQUEST_URI"];

$helper = new RouterHelper($url);
$className = $helper->getClassName();
$funcName = $helper->getFunName();

// 如果带有doc参数则会转化为文档模式
$helper->genDoc($className, $funcName);

if (class_exists($className)) {
    $oClass = new $className();
} else {
    $msg = "class {$className} is not exist.";
    $helper->error($funcName, $msg);
}

if (method_exists($oClass, $funcName)) {
    $args = $_GET;
    $args || $args = $_POST;
    
    $data = $oClass->$funcName($args);
    Response::success($data);
} else {
    $msg = "method {$funcName} is not exist.";
    $helper->error($funcName, $msg);
}





