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
$dbconf['emulate_prepare'] = true;

// 测试库
$dbconf['test']['hash_num'] = 1;    // 分几个库，默认不填或1，则表示不分库，最大16
$dbconf['test']['master']   = 'mysql://root:root@127.0.0.2/test';
$dbconf['test']['slave'][]  = 'mysql://root:root@127.0.0.1/test';
$dbconf['test']['slave'][]  = 'mysql://root:root@127.0.0.1/test';

// 全局公共库
$dbconf['voyage_share']['master']  = 'mysql://root:root@127.0.0.2/voyage_share';
$dbconf['voyage_share']['slave'][] = 'mysql://root:root@127.0.0.1/voyage_share';


// 玩家资料分库
$dbconf['voyage_1']['master']  = 'mysql://root:root@127.0.0.2/voyage_1';
$dbconf['voyage_1']['slave'][] = 'mysql://root:root@127.0.0.1/voyage_1';

$dbconf['voyage_2']['master']  = 'mysql://root:root@127.0.0.2/voyage_2';
$dbconf['voyage_2']['slave'][] = 'mysql://root:root@127.0.0.1/voyage_2';

return $dbconf;