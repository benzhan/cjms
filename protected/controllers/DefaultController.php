<?php

/**
 * 首页
 * @author benzhan
 */
class DefaultController extends Controller {

    /**
     * 首页
     * @author benzhan
     */
    function actionIndex() {
        $objVMenu = new VMenuNode();
        $datas = $objVMenu->getLevel2Data();
        $this->tpl->assign('menus', $datas);
        $this->tpl->display('index');
    }
    
    /**
     * 获取默认的tree结构
     * @author benzhan
     * @param array $args
     */
    function actionMenuTree(array $args) {
        $rules = array(
            'nodeId' => 'int'
        );
        Param::checkParam($rules, $args);
        $nodeId = $args['nodeId'];
    
        $objMenu = new VMenuNode();
        $node = $objMenu->getNodeById($nodeId);
    
        $items = $objMenu->getDirectSubNode($nodeId);
        if ($items) {
            $node['items'] = $items;
        }
    
        $node = array('items' => $node);
        $this->tpl->assign('tree', array('items' => $node));
        $this->tpl->display('menu_tree');
    }
    
    /**
     * 获取默认的tree结构
     * @author benzhan
     * @param array $args
     */
    function actionGetSiteMap(array $args) {
        $rules = array(
            'nodeId' => 'int'
        );
        Param::checkParam($rules, $args);
        
        $nodeId = $args['nodeId'];
        $objMenu = new VMenuNode();
        $siteMap = array();
        do {
            $node = $objMenu->objHelper->getOneObject(compact('nodeId'));
            array_unshift($siteMap, $node);
            $nodeId = $node['parentNodeId'];
        } while($nodeId);
        
        Response::success($siteMap);
    }
    
    
}
