<?php
/**
 * 模板引擎
 *
 * Copyright(c) 2005-2008 by 陈毅鑫(深空). All rights reserved
 *
 * To contact the author write to {@link mailto:shenkong@php.net}
 *
 * @author 陈毅鑫(深空)
 * @version $Id: Template.class.php 1687 2008-07-07 01:16:07Z skchen $
 * @package common
 */

class Template {
    protected static $obj;

    public $vars;
    public $includeFiles;
    public $includeFile;
    public $templates;
    public $template;
    public $contents;
    protected $_content;
    protected $_contents;
    protected $_path;


    protected function __construct() {
        $this->vars = array();
        require_once ROOT_PATH . "framework/lib/template.func.php";
    }

    /**
     * 初始化模板引擎
     *
     * @return object 模板引擎对象
     */
    public static function &init() {
        if (is_null(self::$obj)) {
            self::$obj = new Template();
        }
        return self::$obj;
    }

    /**
     * 注册模板变量
     *
     * 注册模板变量后在模板里就可以直接使用该变量,注册与被注册变量名不一定要一样
     * 如:$template->assign('var', $value);
     * 意思是将当前变量$value注册成模板变量var,在模板里就可以直接调用$val
     *
     * @param string $var 注册到模板里的变量名的字符串形式,不包含$
     * @param mixed $value 需要注册的变量
     */
    public function assign($var, $value) {
        if (is_array($var)) {
            foreach ($var as $key => $val) {
                $this->vars[$key] = $val;
            }
        } else {
            $this->vars[$var] = $value;
        }
    }
    
    
    private function getDefaultTemplate() {
        $traceList = debug_backtrace();
        foreach ($traceList as $trace) {
            if ($trace['file'] !=  ROOT_PATH .'lib' . DIRECTORY_SEPARATOR . 'Template.class.php') {
                //将.php换成.html
                $temp = substr($trace['file'], 0, -4);
                $lastPost = strrpos($temp, DIRECTORY_SEPARATOR);
                $fileName = substr($temp, $lastPost + 1) . '.html';
                $path = substr($temp, 0, $lastPost) . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $fileName;
                break;
            }
        }
        
        return $path;
    }

    /**
     * 解析模板文件
     *
     * 解析模板,并将变量植入模板,解析完后返回字符串结果
     *
     * @param unknown_type $templates
     * @return unknown
     */
    public function fetch($templates) {
        if (is_array($templates)) {
            $this->templates = $templates;
        } else {
            $this->templates = func_get_args();
        }
        
        extract($this->vars);

        $this->_contents = '';
        if ($this->templates) {
            foreach ($this->templates as $this->template) {
                $this->_path = $this->getPath($this->template);
                
                ob_end_clean();
                ob_start();
                require $this->_path;
                $this->_content = ob_get_contents();
                ob_end_clean();
                ob_start();
                $this->_contents .= $this->_content;
                $this->contents[$this->template] = $this->_content;
            }
        } else {
            $this->_path = $this->getDefaultTemplate();
            
            ob_end_clean();
            ob_start();
            require $this->_path;
            $this->_content = ob_get_contents();
            ob_end_clean();
            ob_start();
            $this->_contents .= $this->_content;
            $this->contents[$this->template] = $this->_content;
        }

        return $this->_contents;
    }

    public function getPath($path) {
        $path = explode("_", $path);
        $num = count($path);
        if ($num == 1) {
            return ROOT_PATH . "views" . DIRECTORY_SEPARATOR . $path[0] . ".html";
        } elseif ($num > 1) {
            $templatePath = '';
            $templatePath = $path[$num - 1];
            array_pop($path);
            $templatePath = ROOT_PATH . implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $templatePath . ".html";
            return $templatePath;
        } else {
            return false;
        }
    }

    public function display($templates = array()) {
        if (!is_array($templates)) {
            $templates = func_get_args();
        }

        exit($this->fetch($templates));
    }
}

//end of script
