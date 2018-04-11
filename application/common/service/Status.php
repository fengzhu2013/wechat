<?php
namespace app\common\service;

/**
 * 系统给状态码加工，添加提示信息
 * Class Status
 * @package app\common\service
 */
class Status
{
    private static $msg = [
        '0'     => 'success',

        '10001' => 'failed',
        '10002' => '添加失败',
        '10003' => '修改失败',
        '10004' => '删除失败',

        //系统级错误
        '20001' => '系统维修中，请稍后再试',
        '20002' => '请求超时，请稍后再试',
        '20003' => '系统开小差了，请等等',

        //提示信息
        '30001' => '未登陆',
        '30002' => '非法会话信息',
        '30003' => '会话已过期',
        '30004' => '登陆已注销',
        '30005' => '权限不够',
        '30006' => '已是管理员',

        //应用级错误
        '40001' => '用户号格式错误',
        '40002' => '用户号错误或不存在',
        '40003' => '账号或密码错误',
        '40004' => '识别标识符错误或不存在',


        '50001' => '传参为空',
        '50002' => '传参格式不符合要求',
        '50003' => '传承不安全',
        '50004' => '没有操作信息',
        '50005' => '没有更新的信息',
        '50006' => '没有记录信息',
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

    /**
     * 自定义强制输出json，并结束程序运行
     * @param $info
     */
    public static function returnJson($info)
    {
        header("Content-type:application/json;charset=utf-8");
        echo json_encode($info);
        exit;
    }


}