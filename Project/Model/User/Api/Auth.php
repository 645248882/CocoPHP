<?php

/**
 * 用户认证模型
 */

class Model_User_Api_Auth
{
	public static function getUidByDevice($imei, $platform)
	{
        if (! $imei || ! $platform) {
            throws("Device register error", 1002);
        }

        $token =  md5($imei . '-' . $platform);

        // 根据token获取用户信息
        if (! $uid = Dao('UserDevice')->getUidByDevice($token)) {
        	$setArr = array(
                'imei'     => $imei,
                'platform' => $platform,
                'token'    => $token
    		);

        	return Dao('UserDevice')->insert($setArr);
        }

        return $uid;
	}
}