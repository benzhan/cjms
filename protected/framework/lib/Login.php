<?php

/**
* 用户信息类
* @author benzhan
* @package common
*/
class Login {
    /**
     * 检查管理员权限
     * @param unknown_type $data
     */
    private static function checkPermissions($username) {
    	
    	$objUser = new TableHelper("cUser");
    	$where = array("userId" => $username, 'enable' => 1);
    	$row = $objUser->getRow($where);
    	// var_dump($row);exit();
    	
    	return $row != null;
    }
        
    private static function checkUdb() {
    	$url = "http://webapi.duowan.com/api_udb2.php";
    	
    	if (!$_COOKIE["username"]) {
    		return false;
    	}
    	
    	$data = array();
    	foreach ($_COOKIE as $key => $value) {
    		$data["COOKIE[{$key}]"] = $value;
    	}
    	
    	$data["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
    	$data["HTTP_HOST"] = $_SERVER["HTTP_HOST"];
    	$data["format"] = "json";

    	$objHttp = new dwHttp();
    	$i = 0;
    	do {
        	$json = $objHttp->post($url, $data, 2);
        	$i++;
    	} while(!$json && $i < 3);
    	
    	return json_decode($json, true);
    }
    
    public static function checkLogin() {
        if ($_SESSION["username"]) {  	
        	if (!self ::checkPermissions($_SESSION["username"])) {
        		unset($_SESSION["username"]);
        		self::noPermission();
        	}
        } else {
        	$result = self::checkUdb();
        	if ($result && $result['username']) {
        		if (!self ::checkPermissions($result['username'])) {
        			unset($_SESSION["username"]);
        			unset($_SESSION["yyuid"]);
        			self::noPermission();
        		} else {
        			$_SESSION["username"] = $result['username'];
        			$_SESSION["yyuid"] = $result['yyuid'];
        		}
        	} else {
        		self::openLogin();
        	}
        }
    }
    
    private static function openLogin() {
    	$domain = COOKIE_DOMAIN;
    	$self = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	$selfUrl = urlencode($self);
    	Response::exitMsg("<script type='text/javascript'>top.location = '" . SITE_URL . "user/login?url={$selfUrl}';</script>");
    	
    }
    
    private static function noPermission() {
    	$domain = COOKIE_DOMAIN;
    	$self = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	$selfUrl = urlencode($self);
    	Response::exitMsg("<meta charset='utf-8'>对不起，您没有权限！请联系Ben开通！<a href='" . SITE_URL ."user/login?url={$selfUrl}'>换个帐号登录</a>");
    }
}

//end of script
