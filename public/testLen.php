<?php

$arr1 = [
    0 => [0,1],
    1 => [0,1],
    2 => ['a' => 1,'b' => 2],
    3 => ['a' => 1,'b' => 2],
];

function arrayUnique(array $arr)
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

var_dump(arrayUnique($arr1));
exit;

$str1 = 'aaaa';

$str2 = '张三';

$str3 = '李四2';

var_dump(strlen($str1),strlen($str2),strlen($str3).'<br>');
var_dump(mb_strlen($str1,'utf-8'),mb_strlen($str2,'utf-8'),mb_strlen($str3,'utf-8').'<br>');