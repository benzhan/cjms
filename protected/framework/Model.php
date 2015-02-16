<?php
class Model {
    protected $tableName;
    protected $dbKey = 'default';
    /**
     * 数据库的表助手
     * @var TableHelper
     */
    public $objHelper;
    
    /**
     * 数据库的操作类
     * @var DB
     */
    public $objDb;
    
    /**
     * 数据库的操作类集合 array(string => DB)
     * @var Array 
     */
    public $dbs;

    public function __construct() {
        $this->getHelper();
    }

    private function getHelper() {
        $this->getDb();
        if (!$this->objHelper) {
            $this->objHelper = new TableHelper($this->tableName, $this->dbKey);
        }
        
        return $this->objHelper;
    }

    private function getDb() {
        if (!$this->dbs) {
            $this->dbs = DB::init($GLOBALS['dbInfo']);
        }
        
        // 以下写法完全是为了zend studio的提示功能
        if (false) {
            $this->objDb = $this->dbs;
        }
        $this->objDb = $this->dbs[$this->dbKey];
        return $this->objDb;
    }

}