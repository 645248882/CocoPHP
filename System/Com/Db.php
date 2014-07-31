<?php
/**
 * database factory
 */
class Com_Db {

    /**
     * 数据库连接实例
     */
    public static $_dbs = array();

    /**
     * 是否写SQL日志
     *
     * @var bool
     */
    private static $_enableLogging = true;

    /**
     * 取得DB连接
     *
     * @param string $dbName
     * @return void
     */
    public static function get($dbName)
    {
        // 获取数据库连接配置
        $dbConf = Core_Config::load('Db');

        if (! $dbConf || ! is_array($dbConf)) {
            throw new Exception("Empty DB CONFIG  please check db.conf.php file");
        }

        $dbServer = $dbConf[$dbName];

        if (! $dbServer) {
            throw new Exception('Invalid DB configuration [' . $dbName . '], plz check: db.conf.php');
        }

        if (! isset(self::$_dbs[$dbName])) {
            // 创建数据库连接实例
            self::$_dbs[$dbName] = new Com_Db_Pdo(
                $dbServer,
                $dbConf['persistent'],
                $dbConf['emulate_prepare']
            );
        }

        return self::$_dbs[$dbName];
    }

    /**
     * 设置是否写SQL日志
     *
     * @return bool
     */
    public static function enableLogging($bool = null)
    {
        if (null !== $bool) {
            self::$_enableLogging = (bool) $bool;
        }

        // 非调试模式下永远为否
        return ! isDebug() ? false : self::$_enableLogging;
    }

    /**
     * 释放所有DB连接
     *
     * @return void
     */
    public static function disconnect()
    {
        if (self::$_dbs && is_array(self::$_dbs)) {
            foreach (self::$_dbs as $db) {
                $db->disconnect();
            }
            self::$_dbs = null;
        }
    }

    /**
     * 记录 SQL 执行日志（作用：供开发环境下打印SQL语句）
     *
     * @param array $sqlInfo
     * @param int $sqlCostTime
     * @param array $explainResult
     * @return void
     */
    public static function sqlLog($sqlInfo, $sqlCostTime, $explainResult = array())
    {
        $sqlInfo['time']    = $sqlCostTime;
        $sqlInfo['explain'] = $explainResult;
        $GLOBALS['_SQLs'][] = $sqlInfo;
    }

    /**
     * 将 SQL 语句中的 ? 替换为实际值
     *
     * @param string $sql
     * @param array $params
     * @return string
     */
    public static function getRealSql($sql, $params = array())
    {
        if ($params && is_array($params)) {
            while (strpos($sql, '?') > 0) {
                $sql = preg_replace('/\?/', "'" . array_shift($params) . "'", $sql, 1);
            }
        }

        return $sql;
    }

    /**
     * 对 MYSQL LIKE 的内容进行转义
     * @thanks ZhangYanJiong
     *
     * @param string string
     * @return string
     */
    public static function likeQuote($str)
    {
        return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
    }
}