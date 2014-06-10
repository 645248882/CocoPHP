<?php

function pr( $arr)
{
    echo '<pre>';

    print_r($arr);
}


function template($tpl)
{
    return rtrim(Core_View::getInstance()->getScriptPath(), DS) . DS . $tpl . TPL_EXT;
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