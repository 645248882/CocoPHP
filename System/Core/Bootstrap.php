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
        // 设置时区
        date_default_timezone_set(CUR_TIMEZONE);

        header("Content-Type: text/html; charset=utf-8");

        // 性能测试 - 程序开始执行时间、消耗内存
        $GLOBALS['_START_TIME'] = microtime(true);
        $GLOBALS['_START_MEM']  = memory_get_usage();
        $GLOBALS['_TIME']       = $_SERVER['REQUEST_TIME'];
        $GLOBALS['_DATE']       = date('Y-m-d H:i:s');

        // 开启缓存
        ob_start();
    }
}
