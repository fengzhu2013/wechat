<?php
namespace app\system\model;

use think\Model;

class Admin extends Model
{

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 验证userId是否存在该表中
     * @param string $userId
     * @return bool
     */
    public function verifyUserId(string $userId)
    {
        $info = self::getSelf(['userId' => $userId]);
        if ($info) {
            return true;
        }
        return false;
    }

    /**
     * 完善添加管理员时所需要的固定信息
     * @param int $time 时间戳
     */
    public function perfectAddAdminInfo($time = 0)
    {
        $this->data['password']     = md5($this->data['password']);
        $this->data['status']       = '1';
        $this->data['createTime']   = $time?$time:time();
    }

}