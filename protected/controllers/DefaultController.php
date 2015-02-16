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
        $objVMenu = new VMenuNode();
        $datas = $objVMenu->getLevel2Data();
        $this->tpl->assign('menus', $datas);
        $this->tpl->display('index');
    }
}
