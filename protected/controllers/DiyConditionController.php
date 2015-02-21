<?php

/**
 * Diy条件
 * @author benzhan
 */
class DiyConditionController extends Controller {

    /**
     * 条件
     * @author benzhan
     */
    function actionIndex($args) {
        $rules = array(
            'tableId' => 'string',
            'where' => array('array', 'nullable' => true),
        );
        Param::checkParam($rules, $args);
        require_once ROOT_PATH . 'diyConfig.inc.php';
        
        //将当前的查询条件合并上默认的查询条件
        if (!$args['notDefault']) {
            $oTable = new Diy_Table();
            $defaultCondition = $oTable->getTableMeta(array('tableId' => $args['tableId'], 'metaKey' => 'tableDefaultCondition'));
            $defaultCondition = (array)  json_decode($defaultCondition['metaValue'], true);
            $defaultCondition = $this->_formatDefaultCondition($defaultCondition);
             
            $defaultCondition && $args['where'] = $this->_mergeDefaultCondition($args['where'], $defaultCondition);
        }
        
        $fields = $oTable->getFields($args['tableId']);
        $args['fields'] = $this->_adaptFields($fields);
        
        $this->tpl->assign($args);
        $this->tpl->assign('opts', $GLOBALS['diy']['opts']);
        $this->tpl->display('diy_condition');
    }
    
    /**
     * 执行默认条件的回调
     * @param array $defaultCondition
     * @author benzhan
     */
    private function _formatDefaultCondition($defaultCondition) {
        foreach ($defaultCondition as $i => $data) {
            isset($data[2]) && $data[2] = $this->_formatCondition($data[2]);
            isset($data[3]) && $data[3] = $this->_formatCondition($data[3]);
    
            $defaultCondition[$i] = $data;
        }
        
        return $defaultCondition;
    }

    private function _formatCondition($val) {
        if (preg_match('/^::/', $val)) {
            $callback = substr($val, 2);
            $callback = create_function('', $callback);
            if ($callback) {
                return $callback();
            }
        }
         
        if (is_array($val)) { return current($val); }
         
        return $val;
    }
    
    private function _mergeDefaultCondition($where, $defaultCondition) {
        $map = array();
        foreach ($defaultCondition as $i => $value) {
            $map[$value[0]][$value[1]]  = $i;
        }
         
        foreach ($where as $value) {
            $key = $value[0];
            $opt = $value[1];
             
            $i = $map[$key][$opt];
            if (isset($i)) {
                $defaultCondition[$i] = $value;
            } else {
                $defaultCondition[] = $value;
            }
        }
         
        return $defaultCondition;
    }
    

    /**
     * 适应当前的列配置
     * @param $args array(array(), array())
     * @author benzhan
     */
    private function _adaptFields($fields) {
        $tData = arrayFormatKey($fields, 'fieldName');
        foreach ($fields as $field) {
            $tData[$field['fieldName']] = arrayFilter($field, 'fieldType', 'fieldName', 'fieldCName', 'defaultDisplay', 'fieldDisplay');
            $tData[$field['fieldName']]['enum'] = $field['fieldLength'];
    
            //判断enum类型是否存在字段长度
            if ($field['fieldType'] == "enum" && !$field['fieldLength'] && $field['fieldMap']) {
                $fieldMap = json_decode($field['fieldMap'], true);
                if ($fieldMap) {
                    $funcName = $fieldMap['name'];
                    $enum = Diy_Plugin_Map::$funcName($fieldMap);
                    foreach ($enum as $k => $v) {
                        $enum[$k] = join(",", $v);
                    }
                    $tData[$field['fieldName']]['enum'] = $enum;
                }
            }
        }
        return $tData;
    }
}
