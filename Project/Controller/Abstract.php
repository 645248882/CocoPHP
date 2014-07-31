<?php

/**
 * 控制器抽象父类
 * @author sunli <sunliwodewy@163.com>
 */

abstract class Controller_Abstract extends Core_Controller_Abstract
{
    protected static $secret = '770fed4ca2aabd20ae9a5dd774711de2';
    /**
     * 当前用户 uid
     *
     * @var int
     */
    protected $_uid;

    /**
     * 当前用户实例对象
     *
     * @var Model_User
     */
    protected $_user;

    /**
     * 是否检测登陆
     *
     * @var bool
     */
    protected $_checkAuth = true;

    public function init()
    {
        $request = $this->getQueryx();

        // 签名验证
        if (! $this->_validate($request) && 0) {
            throws("sign 验证不通过", 1000);
        }

        $uid = Model_User_Api_Auth::getUidByDevice($request['imei'], $request['platform']);

        if (! $uid) {
            throws("login fail", 1001);
        }

        $this->_user = new Model_User($uid);

        $this->_uid = $this->_user['uid'];
    }

    /**
     * http://www.xxxx.com/控制器名/方法名?参数名1=参数值1&参数名2=参数值2&参数名3=参数值3
     * 把参数和私钥MD5加密
     */ 
    private function _validate($request)
    {
        $sign = $request['sign'];

        if ($request && $sign) {
            unset($request['sign']);
            $str = '';
            foreach ($request as $k => $v) {
                $str = $k . $v;
            }

            if ($sign == md5($str . self::$secret)) {
                return true;
            }
        }

        return false;
    }

    public function json($output)
    {
        header('Content-type: text/json');
        header('Content-type: application/json; charset=UTF-8');
        exit(json_encode($output));
    }
}