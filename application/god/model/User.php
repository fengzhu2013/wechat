<?php
namespace app\god\model;


use app\common\logic\Common;
use think\Exception;
use think\exception\DbException;
use think\Model;

class User extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 通过openidArr活动userIdArr
     * @param array $openidArr
     * @return array
     * @throws Exception
     */
    public function getUserIdByOpenidArr(array $openidArr): array
    {
        $ret = [];
        foreach ($openidArr as $openid) {
            $where['openid'] = $openid;
            $userId = self::where($where)->value('user_id');
            if (empty($userId)) {
                $userId = Common::createUserId();
                $info   = ['userId' => $userId,'openid' => $openid,'status' => '3'];
                $res    = $this->insertOne($info);
                if (empty($res)) {
                    throw new Exception('系统出现了问题');
                }
            }
            $ret[$openid] = $userId;
        }
        return $ret;
    }

    public function insertOne(array $info):int
    {
        $info = self::$formatObj->formatArrKey($info,'i');
        $this->data($info,true)->isUpdate(false)->save();
        $ret  = $this->getAttr('id');
        if (empty($ret)) {
            return 0;
        }
        return $ret;
    }

    /**
     * 更新消息
     * @param array $info
     * @param array $where
     * @return int
     */
    public function updateInfo(array $info,array $where):int
    {
        $data   = self::$formatObj->formatArrKey($info,'i');
        $where  = self::$formatObj->formatArrKey($where,'i');
        return $this->where($where)->update($data);
    }

}