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
        // 计算父节点
        $objRMenu = new RMenuNode();
        $parentIds = $objRMenu->getAllParentIds($nodeId);
        
        // 也需要把当前节点也参与查询
        $parentIds[] = $nodeId;
        
        // 计算子节点
        $childIds = $objRMenu->getAllChildIds($nodeId);
        $parentIds = array_merge($parentIds, $childIds);
        
        $where = array('nodeId' => $parentIds);
        $keyWord = array('_field' => 'DISTINCT userId');
        $userIds = $this->objHelper->getCol($where, $keyWord);
    
        return $userIds;
    }
    
    /**
     * 检查nodeId是否有权限
     * @author benzhan
     * @param int $nodeId
     * @param string $userName
     * @return boolean
     */
    function checkRight($nodeId, $userName = null) {
        $allUserIds = $this->getAllUserIds($nodeId);
        $userName  || $userName = $_SESSION['username'];
        return in_array($userName, $allUserIds);
    }

}
