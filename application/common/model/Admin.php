<?php
namespace app\common\model;

use app\common\logic\FormatString;
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



}