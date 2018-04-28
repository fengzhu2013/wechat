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

    /**
     * 查找字符串的位置，1和2表示查找第一次出现的位置或第二次出现的位置，
     * -1和-2表示最后一次及倒数第二次出现的位置， $position不能为0
     * @param string $sting
     * @param string $needle
     * @param int $position
     * @return bool|int
     */
    public static function stringPosition(string $sting,string $needle,int $position = 1)
    {
        if (0 === $position) {
            return false;
        }
        $return = 0;
        if ($position > 0) {
            for ($i = 0;$i < $position;$i++) {
                if (0 === $i) {
                    $offset = 0;
                } else {
                    $offset = $return + 1;
                }
                $return = strpos($sting,$needle,$offset);
                if (is_bool($return)) {
                    return $return;
                }
            }
            return $return;
        } else {
            for ($i = $position;$i < 0;$i++) {
                if ($position === $i) {
                    $offset = 0;
                } else {
                    $strLen = strlen($sting);
                    $offset = $return - $strLen - 1;
                    if (abs($offset) > $strLen) {
                        return false;
                    }
                }
                $return = strrpos($sting,$needle,$offset);
                if (is_bool($return)) {
                    return $return;
                }
            }
            return $return;
        }
    }




}