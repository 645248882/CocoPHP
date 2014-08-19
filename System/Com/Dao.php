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
    protected $_dbs = array();

    /**
     * sql拼接容器
     */
    protected $_options  = array();

    /**
     * 插入sql占位符
     */ 
    protected $_params   = array();

    /**
     * sql拼接对象
     */
    protected $_sqlBuilder;

    /**
     * 主键
     *
     * @var string
     */
    protected $_pk = 'id';

    /**
     * 可选属性，用于 pairs()
     *
     * @var string
     */
    protected $_nameField = 'name';

    protected function db()
    {
        if (! isset($this->_dbs[$this->_dbName])) {
            if (! $this->_dbName) {
                throws(get_class($this) . ' 没有定义数据库，无法使用 Com_Dao', 'sql');
            }

            $this->_dbs[$this->_dbName] = Com_Db::get($this->_dbName);
        }

         return $this->_dbs[$this->_dbName];
    }

    /**
     * 设置当前库名
     *
     * @return $this
     */
    public function setDbName($dbName)
    {
        $this->_dbName = $dbName;

        return $this;
    }

    /**
     * 设置当前表名
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;

        return $this;
    }

    public function getTableName()
    {
        if (isset($this->_options['table'])) {
            return $this->_options['table'];
        }

        // 缺省根据类名获取表名
        // 例如：Dao_User_Index => user_index
        if (null === $this->_tableName) {
            $this->_tableName = strtolower(str_replace('Dao_', '', get_called_class()));
        }

        return $this->_tableName;
    }

    public function getBuilder()
    {
        if ($this->_sqlBuilder === null) {
            $this->_sqlBuilder = new Com_Db_SqlBuilder();
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

        // 读操作
        $readMethods = array(
            'fetchAll'   => 1,
            'fetchAssoc' => 1,
            'fetchOne'   => 1,
            'fetchRow'   => 1,
            'fetchCol'   => 1,
            'fetchPairs' => 1,
        );

        if (isset($readMethods[$method])) {
            return $this->_read($method);
        }

        // 执行写操作
        if (isset($writeMethods[$method])) {

            if ($method == 'delete' && $args) {
                throws('Com_Dao::delete() 的条件参数必须使用 where() 等方法来设置', 'sql');
            } elseif ($method == 'update' && count($args) > 1) {
                throws('Com_Dao::update() 的条件参数必须使用 where() 等方法来设置', 'sql');
            }

            return $this->_write($method, $args);
        }


        throws('Call to undefined method Com_Dao::' . $method, 'sql');
    }

    protected function _write($method, $args = null)
    {
        $sqlBuilder = $this->table($this->getTableName())
                           ->getBuilder()
                           ->setOptions($this->_options);

        $buildMethod = 'build' . ucfirst($method) .'Sql';
        $sql = call_user_func_array(array($sqlBuilder, $buildMethod), $args);

        if (is_array($sql)) {
            $this->_params = array_merge($sql['params'], $this->_params);
            $sql = $sql['sql'];
        }

        $result = $this->db()->query($sql, $this->_params, true);

        // 重置存储的选项
        $this->reset();

        return $result;
    }

    protected function _read($fetchMethod)
    {
        $sql = $this->table($this->getTableName())
                    ->getBuilder()
                    ->setOptions($this->_options)
                    ->buildSelectSql();

        $result = $this->db()->$fetchMethod($sql, $this->_params);

        // 重置存储的选项
        $this->reset();

        return $result;
    }

    public function reset()
    {
        $this->_options     = array();
        $this->_params      = array();
        $this->_isMaster    = false;

        return $this;
    }

    protected function _getPkCondition($pk)
    {
        // 复合主键
        if (is_array($this->_pk)) {
            return array_combine($this->_pk, $pk);
        }
        // 单一主键
        else {
            return array($this->_pk => $pk);
        }
    }

    /**
     * 根据主键 fetchRow + Cache
     *
     * @param mixed $pk
     * @return array
     */
    public function get($pk)
    {
        return $this->where(array($this->_pk => $pk))->fetchRow();
    }

   /**
     * 更新（根据主键）
     *
     * @param array $setArr
     * @param mixed $pk
     * @param array $extraWhere 格外的WHERE条件
     * @return bool/int
     */
    public function updateByPk(array $setArr, $pk, array $extraWhere = array())
    {
        if (! $setArr) {
            return false;
        }

        $where = $this->_getPkCondition($pk);

        if ($extraWhere) {
            $where = array_merge($where, $extraWhere);
        }

        if (! $result = $this->where($where)->update($setArr)) {
            return $result;
        }

        return $result;
    }

    /**
     * 批量更新（根据主键）
     * 注：本方法不支持复合主键
     *
     * @param array $setArr
     * @param array $pks
     * @param array $extraWhere 格外的WHERE条件
     * @return bool/int
     */
    public function updateByPks(array $setArr, array $pks, array $extraWhere = array())
    {
        $where = array($this->_pk => array('IN', $pks));

        if ($extraWhere) {
            $where = array_merge($where, $extraWhere);
        }

        if (! $result = $this->where($where)->update($setArr)) {
            return $result;
        }

        return $result;
    }


    /**
     * 删除（根据主键）
     *
     * @param mixed $pk
     * @param array $extraWhere 格外的WHERE条件
     * @return bool
     */
    public function deleteByPk($pk, array $extraWhere = array())
    {
        $where = $this->_getPkCondition($pk);

        if ($extraWhere) {
            $where = array_merge($where, $extraWhere);
        }

        if (! $result = $this->where($where)->delete()) {
            return $result;
        }

        return $result;
    }

    /**
     * 批量删除（根据主键）
     * 注：本方法不支持复合主键
     *
     * @param array $pks
     * @param array $extraWhere 格外的WHERE条件
     * @return bool
     */
    public function deleteByPks(array $pks, array $extraWhere = array())
    {
        $where = array($this->_pk => array('IN', $pks));

        if ($extraWhere) {
            $where = array_merge($where, $extraWhere);
        }

        if (! $result = $this->where($where)->delete()) {
            return $result;
        }

        return $result;
    }
}