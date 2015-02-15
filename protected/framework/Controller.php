<?php

class Controller{
    public $template;
    
    private function __construct() {
        $this->template = Template::init();
    }
    
}