<?php

class ResultHistory extends Model {
    private $resultDate;
    private $resultYear;
    
    public function __construct($resultDate = null) {
        $resultDate || $resultDate = date('Y-m-d');
        
        $this->tableName = 'lhc_result_history';
        $this->resultDate = $resultDate;
        $this->resultYear = substr($resultDate, 0, 4);
        parent::__construct();
    }
    
    public function getNewestYear() {
        $sql = 'SELECT MAX(result_year) AS result_year FROM ' . $this->tableName;
        $resultYear = $this->db->getOne($sql);
        if (!$resultYear) {
            return date('Y');
        }
        
        return $resultYear;
    }
    
    public function getNextResultDate() {
        $resultYear = $this->getNewestYear();
        
        $sql = 'SELECT MAX(next_result_date) AS next_result_date FROM ' . $this->tableName;
        $sql .= " WHERE result_year = '{$resultYear}' ";
        $nextResultDate = $this->db->getOne($sql);
        return $nextResultDate;
    }
    
    public function getNextResultNum() {
        $resultYear = $this->getNewestYear();
        
        $sql = 'SELECT MAX(result_num) AS newestNum FROM ' . $this->tableName;
        $sql .= " WHERE result_year = '{$resultYear}' ";
        $newestNum = $this->db->getOne($sql);
        return $newestNum + 1;
    }
    
    public function getNewestResult() {
        $tableName = $this->tableName;
        $sql = "SELECT * FROM {$tableName} ORDER BY result_year, result_num DESC";
        return $this->db->getRow($sql);
    }
    
    public static $animals = array(
        '鼠', '牛', '虎', '兔', '龙', '蛇',
        '马', '羊', '猴', '鸡', '狗', '猪'
    );
    
    public static function getAnimal($year){
        $key = ($year - 1900) % 12;
        return self::$animals[$key];
    }
    
}