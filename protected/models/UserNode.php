<?php

class UserNode extends Model {
    protected $tableName = 'rUserNode';
    
    function getUserIds($nodeId) {
        $where = array('nodeId' => $nodeId);
        $keyWord = array('_field' => 'userId');
        $userIds = $this->objHelper->getCol($where, $keyWord);
        
        return $userIds;
    }
    
    function getAllUserIds($nodeId) {
        $objRMenu = new RMenuNode();
        $parentIds = $objRMenu->getAllParentIds($nodeId);
        // 也需要把当前节点也参与查询
        $parentIds[] = $nodeId;
        
        $where = array('nodeId' => $parentIds);
        $keyWord = array('_field' => 'userId');
        $userIds = $this->objHelper->getCol($where, $keyWord);
    
        return $userIds;
    }

}
