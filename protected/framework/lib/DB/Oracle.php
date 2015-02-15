<?php

class DB_Oracle extends DB {
    protected $limit;
    protected $autoCommit = OCI_COMMIT_ON_SUCCESS;

    public function __construct($dbInfo, $dbKey) {
        $this->dbKey = $dbKey;
        $this->dsn = $dbInfo;
    }

    public function connect($type = "slave") {
        global $_configs;
        if ($type == "master" || !isset($this->dsn["slave"])) {
            $dbName = isset($this->dsn["master"]) ? $this->dsn["master"]["dbName"] : $this->dsn["dbName"];
            $dbUser = isset($this->dsn["master"]) ? $this->dsn["master"]["dbUser"] : $this->dsn["dbUser"];
            $dbPass = isset($this->dsn["master"]) ? $this->dsn["master"]["dbPass"] : $this->dsn["dbPass"];
            //$this->uConn = oci_new_connect($dbUser, $dbPass, $dbName);
            $this->uConn = oci_new_connect($dbUser, $dbPass, $dbName, str_replace('-', '', DEFAULT_CHARSET));
            //$this->uConn = oci_new_connect($dbUser, $dbPass, $dbName, 'AL32UTF8');
            if (!$this->uConn) {
                throw new DB_Exception('更新数据库连接失败');
            }
            if (!isset($this->dsn["slave"])) {
                $this->qConn =& $this->uConn;
            }
        } else {
            if (empty($this->dsn["slave"])) {
                $this->connect('master');
                return $this->qConn =& $this->uConn;
            }
            if (empty($_COOKIE[COOKIE_PREFIX . $this->dbKey . 'DbNo'])) {
                $dbNo = array_rand($this->dsn["slave"]);
                setcookie(COOKIE_PREFIX . $this->dbKey . 'DbNo', $dbNo, null, COOKIE_PATH, COOKIE_DOMAIN);
            } else {
                $dbNo = $_COOKIE[COOKIE_PREFIX . $this->dbKey . 'DbNo'];
            }
            $dbInfo = $this->dsn["slave"][$dbNo];
            $dbName = $dbInfo["dbName"];
            $dbUser = $dbInfo["dbUser"];
            $dbPass = $dbInfo["dbPass"];
            $this->qConn = oci_new_connect($dbUser, $dbPass, $dbName, str_replace('-', '', DEFAULT_CHARSET));

            if (!$this->qConn) {
                if (!$this->uConn) {
                    $this->connect('slave');
                }
                $this->qConn =& $this->uConn;
                if (!$this->qConn) {
                    throw new DB_Exception('查询数据库选择失败');
                }
            }
        }
        return true;
    }

    public function close() {
        if (is_resource($this->uConn)) {
            oci_close($this->uConn);
        }
        if (is_resource($this->qConn)) {
            oci_close($this->qConn);
        }
    }

    public function query($sql, $limit = null, $quick = false) {
        if ($limit != null) {
            $limit = explode(',', $limit);
            foreach ($limit as $key => $value) {
                $limit[$key] = (int) trim($value);
            }
            if (count($limit) == 1) {
                $limit[1] = $limit[0];
                $limit[0] = 0;
            }
        } else {
            $limit[0] = 0;
            $limit[1] = -1;
        }

        $this->sqls[] = $sql;
        $this->qSqls[] = $sql;
        $this->sql = $sql;
        $this->limit = $limit;
        $this->time[count($this->sqls) - 1][] = microtime(true);
        if (!$this->qConn) {
            $this->connect("slave");
        }
        $this->time[count($this->sqls) - 1][] = microtime(true);
        if (!$this->qrs = oci_parse($this->qConn, $sql)) {
            $e = oci_error($this->qrs);
            throw new DB_Exception('SQL解析失败:' . $e['message']);
        }

        if (!oci_execute($this->qrs, $this->autoCommit)) {
            $e = oci_error($this->qrs);
            throw new DB_Exception('查询执行失败:' . $e['message']);
        }
        $this->time[count($this->sqls) - 1][] = microtime(true);
        $this->queryNum++;
        return $this->qrs;
    }

    // 这里没有实现LIMIT,使用LIMIT请用getAll
    public function fetch($rs, $fetchMode = self::DB_FETCH_DEFAULT) {
        if (!$fetchMode) {
            $fetchMode = self::DB_FETCH_DEFAULT;
        }
        $_record = oci_fetch_array($rs, $fetchMode);
        if (is_array($_record)) {
            foreach ($_record as $key => $value) {
                $key = strtolower($key);
                $key = preg_replace('/(?<=\w)_(\w)/e', 'strtoupper("\1")', $key);
                $record[$key] = $value;
            }
        }
        return $record;
    }

    public function update($sql) {
        $this->sql = $sql;
        $this->sqls[] = $this->sql;
        $this->uSqls[] = $this->sql;
        if (!$this->uConn) {
            $this->connect("master");
        }

        if (!$this->urs = oci_parse($this->uConn, $sql)) {
            $e = oci_error($this->urs);
            throw new DB_Exception('SQL解析失败:' . $e['message']);
        }

        if (!oci_execute($this->urs, $this->autoCommit)) {
            $e = oci_error($this->urs);
            throw new DB_Exception('更新失败:' . $e['message']);
        } else {
            $this->updateNum++;
            return $this->urs;
        }
    }

    public function getOne($sql) {
        if (!$rs = $this->query($sql, 1, true)) {
            return false;
        }
        $result = array();
        oci_fetch_all($rs, $result, $this->limit[0], $this->limit[1], self::DB_FETCH_ROW);
        $this->free();
        return $result[0][0];
    }

    public function getCol($sql, $limit = null) {
        if (!$rs = $this->query($sql, $limit, true)) {
            return false;
        }
        $result = array();
        oci_fetch_all($rs, $result, $this->limit[0], $this->limit[1], self::DB_FETCH_ROW);
        foreach ($result as $key => $value) {
            $this->free();
            return $value;
        }
    }

    public function getRow($sql, $fetchMode = self::DB_FETCH_DEFAULT) {
        if (!$rs = $this->query($sql, 1, true)) {
            return false;
        }
        oci_fetch_all($rs, $result, $this->limit[0], $this->limit[1], $fetchMode);
        $result = $this->changeData($result);
        $this->free();
        return $result[0];
    }

    public function getAll($sql, $limit = null, $fetchMode = self::DB_FETCH_DEFAULT) {
        if (!$rs = $this->query($sql, $limit, true)) {
            return false;
        }
        $result = array();
        oci_fetch_all($rs, $result, $this->limit[0], $this->limit[1], $fetchMode);
        $result = $this->changeData($result);
        $this->free();
        return $result;
    }

    public function changeData($data) {
        $result = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    // 将Oracle以下划线作为分割符的字段名转换为驼峰式
                    $key = strtolower($key);
                    $key = preg_replace('/(?<=\w)_(\w)/e', 'strtoupper("\1")', $key);
                    foreach ($value as $k => $v) {
                        $result[$k][$key] = $v;
                    }
                }
            }
        }
        return $result;
    }

    public function rows() {
        return oci_num_rows($this->qrs);
    }

    public function free() {
        if ($this->qrs) {
            oci_free_statement($this->qrs);
        }
        if ($this->urs) {
            oci_free_statement($this->urs);
        }
        $this->qrs = null;
        $this->urs = null;
    }

    public function escape($str) {
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key] = $this->escape($value);
            }
        } else {
            return str_replace("'", "''", $str);
        }
        return $str;
    }

    /**
     * 设置是否开启事务(是否自动提交)
     *
     * 当设置为false的时候,即开启事务处理模式,表类型应该为INNODB
     *
     * @param boolean $mode
     * @return boolean
     */
    public function autoCommit($mode = false) {
        if ($mode) {
            $this->autoCommit = OCI_COMMIT_ON_SUCCESS;
        } else {
            $this->autoCommit = OCI_DEFAULT;
        }
    }

    /**
     * 提交执行的SQL
     *
     * 当开启事务处理后,要手动提交执行的SQL语句
     *
     * @return boolean
     */
    public function commit() {
        return oci_commit($this->uConn);
    }

    /**
     * 回滚
     *
     * 当开启事务处理后,有需要的时候进行回滚
     *
     * @return boolean
     */
    public function rollback() {
        return oci_rollback($this->uConn);
    }

    public function __destruct() {
    }
}

//end of script
