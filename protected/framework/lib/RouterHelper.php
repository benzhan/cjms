<?php

/**
 * 工具类
 *
 * @author benzhan
 * @version 1.0 update time: 2014-7-11
 * @package lib
 */
class RouterHelper {
    private $mParts;

    function __construct($url = "") {
        if (defined('SITE_DIR')) {
            $url = str_replace(SITE_DIR, '', $url);
        }
        $this->mParts = preg_split("/\//", $url, null, PREG_SPLIT_NO_EMPTY);
    }

    function display($classInfos) {
        $template = Template::init();
        $template->assign('classInfos', $classInfos);
        $template->display('doc');
    }

    function genDoc($className, $funcName) {
        $doc = $_GET['doc'];
        if ($doc) {
            $objDoc = new Doc();
            switch ($doc) {
                case "module" :
                    $classInfos = $objDoc->getClassInfos(ROOT_PATH . 'controllers/');
                    $this->display($classInfos);
                    break;
                case "class" :
                    $classInfo = $objDoc->getClassInfo($className);
                    $api = str_replace("_", "/", $className);
                    $api = str_replace("Controller", '', $api);
                    $classInfos = array();
                    $classInfos[$api] = $classInfo;
                    $this->display($classInfos);
                    break;
                case "func" :
                    $params = $objDoc->getFuncInfo($className, $funcName);
                    
                    $oClass = new $className();
                    if (method_exists($oClass, $funcName)) {
                        $args = array(
                            "__getRules" => true,
                            '__params' => $params
                        );
                        $rules = $oClass->$funcName($args);
                    } else {
                        Response::error(CODE_NOT_EXIST_INTERFACE, null, "method {$funcName} is not exist.");
                    }
                    break;
            }
            
            exit();
        }
    }

    function getClassName() {
        $parts = $this->mParts;
        $len = count($parts);
        $classParts = array();
        for ($i = 0; $i < $len - 1; $i++) {
            // $classParts[] = ucfirst(strtolower($parts[$i]));
            $classParts[] = $parts[$i];
        }
        
        if ($len >= 3 && $classParts[$len - 2] == $classParts[$len - 3]) {
            unset($classParts[$len - 2]);
        }
        
        $className = join("_", $classParts);
        $className || $className = 'Default';
        
        return $className . 'Controller';
    }

    function getFunName() {
        $parts = $this->mParts;
        $funcName = end($parts);
        $pos = strpos($funcName, '?');
        if ($pos !== false) {
            $funcName = substr($funcName, 0, $pos);
        }
        $funcName || $funcName = 'Index';
        return "action{$funcName}";
    }

    function error($funcName, $msg) {
        $parts = explode(".", $funcName);
        if (count($parts) >= 2) {
            header('HTTP/1.1 404 Not Found');
            exit();
        } else {
            Response::error(CODE_NOT_EXIST_INTERFACE, null, $msg);
        }
    }

}

//end of script
