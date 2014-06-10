<?php

/**
 * 战斗相关助手方法
 *
 */

class model_battle_util
{
    /**
     * 计算优先开火权
     */
    public static function calcFirePriority()
    {
        return '_self';
    }

    public static function filterDiedHero(array &$heros)
    {
        $aliveHeros = array();

        if ($heros) {
            foreach ($heros as $hero) {
                if ($hero && $hero['hp'] > 0) {
                    $aliveHeros[] = $hero;
                }
            }
        }

        $heros = $aliveHeros;

        return $heros;
    }


    public static function isHeroAliveByNo($heros, $heroNo)
    {
        return isset($heros[$heroNo]) && self::isHeroAlive($heros[$heroNo]) ? true : false;
    }

    public static function isHeroAlive($hero)
    {
        return $hero['hp'] > 0 ? true : false;
    }

    public static function getAliveHeroCount($heros)
    {
        $aliveCount = 0;

        foreach ($heros as $hero) {
            if ($hero && $hero['hp'] > 0) {
                $aliveCount++;
            }
        }

        return $aliveCount;
    }
}