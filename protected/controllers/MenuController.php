<?php

class MenuController extends Controller {
    
    /**
     * 首页
     * @author benzhan
     */
    function actionIndex() {
        $objMenu = new VMenuNode();
        $items = $objMenu->getDirectSubNode(0);

        $node = array('text' => '根目录', 'value' => 0, 'data' => array());
        $node['items'] = $items;
        $node = array('items' => $node);
        $this->tpl->assign('tree', array('items' => $node));
        $this->tpl->display('menu');
    }
    
    /**
     * 获取子节点
     * @author benzhan
     * @param unknown $args
     */
    function actionGetChildsByPId($args) {
        $rules = array(
            'nodeId' => 'int'
        );
        Param::checkParam($rules, $args);
        
        $objMenu = new VMenuNode();
        $nodes = $objMenu->getDirectSubNode($args['nodeId']);
        $nodes = array('items' => $nodes);
        Response::success($nodes);
    }
    
    /**
     * 添加节点
     * @author benzhan
     * @param unknown $args
     */
    function actionAddNode($args) {
        $rules = array(
            'nodeName' => 'string',
            'leftUrl' => array('string', 'emptyable' => true),
            'rightUrl' => array('string', 'emptyable' => true),
            'parentNodeId' => 'int'
        );
        Param::checkParam($rules, $args);
        
        $objCMenu = new CMenuNode();
        $nodeId = $objCMenu->addNode($args);
        
        Response::success($nodeId, '添加成功！');
    }
    
    /**
     * 保存子节点
     * @author benzhan
     * @param unknown $args
     */
    function actionSaveNode($args) {
        $rules = array(
            'nodeId' => 'int',
            'nodeName' => 'string',
            'leftUrl' => array('string', 'emptyable' => true),
            'rightUrl' => array('string', 'emptyable' => true),
        );
        Param::checkParam($rules, $args);
 
        $objCMenu = new CMenuNode();
        $nodeId = $objCMenu->saveNode($args);
        
        Response::success($nodeId, '保存成功！');
    }

    /**
     * 删除节点
     * @author benzhan
     * @param unknown $args
     */
    function actionDeleteNode($args) {
        $rules = array(
            'nodeId' => 'int',
        );
        Param::checkParam($rules, $args);
        
        // 检查是不是有多层子节点
        $objRMenu = new RMenuNode();
        $where = array('parentNodeId' => $args['nodeId']);
        $subNodeIds = $objRMenu->objHelper->getCol($where, array('_field' => 'nodeId'));
        
        if ($subNodeIds) {
            $where = array('parentNodeId' => $subNodeIds);
            $subNodeIds = $objRMenu->objHelper->getCol($where, array('_field' => 'nodeId'));
            if ($subNodeIds) {
                Response::error(CODE_PARAM_ERROR, '删除失败，请先删除孙子节点');
            }
        }
        
        $where = array('parentNodeId' => $args['nodeId']);
        $objRMenu->objHelper->delObject($where);
        
        $where = array('nodeId' => $args['nodeId']);
        $objRMenu->objHelper->delObject($where);
        
        $objCMenu = new CMenuNode();
        $subNodeIds[] = $args['nodeId'];
        $where = array('nodeId' => $subNodeIds);
        $objCMenu->objHelper->delObject($where);
        
        Response::success(array(), '删除成功！');
    }
    
    function actionFixPos($args) {
        $rules = array(
            'json' => 'string',
        );
        Param::checkParam($rules, $args);
        
        $json = $_REQUEST['json'];
        $json = str_replace("\\", '', $json);
        $objJson = json_decode($json, true);

        if (!$objJson) {
            exit(json_encode(array('ret' => true, 'msg' => '保存成功！')));
        }
        
        //要更新的数据
        $updateData = array();
        //新的关系
        $newMap = array();
        //旧的关系
        $oldMap = array();
        
        try {
            //格式化新的关系
            foreach ($objJson as $obj) {
                $newMap[$obj['parentNodeId']][$obj['nodeId']] = $obj['sortPos'];
            }
            
            //获取旧的关系
            $pIds = array_keys($newMap);
            $menuDatas = $this->_oMenuNode->getChildByPid($pIds);
            
            foreach ($menuDatas as $data) {
                $oldMap[$data['parentNodeId']][$data['nodeId']] = $data['sortPos'];
            }
            
            $updateData = $addData = $deleteData = array();
            //对比新旧的数据，得到新增数据和变更数据
            foreach ($newMap as $pId => $data) {
                foreach ($data as $id => $sortPos) {
                    if ($oldMap[$pId][$id] == $sortPos) { continue; }
                    
                    $args = array('parentNodeId' => $pId, 'nodeId' => $id, 'sortPos' => $sortPos);
                    if (isset($oldMap[$pId][$id])) {
                        $where = array('parentNodeId' => $pId, 'nodeId' => $id, 'sortPos' => $oldMap[$pId][$id]);
                        $updateData[] = compact('args', 'where');
                    } else {
                        $addData[] = $args;
                    }
                }
            }
            
            //对比旧新数据，得到删除数据
            foreach ($oldMap as $pId => $data) {
                foreach ($data as $id => $sortPos) {
                    if ($newMap[$pId][$id] == $sortPos) { continue; }
                    if (!isset($newMap[$pId][$id])) {
                        $where = array('parentNodeId' => $pId, 'nodeId' => $id, 'sortPos' => $sortPos);
                        $deleteData[] = $where;
                    } 
                }
            }
            
            
            //插入不同的数据
            foreach ($updateData as $data) {
                $this->_oMenuNode->saveNodeRelation($data['args'], $data['where']);
            }
            $addData && $this->_oMenuNode->addNodeRelation($addData);
            $deleteData && $this->_oMenuNode->deleteNodeRelation($where);
        } catch (Exception $ex) {
            exit(json_encode(array('ret' => false, 'msg' => '保存失败！' . $ex->getMessage())));
        }
        
        return array('ret' => true, 'msg' => '保存成功！');
    }
}
