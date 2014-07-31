<?php
/*
示例格式：
return array(
    '模块名1' => array(
        'host'       => '127.0.0.1',
        'port'       => '6379',
        'database'   => '0',        // 数据库序号 0~15
        'persistent' => true,       // 是否持久连接
        'timeout'    => 0,
        'options'    => array(      // 附加选项
            Redis::OPT_SERIALIZER => Redis::SERIALIZER_NONE / SERIALIZER_PHP / SERIALIZER_IGBINARY, // 序列化方式
            Redis::OPT_PREFIX     => 'myAppName:', // 自定义键值前缀
        ),
    ),
    '模块名2' => array(
        ....
    ),
);
 */
return array(
    'default' => array(
        'host'       => '192.168.253.129',
        'port'       => '6379',
        'database'   => '0',
    ),
    'static' => array(
        'host'       => '192.168.253.129',
        'port'       => '6379',
        'database'   => '1',
    ),
    'queue' => array(
        'host'       => '192.168.253.129',
        'port'       => '6379',
        'database'   => '2',
    ),
);