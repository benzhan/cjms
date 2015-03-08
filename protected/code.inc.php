<?php

//常规错误码
define("CODE_SUCCESS", 0); 
define("CODE_UNKNOW_ERROT", -1);
define("CODE_NOT_EXIST_INTERFACE", -2);
define("CODE_PARAM_ERROR", -3);
define("CODE_INTER_ERROR", -4);
define("CODE_USER_LOGIN_FAIL", -5);
define("CODE_SIGN_ERROR", -6);
define("CODE_UNLOGIN_ERROR", -7);
define("CODE_NO_PERMITION", -8);
define("CODE_NORMAL_ERROR", -9);
define("CODE_DB_ERROR", -10);
define("CODE_REQUEST_TIMEOUT", -11);
define("CODE_REQUEST_ERROR", -12);


$GLOBALS['code_map'] = array(
    // 常规错误码
    CODE_SUCCESS => '成功',
    CODE_UNKNOW_ERROT => '未知错误', // 这个需要告警的错误码
    CODE_NOT_EXIST_INTERFACE => '接口不存在',
    CODE_PARAM_ERROR => '参数错误',
    CODE_INTER_ERROR => '系统繁忙，请稍后再试', // http请求返回错误
    CODE_USER_LOGIN_FAIL => '登录态失效，请重新登录',
    CODE_SIGN_ERROR => '签名错误',
    CODE_UNLOGIN_ERROR => '没有登录',
    CODE_NO_PERMITION => '没有权限',
    CODE_NORMAL_ERROR => '常规错误',
    CODE_DB_ERROR => '系统繁忙，请稍后再试', // 这个需要告警的错误码
    CODE_REQUEST_TIMEOUT => '网络请求超时，请重试',
    CODE_REQUEST_ERROR => '访问外部接口出错，请稍后重试', // 这个需要告警的错误码
);

//end of script
