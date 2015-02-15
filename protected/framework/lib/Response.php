<?php

/**
 * 响应类
 * Copyright(c) 2014 by benzhan. All rights reserved
 * To contact the author write to {@link mailto:zhanchaojiang@qq.com}
 * 
 * @author benzhan
 * @version Jul 19, 2014
 */
class Response {
    public static $ver = '1.0';
    private static $code = CODE_SUCCESS;
    public static $debugMsg;

    /**
     * 处理成功的响应
     * @param mixed $data
     */
    public static function success($data, $msg = null, $debugMsg = null) {      
        $code = CODE_SUCCESS;
        self::$debugMsg = $debugMsg;
        
        $response = array(
            'result' => 1,
            'code' => $code,
        );
        
        if (!$msg) {
            $msg = $GLOBALS['code_map'][$code];
        }
        
        if (DEBUG && $debugMsg) {
            $msg = $msg . " 【调试信息:{$debugMsg}】";
        }
        
        $response['msg'] = $msg;
        
        if (!empty($data)) {
            if (is_array($data)) {
                ksort($data);
            }
            
            $response['data'] = $data;
        }
        
        self::exitData($response);
    }

    /**
     * 处理失败的响应
     * @param int $code
     * @param string $msg
     * @param string $debugMsg
     * @param mixed $extData
     */
    public static function error($code, $msg = null, $debugMsg = null, $extData = null) {
        self::$debugMsg = $debugMsg;
        
        if (!$msg) {
            $msg = $GLOBALS['code_map'][$code];
        }
        
        if (DEBUG && $debugMsg) {
            $msg = $msg . " 【调试信息:{$debugMsg}】";
        }
        
        $response = array(
            'result' => 0 ,'code' => $code ,'msg' => $msg
        );
        
        if (!empty($extData)) {
            $response = array_merge($response, $extData);
        }
        
        self::exitData($response);
    }

    /**
     * 退出脚本，返回数据
     * @param array $response
     */
    public static function exitData(array $response) {
        self::$code = (int) $response['code'];
        $json = json_encode($response);
        self::exitMsg($json);
    }
    
    /**
     * 退出脚本，返回数据
     * @param array $response
     */
    public static function exitMsg($json) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
    
        if (!DEBUG) {
            ob_clean();
        }
        
        CallLog::logSelfCall(self::$code, $json);
        
        exit($json);
    }

}

//end of script
