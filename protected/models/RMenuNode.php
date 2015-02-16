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

}
