<?php

/**
 * 我的关系模型抽象父类
 */

abstract class Model_User_Trait extends Core_Model_Abstract
{
    protected $_uid;
    protected $_user;

    public function __construct(Model_User $user)
    {
        $this->_user = $user;
        $this->_uid  = $this->_user['uid'];

        // 相当于子类构函
        $this->_initTrait();
    }

    public function _initTrait()
    {
    	
    }
}