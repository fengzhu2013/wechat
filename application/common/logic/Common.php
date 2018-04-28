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


    public static function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//post请求
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if($output === false) {
            return 'Curl error: ' . curl_error($curl) . "<br>\n\r";
        } else {
            curl_close($curl);
            return $output;
        }
    }








}