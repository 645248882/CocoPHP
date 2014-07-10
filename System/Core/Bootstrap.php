<?php

class Core_Bootstrap {

    public static function init()
    {
        $self = new self();

        foreach (get_class_methods($self) as $method) {
            if ($method !== 'init' && is_callable(array($self, $method))) {
                $self->$method();
            }
        }
    }

    public function initGlobal()
    {
        if (! defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        define('TPL_PATH',  APP_PATH . 'Views' . DS);

        header("Content-Type: text/html; charset=utf-8");

        // 引入系统配置文件
        require SYS_PATH . 'Core/Config.php';
        Core_Config::load("global");

        // 设置时区
        date_default_timezone_set('Asia/Shanghai');

        // 核心函数
        require SYS_PATH . 'Core/Function.php';

        // 开启缓存
        ob_start();
    }
}
