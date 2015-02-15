<?php
class Model {
    protected $tableName;
    protected $dbKey = 'default';
    public $helper;
    public $db;
    public $dbs;

    public function __construct() {
        $this->getHelper();
    }

    private function getHelper() {
        $this->getDb();
        if (!$this->helper) {
            $this->helper = new TableHelper($this->tableName, $this->dbKey);
        }
        
        return $this->helper;
    }

    private function getDb() {
        if (!$this->dbs) {
            $this->dbs = DB::init($GLOBALS['dbInfo']);
        }
        
        // 以下写法完全是为了zend studio的提示功能
        if (false) {
            $this->db = $this->dbs;
        }
        $this->db = $this->db[$this->dbKey];
        return $this->db;
    }

}