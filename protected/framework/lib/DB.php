<?php

/**
 * DB 抽象类
 * 其中主要是创建了一个静态变量$db，所有集成类的对象实例化到$db中方便调用
 * 该抽象类初始化时候根据配置文件存入$db变量，并调用子类进行DB实例化，使用DB::init()进行调用
 * 本类只实现了一个静态方法，并规定了其子类必须实现的一些方法。
 *
 */
abstract class DB {
    const DB_FETCH_ASSOC    = 1;
    const DB_FETCH_ARRAY    = 3;
    const DB_FETCH_ROW      = 2;
    const DB_FETCH_DEFAULT  = self::DB_FETCH_ASSOC;
    
    const DB_TYPE_MYSQLI = 'mysqli';
    const DB_TYPE_ORACLE = 'oracle';

    public static $db = array();

    protected static $dbType = array('mysqli' => 'MySQLi', 'oracle' => 'Oracle');

    public $dsn;
    protected $uConn;
    protected $qConn;
    protected $dbKey;
    protected $sql;
    protected $sqls;
    protected $qrs;
    protected $urs;
    protected $uSqls;
    protected $qSqls;
    protected $queryNum;
    protected $updateNum;
    protected $debug = false;

    protected function __construct() {
    }

    /**
     * DB初始化
     *
     * @param array $dsn 配置文件中的DB信息
     * @return array|DB DB对象
     */
    public static function init($dsn) {
        foreach ($dsn as $dbKey => $dbInfo) {
            if ($dbInfo['enable'] !== false) {
                if (empty(self::$db[$dbKey])) {
                    $className = 'DB_' . self::$dbType[strtolower($dbInfo['dbType'])];
                    self::$db[$dbKey] = new $className($dbInfo, $dbKey);
                } else {
                    // Tool::debug("dbKey:{$dbKey} is not empty.");
                }
            }
        }
        
        return self::$db;
    }

    public abstract function connect($type = "slave");
    public abstract function close();
    public abstract function query($sql, $limit = null, $quick = false);
    public abstract function update($sql);
    public abstract function getOne($sql);
    public abstract function getCol($sql, $limit = null);
    public abstract function getRow($sql, $fetchMode = self::DB_FETCH_DEFAULT);
    public abstract function getAll($sql, $limit = null, $fetchMode = self::DB_FETCH_DEFAULT);

    public function getSQL() {
        return $this->sqls;
    }
    
    public function getDebugTime() {
        return $this->time;
    }
    
    public function debug($flag = true) {
        $this->debug = $flag;
    }
}


//end of script
