<?php

define("CODE_SUCCESS", 0);
define("CODE_UNKNOW_ERROT", -1);
define("CODE_NOT_EXIST_INTERFACE", -2);
define("CODE_PARAM_EMPTY", -3);
define("CODE_PARAM_ERROR", -4);
define("CODE_INTER_ERROR", 5);
define("CODE_NOT_ALLOW_GET", -6);
define("CODE_NOT_PUBLIC_API", -7);
define("CODE_REQUET_TIME_OUT", -8);
define("CODE_NORMAL_ERROR", -9);

$GLOBALS['globalCodeMap'] = array(
    CODE_SUCCESS => '成功',
    CODE_UNKNOW_ERROT => '未知错误',
    CODE_NOT_EXIST_INTERFACE => '接口不存在',
    CODE_PARAM_EMPTY => '参数不能为空',
    CODE_PARAM_ERROR => 'param error',
    CODE_INTER_ERROR => '系统繁忙，请稍后再试', // http请求返回错误
    CODE_NOT_ALLOW_GET => 'get is not allow',
    CODE_NOT_PUBLIC_API => 'API没有对外开放',
    CODE_REQUET_TIME_OUT => '网络请求超时',
    CODE_NORMAL_ERROR => 'normal error',
);

//end of script
