<?php
namespace app\system\User;

use think\Model;

class User extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 验证userId是否真实存在
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




}