<?php

/**
 * 控制器抽象父类
 * @author sunli <sunliwodewy@163.com>
 */

class Model_User extends Core_Model_Abstract
{
    protected $_uid;

    public function __construct($uid, $extendInit = true)
    {
        $this->_prop = self::getUser($uid);
        $this->_uid  = $this->_prop['id'] = $this->_prop['uid'];
    }

    // 初始化用户数据
    public static function getUser($uid)
    {
        if ($uid < 1) {
            return false;
        }

        // 获取用户数据
        if (! $user = Dao('User')->get($uid)) {
            // 插入用户基本信息
            $setArr = array(
    			'uid'                 => $uid,
                'user_name'           => 'user_0000' . $uid,
    			'energy'              => 1,
    			'diamond'             => 1,
    			'create_time'         => $GLOBALS['_DATE'],
    			'energy_in_next_time' => 0,
            );

            if (Dao('User')->insert($setArr)) {
                return $setArr;
            }
        }

        return $user;
    }

    public function __get($var)
    {
        // 我的扩展行为
        static $_traits = array(
            'base'      => 1,
        );

        if (isset($_traits[$var])) {
            $class = 'Model_User_' . ucfirst($var);
            return $this->{$var} = new $class($this);
        }

        parent::__get($var);
    }


    public function increment($field, $offset)
    {
        if (! $offset) {
            return false;
        }

        $setArr = array(
            $field => array('+', $offset),
        );

        return $this->update($setArr);
    }

    public function decrement($field, $offset)
    {
        return $this->increment($field, -$offset);
    }

    /**
     * 更新用户基本信息表
     *
     * @param array $setArr
     * @param array $extraWhere 格外的WHERE条件
     * @return bool
     */
    public function update(array $setArr, array $extraWhere = array())
    {
        Dao('User')->updateByPk($setArr, $this->_uid, $extraWhere);

        // 当前 $this->_prop 数组数据更新
        $this->_prop = self::getUser($this->_uid);

        return true;
    }
}