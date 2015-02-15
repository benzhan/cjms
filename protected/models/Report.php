<?php

class Report extends Model {
    private $resultDate;
    private $resultYear;

    public function __construct($resultDate = null) {
        $resultDate || $resultDate = date('Y-m-d');
        
        $this->resultDate = $resultDate;
        $this->resultYear = substr($resultDate, 0, 4);
        $this->tableName = 'lhc_report_' . $this->resultYear;
        parent::__construct();
    }

    public function getMaxResultNum() {
        $sql = 'SELECT MAX(result_num) AS newestNum FROM ' . $this->tableName;
        $newestNum = $this->db->getOne($sql);
        return $newestNum;
    }

    public function getList($resultNum) {
        $tableName = $this->tableName;
        $sql = "SELECT * FROM {$tableName} WHERE 1 ";
        if ($resultNum) {
            $sql .= "AND result_num = '{$resultNum}' ";
        }
        $sql .= "ORDER BY pinyin_head ASC";
        
        return $this->db->getAll($sql);
    }
    
    public function getEmptyData($resultNum) {
        $tableName = $this->tableName;
        $sql = "SELECT * FROM {$tableName} WHERE url_size is NULL OR url_size = 0 ";
        if ($resultNum) {
            $sql .= "AND result_num = '{$resultNum}' ";
        }
    
        return $this->db->getAll($sql, 10);
    }
}