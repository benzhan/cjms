<?php

class MenuController extends Controller {
    
    private function initNodeJsEvent($node) {
        if ($node['leftUrl'] && strpos($node['leftUrl'], 'javascript:') === false) {
            $leftUrl = $node['leftUrl'];
        } else if ($node['leftUrl']) {
        	$leftUrl = "javascript:";
            $leftEvent = substr($node['leftUrl'], strlen('javascript:'));
        }
        
        if ($node['rightUrl'] && strpos($node['rightUrl'], 'javascript:') === false) {
        	$rightUrl = $node['rightUrl'];
        } else if ($node['leftUrl']) {
        	$rightUrl = "javascript:";
            $rightEvent = substr($node['rightUrl'], strlen('javascript:'));
        }

        $nodeId = (int) $node['nodeId'];
        $node['jsEvent'] = "menuClickEvent('{$leftUrl}', '{$rightUrl}', $nodeId);{$leftEvent};{$rightEvent};";
        return $node;
    }
    
    /**
     * 获取菜单的数据
     * @author benzhan
     */
    function actionGetMenuData() {
        $objVMenu = new VMenuNode();
        $nodes = $objVMenu->getChildByPid(0);
        
        $datas = array();
        $ids = array();
        foreach ($nodes as $node) {
            $ids[] = $node['nodeId'];
            $datas[$node['nodeId']] = $this->initNodeJsEvent($node);
        }
        
        $nodes = $objVMenu->getChildByPid($ids);
        foreach ($nodes as $node) {
            $datas[$node['parentNodeId']]['items'][$node['nodeId']] = $this->initNodeJsEvent($node);
        }
        
        return $datas;
    }
    
//     function getAllTree($parentNodeId) {
//         $node['items'] = $this->getDirectSubTree($parentNodeId);
        
//         if ($node['items']) {
//             foreach ($node['items'] as &$item) {
//                 $is = $this->getAllTree($item['value']);
//                 $item['items'] = $is['items'];
//             }
//         } else {
//             unset($node['items']);
//         }
//         return $node;
//     }
    

    function actionGetTree($parentNodeId) {
        $menuDatas = $this->getDirectSubTree($parentNodeId);
        
        $objRMenu = new RMenuNode();
        $data = $objRMenu->getNodeById($parentNodeId);
        $node = array('text' => $data['nodeName'], 'value' => $data['nodeId'], 'data' => $data);
        
        $node['items'] = $menuDatas;
        
        return array('items' => array($node));
    }
    
    private function getDirectSubTree($pId) {
    	$pId = (int) $pId;
    	$oVMenuNode = new VMenuNode();
        $menuDatas = $oVMenuNode->getChildData($pId);
        if ($menuDatas) {
            foreach ($menuDatas as $key => $data) {
                $node = array('text' => $data['nodeName'], 'value' => $data['nodeId'], 'data' => $data);
                $data['childNum'] > 0 && $node['items'] = array();
                $menuDatas[$key] = $node;
            }
        }
        
        return $menuDatas;
    }
    
    function actionGetChildsByPId() {
        $pId = (int) $_REQUEST['pId'];
        $menuDatas = $this->getDirectSubTree($pId);
        return array('ret' => true, 'data' => array('items' => $menuDatas));
    }
           
    function actionAddNode() {
        $nodeId = $this->_oMenuNode->addNode($_REQUEST);
        Response::success(compact('nodeId'), '添加成功！');
    }
    
    function actionSaveNode($args) {
        $rules = array(
            'nodeId' => 'int'
        );
        Param::checkParam($rules, $args);
 
        $objCMenu = new CMenuNode();
        $nodeId = $objCMenu->saveNode($args);
        
        Response::success(compact('nodeId'), '保存成功！');
    }

    function actionDeleteNodeRelation($args) {
        $rules = array(
            'nodeId' => 'int',
            'parentNodeId' => 'int'
        );
        Param::checkParam($rules, $args);
        
        $objRMenu = new RMenuNode();
        $objRMenu->deleteNodeRelation($args);
        Response::success(array(), '保存成功！');
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
