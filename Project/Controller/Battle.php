<?php

/**
 * 战斗模拟算法
 */

class Controller_Battle extends Core_Controller_Bbstract {

    public function battleAction()
    {
       try {
            $self = array(
                'name' => "玩家A",
                'hp'   => 100,
            );

            $enemy = array(
                'name' => "玩家B",
                'hp'   => 1000,
            );

            // 我方英雄的数据准备
            $selfHeros = array(
                array(
                    'name'   => '绿巨人',
                    'hp'     => 10,
                    'attact' => 10,
                ),
                array(
                    'name'   => '钢铁侠',
                    'hp'     => 50,
                    'attact' => 20,
                ),
                array(
                    'name'   => '齐天大圣',
                    'hp'     => 50,
                    'attact' => 20,
                ),
            );

            // 敌方英雄的数据准备
            $enemyHeros = array(
                array(
                    'name'   => '猫女',
                    'hp'     => 30,
                    'attact' => 5,
                ),
                array(
                    'name'   => '美国队长',
                    'hp'     => 80,
                    'attact' => 10,
                ),

            );

            // 设置玩家战斗平台
            $battle = new model_battle_player();

            $battle->setSelf($self);
            $battle->setenemy($enemy);


            $battle->setSelfHeros($selfHeros);
            $battle->setEnemyHeros($enemyHeros);

            // 正式开打
            $recoder = $battle->process();

            pr($recoder);

            if ($recoder['_result'] > 0) {
                echo "我方胜利了";
            } else {
                echo "我方失败了";
            }
        } catch (exception $e) {
            echo "无法开打";
        }

        return false;
    }
}