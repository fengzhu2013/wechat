<?php
namespace app\common\model;

/**
 * 系统给状态码加工，添加提示信息
 * Class Status
 * @package app\common\model
 */
class Status
{
    private static $msg = [
        '0'     => 'success',
        '10001' => 'failed',

        //系统级错误
        '20001' => '系统维修中，请稍后再试',
        '20002' => '请求超时，请稍后再试',
        '20003' => '系统开小差了，请等等',

        //提示信息
        '30001' => '未登陆',

        //应用级错误
        '40001' => '密码错误',
    ];

    private static $status;

    /**
     * 加工信息
     * @param $info
     * @return array
     */
    public static function processStatus($info)
    {
        //如果info是数组，加工成，状态为0
        if (is_array($info) && count($info)) {
            return [
                'data'      => $info,
                'status'    => 0,
                'msg'       => self::$msg['0']
            ];
        }

        //如果info是字符串
        if (is_string($info)) {
            //存在‘info'key
            if (isset(self::$msg[$info]))
                return [
                    'status'    => intval($info),
                    'msg'       => self::$msg[$info]
                ];
            //不存在，提示系统错误
            return [
                'status'    => 20001,
                'msg'       => self::$msg['20001'],
            ];

        }

        //如果info是bool
        if ($info && is_bool($info)) {
            //is true,return 'success'
            return [
                'status'    => 0,
                'msg'       => self::$msg['0']
            ];
        } elseif (is_bool($info)) {
            //is false,return 'failed'
            return [
                'status'    => 10001,
                'msg'       => self::$msg['10001'],
            ];
        } else {
            //其他，提示系统开小差
            return [
                'status'    => 20003,
                'msg'       => self::$msg['20003'],
            ];
        }

    }


}