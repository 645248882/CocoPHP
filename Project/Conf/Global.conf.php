<?php

// 是否调试模式
define('DEBUG_MODE', true);

// 调试模式下，是否 Explain SQL
define('DEBUG_EXPLAIN_SQL', true);

// 模板文件扩展名
define('TPL_EXT', '.phtml');

// 配置文件目录
define('CONF_PATH',  APP_PATH  . 'Conf' . DS);

// 数据存放目录
define('DATA_PATH',  APP_PATH  . 'Data' . DS);

// 日志目录
define('LOG_PATH',  DATA_PATH . 'Logs' . DS);

// 文件缓存目录
define('CACHE_PATH', DATA_PATH . 'Cache' . DS);

// 当前时区
define('CUR_TIMEZONE', 'Asia/Shanghai');
