<?php
namespace app\common\logic;

class ArrayTool
{
    /**
     * 去除数组中键指定的值,不影响原来的数组
     * @param array $arr    需要操作的数组,一位数组
     * @param mixed $keys    array|string
     * @return array|false
     */
    public static function removeKey(array $arr,$keys)
    {
        $newArr = $arr;
        if (count($newArr) !== count($newArr,1)) {
            return false;
        }
        if (is_string($keys)) {
            foreach ($newArr as $key => $val) {
                if ($keys === strval($key)) {
                    unset($newArr[$key]);
                }
            }
            return $newArr;
        }
        if (is_array($keys)) {
            foreach ($newArr as $key => $val) {
                if (in_array($key,$keys)) {
                    unset($val[$key]);
                }
            }
            return $newArr;
        }
        return $newArr;
    }


    /**
     * 检验数组$arr中是否存在$keys中的元素,如果都存在，返回true
     * @param array $arr
     * @param array $keys
     * @return bool
     */
    public static function checkNeedKey(array $arr,array $keys)
    {
        if (count($arr) == count($arr,1)) {
            //一维数组
            return self::checkANeedKey($arr,$keys);
        } else {
            //多维数组
            foreach ($arr as $item) {
                if (!self::checkANeedKey($item,$keys)) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * 检验数组$arr中是否存在$keys中的元素,如果都存在，返回true
     * @param array $arr        一维数组
     * @param array $keys
     * @return bool
     */
    public static function checkANeedKey(array $arr,array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($arr[$key]) || empty($arr[$key])) {
                return false;
            }
        }
        return true;
    }


}