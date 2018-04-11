<?php
namespace app\common\service;

use app\common\model\AdminLog;

/**
 * 写日志类
 * Class WriteLog
 * @package app\common\service
 */
class WriteLog
{
    private static $logInfo;


    /**
     * 格式化数据
     * @param $return   mixed 逻辑返回的结果
     * @param $operatorInfo array 当前操作者信息
     * @param $objectType  string 操作对象
     * @param $action      string 操作动作类型
     * @param $msg          array 需要记录的信息
     */
    public static function init($return,$operatorInfo,$objectType,$action,$msg = [])
    {
        //初始化数据
        self::$logInfo = [
            'userId'        => $operatorInfo['userId'],
            'objectId'      => $operatorInfo['objectId'],
            'objectType'    => $objectType,
            'action'        => $action,
            'createTime'    => $operatorInfo['createTime'],
        ];
        $msg['updateTime'] = $operatorInfo['createTime'];
        if (is_array($return)) {
            self::$logInfo['status'] = 2;
            $msg = array_merge($return,$msg);
        } elseif (is_string($return)) {
            self::$logInfo['status'] = 1;
        } elseif ($return && is_bool($return)) {
            self::$logInfo['status'] = 2;
        } else {
            self::$logInfo['status'] = 1;
        }
        self::$logInfo['msg'] = json_encode($msg);
    }

    public static function writeLog($return,$operatorInfo,$objectType,$action,$msg = [])
    {
        self::init($return,$operatorInfo,$objectType,$action,$msg);

        //实例化日志数据库模型
        $AdminLog = new AdminLog(self::$logInfo);
        //写日志
        $AdminLog->saveSelf();
    }

}