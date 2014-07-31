<?php

/**
 * PDO 数据库操作基类
 *
 * @author JiangJian <silverd@sohu.com>
 */

class Com_db_Pdo
{
    /**
     * 存放数据库的连接
     *
     * @var object
     */
    protected $_dbConn = null;

    /**
     * 存放当前DB连接
     *
     * @var object
     */
    protected $_db = null;

    /**
     * 数据库连接配置信息
     *
     * @var array
     */
    protected $_dbConf = array();

    /**
     * 是否进行长连接
     *
     * @var bool
     */
    protected $_persistent = false;

    /**
     * 是否启用仿真 (emulate) 预备义语句 (true)，否则使用原生 (native) 的预备义语句 (false)
     * 原生更加安全，确保语句在发送给 MySQL 服务器执行前被分析，如有语法错误会在 prepare 阶段就报错
     * 但仿真性能会更快，但有 SQL 注入风险 (例如表名为 ? 的情况)
     * 缺省设为 null 表示不改变 PDO::ATTR_EMULATE_PREPARES
     *
     * @var bool/null
     */
    protected $_emulatePrepare = null;

    /**
     * 构造函数，初始化配置
     *
     * @param array $writeConf
     * @param array $readConf
     * @param bool $forceMaster
     * @param bool $persistent
     * @param bool $emulatePrepare
     */
    public function __construct($dbConf, $persistent = false, $emulatePrepare = false)
    {
        $this->_dbConf          = $dbConf;
        $this->_persistent      = $persistent;
        $this->_emulatePrepare  = $emulatePrepare;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * 获取主数据连接
     *
     * @return PDO Object
     */
    protected function _getDbConn()
    {
        // 判断是否已经连接
        if ($this->_dbConn && is_object($this->_dbConn)) {
            return $this->_dbConn;
        }

        $db = $this->_connect(parse_url($this->_dbConf));
        if (! $db || ! is_object($db)) {
            return false;
        }

        $this->_dbConn = $db;
        return $this->_dbConn;
    }

    /**
     * 连接数据库
     *
     * @param array $conf
     * @return PDO Object
     */
    protected function _connect(array $conf)
    {
        try {
            $conf['path'] = trim($conf['path'], '/');
            ! isset($conf['port']) && $conf['port'] = '3306';

            $dsn = 'mysql:dbname=' . $conf['path'] . ';host=' . $conf['host'] . ';port=' . $conf['port'];

            $params = array();

            // 持久连接
            if ($this->_persistent) {
                $params[PDO::ATTR_PERSISTENT] = true;
            }

            $db = new PDO($dsn, $conf['user'], $conf['pass'], $params);

            // 仿真预备义语句（实际PDO默认为true）
            if ($this->_emulatePrepare != null) {
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->_emulatePrepare);
            }

            // 设置编码
            $db->exec("SET NAMES UTF8");

            $db->dsn = $conf;

        } catch (Exception $e) {
            exit($e->getMessage());
        }

        return $db;
    }

    /**
     * 释放数据库连接（释放写连接、读连接、临时连接）
     */
    public function disconnect()
    {
        $this->_dbConn = $this->_db = null;
    }


    /**
     * 执行操作的底层接口
     *
     * @param string $sql
     * @param array $params
     * @return PDO Statement
     */
    protected function _autoExecute($sql, $params = array())
    {
        try {
            $this->_getChoiceDbConnect();

            if (! $this->_db) {
                exit('DB connection lost.');
            }

            // 调试模式打印SQL信息
            $explain = array();

            if (Com_db::enableLogging() && DEBUG_EXPLAIN_SQL) {
                $explain = $this->_explain($sql, $params);
            }
            
            $sqlStartTime = microtime(true);

            // 预编译 SQL
            $stmt = $this->_db->prepare($sql);

            if (! $stmt) {
                exit(implode(',', $this->_db->errorInfo()));
            }

            // 绑定参数
            $params = $params ? (array) $params: array();

            // 执行 SQL
            if (! $stmt->execute($params)) {
                exit(implode(',', $stmt->errorInfo()));
            }

            $sqlCostTime = microtime(true) - $sqlStartTime;

            // 调试模式打印SQL信息
            if (Com_db::enableLogging()) {
                Com_db::sqlLog($this->_formatLogSql($sql, $params), $sqlCostTime, $explain);
            }

            return $stmt;

        } catch (Exception $e) {
            exit(Com_db::getRealSql($sql, $params));
            return false;
        }
    }

    /**
     * 返回 Explain SQL 信息
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function _explain($sql, $params)
    {
        if ('select' != strtolower(substr($sql, 0, 6))) {
            return array();
        }

        $sql = Com_db::getRealSql($sql, $params);

        $explain = array();
        $stmt = $this->_db->query("EXPLAIN " . $sql);
        if ($stmt instanceof PDOStatement) {
            $explain = $stmt->fetch(PDO::FETCH_ASSOC);
            $explain['sql'] = $sql;
            $stmt->closeCursor();
        }

        return $explain;
    }

    /**
     * 把带参数的 SQL 的转换为可记录的 Log
     *
     * @param string $sql
     * @param array $params
     * @return string
     */
    protected function _formatLogSql($sql, $params)
    {
        return array(
            'sql'     => $sql,
            'params'  => $params,
            'realSql' => Com_db::getRealSql($sql, $params),
            'host'    => isset($this->_db->dsn['host']) ? $this->_db->dsn['host'] : '',
            'dbName'  => isset($this->_db->dsn['path']) ? $this->_db->dsn['path'] : '',
        );
    }

    /**
     * 执行一条 SQL （一般针对写操作，如 insert/replace/update/delete）
     *
     * @param string $sql
     * @param array $params
     * @return
     *         int insertId 插入
     *         int rowCount 替换、更新、删除
     *         false SQL 执行失败
     */
    public function query($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if (! $stmt) {
            return false;
        }

        // INSERT 语句返回 insertId
        if (strtoupper(substr(trim($sql), 0, 6)) == 'INSERT') {
            return $this->lastInsertId();
        } 
        
        return $stmt->rowCount();
    }

    /**
     * 获取所有记录
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return array();
    }

    /**
     * 获取第一列
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchCol($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        return array();
    }

    /**
     * 获取键值对
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchPairs($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if ($stmt) {
            $data = array();
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $data[$row[0]] = $row[1];
            }
            return $data;
        }
        return array();
    }

    /**
     * 获取关联数组
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAssoc($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if ($stmt) {
            $data = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $key = current($row);
                $data[$key] = $row;
            }
            return $data;
        }
        return array();
    }

    /**
     * 获取一个单元格
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchOne($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if ($stmt) {
            return $stmt->fetchColumn();
        }
        return null;
    }

    /**
     * 获取单条记录
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchRow($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return array();
    }

    /**
     * PDO fetch method
     *
     * @param PDO Statement $stmt
     * @return arary
     */
    public function fetchArray($stmt)
    {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 执行 SQL 并返回 PDO Statement
     *
     * @param string $sql
     * @param array $params
     * @return PDO Statement
     */
    public function execute($sql, $params = array())
    {
        $stmt = $this->_autoExecute($sql, $params);
        return $stmt ? $stmt : false;
    }

    /**
     * 获取自增ID
     *
     * @return lastInsertId
     */
    public function lastInsertId()
    {
        return $this->_db->lastInsertId();
    }

    /**
     * 选择数据库连接
     *
     * @param bool $forceMaster 是否强制连接主库
     * @return void
     */
    protected function _getChoiceDbConnect($forceMaster = false)
    {
        $this->_db = $this->_getDbConn();
    }

    /**
     * 事务开始
     */
    public function beginTransaction()
    {
        $this->_getChoiceDbConnect(true);
        $this->_db->beginTransaction();
    }

    /**
     * 事务提交
     */
    public function commit()
    {
        $this->_getChoiceDbConnect(true);
        $this->_db->commit();
    }

    /**
     * 事务回滚
     */
    public function rollBack()
    {
        $this->_getChoiceDbConnect(true);
        $this->_db->rollBack();
    }

    /**
     * 检查一个表是否存在
     *
     * @param string $table
     * @return bool
     */
    public function isTableExist($table)
    {
        return (bool) $this->fetchRow("SHOW TABLES LIKE '{$table}'");
    }
}