<?php

require_once 'BaseTest.php';

class DiyDataControllerTest extends BaseTest {
    protected $obj;
    
    public function __construct() {
        $this->obj = new DiyDataController();
    }
    
    public function actionTable() {
        $param = 'tableId=99a3b406-b929-4609-6049-7af92aea273f&where=&keyWord=%7B"_page"%3Anull%2C"_pageSize"%3Anull%2C"_sortKey"%3Anull%2C"_sortDir"%3Anull%7D';
        parse_str($param, $args);
        
        $this->obj->actionTable($args);
    }

}

$obj = new DiyDataControllerTest();
$obj->actionTable();
