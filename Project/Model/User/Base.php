<?php

/**
 * 用户相关
 */

class Model_User_Base extends Model_User_Trait
{
    /**
     * 检测精力是否够
     *
     * @param int $amount 数量
     * @return void
     */
    public function checkEnergyEnough($amount = 1)
    {
        if ($amount > $this->_user['energy']) {
            throws('NoEnoughEnergy');
        }
    }

    /**
     * 消耗精力值
     *
     * @param int $amount 数量
     * @return bool
     */
    public function consumeEnergy($amount = 1)
    {
        if ($amount < 1) {
            return false;
        }

        return $this->_user->decrement('energy', $amount);
    }

    /**
     * 检测钻石余额
     *
     * @param int $amount 数量
     * @return void
     */
    public function checkDiamond($amount)
    {
        if ($amount > $this->_user['diamond']) {
            throws('NoEnoughDiamond');
        }

        return true;
    }

    public function addDiamond($amount)
    {
        if ($amount < 1) {
            return false;
        }

        // 执行增加
        return $this->_user->update(array('diamond' => array('+', $amount)));
    }

    public function consumeDiamond($amount)
    {
        if ($amount < 1) {
            return false;
        }

        // 执行扣除
        $this->_user->update(array('diamond' => array('-', $amount)));

        // 增加金块消费日志
        $setArr = array(
            'uid'                => $this->_uid,
            'user_name'          => $this->_user['user_name'],
            'diamond'            => $amount,
            'create_time'        => $GLOBALS['_DATE'],
        );

        //Dao('Massive_UserLogConsume')->insert($setArr);
        return true;
    }
}