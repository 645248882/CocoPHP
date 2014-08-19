<?php

/**
 * 控制器抽象父类
 * @author sunli <sunliwodewy@163.com>
 */

abstract class Controller_Abstract extends Core_Controller_Abstract
{
	private static $_secret = 'asfa~~@#$test';

    public function init()
    {   
    	return true;     
    	$request = $this->getQueryx();

        if ( 0 && ! $this->_validate($request) ) {
            throws("登陆参数验证不通过");
        }

        //$uid = Model_User_Api_Auth::getUidByDevice($request['imei'], $request['platform']);

        $uid = 1;

        if (! $uid) {
            throws("登陆失败", 1001);
       }

        $this->_user = new Model_User($uid);

        $this->_uid = $this->_user['uid'];
    }

    private function _validate($request)
	{
	    $sign = $request['sign'];

	    if ($request && $sign) {
	        unset($request['sign']);
	        $str = '';
	        foreach ($request as $k => $v) {
	            $str = $k . $v;
	        }

	        if ($sign == md5($str . self::$_secret)) {
	            return true;
	        }
	    }

	    return false;
	}
}