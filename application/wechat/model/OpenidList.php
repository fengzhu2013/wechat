<?php
namespace app\wechat\model;


use think\Model;

class OpenidList extends Model
{

    public function __construct($data = [])
    {
        parent::__construct($data);
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
     * @param string $status
     * @param array $where
     * @return int
     */
    public function updateStatus(string $status,array $where): int
    {
        $where = self::$formatObj->formatArrKey($where,'i');
        $info  = ['status' => $status];
        $ret   = $this->isUpdate(true)->save($info,$where);
        if (!$ret) {
            return 0;
        }
        return $ret;
    }



}