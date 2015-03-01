<?php

/**
 * 开放的接口
 * @author benzhan
 */
class OpenController extends Controller {
    
    /**
     * 首页
     * @author benzhan
     */
    function actionCheckRight($args) {
        $rule = array(
            'url' => 'string',
            'userId' => 'string' 
        );
        Param::checkParam($rule, $args);
        
        $url = $args['url'];
        $parts = explode('&_nodeId=', $url);
        if (!$parts) {
            $parts = explode('?_nodeId=', $url);
        }
        
        if (!$parts) {
            Response::error(CODE_PARAM_ERROR, null, 'url is not valid');
        }
        
        $nodeId = $parts[1];
        
        $objUserNode = new UserNode();
        if (!$objUserNode->checkRight($nodeId, $args['userId'])) {
            Response::error(CODE_NO_PERMITION, null, "{$args['userId']} is not permition to access nodeId:{$nodeId}");
        }
        
        $objCMenuNode = new CMenuNode();
        $node = $objCMenuNode->objHelper->getRow(compact('nodeId'));
        
        $parts = explode('#', $node['leftUrl']);
        $leftUrl = $parts[0];
        
        $parts = explode('#', $node['rightUrl']);
        $rightUrl = $parts[0];
        
        $leftIndex = strpos($parts[0], $leftUrl);
        $rightIndex = strpos($parts[0], $rightUrl);
        
        if ($leftIndex !== false || $rightIndex !== false) {
            Response::success();
        } else {
            $debugMsg = "left:{$leftUrl} and right:{$rightUrl} is not match {$parts[0]}; leftIndex:{$leftIndex}, rightIndex:{$rightIndex}";
            Response::error(CODE_NO_PERMITION, null, $debugMsg);
        }
           
    }
    
}
