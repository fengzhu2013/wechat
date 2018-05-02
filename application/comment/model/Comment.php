<?php
namespace app\comment\model;

use think\Model;

class Comment extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function insertAll(array $info)
    {
        foreach ($info as $val) {
            $data[] = self::$formatObj->formatArrKey($val,'i');
        }
        return $this->isUpdate(false)->saveAll($data,false);
    }

    /**
     * 插入一条信息
     * @param array $info
     * @return int
     */
    public function insertInfo(array $info): int
    {
        $data = self::$formatObj->formatArrKey($info,'i');
        $this->data($data,true)->isUpdate(false)->save();
        $id   = $this->getAttr('id');
        if (!$id) {
            return 0;
        }
        return $id;
    }

    /**
     * 获得一条评论
     * @param array $where
     * @param array $order
     * @return array
     */
    public function getOneComment(array $where,array $order = []):array
    {
        if ($where) {
            $where = self::$formatObj->formatArrKey($where,'i');
        }
        if ($order) {
            $order = self::$formatObj->formatArrKey($order,'i');
        }
        $info = self::where($where)->order($order)->find();
        if (empty($info)) {
            return [];
        }
        return self::$formatObj->formatArrKey($info->toArray());
    }

    /**
     * 获得评论的评论数
     * @param string $userId
     * @return int
     */
    public function getComNum(string $userId)
    {
        $ret = 0;
        $where  = ['user_id' => $userId];
        $info   = self::where($where)->order('id','desc')->find();
        if (empty($info) || !isset($info['com_num'])) {
            return $ret;
        }
        $ret = intval($info['com_num']);
        return $ret;
    }



}