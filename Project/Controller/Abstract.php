<?php

/**
 * 控制器抽象父类
 * @author sunli <sunliwodewy@163.com>
 */

abstract class Controller_Abstract extends Core_Controller_Abstract
{
    public function init()
    {
        echo "action init";
    }
}