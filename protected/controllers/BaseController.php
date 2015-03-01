<?php

/**
 * 基础Controller
 * @author benzhan
 */
class BaseController extends Controller {
    
    protected $noLoginActions = array(); // 不需要登录action

    public function __construct() {
        parent::__construct();
        
        $noLoginActions = array_map('strtolower', $this->noLoginActions);
        $useLoginInfoActions = array_map('strtolower', $this->useLoginInfoActions);
        
        // 如果是需要登录态的action
        if (!in_array(ACTION_NAME, $noLoginActions)) { 
            Login::checkLogin();
        }
    }
    
    
}
