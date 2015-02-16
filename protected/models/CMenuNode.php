<?php

class CMenuNode extends Model {
    protected $tableName = 'cMenuNode';
    
    function addNode($args) {
        $this->objHelper->addObject(arrayFilter($args, 'nodeName', 'leftUrl', 'rightUrl'));
        $nodeId = $this->objHelper->getInsertId();
        
        $parentNodeId = (int) $args ["parentNodeId"];
        
        $oMenuRelation = new TableHelper('rMenuNode');
        $maxSortPos = $oMenuRelation->getOne(compact('parentNodeId'), array('_field' => 'MAX(sortPos)'));
        $sortPos = $maxSortPos + 1;
        $oMenuRelation->addObject(compact('nodeId', 'sortPos', 'parentNodeId'));
        
        return $nodeId;
    }
    
    function saveNode($args) {
        $nodeId = (int) $args ["nodeId"];
        if ($nodeId) {
            $this->objHelper->updateObject(arrayFilter($args, 'nodeName', 'leftUrl', 'rightUrl'), compact('nodeId'));
        }
        
        return $nodeId;
    }
    
    
}
