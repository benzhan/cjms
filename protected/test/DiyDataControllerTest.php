<?php

require_once 'BaseTest.php';

class DiyDataControllerTest extends BaseTest {
    protected $obj;
    
    public function __construct() {
        $this->obj = new DiyDataController();
    }
    
    public function actionTable() {
        $param = 'tableId=ee524ea0-10e2-edf9-9ad5-7222c4b567cc';
        parse_str($param, $args);
        
        $this->obj->actionTable($args);
    }

}

$obj = new DiyDataControllerTest();
$obj->actionTable();
