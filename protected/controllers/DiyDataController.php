<?php

/**
 * Diy数据
 * @author benzhan
 */
class DiyDataController extends Controller {

    function actionReport($args) {
        $this->_checkParam($args);
        
        $objCondition = new DiyConditionController();
        $conditionHtml = $objCondition->actionIndex($args, false);
        $tableHtml = $this->actionTable($args, false);
        
        $this->tpl->assign(compact('conditionHtml', 'tableHtml'));
        $this->tpl->display('diy_report');
    }
    
    /**
     * 表格
     * @author benzhan
     */
    function actionTable($args, $display = true) {
        $this->_checkParam($args);
        
        try {
            $tableId = $args['tableId'];
            $oConfig = new Diy_Table();
            $tableInfo = $oConfig->getTableInfo($tableId);
            $fields = $oConfig->getFields($tableId);
             
            $page = $args['keyWord']['_page'];
            $pageSize = $args['keyWord']['_pageSize'] ? $args['keyWord']['_pageSize'] : $tableInfo['pagination'];
            $result = $this->_getPageData($args, $page, $pageSize);
            $datas = $result['datas'];
            $other = $result['other'];
            $other['tableInfo'] = $tableInfo;
            $other['fields'] = $this->_getFormatFields($fields, $args['keyWord']);
            $other['showGroupBy'] = $args['keyWord']['_showGroupBy'];
            $other['hideNoGroupBy'] = $args['keyWord']['_hideNoGroupBy'];

            $oData = new Diy_Data();
            $datas = $oData->formatDatas($datas, $other['fields'], $args['keyWord']['_fields']);
            
            $this->assignTableArgs($datas, $other);
            $template = Template::init();
            if ($display) {
                $template->display('diy_table');
            } else {
                return $template->fetch('diy_table');
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    
    private function _checkParam(&$args) {
        isset($args['where']) && $args['where'] = json_decode($args['where'], true);
        isset($args['keyWord']) && $args['keyWord'] = json_decode($args['keyWord'], true);
        
        $rules = array(
            'tableId' => 'string',
            'where' => array('array', 'nullable' => true),
            'keyWord' => array('object',
                'nullable' => true,
                'elem' => array(
                    '_page' => array('int', 'nullable' => true),
                    '_pageSize' => array('int', 'nullable' => true),
                    '_sortKey' =>  array('string', 'nullable' => true),
                    '_sortDir' =>  array('string', 'enum' => array('ASC', 'DESC'), 'nullable' => true),
                ),
            ),
        );
        Param::checkParam($rules, $args);
    }
    
    private function _getPageData($args, $page, $pageSize) {
        //特殊处理page为-1的参数
        if ($page == -1) {
            $page = 1;
            $pageSize = 1000000;
        } else if (!$page) {
            $page = 1;
        }
        $args['keyWord']['_limit'] = ($page - 1) * $pageSize . "," . $pageSize;
         
        $oData = new Diy_Data();
        $datas = $oData->getTableData($args);
        $rowNum = $oData->getTableDataNum($args);
        //如果没那么多页，则需要重新查询
        if ($rowNum < (int) $args['keyWord']['_limit'] ) {
            $page = 1;
            $args['keyWord']['_limit'] = $page ? ($page - 1) * $pageSize . "," . $pageSize : $pageSize;
            $datas = $oData->getTableData($args);
        }
         
        $keyWord = $args['keyWord'];
        $other = compact('rowNum', 'page', 'pageSize', 'keyWord');
        return compact('datas', 'other');
    }

    /**
     * 初始化列的groupby和cal属性
     * @param array $fields
     * @param array $args
     * @author benzhan
     */
    private function _getFormatFields($fields, $args) {
        $keyWords = array('_min', '_max', '_avg', '_sum', '_count', '_groupby');
    
        foreach ($keyWords as $keyWord) {
            if (!$args[$keyWord]) { continue; }
            $fieldNames = explode(',', $args[$keyWord]);
            foreach ($fieldNames as $fieldName) {
                if ($keyWord == '_groupby') {
                    $fields[$fieldName]['groupby'] = '_groupby';
                } else {
                    $fields[$fieldName]['cal'] = $keyWord;
                }
            }
        }
    
        return $fields;
    }

    /**
     * 格式化数据，翻译字段、执行回调
     * @param array $datas 二维数据
     * @param array $fields 列信息
     * @author benzhan
     */
    public function formatDatas($datas, $fields, $selectFields = array()) {
        $map = $this->_getMap($datas, $fields);
    
        $funcs = $tDatas = array ();
        foreach ( $datas as $rowIndex => $row ) {
            $tempRow = $row;
            foreach ( $row as $fieldName => $value ) {
                $field = $fields[$fieldName];
    
                //字段翻译
                if ($field['fieldLength']) {
                    $v = $field['fieldLength'][$row[$fieldName]];
                    isset($v) || $v = $row[$fieldName];
                    $tempRow[$fieldName] = $row[$fieldName] = $v;
                }
    
                //字典表
                if ($field['fieldMap']) {
                    $arr = $map[$fieldName][$row[$fieldName]];
                    $v = $arr ?  join(',', (array) $arr) : $row[$fieldName];
                    $value = $tempRow[$fieldName] = $row[$fieldName] = $v;
                }
    
                //字段回调
                if ($field['callBack']) {
                    if (!isset($funcs[$fieldName])) {
                        $funcs[$fieldName] = create_function('$row, $_row, $_field, $_rowIndex, $rowIndex, $_val', $field['callBack']);
                    }
    
                    $row[$fieldName] = $funcs[$fieldName]($tempRow, $tempRow, $field, $rowIndex, $rowIndex, $value);
                }
            }
            $tDatas[] = $row;
        }
    
        return $this->_filterData($tDatas, $fields, $selectFields);
    }
    
    protected function assignTableArgs($datas, $other) {
        $fieldNames = $this->_getSortKey($datas, $other);
    
        $mergeFieldData = $this->_mergeCol($datas, $other['fields']);
        $other['fields'] = $this->_getRowIcon($other['fields'], $other['showGroupBy']);
        
        $pagerHtml = Tool::getPageHtml($other);
        // var_dump(compact('datas', 'fieldNames', 'other', 'mergeFieldData', 'pagerHtml'));exit;
        
        $template = Template::init();
        $template->assign(compact('datas', 'fieldNames', 'other', 'mergeFieldData', 'pagerHtml'));
    }
    
    private function _getSortKey($datas, $other) {
        if (!$datas) { return array(); }
    
        $data = current($datas);
        $fieldNames = array_keys($data);
    
        $map = array();
        foreach ($fieldNames as $fieldName) {
            $field = $other['fields'][$fieldName];
            $map[$fieldName] = $field['fieldSortName'];
        }
    
        return $map;
    }
    
    /**
     * 适当排序，然后再合并相同的行
     * @param array $datas 二维数组
     * @param array $fields
     * @author benzhan
     */
    private function  _mergeCol(&$datas, $fields) {
        $objData = new Diy_Data();
        $mergeFieldNames = $objData->getMergeField($fields);
        if (!$mergeFieldNames) { return; }
    
        //$this->_sortData($datas, $mergeFieldNames);
        $mergeFieldData = $count = array();
        $lastFieldName = '';
    
        foreach ($datas as $i => $data) {
            foreach ($data as $fieldName => $value) {
                if (!$mergeFieldNames[$fieldName]) { continue; }
                if ($lastFieldName) {
                    //判断前一列是否合并了
                    $lastFieldIsMerge = $mergeFieldData[$lastFieldName][$i] == 0;
                } else {
                    $lastFieldIsMerge = false;
                }
    
                //对需要合并的列则判断是否跟上一列相同，并且前一列是合并的
                if ($value == $datas[$i - 1][$fieldName] && $lastFieldIsMerge) {
                    $count[$fieldName]++;
                    $mergeFieldData[$fieldName][$i] = 0;
                } else {
                    if ($count[$fieldName] > 1) {
                        $mergeFieldData[$fieldName][$i - $count[$fieldName]] = $count[$fieldName];
                    }
                    $mergeFieldData[$fieldName][$i] = $count[$fieldName] = 1;
                }
    
                $lastFieldName = $fieldName;
            }
        }
    
        $i++;
        foreach ($mergeFieldNames as $fieldName) {
            if ($count[$fieldName] > 1) {
                $mergeFieldData[$fieldName][$i - $count[$fieldName]] = $count[$fieldName];
            } else {
                $count[$fieldName] = 1;
            }
        }
    
        return $mergeFieldData;
    }
    

    /**
     * 获取分组或计算的图标
     * @param unknown_type $fields
     * @author benzhan
     */
    private function _getRowIcon($fields, $showGroupBy) {
        $style = ($showGroupBy ? '' : "style='display:none;'");
        foreach ($fields as $k => $field) {
            if ($field['fieldDisplay'] & 1) {
                // 这个是指标
                $field['icon'] = "<a class='cal icon {$field['cal']}' {$style} title=\"
                <select>
                <option value=''>原始</option>
                <option value='_max'>最大</option>
                <option value='_min'>最小</option>
                <option value='_avg'>平均</option>
                <option value='_sum'>总和</option>
                <option value='_count'>计数</option>
                </select>
                \"></a>";
            } else if ($field['fieldDisplay'] & 2) {
                // 这个是纬度
                $className = $field['groupby'] ? 'groupby' : 'noGroupby';
                $field['icon'] = "<a {$style} class='{$className} icon'></a>";
            }
            
            $fields[$k] = $field;
        }
        
        return $fields;
    }
    
}
