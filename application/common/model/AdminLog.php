<?php
namespace app\common\model;

use think\Db;
use think\Model;

class AdminLog extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 通过$SESSION_ID获得登录日志信息
     * @param $SESSION_ID
     * @return array|mixed
     */
    public function getInfoBySESSION_ID($SESSION_ID)
    {
        $sql = " select * from `w_admin_log` where `object_type` = 'admin' and `action` = 'l' and `status` = 2 and `msg` -> '$.SESSION_ID' = '{$SESSION_ID}'";
        $return = Db::query($sql);
        if (isset($return[0])) {
            return self::$formatObj->formatArrKey($return[0]);
        }
        return $return;
    }

    /**
     * 通过$userId获得最新的一次登录信息
     * @param $userId
     * @return array
     */
    public function getLastInfoByUserId($userId)
    {
        $logInfo = self::where('user_id',$userId)->where('action','l')->order('id','desc')->find();
        if ($logInfo) {
            return self::$formatObj->formatArrKey($logInfo->toArray());
        }
        return [];
    }

    /**
     * 判断SESSION_ID是否被注销，如果注销了，返回true
     * @param $SESSION_ID
     * @return bool
     */
    public function SESSION_IDIsLogout($SESSION_ID)
    {
        $sql = " select * from `w_admin_log` where `object_type` = 'admin' and `action` = 'r' and `status` = 2 and `msg` -> '$.SESSION_ID' = '{$SESSION_ID}'";
        $return = Db::query($sql);
        if (isset($return[0])) {
            return true;
        }
        return false;
    }

    /**
     * 更新操作时间
     * @param $logInfo
     * @param $actionTime
     * @return false|int
     */
    public function updateActionTime($logInfo,$actionTime)
    {
        $msg = json_decode($logInfo['msg'],true);
        $msg['updateTime'] = $actionTime;
        $msg = json_encode($msg);
        return self::save(['msg' => $msg],['id' => $logInfo['id']]);
    }





}