<?php
namespace app\wechat\model;


use think\Model;

class User extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 根据openid获得信息
     * @param $openid
     * @return array
     */
    public function getInfoByOpenid($openid):array
    {
        $where = ['openid' => $openid];
        return self::getSelf($where);
    }

    /**
     * 插入一条信息，返回自增id
     * @param $info
     * @return mixed
     */
    public function insertInfo($info)
    {
        $info = self::$formatObj->formatArrKey($info,'i');
        $this->data($info);
        $this->isUpdate(false)->save();
        return $this->getAttr('id');
    }

    /**
     * 更新一条信息
     * @param $info
     * @param $where
     * @return int|string
     */
    public function updateStatus($info,$where)
    {
        $info = self::$formatObj->formatArrKey($info,'i');
        return self::where($where)->update($info);
    }





}