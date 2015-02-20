<?php

class Diy_Table {
    private $_dbKey = "Report";

    /**
     * 获取表配置信息
     * @param int $tableId 表Id
     * @author benzhan
     */
	public function getTableInfo($tableId) {
	    $where = compact('tableId');
	    
		$oBaseTable = new TableHelper('Cmdb3Table', $this->_dbKey);
		return $oBaseTable->getRow($where);
	}
	
	/**
	 * 获取格式化字段长度后的列信息，key => value格式，其中fieldName为key
	 * @param string $tableId 表Id
	 * @param bool $onlyDisplay 只过滤展示列
	 * @author benzhan
	 */
	public function getFields($tableId, $onlyDisplay = false) {
	    //这里一定要加memcache
	    $where = compact('tableId');
	    
	    $tableInfo = $this->getTableInfo($tableId);
	    $fields = $this->getFields2($tableId, $onlyDisplay);
	    
	    foreach ($fields as $fieldName => $field) {
	        $fields[$fieldName]['fieldLength'] = $this->_getFieldLength($field['fieldLength'], $tableInfo);
	    } 
	    return $fields;
	}
	
	/**
	 * 获取为未格式化的列信息
	 * @param string $tableId
	 * @author benzhan
	 */
	public function getFields2($tableId, $onlyDisplay = false) {
	    //这里一定要加memcache
	    $where = compact('tableId');
	    
	    $other = array('_sortKey' => 'fieldPosition', '_sortDir' => 'ASC');
	    $onlyDisplay && $other['_where'] = "fieldDisplay > 0";
	    
	    $oBaseField = new TableHelper('Cmdb3Field', $this->_dbKey);
	    $fields = $oBaseField->getAll($where, $other);
	    
	    return arrayFormatKey($fields, 'fieldName');
	}
	
	/**
	 * 设置默认的列
	 * @param array $args array('tableId', 'fields'...)
	 * @author benzhan
	 */
	public function setDefaultFields($args) {
	    $rules = array(
            'tableId' => 'string',
            'fields' => 'strArr'
	    );
	    Param::checkParam($rules, $args);
	    
	    $where = array();
	    $where['tableId'] = $args['tableId'];
	    
	    $oBaseField = new TableHelper('Cmdb3Field', $this->_dbKey);
	    
	    $oBaseField->autoCommit(false);
	    $oBaseField->updateObject(array('defaultDisplay' => 0), $where);
	    $where['fieldName'] = $args['fields'];
	    $oBaseField->updateObject(array('defaultDisplay' => 1), $where);
	    $oBaseField->tryCommit();
	    
	    return true;
	}
	
	private function _getFieldLength($fieldLength, $tableInfo) {
	    if (!$fieldLength) { return $fieldLength; }
	    if(!$tableInfo['sourceType']) $tableInfo['sourceType'] = "mysqli";
	    
	    try {
	        $selectRange = json_decode($fieldLength, true);
	        if ($selectRange) { return $selectRange; }
	        
	        $meta = array();
	        $meta['id'] = $tableInfo['tableId'];
	        $meta['source'] =  array(
                'enable' => true,
                'dbType' => $tableInfo['sourceType'],
                'dbHost' => $tableInfo['sourceHost'],
                'dbPort' => $tableInfo['sourcePort'],
                'dbName' => $tableInfo['sourceDb'],
                'dbUser' => $tableInfo['sourceUser'],
                'dbPass' => $tableInfo['sourcePass'],
                'dbTable' => $tableInfo['sourceTable'],
                'callBack' => $tableInfo['sourceCallBack']
	        );
	        
	        $objData = new Diy_Data();
	        //当前连接的db对象
	        $_curDb = $objData->getDb($tableInfo);
	        
	        $func = create_function('$meta, $_curDb', $fieldLength);
	        return $func($meta, $_curDb);
	    } catch (Exception $ex) {
	        Tool::err($ex);
	        return array();
	    }
	}

	/**
	 * 设置默认条件或选择列
	 * @param array $args array('tableId', 'metaKey', 'metaValue')
	 * @author benzhan
	 */
	public function setTableMeta($args) {
	    $rules = array(
            'tableId' => 'string',
            'metaKey' => 'string',
            'metaValue' => 'string'
	    );
	    Param::checkParam($rules, $args);
	
	    $oBaseTable = new TableHelper('Cmdb3TableMeta', $this->_dbKey);
	    return $oBaseTable->replaceObject($args);
	}
	
	/**
	 * 获取默认条件或选择列
	 * @param array $args array('tableId', 'metaKey')
	 * @author benzhan
	 */
	public function getTableMeta($where) {
	    $rules = array(
            'tableId' => 'string',
            'metaKey' => 'string',
	    );
	    Param::checkParam($rules, $where);
	    
	    $oBaseTable = new TableHelper('Cmdb3TableMeta', $this->_dbKey);
	    return $oBaseTable->getRow($where);
	}
	
}
