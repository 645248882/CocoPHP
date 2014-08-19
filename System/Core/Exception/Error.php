<?php

/**
 * 错误异常处理器
 */

class Core_Exception_Error extends Core_Controller_Abstract
{
    public function errorAction()
    {
        $e = $this->get('exception');

        if (! $e instanceof Exception) {
            exit("抛出异常出错");
        }

        try {
            // 把异常重新抛出，捕获异常的类型
            throw $e;
        } catch (Core_Exception_Fatal $e) {
            echo $e->getMessage();

        } catch (Core_Exception_Logic $e) {
            echo $e->getMessage();

        } catch (Core_Exception_Sql $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            $data = array();
            $msg = explode('__', $e->getMessage());
            $data[$msg[1]] = $msg[0];
        }
    }
}