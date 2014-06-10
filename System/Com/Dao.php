<?php

/**
 * Data Access Object
 *
 */

class Com_Dao
{
    /**
     * 当前数据库
     */
    protected $_dbName = '';

    /**
     * 当前表
     */
    protected $_tableName = '';

    /**
     * Db连接实例
     */
    protected $_db;

    /**
     * sql拼接容器
     */
    protected $_options  = array();

    /**
     * 连接主数据库
     */
    protected $_isMater = false;

    /**
     * 是否开启缓存
     */
    protected $_enCached = false;

    /**
     * sql拼接对象
     */
    protected $_sqlBuilder;

    public function test()
    {
        pr($this->_options );
    }

    protected function db()
    {
        if (null == $this->_db) {
            if (! $this->_dbName) {
                throw new Exception(get_class($this) . ' 没有定义 $_dbName，无法使用 Com_Dao');
            }

            $this->_db = new com_db($this->_dbName);
        }

        return $this;
    }

    public function getDb()
    {

    }

    public function setDb($dbName)
    {

    }

    public function getTableName()
    {
        if (isset($this->_options ['tableName'])) {
            return $this->_options ['tableName'];
        }

        if (null == $this->_tableName) {
            //todo 默认tableName获取方式
        }

        return $this->_tableName;
    }

    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function getSqlBuilder()
    {
        if ($this->_sqlBuilder === null) {
            $this->_sqlBuilder = new Com_DB_SqlBuilder();
        }

        return $this->_sqlBuilder;
    }

    public function __call($method, $args)
    {
        // SQL查询链式操作
        $selectMethods = array(
            'table' => 1,
            'field' => 1,
            'where' => 1,
            'order' => 1,
            'join'  => 1,
            'limit' => 1,
            'group' => 1,
            'lock'  => 1,
            'having' => 1,
        );

        if (isset($selectMethods[$method])) {
            $this->_options[$method] = (isset($args[0]) ? $args[0] : null);
            return $this;
        }

        // 写操作
        $writeMethods = array(
            'insert'    => 1,
            'update'    => 1,
            'delete'    => 1,
            'increment' => 1,
            'decrement' => 1,
        );

        if (isset($writeMethods[$method])) {
            // 进行写数据库操作
        }

        // 读操作
        $readMethods = array(
            'fetchAll' => 1,
            'fetchOne' => 1,
            'fetchCol' => 1,
            'fetchRow' => 1,
        );

        if (isset($readMethods[$method])) {
            return $this->_read($method);
        }
    }

    protected function _write()
    {

    }

    protected function _read($method)
    {
        $sql = $this->table($this->getTableName())->getSqlBuilder()->setOptions($this->_options)->buildSelectSql();
    }

    public function reset()
    {
        $this->_options     = array();
        $this->_params      = array();
        $this->_isMaster    = false;
        $this->_enableCache = false;

        return $this;
    }

    /**
     * 根据主键 fetchRow + Cache
     *
     * @param mixed $pk
     * @param bool $cached 是否读取缓存
     * @return array
     */
    public function get($pk, $cached = true)
    {
        //return $this->where(array($this->_pk => $pk))->cache($cached)->fetchRow();
    }
}