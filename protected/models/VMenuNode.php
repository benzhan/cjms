<?php

class VMenuNode extends Model {
    protected $tableName = 'vMenuNode';

    function getChildData($pId) {
        $datas = $this->objHelper->getObject(array('parentNodeId' => $pId), array('_sort' => 'sortPos ASC'));
        
        foreach ($datas as $key => $data) {
            $datas[$key]['childNum'] = $this->objHelper->getCount(array('parentNodeId' => $data['nodeId']));
        }

        return $datas;
    }
    
    function getChildByPid($pId) {
        $pId = (array) $pId;
        return $this->objHelper->getObject(array('parentNodeId' => $pId), array('_sortExpress' => 'sortPos'));
    }

    function getNodeById($nodeId) {
        return $this->objHelper->getOneObject(compact('nodeId'));
    }
    
    /**
     * 获取两级数据
     * @author benzhan
     * @return Ambigous <multitype:unknown , unknown>
     */
    function getLevel2Data() {
        $nodes = $this->getChildByPid(0);
        
        $datas = array();
        foreach ($nodes as $node) {
            $datas[$node['nodeId']] = $node;
        }
        
        $ids = array_keys($datas);
        $nodes = $this->getChildByPid($ids);
        foreach ($nodes as $node) {
            $datas[$node['parentNodeId']]['items'][$node['nodeId']] = $node;
        }
        
        return $datas;
    }
    
}
