<?php
namespace app\content\model;


use think\Model;

class User extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 根据openid获得信息
     * @param string $openid
     * @return array
     */
    public function getInfoByOpenid(string $openid): array
    {
        $where = ['openid' => $openid];
        $list  = self::get($where);
        $info  = self::$formatObj->formatArrKey($list->toArray());
        return $info;
    }

    /**
     * 插入一条数据
     * @param array $info
     * @return int
     */
    public function insertOneInfo(array $info): int
    {
        $data = self::$formatObj->formatArrKey($info,'i');
        $this->data($data)->isUpdate(false)->save();
        return $this->getAttr('id');
    }




}