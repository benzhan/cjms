<?php

/**
 * 首页
 * @author benzhan
 *
 */
class DefaultController extends Controller {

    /**
     * 首页
     * @author benzhan
     * @param array $args
     */
    function actionIndex(array $args) {
        //$this->tpl-
        $this->tpl->display('index');
    }
}
