<?php
namespace app\common\logic;

class ArrayTool
{
    /**
     * 去除数组中键指定的值
     * @param array $arr    需要操作的数组,一位数组
     * @param mixed $keys    array|string
     * @return array|false
     */
    public static function removeKey(array $arr,$keys)
    {
        if (count($arr) !== count($arr,1)) {
            return false;
        }
        if (is_string($keys)) {
            foreach ($arr as $key => $val) {
                if ($keys === strval($key)) {
                    unset($arr[$key]);
                }
            }
            return $arr;
        }
        if (is_array($keys)) {
            foreach ($arr as $key => $val) {
                if (in_array($key,$keys)) {
                    unset($val[$key]);
                }
            }
            return $arr;
        }
        return $arr;
    }
}