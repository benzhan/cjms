<?php

class Diy_Data {
    private $_dbKey = "Report";
    
    /**
     * 读取tableId的数据
     * @param array $args array ('tableId', 'fields', 'where', 'keyWord', 'getCount') 
     * int $tableId 表id
     * array $where 查询条件, array(array('field' => 'id', 'opt' => '=', 'val' => '12'), array(), array())
     * array $fields 选择列 array('fieldName1', 'fieldName2')
     * array $keyWord 查询关键字, array('_limit', '_sortKey', '_sortDir', '_lockRow', '_tableName', '_groupby')
     * string $getCount，是否要返回行数
     * @author benzhan
     */
    public function getTableData($args) {
        $oConfig = new Diy_Table();
        //$fields = $oConfig->getFields($args['tableId']);
        
        //修改为只查询展现出来的字段 edit by benzhan 2012-10-23
        $fields = $oConfig->getFields($args['tableId'], true);

        $fields = $this->_getCalField($fields, $args['keyWord']);
        if ($args['fields']) {
            //如果有传入fields，则按条件过滤
            $sortedFields = arrayFilter((array) $args['fields'], $fields);
            $sortedFields += $fields;
        } else {
            //如果没有传入fields，则过滤出默认的展示项
            $sortedFields = array();
            foreach ($fields as $fieldName => $field) {
                if ($field['defaultDisplay']) { 
                    $sortedFields[$fieldName] = $field; 
                }
            }
        }
        
        $oBase = $this->_getBase($args);
        $keyWord = $this->_getKeyWord($args, $fields); 
        //将字典表的值转化为原始值
        $args['where'] = $this->_getTranslateWhere($fields, $args['where']);
        $keyWord['_where'] = $this->getWhereStr($args['where']);
        $sql = 'SQL_CALC_FOUND_ROWS ';
        if ($sortedFields) {
            foreach ( $sortedFields as $fieldName => $field) {
                if ($field['fieldVirtualValue']) {
                    $sql .= "{$field['fieldVirtualValue']} AS `{$fieldName}`,";
                } else {
                    $sql .= "`{$fieldName}`,";
                }
            }
        } 
        
        $keyWord['_field'] = substr($sql, 0, -1);
        $datas = $oBase->getAll($keyWord);
        return $datas;
    }
    
    public function getTableDataNumByCount($args) {
    	$oConfig = new Diy_Table();
    	$fields = $oConfig->getFields($args['tableId']);
    	$fields = $this->_getCalField($fields, $args['keyWord']);
    	$sortedFields = arrayFilter((array) $args['fields'], $fields);
    	$sortedFields += $fields;
    
    	$oBase = $this->_getBase($args);
    	$keyWord = $this->_getKeyWord($args, $fields);
    	//将字典表的值转化为原始值
    	$args['where'] = $this->_getTranslateWhere($fields, $args['where']);
    	$keyWord['_where'] = $this->getWhereStr($args['where']);

    	$keyWord['_field'] = "count(*)";
    	$sql = $oBase->buildSql($keyWord, array(), true);
    	return $oBase->getDb()->getOne($sql);
    }    
    
    /**
     * 获取行数
     * @param array $args array ('tableId', 'fields', 'where', 'keyWord', 'getCount')  同上
     * @author benzhan
     */
    public function getTableDataNum($args) {
        $oBase = $this->_getBase($args);
        if(false) {
        	return $this->getTableDataNumByCount($args);
        }else {
        	return $oBase->getFoundRows();
        }
    }
    
    
    /**
     * 获取需要合并的列
     * @author benzhan
     */
    function getMergeField($fields) {
        $mergeFieldNames = array();
        foreach ($fields as $field) {
            if ($field['needMerge']) {
                $mergeFieldNames[$field['fieldName']] = $field['fieldName'];
                $count[$field['fieldName']] = 1;
            }
        }
    
        return $mergeFieldNames;
    }
    
    private function _getKeyWord(&$args, $fields) {
        $keyWord = $args['keyWord'];
        if (!$keyWord['_sortKey']) { 
            //判断是否有默认排序
            foreach ($fields as $field) {
                if ($field['defaultSortOrder'] && $field['defaultDisplay']) {
                    $keyWord['_sortKey'] = $field['fieldName'];
                    $keyWord['_sortDir'] = $field['defaultSortOrder'];
                    break;
                }
            }
        }
        
        //判断是否有合并列
        $mergeFieldNames = $this->getMergeField($fields);
        foreach ($mergeFieldNames as $fieldName) {
            $dir = $keyWord['_sortKey'] == $fieldName ? $keyWord['_sortDir'] : 'ASC';
            
            if ($keyWord['_sort']) {
                $keyWord['_sort'] .= ", {$fieldName} {$dir}";
            } else {
                $keyWord['_sort'] = "{$fieldName} {$dir}";
            }
        }
        
        $args['keyWord'] = $keyWord;
        return $keyWord;
    }
    
    /**
     * 将字典表的value翻译为key
     * @param array $fields
     * @param array $where
     * @author benzhan
     */
    private function _getTranslateWhere($fields, $where) {
        foreach ($where as $i => $value) {
            $fieldName = $value[0];
            $field = $fields[$fieldName];
            $key = $field['fieldVirtualValue'] ? $field['fieldVirtualValue'] : $fieldName;
            $where[$i][0] = $key;
    
            if (!$field['fieldMap']) {  continue; }
            $fieldMap = json_decode($field['fieldMap'], true);
            if (!$fieldMap) {  continue; }
    
            $funcName = $fieldMap['name'];
            $args = $fieldMap+ array('where' => $value[2], 'valKey' => true);
    
            $valueMap = Diy_Plugin_Map::$funcName($args);
            //这里比较恶心，有可能当前的值为key无需翻译，目前图片列表就是这样
            if (!$valueMap) { continue; }
            
            $where[$i] = array($key, 'in', $valueMap);
        }
    
        return $where;
    }
    
    private function _getBase(&$args) {
        $this->_checkTableDataParam($args);
        $args['where'] = ( array ) $args['where'];
        
        $oConfig = new Diy_Table();
        $tableInfo = $oConfig->getTableInfo($args['tableId']);
        
        $info = $this->_getTableNameInfo($tableInfo, $args['where']);
        $tableName = $info['tableName'];
        $args['where'] = $info['where'];
                
        $db = $this->getDb($tableInfo);
        
        $oBase = new TableHelper($tableName, $db);
        return $oBase;
    }
    
    /**
     * 检查是否存在非法的分组计算的情况
     * @author benzhan
     */
    private function _checkInvalidGroupFunction($keys, $virtualValue) {
        $virtualValue = strtoupper($virtualValue);
        $key = join('|', $keys);
       
        return preg_match("/^({$key})(\s)*\([\w`]+\)$/", $virtualValue);
    }
    
    /**
     * 格式化列的计算规则
     * @param array $fields
     * @param array $keyWord
     */
    private function _getCalField($fields, $keyWord) {
        $keys = array ('MAX', 'MIN', 'SUM', 'COUNT', 'AVG');
        
        foreach ( $keys as $key ) {
            $k = '_' . strtolower($key);
            if (!$keyWord[$k]) { continue; }
            
            $values = explode(',', $keyWord[$k]);
            foreach ( $values as $value ) {
                if ($fields[$value]['fieldVirtualValue'] && !$this->_checkInvalidGroupFunction($keys, $fields[$value]['fieldVirtualValue'])) {
                    $fields[$value]['fieldVirtualValue'] = "{$key}({$fields[$value]['fieldVirtualValue']})";
                } else {
                    $fields[$value]['fieldVirtualValue'] = "{$key}(`{$value}`)";
                }
            }
        }
        
        //如果存在计算规则，则检查是否传入了groupby
        if ($values && !$keyWord['_groupby']) {
            Tool::err('没有设置groupby字段！');
        }
        
        return $fields;
    }
    

    /**
     * 获取表名和where
     * @param array $tableInfo
     * @param array $where
     * @return array('tableName', 'where')
     * @author benzhan
     */
    private function _getTableNameInfo($tableInfo, $where) {
        $sourceCallBack = trim($tableInfo['sourceCallBack']);
        if ($sourceCallBack) {
            $func = create_function('&$_conditions', $sourceCallBack);
            //格式化后的where
            $_conditions = $this->getFormatWhere($where);
            
            $tableName = $func($_conditions);
            
            //$_condition可以被修改，所以要重置where
            $where = array();
            foreach ($_conditions as $key => $value) {
                foreach ($value as $opt => $v) {
                    $tempValue = array($key, $opt);
                    $tempValue = array_merge($tempValue, (array) $v);
                    $where[] = $tempValue;
                }
            }
        } else {
            $tableName = $tableInfo['sourceTable'];
        }
        
        return compact('tableName', 'where');
    }
    
    private function _getMap($datas, $fields) {
        $map = array ();
        $fieldNames = array_keys(current($datas)) ;
        foreach ( $fieldNames as $fieldName ) {
            $field = $fields[$fieldName];
            $fieldMap = json_decode($field['fieldMap'], true);
            if (!$fieldMap) { continue; }        
            
            foreach ( $datas as $rowNum => $row ) {
                $val = $row[$fieldName];
                //字典表
                $map[$fieldName][$val] = $val;
            }
        
            $funcName = $fieldMap['name'];
            //$key = $field['fieldVirtualValue'] ? $field['fieldVirtualValue'] : $fieldName;
            $args = $fieldMap+ array('where' => $map[$fieldName]);
            $map[$fieldName] = Diy_Plugin_Map::$funcName($args);
        }
        
        return $map;
    }
    
    /**
     * 过滤出需要显示的列
     * @author benzhan
     */
    private function _filterData($datas, $fields, $selectFields) {
        $tData = array();
        foreach ($datas as $data) {
            $tempRow = array();
            foreach ($data as $fieldName => $value) {
                if (!$fields[$fieldName]['fieldDisplay']) { continue; }
                if ($selectFields && !in_array($fieldName, $selectFields)) { continue; }
                $tempRow[$fieldName] = $value;
            }
            $tData[] = $tempRow;
        }
        
        return $tData;
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
        
    /**
     * 格式化数据，翻译字段、执行回调
     * @param array $datas 一维数据
     * @param array $fields 列信息
     * @author benzhan
     */
   public function formatRow($row, $fields) {
       $datas = array($row);
       $datas = $this->formatDatas($datas, $fields);
       return current($datas);
   } 
    
    /**
     * 检查参数
     * @param array $args 
     * @author benzhan
     */
    private function _checkTableDataParam($args) {
        global $_configs;
        $opts = array_keys($_configs['opts']);
        $rules = array (
            'tableId' => 'string', 
            'fields' => array ('array', 
                'nullable' => true, 
                'emptyable' => true 
            ), 
            'where' => array ('array', 
                'nullable' => true,
                'elem' => 'array' 
            ), 
            'keyWord' => array ('array', 
                'emptyable' => true 
            ) 
        );
        
        Param::checkParam($rules, $args);
    }
    
    /**
     * 获取where语句
     * @param array $where 条件数据
     * @author benzhan
     */
    function getWhereStr($where) {
        $_where = '1 ';
        if ($where) {
            foreach ( $where as $value ) {
                $field = $value[0];
                if (strpos($field, '|||') === false) {
                    $item = $this->_getWhereItemStr($field, $value);
                    $item && $_where .= "AND {$item} "; 
                } else {
                    //支持|||分割的参数
                    $fields = explode('|||', $field);
                    $_where .= "AND (0 ";
                    foreach ($fields as $field) {
                        $field = trim($field);
                        $item = $this->_getWhereItemStr($field, $value);
                        $item && $_where .= "OR {$item} ";
                    }
                    $_where .= ") ";
                }
            }
        }
        
        return $_where;
    }
    
    private function _getWhereItemStr($field, $value) {
        $opt = $value[1];
        $val = $value[2];
        if (!isset($val) || $val === '' || !$field) {
            return '';
        }
        
        $item = $field . ' ';
        switch ($opt) {
            case 'like' :
                $item .= "LIKE '%{$val}%' ";
                break;
            case 'like .%' :
                $item .= "LIKE '{$val}%' ";
                break;
            case 'like %.' :
                $item .= "LIKE '%{$val}' ";
                break;
            case ':' :
                if ($val && $value[3]) {
                    $item .= "BETWEEN '{$val}' AND '{$value[3]}' ";
                } else if ($val ) {
                    $item .= ">= '{$val}' ";
                } else if ($value[3]) {
                    $item .= "<= '{$value[3]}' ";
                }
        
                break;
            default :
                $opt = strtoupper($opt);
                if (is_array($val)) {
                    $item .= "{$opt} ('" . join("', '", $val) . "') ";
                } else if ($opt == 'IN' || $opt == 'NOT IN') {
                    $item .= "{$opt} ('" . $val . "') ";
                } else {
                    $item .= "{$opt} '{$val}' ";
                }
            break;
        }
        
        return $item;
    }
    
    /**
     * 格式化where，返回以fieldName和opt为key, val为value
     * @param array $where
     * @author benzhan
     */
    function getFormatWhere($where) {
        $formatWhere = array();
        foreach ($where as $value) {
            if ($value[1] != ':') {
                $v = $value[2];
            } else {
                $v = array($value[2], $value[3]);
            }
            
            $formatWhere[$value[0]][$value[1]] = $v;
        }
        return $formatWhere;
    }
    
    /**
     * 获取db对象
     * @param array $tableInfo 表的信息
     * @author benzhan
     */
    public function getDb($tableInfo) {
        global $db;
        $tempDbKey = "tempTable{$tableInfo['tableId']}";
        if ($db[$tempDbKey]) { return  $db[$tempDbKey]; }
        
        $tableInfo['sourceType'] || $tableInfo['sourceType'] = "mysqli";
        $tableInfo['sourceDb'] || $tableInfo['sourceDb'] = 'information_schema';
        $dbInfo = array (
            'enable' => true, 
            'dbHost' => $tableInfo['sourceHost'], 
            'dbPort' => $tableInfo['sourcePort'], 
            'dbName' => $tableInfo['sourceDb'], 
            'dbUser' => $tableInfo['sourceUser'], 
            'dbPass' => $tableInfo['sourcePass'],
        	'dbType' => $tableInfo['sourceType']
        );
        
        $args = array ( $tempDbKey => $dbInfo );
        $db = DB::init($args, $tempDbKey);
        return $db[$tempDbKey];
    }
    
    /**
     * 获取db对象
     * @param string $tableId 表的id
     * @author benzhan
     */
    public function getDb2($tableId) {
        $oConfig = new Diy_Table();
        $tableInfo = $oConfig->getTableInfo($tableId);
        return $this->getDb($tableInfo);
    }
}



