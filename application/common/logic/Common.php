<?php
namespace app\common\logic;


use app\common\model\User;

class Common
{

    /**
     * 生成内部唯一标识符
     * @return string
     */
    public static function createUserId()
    {
        $userId = 'tf';
        //获得年份
        $year   = substr(date('y-m-d',time()),0,2);
        //获得最后的$userId
        $User   = new User();
        $last   = $User->getLastUserId();
        //截取后6位
        $substr = substr($last,4);
        $need   = intval($substr) + 1;
        $string = StringTool::lFill($need,'0',6-strlen($need));
        return $userId.$year.$string;
    }








}