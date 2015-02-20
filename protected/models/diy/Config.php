<?php

class Diy_Config {
    private $_dbKey = "Report";

	public function getHosts() {
		$oBaseTable = new TableHelper('Cmdb3Table', $this->_dbKey);
		return $oBaseTable->getCol(array('_field' => 'sourceHost', '_groupby' => 'sourceHost', '_sortKey' => 'sourceHost'));
	}

	public function getDbs($args) {
	    $db = $this->_getDb($args);
	    
	    $sql = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA";
	    return $db->getCol($sql);
	}
	
	public function getTables($args) {
	    $db = $this->_getDb($args);
	    $args = $db->escape($args);
	    
	    $sql = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$args['sourceDb']}'";
	    return $db->getCol($sql);
	}
	
	public function getFields($args) {
	    $db = $this->_getDb($args);
	    $args = $db->escape($args);
	    try {
	        $sql = "SHOW FULL FIELDS FROM {$args['sourceTable']}";
	        return $db->getAll($sql);
	    } catch (Exception $ex) {
	        return array();
	    }
	}
	
	private function _getDb($args) {
	    $oData = new Diy_Data();
	    return $oData->getDb($args);
	}
}
