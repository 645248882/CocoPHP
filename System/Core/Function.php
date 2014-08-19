<?php

function pr( $arr)
{
    echo '<pre>';

    print_r($arr);
}

/**
 * 抛异常
 *
 * @param string $msg
 * @param string $class
 * @throws Core_Exception_Abstract
 * @return void
 */
function throws($msg, $class = 'Logic')
{
    $class = 'Core_Exception_' . ucfirst($class);
    throw new $class($msg);
}

/**
 * 逗号连接
 *
 * @param array $array
 * @return string
 */
function ximplode($array)
{
    return empty($array) ? 0 : "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
}

/**
 * 逗号切开
 *
 * @param string $string
 * @return array
 */
function xexplode($string)
{
    return $string ? explode(',', $string) : array();
}

/**
 * 单例加载
 *
 * @param string $className
 * @return object
 */
function S($className)
{
    return Core_Loader::getSingleton($className);
}

/**
 * 加载 Dao
 *
 * @param string $name
 * @return object
 */
function Dao($name)
{
    return S('Dao_' . $name);
}

/**
 * 是否调试模式
 *
 * @return bool
 */
function isDebug()
{
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        return true;
    }
    return false;
}

