<?php

class Helper_Array
{
    /**
     * 将一维数组变成奇偶键值关联数组
     * Example: array(a, b, c, d) => array(a => b, c => d)
     *
     * @param array $array
     * @param array $array
     *
     * @return bool
     */
    public static function assoc(array $array)
    {
        $return = array();

        if ($array) {
            $count  = count($array);
            for ($i = 0; $i < $count; $i+=2) {
                $return[$array[$i]] = ($i + 1 < $count) ? $array[$i + 1] : null;
            }
        }

        return $return;
    }
}