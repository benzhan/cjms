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
    
    /**
     * 读取数据库系信息
     * @author benzhan
     * @param unknown $args
     */
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
    
    /**
     * 获取表格信息
     * @author benzhan
     * @param unknown $args
     */
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
    

    public function actionGetFieldTable($args) {
        require_once ROOT_PATH . 'diyConfig.inc.php';
        
        $rules = array(
            'tableId' => 'string',
            'loadType' => array('int', enum => array(1, 2, 3)),
        );
        Param::checkParam($rules, $args);
        
        $tableId = $args['tableId'];
        if ($tableId && $args['loadType'] & 1) {
            $oConfigTable = new Diy_Table();
            $fields = $oldFields = $oConfigTable->getFields2($tableId);
        }
    
        if ($args['loadType'] & 2) {
            $objConfig = new Diy_Config();
            $newFields = $objConfig->getFields($args);
            $fields = $this->_processFields($newFields, array());
        }
    
        if ($args['loadType'] == 3) {
            $fields = $this->_processFields($newFields, $oldFields);
        }
        
        $fieldTypes = $GLOBALS['diy']['fieldTypes'];
        $map = $GLOBALS['diy']['map'];
        $data = compact('fields', 'fieldTypes', 'map');
        
        $template = Template::init();
        $template->assign(compact('data'));
        $template->display('diy_config_table');
    }
    
    private function _processFields($newFields, $fields) {
        $newFields = ArrayformatKey($newFields, 'Field');
        foreach ($newFields as $field) {
            $fieldName = $field['Field'];
            if (!$fields[$fieldName]) {
                $newField = array();
                $newField['fieldType'] = $fieldType = $field['Type'];
                $newField['fieldName'] = $newField['fieldSortName'] = $fieldName;
                $newField['fieldCName'] = $field['Comment'] ? $field['Comment'] : $fieldName;
                $newField['fieldPostion'] = count($fields);
    
                if (preg_match('/\w+Id/', $fieldName) || ($fieldType != 'int' && $fieldType != 'float' && $fieldType != 'double')) {
                    $newField['fieldDisplay'] = 2;
                } else {
                    $newField['fieldDisplay'] = 1;
                }
    
                $fields[$fieldName] = $newField;
            }
        }
    
        return $fields;
    }
    
}

