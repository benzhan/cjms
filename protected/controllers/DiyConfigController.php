<?php

/**
 * Diy报表配置
 * @author benzhan
 */
class DiyConfigController extends BaseController {
    private $_dbKey = "Report";
    
    function actionEdit($args) {
        require_once ROOT_PATH . 'diyConfig.inc.php';
        
        $rules = array(
            'tableId' => array('string', 'nullable' => true)
        );
        Param::checkParam($rules, $args);
        
        $tableId = $args['tableId'];
        
        if ($tableId) {
            $oConfigTable = new Diy_Table();
            $tableInfo = $oConfigTable->getTableInfo($tableId);
            $isAdmin = $oConfigTable->isAdmin2($tableInfo);
            if (!$isAdmin) {
                Response::exitMsg("<meta charset='utf-8'><p>对不起，您没有权限. </p>");
            }
            $link = SITE_URL . 'DiyData/report?tableId=' . $tableId;
        } else {
            $link = "新建的页面,保存后自动生成...";
        }
        
        $oConfig= new Diy_Config();
        $sourceHosts = $oConfig->getHosts();
        $map = $GLOBALS['diy']['map'];
        $pageSizes = $GLOBALS['diy']['pageSizes'];

        $args = compact('tableInfo', 'sourceHosts', 'link', 'map', 'pageSizes', 'isAdmin');
        $this->tpl->assign($args);
        $this->tpl->display('diy_config');
    }
    
    /**
     * 读取数据库系信息
     * @author benzhan
     * @param unknown $args
     */
    function actionGetDbs($args) {
        $rules = array(
            'sourceHost' => 'ip',
            'sourcePort' => 'int',
            'sourceUser' => array('string', 'emptyable' => true),
            'sourcePass' => array('string', 'emptyable' => true),
        );
        Param::checkParam($rules, $args);
        
        $oConfig= new Diy_Config();
        return $oConfig->getDbs($args);
    }
    
    /**
     * 获取表格信息
     * @author benzhan
     * @param unknown $args
     */
    function actionGetTables($args) {
        $rules = array(
            'sourceHost' => 'ip',
            'sourcePort' => 'int',
            'sourceDb' => 'string',
            'sourceUser' => array('string', 'emptyable' => true),
            'sourcePass' => array('string', 'emptyable' => true),
        );
        Param::checkParam($rules, $args);
    
        $oConfig= new Diy_Config();
        return $oConfig->getTables($args);
    }

    public function actionGetFieldTable($args) {
        require_once ROOT_PATH . 'diyConfig.inc.php';
        
        $rules = array(
            'tableId' => array('string', 'nullable' => true),
            'loadType' => array('int', enum => array(1, 2, 3)),
        );
        Param::checkParam($rules, $args);
        
        $tableId = $args['tableId'];
        if ($tableId && $args['loadType'] & 1) {
            $oConfigTable = new Diy_Table();
            $fields = $oldFields = $oConfigTable->getFields2($tableId);
        }
    
        if ($args['loadType'] & 2) {
            $objConfig = new Diy_Config();
            $newFields = $objConfig->getFields($args);
            $fields = $this->_processFields($newFields, array());
        }
    
        if ($args['loadType'] == 3) {
            $fields = $this->_processFields($newFields, $oldFields);
        }
        
        $fields = $this->_processFields2($fields);
        
        $fieldTypes = $GLOBALS['diy']['fieldTypes'];
        $map = $GLOBALS['diy']['map'];
        $data = compact('fields', 'fieldTypes', 'map');
        
        $template = Template::init();
        $template->assign(compact('data'));
        $template->display('diy_config_table');
    }
    
    private function _processFields($newFields, $fields) {
        $newFields = ArrayformatKey($newFields, 'Field');
        
        foreach ($newFields as $field) {
            $fieldName = $field['Field'];
            if (!$fields[$fieldName]) {
                $newField = array();
                
                $fieldType = $field['Type'];
                preg_match('/(\w+)\((.+)\)/', $fieldType, $matches);
                if ($matches) {
                    $fieldType = $matches[1];
                    $length = (int) $matches[2];
                } else {
                    $length = 0;
                }
                
                // 处理类型
                $fieldType = $GLOBALS['diy']['fieldTypeMap'][$fieldType];
                if ($fieldType == 'string' && $length > 200) {
                    $fieldType = 'text';
                } else if ($fieldType == 'enum') {
                    // 处理枚举类型
                    $values = explode(',', $matches[2]);
                    $fieldLength = array();
                    foreach ($values as $value) {
                        $fieldLength[$value] = $value;
                    }
                    $newField['fieldLength'] = json_encode($fieldLength);
                } else if ($fieldType == 'int') {
                    $lowerFieldName = strtolower($fieldName);
                    
                    if (strpos($lowerFieldName, 'time') !== false) {
                        $fieldType = 'datetime';
                        $newField['fieldVirtualValue'] = "FROM_UNIXTIME($fieldName,'%Y-%m-%d %H:%i:%s')";
                    } if (strpos($lowerFieldName, 'date') !== false || strpos($lowerFieldName, 'day') !== false) {
                        $fieldType = 'date';
                        $newField['fieldVirtualValue'] = "FROM_UNIXTIME($fieldName,'%Y-%m-%d')";
                    } 
                }
                
                $newField['fieldType'] = $fieldType;
                $newField['fieldName'] = $newField['fieldSortName'] = $fieldName;
                $newField['fieldCName'] = $field['Comment'] ? $field['Comment'] : $fieldName;
                $newField['fieldPostion'] = count($fields);
    
                // 设置默认的展现方式
                if (preg_match('/\w+Id/', $fieldName) || ($fieldType != 'int' && $fieldType != 'float')) {
                    $newField['fieldDisplay'] = 2;
                } else {
                    $newField['fieldDisplay'] = 1;
                }
  
                $fields[$fieldName] = $newField;
            }
        }
        
        return $fields;
    }

    private function _processFields2($fields) {
        foreach ($fields as $key => $field) {
            // 设置默认显示高级选项
            if ($field['fieldVirtualValue'] || $field['defaultValue'] || $field['fieldMap'] || $field['callBack'] || $field['fieldLength']) {
                $field['showAdv'] = 1;
                
                //var_dump($field);exit;
            }
    
            $fields[$key] = $field;
        }
    
        return $fields;
    }
    
    public function actionSaveTableAndFields($args) {
        $args['fields'] = json_decode($args['fields'], true);
        $rules = array(
            'tableId' => array('string', 'nullable' => true),
            'fields' => 'array',
        );
        Param::checkParam($rules, $args);
        
        $oBaseTable = new TableHelper('Cmdb3Table', $this->_dbKey);
        $tableId = $args['tableId'];
        $fields = arrayPop($args, 'fields');
    
        try {
            $oBaseTable->autoCommit(false);
            $args['lastModifyTime'] = date('Y-m-d H:i:s');
            if ($tableId) {
                $one = $oBaseTable->getOne(compact('tableId'));
                if (!$one) {
                    Response::error(CODE_PARAM_ERROR, null, 'tableId is not valid.');
                }
                
                // 修改时，才需要检查权限
                $this->_checkRight($tableId);
                //修改表信息
                $where = compact('tableId');
                $oBaseTable->updateObject($args, $where);
            } else {
                //添加表信息
                $args['tableId'] = $tableId = uuid();
                $args['createTime'] = date('Y-m-d H:i:s');
                // $args['authorId'] = $user['userId'] ? $user['userId'] : 0;
                $args['authorName'] = $_SESSION['username'] ? $_SESSION['username'] : 'guest';
                $oBaseTable->addObject($args);
                $where = compact('tableId');
            }
    
            $oBaseFields = new TableHelper('Cmdb3Field', $this->_dbKey);
            $oldFieldIds = $oBaseFields->getAll($where, array('_field' =>'fieldId'));
            $oldFieldIds = arrayFormatKey($oldFieldIds, 'fieldId');
    
            foreach ($fields as $field) {
                $field['tableId'] = $tableId;
                $fieldId = $field['fieldId'];
                if ($fieldId) {
                    unset($oldFieldIds[$fieldId]);
                    $oBaseFields->updateObject($field, compact('fieldId'));
                } else {
                    $field['fieldId'] = uuid();
                    $oBaseFields->addObject($field);
                }
            }
    
            if ($oldFieldIds) {
                $oldFieldIds = array_keys($oldFieldIds);
                $oBaseFields->delObject(array('fieldId' => $oldFieldIds));
            }
    
            $oBaseTable->tryCommit();
        } catch (Exception $ex) {
            Response::error(CODE_DB_ERROR, null, $ex->getMessage());
        }
    
        Response::success($tableId, "保存成功,复制链接地址可查看数据");
    }
    
    public function actionCopyTable($args) {
        $tableId = $args['tableId'];
        $rules = array(
            'tableId' => array('string', 'nullable' => true),
        );
        Param::checkParam($rules, $args);
        
        $newTableId = uuid();
    
        try {
            //复制table
            $oBase = new TableHelper('Cmdb3Table', $this->_dbKey);
            $oBase->autoCommit(false);
    
            Tool::log('copy Cmdb3Table.');
            $where = compact('tableId');
            $where = $oBase->escape($where);
            $table = $oBase->getRow($where);
            $table['tableId'] = $newTableId;
            $table['createTime'] = date('Y-m-d H:i:s');
            $table['tableName'] .= '【复制】' . $table['createTime'];
            $table['tableCName'] .= '【复制】' . $table['createTime'];
            $table['lastModifyTime'] = date('Y-m-d H:i:s');
            
            $table['authorId'] = $_SESSION['yyuid'] ? $_SESSION['yyuid'] : 0;
            $table['authorName'] = $_SESSION['username'] ? $_SESSION['username'] : 'guest';
            $table['admins'] = $table['authorName'];
            $table['sourceUser'] = '';
            $table['sourcePass'] = '';
    
            $oBase->addObject($table);
                
                // 复制tableMeta
                /*
             * $oBase = new TableHelper('Cmdb3TableMeta', $this->_dbKey);
             * $tableMetas = $oBase->getAll($where);
             * foreach ($tableMetas as $i => $tableMeta) {
             * $tableMetas[$i]['tableId'] = $newTableId;
             * }
             * $oBase->addObjects2($tableMetas);
             */
                
            // 复制fields
            $oBase = new TableHelper('Cmdb3Field', $this->_dbKey);
            $fields = $oBase->getAll($where);
            foreach ($fields as $i => $field) {
                $fields[$i]['tableId'] = $newTableId;
                $fields[$i]['fieldId'] = uuid();
            }
            $oBase->addObjects2($fields);
    
            $oBase->tryCommit();
            return true;
        } catch (Exception $ex) {
            Response::error(CODE_DB_ERROR, null, $ex->getMessage());
        }
    }

    public function actionDeleteTable($args) {
        $tableId = $args['tableId'];
        $where = compact('tableId');
        $rules = array(
            'tableId' => array('string', 'nullable' => true),
        );
        Param::checkParam($rules, $args);
        
        // 检查权限
        $this->_checkRight($tableId);
        
        try {
            $oBase = new TableHelper('Cmdb3Table', $this->_dbKey);
            $where = $oBase->escape($where);
            $oBase->autoCommit(false);
            
            Tool::log('delete Cmdb3TableMeta.');
            $oBase->delObject($where + array(
                '_tableName' => 'Cmdb3TableMeta'
            ));
            Tool::log('delete Cmdb3Field.');
            $oBase->delObject($where + array(
                '_tableName' => 'Cmdb3Field'
            ));
            Tool::log('delete Cmdb3Table.');
            $oBase->delObject($where + array(
                '_tableName' => 'Cmdb3Table'
            ));
            
            $oBase->tryCommit();
            return true;
        } catch (Exception $ex) {
            Tool::err($ex->getMessage());
            Tool::err($ex->getTrace());
            Response::error(CODE_DB_ERROR, null, $ex->getMessage());
        }
        
        return true;
    }
    
    private function _setDefault($args, $metaKey) {
        $rules = array(
            'tableId' => 'string',
            'metaValue' => 'string'
        );
        Param::checkParam2($rules, $args);
        
        $args['metaKey'] = $metaKey;
        // 检查权限
        $this->_checkRight($args['tableId']);
        
        $objTable = new Diy_Table();
        $flag = $objTable->setTableMeta($args);
        Response::success($flag, '保存成功！');
    }

    public function actionSetDefaultCondition($args) {
        $this->_setDefault($args, 'tableDefaultCondition');
    }
    
    public function actionSetDefaultView($args) {
        $this->_setDefault($args, 'tableDefaultView');
    }
    
    public function actionGetDefaultCondition($args) {
        $rules = array(
            'tableId' => 'string',
        );
        Param::checkParam2($rules, $args);
    
        $args['metaKey'] = 'tableDefaultCondition';
    
        $objTable = new Diy_Table();
        $data = $objTable->getTableMeta($args);
        Response::success($data);
    }
    
    /**
     * 检查用户权限
     * @author benzhan
     * @param unknown $tableId
     */
    private function _checkRight($tableId) {
        $objTable = new Diy_Table();
        if (!$objTable->isAdmin($tableId)) {
            Response::error(CODE_NO_PERMITION, null, '');
        }
    }
    
}

