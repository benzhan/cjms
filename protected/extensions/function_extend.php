<?php

function getip(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){ //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_CDN_SRC_IP'])){
			$ip = $_SERVER['HTTP_CDN_SRC_IP'];		
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){  //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else{
			$ip = $_SERVER['REMOTE_ADDR'];
	}
	return substr($ip, 0, 15);
}

/**
 * 删除并返回某个key的值
 * @author benzhan
 */
function arrayPop(&$array, $key) {
    $value = $array[$key];
    unset($array[$key]);
    return $value;
}

/**
 * 生成UUID
 */
function uuid() {
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $uuid;
}

/**
 * 从数组中过滤出指定key的子数组<br>
 * 支持arrayFilte(array $arr, array $keys)<br>
 * 支持arrayFilte(array $arr, $key1, $key2, $key3)<br>
 * @author benzhan
 */
function arrayFilter(array $array, $keys) {
    if (!is_array($keys)) {
        $args = func_get_args();
        //最后一个是数组
        $array = arrayPop($args, 0);
        $keys = $args;
    }
    
    $tData = array();
    foreach ($keys as $key) {
        $tData[$key] = $array[$key];
    } 
    return $tData;
}

/**
 * 格式化数组为 array(key => value)
 * @author benzhan
 * @param array $array
 * @param unknown $key
 * @return multitype:unknown
 */
function arrayFormatKey(array $array, $key) {
    $tData = array();
    foreach ($array as $value) {
        $tData[$value[$key]] = $value;
    }
    
    return $tData;
}

// 自动包含类
function myAutoload($className) {
    if (class_exists($className, false) || interface_exists($className, false)) {return false;}
    
    $types = array(
        'models',
        'extensions',
        'controllers',
        'framework',
        'framework' . DIRECTORY_SEPARATOR . 'lib',
    );
    
    $file_exists = false;
    $basePath = BASE_DIR . 'protected' . DIRECTORY_SEPARATOR;
    
    $pathName = DIRECTORY_SEPARATOR . str_replace("_", DIRECTORY_SEPARATOR, $className) . '.php';
    foreach ($types as $type) {
        $file = $basePath . $type . $pathName;
        if (file_exists($file)) {
            require_once $file;
            $file_exists = true;
        } 
        // var_dump($file);
    }
}
spl_autoload_register('myAutoload');

function error_handler ($error_level, $error_message, $file, $line) {
    $EXIT = FALSE;
    switch ($error_level) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error_type = 'Notice';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error_type = 'Warning';
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $error_type = 'Fatal Error';
            $EXIT = TRUE;
            break;
        default:
            $error_type = 'Unknown';
            $EXIT = TRUE;
            break;
    }
    
    if ($EXIT) {
        Util::log($error_message, $error_type, $file, $line);
    }
}
set_error_handler ('error_handler');


if( !function_exists('apc_store') ){
    function apc_store(){}
    function apc_add(){}
    function apc_fetch(){}
}

function var_log($msg, $label='notice'){
    Util::log($msg, $label, '', '', 1);
}


if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

