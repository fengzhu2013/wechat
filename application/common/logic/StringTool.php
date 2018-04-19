<?php
namespace app\common\logic;


class StringTool
{

    /**
     * 把一个字符串($string)循环增加某个字段($fill)数次($count), 向左添加
     * @param string $string
     * @param $fill
     * @param int $count
     * @return string
     */
    public static function lFill(string $string,$fill,int $count):string
    {
        $fillString = '';
        for ($i=0;$i < $count;$i++) {
            $fillString .= $fill;
        }
        return $fillString.$string;
    }

    /**
     * 把一个字符串($string)循环增加某个字段($fill)数次($count), 向右添加
     * @param string $string
     * @param $fill
     * @param int $count
     * @return string
     */
    public static function rFill(string $string,$fill,int $count):string
    {
        for ($i=0;$i < $count;$i++) {
            $string .= $fill;
        }
        return $string;
    }

}