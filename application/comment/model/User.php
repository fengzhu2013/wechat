<?php
namespace app\comment\model;

use think\Model;

class User extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 根据openid获得userId
     * @param string $openid
     * @return string
     */
    public function getUserIdByOpenid(string $openid): string
    {
        $where = ['openid' => $openid];
        $info  = self::get($where);
        if (empty($info)) {
            return '';
        }
        return $info->getAttr('user_id');
    }

    /**
     * 插入一条数据
     * @param array $info
     * @return int
     */
    public function insertInfo(array $info):int
    {
        $data = self::$formatObj->formatArrKey($info,'i');
        $this->data($data)->isUpdate(false)->save();
        $id   = $this->getAttr('id');
        if (!$id) {
            return 0;
        }
        return $id;
    }


}