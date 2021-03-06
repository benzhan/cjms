<?php

class VMenuNode extends Model {
    protected $tableName = 'vMenuNode';

    private function getChildData($pId) {
        $datas = $this->getChildByPid($pId);
        
        foreach ($datas as $key => $data) {
            $datas[$key]['childNum'] = $this->objHelper->getCount(array('parentNodeId' => $data['nodeId']));
        }

        return $datas;
    }
    
    function getChildByPid($pId) {
        return $this->objHelper->getAll(array('parentNodeId' => $pId), array('_sort' => 'sortPos ASC'));
    }
    
    function getDirectSubNode($pId) {
        $pId = (int) $pId;
        $menuDatas = $this->getChildData($pId);
        if ($menuDatas) {
            $objUserNode = new UserNode();
            foreach ($menuDatas as $key => $data) {
                $allUserIds = $objUserNode->getAllUserIds($data['nodeId']);
                $data['allUserIds'] = join(';', $allUserIds);
                if (in_array($_SESSION['username'], $allUserIds)) {
                    // 如果有权限
                    $userIds = $objUserNode->getUserIds($data['nodeId']);
                    $data['userIds'] = join(';', $userIds);
                    
                    $node = array('text' => $data['nodeName'], 'value' => $data['nodeId'], 'data' => $data);
                    $data['childNum'] > 0 && $node['items'] = array();
                    
                    $menuDatas[$key] = $node;
                } else {
                    // 没有权限
                    unset($menuDatas[$key]);
                }
            }
        }
        
        return $menuDatas;
    }

    function getNodeById($nodeId) {
        $data = $this->objHelper->getRow(compact('nodeId'));
        $node = array('text' => $data['nodeName'], 'value' => $data['nodeId'], 'data' => $data);
        
        return $node;
    }
    
    /**
     * 获取两级数据
     * @author benzhan
     * @return Ambigous <multitype:unknown , unknown>
     */
    function getLevel2Data() {
        $nodes = $this->getChildByPid(0);
        
        $datas = array();
        $objUserNode = new UserNode();
        foreach ($nodes as $node) {
            $nodeId = $node['nodeId'];
            if ($objUserNode->checkRight($nodeId)) {
                $datas[$node['nodeId']] = $node;
            }
        }
        
        $ids = array_keys($datas);
        $nodes = $this->getChildByPid($ids);
        foreach ($nodes as $node) {
            if ($objUserNode->checkRight($node['nodeId'])) {
                $datas[$node['parentNodeId']]['items'][$node['nodeId']] = $node;
            }
        }
        
        return $datas;
    }
    
}
