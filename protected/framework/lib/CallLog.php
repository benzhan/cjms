<?php

/**
 * 调用类日志
 *
 * @author benzhan
 * @package lib
 */
class CallLog {
    private static $call_id = null;
    private static $objRedis = null;
    
    public static function getLogRedis() {
        if (self::$objRedis == null) {
            $config = $GLOBALS['logstash_redis'];
            self::$objRedis = new Redis();
            self::$objRedis->connect($config['host'], $config['port']);
        }
        
        return self::$objRedis;
    }

    public static function getCallId() {
        if (!self::$call_id) {
            self::$call_id = (microtime(true) * 10000) . rand(1000, 10000);
        }
        return self::$call_id;
    }

    public static function logModuleCall($method, $toUrl, $postData, $response, $startTime) {
        $data = array();
        $data['from_call_id'] = self::getCallId();
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('?', $url);
        $data['from_url'] = $parts[0];
        $data['method'] = $method;
        
        $parts = explode('?', $toUrl);
        $data['to_url'] = $parts[0];
        $getParam = $parts[1];
        
        if (is_array($postData)) {
            $postParam = http_build_query($postData);
        } else {
            $postParam = $postData;
        }
        
        if ($getParam && $postParam) {
            $param = $getParam . '&' . $postParam;
        } else if ($getParam) {
            $param = $getParam;
        } else {
            $param = $postParam;
        }
        
        $data['param'] = $param;
        if ($response !== false) {
            $objResult = json_decode($response, true);
            $data['code'] = $objResult['code'];
            $data['response'] = substr($response, 0, 3000);
        } else {
            $data['code'] = CODE_REQUEST_TIMEOUT;
        }
        
        $data['delay'] = microtime(true) - $startTime;
        $data['server_ip'] = self::getWanIp();
        
        $pushData = array(
            'message' => $data,
            'type' => TYPE_MODULE_CALL,
            'time' => date('Y-m-d H:i:s', $startTime)
        );
        
        $config = $GLOBALS['logstash_redis'];
        if ($config && class_exists("Redis")) {
            $objRedis = self::getLogRedis();
            $objRedis->connect($config['host'], $config['port']);
            $objRedis->rPush('logstash:redis', json_encode($pushData));
        }
    }

    public static function logSelfCall($code, $response) {
        global $startTime;
        
        $data = array();
        $data['call_id'] = self::getCallId();
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('?', $url);
        $data['url'] = $parts[0];
        $data['method'] = $_SERVER['REQUEST_METHOD'];
        $getParam = $parts[1];
        $postParam = http_build_query($_POST);
        
        if ($getParam && $postParam) {
            $param = $getParam . '&' . $postParam;
        } else if ($getParam) {
            $param = $getParam;
        } else {
            $param = $postParam;
        }
        
        $data['param'] = $param;
        $data['cookie'] = http_build_query($_COOKIE);
        if (Response::$debugMsg) {
            $response = '【debugMsg:' . Response::$debugMsg . "】{$response}";
        }
        
        $data['response'] = substr($response, 0, 3000);
        $data['code'] = $code;
        $data['delay'] = microtime(true) - $startTime;
        if ($data['delay'] > $GLOBALS['xhprof_save_value']) {
            $GLOBALS['xhprof_save'] = 1;
        }

        $data['server_ip'] = self::getWanIp();
        $data['client_ip'] = getip();
        $data['useragent'] = $_SERVER['HTTP_USER_AGENT'];
        
        $pushData = array(
            'message' => $data,
            'type' => TYPE_SELF_CALL,
            'time' => date('Y-m-d H:i:s', $startTime)
        );
        
        $config = $GLOBALS['logstash_redis'];
        if ($config && class_exists("Redis")) {
            $objRedis = self::getLogRedis();
            $objRedis->connect($config['host'], $config['port']);
            $objRedis->rPush('logstash:redis', json_encode($pushData));
        }
        
    }

    public static function getWanIp() {
        $apcKey = 'log_wan_ip';
        $wanIp = apc_fetch($apcKey);
        
        if ($wanIp) {
            $matches = array();
            preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $wanIp, $matches);
            if ($matches) {
                return $wanIp;
            }
        }
        
        $output = array();
        $return_var = array();
        $ret = exec("ifconfig", $output, $return_var);
        
        $datas = array();
        $key = null;
        foreach ($output as $row) {
            $matches = array();
            preg_match('/^[^\s]+/', $row, $matches);
            if ($matches) {
                $key = $matches[0];
            } else {
                preg_match('/inet addr:(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $row, $matches);
                if ($matches && $key) {
                    $datas[$key] = $matches[1];
                    $key = '';
                }
            }
        }
        
        foreach ($datas as $key => $ip) {
            $matches = array();
            preg_match('/^eth\d+$/', $key, $matches);
            if ($matches && !self::isInternalIp($ip)) {
                break;
            }
        }
        
        $ip || $ip = $_SERVER['SERVER_ADDR'];
        apc_add($apcKey, $ip, 3600 * 24);
        
        return $ip;
    }

    private static function isInternalIp($ip) {
        $ip = ip2long($ip);
        $a = ip2long('10.0.0.0'); // A类网的预留ip
        $b = ip2long('172.16.0.0'); // B类网的预留ip
        $c = ip2long('192.168.0.0'); // C类网的预留ip
        return ($ip & $a) === $a || ($ip & $b) === $b || ($ip & $c) === $c;
    }

}

//end of script
