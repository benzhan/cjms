<?php

class Controller{
    public $tpl;
    
    public function __construct() {
        $this->tpl = Template::init();
    }
    
}