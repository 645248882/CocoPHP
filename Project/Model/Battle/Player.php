<?php

/**
 * 战斗模型,普通玩家之间对战
 *
 */
class model_battle_player
{
    const
    WIN        = 1,  // 胜利
    LOSE       = -1; // 失败

    protected $_self = array();

    protected $_enemy = array();

    protected $_selfHeros  = array();

    protected $_enemyHeros  = array();

    // 战斗结果
    protected $_result = 0;

    // 战斗过程记录
    protected $_recorder = array();

    public function setSelf(array $self)
    {
        $this->_self = $self;
    }

    public function setEnemy(array $enemy)
    {
        $this->_enemy = $enemy;
    }

    public function setSelfHeros(array $selfHeros)
    {
        if (! $selfHeros) {
            throw new Exception("我方参战对象不能为空");
        }

        $this->_selfHeros  = $selfHeros;
    }

    public function setEnemyHeros(array $enemyHeros)
    {
        if (! $enemyHeros) {
            throw new Exception("我方参战对象不能为空");
        }

        $this->_enemyHeros  = $enemyHeros;
    }

    public function process()
    {
        // todo 站前初始化工作
        $this->_result = $this->_process();

        // 记录战斗结果
        $this->_recorder['_result'] = $this->_result;

        return $this->_recorder;
    }

    // 开打详细流程
    protected function _process()
    {
        // 决定开火权
        $fireTurn = model_battle_util::calcFirePriority($this->_selfHeros , $this->_enemyHeros );

        // 开始的回合数
        $round = 0;

        // 战斗只胜负分出胜负为止
        while (true) {
            // 记录开始的回合数
            $round++;

            // 整理英雄，剔除已经死亡的英雄
            model_battle_util::filterDiedHero($this->_selfHeros );
            model_battle_util::filterDiedHero($this->_enemyHeros );

            $selfRounds =  count($this->_selfHeros );
            $enemyRounds = count($this->_enemyHeros );

            for ($j = 1; $j <= 2; $j++) {
                // 我方开火
                if ($fireTurn == '_self') {
                    // 我方英雄依次开火
                    for ($heroNo = 0; $heroNo < $selfRounds; $heroNo++) {

                        // 如果我方“主角”的HP为0，则判断为我方失败
                        if ($this->_self['hp'] <= 0) {
                            return self::LOSE;
                        }

                        // 开火前，判断我方英雄是否活着
                        if (model_battle_util::isHeroAliveByNo($this->_selfHeros , $heroNo)) {

                            // 如果对方主角HP为0，或者找不到下一个对手，战斗结束， 我方胜利
                            if ($this->_enemy['hp'] <= 0 || model_battle_util::getAliveHeroCount($this->_enemyHeros) <= 0) {
                                return self::WIN;
                            }

                            // 定位防御英雄
                            if (model_battle_util::isHeroAliveByNo($this->_enemyHeros, $heroNo)) {
                                $this->__attackProcess($this->_selfHeros[$heroNo], $this->_enemyHeros[$heroNo], $round);
                            } else {
                                // 如果这个位置上的英雄已经死亡，则攻击“主角”
                                $this->__attackProcess($this->_selfHeros[$heroNo], $this->_enemy, $round);
                            }
                        }
                    }
                }

                // 敌方开火
                if ($fireTurn == '_enemy') {
                    // 敌方英雄依次开火
                    for ($heroNo = 0; $heroNo < $enemyRounds; $heroNo++) {

                        // 如果敌方“主角”的HP为0，则判断为我方胜利
                        if ($this->_enemy['hp'] <= 0) {
                            return self::WIN;
                        }

                        // 开火前，判断敌方英雄是否活着
                        if (model_battle_util::isHeroAliveByNo($this->_enemyHeros , $heroNo)) {

                            // 如果主角HP为0, 或者找不到下一个对手 战斗结束, 我方失败了
                            if ($this->_self['hp'] <= 0 || model_battle_util::getAliveHeroCount($this->_selfHeros) <= 0) {
                                return self::LOSE;
                            }

                            // 定位防御英雄
                            if (model_battle_util::isHeroAliveByNo($this->_selfHeros, $heroNo)) {
                                $this->__attackProcess($this->_enemyHeros[$heroNo], $this->_selfHeros[$heroNo], $round);
                            } else {
                                // 如果这个位置上的英雄已经死亡，则攻击“主角”
                                $this->__attackProcess($this->_enemyHeros[$heroNo], $this->_self, $round);
                            }
                        }
                    }
                }

                // 开火后交换开火权
                $fireTurn = ($fireTurn == '_self') ? '_enemy' : '_self';
            }
        }
    }

  /**
     * 进一步细化的攻击流程（可供双方调用）
     *
     * @param  $attactHero
     * @param  $defenderHero
     * @return void
     */
    protected function __attackProcess(&$attactHero, &$defenderHero, $round)
    {
        // 开始攻击伤害
        $damage = $attactHero['attact'];
        // 执行扣血
        $defenderHero['hp'] = max(0, $defenderHero['hp'] - $damage);
        // 记录本次攻击
        $this->_recorder[] = '第' . $round . '回合：' . $attactHero['name'] . '攻击' . $defenderHero['name'] . '造成' . $damage . '点伤害，此时' . $defenderHero['name'] . '的HP是' . $defenderHero['hp'];
    }
}
