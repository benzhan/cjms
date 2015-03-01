<?php

/**
 * 用户
 * @author benzhan
 */
class UserController extends BaseController {
    
    protected $noLoginActions = array('login');

    /**
     * 首页
     * @author benzhan
     */
    function actionLogin() {
        $this->tpl->display('login');
    }
    
    function actionLogout() {
        unset($_SESSION['username']);
        setcookie("username");
        setcookie("password");
        setcookie("oauthCookie");
        setcookie("osinfo");
        
        header("location: " . SITE_URL);
    }
}
