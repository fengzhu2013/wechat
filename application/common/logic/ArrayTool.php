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
                    unset($newArr[$key]);
                }
            }
            return $newArr;
        }
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

    /**
     * 比较两个数组的key和value,若第一个数组的元素（包括key），和第二个都一样，返回true
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    public static function compareArr(array $arr1,array $arr2):bool
    {
        if (empty(array_diff_assoc($arr1,$arr2))) {
            return true;
        }  else {
            return false;
        }
    }

    /**
     * 移除数组中重复的元素
     * @param array $arr
     * @return array
     */
    public static function arrayUnique(array $arr):array
    {
        if (count($arr) === count($arr,1)) {
            return array_unique($arr);
        } else {
            //多维数组
            foreach ($arr as $key => $val) {
                $arr[$key] = json_encode($val);
            }
            $newArr = array_unique($arr);
            foreach ($newArr as $key => $val) {
                $newArr[$key] = json_decode($val,true);
            }
            return $newArr;
        }
    }

    /**
     * 为needle数组补充数据
     * @param array $needle
     * @param array $formatKey
     * @param string $replace
     * @return array
     */
    public static function formatArray(array $needle,array $formatKey,$replace = ''): array
    {
        foreach ($formatKey as $val) {
            if (!isset($needle[$val])) {
                $needle[$val] = $replace;
            }
        }
        return $needle;
    }

    /**
     * 给多维数组，按照子数组的某个元素排序
     * @param array $arr
     * @param string $key
     * @param string $callback
     * @return array
     */
    public static function sortArray(array $arr,string $key,string $callback = ''): array
    {
        $newArr = [];
        $count  = count($arr);
        $mCount = count($arr,1);
        if ($count == $mCount) {
            return [];
        }
        for($i=0;$i<$count-1;$i++) {
            for ($j=0;$j<$count-$i-1;$j++) {
                if ($callback) {
                    $left  = call_user_func($callback,$arr[$j][$key]);
                    $right = call_user_func($callback,$arr[$j+1][$key]);
                } else {
                    $left = $arr[$j][$key];
                    $right = $arr[$j+1][$key];
                }
                if ($right < $left) {
                    $newArr = $arr[$j];
                    $arr[$j] = $arr[$j+1];
                    $arr[$j+1] = $newArr;
                }
            }
        }
        return $arr;
    }



}