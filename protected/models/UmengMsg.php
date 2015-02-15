<?php

class UmengMsg extends Model {
    
    public function __construct($resultDate = null) {
        $this->tableName = 'lhc_umeng_msg';
        parent::__construct();
    }
    
}