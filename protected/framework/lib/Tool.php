<?php

/**
 * 工具类
 * @author benzhan
 * @version 1.0 update time: 2011-10-13
 * @package lib
 */
class Tool {

    /**
     * 调试日志
     * @author benzhan
     * @param string/array $content 调试内容
     */
    public static function debug($content) {

    }
    
    /**
     * 记录流水日志
     * @author benzhan
     * @param string/array $content 流水日志内容
     */
    public static function log($content) {

    }
        
    /**
     * 记录错误日志
     * @author benzhan
     * @param string/array $content 错误日志内容
     */
    public static function err($content) {

    }
    
    /**
     * 获取分页Html
     * @param array $args array('rowNum', 'page', 'pageSize')
     * @author benzhan
     */
    public static function getPageHtml($args) {
        isset($args['pageSize']) || $args['pageSize'] = 20;
        isset($args['page']) || $args['page'] =  (int) $_REQUEST['_page'];
        
        // 不需要分页
        if ($args['rowNum'] <= $args['pageSize']) {
            return '';
        }
        
        $args['options'] = array(10, 20, 50, 100);
        $args['total'] = ceil($args['rowNum'] / $args['pageSize']);
        
        $tpl = Template::init();
        $tpl->assign($args);
        return $tpl->fetch('bootstrap_pager');
    }
    
}

//end of script
