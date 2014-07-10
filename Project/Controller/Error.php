<?php

/**
 * 错误异常处理器
 */

class Controller_Error extends Core_Controller_Abstract
{
    public function errorAction()
    {
        $e = $this->get('exception');

        if (! $e instanceof Exception) {
            exit("access denied");
        }

        try {
            // 把异常重新抛出，捕获异常的类型
            throw $e;
        } catch (Exception $e) {

            echo '<pre>', $e->getMessage(), '</pre>';
            exit();
        }

        return false;
    }
}