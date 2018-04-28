<?php
namespace app\common\logic;


class Date
{
    const DAY           = 86400;

    public function __construct()
    {

    }

    /**
     * 验证字符串是否是一个有效日期
     * @param $date
     * @return bool
     */
    public static function verifyDateIsTrue(string $date):bool
    {
        return false !== strtotime($date);
    }

    /**
     * 日期增加一天
     * @param string $date
     * @return string
     */
    public static function addOneDay(string $date): string
    {
        $timestamp = strtotime($date);
        $time      = $timestamp + self::DAY;
        return date('Y-m-d',$time);
    }

    /**
     * 日期减少一天
     * @param string $date
     * @return string
     */
    public static function ReduceOneDay(string $date): string
    {
        $timestamp = strtotime($date);
        $time      = $timestamp - self::DAY;
        return date('Y-m-d',$time);
    }


    /**
     * 获取到两个日期相差的天数
     * @param string $fDate
     * @param string $sDate
     * @return int
     */
    public static function diffDate(string $fDate,string $sDate):int
    {
        //获得日期时间戳
        $fTimestamp = strtotime($fDate);
        $sTimestamp = strtotime($sDate);

        //再转换一次成固定格式的日期
        $fDate      = date('Y-m-d',$fTimestamp);
        $sDate      = date('Y-m-d',$sTimestamp);

        //再转换成时间戳
        $fTimestamp = strtotime($fDate);
        $sTimestamp = strtotime($sDate);

        //想减
        $diff       = $fTimestamp - $sTimestamp;
        return intval($diff/self::DAY);
    }







}