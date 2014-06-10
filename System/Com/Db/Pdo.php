<?php
/**
 * 数据库底层
 */

class Com_Db_Pdo {
    /**
     * 当前数据库连接实例
     */
    protected $_dbConn;

    /**
     * 数据库连接配置
     */
    protected $_dbConf;

    /**
     * 是否持久连接
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

    public function __construct($dbConf, $isPeristent = false, $_emulatePrepare = false)
    {
        $this->_dbConf          = $dbConf;
        $this->_persistent      = $persistent;
        $this->_emulatePrepare  = $emulatePrepare;
    }

    protected function _getDbConn()
    {
        if ($this->_dbConn && is_object($this->_dbConn)) {
            return $this->_dbConn;
        }

        $this->_dbConn = $this->_connect(parse_url($this->_dbConf));

        if (! $this->_dbConn) {
            throw new Exception("unable to connect mysql");
        }

        return $this->_dbConn;
    }


    protected function _connect($conf)
    {
        try {
            $path = trim($conf['path'], '/');
            $port = isset($conf['port']) ? $conf['port'] : 3306;

            $dsn = 'mysql:dbname=' . $conf['path'] . ';host=' . $conf['host'] . ';port=' . $conf['port'];
            $params = array();

            if ($this->_persistent) {
                $params[PDO::ATTR_PERSISTENT] = true;
            }

            $db = new PDO($dsn, $conf['user'], $conf['pass'], $param);

            // 仿真预备义语句（实际PDO默认为true）
            if ($this->_emulatePrepare != null) {
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->_emulatePrepare);
            }

            // 设置编码
            $db->exec("SET NAMES UTF8");

            return $db;
        } catch (Exception $e) {
            exit("Can not connect mysql");
            return false;
        }
    }

    public function _autoExecute($sql, $params = array())
    {
        try {
            $this->_dbConn();

            if (! $this->_dbConn) {
                throw new Exception('DB connection lost.');
            }

            // 预编译SQL
            $stmt = $this->_dbConn->prepare($sql);

            if (! $stmt) {
                throw new  Exception(implode(',', $this->_dbConn->errorInfo());
            }

            // 绑定参数
            $params =  $params ? (array) $params: array();

            // 执行SQL
            if ($stmt->execute($params)) {
                throw new  Exception(implode(',', $this->_dbConn->errorInfo());
            }

            return $stmt;
        } catch(Exception $e) {
            exit("Can not exec sql");
            return false;
        }
    }
}