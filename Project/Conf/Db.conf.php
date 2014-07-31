<?php
/**
 * 库与服务器的对应关系(可找出某库在哪台服务器上)
 * 以及各库相关账号密码信息
 * 注：每增加一个数据库，都需修改本文件并增加一组配置
 */

$dbconf = array();

// 持久连接
$dbconf['persistent'] = false;

// true 启用仿真 (emulate) 预备义语句
// false 使用原生 (native) 预备义语句
$dbconf['emulate_prepare'] = false;

// 2014库
$dbconf['game_2048'] = 'mysql://root:root@127.0.0.1/game_2048';

return $dbconf;