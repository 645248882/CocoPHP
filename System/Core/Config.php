<?php

class Core_Config {

    public static $data = array();

    public static function load($fileName)
    {
        if (! isset(self::$data[$fileName])) {
            self::$data[$fileName] = include APP_PATH . 'Conf/' . ucfirst($fileName) . '.conf.php';
        }

        return self::$data[$fileName];
    }
}