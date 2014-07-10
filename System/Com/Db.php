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
     * 取得DB连接
     *
     * @param string $dbName
     * @return void
     */
    public static function get($dbName)
    {
        // 获取数据库连接配置
        $dbConf = Core_Config::loadEnv('db');

        if (! $dbConf || ! is_array($dbConf)) {
            throw new Exception("Empty DB CONFIG  please check db.conf.php file");
        }

        $dbServer = $dbConf[$dbName];

        if (! $dbServer) {
            throw new Com_DB_Exception('Invalid DB configuration [' . $dbName . '], plz check: db.conf.php');
        }

        // 可能强制连接到主库
        $dbKey = $dbName;

        if (isset(self::$_dbs[$dbKey])) {

            // 创建数据库连接实例
            self::$_dbs[$dbKey] = new Com_DB_PDO(
                $dbServers,
                $dbConf['persistent'],
                $dbConf['emulate_prepare']
            );
        }

        return self::$_dbs[$dbKey];
    }
}