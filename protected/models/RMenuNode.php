<?php

class RMenuNode extends Model {
    protected $tableName = 'rMenuNode';
    
    function saveNodeRelation($args, $where) {
        $where = arrayFilter($where, 'nodeId', 'parentNodeId');
        if (!$where) { return false; }
        
        $this->objHelper->updateObject($args, $where);
        return true;
    }
    
    function deleteNodeRelation($where) {
        $where = arrayFilter($where, 'nodeId', 'parentNodeId');
        if (!$where) { return false; }
        
        return $this->objHelper->delObject($where);
    }
    
    function getAllParentIds($nodeId) {
        $parentIds = array();
        $tData = array($nodeId);
        
        while ($tData) {
            $where = array('nodeId' => $tData);
            $keyWord = array('_field' => 'parentNodeId');
            $tData = $this->objHelper->getCol($where, $keyWord);
            $parentIds = array_merge($parentIds, $tData);
        }
        
        return $parentIds;
    }
    
    function getAllChildIds($nodeId) {
        $nodeIds = array();
        $tData = array($nodeId);
    
        while ($tData) {
            $where = array('parentNodeId' => $tData);
            $keyWord = array('_field' => 'nodeId');
            $tData = $this->objHelper->getCol($where, $keyWord);
            $nodeIds = array_merge($nodeIds, $tData);
        }
    
        return $nodeIds;
    }

}
