<?php

/**
 * Diy报表配置
 * @author benzhan
 */
class DiyConfigController extends Controller {
    
    function actionEdit($args) {
        require_once ROOT_PATH . 'diyConfig.inc.php';
        
        $rules = array(
            'tableId' => array('string', 'nullable' => true)
        );
        Param::checkParam($rules, $args);
        
        $tableId = $args['tableId'];
        
        if ($tableId) {
            $oConfigTable = new Diy_Table();
            $tableInfo = $oConfigTable->getTableInfo($tableId);
            $link = SITE_URL . 'DiyData/report?tableId=' . $tableId;
        } else {
            $link = "新建的页面,保存后自动生成...";
        }
        
        $oConfig= new Diy_Config();
        $sourceHosts = $oConfig->getHosts();
        $map = $GLOBALS['diy']['map'];
        $pageSizes = $GLOBALS['diy']['pageSizes'];

        $args = compact('tableInfo', 'sourceHosts', 'link', 'map', 'pageSizes');
        $this->tpl->assign($args);
        $this->tpl->display('diy_config');
    }
    
    function actionGetDbs($args) {
        $rules = array(
            'sourceHost' => 'ip',
            'sourcePort' => 'int',
            'sourceUser' => array('string', 'emptyable' => true),
            'sourcePass' => array('string', 'emptyable' => true),
        );
        Param::checkParam($rules, $args);
        
        $oConfig= new Diy_Config();
        return $oConfig->getDbs($args);
    }
    
    function actionGetTables($args) {
        $rules = array(
            'sourceHost' => 'ip',
            'sourcePort' => 'int',
            'sourceDb' => 'string',
            'sourceUser' => array('string', 'emptyable' => true),
            'sourcePass' => array('string', 'emptyable' => true),
        );
        Param::checkParam($rules, $args);
    
        $oConfig= new Diy_Config();
        return $oConfig->getTables($args);
    }
    
}

