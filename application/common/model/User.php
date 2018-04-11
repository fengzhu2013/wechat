<?php
namespace app\common\model;

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



    /**
     * 获得系统用户列表
     * @param $param
     * @param array $where
     * @param array $order
     * @return array
     */


    public function getCount()
    {

    }

}