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
        '30007' => '不是管理员',
        '30008' => '账号状态不正常',
        '30009' => '不能跨域请求',

        //应用级错误
        '40001' => '用户号格式错误',
        '40002' => '用户号错误或不存在',
        '40003' => '账号或密码错误',
        '40004' => '识别标识符错误或不存在',
        '40005' => '文件内容不符合规定，没有通过必要字段检测',
        '40006' => '微信永久二维码剩余数量不够',


        '50001' => '传参为空',
        '50002' => '传参格式不符合要求',
        '50003' => '传参不安全',
        '50004' => '没有操作信息',
        '50005' => '没有更新的信息',
        '50006' => '没有记录信息',
        '50007' => '不用重复操作',

        '60001' => '文件上传失败',
        '60002' => '读取文件失败，请重新上传',


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
                'info'      => $info,
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
     * 返回指定信息
     * @param $msg
     * @return array
     */
    public static function processMsg($msg)
    {
        return ['status' => 0,'msg' => $msg];
    }

    /**
     * 加工验证错误信息
     * @param $msg
     * @return array
     */
    public static function processValidateMsg($msg)
    {
        return ['status' => 50002,'msg' => $msg];
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

    /**
     * 输出文件
     * @param $fileName
     */
    public static function returnFile($fileName)
    {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($fileName)); //文件名
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: '. filesize($fileName)); //告诉浏览器，文件大小
        readfile($fileName);
    }


}